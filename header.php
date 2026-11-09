<?php
// =====================================================
// TaskMate - Header bersama (semua halaman)
// =================================================----
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' • TaskMate' : 'TaskMate' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php if (isset($_SESSION['user_id'])): // halaman terproteksi punya sidebar ?>
<div class="app-shell">
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    <div class="app-main">
        <button class="btn-mobile-toggle d-lg-none" id="btnToggleSidebar" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>
<?php endif; ?>
