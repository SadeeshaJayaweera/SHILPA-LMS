<?php
ob_start();
?>
<div class="admin-users-view">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0 0 0.25rem 0;">User Directory Management</h1>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Browse, search, and manage registered students, faculty lecturers, and platform administrators.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/admin/dashboard" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'home', 'size' => 16]) ?>
                <span>Admin Dashboard</span>
            </a>
            <a href="/login/reg_via_admin.php" class="btn btn-primary btn-sm">
                <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                <span>Add User</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <form action="/admin/users" method="GET" style="display: flex; align-items: center; gap: 0.75rem; flex: 1; max-width: 420px;">
            <input type="hidden" name="role" value="<?= e($roleFilter) ?>">
            <div style="position: relative; width: 100%;">
                <input 
                    type="search" 
                    name="q" 
                    value="<?= e($search) ?>" 
                    placeholder="Search by full name or email..." 
                    class="form-control"
                    style="padding-left: 2.25rem;"
                >
                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex;">
                    <?= component('icon', ['name' => 'search', 'size' => 16]) ?>
                </span>
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>

        <!-- Role Filter Tabs -->
        <div style="display: flex; gap: 0.375rem; background-color: var(--bg-surface-elevated); padding: 0.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
            <a href="/admin/users?role=all" class="btn btn-sm <?= $roleFilter === 'all' ? 'btn-primary' : 'btn-ghost' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                All Users
            </a>
            <a href="/admin/users?role=student" class="btn btn-sm <?= $roleFilter === 'student' ? 'btn-primary' : 'btn-ghost' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                Students
            </a>
            <a href="/admin/users?role=lecturer" class="btn btn-sm <?= $roleFilter === 'lecturer' ? 'btn-primary' : 'btn-ghost' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                Faculty
            </a>
            <a href="/admin/users?role=admin" class="btn btn-sm <?= $roleFilter === 'admin' ? 'btn-primary' : 'btn-ghost' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                Admins
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card" style="overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-subtle); background-color: var(--bg-surface-elevated); color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.875rem 1rem; text-align: left;">ID</th>
                        <th style="padding: 0.875rem 1rem; text-align: left;">User Profile</th>
                        <th style="padding: 0.875rem 1rem; text-align: left;">Role</th>
                        <th style="padding: 0.875rem 1rem; text-align: left;">Enrolled / Created</th>
                        <th style="padding: 0.875rem 1rem; text-align: right;">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                                No users found matching your search or role filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php 
                            $roleLabel = match ($u->user_type ?? '') {
                                'a', 'm', 'admin' => 'Administrator',
                                'l', 'lecturer' => 'Faculty Lecturer',
                                default => 'Student',
                            };
                            $roleBadge = match ($u->user_type ?? '') {
                                'a', 'm', 'admin' => 'badge-danger',
                                'l', 'lecturer' => 'badge-primary',
                                default => 'badge-secondary',
                            };
                            $initial = strtoupper(substr($u->Full_Name ?? 'U', 0, 1));
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 0.875rem 1rem; font-family: var(--font-mono); color: var(--text-muted);">
                                    #<?= (int)$u->UserId ?>
                                </td>
                                <td style="padding: 0.875rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="avatar avatar-sm avatar-primary" style="width: 2rem; height: 2rem; font-size: 0.8125rem;">
                                            <?= e($initial) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-primary);">
                                                <?= e($u->Full_Name) ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                                <?= e($u->Email) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.875rem 1rem;">
                                    <span class="badge <?= $roleBadge ?>"><?= $roleLabel ?></span>
                                </td>
                                <td style="padding: 0.875rem 1rem; font-size: 0.8125rem; color: var(--text-secondary);">
                                    <?= e($u->created_at ?? date('Y-m-d')) ?>
                                </td>
                                <td style="padding: 0.875rem 1rem; text-align: right;">
                                    <a href="/login/delete.php?deleteid=<?= (int)$u->UserId ?>" class="btn btn-outline-danger btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" onclick="return confirm('Are you sure you want to remove this user?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
