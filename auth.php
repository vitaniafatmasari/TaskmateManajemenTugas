<?php
// =====================================================
// TaskMate - Helper Autentikasi & Sesi
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Wajib login untuk membuka halaman terproteksi
function wajib_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Sudah login? arahkan ke dashboard (untuk halaman login/register)
function sudah_login(): void
{
    if (isset($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit;
    }
}

function user_id(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

function user_nama(): string
{
    return $_SESSION['user_nama'] ?? '';
}

function user_email(): string
{
    return $_SESSION['user_email'] ?? '';
}

// Pesan flash sederhana (sekali tampil)
function set_flash(string $type, string $pesan): void
{
    $_SESSION['flash'] = ['type' => $type, 'pesan' => $pesan];
}

function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Sanitasi output HTML
function e(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Pastikan sebuah tugas benar-benar milik user yang login.
// Mengembalikan data tugas atau menghentikan eksekusi.
function dapatkan_tugas_milik_user(PDO $pdo, int $id_tugas, int $id_user): array
{
    $stmt = $pdo->prepare('SELECT * FROM tugas WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$id_tugas, $id_user]);
    $tugas = $stmt->fetch();
    if (!$tugas) {
        set_flash('error', 'Tugas tidak ditemukan atau bukan milik Anda.');
        header('Location: tugas.php');
        exit;
    }
    return $tugas;
}
