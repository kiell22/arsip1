<?php
ob_start();
session_start();
if (empty($_SESSION['admin'])) {
    header("Location: ./index.php");
    die();
}
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) { echo "ID tidak valid."; die(); }

$q = mysqli_query($config, "SELECT a.*, u.nama as nama_pegawai FROM tbl_antrian a LEFT JOIN tbl_user u ON a.id_user=u.id_user WHERE a.id_antrian='$id'");
if (mysqli_num_rows($q) == 0) { echo "Data tidak ditemukan."; die(); }
$data = mysqli_fetch_array($q);

$q_inst = mysqli_query($config, "SELECT * FROM tbl_instansi");
$inst   = mysqli_fetch_array($q_inst);
?>
<!doctype html>
<html lang="id">
<head>
    <title>Cetak Legalisir #<?php echo $data['no_antrian']; ?> - <?php echo $data['nama']; ?></title>
    <meta charset="utf-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #607d8b; font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; }

        /* Toolbar */
        .toolbar {
            background: #263238; padding: 10px 20px;
            display: flex; align-items: center; gap: 10px;
            position: sticky; top: 0; z-index: 100;
        }
        .toolbar button, .toolbar a {
            padding: 8px 20px; border-radius: 4px; border: none;
            font-size: 0.9rem; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-print { background: #43a047; color: #fff; }
        .btn-close { background: #e53935; color: #fff; }
        .toolbar span { color: #90a4ae; font-size: 0.85rem; margin-left: auto; }

        /* Paper */
        .paper-wrap { padding: 24px; display: flex; justify-content: center; }
        .paper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm 15mm;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }

        /* Kop Surat */
        .kop { display: flex; align-items: center; padding-bottom: 10px; border-bottom: 3px solid #000; margin-bottom: 6px; }
        .kop img { width: 70px; height: 70px; margin-right: 16px; flex-shrink: 0; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text .instansi { font-size: 11pt; font-weight: normal; }
        .kop-text .nama-instansi { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; }
        .kop-text .alamat { font-size: 9pt; color: #333; }
        .kop-garis2 { border-bottom: 1px solid #000; margin-bottom: 16px; }

        /* Judul */
        .judul-surat { text-align: center; margin: 16px 0 4px; }
        .judul-surat h2 { font-size: 13pt; text-transform: uppercase; letter-spacing: 2px; text-decoration: underline; }
        .no-antrian-cetak {
            text-align: center; margin: 8px 0 16px;
            font-size: 11pt;
        }
        .no-antrian-cetak .nomor {
            display: inline-block;
            border: 2px solid #000;
            padding: 4px 24px;
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 4px;
        }

        /* Tabel Detail */
        .detail-table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 11pt; }
        .detail-table tr td { padding: 5px 8px; vertical-align: top; }
        .detail-table tr td:first-child { width: 38%; font-weight: normal; }
        .detail-table tr td:nth-child(2) { width: 4%; text-align: center; }
        .detail-table tr td:last-child { width: 58%; }
        .detail-table tr:nth-child(odd) td { background: #f9f9f9; }

        /* Status */
        .status-disetujui { color: #1b5e20; font-weight: bold; }
        .status-ditolak   { color: #b71c1c; font-weight: bold; }
        .status-menunggu  { color: #e65100; font-weight: bold; }
        .status-diperiksa { color: #0d47a1; font-weight: bold; }

        /* TTD */
        .ttd-section { display: flex; justify-content: space-between; margin-top: 36px; }
        .ttd-box { width: 44%; text-align: center; font-size: 11pt; }
        .ttd-box .ttd-space { height: 70px; }
        .ttd-box .ttd-nama { font-weight: bold; border-top: 1px solid #000; padding-top: 4px; }
        .ttd-box .ttd-nip { font-size: 10pt; }

        /* Footer cetak */
        .cetak-footer { margin-top: 24px; border-top: 1px dashed #999; padding-top: 8px; font-size: 9pt; color: #666; text-align: center; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .paper-wrap { padding: 0; }
            .paper { box-shadow: none; width: 100%; min-height: auto; padding: 10mm 15mm; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button class="btn-print" onclick="window.print()">&#128438; Cetak / Print</button>
    <a class="btn-close" href="javascript:window.close()">&#10005; Tutup</a>
    <span>Pratinjau Cetak &mdash; Antrian #<?php echo $data['no_antrian']; ?> &mdash; <?php echo htmlspecialchars($data['nama']); ?></span>
</div>

<div class="paper-wrap">
<div class="paper">

    <!-- Kop Surat -->
    <div class="kop">
        <?php if (!empty($inst['logo'])): ?>
        <img src="upload/<?php echo $inst['logo']; ?>" alt="logo">
        <?php endif; ?>
        <div class="kop-text">
            <div class="instansi"><?php echo htmlspecialchars($inst['institusi']); ?></div>
            <div class="nama-instansi"><?php echo htmlspecialchars($inst['nama']); ?></div>
            <div class="alamat">
                <?php echo htmlspecialchars($inst['alamat']); ?> &nbsp;&bull;&nbsp;
                <?php echo $inst['website']; ?> &nbsp;&bull;&nbsp;
                <?php echo $inst['email']; ?>
            </div>
        </div>
    </div>
    <div class="kop-garis2"></div>

    <!-- Judul -->
    <div class="judul-surat">
        <h2>Bukti Legalisir Ijazah</h2>
    </div>
    <div class="no-antrian-cetak">
        Nomor Antrian &nbsp;
        <span class="nomor"><?php echo str_pad($data['no_antrian'], 3, '0', STR_PAD_LEFT); ?></span>
    </div>

    <!-- Detail -->
    <table class="detail-table">
        <tr><td>Nama Pemohon</td><td>:</td><td><?php echo htmlspecialchars($data['nama']); ?></td></tr>
        <tr><td>No. HP / WhatsApp</td><td>:</td><td><?php echo htmlspecialchars($data['no_hp']); ?></td></tr>
        <tr><td>Jenis Surat</td><td>:</td><td><?php echo htmlspecialchars($data['jenis_surat']); ?></td></tr>
        <tr><td>Jumlah Lembar</td><td>:</td><td><?php echo $data['jumlah_lembar']; ?> (<?php echo terbilang($data['jumlah_lembar']); ?>) lembar</td></tr>
        <?php if (!empty($data['keterangan'])): ?>
        <tr><td>Keterangan</td><td>:</td><td><?php echo htmlspecialchars($data['keterangan']); ?></td></tr>
        <?php endif; ?>
        <tr><td>Tanggal Daftar</td><td>:</td><td><?php echo indoDate($data['tgl_daftar']); ?></td></tr>
        <tr><td>Tanggal Diproses</td><td>:</td><td><?php echo !empty($data['tgl_proses']) ? date('d/m/Y H:i', strtotime($data['tgl_proses'])) : '-'; ?></td></tr>
        <tr>
            <td>Status</td><td>:</td>
            <td><span class="status-<?php echo $data['status']; ?>"><?php
                $sl = ['menunggu'=>'Menunggu','diperiksa'=>'Sedang Diverifikasi','disetujui'=>'DISETUJUI','ditolak'=>'DITOLAK'];
                echo $sl[$data['status']];
            ?></span></td>
        </tr>
        <?php if (!empty($data['catatan_pegawai'])): ?>
        <tr><td>Catatan Pegawai</td><td>:</td><td><?php echo htmlspecialchars($data['catatan_pegawai']); ?></td></tr>
        <?php endif; ?>
        <tr><td>Diperiksa Oleh</td><td>:</td><td><?php echo htmlspecialchars($data['nama_pegawai'] ?? '-'); ?></td></tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd-section">
        <div class="ttd-box">
            <div>Pemohon,</div>
            <div class="ttd-space"></div>
            <div class="ttd-nama"><?php echo htmlspecialchars($data['nama']); ?></div>
        </div>
        <div class="ttd-box">
            <div>Papua, <?php echo indoDate(date('Y-m-d')); ?></div>
            <div style="margin-top:4px; font-size:10pt;">a.n. Kepala <?php echo htmlspecialchars($inst['nama']); ?></div>
            <div class="ttd-space"></div>
            <div class="ttd-nama"><?php echo htmlspecialchars($inst['kepsek']); ?></div>
            <div class="ttd-nip">NIP. <?php echo htmlspecialchars($inst['nip']); ?></div>
        </div>
    </div>

    <div class="cetak-footer">
        Dokumen ini dicetak secara otomatis oleh SILAPA - Sistem Informasi Legalisir Ijazah Papua &mdash; <?php echo htmlspecialchars($inst['nama']); ?>
        &mdash; <?php echo date('d/m/Y H:i'); ?>
    </div>

</div>
</div>

<?php
function terbilang($n) {
    $kata = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh',
             'sebelas','dua belas','tiga belas','empat belas','lima belas','enam belas','tujuh belas',
             'delapan belas','sembilan belas','dua puluh'];
    if ($n <= 20) return $kata[$n] ?? $n;
    return $n;
}
?>
</body>
</html>
