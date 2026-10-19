<?php
ob_start();
session_start();

if (empty($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ./");
    die();
}

require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$jenis     = isset($_GET['jenis']) ? $_GET['jenis'] : 'backup';
$nama_file = isset($_GET['nama_file']) ? basename($_GET['nama_file']) : '';
$tgl_aksi  = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d H:i:s');
$operator  = $_SESSION['nama'];

$q_inst = mysqli_query($config, "SELECT * FROM tbl_instansi");
$inst   = mysqli_fetch_array($q_inst);

$judul_jenis = ($jenis == 'backup') ? 'BACKUP DATABASE' : 'RESTORE DATABASE';
$tgl_format  = date('d/m/Y H:i:s', strtotime($tgl_aksi));
$ukuran_file = '';
if (!empty($nama_file) && file_exists("backup/$nama_file")) {
    $bytes = filesize("backup/$nama_file");
    $ukuran_file = number_format($bytes / 1024, 2) . ' KB';
}

$nama_excel = 'Laporan_' . ucfirst($jenis) . '_' . date('Ymd_His') . '.xls';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_excel\"");
header("Pragma: no-cache");
header("Expires: 0");
ob_end_clean();
?>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 11pt; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 6px 10px; }
    .header-title { font-size: 14pt; font-weight: bold; text-align: center; }
    .sub-title { font-size: 11pt; text-align: center; }
    .section-title { background: #37474f; color: #fff; font-weight: bold; text-align: center; }
    .label-col { background: #eceff1; font-weight: bold; width: 35%; }
    .green-row { background: #e8f5e9; }
    .footer-note { font-size: 9pt; color: #555; font-style: italic; }
    .ttd-area { text-align: center; padding: 20px; }
</style>
</head>
<body>

<!-- Kop Surat -->
<table style="border:none; margin-bottom:10px;">
    <tr>
        <td style="border:none; width:80px; text-align:center; vertical-align:middle;">
            <?php if (!empty($inst['logo'])): ?>
            <img src="upload/<?php echo $inst['logo']; ?>" width="70" height="70">
            <?php endif; ?>
        </td>
        <td style="border:none; vertical-align:middle; padding-left:10px;">
            <div class="header-title"><?php echo strtoupper($inst['nama']); ?></div>
            <div class="sub-title"><?php echo $inst['alamat']; ?></div>
            <div class="sub-title">Website: <?php echo $inst['website']; ?> | Email: <?php echo $inst['email']; ?></div>
        </td>
    </tr>
</table>

<table style="border:none; margin-bottom:6px;">
    <tr><td style="border:none; border-top:3px solid #000; border-bottom:1px solid #000;">&nbsp;</td></tr>
</table>

<!-- Judul Dokumen -->
<table style="border:none; margin-bottom:14px;">
    <tr>
        <td style="border:none; text-align:center; font-size:13pt; font-weight:bold; text-transform:uppercase;">
            BERITA ACARA <?php echo $judul_jenis; ?>
        </td>
    </tr>
    <tr>
        <td style="border:none; text-align:center; font-size:10pt; color:#555;">
            SILAPA - Sistem Informasi Legalisir Ijazah Papua
        </td>
    </tr>
</table>

<!-- Detail Aksi -->
<table>
    <tr>
        <td class="section-title" colspan="2">INFORMASI <?php echo $judul_jenis; ?></td>
    </tr>
    <tr class="green-row">
        <td class="label-col">Jenis Aksi</td>
        <td><?php echo $judul_jenis; ?></td>
    </tr>
    <tr>
        <td class="label-col">Tanggal &amp; Waktu</td>
        <td><?php echo $tgl_format; ?></td>
    </tr>
    <tr class="green-row">
        <td class="label-col">Nama File Database</td>
        <td><?php echo !empty($nama_file) ? $nama_file : '-'; ?></td>
    </tr>
    <tr>
        <td class="label-col">Ukuran File</td>
        <td><?php echo !empty($ukuran_file) ? $ukuran_file : '-'; ?></td>
    </tr>
    <tr class="green-row">
        <td class="label-col">Nama Database</td>
        <td><?php echo $database; ?></td>
    </tr>
    <tr>
        <td class="label-col">Operator / Pelaksana</td>
        <td><?php echo htmlspecialchars($operator); ?></td>
    </tr>
    <tr class="green-row">
        <td class="label-col">Status</td>
        <td><strong>BERHASIL &#10003;</strong></td>
    </tr>
</table>

<br>

<!-- Informasi Instansi -->
<table>
    <tr>
        <td class="section-title" colspan="2">INFORMASI INSTANSI</td>
    </tr>
    <tr>
        <td class="label-col">Nama Instansi</td>
        <td><?php echo $inst['nama']; ?></td>
    </tr>
    <tr class="green-row">
        <td class="label-col">Pemerintah Daerah</td>
        <td><?php echo $inst['institusi']; ?></td>
    </tr>
    <tr>
        <td class="label-col">Alamat</td>
        <td><?php echo $inst['alamat']; ?></td>
    </tr>
    <tr class="green-row">
        <td class="label-col">Kepala Badan</td>
        <td><?php echo $inst['kepsek']; ?></td>
    </tr>
    <tr>
        <td class="label-col">NIP Kepala Badan</td>
        <td><?php echo $inst['nip']; ?></td>
    </tr>
</table>

<br>

<!-- Tanda Tangan -->
<table style="border:none;">
    <tr>
        <td style="border:none; width:50%;"></td>
        <td style="border:none; width:50%; text-align:center;">
            <div>Jayapura, <?php echo date('d/m/Y'); ?></div>
            <div>Operator Sistem,</div>
            <br><br><br><br>
            <div><strong><u><?php echo htmlspecialchars($operator); ?></u></strong></div>
            <div style="font-size:9pt; color:#555;">NIP. <?php echo $_SESSION['nip']; ?></div>
        </td>
    </tr>
</table>

<br>
<table style="border:none;">
    <tr>
        <td style="border:none; font-size:9pt; color:#777; font-style:italic;">
            * Dokumen ini dicetak secara otomatis oleh sistem SILAPA pada <?php echo date('d/m/Y H:i:s'); ?>
        </td>
    </tr>
</table>

</body>
</html>
