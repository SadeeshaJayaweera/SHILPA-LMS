<?php
$headers = $headers ?? [];
$rows = $rows ?? [];
$emptyText = $emptyText ?? 'No records found';
?>
<div class="table-container">
    <table class="data-table">
        <?php if (!empty($headers)): ?>
            <thead>
                <tr>
                    <?php foreach ($headers as $h): ?>
                        <th><?= e($h) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
        <?php endif; ?>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?= count($headers) ?: 1 ?>" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                        <?= e($emptyText) ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?= $cell ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
