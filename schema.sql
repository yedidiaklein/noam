-- ─────────────────────────────────────────────────────────────
--  אתחול מסד הנתונים לחדר הבריחה של נֹעַם
--  הרצה (כ-root): mysql -u root -p < schema.sql
-- ─────────────────────────────────────────────────────────────

-- 1) יצירת מסד הנתונים
CREATE DATABASE IF NOT EXISTS noam
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- 2) יצירת המשתמש והרשאות (עדכנו את הסיסמה בהתאם ל-includes/config.php)
CREATE USER IF NOT EXISTS 'noam'@'localhost' IDENTIFIED BY 'N0@m123!';
GRANT ALL PRIVILEGES ON noam.* TO 'noam'@'localhost';
FLUSH PRIVILEGES;

-- 3) הטבלה (נוצרת גם אוטומטית בטעינת הדף הראשונה; כאן למקרה של אתחול ידני)
USE noam;
CREATE TABLE IF NOT EXISTS teams (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    slot          TINYINT NOT NULL UNIQUE,
    team_name     VARCHAR(190) NOT NULL,
    participants  VARCHAR(500) NOT NULL DEFAULT '',
    stage         TINYINT NOT NULL DEFAULT 1,
    forefather_ok TINYINT NOT NULL DEFAULT 0,
    code_ok       TINYINT NOT NULL DEFAULT 0,
    limerick      TEXT NULL,
    attempts      INT NOT NULL DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- איפוס המשחק (אופציונלי): מוחק את כל הקבוצות
-- TRUNCATE TABLE teams;
