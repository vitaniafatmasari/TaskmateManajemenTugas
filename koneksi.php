<?php
// =====================================================
// TaskMate - Koneksi Database (PDO + MySQL)
// =====================================================
// Sesuaikan nilai di bawah dengan konfigurasi server Anda.
// Untuk InfinityFree, ganti sesuai detail database hosting.

define('DB_HOST', 'localhost');
define('DB_NAME', 'manajemen_tugas');
define('DB_USER', 'root');      // ganti pada hosting (mis. if0_xxx)
define('DB_PASS', '');           // ganti pada hosting
define('DB_CHARSET', 'utf8mb4');

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Tampilkan pesan ramah (tanpa membocorkan detail sensitif di produksi)
    http_response_code(500);
    die('Koneksi database gagal. Periksa konfigurasi pada config/koneksi.php. '
        . '<br><small>' . htmlspecialchars($e->getMessage()) . '</small>');
}
