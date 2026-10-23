<?php
require_once __DIR__ . '/includes/functions.php';
$team = require_team();
$key = track_key_by_slot((int) $team['slot']);
$t = track($key);

// שמירה על סדר השלבים
if ($team['stage'] < 2) {
    header('Location: stage1.php');
    exit;
}
if ($team['stage'] > 2) {
    header('Location: stage3.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = preg_replace('/\D/', '', ($_POST['d1'] ?? '') . ($_POST['d2'] ?? '') . ($_POST['d3'] ?? ''));
    if (strlen($code) !== 3) {
        $error = 'הקריפטקס דורש שלוש ספרות.';
    } elseif ($code === $t['code']) {
        update_team((int) $team['id'], ['stage' => 3, 'code_ok' => 1]);
        header('Location: stage3.php');
        exit;
    } else {
        update_team((int) $team['id'], ['attempts' => $team['attempts'] + 1]);
        $error = 'הקריפטקס לא נפתח… בדקו את שלוש הספרות ונסו שוב.';
    }
}

$pageTitle = 'שער שני';
require __DIR__ . '/includes/header.php';
?>
<div class="stepper">
    <div class="step done"><span class="n">✓</span> זיהוי האב</div>
    <div class="step active"><span class="n">2</span> החידה והקוד</div>
    <div class="step"><span class="n">3</span> החרוז לנֹעַם</div>
</div>

<div class="card">
    <div class="center"><span class="badge-letter"><?= e($t['letter']) ?></span></div>
    <h1 class="center"><?= e($t['riddle_title']) ?></h1>
    <p class="riddle-note"><?= e($t['riddle_note']) ?></p>

    <div class="piyyut"><?= e($t['poem']) ?></div>

    <div class="msg info center">
        הַקּוֹד הַמְבֻקָּשׁ מֻרְכָּב מִשְּׁלֹשׁ סְפָרוֹת.<br>
        <span class="small">רמז כללי: בכל שלב שבו תגיעו למספר רב-ספרתי — צַמְצְמוּ אותו בשיטה המוכרת (חיבור ספרות) עד לספרה יחידה.</span>
    </div>

    <ul class="clues">
        <?php foreach ($t['clues'] as $i => $clue): ?>
            <li class="clue">
                <h3><?= e($clue[0]) ?></h3>
                <p><?= e($clue[1]) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($error): ?>
        <div class="msg err center"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <p class="center muted">הזינו את שלוש הספרות אל הקריפטקס:</p>
        <div class="cryptex">
            <input type="text" name="d1" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" required>
            <input type="text" name="d2" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" required>
            <input type="text" name="d3" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" required>
        </div>
        <div class="center">
            <button class="btn wine" type="submit">סוֹבְבוּ אֶת הַגַּלְגַּל 🔓</button>
        </div>
    </form>
</div>

<script>
// מעבר אוטומטי בין ספרות הקריפטקס
const boxes = Array.from(document.querySelectorAll('.cryptex input'));
boxes.forEach((box, i) => {
    box.addEventListener('input', () => {
        box.value = box.value.replace(/\D/g, '');
        if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
    });
    box.addEventListener('keydown', (ev) => {
        if (ev.key === 'Backspace' && !box.value && i > 0) boxes[i - 1].focus();
    });
});
boxes[0].focus();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
