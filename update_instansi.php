<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$config = conn($host, $username, $password, $database);

$q = mysqli_query($config, "UPDATE tbl_instansi SET
    institusi  = 'Pemerintah Provinsi Papua',
    nama       = 'Dinas Pendidikan Provinsi Papua',
    status     = 'Instansi Pemerintah',
    alamat     = 'Provinsi Papua'
    WHERE id_instansi = 1");

if ($q) {
    echo '<p style="color:green;font-size:1.1rem;">&#10003; Nama instansi berhasil diperbarui ke <strong>Dinas Pendidikan Provinsi Papua</strong>.</p>';
} else {
    echo '<p style="color:red;">Gagal: ' . mysqli_error($config) . '</p>';
}

echo '<br><a href="index.php" style="padding:10px 20px;background:#546e7a;color:#fff;text-decoration:none;border-radius:4px;">Ke Halaman Login</a>';
?>
