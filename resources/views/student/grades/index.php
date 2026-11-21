<?php
ob_start();
?>
<div class="student-transcript-container" style="max-width: 1100px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Header Summary Card -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <span class="badge badge-primary">Official Academic Records</span>
            <h1 style="font-size: 2rem; font-weight: 800; margin: 0.5rem 0 0.25rem; font-family: var(--font-heading);">
                My Academic Performance &amp; Transcripts
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0;">
                Verified coursework results, cumulative Grade Point Average (GPA), and published institutional qualifications.
            </p>
        </div>

        <div style="display: flex; gap: 1.5rem; background: var(--bg-surface); padding: 1.25rem 2rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); text-align: center;">
            <div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary);"><?= number_format($cumulativeGpa, 2) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Cumulative GPA</div>
            </div>
            <div style="width: 1px; background: var(--border-subtle);"></div>
            <div>
                <div style="font-size: 2rem; font-weight: 800; color: #10b981;"><?= $totalCredits ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Earned Credits</div>
            </div>
        </div>
    </div>

    <!-- Official Records Table -->
    <div class="card" style="padding: 1.75rem; border: 1px solid var(--border-subtle);">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1.25rem;">Published Course Results</h3>

        <?php if (empty($records)): ?>
            <div style="text-align: center; padding: 4rem 1.5rem; color: var(--text-muted);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-surface-elevated); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <?= component('icon', ['name' => 'award', 'size' => 32]) ?>
                </div>
                <h4 style="margin: 0.5rem 0 0.25rem; color: var(--text-base);">No Published Grades Yet</h4>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto;">
                    Once your instructors finalize and officially publish course evaluations, your grades and transcripts will be authenticated here.
                </p>
            </div>
        <?php else: ?>
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-subtle); text-align: left;">
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Course Module</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Credits</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Score Achieved</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Percentage</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Final Grade</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <?php
                        $c = $r['course'];
                        $g = $r['grade'];
                        ?>
                        <tr style="border-bottom: 1px solid var(--border-subtle);">
                            <td style="padding: 1rem 0.75rem;">
                                <div style="font-weight: 700; color: var(--text-base);"><?= e((string)$c->C_NAME) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e((string)($c->category()->name ?? 'Computing')) ?></div>
                            </td>
                            <td style="padding: 1rem 0.75rem; font-weight: 600;"><?= $r['credits'] ?></td>
                            <td style="padding: 1rem 0.75rem; color: var(--text-muted);"><?= (int)$g->total_score ?> / <?= (int)$g->max_score ?></td>
                            <td style="padding: 1rem 0.75rem; font-weight: 700;"><?= (int)$g->percentage ?>%</td>
                            <td style="padding: 1rem 0.75rem;">
                                <span class="badge badge-primary" style="font-weight: 800; font-size: 0.8125rem;"><?= e((string)$g->letter_grade) ?></span>
                            </td>
                            <td style="padding: 1rem 0.75rem; text-align: right;">
                                <a href="/courses/<?= (int)$c->C_ID ?>/workspace" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                    View Course
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
