<?php
ob_start();
session_start();
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$q_inst        = mysqli_query($config, "SELECT * FROM tbl_instansi");
$inst          = mysqli_fetch_array($q_inst);
$today         = date('Y-m-d');
$q_total       = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian");
$total_all     = mysqli_fetch_array($q_total)['t'];
$q_hari        = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian WHERE tgl_daftar='$today'");
$total_hari    = mysqli_fetch_array($q_hari)['t'];
$q_selesai     = mysqli_query($config, "SELECT COUNT(*) as t FROM tbl_antrian WHERE status='disetujui'");
$total_selesai = mysqli_fetch_array($q_selesai)['t'];
?>
<!doctype html>
<html lang="id">
<head>
<title>SILAPA - Antrian Legalisir Ijazah | <?php echo htmlspecialchars($inst['nama']); ?></title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="description" content="Layanan antrian legalisir ijazah digital resmi SILAPA - <?php echo htmlspecialchars($inst['nama']); ?>"/>
<link rel="shortcut icon" href="upload/<?php echo $inst['logo']; ?>">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#fff;color:#1a1a2e;overflow-x:hidden;}

/* ── NAVBAR ── */
.navbar{
    position:fixed;top:0;left:0;right:0;z-index:999;
    height:68px;padding:0 6%;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(255,255,255,0.92);
    backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
    border-bottom:1px solid rgba(0,0,0,0.06);
    transition:box-shadow .3s;
}
.navbar.scrolled{box-shadow:0 4px 32px rgba(0,0,0,0.1);}
.nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.nav-brand img{width:36px;height:36px;object-fit:contain;}
.nav-brand-text{line-height:1.2;}
.nav-brand-text strong{display:block;font-size:.85rem;font-weight:700;color:#1a1a2e;}
.nav-brand-text small{font-size:.7rem;color:#6b7280;font-weight:400;}
.nav-menu{display:flex;align-items:center;gap:4px;}
.nav-menu a{
    padding:7px 14px;border-radius:8px;
    font-size:.83rem;font-weight:500;color:#4b5563;
    text-decoration:none;transition:all .2s;
}
.nav-menu a:hover{background:#f3f4f6;color:#1a1a2e;}
.nav-menu .nav-cta{
    background:#2563eb;color:#fff!important;
    padding:8px 18px;border-radius:8px;margin-left:6px;
    font-weight:600;
}
.nav-menu .nav-cta:hover{background:#1d4ed8;transform:translateY(-1px);}
.nav-toggle{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:6px;}
.nav-toggle span{width:22px;height:2px;background:#374151;border-radius:2px;transition:.3s;}

/* ── HERO ── */
.hero{
    min-height:100vh;
    padding:100px 6% 80px;
    display:flex;align-items:center;
    background:url('asset/img/bg1.webp') center center/cover no-repeat;
    position:relative;overflow:hidden;
}
.hero::before{
    content:'';
    position:absolute;inset:0;
    background:linear-gradient(160deg,rgba(15,23,42,.88) 0%,rgba(30,58,138,.82) 50%,rgba(29,78,216,.75) 100%);
    z-index:1;
}
.hero-blob,.hero-inner{position:relative;z-index:2;}
.hero-blob{
    position:absolute;border-radius:50%;filter:blur(80px);opacity:.25;pointer-events:none;
}
.hero-blob-1{width:500px;height:500px;background:#3b82f6;top:-100px;right:-100px;}
.hero-blob-2{width:350px;height:350px;background:#06b6d4;bottom:-80px;left:10%;}
.hero-blob-3{width:200px;height:200px;background:#8b5cf6;top:40%;right:30%;}
.hero-inner{
    max-width:1200px;margin:0 auto;width:100%;
    display:grid;grid-template-columns:1fr 420px;gap:60px;align-items:center;
    position:relative;z-index:2;
}
.hero-tag{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);
    color:rgba(255,255,255,.9);padding:6px 14px;border-radius:100px;
    font-size:.75rem;font-weight:600;letter-spacing:.5px;
    margin-bottom:22px;
}
.hero-tag .dot{width:7px;height:7px;background:#34d399;border-radius:50%;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
.hero h1{
    font-size:clamp(2.2rem,4.5vw,3.4rem);
    font-weight:800;color:#fff;line-height:1.15;
    margin-bottom:20px;
}
.hero h1 .accent{
    background:linear-gradient(90deg,#34d399,#06b6d4);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
}
.hero-desc{
    font-size:1rem;color:rgba(255,255,255,.75);
    line-height:1.8;margin-bottom:36px;max-width:500px;
}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:48px;}
.btn-primary{
    display:inline-flex;align-items:center;gap:8px;
    padding:14px 26px;border-radius:10px;
    background:#fff;color:#1d4ed8;
    font-weight:700;font-size:.92rem;text-decoration:none;
    box-shadow:0 4px 24px rgba(0,0,0,.2);
    transition:all .25s;
}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 10px 32px rgba(0,0,0,.25);}
.btn-ghost{
    display:inline-flex;align-items:center;gap:8px;
    padding:13px 24px;border-radius:10px;
    background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.25);
    color:#fff;font-weight:600;font-size:.9rem;text-decoration:none;
    backdrop-filter:blur(4px);transition:all .25s;
}
.btn-ghost:hover{background:rgba(255,255,255,.18);}
.hero-stats{display:flex;gap:16px;flex-wrap:wrap;}
.stat-pill{
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.15);
    border-radius:12px;padding:14px 20px;
    backdrop-filter:blur(8px);text-align:center;min-width:110px;
}
.stat-pill .snum{font-size:1.9rem;font-weight:800;color:#fff;line-height:1;}
.stat-pill .slbl{font-size:.7rem;color:rgba(255,255,255,.65);margin-top:4px;font-weight:500;}

/* HERO CARD */
.hero-card{
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.15);
    border-radius:24px;padding:28px;
    backdrop-filter:blur(16px);
    box-shadow:0 24px 64px rgba(0,0,0,.3);
}
.hc-head{display:flex;align-items:center;gap:12px;margin-bottom:24px;}
.hc-logo{width:46px;height:46px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;overflow:hidden;border-radius:8px;}
.hc-logo img{width:38px;height:38px;object-fit:contain;}
.hc-info h6{font-size:.88rem;font-weight:700;color:#fff;margin:0;}
.hc-info p{font-size:.7rem;color:rgba(255,255,255,.6);margin:0;}
.hc-ticket{
    background:rgba(255,255,255,.12);border-radius:14px;
    padding:20px;text-align:center;margin-bottom:16px;
    border:1px dashed rgba(255,255,255,.2);
}
.hc-ticket .t-label{font-size:.68rem;color:rgba(255,255,255,.6);font-weight:500;letter-spacing:.5px;text-transform:uppercase;}
.hc-ticket .t-num{font-size:4rem;font-weight:900;color:#fff;line-height:1;margin:4px 0;}
.hc-ticket .t-date{font-size:.72rem;color:rgba(255,255,255,.5);}
.hc-rows{display:flex;flex-direction:column;gap:8px;}
.hc-row{
    display:flex;justify-content:space-between;align-items:center;
    background:rgba(255,255,255,.07);border-radius:8px;padding:9px 13px;
}
.hc-row .rk{font-size:.72rem;color:rgba(255,255,255,.55);}
.hc-row .rv{font-size:.78rem;color:#fff;font-weight:600;}
.badge-ok{
    background:rgba(52,211,153,.2);color:#34d399;
    padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;
}

/* ── SECTION BASE ── */
section{padding:88px 6%;}
.wrap{max-width:1200px;margin:0 auto;}
.sec-tag{
    display:inline-block;
    background:#eff6ff;color:#2563eb;
    padding:5px 14px;border-radius:100px;
    font-size:.75rem;font-weight:700;letter-spacing:.5px;
    margin-bottom:12px;
}
.sec-title{font-size:clamp(1.7rem,3vw,2.3rem);font-weight:800;color:#1a1a2e;line-height:1.25;margin-bottom:12px;}
.sec-sub{font-size:.95rem;color:#6b7280;line-height:1.8;max-width:520px;}

/* ── ABOUT ── */
.about{background:#f8faff;}
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;}
.about-visual{
    background:linear-gradient(145deg,#1e3a8a,#2563eb,#0ea5e9);
    border-radius:24px;padding:28px;
    display:flex;flex-direction:column;gap:12px;
}
.av-card{
    background:rgba(255,255,255,.12);border-radius:12px;
    padding:14px 16px;display:flex;align-items:center;gap:14px;
    border:1px solid rgba(255,255,255,.1);
    transition:transform .2s;
}
.av-card:hover{transform:translateX(4px);}
.av-icon{
    width:42px;height:42px;border-radius:10px;
    background:rgba(255,255,255,.18);
    display:flex;align-items:center;justify-content:center;
    font-size:1.2rem;flex-shrink:0;
}
.av-text p{font-size:.68rem;color:rgba(255,255,255,.65);margin:0;}
.av-text h6{font-size:.85rem;font-weight:700;color:#fff;margin:0;}
.about-content .sec-sub{margin-bottom:28px;}
.check-list{display:flex;flex-direction:column;gap:12px;}
.check-item{display:flex;align-items:flex-start;gap:12px;}
.check-item .ci{
    width:22px;height:22px;border-radius:50%;
    background:#eff6ff;color:#2563eb;
    display:flex;align-items:center;justify-content:center;
    font-size:.65rem;font-weight:800;flex-shrink:0;margin-top:2px;
}
.check-item p{font-size:.88rem;color:#4b5563;line-height:1.65;margin:0;}

/* ── STEPS ── */
.steps{background:#fff;}
.steps-grid{
    display:grid;grid-template-columns:repeat(4,1fr);gap:20px;
    position:relative;margin-top:48px;
}
.steps-grid::before{
    content:'';position:absolute;
    top:34px;left:12.5%;right:12.5%;height:2px;
    background:linear-gradient(90deg,#2563eb,#0ea5e9);
    z-index:0;
}
.step{
    background:#fff;border-radius:18px;padding:28px 20px 24px;
    text-align:center;position:relative;z-index:1;
    border:1.5px solid #e5e7eb;
    transition:all .3s;
}
.step:hover{border-color:#bfdbfe;box-shadow:0 12px 36px rgba(37,99,235,.12);transform:translateY(-6px);}
.step-n{
    width:52px;height:52px;border-radius:50%;
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;font-size:1.2rem;font-weight:800;
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 14px;
    box-shadow:0 6px 20px rgba(37,99,235,.35);
}
.step-emoji{font-size:1.7rem;margin-bottom:10px;}
.step h5{font-size:.92rem;font-weight:700;color:#1a1a2e;margin-bottom:8px;}
.step p{font-size:.8rem;color:#6b7280;line-height:1.65;margin:0;}

/* ── FEATURES ── */
.features{background:#f8faff;}
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:48px;}
.feat-card{
    background:#fff;border-radius:18px;padding:28px 24px;
    border:1.5px solid #e5e7eb;
    transition:all .3s;
}
.feat-card:hover{border-color:#bfdbfe;box-shadow:0 12px 36px rgba(37,99,235,.1);transform:translateY(-4px);}
.feat-icon{
    width:50px;height:50px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    margin-bottom:16px;
}
.feat-card h5{font-size:.92rem;font-weight:700;color:#1a1a2e;margin-bottom:8px;}
.feat-card p{font-size:.83rem;color:#6b7280;line-height:1.65;margin:0;}
.fi-blue{background:#eff6ff;color:#2563eb;}
.fi-green{background:#f0fdf4;color:#16a34a;}
.fi-amber{background:#fffbeb;color:#d97706;}
.fi-purple{background:#faf5ff;color:#7c3aed;}
.fi-teal{background:#f0fdfa;color:#0d9488;}
.fi-rose{background:#fff1f2;color:#e11d48;}

/* ── JAM ── */
.jam{background:#fff;}
.jam-grid{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;}
.jam-list{display:flex;flex-direction:column;gap:10px;margin-top:28px;}
.jam-item{
    display:flex;justify-content:space-between;align-items:center;
    padding:14px 18px;border-radius:10px;
    background:#f8faff;border-left:3px solid #2563eb;
}
.jam-item.off{background:#fff5f5;border-left-color:#ef4444;}
.jam-item .day{font-size:.88rem;font-weight:600;color:#1a1a2e;}
.jam-item .time{font-size:.85rem;color:#4b5563;}
.jam-item .time.closed{color:#ef4444;font-weight:600;}
.jam-box{
    background:linear-gradient(145deg,#1e3a8a,#2563eb);
    border-radius:24px;padding:40px 32px;text-align:center;color:#fff;
}
.jam-clock{font-size:3.2rem;font-weight:800;letter-spacing:2px;line-height:1;margin-bottom:6px;}
.jam-tz{font-size:.8rem;opacity:.7;margin-bottom:28px;}
.jam-note{
    background:rgba(255,255,255,.12);border-radius:12px;
    padding:16px;font-size:.82rem;line-height:1.7;text-align:left;
    border:1px solid rgba(255,255,255,.1);
}

/* ── CONTACT ── */
.contact{background:#f8faff;}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;}
.contact-items{display:flex;flex-direction:column;gap:20px;}
.ci-item{display:flex;gap:16px;align-items:flex-start;}
.ci-icon{
    width:46px;height:46px;border-radius:12px;
    background:#eff6ff;color:#2563eb;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
}
.ci-icon .material-icons{font-size:1.2rem;}
.ci-body h6{font-size:.82rem;font-weight:700;color:#1a1a2e;margin:0 0 4px;}
.ci-body p,.ci-body a{font-size:.88rem;color:#4b5563;margin:0;line-height:1.6;text-decoration:none;}
.ci-body a:hover{color:#2563eb;}
.cta-card{
    background:linear-gradient(145deg,#1e3a8a,#2563eb,#0ea5e9);
    border-radius:24px;padding:40px 32px;color:#fff;
    display:flex;flex-direction:column;justify-content:center;
}
.cta-card h4{font-size:1.4rem;font-weight:800;margin-bottom:12px;line-height:1.3;}
.cta-card p{font-size:.9rem;opacity:.85;line-height:1.75;margin-bottom:28px;}
.cta-btns{display:flex;flex-direction:column;gap:10px;}
.btn-cta-w{
    display:inline-flex;align-items:center;gap:8px;
    padding:13px 22px;border-radius:10px;
    background:#fff;color:#1d4ed8;
    font-weight:700;font-size:.88rem;text-decoration:none;
    transition:all .2s;width:fit-content;
}
.btn-cta-w:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.15);}
.btn-cta-o{
    display:inline-flex;align-items:center;gap:8px;
    padding:12px 22px;border-radius:10px;
    background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.3);
    color:#fff;font-weight:600;font-size:.88rem;text-decoration:none;
    transition:all .2s;width:fit-content;
}
.btn-cta-o:hover{background:rgba(255,255,255,.2);}

/* ── FOOTER ── */
footer{background:#0f172a;color:#fff;padding:56px 6% 28px;}
.footer-inner{max-width:1200px;margin:0 auto;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px;margin-bottom:48px;}
.footer-brand img{width:44px;height:44px;object-fit:contain;margin-bottom:14px;}
.footer-brand h5{font-size:.95rem;font-weight:700;margin-bottom:8px;}
.footer-brand p{font-size:.8rem;color:#6b7280;line-height:1.75;margin:0;}
.footer-col h6{font-size:.8rem;font-weight:700;color:#93c5fd;margin-bottom:16px;letter-spacing:.5px;text-transform:uppercase;}
.footer-col ul{list-style:none;}
.footer-col ul li{margin-bottom:9px;}
.footer-col ul li a{font-size:.82rem;color:#6b7280;text-decoration:none;transition:color .2s;}
.footer-col ul li a:hover{color:#fff;}
.footer-bottom{
    border-top:1px solid rgba(255,255,255,.07);
    padding-top:24px;
    display:flex;justify-content:space-between;align-items:center;
    flex-wrap:wrap;gap:8px;
}
.footer-bottom p{font-size:.78rem;color:#4b5563;margin:0;}

/* ── RESPONSIVE ── */
@media(max-width:960px){
    .hero-inner{grid-template-columns:1fr;text-align:center;}
    .hero-actions{justify-content:center;}
    .hero-stats{justify-content:center;}
    .hero-card{max-width:380px;margin:0 auto;}
    .about-grid,.jam-grid,.contact-grid{grid-template-columns:1fr;}
    .steps-grid{grid-template-columns:repeat(2,1fr);}
    .steps-grid::before{display:none;}
    .feat-grid{grid-template-columns:repeat(2,1fr);}
    .footer-grid{grid-template-columns:1fr;}
    .nav-menu{
        display:none;position:fixed;
        top:68px;left:0;right:0;
        background:#fff;flex-direction:column;
        padding:16px;gap:4px;
        box-shadow:0 8px 32px rgba(0,0,0,.1);
        border-top:1px solid #f3f4f6;
    }
    .nav-menu.open{display:flex;}
    .nav-menu a{padding:10px 14px;}
    .nav-toggle{display:flex;}
}
@media(max-width:600px){
    .steps-grid,.feat-grid{grid-template-columns:1fr;}
    section{padding:64px 5%;}
    .hero{padding:90px 5% 64px;}
    .hero h1{font-size:2rem;}
}
</style>
</head>
<body>
<?php include 'landing_body.php'; ?>
</body>
</html>
