<?php
ob_start();
?>
<div class="curriculum-builder-container" style="max-width: 1200px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Top Navigation / Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
            <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Course Management</a>
            <span>/</span>
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
            <span>/</span>
            <span style="color: var(--text-base); font-weight: 600;">Curriculum Builder</span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" class="btn btn-secondary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'eye', 'size' => 16]) ?>
                <span>Preview Workspace</span>
            </a>
            <button onclick="document.getElementById('modal-add-module').style.display='flex'" class="btn btn-primary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                <span>Add Module</span>
            </button>
        </div>
    </div>

    <!-- Curriculum Header Banner -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Curriculum Studio</span>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
                    <?= e((string)$course->C_NAME) ?>
                </h1>
                <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0; max-width: 700px;">
                    Organize learning modules, author lectures, configure multimedia blocks, and sequence curriculum progression.
                </p>
            </div>

            <div style="display: flex; gap: 1.5rem; background: var(--bg-surface); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);"><?= count($modules) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Modules</div>
                </div>
                <div style="width: 1px; background: var(--border-subtle);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);"><?= $totalLessons ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Lectures</div>
                </div>
                <div style="width: 1px; background: var(--border-subtle);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);"><?= $totalMinutes ?>m</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Duration</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules List -->
    <?php if (empty($modules)): ?>
        <div class="card" style="padding: 4rem 2rem; text-align: center; border: 2px dashed var(--border-subtle);">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-surface-elevated); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--text-muted);">
                <?= component('icon', ['name' => 'book-open', 'size' => 32]) ?>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">No Curriculum Modules Yet</h3>
            <p style="color: var(--text-muted); max-width: 460px; margin: 0 auto 1.5rem; font-size: 0.875rem;">
                Start structuring this course by adding your first foundational module. You can nest lessons, video lectures, and rich text blocks inside.
            </p>
            <button onclick="document.getElementById('modal-add-module').style.display='flex'" class="btn btn-primary">
                <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                <span>Add First Module</span>
            </button>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php foreach ($modules as $mIndex => $module): ?>
                <?php
                $lessons = $module->lessons();
                $modDuration = $module->totalDurationMinutes();
                ?>
                <div class="card" style="border: 1px solid var(--border-subtle); overflow: hidden; padding: 0;">
                    <!-- Module Header Bar -->
                    <div style="background: var(--bg-surface-elevated); padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 4px; background: var(--primary-bg, #e0e7ff); color: var(--primary, #4338ca);">
                                MODULE <?= $mIndex + 1 ?>
                            </span>
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0;">
                                <?= e((string)$module->title) ?>
                            </h3>
                            <?php if (!(int)$module->is_published): ?>
                                <span class="badge badge-warning" style="font-size: 0.6875rem;">Draft</span>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-size: 0.8125rem; color: var(--text-muted);">
                                <?= count($lessons) ?> lessons • <?= $modDuration ?> mins
                            </span>
                            <button onclick="openAddLessonModal(<?= (int)$module->id ?>, '<?= e(addslashes((string)$module->title)) ?>')" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                <?= component('icon', ['name' => 'plus', 'size' => 14]) ?>
                                <span>Add Lesson</span>
                            </button>
                            <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/modules/<?= (int)$module->id ?>/delete" onsubmit="return confirm('Delete this module and all its lessons?');" style="display: inline;">
                                <button type="submit" class="btn btn-danger" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                                    <?= component('icon', ['name' => 'trash-2', 'size' => 14]) ?>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Module Body (Lessons & Blocks) -->
                    <div style="padding: 1.25rem 1.5rem;">
                        <?php if (!empty($module->description)): ?>
                            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1rem;">
                                <?= e((string)$module->description) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (empty($lessons)): ?>
                            <div style="padding: 1.5rem; text-align: center; background: var(--bg-surface); border-radius: var(--radius-md); border: 1px dashed var(--border-subtle); color: var(--text-muted); font-size: 0.875rem;">
                                No lessons in this module yet. Click <strong>Add Lesson</strong> above to begin authoring lectures.
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <?php foreach ($lessons as $lIndex => $lesson): ?>
                                    <?php $blocks = $lesson->blocks(); ?>
                                    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); width: 24px;">
                                                    <?= $lIndex + 1 ?>.
                                                </span>
                                                <span class="badge badge-secondary" style="text-transform: uppercase; font-size: 0.6875rem;">
                                                    <?= e((string)$lesson->content_type) ?>
                                                </span>
                                                <strong style="font-size: 0.9375rem;"><?= e((string)$lesson->title) ?></strong>
                                                <?php if ($lesson->isFreePreview()): ?>
                                                    <span class="badge badge-success" style="font-size: 0.6875rem;">Preview</span>
                                                <?php endif; ?>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="font-size: 0.75rem; color: var(--text-muted);">
                                                    <?= (int)$lesson->duration_minutes ?> mins
                                                </span>
                                                <button onclick="openAddBlockModal(<?= (int)$lesson->id ?>, '<?= e(addslashes((string)$lesson->title)) ?>')" class="btn btn-ghost" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                    <?= component('icon', ['name' => 'plus-circle', 'size' => 14]) ?>
                                                    <span>Block</span>
                                                </button>
                                                <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/lessons/<?= (int)$lesson->id ?>/delete" onsubmit="return confirm('Delete lesson?');" style="display: inline;">
                                                    <button type="submit" class="btn btn-ghost" style="color: #ef4444; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                        <?= component('icon', ['name' => 'trash', 'size' => 14]) ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <?php if (!empty($lesson->content_body)): ?>
                                            <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 0.5rem; line-height: 1.4;">
                                                <?= e((string)$lesson->content_body) ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Rendered Content Blocks -->
                                        <?php if (!empty($blocks)): ?>
                                            <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed var(--border-subtle); display: flex; flex-direction: column; gap: 0.5rem;">
                                                <div style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--text-muted);">
                                                    Content Blocks (<?= count($blocks) ?>)
                                                </div>
                                                <?php foreach ($blocks as $block): ?>
                                                    <div style="font-size: 0.8125rem;">
                                                        <?= $block->renderHtml() ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Add Module -->
<div id="modal-add-module" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <h3 style="margin: 0 0 1rem; font-size: 1.25rem;">Add Curriculum Module</h3>
        <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/modules">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Module Title</label>
                <input type="text" name="title" required placeholder="e.g. Module 1: Architecture Fundamentals" class="form-input" style="width: 100%;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Description</label>
                <textarea name="description" rows="3" placeholder="Learning objectives and overview for this module" class="form-input" style="width: 100%;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modal-add-module').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Module</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Lesson -->
<div id="modal-add-lesson" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 540px; padding: 2rem;">
        <h3 id="modal-add-lesson-title" style="margin: 0 0 1rem; font-size: 1.25rem;">Add Lecture / Lesson</h3>
        <form id="form-add-lesson" method="POST" action="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Lesson Title</label>
                <input type="text" name="title" required placeholder="e.g. Distributed Consensus & Raft Protocol" class="form-input" style="width: 100%;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Content Type</label>
                    <select name="content_type" class="form-input" style="width: 100%;">
                        <option value="video">Video Lecture</option>
                        <option value="article">Interactive Reading</option>
                        <option value="interactive">Hands-on Lab</option>
                        <option value="quiz">Knowledge Check</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="20" min="1" max="600" class="form-input" style="width: 100%;">
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Video URL (optional)</label>
                <input type="url" name="video_url" placeholder="https://..." class="form-input" style="width: 100%;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Lesson Brief / Overview</label>
                <textarea name="content_body" rows="3" placeholder="Key concepts covered..." class="form-input" style="width: 100%;"></textarea>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" name="is_free_preview" value="1">
                    <span>Allow as free preview for prospective learners</span>
                </label>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modal-add-lesson').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Lesson</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Content Block -->
<div id="modal-add-block" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 540px; padding: 2rem;">
        <h3 id="modal-add-block-title" style="margin: 0 0 1rem; font-size: 1.25rem;">Add Content Block</h3>
        <form id="form-add-block" method="POST" action="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Block Type</label>
                <select name="block_type" class="form-input" style="width: 100%;">
                    <option value="rich_text">Rich Text / Markdown</option>
                    <option value="code_snippet">Code Snippet</option>
                    <option value="callout">Important Callout</option>
                    <option value="video">Embedded Video</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Content / Text</label>
                <textarea name="content" rows="4" placeholder="Enter content or code..." class="form-input" style="width: 100%; font-family: var(--font-mono, monospace);"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modal-add-block').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Block</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddLessonModal(moduleId, moduleTitle) {
    document.getElementById('modal-add-lesson-title').innerText = 'Add Lesson to ' + moduleTitle;
    document.getElementById('form-add-lesson').action = '/faculty/courses/<?= (int)$course->C_ID ?>/modules/' + moduleId + '/lessons';
    document.getElementById('modal-add-lesson').style.display = 'flex';
}

function openAddBlockModal(lessonId, lessonTitle) {
    document.getElementById('modal-add-block-title').innerText = 'Add Block to: ' + lessonTitle;
    document.getElementById('form-add-block').action = '/faculty/courses/<?= (int)$course->C_ID ?>/lessons/' + lessonId + '/blocks';
    document.getElementById('modal-add-block').style.display = 'flex';
}
</script>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
