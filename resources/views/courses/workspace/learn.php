<?php
ob_start();
?>
<div class="learning-theater" style="max-width: 1400px; margin: 0 auto;">
    <!-- Theater Header Navigation -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0 1.5rem; border-bottom: 1px solid var(--border-subtle); margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'arrow-left', 'size' => 14]) ?>
                <span>Course Overview</span>
            </a>
            <span style="color: var(--border-subtle);">|</span>
            <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">
                <?= e((string)$course->C_NAME) ?>
            </span>
            <span style="color: var(--text-muted);">/</span>
            <span style="font-size: 0.875rem; font-weight: 700; color: var(--text-base);">
                <?= e((string)$lesson->title) ?>
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <?php if ($prevLesson): ?>
                <a href="/courses/<?= (int)$course->C_ID ?>/learn/<?= (int)$prevLesson->id ?>" class="btn btn-secondary btn-sm">
                    <?= component('icon', ['name' => 'chevron-left', 'size' => 14]) ?>
                    <span>Previous</span>
                </a>
            <?php endif; ?>

            <?php if ($nextLesson): ?>
                <a href="/courses/<?= (int)$course->C_ID ?>/learn/<?= (int)$nextLesson->id ?>" class="btn btn-primary btn-sm">
                    <span>Next Lecture</span>
                    <?= component('icon', ['name' => 'chevron-right', 'size' => 14]) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Theater Content Grid -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start;">
        <!-- Main Lecture Player / Viewer -->
        <div>
            <!-- Video / Media Canvas -->
            <div style="background: #000; border-radius: var(--radius-lg); aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; color: white; margin-bottom: 1.5rem; position: relative; overflow: hidden; box-shadow: var(--shadow-lg);">
                <?php if ($lesson->content_type === 'video' && !empty($lesson->video_url)): ?>
                    <iframe src="<?= e((string)$lesson->video_url) ?>" title="Lecture Player" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem;">
                        <?= component('icon', ['name' => 'play-circle', 'size' => 64]) ?>
                        <div style="font-size: 1.25rem; font-weight: 700; margin-top: 1rem;"><?= e((string)$lesson->title) ?></div>
                        <div style="font-size: 0.875rem; opacity: 0.7; margin-top: 0.25rem;">Interactive Academic Lecture Broadcast • <?= (int)$lesson->duration_minutes ?> Minutes</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Lesson Title & Action Toolbar -->
            <div class="card" style="padding: 1.75rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary-text); text-transform: uppercase;">
                            <?= e((string)($currentModule->title ?? 'Module Lecture')) ?>
                        </div>
                        <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0.25rem 0; font-family: var(--font-heading);">
                            <?= e((string)$lesson->title) ?>
                        </h1>
                        <div style="font-size: 0.8125rem; color: var(--text-muted);">
                            Duration: <?= (int)$lesson->duration_minutes ?> minutes • Content Type: <?= strtoupper(e($lesson->content_type)) ?>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <?php if ($isEnrolled): ?>
                            <form method="POST" action="/courses/<?= (int)$course->C_ID ?>/lessons/<?= (int)$lesson->id ?>/complete" style="margin: 0;">
                                <button type="submit" class="btn <?= $isLessonCompleted ? 'btn-secondary' : 'btn-primary' ?>" style="font-size: 0.875rem;">
                                    <?php if ($isLessonCompleted): ?>
                                        <?= component('icon', ['name' => 'check-circle', 'size' => 16]) ?>
                                        <span style="color: #10b981; font-weight: 700;">Completed</span>
                                    <?php else: ?>
                                        <?= component('icon', ['name' => 'circle', 'size' => 16]) ?>
                                        <span>Mark as Complete</span>
                                    <?php endif; ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($nextLesson): ?>
                            <a href="/courses/<?= (int)$course->C_ID ?>/learn/<?= (int)$nextLesson->id ?>" class="btn btn-secondary">
                                <span>Next Lecture</span>
                                <?= component('icon', ['name' => 'chevron-right', 'size' => 16]) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Lecture Body / Transcript -->
                <div style="font-size: 0.9375rem; line-height: 1.6; color: var(--text-base); border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
                    <?= nl2br(e((string)($lesson->content_body ?: 'Detailed lecture notes, architectural diagrams, and interactive transcripts accompany this session.'))) ?>
                </div>
            </div>
        </div>

        <!-- Right: Syllabus Navigator Sidebar -->
        <div class="card" style="padding: 1.25rem; max-height: 80vh; overflow-y: auto; position: sticky; top: 1.5rem;">
            <div style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                Course Curriculum
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($modules as $mIdx => $m): ?>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.375rem;">
                            Module <?= $mIdx + 1 ?>: <?= e((string)$m->title) ?>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <?php foreach ($m->lessons() as $l): ?>
                                <?php
                                $isActive = ((int)$l->id === (int)$lesson->id);
                                $isDone = in_array((int)$l->id, $completedLessonIds ?? [], true);
                                ?>
                                <a href="/courses/<?= (int)$course->C_ID ?>/learn/<?= (int)$l->id ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.625rem; border-radius: var(--radius-md); text-decoration: none; font-size: 0.8125rem; <?= $isActive ? 'background: var(--primary); color: white; font-weight: 600;' : 'background: var(--bg-surface); color: var(--text-base);' ?>">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php if ($isDone): ?>
                                            <?= component('icon', ['name' => 'check-circle', 'size' => 14]) ?>
                                        <?php else: ?>
                                            <?= component('icon', ['name' => $isActive ? 'play' : 'file-text', 'size' => 14]) ?>
                                        <?php endif; ?>
                                        <span><?= e((string)$l->title) ?></span>
                                    </div>
                                    <span style="font-size: 0.6875rem; opacity: 0.8;"><?= (int)$l->duration_minutes ?>m</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
