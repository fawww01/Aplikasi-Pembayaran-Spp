<?php
include 'koneksi.php';

if (isset($_GET['nisn'], $_GET['bulan'], $_GET['tahun'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_GET['nisn']);
    $bulan = mysqli_real_escape_string($koneksi, $_GET['bulan']);
    $tahun = mysqli_real_escape_string($koneksi, $_GET['tahun']);

    // Jalankan query DELETE berdasarkan nisn, bulan, dan tahun
    $query = "DELETE FROM pembayaran 
              WHERE nisn = '$nisn' 
              AND bulan_dibayar = '$bulan' 
              AND tahun_dibayar = '$tahun'";

    if (mysqli_query($koneksi, $query)) {
        // Berhasil menghapus
        header("Location: index_petugas.php?page=history_petugas&status=sukses");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    echo "Parameter tidak lengkap.";
}
?>
