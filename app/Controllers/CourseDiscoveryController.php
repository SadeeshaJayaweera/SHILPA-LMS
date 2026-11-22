<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;

class CourseDiscoveryController extends Controller {
    public function index(Request $request): Response {
        $query = trim((string)$request->query('q', ''));
        $category = trim((string)$request->query('category', ''));
        $sort = trim((string)$request->query('sort', 'latest'));

        $builder = Course::query();

        if ($query !== '') {
            $builder->where('C_NAME', 'LIKE', "%$query%")
                    ->orWhere('C_DESCRIPTION', 'LIKE', "%$query%");
        }

        if ($category !== '' && $category !== 'all') {
            $builder->where('C_CARD_DESCRIPTION', 'LIKE', "%$category%");
        }

        if ($sort === 'top') {
            $builder->where('TOP_COURSE', 'Yes')->orWhere('TOP_COURSE', 'yes');
        } else {
            $builder->orderBy('TOP_COURSE', 'DESC')->orderBy('C_ID', 'ASC');
        }

        $courses = array_map(fn($row) => new Course($row), $builder->get());

        return $this->render('courses/index', [
            'courses' => $courses,
            'searchTerm' => $query,
            'activeCategory' => $category,
            'activeSort' => $sort,
            'totalCount' => count($courses),
        ]);
    }

    public function show(Request $request, string $id): Response {
        $courseId = (int)$id;
        $course = Course::find($courseId);

        if (!$course) {
            return new Response(
                \App\Core\View::render('errors/404', ['path' => "/courses/$id"]),
                404,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return $this->render('courses/show', [
            'course' => $course,
        ]);
    }
}
