<?php
session_start();
include 'koneksi.php';

$response = [
    "siswa" => 0,
    "petugas" => 0,
    "pembayaran" => 0,
    "transaksiHariIni" => 0,
];

$id_petugas = $_SESSION['user']['id_petugas'];

$querySiswa = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa");
if ($querySiswa) {
    $data = mysqli_fetch_assoc($querySiswa);
    $response['siswa'] = $data['total'];
}

$queryPetugas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM petugas");
if ($queryPetugas) {
    $data = mysqli_fetch_assoc($queryPetugas);
    $response['petugas'] = $data['total'];
}

$bulan_ini = date('m');
$tahun_ini = date('Y');
$queryPembayaran = mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM pembayaran 
    WHERE MONTH(tgl_bayar) = '$bulan_ini' 
      AND YEAR(tgl_bayar) = '$tahun_ini' 
      AND id_petugas = '$id_petugas'
");
if ($queryPembayaran) {
    $data = mysqli_fetch_assoc($queryPembayaran);
    $response['pembayaran'] = $data['total'];
}

$tanggal_hari_ini = date('Y-m-d');
$queryTransaksiHariIni = mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM pembayaran 
    WHERE DATE(tgl_bayar) = '$tanggal_hari_ini' 
      AND id_petugas = '$id_petugas'
");
if ($queryTransaksiHariIni) {
    $data = mysqli_fetch_assoc($queryTransaksiHariIni);
    $response['transaksiHariIni'] = $data['total'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
