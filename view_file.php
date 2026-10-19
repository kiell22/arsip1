<?php
session_start();
if (empty($_SESSION['admin'])) {
    header("Location: ./index.php");
    die();
}
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$f    = basename($_GET['f'] ?? '');
$mode = $_GET['mode'] ?? 'view';

if (empty($f)) { echo "File tidak ditemukan."; die(); }

$path = 'upload/legalisir/' . $f;
if (!file_exists($path)) { echo "File tidak ditemukan di server."; die(); }

// Mode raw: serve file langsung
if ($mode === 'raw') {
    $mime = mime_content_type($path);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $f . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    die();
}

// Mode download
if ($mode === 'download') {
    $mime = mime_content_type($path);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $f . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    die();
}

$ext       = strtolower(pathinfo($f, PATHINFO_EXTENSION));
$size      = round(filesize($path) / 1024, 1);
$raw_url   = 'view_file.php?f=' . urlencode($f) . '&mode=raw';
$dl_url    = 'view_file.php?f=' . urlencode($f) . '&mode=download';
$pdf_exts  = ['pdf'];
$img_exts  = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
$word_exts = ['doc', 'docx'];
$xl_exts   = ['xls', 'xlsx'];

// Fungsi baca isi docx (ZIP XML)
function readDocx($path) {
    if (!class_exists('ZipArchive')) return null;
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) return null;
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    if (!$xml) return null;
    // Hapus tag XML, ambil teks
    $xml   = str_replace('</w:p>', "\n", $xml);
    $xml   = str_replace('</w:tr>', "\n", $xml);
    $xml   = str_replace('<w:tab/>', "\t", $xml);
    $teks  = strip_tags($xml);
    $teks  = preg_replace('/\n{3,}/', "\n\n", $teks);
    return trim($teks);
}

// Fungsi baca isi xlsx
function readXlsx($path) {
    if (!class_exists('ZipArchive')) return null;
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) return null;
    $xml = $zip->getFromName('xl/sharedStrings.xml');
    $zip->close();
    if (!$xml) return null;
    preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $xml, $matches);
    return implode(' | ', array_map('strip_tags', $matches[1]));
}
?>
<!doctype html>
<html lang="id">
<head>
    <title>Lihat File - <?php echo htmlspecialchars($f); ?></title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #455a64; font-family: Arial, sans-serif; }
        .topbar {
            background: #263238; color: #fff; padding: 10px 20px;
            display: flex; align-items: center; justify-content: space-between;
            height: 50px;
        }
        .topbar .filename { font-size: 1rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 60%; }
        .topbar .actions a {
            display: inline-block; padding: 6px 14px; border-radius: 4px;
            text-decoration: none; font-size: 0.85rem; margin-left: 6px;
        }
        .btn-dl  { background: #1565c0; color: #fff; }
        .btn-cls { background: #c62828; color: #fff; }
        .viewer  { width: 100%; height: calc(100vh - 50px); border: none; display: block; }
        .img-viewer {
            height: calc(100vh - 50px); overflow: auto;
            text-align: center; padding: 20px; background: #455a64;
        }
        .img-viewer img { max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
        .doc-viewer {
            height: calc(100vh - 50px); overflow-y: auto;
            background: #fff; padding: 0;
        }
        .doc-content {
            max-width: 800px; margin: 0 auto; padding: 40px 50px;
            background: #fff; min-height: 100%;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            font-size: 13px; line-height: 1.8; color: #212121;
            white-space: pre-wrap; word-wrap: break-word;
        }
        .doc-header {
            background: #37474f; color: #fff; padding: 12px 20px;
            font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center;
        }
        .center-box {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; height: calc(100vh - 50px);
            color: #fff; text-align: center; padding: 20px;
        }
        .center-box .icon { font-size: 5rem; margin-bottom: 15px; }
        .center-box p { font-size: 1.1rem; margin-bottom: 8px; color: #cfd8dc; }
        .center-box .fileinfo { font-size: 0.9rem; color: #90a4ae; margin-bottom: 25px; }
        .btn-download {
            display: inline-block; padding: 14px 32px; background: #1565c0;
            color: #fff; border-radius: 6px; text-decoration: none; font-size: 1.1rem;
        }
    </style>
</head>
<body>

<div class="topbar">
    <span class="filename">&#128196; <?php echo htmlspecialchars($f); ?> <small style="color:#90a4ae;">(<?php echo $size; ?> KB)</small></span>
    <div class="actions">
        <a class="btn-dl" href="<?php echo $dl_url; ?>">&#11015; Download</a>
        <a class="btn-cls" href="javascript:window.close()">&#10005; Tutup</a>
    </div>
</div>

<?php if (in_array($ext, $pdf_exts)): ?>
    <iframe class="viewer" src="<?php echo $raw_url; ?>"></iframe>

<?php elseif (in_array($ext, $img_exts)): ?>
    <div class="img-viewer">
        <img src="<?php echo $raw_url; ?>" alt="<?php echo htmlspecialchars($f); ?>">
    </div>

<?php elseif (in_array($ext, $word_exts)): ?>
    <?php $teks = readDocx($path); ?>
    <?php if ($teks): ?>
    <div class="doc-viewer">
        <div class="doc-header">
            <span>&#128196; Pratinjau isi dokumen Word (teks saja)</span>
            <a href="<?php echo $dl_url; ?>" style="color:#90caf9; font-size:0.85rem;">&#11015; Download file asli</a>
        </div>
        <div class="doc-content"><?php echo htmlspecialchars($teks); ?></div>
    </div>
    <?php else: ?>
    <div class="center-box">
        <div class="icon">&#128196;</div>
        <p>Dokumen Word (.<?php echo $ext; ?>)</p>
        <p class="fileinfo"><?php echo htmlspecialchars($f); ?> &nbsp;|&nbsp; <?php echo $size; ?> KB</p>
        <a class="btn-download" href="<?php echo $dl_url; ?>">&#11015; Download File</a>
    </div>
    <?php endif; ?>

<?php elseif (in_array($ext, $xl_exts)): ?>
    <?php $teks = readXlsx($path); ?>
    <?php if ($teks): ?>
    <div class="doc-viewer">
        <div class="doc-header">
            <span>&#128200; Pratinjau isi spreadsheet Excel (teks saja)</span>
            <a href="<?php echo $dl_url; ?>" style="color:#90caf9; font-size:0.85rem;">&#11015; Download file asli</a>
        </div>
        <div class="doc-content"><?php echo htmlspecialchars($teks); ?></div>
    </div>
    <?php else: ?>
    <div class="center-box">
        <div class="icon">&#128200;</div>
        <p>Spreadsheet Excel (.<?php echo $ext; ?>)</p>
        <p class="fileinfo"><?php echo htmlspecialchars($f); ?> &nbsp;|&nbsp; <?php echo $size; ?> KB</p>
        <a class="btn-download" href="<?php echo $dl_url; ?>">&#11015; Download File</a>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="center-box">
        <div class="icon">&#128190;</div>
        <p>File (.<?php echo $ext; ?>) tidak dapat dipratinjau.</p>
        <p class="fileinfo"><?php echo htmlspecialchars($f); ?> &nbsp;|&nbsp; <?php echo $size; ?> KB</p>
        <a class="btn-download" href="<?php echo $dl_url; ?>">&#11015; Download File</a>
    </div>
<?php endif; ?>

</body>
</html>
