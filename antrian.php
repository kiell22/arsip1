<?php
ob_start();
session_start();
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$today        = date('Y-m-d');
$pesan_sukses = '';
$no_antrian_baru = '';
$hasil_cek    = null;
$error        = '';

if (isset($_POST['daftar'])) {
    $nama        = trim(mysqli_real_escape_string($config, $_POST['nama']));
    $no_hp       = trim(mysqli_real_escape_string($config, $_POST['no_hp']));
    $jenis_surat = trim(mysqli_real_escape_string($config, $_POST['jenis_surat']));
    $jumlah      = (int)$_POST['jumlah_lembar'];
    $keterangan  = trim(mysqli_real_escape_string($config, $_POST['keterangan']));

    if (empty($nama) || empty($no_hp) || empty($jenis_surat) || $jumlah < 1) {
        $error = 'Semua field wajib diisi dengan benar.';
    } else {
        $q_no      = mysqli_query($config, "SELECT COUNT(*) as total FROM tbl_antrian WHERE tgl_daftar='$today'");
        $r_no      = mysqli_fetch_array($q_no);
        $no_antrian = $r_no['total'] + 1;

        $file_surat = '';
        $target_dir = 'upload/legalisir/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        if (!empty($_FILES['file_surat']['name'][0])) {
            $uploaded    = [];
            $total_files = count($_FILES['file_surat']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['file_surat']['error'][$i] === UPLOAD_ERR_OK) {
                    $nama_file = time() . $i . '-' . basename($_FILES['file_surat']['name'][$i]);
                    if (move_uploaded_file($_FILES['file_surat']['tmp_name'][$i], $target_dir . $nama_file)) {
                        $uploaded[] = $nama_file;
                    }
                }
            }
            $file_surat = implode(',', $uploaded);
        }

        $file_surat_esc = mysqli_real_escape_string($config, $file_surat);
        $q = mysqli_query($config, "INSERT INTO tbl_antrian (no_antrian, nama, no_hp, jenis_surat, jumlah_lembar, keterangan, file_surat, status, tgl_daftar)
            VALUES ('$no_antrian','$nama','$no_hp','$jenis_surat','$jumlah','$keterangan','$file_surat_esc','menunggu','$today')");

        if ($q) {
            $no_antrian_baru = $no_antrian;
            $pesan_sukses    = true;
        } else {
            $error = 'Gagal menyimpan data. Silakan coba lagi.';
        }
    }
}

if (isset($_POST['cek'])) {
    $no_cek   = (int)$_POST['no_cek'];
    $nama_cek = trim(mysqli_real_escape_string($config, $_POST['nama_cek']));
    $q_cek    = mysqli_query($config, "SELECT * FROM tbl_antrian WHERE no_antrian='$no_cek' AND nama='$nama_cek' AND tgl_daftar='$today'");
    if (mysqli_num_rows($q_cek) > 0) {
        $hasil_cek = mysqli_fetch_array($q_cek);
        // Hitung posisi antrian (berapa orang menunggu sebelum dia)
        $posisi_cek = null;
        if ($hasil_cek['status'] === 'menunggu') {
            $q_pos = mysqli_query($config, "SELECT COUNT(*) as pos FROM tbl_antrian WHERE tgl_daftar='$today' AND status='menunggu' AND no_antrian < '$no_cek'");
            $posisi_cek = mysqli_fetch_array($q_pos)['pos'] + 1;
        }
    } else {
        $error = 'Data tidak ditemukan. Pastikan nomor antrian dan nama sesuai (berlaku hari ini).';
    }
}

$q_inst = mysqli_query($config, "SELECT * FROM tbl_instansi");
$inst   = mysqli_fetch_array($q_inst);

// Hitung antrian hari ini
$q_total = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian WHERE tgl_daftar='$today'");
$total_hari_ini = mysqli_fetch_array($q_total)['t'];
?>
<!doctype html>
<html lang="id">
<head>
    <title>SILAPA - Antrian Legalisir Ijazah</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <link rel="shortcut icon" href="upload/<?php echo $inst['logo']; ?>">
    <link rel="stylesheet" href="asset/css/materialize.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eceff1; font-family: 'Roboto', Arial, sans-serif; }

        /* HEADER */
        .site-header {
            background: linear-gradient(135deg, #263238, #37474f);
            color: #fff;
            padding: 24px 16px 20px;
            text-align: center;
        }
        .site-header img {
            width: 72px; height: 72px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.25);
            display: block; margin: 0 auto 12px;
        }
        .site-header h1 { margin: 0; font-size: 1.1rem; font-weight: 700; line-height: 1.4; }
        .site-header p  { margin: 4px 0 0; font-size: 0.85rem; opacity: 0.75; }
        .header-info {
            display: flex; justify-content: center; gap: 24px;
            margin-top: 16px; padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }
        .header-info .info-item { text-align: center; }
        .header-info .info-item .num { font-size: 1.6rem; font-weight: 700; }
        .header-info .info-item .lbl { font-size: 0.75rem; opacity: 0.7; }

        /* TABS */
        .tab-nav {
            display: flex;
            background: #fff;
            border-bottom: 2px solid #e0e0e0;
            position: sticky; top: 0; z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .tab-nav button {
            flex: 1; padding: 14px 8px;
            border: none; background: none;
            font-size: 0.9rem; font-weight: 600;
            color: #90a4ae; cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .tab-nav button.active { color: #37474f; border-bottom-color: #37474f; }
        .tab-nav button i { font-size: 18px; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* CARD */
        .content-card {
            background: #fff;
            margin: 16px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .card-header h5 { margin: 0; font-size: 1rem; font-weight: 700; color: #263238; }
        .card-header p  { margin: 4px 0 0; font-size: 0.85rem; color: #90a4ae; }
        .card-body { padding: 20px; }

        /* SUCCESS BOX */
        .success-box {
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
            border: 1px solid #a5d6a7;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-box .check-icon {
            width: 56px; height: 56px;
            background: #43a047; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }
        .success-box .check-icon i { color: #fff; font-size: 32px; }
        .success-box h5 { margin: 0 0 8px; color: #2e7d32; font-size: 1rem; }
        .no-antrian-display {
            font-size: 4rem; font-weight: 900;
            color: #37474f; line-height: 1;
            margin: 12px 0;
        }
        .success-box p { margin: 0; font-size: 0.85rem; color: #555; }

        /* FORM */
        .form-group { margin-bottom: 0; }
        .input-field { margin-bottom: 4px !important; }
        .input-field label { font-size: 0.95rem !important; }

        /* UPLOAD */
        .upload-zone {
            border: 2px dashed #b0bec5;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #fafafa;
            margin-bottom: 8px;
        }
        .upload-zone:hover { border-color: #546e7a; background: #f0f4f8; }
        .upload-zone i { font-size: 2rem; color: #90a4ae; display: block; margin-bottom: 6px; }
        .upload-zone p { margin: 0; font-size: 0.85rem; color: #78909c; }
        .upload-zone input[type=file] { display: none; }
        .file-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .file-chip {
            background: #e3f2fd; color: #1565c0;
            border-radius: 20px; padding: 4px 12px;
            font-size: 0.8rem; display: flex; align-items: center; gap: 4px;
        }
        .file-chip i { font-size: 14px; }

        /* BUTTON */
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #37474f, #546e7a);
            color: #fff; border: none; border-radius: 8px;
            font-size: 1rem; font-weight: 600;
            cursor: pointer; margin-top: 8px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity 0.2s;
        }
        .btn-submit:hover { opacity: 0.9; }

        /* ERROR */
        .error-box {
            background: #ffebee; border-left: 4px solid #e53935;
            border-radius: 4px; padding: 12px 14px;
            margin-bottom: 16px; font-size: 0.9rem; color: #c62828;
            display: flex; align-items: center; gap: 8px;
        }

        /* STATUS RESULT */
        .result-card {
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 16px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .result-card .result-header {
            background: #37474f; color: #fff;
            padding: 12px 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .result-card .result-header i { font-size: 20px; }
        .result-card .result-header span { font-weight: 600; }
        .result-body { padding: 0; }
        .result-row {
            display: flex; padding: 10px 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        .result-row:last-child { border-bottom: none; }
        .result-row .lbl { width: 42%; color: #78909c; font-weight: 500; flex-shrink: 0; }
        .result-row .val { color: #263238; }
        .no-antrian-sm { font-size: 1.8rem; font-weight: 900; color: #37474f; }

        /* STATUS BADGE */
        .badge {
            display: inline-block; padding: 4px 14px;
            border-radius: 20px; font-size: 0.82rem; font-weight: 700;
        }
        .badge-menunggu  { background: #fff8e1; color: #f57f17; }
        .badge-diperiksa { background: #e3f2fd; color: #1565c0; }
        .badge-disetujui { background: #e8f5e9; color: #2e7d32; }
        .badge-ditolak   { background: #ffebee; color: #c62828; }

        /* FOOTER */
        .site-footer {
            text-align: center; padding: 20px 16px;
            font-size: 0.8rem; color: #90a4ae;
        }

        /* LOGIN LINK */
        .login-link {
            display: block; text-align: center;
            padding: 10px; font-size: 0.82rem; color: #90a4ae;
        }
        .login-link a { color: #546e7a; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>

<!-- Header -->
<div class="site-header">
    <?php if (!empty($inst['logo'])): ?>
    <img src="upload/<?php echo $inst['logo']; ?>" alt="logo">
    <?php endif; ?>
    <h1><?php echo htmlspecialchars($inst['nama']); ?></h1>
    <p>SILAPA - Sistem Informasi Legalisir Ijazah Papua</p>
    <div class="header-info">
        <div class="info-item">
            <div class="num"><?php echo $total_hari_ini; ?></div>
            <div class="lbl">Antrian Hari Ini</div>
        </div>
        <div class="info-item">
            <div class="num"><?php echo date('d'); ?></div>
            <div class="lbl"><?php
                $bln = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                echo $bln[(int)date('m')].' '.date('Y');
            ?></div>
        </div>
    </div>
</div>

<!-- Tab Nav -->
<div class="tab-nav">
    <button class="active" onclick="showTab('daftar',this)" id="btn-daftar">
        <i class="material-icons">queue</i> Daftar Antrian
    </button>
    <button onclick="showTab('cek',this)" id="btn-cek">
        <i class="material-icons">search</i> Cek Status
    </button>
</div>

<!-- Tab Daftar -->
<div id="tab-daftar" class="tab-content active">
    <div class="content-card">
        <div class="card-header">
            <h5>Form Pendaftaran Legalisir Surat</h5>
            <p>Isi data dengan benar untuk mendapatkan nomor antrian</p>
        </div>
        <div class="card-body">
            <?php if ($pesan_sukses): ?>
            <div class="success-box">
                <div class="check-icon"><i class="material-icons">check</i></div>
                <h5>Pendaftaran Berhasil!</h5>
                <div class="no-antrian-display">#<?php echo $no_antrian_baru; ?></div>
                <p>Nomor antrian Anda hari ini.<br>Simpan nomor ini untuk cek status.</p>
            </div>
            <?php endif; ?>

            <?php if ($error && isset($_POST['daftar'])): ?>
            <div class="error-box">
                <i class="material-icons" style="font-size:18px;">error_outline</i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="input-field col s12 m6">
                        <i class="material-icons prefix">person</i>
                        <input type="text" id="nama" name="nama" required
                            value="<?php echo isset($_POST['nama']) && !$pesan_sukses ? htmlspecialchars($_POST['nama']) : ''; ?>">
                        <label for="nama">Nama Lengkap</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <i class="material-icons prefix">phone</i>
                        <input type="tel" id="no_hp" name="no_hp" required
                            value="<?php echo isset($_POST['no_hp']) && !$pesan_sukses ? htmlspecialchars($_POST['no_hp']) : ''; ?>">
                        <label for="no_hp">Nomor HP / WhatsApp</label>
                    </div>
                    <div class="input-field col s12 m8">
                        <i class="material-icons prefix">description</i>
                        <input type="text" id="jenis_surat" name="jenis_surat" required
                            value="<?php echo isset($_POST['jenis_surat']) && !$pesan_sukses ? htmlspecialchars($_POST['jenis_surat']) : ''; ?>">
                        <label for="jenis_surat">Jenis Surat yang Dilegalisir</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <i class="material-icons prefix">layers</i>
                        <input type="number" id="jumlah_lembar" name="jumlah_lembar" min="1" required
                            value="<?php echo isset($_POST['jumlah_lembar']) && !$pesan_sukses ? (int)$_POST['jumlah_lembar'] : 1; ?>">
                        <label for="jumlah_lembar">Jumlah Lembar</label>
                    </div>
                    <div class="input-field col s12">
                        <i class="material-icons prefix">notes</i>
                        <textarea id="keterangan" name="keterangan" class="materialize-textarea"><?php echo isset($_POST['keterangan']) && !$pesan_sukses ? htmlspecialchars($_POST['keterangan']) : ''; ?></textarea>
                        <label for="keterangan">Keterangan (opsional)</label>
                    </div>
                    <div class="col s12" style="margin-bottom:16px;">
                        <p style="font-size:0.82rem; color:#78909c; margin:0 0 8px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Upload File Surat (opsional)</p>
                        <div class="upload-zone" onclick="document.getElementById('file_surat').click()">
                            <i class="material-icons">cloud_upload</i>
                            <p>Ketuk untuk memilih file<br><small>Semua format diterima, bisa lebih dari satu file</small></p>
                            <input type="file" id="file_surat" name="file_surat[]" multiple onchange="tampilkanFile(this)">
                        </div>
                        <div class="file-chips" id="file-list"></div>
                    </div>
                </div>
                <button type="submit" name="daftar" class="btn-submit">
                    <i class="material-icons">queue</i> Ambil Nomor Antrian
                </button>
            </form>
        </div>
    </div>
    <p class="login-link"><a href="index.php">Login sebagai Pegawai</a></p>
</div>

<!-- Tab Cek Status -->
<div id="tab-cek" class="tab-content">
    <div class="content-card">
        <div class="card-header">
            <h5>Cek Status Antrian</h5>
            <p>Masukkan nomor antrian dan nama lengkap (berlaku hari ini)</p>
        </div>
        <div class="card-body">
            <?php if ($error && isset($_POST['cek'])): ?>
            <div class="error-box">
                <i class="material-icons" style="font-size:18px;">error_outline</i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="input-field col s12 m4">
                        <i class="material-icons prefix">confirmation_number</i>
                        <input type="number" id="no_cek" name="no_cek" min="1" required
                            value="<?php echo isset($_POST['no_cek']) ? (int)$_POST['no_cek'] : ''; ?>">
                        <label for="no_cek">Nomor Antrian</label>
                    </div>
                    <div class="input-field col s12 m8">
                        <i class="material-icons prefix">person</i>
                        <input type="text" id="nama_cek" name="nama_cek" required
                            value="<?php echo isset($_POST['nama_cek']) ? htmlspecialchars($_POST['nama_cek']) : ''; ?>">
                        <label for="nama_cek">Nama Lengkap</label>
                    </div>
                </div>
                <button type="submit" name="cek" class="btn-submit">
                    <i class="material-icons">search</i> Cek Status
                </button>
            </form>

            <?php if ($hasil_cek):
                $sl = ['menunggu'=>'Menunggu','diperiksa'=>'Sedang Diverifikasi','disetujui'=>'Disetujui','ditolak'=>'Ditolak'];
            ?>
            <div class="result-card">
                <div class="result-header">
                    <i class="material-icons">assignment</i>
                    <span>Hasil Pengecekan Antrian</span>
                </div>
                <div class="result-body">

                    <?php if ($hasil_cek['status'] === 'menunggu' && $posisi_cek !== null): ?>
                    <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:20px 16px;text-align:center;">
                        <div style="font-size:.72rem;color:rgba(255,255,255,.7);font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px;">Posisi Antrian Anda</div>
                        <div style="font-size:3.5rem;font-weight:900;color:#fff;line-height:1;"><?php echo $posisi_cek; ?></div>
                        <div style="font-size:.82rem;color:rgba(255,255,255,.75);margin-top:6px;">
                            <?php if($posisi_cek == 1): ?>
                                🎉 Anda adalah antrian berikutnya!
                            <?php else: ?>
                                <?php echo ($posisi_cek - 1); ?> orang di depan Anda
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php elseif($hasil_cek['status'] === 'diperiksa'): ?>
                    <div style="background:linear-gradient(135deg,#1565c0,#1976d2);padding:16px;text-align:center;">
                        <div style="font-size:.85rem;color:#fff;font-weight:600;">🔍 Dokumen Anda Sedang Diverifikasi</div>
                    </div>
                    <?php elseif($hasil_cek['status'] === 'disetujui'): ?>
                    <div style="background:linear-gradient(135deg,#2e7d32,#43a047);padding:16px;text-align:center;">
                        <div style="font-size:.85rem;color:#fff;font-weight:600;">✅ Dokumen Anda Telah Disetujui — Silakan Ambil</div>
                    </div>
                    <?php elseif($hasil_cek['status'] === 'ditolak'): ?>
                    <div style="background:linear-gradient(135deg,#c62828,#e53935);padding:16px;text-align:center;">
                        <div style="font-size:.85rem;color:#fff;font-weight:600;">❌ Pengajuan Ditolak — Lihat Catatan di Bawah</div>
                    </div>
                    <?php endif; ?>
                    <div class="result-row">
                        <span class="lbl">Nomor Antrian</span>
                        <span class="val"><span class="no-antrian-sm">#<?php echo $hasil_cek['no_antrian']; ?></span></span>
                    </div>
                    <div class="result-row">
                        <span class="lbl">Nama</span>
                        <span class="val"><?php echo htmlspecialchars($hasil_cek['nama']); ?></span>
                    </div>
                    <div class="result-row">
                        <span class="lbl">No. HP</span>
                        <span class="val"><?php echo htmlspecialchars($hasil_cek['no_hp']); ?></span>
                    </div>
                    <div class="result-row">
                        <span class="lbl">Jenis Surat</span>
                        <span class="val"><?php echo htmlspecialchars($hasil_cek['jenis_surat']); ?></span>
                    </div>
                    <div class="result-row">
                        <span class="lbl">Jumlah Lembar</span>
                        <span class="val"><?php echo $hasil_cek['jumlah_lembar']; ?> lembar</span>
                    </div>
                    <div class="result-row">
                        <span class="lbl">Tanggal Daftar</span>
                        <span class="val"><?php echo indoDate($hasil_cek['tgl_daftar']); ?></span>
                    </div>
                    <div class="result-row">
                        <span class="lbl">Status</span>
                        <span class="val">
                            <span class="badge badge-<?php echo $hasil_cek['status']; ?>">
                                <?php echo $sl[$hasil_cek['status']]; ?>
                            </span>
                        </span>
                    </div>
                    <?php if (!empty($hasil_cek['catatan_pegawai'])): ?>
                    <div class="result-row">
                        <span class="lbl">Catatan</span>
                        <span class="val"><?php echo htmlspecialchars($hasil_cek['catatan_pegawai']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="site-footer">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($inst['nama']); ?></div>

<script src="asset/js/jquery-2.1.1.min.js"></script>
<script src="asset/js/materialize.min.js"></script>
<script>
$(document).ready(function(){ $('select').material_select(); });

function showTab(tab, btn) {
    document.querySelectorAll('.tab-content').forEach(function(el){ el.classList.remove('active'); });
    document.querySelectorAll('.tab-nav button').forEach(function(el){ el.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

function tampilkanFile(input) {
    var list = document.getElementById('file-list');
    list.innerHTML = '';
    for (var i = 0; i < input.files.length; i++) {
        var chip = document.createElement('div');
        chip.className = 'file-chip';
        chip.innerHTML = '<i class="material-icons">insert_drive_file</i>' + input.files[i].name;
        list.appendChild(chip);
    }
}

<?php if (isset($_POST['cek'])): ?>
showTab('cek', document.getElementById('btn-cek'));
<?php endif; ?>
</script>
</body>
</html>
