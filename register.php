<?php
// =====================================================
// TaskMate - Registrasi
// =====================================================
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';
sudah_login();

$errors = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['konfirmasi'] ?? '';
    $old['nama']  = e($nama);
    $old['email'] = e($email);

    if ($nama === '') {
        $errors[] = 'Nama mahasiswa wajib diisi.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // Cek email unik
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email sudah terdaftar. Gunakan email lain.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
        $ins->execute([$nama, $email, $hash]);
        set_flash('success', 'Registrasi berhasil! Silakan masuk dengan akun Anda.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi • TaskMate</title>
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
        <h2 class="auth-title" style="font-size:18px;margin-top:6px">Daftar Akun Mahasiswa</h2>

        <?php if ($errors): ?>
            <div class="alert alert-soft alert-danger mt-3">
                <ul class="mb-0 ps-3"><?php foreach ($errors as $er): ?>
                    <li><?= e($er) ?></li>
                <?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <?php if ($f = get_flash()): ?>
            <div class="alert alert-soft alert-<?= $f['type'] === 'success' ? 'success' : 'danger' ?> mt-3"><?= e($f['pesan']) ?></div>
        <?php endif; ?>

        <form method="post" action="register.php" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Nama Mahasiswa</label>
                <input type="text" name="nama" class="form-control" value="<?= $old['nama'] ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= $old['email'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" minlength="6" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="konfirmasi" class="form-control" minlength="6" required>
            </div>
            <button type="submit" class="btn btn-dusty w-100 mt-1"><i class="bi bi-person-plus me-1"></i> Daftar</button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size:14px">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</div>
</body>
</html>
