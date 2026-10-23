<?php
require_once __DIR__ . '/includes/functions.php';
boot();

// מותר לצפייה רק אם כל הקבוצות סיימו, או שהמנהל מחובר
if (!all_done() && !admin_logged_in()) {
    $pageTitle = 'עדיין לא…';
    require __DIR__ . '/includes/header.php';
    $doneCount = (int) db()->query("SELECT COUNT(*) FROM teams WHERE stage >= 4 AND limerick IS NOT NULL AND limerick <> ''")->fetchColumn();
    echo '<div class="card center"><div class="ornament">✦</div><h1>הַשִּׁיר עֲדַיִן נֶאֱגָר…</h1>'
       . '<p class="subtitle">' . $doneCount . ' מתוך 5 קבוצות סיימו. השיר ייחשף כאשר כל החבורות יעברו את כל השערים.</p>'
       . '<a class="btn secondary" href="final.php">בִּדְקוּ שׁוּב ↻</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// איסוף בתי השיר לפי סדר המשבצות (נ, ע, מ, כ, ו)
$rows = db()->query('SELECT * FROM teams ORDER BY slot ASC')->fetchAll();
$bySlot = [];
foreach ($rows as $r) {
    $bySlot[(int) $r['slot']] = $r;
}

$pageTitle = 'השיר לנֹעַם';
require __DIR__ . '/includes/header.php';
?>
<div class="card">
    <div class="ornament">✦ ✦ ✦</div>
    <img class="noam-portrait large" src="assets/noam.png" alt="נֹעַם">
    <h1 class="center">הַשִּׁיר לְנֹעַם</h1>
    <p class="subtitle center">חמש החבורות, חמישה אבות, חמש אותיות — המתאחדות לברכה אחת.</p>

    <div class="acrostic-strip">
        <?php foreach (game_data() as $t): ?>
            <span><?= e($t['letter']) ?></span>
        <?php endforeach; ?>
    </div>
    <p class="center muted">נֹעַם כ״ו — מַזָּל טוֹב!</p>

    <hr class="rule">

    <div class="final-poem">
        <?php for ($s = 1; $s <= 5; $s++):
            $t = track(track_key_by_slot($s));
            $row = $bySlot[$s] ?? null;
        ?>
            <div class="stanza">
                <?php if ($row && trim((string) $row['limerick']) !== ''): ?>
                    <?= e($row['limerick']) ?>
                <?php else: ?>
                    <span class="muted">(קבוצה <?= $s ?> — אות <?= e($t['letter']) ?> — טרם הושלמה)</span>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>

    <div class="ornament">✦ ✦ ✦</div>
</div>

<div class="center" style="margin-top:20px">
    <a class="small" style="color:var(--gold-light)" href="index.php">חזרה לדף הבית</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
