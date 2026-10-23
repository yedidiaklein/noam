<?php
require_once __DIR__ . '/functions.php';
boot();
$cfg = config();
$pageTitle = $pageTitle ?? $cfg['game_title'];
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e($cfg['game_title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bona+Nova:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="parchment-bg"></div>
    <header class="site-header">
        <a href="index.php" class="brand">
            <span class="brand-mark">נ</span>
            <span class="brand-text">
                <strong><?= e($cfg['game_title']) ?></strong>
                <small><?= e($cfg['game_subtitle']) ?></small>
            </span>
        </a>
    </header>
    <main class="container">
