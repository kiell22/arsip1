<?php
ob_start();
session_start();
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$today   = date('Y-m-d');
$q_inst  = mysqli_query($config, "SELECT * FROM tbl_instansi");
$inst    = mysqli_fetch_array($q_inst);

// Antrian sedang diperiksa
$q_aktif = mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today' AND status='diperiksa' ORDER BY no_antrian ASC LIMIT 5");

// Antrian menunggu berikutnya
$q_tunggu = mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today' AND status='menunggu' ORDER BY no_antrian ASC LIMIT 8");

// Antrian selesai hari ini
$q_selesai = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian WHERE tgl_daftar='$today' AND status='disetujui'");
$total_selesai = mysqli_fetch_array($q_selesai)['t'];

// Total hari ini
$q_total = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian WHERE tgl_daftar='$today'");
$total_hari = mysqli_fetch_array($q_total)['t'];

// Menunggu
$q_nunggu = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian WHERE tgl_daftar='$today' AND status='menunggu'");
$total_nunggu = mysqli_fetch_array($q_nunggu)['t'];
?>
<!doctype html>
<html lang="id">
<head>
<title>SILAPA - Monitor Antrian | <?php echo htmlspecialchars($inst['nama']); ?></title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="shortcut icon" href="upload/<?php echo $inst['logo']; ?>">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;}
body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:#0f172a;
    color:#fff;
    min-height:100vh;
    overflow-x:hidden;
}

/* TOP BAR */
.topbar{
    background:rgba(255,255,255,.04);
    border-bottom:1px solid rgba(255,255,255,.08);
    padding:14px 32px;
    display:flex;align-items:center;justify-content:space-between;
    gap:16px;
}
.topbar-brand{display:flex;align-items:center;gap:12px;}
.topbar-brand img{width:42px;height:42px;object-fit:contain;}
.topbar-brand-text strong{display:block;font-size:.95rem;font-weight:700;color:#fff;}
.topbar-brand-text small{font-size:.75rem;color:#64748b;}
.topbar-right{display:flex;align-items:center;gap:24px;}
.topbar-clock{text-align:right;}
.topbar-clock .clock-time{font-size:1.6rem;font-weight:800;color:#fff;line-height:1;letter-spacing:1px;}
.topbar-clock .clock-date{font-size:.75rem;color:#64748b;margin-top:2px;}
.topbar-stats{display:flex;gap:16px;}
.ts-item{text-align:center;padding:6px 16px;background:rgba(255,255,255,.06);border-radius:8px;}
.ts-item .tsn{font-size:1.3rem;font-weight:800;line-height:1;}
.ts-item .tsl{font-size:.68rem;color:#64748b;margin-top:2px;}
.ts-item.blue .tsn{color:#60a5fa;}
.ts-item.green .tsn{color:#34d399;}
.ts-item.amber .tsn{color:#fbbf24;}

/* MAIN GRID */
.main{
    display:grid;
    grid-template-columns:1fr 340px;
    gap:20px;
    padding:20px 32px 24px;
    min-height:calc(100vh - 73px);
}

/* PANEL KIRI - SEDANG DIPANGGIL */
.panel-left{display:flex;flex-direction:column;gap:16px;}
.panel-label{
    font-size:.72rem;font-weight:700;letter-spacing:1.5px;
    text-transform:uppercase;color:#64748b;
    margin-bottom:4px;
}

/* NOMOR AKTIF UTAMA */
.active-main{
    background:linear-gradient(135deg,#1e3a8a,#2563eb,#0ea5e9);
    border-radius:20px;padding:32px 36px;
    display:flex;align-items:center;gap:28px;
    position:relative;overflow:hidden;
    flex:1;
}
.active-main::before{
    content:'';position:absolute;
    top:-60px;right:-60px;
    width:220px;height:220px;
    background:rgba(255,255,255,.06);
    border-radius:50%;
}
.active-main::after{
    content:'';position:absolute;
    bottom:-40px;left:20%;
    width:140px;height:140px;
    background:rgba(255,255,255,.04);
    border-radius:50%;
}
.active-icon{
    width:72px;height:72px;border-radius:16px;
    background:rgba(255,255,255,.15);
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;position:relative;z-index:1;
}
.active-icon .material-icons{font-size:2.2rem;color:#fff;}
.active-body{position:relative;z-index:1;flex:1;}
.active-body .ab-label{font-size:.78rem;color:rgba(255,255,255,.65);font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px;}
.active-body .ab-no{font-size:5rem;font-weight:900;color:#fff;line-height:1;margin-bottom:8px;}
.active-body .ab-name{font-size:1.3rem;font-weight:700;color:#fff;margin-bottom:4px;}
.active-body .ab-detail{font-size:.85rem;color:rgba(255,255,255,.7);}
.active-empty{
    background:rgba(255,255,255,.04);border:2px dashed rgba(255,255,255,.1);
    border-radius:20px;padding:48px 36px;
    display:flex;align-items:center;justify-content:center;
    flex-direction:column;gap:12px;text-align:center;flex:1;
}
.active-empty .material-icons{font-size:3rem;color:#334155;}
.active-empty p{color:#475569;font-size:.95rem;}

/* ANTRIAN AKTIF LAINNYA (jika lebih dari 1) */
.active-others{display:flex;flex-direction:column;gap:10px;}
.active-other-card{
    background:rgba(37,99,235,.15);
    border:1px solid rgba(37,99,235,.3);
    border-radius:12px;padding:14px 18px;
    display:flex;align-items:center;gap:14px;
}
.active-other-card .aoc-no{
    font-size:1.8rem;font-weight:900;color:#60a5fa;
    min-width:52px;text-align:center;line-height:1;
}
.active-other-card .aoc-info .aoc-name{font-size:.9rem;font-weight:700;color:#fff;margin:0 0 2px;}
.active-other-card .aoc-info .aoc-detail{font-size:.78rem;color:#64748b;margin:0;}

/* PANEL KANAN - ANTRIAN MENUNGGU */
.panel-right{display:flex;flex-direction:column;gap:12px;}
.queue-list{display:flex;flex-direction:column;gap:8px;flex:1;}
.queue-card{
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px;padding:12px 16px;
    display:flex;align-items:center;gap:14px;
    transition:all .3s;
}
.queue-card:first-child{
    background:rgba(251,191,36,.1);
    border-color:rgba(251,191,36,.25);
}
.queue-card .qc-no{
    font-size:1.6rem;font-weight:900;
    min-width:44px;text-align:center;line-height:1;
    color:#94a3b8;
}
.queue-card:first-child .qc-no{color:#fbbf24;}
.queue-card .qc-info{flex:1;}
.queue-card .qc-info .qc-name{font-size:.88rem;font-weight:700;color:#e2e8f0;margin:0 0 2px;}
.queue-card .qc-info .qc-detail{font-size:.75rem;color:#64748b;margin:0;}
.queue-card .qc-pos{
    font-size:.7rem;font-weight:700;
    padding:3px 10px;border-radius:20px;
    background:rgba(255,255,255,.08);color:#94a3b8;
    white-space:nowrap;
}
.queue-card:first-child .qc-pos{background:rgba(251,191,36,.2);color:#fbbf24;}

.queue-empty{
    flex:1;display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    gap:10px;text-align:center;
    background:rgba(255,255,255,.03);
    border:1px dashed rgba(255,255,255,.08);
    border-radius:12px;padding:32px 16px;
}
.queue-empty .material-icons{font-size:2.5rem;color:#334155;}
.queue-empty p{color:#475569;font-size:.85rem;margin:0;}

/* TICKER BAWAH */
.ticker{
    background:rgba(37,99,235,.15);
    border-top:1px solid rgba(37,99,235,.2);
    padding:10px 32px;
    display:flex;align-items:center;gap:12px;
    font-size:.82rem;color:rgba(255,255,255,.7);
    position:fixed;bottom:0;left:0;right:0;
}
.ticker .tk-label{
    background:#2563eb;color:#fff;
    padding:3px 12px;border-radius:4px;
    font-size:.72rem;font-weight:700;
    white-space:nowrap;flex-shrink:0;
}
.ticker-text{white-space:nowrap;animation:ticker 30s linear infinite;}
@keyframes ticker{0%{transform:translateX(100vw);}100%{transform:translateX(-100%);}}

/* RESPONSIVE */
@media(max-width:900px){
    .main{grid-template-columns:1fr;padding:16px;}
    .topbar{padding:12px 16px;flex-wrap:wrap;}
    .topbar-stats{display:none;}
    .active-main{padding:24px;}
    .active-body .ab-no{font-size:3.5rem;}
}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-brand">
        <img src="upload/<?php echo $inst['logo']; ?>" alt="logo">
        <div class="topbar-brand-text">
            <strong><?php echo htmlspecialchars($inst['nama']); ?></strong>
        <small>Monitor Antrian Legalisir Ijazah - SILAPA</small>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-stats">
            <div class="ts-item blue">
                <div class="tsn"><?php echo $total_hari; ?></div>
                <div class="tsl">Total Hari Ini</div>
            </div>
            <div class="ts-item amber">
                <div class="tsn"><?php echo $total_nunggu; ?></div>
                <div class="tsl">Menunggu</div>
            </div>
            <div class="ts-item green">
                <div class="tsn"><?php echo $total_selesai; ?></div>
                <div class="tsl">Selesai</div>
            </div>
        </div>
        <div class="topbar-clock">
            <div class="clock-time" id="clock">--:--:--</div>
            <div class="clock-date" id="clock-date"></div>
        </div>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <!-- KIRI: SEDANG DIPANGGIL -->
    <div class="panel-left">
        <div class="panel-label">🔔 Sedang Dipanggil / Diperiksa</div>

        <?php
        $aktif_rows = [];
        while($r = mysqli_fetch_array($q_aktif)) $aktif_rows[] = $r;

        if(count($aktif_rows) > 0):
            $utama = $aktif_rows[0];
        ?>
        <div class="active-main">
            <div class="active-icon">
                <i class="material-icons">record_voice_over</i>
            </div>
            <div class="active-body">
                <div class="ab-label">Nomor Antrian Dipanggil</div>
                <div class="ab-no">#<?php echo str_pad($utama['no_antrian'], 3, '0', STR_PAD_LEFT); ?></div>
                <div class="ab-name"><?php echo htmlspecialchars($utama['nama']); ?></div>
                <div class="ab-detail">
                    <?php echo htmlspecialchars($utama['jenis_surat']); ?>
                    &nbsp;·&nbsp;
                    <?php echo $utama['jumlah_lembar']; ?> lembar
                </div>
            </div>
        </div>

        <?php if(count($aktif_rows) > 1): ?>
        <div class="active-others">
            <?php for($i=1;$i<count($aktif_rows);$i++): $r=$aktif_rows[$i]; ?>
            <div class="active-other-card">
                <div class="aoc-no">#<?php echo str_pad($r['no_antrian'],3,'0',STR_PAD_LEFT); ?></div>
                <div class="aoc-info">
                    <p class="aoc-name"><?php echo htmlspecialchars($r['nama']); ?></p>
                    <p class="aoc-detail"><?php echo htmlspecialchars($r['jenis_surat']); ?> · <?php echo $r['jumlah_lembar']; ?> lembar</p>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="active-empty">
            <i class="material-icons">hourglass_empty</i>
            <p>Belum ada antrian yang sedang dipanggil</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- KANAN: ANTRIAN MENUNGGU -->
    <div class="panel-right">
        <div class="panel-label">⏳ Antrian Menunggu</div>

        <?php
        $tunggu_rows = [];
        while($r = mysqli_fetch_array($q_tunggu)) $tunggu_rows[] = $r;

        if(count($tunggu_rows) > 0): ?>
        <div class="queue-list">
            <?php foreach($tunggu_rows as $i => $r): ?>
            <div class="queue-card">
                <div class="qc-no"><?php echo str_pad($r['no_antrian'],3,'0',STR_PAD_LEFT); ?></div>
                <div class="qc-info">
                    <p class="qc-name"><?php echo htmlspecialchars($r['nama']); ?></p>
                    <p class="qc-detail"><?php echo htmlspecialchars($r['jenis_surat']); ?> · <?php echo $r['jumlah_lembar']; ?> lembar</p>
                </div>
                <div class="qc-pos">
                    <?php echo $i === 0 ? 'Berikutnya' : 'Ke-'.($i+1); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="queue-empty">
            <i class="material-icons">done_all</i>
            <p>Tidak ada antrian yang menunggu</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- TICKER -->
<div class="ticker">
    <span class="tk-label">INFO</span>
    <div style="overflow:hidden;flex:1;">
        <span class="ticker-text">
            Selamat datang di layanan legalisir ijazah SILAPA - <?php echo htmlspecialchars($inst['nama']); ?>.
            Harap perhatikan nomor antrian Anda di layar ini.
            Jika nomor Anda dipanggil, segera menuju loket pelayanan.
            Pendaftaran antrian online tersedia di <?php echo $_SERVER['HTTP_HOST']; ?>/arsip/antrian.php
        </span>
    </div>
</div>

<script>
// Jam WIT
var hariArr = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
var bulanArr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

function tickClock(){
    var now = new Date();
    var wit = new Date(now.getTime() + (9 * 3600000));
    var h = String(wit.getUTCHours()).padStart(2,'0');
    var m = String(wit.getUTCMinutes()).padStart(2,'0');
    var s = String(wit.getUTCSeconds()).padStart(2,'0');
    document.getElementById('clock').textContent = h+':'+m+':'+s;

    var hari = hariArr[wit.getUTCDay()];
    var tgl  = wit.getUTCDate();
    var bln  = bulanArr[wit.getUTCMonth()];
    var thn  = wit.getUTCFullYear();
    document.getElementById('clock-date').textContent = hari+', '+tgl+' '+bln+' '+thn+' WIT';
}
setInterval(tickClock, 1000); tickClock();

// Auto refresh setiap 15 detik
setTimeout(function(){ location.reload(); }, 15000);
</script>
</body>
</html>
