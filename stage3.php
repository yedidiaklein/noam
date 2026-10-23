<?php
require_once __DIR__ . '/includes/functions.php';
$team = require_team();
$key = track_key_by_slot((int) $team['slot']);
$t = track($key);

if ($team['stage'] < 3) {
    header('Location: stage2.php');
    exit;
}
if ($team['stage'] > 3) {
    header('Location: done.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['limerick'] ?? '');
    $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text)), fn($l) => $l !== ''));

    if (mb_strlen($text) < 15) {
        $error = 'כתבו לפחות כמה שורות של ברכה לנֹעַם.';
    } elseif (count($lines) < 3) {
        $error = 'חרוז ראוי לנֹעַם זקוק לשלוש שורות לכל הפחות.';
    } else {
        // ודאו שלפחות השורה הראשונה פותחת באות הקבוצה (ליצירת האקרוסטיכון)
        $firstLetter = mb_substr(preg_replace('/[\x{0591}-\x{05C7}\s]/u', '', $lines[0]), 0, 1);
        if ($firstLetter !== $t['letter']) {
            $error = 'כדי שהאקרוסטיכון יתקבל — השורה הפותחת צריכה להתחיל באות ' . $t['letter'] . '.';
        } else {
            update_team((int) $team['id'], ['stage' => 4, 'limerick' => $text]);
            header('Location: done.php');
            exit;
        }
    }
}

$pageTitle = 'שער שלישי';
require __DIR__ . '/includes/header.php';
?>
<div class="stepper">
    <div class="step done"><span class="n">✓</span> זיהוי האב</div>
    <div class="step done"><span class="n">✓</span> החידה והקוד</div>
    <div class="step active"><span class="n">3</span> החרוז לנֹעַם</div>
</div>

<div class="card">
    <div class="center"><span class="badge-letter"><?= e($t['letter']) ?></span></div>
    <h1 class="center">שַׁעַר שְׁלִישִׁי · הַחֲרוּז לְנֹעַם</h1>
    <p class="subtitle center">פתחתם את הקריפטקס! זכיתם באות <strong><?= e($t['letter']) ?></strong>.</p>

    <p class="muted center">
        חמש הקבוצות יחד מרכיבות שיר ברכה לנֹעַם, שראשי בתיו מצטרפים לאקרוסטיכון <strong>נֹעַם כ״ו</strong>.
        <br>כִּתבו חרוז (לימריק) שהשורה הפותחת שלו מתחילה באות שלכם — <strong><?= e($t['letter']) ?></strong>.
    </p>

    <div class="msg info">
        <strong>מילות פתיחה מוצעות (לא מחייבות):</strong>
        <?= e(implode(' · ', $t['limerick_words'])) ?>
        <br><span class="small muted">משקל הלימריק: חמש שורות — שלוש ארוכות (שורות 1,2,5) ושתיים קצרות (3,4). החריזה: א-א-ב-ב-א. קצבי, שמח, ומחבק.</span>
    </div>

    <?php if ($error): ?>
        <div class="msg err center"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="limerick">החרוז שלכם לנֹעַם</label>
        <textarea id="limerick" name="limerick" placeholder="<?= e($t['letter']) ?>…&#10;…&#10;…&#10;…&#10;…"><?= e($_POST['limerick'] ?? ($t['letter'] . ' ')) ?></textarea>
        <div class="center">
            <button class="btn" type="submit">הַגִּישׁוּ אֶת הַבְּרָכָה ✦</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
