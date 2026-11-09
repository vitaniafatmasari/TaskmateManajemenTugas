<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

wajib_login();
$uid = user_id();

// Ubah status cepat via GET ?ubah_status=ID&status=...
if (isset($_GET['ubah_status'], $_GET['status'])) {
    $id = (int) $_GET['ubah_status'];
    $status = $_GET['status'];
    $allowed = ['Belum Dikerjakan','Sedang Dikerjakan','Selesai'];
    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare('UPDATE tugas SET status = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$status, $id, $uid]);
        set_flash('success', 'Status tugas diperbarui menjadi "' . $status . '".');
    }
    header('Location: tugas.php?' . http_build_query([
        'q' => $_GET['q'] ?? '', 'status' => $_GET['status_f'] ?? '', 'prioritas' => $_GET['prioritas_f'] ?? ''
    ]));
    exit;
}

$q         = trim($_GET['q'] ?? '');
$fStatus   = $_GET['status'] ?? '';
$fPrio     = $_GET['prioritas'] ?? '';

$sql = 'SELECT * FROM tugas WHERE user_id = ?';
$params = [$uid];

if ($q !== '') {
    $sql .= ' AND (nama_tugas LIKE ? OR mata_kuliah LIKE ?)';
    $params[] = "%$q%"; $params[] = "%$q%";
}
if ($fStatus !== '') {
    $sql .= ' AND status = ?'; $params[] = $fStatus;
}
if ($fPrio !== '') {
    $sql .= ' AND prioritas = ?'; $params[] = $fPrio;
}
$sql .= ' ORDER BY tenggat_waktu ASC, created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tugas_list = $stmt->fetchAll();

function badge_status(string $st): string {
    return match($st) {
        'Selesai' => 'bg-selesai',
        'Sedang Dikerjakan' => 'bg-proses',
        default => 'bg-belum'
    };
}
function badge_prio(string $p): string {
    return match($p) {
        'Tinggi' => 'bg-tinggi',
        'Rendah' => 'bg-rendah',
        default => 'bg-sedang'
    };
}

$page_title = 'Daftar Tugas';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-head d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Daftar Tugas</h1>
        <p>Kelola seluruh tugas kuliahmu di satu tempat.</p>
    </div>
    <a href="tambah_tugas.php" class="btn btn-dusty"><i class="bi bi-plus-lg me-1"></i> Tambah Tugas</a>
</div>

<?php if ($f = get_flash()): ?>
    <div class="alert alert-soft alert-<?= $f['type'] === 'success' ? 'success' : 'danger' ?>"><?= e($f['pesan']) ?></div>
<?php endif; ?>

<div class="card-soft mb-3">
    <form method="get" action="tugas.php" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Cari Tugas</label>
            <input type="text" name="q" class="form-control" value="<?= e($q) ?>" placeholder="Nama tugas / mata kuliah">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="Belum Dikerjakan" <?= $fStatus==='Belum Dikerjakan'?'selected':'' ?>>Belum Dikerjakan</option>
                <option value="Sedang Dikerjakan" <?= $fStatus==='Sedang Dikerjakan'?'selected':'' ?>>Sedang Dikerjakan</option>
                <option value="Selesai" <?= $fStatus==='Selesai'?'selected':'' ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Prioritas</label>
            <select name="prioritas" class="form-select">
                <option value="">Semua</option>
                <option value="Rendah" <?= $fPrio==='Rendah'?'selected':'' ?>>Rendah</option>
                <option value="Sedang" <?= $fPrio==='Sedang'?'selected':'' ?>>Sedang</option>
                <option value="Tinggi" <?= $fPrio==='Tinggi'?'selected':'' ?>>Tinggi</option>
            </select>
        </div>
        <div class="col-md-1 d-grid">
            <button class="btn btn-dusty" type="submit"><i class="bi bi-funnel"></i></button>
        </div>
    </form>
</div>

<div class="table-soft">
    <?php if (!$tugas_list): ?>
        <div class="empty-state">
            <i class="bi bi-clipboard-x"></i>
            <p>Belum ada tugas yang ditambahkan atau tidak ada hasil yang cocok.</p>
            <a href="tambah_tugas.php" class="btn btn-dusty mt-2"><i class="bi bi-plus-lg me-1"></i> Tambah Tugas Pertama</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
        <table>
            <thead><tr>
                <th style="width:50px">No</th>
                <th>Nama Tugas</th>
                <th>Mata Kuliah</th>
                <th>Tgl Diberikan</th>
                <th>Tenggat</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr></thead>
            <tbody>
            <?php $no=1; foreach ($tugas_list as $t): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><div style="font-weight:600"><?= e($t['nama_tugas']) ?></div>
                        <?php if ($t['deskripsi']): ?><small style="color:#9b7e8c"><?= e(mb_strimwidth($t['deskripsi'],0,50,'…')) ?></small><?php endif; ?>
                    </td>
                    <td><?= e($t['mata_kuliah']) ?></td>
                    <td><?= date('d M Y', strtotime($t['tanggal_diberikan'])) ?></td>
                    <td><?= date('d M Y', strtotime($t['tenggat_waktu'])) ?></td>
                    <td><span class="badge-pill <?= badge_prio($t['prioritas']) ?>"><?= e($t['prioritas']) ?></span></td>
                    <td>
                        <select class="form-select form-select-sm" style="width:auto;display:inline-block" onchange="window.location='tugas.php?ubah_status=<?= $t['id'] ?>&status='+this.value+'&<?= http_build_query(['q'=>$q,'status_f'=>$fStatus,'prioritas_f'=>$fPrio]) ?>'">
                            <?php foreach (['Belum Dikerjakan','Sedang Dikerjakan','Selesai'] as $st): ?>
                                <option value="<?= $st ?>" <?= $t['status']===$st?'selected':'' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="text-end">
                        <a href="edit_tugas.php?id=<?= $t['id'] ?>" class="btn btn-soft-outline btn-sm-soft" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="hapus_tugas.php?id=<?= $t['id'] ?>" class="btn btn-soft-outline btn-sm-soft" data-confirm-hapus="Yakin ingin menghapus tugas &quot;<?= e($t['nama_tugas']) ?>&quot;?" title="Hapus"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
