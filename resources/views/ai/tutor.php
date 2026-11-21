<?php
ob_start();
?>
<div class="ai-tutor-workspace" style="max-width: 1200px; margin: 0 auto; height: calc(100vh - 170px); display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: stretch;">
    <!-- Left Sidebar: Consultations -->
    <div class="card" style="padding: 1.25rem; display: flex; flex-direction: column; overflow: hidden;">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; color: #fff;">
                    <?= component('icon', ['name' => 'zap', 'size' => 16]) ?>
                </div>
                <h2 style="font-size: 1.125rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Shilpa AI Tutor
                </h2>
            </div>
            <a href="/ai-tutor" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" title="Start New Conversation">
                <?= component('icon', ['name' => 'plus', 'size' => 12]) ?>
                <span>New</span>
            </a>
        </div>

        <?php if ($focusedCourse): ?>
            <div style="padding: 0.625rem 0.75rem; border-radius: var(--radius-md); background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.2); margin-bottom: 1rem; font-size: 0.75rem;">
                <div style="font-weight: 700; color: var(--primary-accent, #6366f1); margin-bottom: 0.125rem;">Focused Academic Context</div>
                <div style="color: var(--text-base); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= e((string)$focusedCourse->C_NAME) ?>
                </div>
            </div>
        <?php endif; ?>

        <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem; padding-right: 0.25rem;">
            <?php if (empty($conversations)): ?>
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                    <?= component('icon', ['name' => 'message-square', 'size' => 32]) ?>
                    <p style="font-size: 0.8125rem; margin-top: 0.5rem;">No previous tutoring sessions.</p>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <?php $isActive = $activeConversation && (int)$activeConversation->id === (int)$conv->id; ?>
                    <a href="/ai-tutor?conversation_id=<?= (int)$conv->id ?>" style="display: block; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); text-decoration: none; color: inherit; background: <?= $isActive ? 'var(--bg-surface-elevated)' : 'transparent' ?>; border: 1px solid <?= $isActive ? 'var(--border-subtle)' : 'transparent' ?>;">
                        <div style="font-weight: 600; font-size: 0.8125rem; color: var(--text-base); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= e((string)$conv->title) ?>
                        </div>
                        <div style="font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.25rem;">
                            <?= date('M d, h:i A', strtotime((string)$conv->updated_at)) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Window: Chat Stream -->
    <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; overflow: hidden;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div>
                <div style="font-weight: 800; font-size: 1.125rem; color: var(--text-base); display: flex; align-items: center; gap: 0.5rem;">
                    <span><?= $activeConversation ? e((string)$activeConversation->title) : 'New AI Consultation' ?></span>
                    <span class="badge badge-success" style="font-size: 0.625rem;">Active</span>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">
                    Autonomous Academic Learning Engine • Grounded in Course Syllabi
                </div>
            </div>
        </div>

        <!-- Suggestion Chips -->
        <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.625rem; white-space: nowrap;" onclick="insertPrompt('Explain the algorithmic time complexity of Dijkstra vs Bellman-Ford.')">
                ⚡ Time Complexity
            </button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.625rem; white-space: nowrap;" onclick="insertPrompt('Test my relational database knowledge with a challenge question.')">
                📝 Quiz Challenge
            </button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.625rem; white-space: nowrap;" onclick="insertPrompt('Summarize key architectural takeaways from this course module.')">
                📖 Summarize Module
            </button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.625rem; white-space: nowrap;" onclick="insertPrompt('How do I structure robust service layers with automated tests in PHP?')">
                🛠 Clean Architecture
            </button>
        </div>

        <!-- Messages Stream -->
        <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding: 0.5rem 0.25rem; margin-bottom: 1rem;" id="chat-messages-container">
            <?php if (empty($activeMessages)): ?>
                <div style="text-align: center; margin: auto; max-width: 480px; color: var(--text-muted); padding: 2rem 1rem;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <?= component('icon', ['name' => 'zap', 'size' => 28]) ?>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-base); margin: 0 0 0.5rem;">Welcome to Shilpa AI Tutor</h3>
                    <p style="font-size: 0.875rem; line-height: 1.5; margin: 0;">
                        Ask conceptual questions, request customized practice quizzes, or paste code snippets for instant step-by-step guidance.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($activeMessages as $m): ?>
                    <?php $isUser = $m->isUser(); ?>
                    <div style="display: flex; justify-content: <?= $isUser ? 'flex-end' : 'flex-start' ?>; gap: 0.75rem;">
                        <?php if (!$isUser): ?>
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0; margin-top: 0.25rem;">
                                AI
                            </div>
                        <?php endif; ?>

                        <div style="max-width: 78%; padding: 0.875rem 1.125rem; border-radius: var(--radius-md); font-size: 0.875rem; line-height: 1.6; <?= $isUser ? 'background: var(--primary-accent, #6366f1); color: #ffffff; border-bottom-right-radius: 2px;' : 'background: var(--bg-surface-elevated); color: var(--text-base); border-bottom-left-radius: 2px; border: 1px solid var(--border-subtle);' ?>">
                            <div style="white-space: pre-line;"><?= e((string)$m->content) ?></div>
                            <div style="font-size: 0.6875rem; margin-top: 0.375rem; text-align: right; opacity: 0.75;">
                                <?= date('h:i A', strtotime((string)$m->created_at)) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Chat Input Form -->
        <form id="ai-chat-form" onsubmit="handleSend(event)" style="display: flex; gap: 0.75rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
            <input type="hidden" name="conversation_id" id="ai-conversation-id" value="<?= $activeConversation ? (int)$activeConversation->id : 0 ?>">
            <input type="hidden" name="course_id" id="ai-course-id" value="<?= $focusedCourse ? (int)$focusedCourse->C_ID : 0 ?>">
            <input type="hidden" name="lesson_id" id="ai-lesson-id" value="<?= $focusedLesson ? (int)$focusedLesson->id : 0 ?>">
            <input type="text" name="prompt" id="ai-prompt-input" required placeholder="Ask Shilpa AI Tutor anything..." autocomplete="off" class="form-input" style="flex: 1; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
            <button type="submit" id="ai-submit-btn" class="btn btn-primary" style="padding: 0.75rem 1.25rem;">
                <?= component('icon', ['name' => 'send', 'size' => 16]) ?>
                <span>Ask</span>
            </button>
        </form>
    </div>
</div>

<script>
function insertPrompt(text) {
    const input = document.getElementById('ai-prompt-input');
    input.value = text;
    input.focus();
}

async function handleSend(e) {
    e.preventDefault();
    const input = document.getElementById('ai-prompt-input');
    const prompt = input.value.trim();
    if (!prompt) return;

    const convId = document.getElementById('ai-conversation-id').value;
    const courseId = document.getElementById('ai-course-id').value;
    const lessonId = document.getElementById('ai-lesson-id').value;
    const container = document.getElementById('chat-messages-container');
    const btn = document.getElementById('ai-submit-btn');

    // Append user message immediately
    const userDiv = document.createElement('div');
    userDiv.style.cssText = 'display: flex; justify-content: flex-end; gap: 0.75rem;';
    userDiv.innerHTML = `<div style="max-width: 78%; padding: 0.875rem 1.125rem; border-radius: var(--radius-md); font-size: 0.875rem; line-height: 1.6; background: var(--primary-accent, #6366f1); color: #ffffff; border-bottom-right-radius: 2px;"><div>${prompt}</div><div style="font-size: 0.6875rem; margin-top: 0.375rem; text-align: right; opacity: 0.75;">Just now</div></div>`;
    container.appendChild(userDiv);
    container.scrollTop = container.scrollHeight;

    input.value = '';
    btn.disabled = true;

    try {
        const res = await fetch('/api/ai/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                conversation_id: convId,
                course_id: courseId,
                lesson_id: lessonId,
                prompt: prompt
            })
        });

        const data = await res.json();
        if (data.success && data.response) {
            if (!convId || convId === '0') {
                document.getElementById('ai-conversation-id').value = data.conversation_id;
            }

            const aiDiv = document.createElement('div');
            aiDiv.style.cssText = 'display: flex; justify-content: flex-start; gap: 0.75rem;';
            aiDiv.innerHTML = `
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0; margin-top: 0.25rem;">AI</div>
                <div style="max-width: 78%; padding: 0.875rem 1.125rem; border-radius: var(--radius-md); font-size: 0.875rem; line-height: 1.6; background: var(--bg-surface-elevated); color: var(--text-base); border-bottom-left-radius: 2px; border: 1px solid var(--border-subtle);"><div style="white-space: pre-line;">${data.response.content}</div><div style="font-size: 0.6875rem; margin-top: 0.375rem; text-align: right; opacity: 0.75;">Just now</div></div>
            `;
            container.appendChild(aiDiv);
            container.scrollTop = container.scrollHeight;
        } else {
            alert(data.error || 'Failed to receive response from AI tutor.');
        }
    } catch (err) {
        alert('Network error communicating with AI tutor.');
    } finally {
        btn.disabled = false;
    }
}
</script>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
