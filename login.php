<?php
// =====================================================
// TaskMate - Login
// =====================================================
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';
sudah_login();

$err = '';
$email_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $email_input = e($email);

    if ($email === '' || $password === '') {
        $err = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Format email tidak valid.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $err = 'Email atau password salah.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int) $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_email']= $user['email'];
            set_flash('success', 'Selamat datang kembali, ' . $user['nama'] . '!');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • TaskMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo"><i class="bi bi-calendar-check"></i></div>
        <div class="auth-title">TaskMate</div>
        <div class="auth-sub">Manage Your Tasks, Achieve Your Goals.</div>
        <h2 class="auth-title" style="font-size:18px;margin-top:6px">Selamat Datang Kembali!</h2>

        <?php if ($err): ?>
            <div class="alert alert-soft alert-danger mt-3"><?= e($err) ?></div>
        <?php endif; ?>
        <?php if ($f = get_flash()): ?>
            <div class="alert alert-soft alert-<?= $f['type'] === 'success' ? 'success' : 'danger' ?> mt-3"><?= e($f['pesan']) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= $email_input ?>" placeholder="nama@email.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-dusty w-100 mt-2"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk</button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size:14px">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</div>
</body>
</html>
