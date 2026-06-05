<?php
/**
 * PDF time report template (rendered by Dompdf).
 * Variables provided by TimeController::export_pdf():
 *   $title, $description (HTML), $priority, $status, $createdLabel,
 *   $deadlineLabel, $entries[], $entryCount, $totalH, $totalM, $genDate
 * Dompdf supports a subset of CSS, so the layout uses tables + inline styles.
 */
$statusColor = ($status === 'Completed') ? '#16a34a' : '#d97706';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { color: #1f2937; font-size: 12px; margin: 0; }
    .header { border-bottom: 3px solid #4f46e5; padding-bottom: 10px; margin-bottom: 16px; }
    .brand { color: #4f46e5; font-size: 22px; font-weight: bold; }
    .brand span { color: #6b7280; font-size: 11px; font-weight: normal; }
    .doc-title { font-size: 11px; color: #6b7280; text-align: right; margin-top: -28px; }
    h1 { font-size: 17px; margin: 0 0 10px; color: #111827; }
    h2 { font-size: 13px; color: #4f46e5; margin: 22px 0 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
    .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 10px; color: #fff; }
    table { width: 100%; border-collapse: collapse; }
    .meta td { padding: 5px 8px; vertical-align: top; font-size: 11px; }
    .meta .label { color: #6b7280; font-weight: bold; width: 110px; }
    .meta .desc { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px; }
    table.entries th { background: #4f46e5; color: #fff; text-align: left; padding: 8px; font-size: 11px; }
    table.entries td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; vertical-align: top; }
    table.entries tr:nth-child(even) td { background: #f9fafb; }
    .num { text-align: center; width: 26px; color: #6b7280; }
    .dur { font-family: 'DejaVu Sans Mono', monospace; white-space: nowrap; }
    .summary { margin-top: 18px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 12px; }
    .summary td { padding: 6px 8px; font-size: 12px; }
    .summary .k { color: #6b7280; width: 160px; }
    .summary .v { font-weight: bold; color: #111827; }
    .empty { padding: 16px; text-align: center; color: #6b7280; border: 1px dashed #d1d5db; border-radius: 6px; }
    .footnote { margin-top: 14px; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>
    <div class="header">
        <div class="brand">Prio <span>· Time Report</span></div>
        <div class="doc-title">Generated <?= htmlspecialchars($genDate, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

    <table class="meta">
        <tr>
            <td class="label">Priority</td>
            <td><?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="label">Status</td>
            <td><span class="badge" style="background: <?= $statusColor ?>;"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span></td>
        </tr>
        <tr>
            <td class="label">Created</td>
            <td><?= htmlspecialchars($createdLabel, ENT_QUOTES, 'UTF-8') ?></td>
            <td class="label">Deadline</td>
            <td><?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php if (!empty($includeDescription)): ?>
        <tr>
            <td class="label">Description</td>
            <td colspan="3"><div class="desc"><?= $description ?></div></td>
        </tr>
        <?php endif; ?>
    </table>

    <h2>Reported time</h2>
    <?php if ($entryCount > 0): ?>
        <table class="entries">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th>Date</th>
                    <th>Duration</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $i => $e): ?>
                    <tr>
                        <td class="num"><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($e['date'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="dur"><?= htmlspecialchars(format_duration($e['seconds']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['text'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty">No time has been reported for this task.</div>
    <?php endif; ?>

    <h2>Summary</h2>
    <table class="summary">
        <tr><td class="k">Total number of entries</td><td class="v"><?= (int) $entryCount ?></td></tr>
        <tr><td class="k">Total reported time</td><td class="v"><?= htmlspecialchars(format_duration($totalSec), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><td class="k">Total (decimal hours)</td><td class="v"><?= number_format($totalSec / 3600, 2) ?> h</td></tr>
        <tr><td class="k">Report generation date</td><td class="v"><?= htmlspecialchars($genDate, ENT_QUOTES, 'UTF-8') ?></td></tr>
    </table>

    <div class="footnote">
        This report was generated automatically by Prio. Durations are the reported time spent per entry.
    </div>
</body>
</html>
