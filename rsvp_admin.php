<?php
$file = __DIR__ . '/data/rsvp.json';
$entries = [];
if (file_exists($file)) {
    $entries = json_decode(file_get_contents($file), true) ?: [];
}
$entries = array_reverse($entries);

$accepts  = array_filter($entries, fn($e) => ($e['attendance'] ?? '') === 'Joyfully Accepts');
$declines = array_filter($entries, fn($e) => ($e['attendance'] ?? '') === 'Regretfully Declines');
$totalGuests = array_sum(array_map(fn($e) => (int)($e['guests'] ?? 0), $accepts));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>RSVP Admin — Elvin & Michelle</title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5efe2; color: #2e2a22; margin: 0; padding: 24px; }
  h1 { color: #5C3A21; margin: 0 0 6px; }
  .sub { color: #6B7A4B; margin-bottom: 24px; }
  .stats { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 28px; }
  .stat { background: white; padding: 16px 22px; border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); flex: 1; min-width: 160px; }
  .stat .num { font-size: 28px; font-weight: 600; color: #5C3A21; }
  .stat .lbl { font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: #888; }
  table { width: 100%; border-collapse: collapse; background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
  th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: top; }
  th { background: #6B7A4B; color: white; font-weight: 500; letter-spacing: 1px; font-size: 12px; text-transform: uppercase; }
  tr:hover { background: #faf6ee; }
  .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; }
  .yes { background: #e8f0d8; color: #4F5A36; }
  .no { background: #f7e1d8; color: #C26B4A; }
  .empty { padding: 40px; text-align: center; color: #999; }
  .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
  .export { background: #5C3A21; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; }
</style>
</head>
<body>
  <div class="top">
    <div>
      <h1>RSVP Responses</h1>
      <div class="sub">Elvin John &amp; Michelle Kaye · May 29, 2026</div>
    </div>
    <a class="export" href="data/rsvp.json" target="_blank">Download JSON</a>
  </div>

  <div class="stats">
    <div class="stat"><div class="num"><?= count($entries) ?></div><div class="lbl">Total Responses</div></div>
    <div class="stat"><div class="num"><?= count($accepts) ?></div><div class="lbl">Accepting</div></div>
    <div class="stat"><div class="num"><?= count($declines) ?></div><div class="lbl">Declining</div></div>
    <div class="stat"><div class="num"><?= $totalGuests ?></div><div class="lbl">Total Guests</div></div>
  </div>

  <?php if (!$entries): ?>
    <div class="empty">No RSVP submissions yet.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Submitted</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Guests</th>
          <th>Role</th>
          <th>Diet</th>
          <th>Message</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($entries as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['submitted_at'] ?? '') ?></td>
            <td><strong><?= htmlspecialchars($e['name'] ?? '') ?></strong></td>
            <td>
              <?= htmlspecialchars($e['email'] ?? '') ?><br>
              <small><?= htmlspecialchars($e['phone'] ?? '') ?></small>
            </td>
            <td>
              <?php $att = $e['attendance'] ?? ''; ?>
              <span class="badge <?= $att === 'Joyfully Accepts' ? 'yes' : 'no' ?>">
                <?= htmlspecialchars($att) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($e['guests'] ?? '') ?></td>
            <td><?= htmlspecialchars($e['role'] ?? '') ?></td>
            <td><?= htmlspecialchars($e['diet'] ?? '') ?></td>
            <td><?= nl2br(htmlspecialchars($e['message'] ?? '')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
