<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

wajib_login();
$uid = user_id();

if (!isset($_GET['id'])) { header('Location: tugas.php'); exit; }
$id = (int) $_GET['id'];

$stmt = $pdo->prepare('SELECT id, nama_tugas FROM tugas WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, $uid]);
$tugas = $stmt->fetch();

if (!$tugas) {
    set_flash('error', 'Tugas tidak ditemukan atau bukan milik Anda.');
    header('Location: tugas.php');
    exit;
}

$del = $pdo->prepare('DELETE FROM tugas WHERE id = ? AND user_id = ?');
$del->execute([$id, $uid]);
set_flash('success', 'Tugas "' . $tugas['nama_tugas'] . '" berhasil dihapus.');
header('Location: tugas.php');
exit;
