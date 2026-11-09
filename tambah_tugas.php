<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

wajib_login();
$uid = user_id();

$errors = [];
$old = [
    'nama_tugas'=>'','mata_kuliah'=>'','deskripsi'=>'',
    'tanggal_diberikan'=>'','tenggat_waktu'=>'','prioritas'=>'Sedang','status'=>'Belum Dikerjakan'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $v) {
        if (isset($_POST[$k])) $old[$k] = trim($_POST[$k]);
    }

    if ($old['nama_tugas']==='' ) $errors[] = 'Nama tugas wajib diisi.';
    if ($old['mata_kuliah']==='') $errors[] = 'Mata kuliah wajib diisi.';
    if ($old['tanggal_diberikan']==='' ) $errors[] = 'Tanggal diberikan wajib diisi.';
    if ($old['tenggat_waktu']==='' ) $errors[] = 'Tenggat waktu wajib diisi.';
    if (!in_array($old['prioritas'],['Rendah','Sedang','Tinggi'],true)) $errors[] = 'Prioritas tidak valid.';
    if (!in_array($old['status'],['Belum Dikerjakan','Sedang Dikerjakan','Selesai'],true)) $errors[] = 'Status tidak valid.';
    if ($old['tanggal_diberikan'] && $old['tenggat_waktu'] && $old['tenggat_waktu'] < $old['tanggal_diberikan']) {
        $errors[] = 'Tenggat waktu tidak boleh lebih awal dari tanggal diberikan.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO tugas (user_id,nama_tugas,mata_kuliah,deskripsi,tanggal_diberikan,tenggat_waktu,prioritas,status)
            VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $uid, $old['nama_tugas'], $old['mata_kuliah'], $old['deskripsi'],
            $old['tanggal_diberikan'], $old['tenggat_waktu'], $old['prioritas'], $old['status']
        ]);
        set_flash('success', 'Tugas berhasil ditambahkan.');
        header('Location: tugas.php');
        exit;
    }
}

$page_title = 'Tambah Tugas';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Tambah Tugas</h1>
    <p>Catat tugas kuliah baru agar tidak terlewat.</p>
</div>

<?php if ($errors): ?>
    <div class="alert alert-soft alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card-soft" style="max-width:720px">
    <form method="post" action="tambah_tugas.php">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Tugas <span style="color:#a23b3b">*</span></label>
                <input type="text" name="nama_tugas" class="form-control" value="<?= e($old['nama_tugas']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mata Kuliah <span style="color:#a23b3b">*</span></label>
                <input type="text" name="mata_kuliah" class="form-control" value="<?= e($old['mata_kuliah']) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi Tugas</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= e($old['deskripsi']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Diberikan <span style="color:#a23b3b">*</span></label>
                <input type="date" name="tanggal_diberikan" id="tanggal_diberikan" class="form-control" value="<?= e($old['tanggal_diberikan']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tenggat Waktu <span style="color:#a23b3b">*</span></label>
                <input type="date" name="tenggat_waktu" id="tenggat_waktu" class="form-control" value="<?= e($old['tenggat_waktu']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioritas</label>
                <select name="prioritas" class="form-select">
                    <?php foreach (['Rendah','Sedang','Tinggi'] as $p): ?>
                        <option value="<?= $p ?>" <?= $old['prioritas']===$p?'selected':'' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['Belum Dikerjakan','Sedang Dikerjakan','Selesai'] as $st): ?>
                        <option value="<?= $st ?>" <?= $old['status']===$st?'selected':'' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-dusty"><i class="bi bi-save me-1"></i> Simpan</button>
            <a href="tugas.php" class="btn btn-soft-outline">Batal</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
