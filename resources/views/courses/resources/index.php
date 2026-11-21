<?php
ob_start();
?>
<div class="resources-management-container" style="max-width: 1200px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Top Navigation / Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
            <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Course Management</a>
            <span>/</span>
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
            <span>/</span>
            <span style="color: var(--text-base); font-weight: 600;">Resource Library</span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/faculty/courses/<?= (int)$course->C_ID ?>/builder" class="btn btn-secondary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'book-open', 'size' => 16]) ?>
                <span>Curriculum Builder</span>
            </a>
            <button onclick="document.getElementById('modal-upload-resource').style.display='flex'" class="btn btn-primary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'upload', 'size' => 16]) ?>
                <span>Upload Resource</span>
            </button>
        </div>
    </div>

    <!-- Header Summary Card -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Asset Repository</span>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
                    <?= e((string)$course->C_NAME) ?>
                </h1>
                <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0; max-width: 680px;">
                    Distribute protected academic materials, lecture slides, datasets, laboratory code, and syllabus documents. All files are safely isolated in non-public storage.
                </p>
            </div>

            <div style="display: flex; gap: 1.5rem; background: var(--bg-surface); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);"><?= count($resources) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Assets</div>
                </div>
                <div style="width: 1px; background: var(--border-subtle);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                        <?php
                        $mb = $totalBytes / 1048576;
                        echo $mb >= 1 ? number_format($mb, 1) . ' MB' : number_format($totalBytes / 1024, 0) . ' KB';
                        ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Total Size</div>
                </div>
                <div style="width: 1px; background: var(--border-subtle);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">
                        <?= array_reduce($resources, fn($carry, $r) => $carry + (int)$r->download_count, 0) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Downloads</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Listing -->
    <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
        <?php if (empty($resources)): ?>
            <div style="text-align: center; padding: 4rem 1.5rem; color: var(--text-muted);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-surface-elevated); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <?= component('icon', ['name' => 'folder', 'size' => 32]) ?>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-base);">No Learning Resources Uploaded</h3>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem;">
                    Upload lecture slide decks, syllabus PDFs, lab datasets, or reference code for students enrolled in this course.
                </p>
                <button onclick="document.getElementById('modal-upload-resource').style.display='flex'" class="btn btn-primary">
                    <?= component('icon', ['name' => 'upload', 'size' => 16]) ?>
                    <span>Upload First Asset</span>
                </button>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Resource</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">File Info</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Access</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Downloads</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $resource): ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 1rem 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--bg-surface-elevated); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                            <?= component('icon', ['name' => $resource->icon(), 'size' => 20]) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-base);"><?= e((string)$resource->title) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono, monospace);">
                                                <?= e((string)$resource->original_filename) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 0.75rem; color: var(--text-muted); font-size: 0.8125rem;">
                                    <div><?= $resource->formattedSize() ?></div>
                                    <div style="font-size: 0.6875rem; text-transform: uppercase;"><?= e((string)$resource->mime_type) ?></div>
                                </td>
                                <td style="padding: 1rem 0.75rem;">
                                    <?php if ($resource->isPublic()): ?>
                                        <span class="badge badge-success" style="font-size: 0.6875rem;">Public Preview</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" style="font-size: 0.6875rem;">Enrolled Only</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem 0.75rem; font-weight: 600; color: var(--text-base);">
                                    <?= (int)$resource->download_count ?>
                                </td>
                                <td style="padding: 1rem 0.75rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                        <a href="/courses/<?= (int)$course->C_ID ?>/resources/<?= (int)$resource->id ?>/download" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                                            <?= component('icon', ['name' => 'download', 'size' => 14]) ?>
                                            <span>Download</span>
                                        </a>
                                        <form action="/faculty/courses/<?= (int)$course->C_ID ?>/resources/<?= (int)$resource->id ?>/delete" method="POST" onsubmit="return confirm('Permanently delete this resource?');" style="margin: 0;">
                                            <button type="submit" class="btn btn-ghost btn-sm" style="color: #ef4444; padding: 0.35rem 0.5rem;">
                                                <?= component('icon', ['name' => 'trash-2', 'size' => 14]) ?>
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

<!-- Modal: Upload Resource -->
<div id="modal-upload-resource" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 540px; padding: 2rem;">
        <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem;">Upload Course Resource</h3>
        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1.25rem;">
            Allowed file types: PDF, Word, PowerPoint, Excel, ZIP, images, code files, and video clips (Max 50MB).
        </p>
        <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/resources" enctype="multipart/form-data">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Resource Title</label>
                <input type="text" name="title" placeholder="e.g. Lecture 04 Slide Deck & Lab Guide" class="form-input" style="width: 100%;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">File Attachment</label>
                <input type="file" name="file" required class="form-input" style="width: 100%; padding: 0.5rem;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" name="is_public_preview" value="1">
                    <span>Allow as public preview (accessible without course enrollment)</span>
                </label>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modal-upload-resource').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload Resource</button>
            </div>
        </form>
    </div>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
