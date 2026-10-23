<?php
require_once __DIR__ . '/includes/functions.php';
boot();
$cfg = config();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password'])) {
        if (hash_equals($cfg['admin_password'], (string) $_POST['password'])) {
            $_SESSION['is_admin'] = true;
        } else {
            $error = 'סיסמה שגויה.';
        }
    } elseif (isset($_POST['reset']) && admin_logged_in()) {
        db()->exec('DELETE FROM teams');
    } elseif (isset($_POST['logout_admin'])) {
        unset($_SESSION['is_admin']);
        header('Location: admin.php');
        exit;
    }
}

$pageTitle = 'לוח בקרה';
require __DIR__ . '/includes/header.php';

if (!admin_logged_in()):
?>
<div class="card">
    <h1 class="center">כְּנִיסַת מְנַהֵל הַמִּשְׂחָק</h1>
    <?php if ($error): ?><div class="msg err center"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <label for="password">סיסמת מנהל</label>
        <input type="password" id="password" name="password" autocomplete="off">
        <div class="center"><button class="btn" type="submit">כְּנִיסָה</button></div>
    </form>
</div>
<?php
else:
    $rows = db()->query('SELECT * FROM teams ORDER BY slot ASC')->fetchAll();
    $stageNames = [1 => 'זיהוי האב', 2 => 'חידה וקוד', 3 => 'כתיבת חרוז', 4 => 'סיימה'];
?>
<div class="card">
    <h1 class="center">לוּחַ בַּקָּרָה</h1>
    <p class="subtitle center">מעקב אחר התקדמות חמש הקבוצות.</p>

    <table class="admin">
        <thead>
            <tr>
                <th>#</th><th>אות</th><th>קבוצה</th><th>משתתפים</th>
                <th>אב</th><th>קוד</th><th>שלב</th><th>ניסיונות</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (game_data() as $t):
            $s = $t['slot'];
            $row = null;
            foreach ($rows as $r) { if ((int) $r['slot'] === $s) { $row = $r; break; } }
        ?>
            <tr>
                <td><?= $s ?></td>
                <td style="font-weight:700;color:var(--wine)"><?= e($t['letter']) ?></td>
                <td><?= $row ? e($row['team_name']) : '<span class="muted">—</span>' ?></td>
                <td class="small"><?= $row ? e($row['participants']) : '' ?></td>
                <td><?= $row && $row['forefather_ok'] ? '<span class="pill yes">✓</span>' : '<span class="pill no">✗</span>' ?></td>
                <td><?= $row && $row['code_ok'] ? '<span class="pill yes">✓</span>' : '<span class="pill no">✗</span>' ?></td>
                <td><?= $row ? e($stageNames[$row['stage']] ?? (string) $row['stage']) : '<span class="muted">טרם</span>' ?></td>
                <td><?= $row ? (int) $row['attempts'] : 0 ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <hr class="rule">
    <div class="center">
        <a class="btn" href="final.php">צְפִיָּה בַּשִּׁיר הַמְּלֵא ✦</a>
    </div>

    <hr class="rule">
    <form method="post" onsubmit="return confirm('לאפס את כל הקבוצות ולמחוק את כל ההתקדמות? פעולה זו אינה הפיכה.');">
        <button class="btn wine" name="reset" value="1" type="submit">אִפּוּס הַמִּשְׂחָק (מחיקת כל הקבוצות)</button>
    </form>
    <form method="post" style="margin-top:10px">
        <button class="btn secondary" name="logout_admin" value="1" type="submit">יְצִיאָה</button>
    </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
