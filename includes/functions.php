<?php
/**
 * פונקציות עזר כלליות למשחק.
 */

require_once __DIR__ . '/db.php';

function boot(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function config(): array
{
    static $c = null;
    if ($c === null) {
        $c = require __DIR__ . '/config.php';
    }
    return $c;
}

function game_data(): array
{
    static $d = null;
    if ($d === null) {
        $d = require __DIR__ . '/data.php';
    }
    return $d;
}

/** החזרת הגדרות הקבוצה לפי מפתח (abraham/isaac/...) */
function track(string $key): ?array
{
    $d = game_data();
    return $d[$key] ?? null;
}

/** החזרת מפתח הקבוצה לפי מספר משבצת (1-5) */
function track_key_by_slot(int $slot): ?string
{
    foreach (game_data() as $key => $t) {
        if ($t['slot'] === $slot) {
            return $key;
        }
    }
    return null;
}

/** בריחה בטוחה ל-HTML */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/** נרמול טקסט עברי להשוואה (ללא ניקוד/רווחים מיותרים) */
function normalize_he(string $s): string
{
    // הסרת ניקוד
    $s = preg_replace('/[\x{0591}-\x{05C7}]/u', '', $s);
    // הסרת גרשיים/מקפים/סימני פיסוק
    $s = preg_replace('/[\'"`׳״\-–\.,;:]/u', '', $s);
    $s = preg_replace('/\s+/u', ' ', trim($s));
    return $s;
}

/** טעינת הקבוצה הנוכחית מהסשן, או null */
function current_team(): ?array
{
    boot();
    if (empty($_SESSION['team_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT * FROM teams WHERE id = ?');
    $stmt->execute([$_SESSION['team_id']]);
    $team = $stmt->fetch();
    return $team ?: null;
}

/** דרישת קבוצה מחוברת — אחרת הפניה לדף הבית */
function require_team(): array
{
    $team = current_team();
    if (!$team) {
        header('Location: index.php');
        exit;
    }
    return $team;
}

function update_team(int $id, array $fields): void
{
    if (!$fields) {
        return;
    }
    $sets = [];
    $vals = [];
    foreach ($fields as $k => $v) {
        $sets[] = "$k = ?";
        $vals[] = $v;
    }
    $vals[] = $id;
    $sql = 'UPDATE teams SET ' . implode(', ', $sets) . ' WHERE id = ?';
    db()->prepare($sql)->execute($vals);
}

/** האם כל חמש הקבוצות סיימו? */
function all_done(): bool
{
    $n = (int) db()->query("SELECT COUNT(*) FROM teams WHERE stage >= 4 AND limerick IS NOT NULL AND limerick <> ''")->fetchColumn();
    return $n >= 5;
}

function admin_logged_in(): bool
{
    boot();
    return !empty($_SESSION['is_admin']);
}
