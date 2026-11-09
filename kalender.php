<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

wajib_login();
$uid = user_id();

$bulan = isset($_GET['b']) ? (int)$_GET['b'] : (int)date('n');
$tahun = isset($_GET['t']) ? (int)$_GET['t'] : (int)date('Y');
if ($bulan < 1 || $bulan > 12) $bulan = (int)date('n');
if ($tahun < 2000 || $tahun > 2100) $tahun = (int)date('Y');

$nama_bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$nama_hari = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

// Navigasi
$prev_b = $bulan==1?12:$bulan-1; $prev_t = $bulan==1?$tahun-1:$tahun;
$next_b = $bulan==12?1:$bulan+1; $next_t = $bulan==12?$tahun+1:$tahun;

// Ambil tugas bulan ini
$awal = sprintf('%04d-%02d-01', $tahun, $bulan);
$akhir = date('Y-m-t', strtotime($awal));
$stmt = $pdo->prepare('SELECT * FROM tugas WHERE user_id = ? AND tenggat_waktu BETWEEN ? AND ? ORDER BY tenggat_waktu ASC');
$stmt->execute([$uid, $awal, $akhir]);
$tugas_bulan = $stmt->fetchAll();

// Kelompokkan per tanggal
$per_tgl = [];
foreach ($tugas_bulan as $t) {
    $per_tgl[$t['tenggat_waktu']][] = $t;
}

// Sel
$hari_pertama = (int)date('w', strtotime($awal)); // 0=Minggu
$jumlah_hari = (int)date('t', strtotime($awal));
$hari_ini = date('Y-m-d');

function kelas_status(string $st): string {
    return match($st) {
        'Selesai' => 'selesai',
        'Sedang Dikerjakan' => 'proses',
        default => 'belum'
    };
}

// Daftar tugas mendekati tenggat (7 hari ke depan, belum selesai)
$mt = $pdo->prepare('SELECT * FROM tugas WHERE user_id = ? AND status <> "Selesai" AND tenggat_waktu BETWEEN ? AND ? ORDER BY tenggat_waktu ASC LIMIT 8');
$mt->execute([$uid, $hari_ini, date('Y-m-d', strtotime('+7 days'))]);
$mendekati = $mt->fetchAll();

$page_title = 'Kalender';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Kalender</h1>
    <p>Pantau tenggat waktu tugasmu sepanjang bulan.</p>
</div>

<?php if ($f = get_flash()): ?>
    <div class="alert alert-soft alert-<?= $f['type']==='success'?'success':'danger' ?>"><?= e($f['pesan']) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-soft">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="kalender.php?b=<?= $prev_b ?>&t=<?= $prev_t ?>" class="btn btn-soft-outline btn-sm-soft"><i class="bi bi-chevron-left"></i></a>
                <h5 class="mb-0"><?= $nama_bulan[$bulan] . ' ' . $tahun ?></h5>
                <a href="kalender.php?b=<?= $next_b ?>&t=<?= $next_t ?>" class="btn btn-soft-outline btn-sm-soft"><i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="cal-grid mb-2">
                <?php foreach ($nama_hari as $h): ?>
                    <div class="cal-head"><?= $h ?></div>
                <?php endforeach; ?>
            </div>
            <div class="cal-grid">
                <?php for ($i=0; $i<$hari_pertama; $i++): ?>
                    <div class="cal-cell empty"></div>
                <?php endfor; ?>
                <?php for ($d=1; $d<=$jumlah_hari; $d++):
                    $tgl = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                    $is_today = $tgl === $hari_ini;
                    $tasks = $per_tgl[$tgl] ?? [];
                ?>
                    <div class="cal-cell <?= $is_today?'today':'' ?>">
                        <span class="cal-num"><?= $d ?></span>
                        <?php foreach (array_slice($tasks,0,3) as $t): ?>
                            <span class="cal-task <?= kelas_status($t['status']) ?>" title="<?= e($t['nama_tugas']) ?>"><?= e($t['nama_tugas']) ?></span>
                        <?php endforeach; ?>
                        <?php if (count($tasks) > 3): ?>
                            <span class="cal-task" style="background:var(--gray);color:#9b7e8c">+<?= count($tasks)-3 ?></span>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="d-flex gap-3 mt-3 flex-wrap" style="font-size:12px">
                <span><span class="badge-pill bg-belum">●</span> Belum Dikerjakan</span>
                <span><span class="badge-pill bg-proses">●</span> Sedang Dikerjakan</span>
                <span><span class="badge-pill bg-selesai">●</span> Selesai</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-soft h-100">
            <div class="section-title"><i class="bi bi-alarm me-1"></i> Tugas Mendekati Tenggat</div>
            <?php if (!$mendekati): ?>
                <div class="empty-state"><i class="bi bi-check2-all"></i>Tidak ada tugas dalam 7 hari ke depan.</div>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                <?php foreach ($mendekati as $t):
                    $hari = (int)((strtotime($t['tenggat_waktu']) - strtotime($hari_ini)) / 86400);
                    $ket = $hari < 0 ? 'Terlambat' : ($hari == 0 ? 'Hari ini' : $hari.' hari lagi');
                ?>
                    <li class="py-2 border-bottom" style="border-color:rgba(217,154,170,.12)!important">
                        <div style="font-weight:600"><?= e($t['nama_tugas']) ?></div>
                        <div class="d-flex justify-content-between" style="font-size:12px">
                            <span style="color:#9b7e8c"><?= e($t['mata_kuliah']) ?></span>
                            <span style="color:<?= $hari <= 1 ? '#a23b3b' : '#a96f3b' ?>;font-weight:600"><?= $ket ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
