<?php
session_start();
include 'koneksi.php';

// Cek apakah user petugas sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['level'] !== 'petugas') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id_petugas = (int)$_SESSION['user']['id_petugas'];

$tanggal_hari_ini = date('Y-m-d');
$bulan_ini = date('m');
$tahun_ini = date('Y');

// Ambil transaksi yang ditangani oleh petugas ini di bulan ini

$lunas_query = mysqli_query($koneksi, "
    SELECT 
        pembayaran.nisn,
        pembayaran.bulan_dibayar,
        pembayaran.tahun_dibayar,
        spp.nominal,
        SUM(pembayaran.jumlah_bayar) AS total_bayar,
        MAX(pembayaran.tgl_bayar) AS terakhir_bayar
    FROM pembayaran
    LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp
    WHERE 
        pembayaran.id_petugas = $id_petugas AND
        MONTH(pembayaran.tgl_bayar) = '$bulan_ini' AND 
        YEAR(pembayaran.tgl_bayar) = '$tahun_ini'
    GROUP BY pembayaran.nisn, pembayaran.bulan_dibayar, pembayaran.tahun_dibayar
");

$total_pembayaran_bulan_ini = 0;
$jumlah_transaksi_hari_ini = 0;

while ($row = mysqli_fetch_assoc($lunas_query)) {
    if ($row['total_bayar'] >= $row['nominal']) {
        $total_pembayaran_bulan_ini += $row['total_bayar'];

        // Cek apakah pelunasan terjadi hari ini
        if (date('Y-m-d', strtotime($row['terakhir_bayar'])) === $tanggal_hari_ini) {
            $jumlah_transaksi_hari_ini++;
        }
    }
}

echo json_encode([
    'pembayaran' => "Rp " . number_format($total_pembayaran_bulan_ini, 0, ',', '.'),
    'transaksiHariIni' => $jumlah_transaksi_hari_ini
]);
