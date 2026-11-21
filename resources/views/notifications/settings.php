<?php
ob_start();
?>
<div class="notification-settings-page" style="max-width: 800px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
                Notification Preferences
            </h1>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Customize what email alerts and transactional notices you receive from SHILPA LMS.
            </p>
        </div>
        <a href="/notifications" class="btn btn-secondary btn-sm">
            <?= component('icon', ['name' => 'arrow-left', 'size' => 14]) ?>
            <span>Notification Feed</span>
        </a>
    </div>

    <!-- Preferences Form -->
    <div class="card" style="padding: 2rem;">
        <form action="/notifications/settings" method="POST">
            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">

            <!-- Master Toggle -->
            <div style="padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle); margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 700; font-size: 1.0625rem; color: var(--text-base);">Email Notifications</div>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.25rem 0 0;">
                            Master switch to receive transactional educational emails at your registered account address.
                        </p>
                    </div>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="email_enabled" value="1" <?= $preferences->allowsEmail() ? 'checked' : '' ?> style="opacity: 0; width: 0; height: 0;" onchange="document.getElementById('toggle-ui-master').style.background = this.checked ? '#4f46e5' : 'var(--border-subtle)';">
                        <span id="toggle-ui-master" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?= $preferences->allowsEmail() ? '#4f46e5' : 'var(--border-subtle)' ?>; transition: .3s; border-radius: 24px;"></span>
                    </label>
                </div>
            </div>

            <!-- Categories -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.9375rem; color: var(--text-base);">Assignment Deadlines &amp; Reminders</div>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.125rem 0 0;">Receive warnings 24 hours prior to submission deadlines.</p>
                    </div>
                    <input type="checkbox" name="assignment_alerts" value="1" <?= (int)($preferences->assignment_alerts ?? 1) === 1 ? 'checked' : '' ?>>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.9375rem; color: var(--text-base);">Course Announcements</div>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.125rem 0 0;">Alerts when instructors broadcast urgent schedules or syllabus notices.</p>
                    </div>
                    <input type="checkbox" name="announcement_alerts" value="1" <?= (int)($preferences->announcement_alerts ?? 1) === 1 ? 'checked' : '' ?>>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.9375rem; color: var(--text-base);">Grades &amp; Assessment Results</div>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.125rem 0 0;">Notifies when an assignment is scored or course gradebook is published.</p>
                    </div>
                    <input type="checkbox" name="grade_alerts" value="1" <?= (int)($preferences->grade_alerts ?? 1) === 1 ? 'checked' : '' ?>>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.9375rem; color: var(--text-base);">Discussion Forum Replies</div>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0.125rem 0 0;">Alerts when peers or faculty reply to your questions or threads.</p>
                    </div>
                    <input type="checkbox" name="discussion_alerts" value="1" <?= (int)($preferences->discussion_alerts ?? 1) === 1 ? 'checked' : '' ?>>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid var(--border-subtle); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <?= component('icon', ['name' => 'save', 'size' => 16]) ?>
                    <span>Save Notification Preferences</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
