<?php
ob_start();
?>
<div class="course-workspace-overview" style="max-width: 1200px; margin: 0 auto;">
    <!-- Breadcrumb Bar -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
        <a href="/courses" style="color: inherit; text-decoration: none;">Courses</a>
        <span>/</span>
        <span style="color: var(--text-base); font-weight: 600;"><?= e((string)$course->C_NAME) ?></span>
    </div>

    <!-- Course Hero Section -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div style="max-width: 720px;">
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                <span class="badge badge-primary"><?= e((string)($course->category()->name ?? 'Computing')) ?></span>
                <span class="badge badge-secondary"><?= strtoupper(e($course->level())) ?> LEVEL</span>
                <span class="badge badge-info"><?= $totalLessons ?> LECTURES</span>
            </div>

            <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
                <?= e((string)$course->C_NAME) ?>
            </h1>

            <p style="color: var(--text-muted); font-size: 1rem; margin: 0 0 1rem; line-height: 1.5;">
                <?= e((string)($course->C_CARD_DESCRIPTION ?: $course->C_DESCRIPTION)) ?>
            </p>

            <div style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.875rem; color: var(--text-muted); flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                    <span>Instructor: <strong><?= e((string)($course->C_DONE_BY ?: 'Faculty')) ?></strong></span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.375rem;">
                    <?= component('icon', ['name' => 'clock', 'size' => 16]) ?>
                    <span>Est. Duration: <strong><?= $totalMinutes ?> Minutes</strong></span>
                </div>
                <a href="/courses/<?= (int)$course->C_ID ?>/announcements" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                    <?= component('icon', ['name' => 'bell', 'size' => 14]) ?>
                    <span>Announcements</span>
                </a>
                <a href="/courses/<?= (int)$course->C_ID ?>/discussions" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                    <?= component('icon', ['name' => 'message-square', 'size' => 14]) ?>
                    <span>Q&amp;A Forum</span>
                </a>
            </div>
        </div>

        <!-- Action / Enrollment Card -->
        <div class="card" style="padding: 1.5rem; min-width: 280px; text-align: center; border: 1px solid var(--border-subtle);">
            <?php if ($isEnrolled): ?>
                <div style="font-size: 0.75rem; font-weight: 700; color: #10b981; text-transform: uppercase; margin-bottom: 0.5rem;">
                    Enrolled Learner
                </div>
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.25rem;">
                        <span>Course Progress</span>
                        <span><?= (int)($enrollment->progress_percent ?? 0) ?>%</span>
                    </div>
                    <div style="width: 100%; height: 8px; border-radius: 4px; background: var(--border-subtle); overflow: hidden;">
                        <div style="width: <?= (int)($enrollment->progress_percent ?? 0) ?>%; height: 100%; background: #10b981;"></div>
                    </div>
                </div>

                <?php
                $firstLesson = null;
                if (!empty($modules)) {
                    $firstModLessons = $modules[0]->lessons();
                    $firstLesson = $firstModLessons[0] ?? null;
                }
                ?>

                <?php if ($firstLesson): ?>
                    <a href="/courses/<?= (int)$course->C_ID ?>/learn/<?= (int)$firstLesson->id ?>" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <?= component('icon', ['name' => 'play', 'size' => 16]) ?>
                        <span>Resume Learning</span>
                    </a>
                <?php else: ?>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Curriculum in preparation</div>
                <?php endif; ?>
            <?php else: ?>
                <div style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.25rem;">Open Enrollment</div>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1.25rem;">
                    Full access to video lectures, interactive lab materials, and accredited certification.
                </p>
                <form action="/courses/<?= (int)$course->C_ID ?>/enroll" method="POST">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <?= component('icon', ['name' => 'graduation-cap', 'size' => 16]) ?>
                        <span>Enroll in This Course</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content & Syllabus Split -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        <!-- Left: Course Curriculum / Modules -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.375rem; font-weight: 700; margin: 0; font-family: var(--font-heading);">
                    Curriculum Syllabus
                </h2>
                <span style="font-size: 0.875rem; color: var(--text-muted);"><?= count($modules) ?> Modules • <?= $totalLessons ?> Lessons</span>
            </div>

            <?php if (empty($modules)): ?>
                <div class="card" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">
                    <?= component('icon', ['name' => 'book-open', 'size' => 36]) ?>
                    <div style="font-weight: 700; margin-top: 0.75rem;">Syllabus in preparation</div>
                    <p style="font-size: 0.8125rem; margin-top: 0.25rem;">The course instructor is finalizing module topics and lesson resources.</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($modules as $mIndex => $mod): ?>
                        <?php $lessons = $mod->lessons(); ?>
                        <div class="card" style="padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary-text); text-transform: uppercase;">
                                        Module <?= $mIndex + 1 ?>
                                    </div>
                                    <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0.125rem 0;">
                                        <?= e((string)$mod->title) ?>
                                    </h3>
                                    <?php if (!empty($mod->description)): ?>
                                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.25rem 0 0;">
                                            <?= e((string)$mod->description) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">
                                    <?= count($lessons) ?> lessons • <?= $mod->totalDurationMinutes() ?> mins
                                </span>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.5rem; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
                                <?php foreach ($lessons as $lIndex => $les): ?>
                                    <?php $isLessonDone = in_array((int)$les->id, $completedLessonIds ?? [], true); ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-radius: var(--radius-md); background: var(--bg-surface); font-size: 0.875rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <?php if ($isLessonDone): ?>
                                                <span style="color: #10b981;"><?= component('icon', ['name' => 'check-circle', 'size' => 16]) ?></span>
                                            <?php else: ?>
                                                <?= component('icon', ['name' => $les->content_type === 'video' ? 'play-circle' : 'file-text', 'size' => 16]) ?>
                                            <?php endif; ?>
                                            <span <?= $isLessonDone ? 'style="text-decoration: line-through; opacity: 0.8;"' : '' ?>>
                                                <?= $mIndex + 1 ?>.<?= $lIndex + 1 ?> <?= e((string)$les->title) ?>
                                            </span>
                                            <?php if ($les->isFreePreview()): ?>
                                                <span class="badge badge-success" style="font-size: 0.625rem;">Free Preview</span>
                                            <?php endif; ?>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span style="font-size: 0.75rem; color: var(--text-muted);"><?= (int)$les->duration_minutes ?> mins</span>
                                            <?php if ($isEnrolled || $les->isFreePreview()): ?>
                                                <a href="/courses/<?= (int)$course->C_ID ?>/learn/<?= (int)$les->id ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                    Watch
                                                </a>
                                            <?php else: ?>
                                                <span style="color: var(--text-muted);"><?= component('icon', ['name' => 'lock', 'size' => 14]) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Outcomes, Prerequisites & Instructor -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Quizzes Section -->
            <?php if (!empty($quizzes)): ?>
                <div class="card" style="padding: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'help-circle', 'size' => 18]) ?>
                        <span>Quizzes &amp; Exams (<?= count($quizzes) ?>)</span>
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <?php foreach ($quizzes as $quiz): ?>
                            <a href="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--bg-surface-elevated); border-radius: var(--radius-sm); text-decoration: none; color: inherit; font-size: 0.8125rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <?= component('icon', ['name' => 'check-circle', 'size' => 14]) ?>
                                    <span style="font-weight: 600;"><?= e((string)$quiz->title) ?></span>
                                </div>
                                <div style="color: var(--text-muted); font-size: 0.75rem;">
                                    <?= (int)$quiz->time_limit_minutes ?>m • <?= (int)$quiz->passing_score_percent ?>% pass
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($assignments)): ?>
                <div class="card" style="padding: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'clipboard', 'size' => 18]) ?>
                        <span>Coursework &amp; Projects (<?= count($assignments) ?>)</span>
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <?php foreach ($assignments as $asgn): ?>
                            <a href="/courses/<?= (int)$course->C_ID ?>/assignments/<?= (int)$asgn->id ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--bg-surface-elevated); border-radius: var(--radius-sm); text-decoration: none; color: inherit; font-size: 0.8125rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <?= component('icon', ['name' => 'file-text', 'size' => 14]) ?>
                                    <span style="font-weight: 600;"><?= e((string)$asgn->title) ?></span>
                                </div>
                                <div style="color: var(--text-muted); font-size: 0.75rem;">
                                    <?= (int)$asgn->max_score ?> pts
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <?= component('icon', ['name' => 'check-circle', 'size' => 18]) ?>
                    <span>What You'll Master</span>
                </h3>
                <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem; color: var(--text-base); display: flex; flex-direction: column; gap: 0.5rem;">
                    <li>Architect high-throughput, enterprise-scale educational backends</li>
                    <li>Design modular decoupled components with rigorous automated tests</li>
                    <li>Audit cryptographic log integrity and mitigate OWASP vulnerabilities</li>
                    <li>Deploy production-ready solutions on modern cloud infrastructures</li>
                </ul>
            </div>

            <!-- Learning Resources Section -->
            <?php if (!empty($resources)): ?>
                <div class="card" style="padding: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'download-cloud', 'size' => 18]) ?>
                        <span>Course Resources (<?= count($resources) ?>)</span>
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <?php foreach ($resources as $res): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--bg-surface-elevated); border-radius: var(--radius-sm); font-size: 0.8125rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                                    <?= component('icon', ['name' => $res->icon(), 'size' => 16]) ?>
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;"><?= e((string)$res->title) ?></span>
                                </div>
                                <div>
                                    <?php if ($isEnrolled || $res->isPublic()): ?>
                                        <a href="/courses/<?= (int)$course->C_ID ?>/resources/<?= (int)$res->id ?>/download" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                                            <?= component('icon', ['name' => 'download', 'size' => 12]) ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">Locked</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Prerequisites Card -->
            <div class="card" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <?= component('icon', ['name' => 'info', 'size' => 18]) ?>
                    <span>Academic Prerequisites</span>
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 0.75rem;">
                    Basic familiarity with OOP principles, relational database concepts, and command-line interfaces.
                </p>
                <div style="font-size: 0.75rem; color: var(--primary-text); font-weight: 600;">
                    Accredited by Faculty of Computing • NSBM
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
