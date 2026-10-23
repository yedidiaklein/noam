<?php
require_once __DIR__ . '/includes/functions.php';
boot();

// אם כבר יש קבוצה מחוברת — המשך לשלב הנוכחי
$team = current_team();
if ($team) {
    $map = [1 => 'stage1.php', 2 => 'stage2.php', 3 => 'stage3.php'];
    header('Location: ' . ($map[$team['stage']] ?? 'done.php'));
    exit;
}

$error = '';
$data = game_data();

// מהן המשבצות התפוסות?
$taken = [];
foreach (db()->query('SELECT slot, team_name FROM teams')->fetchAll() as $r) {
    $taken[(int) $r['slot']] = $r['team_name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot = (int) ($_POST['slot'] ?? 0);
    $name = trim($_POST['team_name'] ?? '');
    $parts = trim($_POST['participants'] ?? '');
    $key = track_key_by_slot($slot);

    if (!$key) {
        $error = 'יש לבחור את מספר הקבוצה.';
    } elseif (isset($taken[$slot])) {
        // הקבוצה כבר נרשמה — חיבור מחדש (המשך מהמקום שבו עצרו)
        $stmt = db()->prepare('SELECT * FROM teams WHERE slot = ?');
        $stmt->execute([$slot]);
        $existing = $stmt->fetch();
        $_SESSION['team_id'] = $existing['id'];
        $map = [1 => 'stage1.php', 2 => 'stage2.php', 3 => 'stage3.php'];
        header('Location: ' . ($map[$existing['stage']] ?? 'done.php'));
        exit;
    } elseif ($name === '') {
        $error = 'נא להזין שם לקבוצה.';
    } else {
        $stmt = db()->prepare('INSERT INTO teams (slot, team_name, participants, stage) VALUES (?, ?, ?, 1)');
        $stmt->execute([$slot, $name, $parts]);
        $_SESSION['team_id'] = (int) db()->lastInsertId();
        header('Location: stage1.php');
        exit;
    }
}

$pageTitle = 'כניסה';
require __DIR__ . '/includes/header.php';
?>
<div class="card">
    <div class="ornament">✦ ✦ ✦</div>
    <img class="noam-portrait" src="assets/noam.png" alt="נֹעַם">
    <h1 class="center">בְּרוּכִים הַבָּאִים לְחֶדֶר הַבְּרִיחָה</h1>
    <p class="subtitle center">מסע בן חמישה שערים בעקבות אבות האומה ופייטני ישראל — לכבוד יום הולדתו של נֹעַם.</p>

    <hr class="rule">

    <p class="muted center">
        התחלקתם לזוגות, וכל זוג יקבל פִּסְקָה מן הפיוט <em>"זְכֹר אָב נִמְשַׁךְ אַחֲרֶיךָ כַּמַּיִם"</em>.
        <br>בחרו את מספר הקבוצה שקיבלתם, רִשמו את שמותיכם — וְצֵאוּ לַדֶּרֶךְ.
    </p>

    <?php if ($error): ?>
        <div class="msg err"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>מספר הקבוצה שלכם</label>
        <div class="slots">
            <?php foreach ($data as $t):
                $s = $t['slot'];
                $isTaken = isset($taken[$s]);
            ?>
                <label class="slot-opt">
                    <input type="radio" name="slot" value="<?= $s ?>" <?= $isTaken ? '' : 'required' ?>>
                    <div class="slot-card">
                        <div class="letter"><?= e($t['letter']) ?></div>
                        <div class="num">קבוצה <?= $s ?></div>
                        <?php if ($isTaken): ?>
                            <div class="taken">נרשמה — להמשך לחצו כאן</div>
                        <?php endif; ?>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="small muted center">כבר התחלתם? בחרו שוב את מספר הקבוצה שלכם כדי להמשיך מהמקום שבו עצרתם.</p>

        <label for="team_name">שם הקבוצה</label>
        <input type="text" id="team_name" name="team_name" placeholder="לדוגמה: נוֹדְדֵי הַמִּדְבָּר" maxlength="180">

        <label for="participants">שמות המשתתפים</label>
        <input type="text" id="participants" name="participants" placeholder="מי ומי בחבורה?" maxlength="480">

        <div class="center">
            <button class="btn" type="submit">פִּתְחוּ אֶת הַשַּׁעַר →</button>
        </div>
    </form>
</div>

<div class="center" style="margin-top:22px">
    <a class="small" style="color:var(--gold-light)" href="admin.php">כניסת מנהל המשחק</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
