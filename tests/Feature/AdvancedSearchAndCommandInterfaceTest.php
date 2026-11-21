<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\DiscussionThread;
use App\Models\LearningPathway;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\CurriculumBuilderService;
use App\Services\SearchService;
use App\Testing\TestCase;

class AdvancedSearchAndCommandInterfaceTest extends TestCase {
    public function testSearchServiceAcrossMultipleEntities(): void {
        $faculty = User::create([
            'Full_Name' => 'Dr. Alan Kay',
            'Email' => 'alankay.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Smalltalk1972!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $uniqueKeyword = 'QuantumCrypt_' . uniqid();

        // 1. Create Course with keyword
        $course = CourseManagementService::createCourse([
            'title' => 'Foundations of ' . $uniqueKeyword,
            'description' => 'Comprehensive introduction to ' . $uniqueKeyword . ' and quantum circuits.',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // 2. Create Lesson with keyword
        $module = CurriculumBuilderService::createModule($cId, [
            'title' => 'Module on ' . $uniqueKeyword,
        ]);
        $lesson = CurriculumBuilderService::createLesson((int)$module->id, [
            'title' => 'Key Exchange in ' . $uniqueKeyword,
            'content_type' => 'text',
            'content_body' => 'Deep dive into ' . $uniqueKeyword . ' protocols.',
        ]);

        // 3. Create Assignment with keyword
        $assignment = Assignment::create([
            'course_id' => $cId,
            'created_by' => (int)$faculty->id,
            'title' => 'Homework on ' . $uniqueKeyword,
            'description' => 'Implement BB84 protocol for ' . $uniqueKeyword,
            'max_score' => 100,
            'due_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'is_published' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Execute unified search
        $results = SearchService::search($uniqueKeyword);

        assert($results['total'] >= 3, 'Search should locate course, lesson, and assignment');
        assert(count($results['courses']) >= 1, 'Should find matching course');
        assert(count($results['lessons']) >= 1, 'Should find matching lesson');
        assert(count($results['assignments']) >= 1, 'Should find matching assignment');

        assert((int)$results['courses'][0]['id'] === $cId);
        assert(str_contains($results['courses'][0]['title'], $uniqueKeyword));
        assert(str_contains($results['lessons'][0]['title'], $uniqueKeyword));
        assert(str_contains($results['assignments'][0]['title'], $uniqueKeyword));
    }

    public function testSearchFilterByType(): void {
        $uniqueKeyword = 'BioInformatics_' . uniqid();

        $course = CourseManagementService::createCourse([
            'title' => 'Computational ' . $uniqueKeyword,
            'description' => 'Gene sequencing and ' . $uniqueKeyword,
            'status' => 'published',
        ]);

        // Search specifically with type='courses'
        $filtered = SearchService::search($uniqueKeyword, 'courses');
        assert(count($filtered['courses']) === 1, 'Should return matching course');
        assert(empty($filtered['lessons']), 'Filtered search should exclude non-requested entities');
        assert(empty($filtered['assignments']), 'Filtered search should exclude non-requested entities');
        assert(empty($filtered['discussions']), 'Filtered search should exclude non-requested entities');
    }

    public function testPlatformCommandsRetrievalAndFiltering(): void {
        // Student commands
        $studentCommands = SearchService::getCommands('student');
        $studentTitles = array_column($studentCommands, 'title');
        assert(in_array('Go to Dashboard', $studentTitles, true));
        assert(in_array('Personalized Recommendations', $studentTitles, true));
        assert(!in_array('Create New Course', $studentTitles, true));
        assert(!in_array('Security Audit Log Viewer', $studentTitles, true));

        // Faculty commands
        $facultyCommands = SearchService::getCommands('lecturer');
        $facultyTitles = array_column($facultyCommands, 'title');
        assert(in_array('Create New Course', $facultyTitles, true));
        assert(in_array('Faculty Teaching Dashboard', $facultyTitles, true));

        // Admin commands
        $adminCommands = SearchService::getCommands('admin');
        $adminTitles = array_column($adminCommands, 'title');
        assert(in_array('Security Audit Log Viewer', $adminTitles, true));
        assert(in_array('User Directory Management', $adminTitles, true));

        // Query matching a command
        $searchCmd = SearchService::search('Audit', 'commands', 20, 'admin');
        assert(!empty($searchCmd['commands']), 'Should find matching admin audit command');
        assert(str_contains($searchCmd['commands'][0]['title'], 'Audit'));
    }

    public function testQuickSearchAutocomplete(): void {
        $quick = SearchService::quickSearch('Dashboard', 5, 'student');
        assert(!empty($quick), 'Quick search should return matching command actions');
        assert(isset($quick[0]['title']));
        assert(isset($quick[0]['url']));
        assert(isset($quick[0]['category']));
    }

    public function testHttpSearchEndpoints(): void {
        // Full Search View
        $res = $this->get('/search?q=Python');
        assert($res->getStatusCode() === 200, 'Search page should return 200');
        assert(str_contains($res->getContent(), 'Global Search'));

        // API Autocomplete Search
        $resApi = $this->get('/api/search?q=course');
        assert($resApi->getStatusCode() === 200, 'Search API should return 200');
        $json = json_decode($resApi->getContent(), true);
        assert($json['status'] === 'success');
        assert(isset($json['results']));

        // API Commands
        $resCmd = $this->get('/api/commands');
        assert($resCmd->getStatusCode() === 200, 'Commands API should return 200');
        $jsonCmd = json_decode($resCmd->getContent(), true);
        assert($jsonCmd['status'] === 'success');
        assert(!empty($jsonCmd['commands']));
    }
}
