<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <a href="landing.php" class="nav-brand">
        <img src="upload/<?php echo $inst['logo']; ?>" alt="logo">
        <div class="nav-brand-text">
            <strong><?php echo htmlspecialchars($inst['nama']); ?></strong>
            <small>Sistem Antrian Legalisir</small>
        </div>
    </a>
    <div class="nav-menu" id="navMenu">
        <a href="#tentang">Tentang</a>
        <a href="#alur">Cara Pakai</a>
        <a href="#fitur">Fitur</a>
        <a href="#jam">Jam Layanan</a>
        <a href="#kontak">Kontak</a>
        <a href="antrian.php" class="nav-cta">Daftar Antrian</a>
    </div>
    <div class="nav-toggle" id="navToggle" onclick="toggleNav()">
        <span></span><span></span><span></span>
    </div>
</nav>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-inner">
        <div class="hero-left">
            <div class="hero-tag">
                <span class="dot"></span> Layanan Digital Resmi
            </div>
            <h1>Antrian Legalisir<br>Surat <span class="accent">Lebih Mudah</span><br>& Transparan</h1>
            <p class="hero-desc">Urus legalisir dokumen pendidikan Anda secara online. Daftar antrian dari mana saja, pantau status secara langsung, tanpa perlu mengantri panjang di kantor.</p>
            <div class="hero-actions">
                <a href="antrian.php" class="btn-primary">
                    <i class="material-icons" style="font-size:18px;">queue</i> Ambil Nomor Antrian
                </a>
                <a href="#alur" class="btn-ghost">
                    <i class="material-icons" style="font-size:18px;">play_circle_outline</i> Cara Penggunaan
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-pill">
                    <div class="snum"><?php echo number_format($total_all); ?></div>
                    <div class="slbl">Total Antrian</div>
                </div>
                <div class="stat-pill">
                    <div class="snum"><?php echo number_format($total_hari); ?></div>
                    <div class="slbl">Antrian Hari Ini</div>
                </div>
                <div class="stat-pill">
                    <div class="snum"><?php echo number_format($total_selesai); ?></div>
                    <div class="slbl">Berhasil Dilegalisir</div>
                </div>
            </div>
        </div>
        <div class="hero-card">
            <div class="hc-head">
                <div class="hc-logo">
                    <img src="upload/<?php echo $inst['logo']; ?>" alt="logo">
                </div>
                <div class="hc-info">
                    <h6>Tiket Antrian</h6>
                    <p><?php echo htmlspecialchars($inst['nama']); ?></p>
                </div>
            </div>
            <div class="hc-ticket">
                <div class="t-label">Nomor Antrian Berikutnya</div>
                <div class="t-num">#<?php echo str_pad($total_hari + 1, 3, '0', STR_PAD_LEFT); ?></div>
                <div class="t-date"><?php echo date('d F Y'); ?></div>
            </div>
            <div class="hc-rows">
                <div class="hc-row"><span class="rk">Jenis Dokumen</span><span class="rv">Ijazah / Transkrip</span></div>
                <div class="hc-row"><span class="rk">Estimasi Proses</span><span class="rv">1 Hari Kerja</span></div>
                <div class="hc-row"><span class="rk">Status Layanan</span><span class="rv"><span class="badge-ok">● Buka</span></span></div>
            </div>
        </div>
    </div>
</section>

<!-- TENTANG -->
<section class="about" id="tentang">
    <div class="wrap about-grid">
        <div class="about-visual">
            <div class="av-card">
                <div class="av-icon">📋</div>
                <div class="av-text">
                    <p>Total Surat Dilegalisir</p>
                    <h6><?php echo number_format($total_selesai); ?> Dokumen Selesai</h6>
                </div>
            </div>
            <div class="av-card">
                <div class="av-icon">⚡</div>
                <div class="av-text">
                    <p>Proses Cepat</p>
                    <h6>Rata-rata 1 Hari Kerja</h6>
                </div>
            </div>
            <div class="av-card">
                <div class="av-icon">📱</div>
                <div class="av-text">
                    <p>Akses Mudah</p>
                    <h6>Dari HP atau Komputer</h6>
                </div>
            </div>
            <div class="av-card">
                <div class="av-icon">🏛️</div>
                <div class="av-text">
                    <p>Layanan Resmi</p>
                    <h6><?php echo htmlspecialchars($inst['nama']); ?></h6>
                </div>
            </div>
        </div>
        <div class="about-content">
            <span class="sec-tag">Tentang Aplikasi</span>
            <h2 class="sec-title">Legalisir Surat Kini Lebih Mudah & Cepat</h2>
            <p class="sec-sub">Platform digital resmi untuk mempermudah masyarakat dalam mengurus legalisir dokumen pendidikan secara online, transparan, dan efisien.</p>
            <div class="check-list">
                <div class="check-item">
                    <div class="ci">✓</div>
                    <p>Daftar antrian secara online tanpa perlu datang langsung hanya untuk mengambil nomor.</p>
                </div>
                <div class="check-item">
                    <div class="ci">✓</div>
                    <p>Pantau status legalisir surat Anda secara real-time kapan saja dan di mana saja.</p>
                </div>
                <div class="check-item">
                    <div class="ci">✓</div>
                    <p>Upload dokumen pendukung langsung dari aplikasi dalam berbagai format file.</p>
                </div>
                <div class="check-item">
                    <div class="ci">✓</div>
                    <p>Bukti legalisir resmi dicetak dan ditandatangani oleh pejabat yang berwenang.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ALUR -->
<section class="steps" id="alur">
    <div class="wrap">
        <div style="text-align:center;">
            <span class="sec-tag">Cara Penggunaan</span>
            <h2 class="sec-title">4 Langkah Mudah Legalisir Surat</h2>
            <p class="sec-sub" style="margin:0 auto;">Proses yang sederhana dan transparan dari pendaftaran hingga dokumen selesai dilegalisir.</p>
        </div>
        <div class="steps-grid">
            <div class="step">
                <div class="step-n">1</div>
                <div class="step-emoji">📝</div>
                <h5>Isi Form Pendaftaran</h5>
                <p>Isi nama, nomor HP, jenis surat, jumlah lembar, dan upload file dokumen pendukung jika diperlukan.</p>
            </div>
            <div class="step">
                <div class="step-n">2</div>
                <div class="step-emoji">🎫</div>
                <h5>Dapatkan Nomor Antrian</h5>
                <p>Sistem memberikan nomor antrian otomatis. Simpan nomor ini untuk memantau status pengajuan Anda.</p>
            </div>
            <div class="step">
                <div class="step-n">3</div>
                <div class="step-emoji">🔍</div>
                <h5>Verifikasi Dokumen</h5>
                <p>Pegawai memeriksa dan memverifikasi dokumen yang Anda ajukan sesuai prosedur yang berlaku.</p>
            </div>
            <div class="step">
                <div class="step-n">4</div>
                <div class="step-emoji">✅</div>
                <h5>Ambil Dokumen</h5>
                <p>Setelah disetujui, bukti legalisir dicetak dan Anda dapat mengambil dokumen yang telah dilegalisir.</p>
            </div>
        </div>
    </div>
</section>

<!-- FITUR -->
<section class="features" id="fitur">
    <div class="wrap">
        <div style="text-align:center;">
            <span class="sec-tag">Fitur Unggulan</span>
            <h2 class="sec-title">Semua yang Anda Butuhkan</h2>
            <p class="sec-sub" style="margin:0 auto;">Fitur lengkap yang dirancang untuk memudahkan proses legalisir surat Anda dari awal hingga selesai.</p>
        </div>
        <div class="feat-grid">
            <div class="feat-card">
                <div class="feat-icon fi-blue"><i class="material-icons">queue</i></div>
                <h5>Antrian Online</h5>
                <p>Daftar antrian dari mana saja tanpa perlu datang ke kantor hanya untuk mengambil nomor antrian.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-green"><i class="material-icons">track_changes</i></div>
                <h5>Pantau Status Real-time</h5>
                <p>Cek status legalisir surat Anda kapan saja menggunakan nomor antrian yang telah diberikan.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-amber"><i class="material-icons">cloud_upload</i></div>
                <h5>Upload Dokumen</h5>
                <p>Upload file pendukung dalam berbagai format (PDF, Word, gambar) langsung dari aplikasi.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-purple"><i class="material-icons">verified_user</i></div>
                <h5>Verifikasi Resmi</h5>
                <p>Setiap pengajuan diperiksa langsung oleh pegawai yang berwenang sesuai prosedur resmi.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-teal"><i class="material-icons">print</i></div>
                <h5>Cetak Bukti Resmi</h5>
                <p>Bukti legalisir dicetak dalam format surat resmi lengkap dengan tanda tangan pejabat berwenang.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-rose"><i class="material-icons">refresh</i></div>
                <h5>Antrian Harian Teratur</h5>
                <p>Nomor antrian direset setiap hari sehingga antrian selalu teratur dan mudah dikelola.</p>
            </div>
        </div>
    </div>
</section>

<!-- JAM LAYANAN -->
<section class="jam" id="jam">
    <div class="wrap jam-grid">
        <div>
            <span class="sec-tag">Jam Operasional</span>
            <h2 class="sec-title">Waktu Layanan Legalisir</h2>
            <p style="color:#6b7280;line-height:1.75;font-size:.92rem;">Layanan legalisir tersedia pada hari dan jam kerja berikut. Pendaftaran antrian online dapat dilakukan kapan saja selama 24 jam.</p>
            <div class="jam-list">
                <div class="jam-item"><span class="day">Senin – Kamis</span><span class="time">08.00 – 16.00 WIT</span></div>
                <div class="jam-item"><span class="day">Jumat</span><span class="time">08.00 – 15.00 WIT</span></div>
                <div class="jam-item off"><span class="day">Sabtu</span><span class="time closed">Tutup</span></div>
                <div class="jam-item off"><span class="day">Minggu &amp; Hari Libur</span><span class="time closed">Tutup</span></div>
            </div>
        </div>
        <div class="jam-box">
            <div class="jam-clock" id="jam-now">--:--:--</div>
            <div class="jam-tz">Waktu Indonesia Timur (WIT)</div>
            <div class="jam-note">
                <strong>📌 Catatan Penting</strong><br><br>
                Pendaftaran antrian online dapat dilakukan <strong>24 jam sehari</strong>. Namun proses pemeriksaan dan legalisir hanya dilakukan pada jam kerja. Harap datang ke kantor sesuai jadwal yang telah ditentukan.
            </div>
        </div>
    </div>
</section>

<!-- KONTAK -->
<section class="contact" id="kontak">
    <div class="wrap">
        <span class="sec-tag">Hubungi Kami</span>
        <h2 class="sec-title" style="margin-bottom:40px;">Informasi Kontak</h2>
        <div class="contact-grid">
            <div class="contact-items">
                <div class="ci-item">
                    <div class="ci-icon"><i class="material-icons">business</i></div>
                    <div class="ci-body">
                        <h6>Nama Instansi</h6>
                        <p><?php echo htmlspecialchars($inst['nama']); ?></p>
                    </div>
                </div>
                <div class="ci-item">
                    <div class="ci-icon"><i class="material-icons">place</i></div>
                    <div class="ci-body">
                        <h6>Alamat</h6>
                        <p><?php echo htmlspecialchars($inst['alamat']); ?></p>
                    </div>
                </div>
                <div class="ci-item">
                    <div class="ci-icon"><i class="material-icons">language</i></div>
                    <div class="ci-body">
                        <h6>Website</h6>
                        <a href="<?php echo $inst['website']; ?>" target="_blank"><?php echo $inst['website']; ?></a>
                    </div>
                </div>
                <div class="ci-item">
                    <div class="ci-icon"><i class="material-icons">email</i></div>
                    <div class="ci-body">
                        <h6>Email</h6>
                        <p><?php echo htmlspecialchars($inst['email']); ?></p>
                    </div>
                </div>
            </div>
            <div class="cta-card">
                <h4>Siap Mengurus Legalisir Surat?</h4>
                <p>Daftar antrian sekarang dan urus legalisir surat Anda dengan mudah, cepat, dan transparan tanpa perlu mengantri lama.</p>
                <div class="cta-btns">
                    <a href="antrian.php" class="btn-cta-w">
                        <i class="material-icons" style="font-size:18px;">queue</i> Daftar Antrian Sekarang
                    </a>
                    <a href="antrian.php" class="btn-cta-o">
                        <i class="material-icons" style="font-size:18px;">search</i> Cek Status Antrian
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="upload/<?php echo $inst['logo']; ?>" alt="logo">
                <h5><?php echo htmlspecialchars($inst['nama']); ?></h5>
                <p>Sistem Antrian Legalisir Surat digital untuk mempermudah layanan masyarakat di bidang pendidikan.</p>
            </div>
            <div class="footer-col">
                <h6>Navigasi</h6>
                <ul>
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#tentang">Tentang</a></li>
                    <li><a href="#alur">Cara Pakai</a></li>
                    <li><a href="#fitur">Fitur</a></li>
                    <li><a href="#jam">Jam Layanan</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h6>Layanan</h6>
                <ul>
                    <li><a href="antrian.php">Daftar Antrian</a></li>
                    <li><a href="antrian.php">Cek Status</a></li>
                    <li><a href="index.php">Login Pegawai</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($inst['nama']); ?>. Hak Cipta Dilindungi.</p>
            <p>Sistem Antrian Legalisir Surat</p>
        </div>
    </div>
</footer>

<script>
// Jam WIT
function tickClock(){
    var now = new Date();
    var wit = new Date(now.getTime() + (9 * 3600000));
    var h = String(wit.getUTCHours()).padStart(2,'0');
    var m = String(wit.getUTCMinutes()).padStart(2,'0');
    var s = String(wit.getUTCSeconds()).padStart(2,'0');
    document.getElementById('jam-now').textContent = h+':'+m+':'+s;
}
setInterval(tickClock, 1000); tickClock();

// Navbar scroll
window.addEventListener('scroll', function(){
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 40);
});

// Mobile nav
function toggleNav(){
    document.getElementById('navMenu').classList.toggle('open');
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click', function(e){
        var t = document.querySelector(this.getAttribute('href'));
        if(t){
            e.preventDefault();
            document.getElementById('navMenu').classList.remove('open');
            window.scrollTo({top: t.offsetTop - 68, behavior:'smooth'});
        }
    });
});
</script>
