<?php
// =====================================================
// TaskMate - Dashboard
// =====================================================

// 1. Panggil koneksi database
require_once __DIR__ . '/config/koneksi.php';

// 2. Panggil fungsi autentikasi
require_once __DIR__ . '/includes/auth.php';

// 3. Periksa login
wajib_login();

// 4. Ambil ID pengguna
$uid = user_id();

// Statistik
$stat = $pdo->prepare('SELECT
    COUNT(*) AS total,
    SUM(status = "Belum Dikerjakan") AS belum,
    SUM(status = "Sedang Dikerjakan") AS proses,
    SUM(status = "Selesai") AS selesai
  FROM tugas WHERE user_id = ?');
$stat->execute([$uid]);
$s = $stat->fetch();
$total  = (int)($s['total']  ?? 0);
$belum  = (int)($s['belum']  ?? 0);
$proses = (int)($s['proses'] ?? 0);
$selesai= (int)($s['selesai']?? 0);
$persen = $total > 0 ? round(($selesai / $total) * 100) : 0;

// Tugas terbaru (5)
$baru = $pdo->prepare('SELECT * FROM tugas WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$baru->execute([$uid]);
$tugas_baru = $baru->fetchAll();

// Tenggat terdekat (5, yang belum selesai)
$dekat = $pdo->prepare('SELECT * FROM tugas WHERE user_id = ? AND status <> "Selesai" ORDER BY tenggat_waktu ASC LIMIT 5');
$dekat->execute([$uid]);
$tugas_dekat = $dekat->fetchAll();

function badge_status(string $st): string {
    return match($st) {
        'Selesai' => 'bg-selesai',
        'Sedang Dikerjakan' => 'bg-proses',
        default => 'bg-belum'
    };
}

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Halo, Selamat Datang! <?= e(user_nama()) ?></h1>
    <p>Yuk, kelola tugas kuliahmu agar lebih teratur dan selesai tepat waktu.</p>
</div>

<?php if ($f = get_flash()): ?>
    <div class="alert alert-soft alert-<?= $f['type'] === 'success' ? 'success' : 'danger' ?>"><?= e($f['pesan']) ?></div>
<?php endif; ?>

<div class="stat-grid">
    <div class="stat-card stat-lavender">
        <div class="stat-icon"><i class="bi bi-journals"></i></div>
        <div class="stat-num"><?= $total ?></div>
        <div class="stat-label">Total Tugas</div>
    </div>
    <div class="stat-card stat-pink">
        <div class="stat-icon"><i class="bi bi-hourglass"></i></div>
        <div class="stat-num"><?= $belum ?></div>
        <div class="stat-label">Belum Dikerjakan</div>
    </div>
    <div class="stat-card stat-peach">
        <div class="stat-icon"><i class="bi bi-pencil"></i></div>
        <div class="stat-num"><?= $proses ?></div>
        <div class="stat-label">Sedang Dikerjakan</div>
    </div>
    <div class="stat-card stat-sage">
        <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="stat-num"><?= $selesai ?></div>
        <div class="stat-label">Selesai</div>
    </div>
</div>

<div class="card-soft mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Progress Penyelesaian</h5>
        <span class="badge-pill bg-belum"><?= $persen ?>%</span>
    </div>
    <div class="progress-soft"><div class="bar" style="width:<?= $persen ?>%"></div></div>
    <p class="mt-2 mb-0" style="font-size:13px;color:#9b7e8c"><?= $selesai ?> dari <?= $total ?> tugas telah diselesaikan.</p>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-soft h-100">
            <div class="section-title"><i class="bi bi-clock-history me-1"></i> Tugas Terbaru</div>
            <?php if (!$tugas_baru): ?>
                <div class="empty-state"><i class="bi bi-inbox"></i>Belum ada tugas.</div>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                <?php foreach ($tugas_baru as $t): ?>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:rgba(217,154,170,.12)!important">
                        <div>
                            <div style="font-weight:600"><?= e($t['nama_tugas']) ?></div>
                            <small style="color:#9b7e8c"><?= e($t['mata_kuliah']) ?></small>
                        </div>
                        <span class="badge-pill <?= badge_status($t['status']) ?>"><?= e($t['status']) ?></span>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-soft h-100">
            <div class="section-title"><i class="bi bi-alarm me-1"></i> Tenggat Terdekat</div>
            <?php if (!$tugas_dekat): ?>
                <div class="empty-state"><i class="bi bi-check2-all"></i>Tidak ada tugas mendesak.</div>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                <?php foreach ($tugas_dekat as $t):
                    $hari = (strtotime($t['tenggat_waktu']) - strtotime(date('Y-m-d'))) / 86400;
                    $ket = $hari < 0 ? 'Terlambat ' . abs((int)$hari) . ' hari'
                          : ($hari == 0 ? 'Hari ini' : $hari . ' hari lagi');
                ?>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:rgba(217,154,170,.12)!important">
                        <div>
                            <div style="font-weight:600"><?= e($t['nama_tugas']) ?></div>
                            <small style="color:#9b7e8c">Tenggat: <?= date('d M Y', strtotime($t['tenggat_waktu'])) ?></small>
                        </div>
                        <small style="color:<?= $hari < 0 ? '#a23b3b' : '#a96f3b' ?>;font-weight:600"><?= $ket ?></small>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
