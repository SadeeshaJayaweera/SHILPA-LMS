<?php
ob_start();
$isEdit = !empty($course);
?>
<div class="course-form-page" style="max-width: 900px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    <?= $isEdit ? 'Edit Academic Course' : 'Author New Academic Course' ?>
                </h1>
                <span class="badge badge-primary"><?= $isEdit ? 'Revision' : 'Draft' ?></span>
            </div>
            <a href="/faculty/courses" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'arrow-left', 'size' => 16]) ?>
                <span>Back to Modules</span>
            </a>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            Define curriculum structure, learning outcomes, academic prerequisites, and publication status.
        </p>
    </div>

    <form action="<?= e($action) ?>" method="POST" class="card" style="padding: 2rem;">
        <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">

        <!-- Section 1: Basic Information -->
        <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem; color: var(--text-base);">
                Module Information
            </h2>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                    Course Title *
                </label>
                <input type="text" name="title" value="<?= e((string)($course->C_NAME ?? '')) ?>" required placeholder="e.g. CS401: Advanced Cloud Systems Architecture" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Academic Category
                    </label>
                    <select name="category_id" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat->id ?>" <?= (!empty($metadata) && (int)$metadata->category_id === (int)$cat->id) ? 'selected' : '' ?>>
                                <?= e((string)$cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Difficulty Level
                    </label>
                    <select name="level" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        <option value="beginner" <?= (!empty($metadata) && $metadata->level === 'beginner') ? 'selected' : '' ?>>Beginner Level (Year 1)</option>
                        <option value="intermediate" <?= (!empty($metadata) && $metadata->level === 'intermediate') ? 'selected' : '' ?>>Intermediate Level (Year 2/3)</option>
                        <option value="advanced" <?= (!empty($metadata) && $metadata->level === 'advanced') ? 'selected' : '' ?>>Advanced Level (Year 4 / Honours)</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                    Course Summary / Subtitle
                </label>
                <input type="text" name="short_description" value="<?= e((string)($course->C_CARD_DESCRIPTION ?? '')) ?>" placeholder="Brief 1-sentence hook shown on course catalog cards" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                    Full Course Description &amp; Scope
                </label>
                <textarea name="description" rows="5" placeholder="Comprehensive module description..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base); font-family: inherit;"><?= e((string)($course->C_DESCRIPTION ?? '')) ?></textarea>
            </div>
        </div>

        <!-- Section 2: Syllabus, Prerequisites & Outcomes -->
        <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem; color: var(--text-base);">
                Curriculum Structure &amp; Prerequisites
            </h2>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                    Syllabus Outline
                </label>
                <textarea name="syllabus" rows="4" placeholder="Week 1: Fundamentals&#10;Week 2: Advanced Design Patterns..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base); font-family: inherit;"><?= e((string)($metadata->syllabus ?? '')) ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Prerequisites (One per line)
                    </label>
                    <textarea name="prerequisites" rows="3" placeholder="CS101 Intro to Computing&#10;Basic OOP in Java or C#" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base); font-family: inherit;"><?php
                    if (!empty($metadata->prerequisites)) {
                        $parsed = json_decode((string)$metadata->prerequisites, true);
                        echo e(is_array($parsed) ? implode("\n", $parsed) : (string)$metadata->prerequisites);
                    }
                    ?></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Course Tags (Comma separated)
                    </label>
                    <input type="text" name="tags" value="<?php
                    if (!empty($metadata->tags)) {
                        $parsed = json_decode((string)$metadata->tags, true);
                        echo e(is_array($parsed) ? implode(', ', $parsed) : (string)$metadata->tags);
                    }
                    ?>" placeholder="Cloud, Architecture, Docker, DevOps" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                </div>
            </div>
        </div>

        <!-- Section 3: Publishing State -->
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem; color: var(--text-base);">
                Publication &amp; Enrollment Status
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Publication State
                    </label>
                    <select name="status" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        <option value="draft" <?= (!empty($metadata) && $metadata->status === 'draft') ? 'selected' : '' ?>>Draft (Internal to Faculty)</option>
                        <option value="published" <?= (!empty($metadata) && $metadata->status === 'published') ? 'selected' : '' ?>>Published (Open for Enrollment)</option>
                        <option value="archived" <?= (!empty($metadata) && $metadata->status === 'archived') ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Lead Instructor Name
                    </label>
                    <input type="text" name="instructor_name" value="<?= e((string)($course->C_DONE_BY ?? session()->get('user', 'Dr. Perera'))) ?>" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="/faculty/courses" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= component('icon', ['name' => 'save', 'size' => 16]) ?>
                <span><?= $isEdit ? 'Save Changes' : 'Create Course Module' ?></span>
            </button>
        </div>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
