<?php
require_once __DIR__ . '/includes/functions.php';
$team = require_team();
$key = track_key_by_slot((int) $team['slot']);
$t = track($key);

if ($team['stage'] < 4) {
    header('Location: stage3.php');
    exit;
}

$done = all_done();
$doneCount = (int) db()->query("SELECT COUNT(*) FROM teams WHERE stage >= 4 AND limerick IS NOT NULL AND limerick <> ''")->fetchColumn();

$pageTitle = 'סיימתם!';
require __DIR__ . '/includes/header.php';
?>
<div class="card center">
    <div class="ornament">✦ ✦ ✦</div>
    <span class="badge-letter"><?= e($t['letter']) ?></span>
    <h1>כָּל הַכָּבוֹד, <?= e($team['team_name']) ?>!</h1>
    <p class="subtitle">עברתם את שלושת השערים — זיהוי האב, פתיחת הקריפטקס, וכתיבת הברכה.</p>

    <div class="piyyut" style="font-size:19px"><?= e($team['limerick']) ?></div>

    <hr class="rule">

    <?php if ($done): ?>
        <div class="msg ok center">כל חמש הקבוצות סיימו! הגיע הרגע לחשוף את השיר המלא לנֹעַם.</div>
        <a class="btn wine" href="final.php">חֲשִׂיפַת הַשִּׁיר לְנֹעַם ✦</a>
    <?php else: ?>
        <div class="msg info center">
            <?= $doneCount ?> מתוך 5 קבוצות סיימו.<br>
            המתינו בסבלנות לשאר החבורות — ואז ייחשף השיר המלא לנֹעַם.
        </div>
        <a class="btn secondary" href="done.php">רַעֲנְנוּ וּבִדְקוּ שׁוּב ↻</a>
    <?php endif; ?>
</div>

<div class="center" style="margin-top:20px">
    <a class="small" style="color:var(--gold-light)" href="logout.php">יציאה / החלפת קבוצה</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
