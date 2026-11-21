<?php
ob_start();
$currentUserId = (int)session()->get('user_id', 0);
?>
<div class="direct-messages-layout" style="max-width: 1200px; margin: 0 auto; height: calc(100vh - 180px); display: grid; grid-template-columns: 340px 1fr; gap: 1.5rem; align-items: stretch;">
    <!-- Left Sidebar: Conversations List -->
    <div class="card" style="padding: 1.25rem; display: flex; flex-direction: column; overflow: hidden;">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                Messages
            </h2>
            <span class="badge badge-primary"><?= count($conversations) ?></span>
        </div>

        <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem; padding-right: 0.25rem;">
            <?php if (empty($conversations)): ?>
                <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                    <?= component('icon', ['name' => 'mail', 'size' => 32]) ?>
                    <p style="font-size: 0.8125rem; margin-top: 0.75rem;">No active conversations yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <?php
                    $u = $conv['user'];
                    $isActive = $activeUser && (int)$activeUser->id === (int)$u->id;
                    $unread = (int)$conv['unreadCount'];
                    ?>
                    <a href="/messages/<?= (int)$u->id ?>" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: var(--radius-md); text-decoration: none; color: inherit; background: <?= $isActive ? 'var(--bg-surface-elevated)' : 'transparent' ?>; border: 1px solid <?= $isActive ? 'var(--border-subtle)' : 'transparent' ?>;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; flex-shrink: 0;">
                            <?= strtoupper(substr((string)$u->Full_Name, 0, 1)) ?>
                        </div>
                        <div style="flex: 1; overflow: hidden;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <div style="font-weight: 700; font-size: 0.875rem; color: var(--text-base); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e((string)$u->Full_Name) ?>
                                </div>
                                <?php if ($unread > 0): ?>
                                    <span class="badge badge-primary" style="font-size: 0.625rem; padding: 0.1rem 0.35rem;"><?= $unread ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 0.125rem;">
                                <?= $conv['latestMessage'] ? e((string)$conv['latestMessage']->message) : 'Start conversation...' ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Window: Active Chat -->
    <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; overflow: hidden;">
        <?php if ($activeUser): ?>
            <!-- Chat Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                        <?= strtoupper(substr((string)$activeUser->Full_Name, 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 1.125rem; color: var(--text-base);">
                            <?= e((string)$activeUser->Full_Name) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            <?= ucfirst(e((string)$activeUser->user_type)) ?> • <?= e((string)$activeUser->Email) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem; padding: 0.5rem 0.25rem; margin-bottom: 1rem;" id="chat-messages-container">
                <?php if (empty($messages)): ?>
                    <div style="text-align: center; margin: auto; color: var(--text-muted);">
                        <?= component('icon', ['name' => 'message-circle', 'size' => 36]) ?>
                        <p style="font-size: 0.875rem; margin-top: 0.5rem;">Say hello! This is the beginning of your conversation.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <?php $isMine = (int)$m->sender_id === $currentUserId; ?>
                        <div style="display: flex; justify-content: <?= $isMine ? 'flex-end' : 'flex-start' ?>;">
                            <div style="max-width: 70%; padding: 0.75rem 1rem; border-radius: var(--radius-md); font-size: 0.875rem; line-height: 1.4; <?= $isMine ? 'background: var(--primary-accent, #6366f1); color: #fff; border-bottom-right-radius: 2px;' : 'background: var(--bg-surface-elevated); color: var(--text-base); border-bottom-left-radius: 2px; border: 1px solid var(--border-subtle);' ?>">
                                <div><?= e((string)$m->message) ?></div>
                                <div style="font-size: 0.6875rem; margin-top: 0.25rem; text-align: right; opacity: 0.75;">
                                    <?= date('h:i A', strtotime((string)$m->created_at)) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Chat Input Form -->
            <form action="/messages/send" method="POST" style="display: flex; gap: 0.75rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                <input type="hidden" name="recipient_id" value="<?= (int)$activeUser->id ?>">
                <input type="text" name="message" required placeholder="Type your message..." autocomplete="off" class="form-input" style="flex: 1; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.25rem;">
                    <?= component('icon', ['name' => 'send', 'size' => 16]) ?>
                    <span>Send</span>
                </button>
            </form>
        <?php else: ?>
            <div style="text-align: center; margin: auto; color: var(--text-muted);">
                <?= component('icon', ['name' => 'mail', 'size' => 48]) ?>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem; color: var(--text-base);">Your Messages</h3>
                <p style="font-size: 0.875rem; max-width: 360px; margin: 0 auto;">Select an ongoing conversation or reach out directly to faculty and peers.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
