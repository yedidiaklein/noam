<?php
require_once __DIR__ . '/includes/functions.php';
$team = require_team();
$key = track_key_by_slot((int) $team['slot']);
$t = track($key);

// אם כבר עברו את השלב — קדימה
if ($team['stage'] > 1) {
    header('Location: stage2.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ans = normalize_he($_POST['answer'] ?? '');
    $accepted = array_map('normalize_he', $t['figure_alts']);
    if ($ans === '') {
        $error = 'נא להקליד את שם האב.';
    } elseif (in_array($ans, $accepted, true)) {
        update_team((int) $team['id'], ['stage' => 2, 'forefather_ok' => 1]);
        header('Location: stage2.php');
        exit;
    } else {
        update_team((int) $team['id'], ['attempts' => $team['attempts'] + 1]);
        $error = 'עדיין לא. הביטו שוב בפסוקים — מי הדמות הנרמזת במים?';
    }
}

$pageTitle = 'שער ראשון';
require __DIR__ . '/includes/header.php';
?>
<div class="stepper">
    <div class="step active"><span class="n">1</span> זיהוי האב</div>
    <div class="step"><span class="n">2</span> החידה והקוד</div>
    <div class="step"><span class="n">3</span> החרוז לנֹעַם</div>
</div>

<div class="card">
    <div class="center"><span class="badge-letter"><?= e($t['letter']) ?></span></div>
    <h1 class="center">שַׁעַר רִאשׁוֹן · זִהוּי הָאָב</h1>
    <p class="subtitle center">קבוצה <?= (int) $team['slot'] ?> — <?= e($team['team_name']) ?></p>

    <p class="muted center">לפניכם הפִּסְקָה שלכם מן הפיוט <em>"זְכֹר אָב נִמְשַׁךְ אַחֲרֶיךָ כַּמַּיִם"</em>.
        כל בית מתאר דמות אחרת מאבות האומה. <strong>מיהו האב הנרמז בפסקה שלכם?</strong></p>

    <div class="piyyut"><?= e($t['opening']) ?></div>

    <div class="msg info center">בחרו מתוך: אַבְרָהָם · יִצְחָק · יַעֲקֹב · מֹשֶׁה · אַהֲרֹן</div>

    <?php if ($error): ?>
        <div class="msg err center"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="answer" class="center">שם האב הנרמז</label>
        <input type="text" id="answer" name="answer" autocomplete="off" placeholder="הקלידו כאן…" style="text-align:center;font-size:22px">
        <div class="center">
            <button class="btn" type="submit">אַשֵּׁר →</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
