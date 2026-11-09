<?php
// =====================================================
// TaskMate - Profil
// =====================================================

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

wajib_login();
$uid = user_id();

$err_nama = ''; $err_pass = ''; $ok_nama = ''; $ok_pass = '';

// Ambil data user
$stmt = $pdo->prepare('SELECT id, nama, email FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$uid]);
$user = $stmt->fetch();
if (!$user) { session_destroy(); header('Location: login.php'); exit; }

// Update nama
if (isset($_POST['update_nama'])) {
    $nama_baru = trim($_POST['nama'] ?? '');
    if ($nama_baru === '') {
        $err_nama = 'Nama tidak boleh kosong.';
    } else {
        $up = $pdo->prepare('UPDATE users SET nama = ? WHERE id = ?');
        $up->execute([$nama_baru, $uid]);
        $_SESSION['user_nama'] = $nama_baru;
        $user['nama'] = $nama_baru;
        $ok_nama = 'Nama berhasil diperbarui.';
    }
}

// Update password
if (isset($_POST['update_password'])) {
    $lama = $_POST['password_lama'] ?? '';
    $baru = $_POST['password_baru'] ?? '';
    $konf = $_POST['konfirmasi'] ?? '';

    $stmtP = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmtP->execute([$uid]);
    $hashDb = $stmtP->fetchColumn();

    if (!$hashDb || !password_verify($lama, $hashDb)) {
        $err_pass = 'Password lama salah.';
    } elseif (strlen($baru) < 6) {
        $err_pass = 'Password baru minimal 6 karakter.';
    } elseif ($baru !== $konf) {
        $err_pass = 'Konfirmasi password tidak cocok.';
    } else {
        $newHash = password_hash($baru, PASSWORD_DEFAULT);
        $up = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $up->execute([$newHash, $uid]);
        $ok_pass = 'Password berhasil diubah.';
    }
}

$page_title = 'Profil';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Profil</h1>
    <p>Kelola informasi akun dan keamanan Anda.</p>
</div>

<?php if ($f = get_flash()): ?>
    <div class="alert alert-soft alert-<?= $f['type']==='success'?'success':'danger' ?>"><?= e($f['pesan']) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card-soft text-center">
            <div class="auth-logo" style="margin:0 auto 14px"><i class="bi bi-person"></i></div>
            <h5 class="mb-1"><?= e($user['nama']) ?></h5>
            <p class="mb-2" style="color:#9b7e8c;font-size:14px"><?= e($user['email']) ?></p>
            <span class="badge-pill bg-belum">Mahasiswa</span>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-soft mb-3">
            <div class="section-title"><i class="bi bi-person-gear me-1"></i> Ubah Nama</div>
            <?php if ($err_nama): ?><div class="alert alert-soft alert-danger py-2"><?= e($err_nama) ?></div><?php endif; ?>
            <?php if ($ok_nama): ?><div class="alert alert-soft alert-success py-2"><?= e($ok_nama) ?></div><?php endif; ?>
            <form method="post" action="profil.php">
                <input type="hidden" name="update_nama" value="1">
                <div class="mb-3">
                    <label class="form-label">Nama Mahasiswa</label>
                    <input type="text" name="nama" class="form-control" value="<?= e($user['nama']) ?>" required>
                </div>
                <button class="btn btn-dusty"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
            </form>
        </div>

        <div class="card-soft">
            <div class="section-title"><i class="bi bi-shield-lock me-1"></i> Ubah Password</div>
            <?php if ($err_pass): ?><div class="alert alert-soft alert-danger py-2"><?= e($err_pass) ?></div><?php endif; ?>
            <?php if ($ok_pass): ?><div class="alert alert-soft alert-success py-2"><?= e($ok_pass) ?></div><?php endif; ?>
            <form method="post" action="profil.php">
                <input type="hidden" name="update_password" value="1">
                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="password_lama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" minlength="6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="konfirmasi" class="form-control" minlength="6" required>
                </div>
                <button class="btn btn-dusty"><i class="bi bi-key me-1"></i> Ubah Password</button>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
