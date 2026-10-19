DROP TABLE tbl_antrian;

CREATE TABLE `tbl_antrian` (
  `id_antrian` int(10) NOT NULL AUTO_INCREMENT,
  `no_antrian` int(5) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `jenis_surat` varchar(250) NOT NULL,
  `jumlah_lembar` int(5) NOT NULL DEFAULT 1,
  `keterangan` text DEFAULT NULL,
  `file_surat` text DEFAULT NULL,
  `status` enum('menunggu','diperiksa','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan_pegawai` text DEFAULT NULL,
  `tgl_daftar` date NOT NULL,
  `tgl_proses` datetime DEFAULT NULL,
  `id_user` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id_antrian`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO tbl_antrian VALUES("1","1","jeskiel","081247052935","surat ijazah","3","","1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221.docx","disetujui","","2026-09-22","2026-09-22 17:15:17","1");
INSERT INTO tbl_antrian VALUES("3","3","rajab","082131231","surat rumah","3","tolong di legalisir","17900656030-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (12).docx,17900656031-1790064868-Berikut adalah c","disetujui","saya setuju","2026-09-22","2026-09-23 07:50:17","1");
INSERT INTO tbl_antrian VALUES("4","4","rajab","082131231","surat rumah","3","tolong di legalisir","17900659410-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (12).docx,17900659411-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (11).docx,17900659412-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (9).docx,17900659413-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (10).docx,17900659414-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (8).docx,17900659415-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (7).docx","menunggu","","2026-09-22","","");
INSERT INTO tbl_antrian VALUES("5","4","rajab","082131231","surat rumah","3","tolong di legalisir","17900660040-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (12).docx,17900660041-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (11).docx,17900660042-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (9).docx,17900660043-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (10).docx,17900660044-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (8).docx,17900660045-1790064868-Berikut adalah contoh best practice implementasi dan penanganan studi kasus terkait cuti di lingkungan Kementerian Keuangan yang disusun berdasarkan ketentuan pada PMK Nomor 221 (7).docx","menunggu","","2026-09-22","","");
INSERT INTO tbl_antrian VALUES("7","1","admin","082321213","surat transkrip","3","tolong di legalisir","17901179310-2024pmkeuangan108.pdf,17901179311-2026pmkeuangan015.pdf","diperiksa","","2026-09-23","2026-09-23 09:23:39","1");
INSERT INTO tbl_antrian VALUES("8","2","jeskiel","081231213","surat tanah","2","tolong di legalisir","17901229490-SKPP_PNS_668795_00_001310_2026_SIGNED.pdf","disetujui","","2026-09-23","2026-09-23 09:23:23","1");



DROP TABLE tbl_instansi;

CREATE TABLE `tbl_instansi` (
  `id_instansi` tinyint(1) NOT NULL,
  `institusi` varchar(150) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `status` varchar(150) NOT NULL,
  `alamat` varchar(150) NOT NULL,
  `kepsek` varchar(50) NOT NULL,
  `nip` varchar(25) NOT NULL,
  `website` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `logo` varchar(250) NOT NULL,
  `id_user` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_instansi`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO tbl_instansi VALUES("1","Pemerintah Provinsi Papua","Dinas Pendidikan Provinsi Papua","Instansi Pemerintah","Jl. Tanjung Ria Base G Jayapura Papua 99771, Tanjung Ria, Jayapura Utara, Jayapura City, Papua 99117","marthen","19650601 199303 1 019","https://disdik.papua.go.id/","disdik@gmail.com","logo-bapperida.png","1");



DROP TABLE tbl_user;

CREATE TABLE `tbl_user` (
  `id_user` tinyint(2) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(35) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nip` varchar(25) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO tbl_user VALUES("1","admin","21232f297a57a5a743894a0e4a801fc3","Administrator","52654765474","1");
INSERT INTO tbl_user VALUES("2","pengguna","8b097b8a86f9d6a991357d40d3d58634","Username","58647333","3");
INSERT INTO tbl_user VALUES("3","kiel1","5fbf605f86bf300d13b428a4a1233b0e","jeskiel","912131232","3");
INSERT INTO tbl_user VALUES("4","pegawai","8f994d65f753abdbefc94ff989105cbc","kiel","081233222","3");



