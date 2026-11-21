<?php
ob_start();
?>
<div class="course-management-page" style="max-width: 1200px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Curriculum &amp; Course Management
                </h1>
                <span class="badge badge-primary">Faculty Portal</span>
            </div>
            <a href="/faculty/courses/create" class="btn btn-primary btn-sm">
                <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                <span>Author New Course</span>
            </a>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            Author, structure, review, and publish university-accredited degree modules and vocational courses.
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
            <?= component('icon', ['name' => 'check-circle', 'size' => 20]) ?>
            <span><?= e((string)session()->get('success')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">
            <?= component('icon', ['name' => 'alert-triangle', 'size' => 20]) ?>
            <span><?= e((string)session()->get('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Filter & Search Toolbar -->
    <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <!-- Status Tabs -->
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="/faculty/courses?status=all" class="btn <?= $currentStatus === 'all' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                    All Modules (<?= count($courses) ?>)
                </a>
                <a href="/faculty/courses?status=published" class="btn <?= $currentStatus === 'published' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                    Published
                </a>
                <a href="/faculty/courses?status=draft" class="btn <?= $currentStatus === 'draft' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                    Drafts
                </a>
            </div>

            <!-- Search and Category Filters -->
            <form action="/faculty/courses" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
                <input type="text" name="q" value="<?= e($currentSearch) ?>" placeholder="Search modules..." class="form-input" style="padding: 0.5rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base); min-width: 200px;">
                <select name="category" onchange="this.form.submit()" class="form-input" style="padding: 0.5rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat->id ?>" <?= $currentCategory === (int)$cat->id ? 'selected' : '' ?>>
                            <?= e((string)$cat->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card" style="padding: 1.5rem;">
        <?php if (empty($courses)): ?>
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <?= component('icon', ['name' => 'book-open', 'size' => 40]) ?>
                <div style="font-weight: 700; font-size: 1.125rem; margin-top: 1rem;">No courses found</div>
                <p style="font-size: 0.875rem; margin-top: 0.25rem;">No courses match your selected filter criteria. Get started by authoring a new course module.</p>
                <a href="/faculty/courses/create" class="btn btn-primary btn-sm" style="margin-top: 1rem;">
                    <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                    <span>Author New Course</span>
                </a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Module</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Category &amp; Level</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Instructor</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <?php $meta = $c->metadata(); ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 0.875rem 0.75rem;">
                                    <div style="font-weight: 700; font-size: 0.9375rem; color: var(--text-base);">
                                        <a href="/courses/<?= (int)$c->C_ID ?>" style="color: inherit; text-decoration: none;">
                                            <?= e((string)$c->C_NAME) ?>
                                        </a>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e((string)($c->C_CARD_DESCRIPTION ?: $c->C_DESCRIPTION)) ?>
                                    </div>
                                </td>
                                <td style="padding: 0.875rem 0.75rem;">
                                    <div style="font-size: 0.8125rem; font-weight: 600;">
                                        <?= e((string)($c->category()->name ?? 'General')) ?>
                                    </div>
                                    <div style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; margin-top: 0.125rem;">
                                        Level: <?= e(ucfirst($c->level())) ?>
                                    </div>
                                </td>
                                <td style="padding: 0.875rem 0.75rem;">
                                    <?php if ($c->isPublished()): ?>
                                        <span class="badge badge-success">Published</span>
                                    <?php elseif ($c->isDraft()): ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Archived</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.875rem 0.75rem; color: var(--text-muted); font-size: 0.8125rem;">
                                    <?= e((string)($c->C_DONE_BY ?: 'Faculty')) ?>
                                </td>
                                <td style="padding: 0.875rem 0.75rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                        <form action="/faculty/courses/<?= (int)$c->C_ID ?>/publish" method="POST" style="margin: 0;">
                                            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                <?= $c->isPublished() ? 'Unpublish' : 'Publish' ?>
                                            </button>
                                        </form>

                                        <a href="/faculty/courses/<?= (int)$c->C_ID ?>/assignments" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <?= component('icon', ['name' => 'clipboard', 'size' => 12]) ?>
                                            <span>Assignments</span>
                                        </a>

                                        <a href="/faculty/courses/<?= (int)$c->C_ID ?>/resources" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <?= component('icon', ['name' => 'folder', 'size' => 12]) ?>
                                            <span>Resources</span>
                                        </a>

                                        <a href="/faculty/courses/<?= (int)$c->C_ID ?>/builder" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <?= component('icon', ['name' => 'book-open', 'size' => 12]) ?>
                                            <span>Curriculum</span>
                                        </a>

                                        <a href="/faculty/courses/<?= (int)$c->C_ID ?>/edit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <?= component('icon', ['name' => 'edit', 'size' => 12]) ?>
                                            <span>Edit</span>
                                        </a>

                                        <form action="/faculty/courses/<?= (int)$c->C_ID ?>/delete" method="POST" onsubmit="return confirm('Delete this course module?');" style="margin: 0;">
                                            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; color: #ef4444;">
                                                <?= component('icon', ['name' => 'trash-2', 'size' => 12]) ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
