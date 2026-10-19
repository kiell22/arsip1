<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);
?>
<!doctype html>
<html>
<head><title>Install - Sistem Antrian Legalisir</title>
<style>
body{font-family:Arial;padding:30px;background:#eceff1;}
.box{background:#fff;padding:25px;border-radius:6px;max-width:600px;margin:0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
h2{color:#37474f;margin-bottom:20px;}
.ok{color:#2e7d32;font-size:1.1rem;margin:8px 0;}
.err{color:#c62828;font-size:1.1rem;margin:8px 0;}
.btn{display:inline-block;padding:12px 24px;margin:5px;border-radius:4px;text-decoration:none;color:#fff;font-size:1rem;}
.btn-green{background:#37474f;}
.btn-blue{background:#1565c0;}
</style>
</head>
<body>
<div class="box">
<h2>&#9881; Instalasi Sistem Antrian Legalisir</h2>
<?php
// Cek koneksi
if (!is_object($config) && !is_resource($config)) {
    echo '<p class="err">&#10007; Koneksi database gagal! Cek config.php</p>';
    die();
}
echo '<p class="ok">&#10003; Koneksi database berhasil.</p>';

// Buat tabel
$sql = "CREATE TABLE IF NOT EXISTS `tbl_antrian` (
  `id_antrian` int(10) NOT NULL AUTO_INCREMENT,
  `no_antrian` int(5) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `jenis_surat` varchar(250) NOT NULL,
  `jumlah_lembar` int(5) NOT NULL DEFAULT 1,
  `keterangan` text,
  `file_surat` varchar(250) DEFAULT NULL,
  `status` enum('menunggu','diperiksa','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan_pegawai` text,
  `tgl_daftar` date NOT NULL,
  `tgl_proses` datetime DEFAULT NULL,
  `id_user` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id_antrian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if (mysqli_query($config, $sql)) {
    echo '<p class="ok">&#10003; Tabel <strong>tbl_antrian</strong> berhasil dibuat / sudah ada.</p>';
} else {
    echo '<p class="err">&#10007; Gagal buat tabel: ' . mysqli_error($config) . '</p>';
}

// Cek tabel berhasil
$cek = mysqli_query($config, "SHOW TABLES LIKE 'tbl_antrian'");
if (mysqli_num_rows($cek) > 0) {
    echo '<p class="ok">&#10003; Tabel <strong>tbl_antrian</strong> terverifikasi ada di database.</p>';
} else {
    echo '<p class="err">&#10007; Tabel tbl_antrian tidak ditemukan setelah dibuat.</p>';
}

// Buat folder
$dir = 'upload/legalisir/';
if (!is_dir($dir)) {
    if (mkdir($dir, 0755, true)) {
        echo '<p class="ok">&#10003; Folder <strong>upload/legalisir/</strong> berhasil dibuat.</p>';
    } else {
        echo '<p class="err">&#10007; Gagal membuat folder upload/legalisir/</p>';
    }
} else {
    echo '<p class="ok">&#10003; Folder <strong>upload/legalisir/</strong> sudah ada.</p>';
}

echo '<br><p style="color:#555;">Instalasi selesai. Silakan akses aplikasi:</p>';
?>
<a class="btn btn-green" href="index.php">&#128274; Login Pegawai</a>
<a class="btn btn-blue" href="antrian.php">&#128203; Halaman Antrian Publik</a>
</div>
</body>
</html>
