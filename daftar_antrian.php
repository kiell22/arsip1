<?php
if (empty($_SESSION['admin'])) {
    $_SESSION['err'] = '<center>Anda harus login terlebih dahulu!</center>';
    header("Location: ./");
    die();
}

$today = date('Y-m-d');

if (isset($_POST['update_status'])) {
    $id      = (int)$_POST['id_antrian'];
    $status  = mysqli_real_escape_string($config, $_POST['status']);
    $catatan = mysqli_real_escape_string($config, $_POST['catatan_pegawai']);
    $id_user = $_SESSION['id_user'];
    $allowed = ['menunggu','diperiksa','disetujui','ditolak'];
    if (in_array($status, $allowed)) {
        mysqli_query($config, "UPDATE tbl_antrian SET status='$status', catatan_pegawai='$catatan', tgl_proses=NOW(), id_user='$id_user' WHERE id_antrian='$id'");
        $_SESSION['succAntrian'] = 'Status antrian berhasil diperbarui.';
    }
    header("Location: ./admin.php?page=antrian");
    die();
}

if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $q_file = mysqli_query($config, "SELECT file_surat FROM tbl_antrian WHERE id_antrian='$id'");
    $r_file = mysqli_fetch_array($q_file);
    if (!empty($r_file['file_surat'])) {
        foreach (explode(',', $r_file['file_surat']) as $f) {
            @unlink('upload/legalisir/' . trim($f));
        }
    }
    mysqli_query($config, "DELETE FROM tbl_antrian WHERE id_antrian='$id'");
    $_SESSION['succAntrian'] = 'Data antrian berhasil dihapus.';
    header("Location: ./admin.php?page=antrian");
    die();
}

$filter_tgl    = isset($_GET['tgl'])    ? mysqli_real_escape_string($config, $_GET['tgl'])    : $today;
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($config, $_GET['status']) : '';

$where  = "WHERE tgl_daftar='$filter_tgl'";
if (!empty($filter_status)) $where .= " AND status='$filter_status'";

$q_list = mysqli_query($config, "SELECT * FROM tbl_antrian $where ORDER BY no_antrian ASC");

// Hitung per status hari ini
$c_all       = mysqli_num_rows(mysqli_query($config, "SELECT id_antrian FROM tbl_antrian WHERE tgl_daftar='$filter_tgl'"));
$c_menunggu  = mysqli_num_rows(mysqli_query($config, "SELECT id_antrian FROM tbl_antrian WHERE tgl_daftar='$filter_tgl' AND status='menunggu'"));
$c_diperiksa = mysqli_num_rows(mysqli_query($config, "SELECT id_antrian FROM tbl_antrian WHERE tgl_daftar='$filter_tgl' AND status='diperiksa'"));
$c_disetujui = mysqli_num_rows(mysqli_query($config, "SELECT id_antrian FROM tbl_antrian WHERE tgl_daftar='$filter_tgl' AND status='disetujui'"));
$c_ditolak   = mysqli_num_rows(mysqli_query($config, "SELECT id_antrian FROM tbl_antrian WHERE tgl_daftar='$filter_tgl' AND status='ditolak'"));

$status_label = ['menunggu'=>'Menunggu','diperiksa'=>'Verifikasi','disetujui'=>'Disetujui','ditolak'=>'Ditolak'];
$status_color = ['menunggu'=>'yellow darken-3','diperiksa'=>'blue','disetujui'=>'green','ditolak'=>'deep-orange'];
$status_icon  = ['menunggu'=>'hourglass_empty','diperiksa'=>'find_in_page','disetujui'=>'check_circle','ditolak'=>'cancel'];
?>

<style>
.stat-cards { display:flex; gap:10px; flex-wrap:wrap; margin:16px 0 10px; }
.stat-card {
    flex:1; min-width:100px;
    background:#fff; border-radius:8px;
    padding:12px 14px; text-align:center;
    box-shadow:0 1px 6px rgba(0,0,0,0.08);
    border-top:3px solid #ccc;
    cursor:pointer; text-decoration:none; display:block;
    transition: box-shadow 0.2s;
}
.stat-card:hover { box-shadow:0 3px 12px rgba(0,0,0,0.15); }
.stat-card .num { font-size:1.8rem; font-weight:800; line-height:1; }
.stat-card .lbl { font-size:0.75rem; color:#78909c; margin-top:4px; }
.stat-card.all   { border-top-color:#546e7a; } .stat-card.all .num   { color:#546e7a; }
.stat-card.wait  { border-top-color:#f9a825; } .stat-card.wait .num  { color:#f9a825; }
.stat-card.check { border-top-color:#1565c0; } .stat-card.check .num { color:#1565c0; }
.stat-card.ok    { border-top-color:#2e7d32; } .stat-card.ok .num    { color:#2e7d32; }
.stat-card.no    { border-top-color:#c62828; } .stat-card.no .num    { color:#c62828; }

.filter-bar {
    background:#fff; border-radius:8px;
    padding:14px 16px; margin-bottom:12px;
    box-shadow:0 1px 6px rgba(0,0,0,0.07);
    display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;
}
.filter-bar .filter-item { flex:1; min-width:160px; }
.filter-bar .filter-item label { font-size:0.78rem; color:#78909c; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; display:block; margin-bottom:4px; }
.filter-bar .filter-item input,
.filter-bar .filter-item select {
    width:100%; padding:8px 10px; border:1px solid #e0e0e0;
    border-radius:6px; font-size:0.9rem; color:#263238;
    background:#fafafa; outline:none;
}
.filter-bar .filter-item input:focus,
.filter-bar .filter-item select:focus { border-color:#546e7a; }
.filter-bar .filter-actions { display:flex; gap:8px; align-items:flex-end; }

/* Antrian cards */
.antrian-list { display:flex; flex-direction:column; gap:10px; }
.antrian-card {
    background:#fff; border-radius:8px;
    box-shadow:0 1px 6px rgba(0,0,0,0.08);
    overflow:hidden;
    border-left:4px solid #ccc;
}
.antrian-card.status-menunggu  { border-left-color:#f9a825; }
.antrian-card.status-diperiksa { border-left-color:#1565c0; }
.antrian-card.status-disetujui { border-left-color:#2e7d32; }
.antrian-card.status-ditolak   { border-left-color:#c62828; }

.antrian-card-top {
    display:flex; align-items:center; padding:12px 16px; gap:14px;
}
.antrian-no {
    font-size:2rem; font-weight:900; color:#37474f;
    min-width:56px; text-align:center; line-height:1;
}
.antrian-info { flex:1; }
.antrian-info .nama { font-size:1rem; font-weight:700; color:#263238; margin:0 0 2px; }
.antrian-info .sub  { font-size:0.82rem; color:#78909c; margin:0; }
.antrian-info .jenis { font-size:0.88rem; color:#546e7a; margin:3px 0 0; }
.antrian-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:700;
}
.badge-menunggu  { background:#fff8e1; color:#f57f17; }
.badge-diperiksa { background:#e3f2fd; color:#1565c0; }
.badge-disetujui { background:#e8f5e9; color:#2e7d32; }
.badge-ditolak   { background:#ffebee; color:#c62828; }

.antrian-card-bottom {
    padding:8px 16px 12px;
    border-top:1px solid #f5f5f5;
    display:flex; gap:6px; flex-wrap:wrap; align-items:center;
}
.antrian-card-bottom .file-links { flex:1; display:flex; gap:6px; flex-wrap:wrap; }
.antrian-card-bottom .file-link {
    font-size:0.78rem; color:#1565c0; text-decoration:none;
    display:inline-flex; align-items:center; gap:2px;
    background:#e3f2fd; padding:3px 8px; border-radius:4px;
}
.antrian-card-bottom .file-link i { font-size:13px; }
.catatan-text { font-size:0.8rem; color:#78909c; font-style:italic; padding:0 16px 10px; }

.empty-state {
    text-align:center; padding:48px 20px; color:#90a4ae;
}
.empty-state i { font-size:4rem; display:block; margin-bottom:12px; }
.empty-state p { font-size:1rem; margin:0; }
</style>

<!-- Secondary Nav -->
<div class="row" style="margin-bottom:0;">
    <div class="col s12">
        <nav class="secondary-nav">
            <div class="nav-wrapper blue-grey darken-1">
                <ul class="left">
                    <li class="waves-effect waves-light">
                        <a href="?page=antrian"><i class="material-icons">queue</i> Antrian Legalisir</a>
                    </li>
                    <li class="waves-effect waves-light">
                        <a href="antrian.php" target="_blank"><i class="material-icons">open_in_new</i> Halaman Publik</a>
                    </li>
                    <li class="waves-effect waves-light">
                        <a href="monitor.php" target="_blank"><i class="material-icons">tv</i> Monitor Antrian</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<?php if (isset($_SESSION['succAntrian'])): ?>
<div id="alert-message" class="row" style="margin:10px 0 0;">
    <div class="col s12">
        <div class="card green lighten-5" style="margin:0;">
            <div class="card-content notif">
                <span class="card-title green-text">
                    <i class="material-icons md-36">done</i> <?php echo $_SESSION['succAntrian']; unset($_SESSION['succAntrian']); ?>
                </span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="stat-cards">
    <a href="?page=antrian&tgl=<?php echo $filter_tgl; ?>" class="stat-card all">
        <div class="num"><?php echo $c_all; ?></div>
        <div class="lbl">Total</div>
    </a>
    <a href="?page=antrian&tgl=<?php echo $filter_tgl; ?>&status=menunggu" class="stat-card wait">
        <div class="num"><?php echo $c_menunggu; ?></div>
        <div class="lbl">Menunggu</div>
    </a>
    <a href="?page=antrian&tgl=<?php echo $filter_tgl; ?>&status=diperiksa" class="stat-card check">
        <div class="num"><?php echo $c_diperiksa; ?></div>
        <div class="lbl">Verifikasi</div>
    </a>
    <a href="?page=antrian&tgl=<?php echo $filter_tgl; ?>&status=disetujui" class="stat-card ok">
        <div class="num"><?php echo $c_disetujui; ?></div>
        <div class="lbl">Disetujui</div>
    </a>
    <a href="?page=antrian&tgl=<?php echo $filter_tgl; ?>&status=ditolak" class="stat-card no">
        <div class="num"><?php echo $c_ditolak; ?></div>
        <div class="lbl">Ditolak</div>
    </a>
</div>

<!-- Filter Bar -->
<form method="GET" action="admin.php">
    <input type="hidden" name="page" value="antrian">
    <div class="filter-bar">
        <div class="filter-item">
            <label>Tanggal</label>
            <input type="date" name="tgl" value="<?php echo $filter_tgl; ?>">
        </div>
        <div class="filter-item">
            <label>Status</label>
            <select name="status">
                <option value="">Semua Status</option>
                <?php foreach ($status_label as $k => $v): ?>
                <option value="<?php echo $k; ?>" <?php echo $filter_status==$k?'selected':''; ?>><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn blue-grey waves-effect waves-light">
                <i class="material-icons left">filter_list</i>Filter
            </button>
            <a href="?page=antrian" class="btn blue-grey lighten-2 waves-effect waves-light">Reset</a>
        </div>
    </div>
</form>

<!-- Daftar Antrian -->
<div class="antrian-list">
<?php if (mysqli_num_rows($q_list) > 0):
    while ($row = mysqli_fetch_array($q_list)): ?>

    <div class="antrian-card status-<?php echo $row['status']; ?>">
        <div class="antrian-card-top">
            <div class="antrian-no">#<?php echo $row['no_antrian']; ?></div>
            <div class="antrian-info">
                <p class="nama"><?php echo htmlspecialchars($row['nama']); ?></p>
                <p class="sub">
                    <i class="material-icons" style="font-size:13px;vertical-align:middle;">phone</i>
                    <?php echo htmlspecialchars($row['no_hp']); ?>
                    &nbsp;&bull;&nbsp;
                    <?php echo $row['jumlah_lembar']; ?> lembar
                    &nbsp;&bull;&nbsp;
                    <?php echo indoDate($row['tgl_daftar']); ?>
                </p>
                <p class="jenis"><?php echo htmlspecialchars($row['jenis_surat']); ?></p>
            </div>
            <div>
                <span class="antrian-badge badge-<?php echo $row['status']; ?>">
                    <i class="material-icons" style="font-size:13px;"><?php echo $status_icon[$row['status']]; ?></i>
                    <?php echo $status_label[$row['status']]; ?>
                </span>
            </div>
        </div>

        <?php if (!empty($row['catatan_pegawai'])): ?>
        <p class="catatan-text"><i class="material-icons" style="font-size:13px;vertical-align:middle;">comment</i> <?php echo htmlspecialchars($row['catatan_pegawai']); ?></p>
        <?php endif; ?>

        <div class="antrian-card-bottom">
            <div class="file-links">
                <?php if (!empty($row['file_surat'])):
                    foreach (explode(',', $row['file_surat']) as $fi => $f):
                        $f = trim($f); ?>
                    <a href="view_file.php?f=<?php echo urlencode($f); ?>" target="_blank" class="file-link">
                        <i class="material-icons">attach_file</i> File <?php echo $fi+1; ?>
                    </a>
                    <?php endforeach;
                endif; ?>
            </div>
            <!-- Tombol Aksi -->
            <a class="btn small blue waves-effect waves-light modal-trigger" href="#modal-<?php echo $row['id_antrian']; ?>">
                <i class="material-icons left">edit</i>Periksa
            </a>
            <a class="btn small yellow darken-3 waves-effect waves-light"
               href="cetak_antrian.php?id=<?php echo $row['id_antrian']; ?>" target="_blank">
                <i class="material-icons left">print</i>Cetak
            </a>
            <a class="btn small deep-orange waves-effect waves-light"
               href="?page=antrian&del=<?php echo $row['id_antrian']; ?>"
               onclick="return confirm('Hapus antrian #<?php echo $row['no_antrian']; ?> atas nama <?php echo htmlspecialchars($row['nama']); ?>?')">
                <i class="material-icons">delete</i>
            </a>
        </div>
    </div>

    <!-- Modal Periksa -->
    <div id="modal-<?php echo $row['id_antrian']; ?>" class="modal" style="border-radius:12px;max-width:520px;">
        <form method="POST" action="?page=antrian">
            <input type="hidden" name="id_antrian" value="<?php echo $row['id_antrian']; ?>">
            <div class="modal-content" style="padding:24px 24px 8px;">

                <!-- Header modal -->
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                    <div style="width:44px;height:44px;border-radius:10px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="material-icons" style="color:#1565c0;font-size:1.4rem;">assignment</i>
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:700;color:#263238;margin:0;">Periksa Antrian <span style="color:#1565c0;">#<?php echo $row['no_antrian']; ?></span></div>
                        <div style="font-size:0.78rem;color:#90a4ae;margin-top:2px;"><?php echo indoDate($row['tgl_daftar']); ?></div>
                    </div>
                    <span style="margin-left:auto;" class="antrian-badge badge-<?php echo $row['status']; ?>">
                        <i class="material-icons" style="font-size:12px;"><?php echo $status_icon[$row['status']]; ?></i>
                        <?php echo $status_label[$row['status']]; ?>
                    </span>
                </div>

                <!-- Info pemohon -->
                <div style="background:#f8faff;border-radius:10px;padding:14px 16px;margin-bottom:16px;border:1px solid #e8f0fe;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <div style="font-size:0.7rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Nama</div>
                            <div style="font-size:0.9rem;font-weight:700;color:#263238;"><?php echo htmlspecialchars($row['nama']); ?></div>
                        </div>
                        <div>
                            <div style="font-size:0.7rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">No. HP</div>
                            <div style="font-size:0.9rem;color:#263238;"><?php echo htmlspecialchars($row['no_hp']); ?></div>
                        </div>
                        <div>
                            <div style="font-size:0.7rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Jenis Surat</div>
                            <div style="font-size:0.88rem;color:#263238;"><?php echo htmlspecialchars($row['jenis_surat']); ?></div>
                        </div>
                        <div>
                            <div style="font-size:0.7rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Jumlah Lembar</div>
                            <div style="font-size:0.88rem;color:#263238;"><?php echo $row['jumlah_lembar']; ?> lembar</div>
                        </div>
                        <?php if (!empty($row['keterangan'])): ?>
                        <div style="grid-column:1/-1;">
                            <div style="font-size:0.7rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Keterangan</div>
                            <div style="font-size:0.85rem;color:#546e7a;"><?php echo htmlspecialchars($row['keterangan']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- File surat -->
                <?php if (!empty($row['file_surat'])): ?>
                <div style="margin-bottom:16px;">
                    <div style="font-size:0.75rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">File Dokumen</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    <?php foreach (explode(',', $row['file_surat']) as $fi => $f): $f = trim($f); ?>
                        <a href="view_file.php?f=<?php echo urlencode($f); ?>" target="_blank"
                           style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#e3f2fd;color:#1565c0;border-radius:6px;font-size:0.8rem;font-weight:600;text-decoration:none;">
                            <i class="material-icons" style="font-size:14px;">attach_file</i> File <?php echo $fi+1; ?>
                        </a>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Update status -->
                <div style="margin-bottom:4px;">
                    <div style="font-size:0.75rem;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Update Status</div>
                    <div class="input-field" style="margin:0;">
                        <select name="status" required>
                            <?php foreach ($status_label as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo $row['status']==$k?'selected':''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="input-field">
                    <textarea name="catatan_pegawai" class="materialize-textarea" style="min-height:70px;"><?php echo htmlspecialchars($row['catatan_pegawai']); ?></textarea>
                    <label class="active">Catatan Pegawai (opsional)</label>
                </div>
            </div>
            <div class="modal-footer" style="padding:12px 24px;border-top:1px solid #f0f0f0;display:flex;gap:8px;justify-content:flex-end;">
                <a href="#!" class="modal-action modal-close btn-flat" style="color:#78909c;">Batal</a>
                <button type="submit" name="update_status" class="btn blue waves-effect waves-light" style="border-radius:8px;">
                    <i class="material-icons left">save</i>Simpan
                </button>
            </div>
        </form>
    </div>

    <?php endwhile;
else: ?>
    <div class="empty-state">
        <i class="material-icons">inbox</i>
        <p>Tidak ada data antrian untuk tanggal ini.</p>
    </div>
<?php endif; ?>
</div>
<br>
