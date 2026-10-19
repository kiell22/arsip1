<?php
    ob_start();
    //cek session
    session_start();

    if(empty($_SESSION['admin'])){
        $_SESSION['err'] = '<center>Anda harus login terlebih dahulu!</center>';
        header("Location: ./");
        die();
    } else {
?>
<!--

Name        : SILAPA - Sistem Informasi Legalisir Ijazah Papua
Version     : v1.0.0
Description : Aplikasi untuk mengelola antrian dan legalisir ijazah secara digital.
Date        : 2024

-->
<!doctype html>
<html lang="en">

<!-- Include Head START -->
<?php include('include/head.php'); ?>
<!-- Include Head END -->

<!-- Body START -->
<body class="bg">

<!-- Header START -->
<header>

<!-- Include Navigation START -->
<?php include('include/menu.php'); ?>
<!-- Include Navigation END -->

</header>
<!-- Header END -->

<!-- Main START -->
<main>

    <!-- container START -->
    <div class="container">

    <?php
        if(isset($_REQUEST['page'])){
            $page = $_REQUEST['page'];
            switch ($page) {
                case 'antrian':
                    include "daftar_antrian.php";
                    break;
                case 'cetak_antrian':
                    include "cetak_antrian.php";
                    break;
                case 'sett':
                    include "pengaturan.php";
                    break;
                case 'pro':
                    include "profil.php";
                    break;
            }
        } else {
    ?>
        <!-- Row START -->
        <div class="row">

            <!-- Include Header Instansi START -->
            <?php include('include/header_instansi.php'); ?>
            <!-- Include Header Instansi END -->

            <!-- Welcome Message START -->
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <h4>Selamat Datang <?php echo $_SESSION['nama']; ?></h4>
                        <p class="description">Anda login sebagai
                        <?php
                            if($_SESSION['admin'] == 1){
                                echo "<strong>Super Admin</strong>. Anda memiliki akses penuh terhadap sistem.";
                            } elseif($_SESSION['admin'] == 2){
                                echo "<strong>Administrator</strong>. Berikut adalah statistik data yang tersimpan dalam sistem.";
                            } else {
                                echo "<strong>Pegawai</strong>. Berikut adalah statistik data antrian legalisir hari ini.";
                            }?></p>
                    </div>
                </div>
            </div>
            <!-- Welcome Message END -->

            <?php
                $today = date('Y-m-d');
                $c_menunggu  = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today' AND status='menunggu'"));
                $c_diperiksa = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today' AND status='diperiksa'"));
                $c_disetujui = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today' AND status='disetujui'"));
                $c_ditolak   = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today' AND status='ditolak'"));
                $c_total     = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_antrian WHERE tgl_daftar='$today'"));
                $c_user      = mysqli_num_rows(mysqli_query($config, "SELECT * FROM tbl_user"));
            ?>

            <!-- Info Statistic START -->
            <div class="col s12 m4">
                <div class="card cyan">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">queue</i> Total Antrian Hari Ini</span>
                        <h5 class="white-text"><?php echo $c_total; ?> Antrian</h5>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card yellow darken-3">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">hourglass_empty</i> Menunggu</span>
                        <h5 class="white-text"><?php echo $c_menunggu; ?> Antrian</h5>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card blue">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">find_in_page</i> Sedang Diverifikasi</span>
                        <h5 class="white-text"><?php echo $c_diperiksa; ?> Antrian</h5>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card green">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">check_circle</i> Disetujui</span>
                        <h5 class="white-text"><?php echo $c_disetujui; ?> Antrian</h5>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card deep-orange">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">cancel</i> Ditolak</span>
                        <h5 class="white-text"><?php echo $c_ditolak; ?> Antrian</h5>
                    </div>
                </div>
            </div>
            <?php if($_SESSION['admin'] == 1 || $_SESSION['admin'] == 2): ?>
            <div class="col s12 m4">
                <div class="card blue accent-2">
                    <div class="card-content">
                        <span class="card-title white-text"><i class="material-icons md-36">people</i> Jumlah Pengguna</span>
                        <h5 class="white-text"><?php echo $c_user; ?> Pengguna</h5>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- Info Statistic END -->

        </div>
        <!-- Row END -->
    <?php
        }
    ?>
    </div>
    <!-- container END -->

</main>
<!-- Main END -->

<!-- Include Footer START -->
<?php include('include/footer.php'); ?>
<!-- Include Footer END -->

</body>
<!-- Body END -->

</html>

<?php
    }
?>
