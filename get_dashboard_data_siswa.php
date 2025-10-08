<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['siswa'])) {
    echo json_encode(['error' => 'Belum login']);
    exit;
}

$nisn = $_SESSION['siswa']['nisn'] ?? null;
if (!$nisn) {
    echo json_encode(['error' => 'NISN tidak ditemukan']);
    exit;
}

// Ambil semua pembayaran siswa
$query = mysqli_query($koneksi, "
    SELECT pembayaran.bulan_dibayar, pembayaran.tahun_dibayar, spp.nominal, 
           SUM(pembayaran.jumlah_bayar) as total_dibayar
    FROM pembayaran
    JOIN spp ON spp.id_spp = pembayaran.id_spp
    WHERE pembayaran.nisn = '$nisn'
    GROUP BY pembayaran.bulan_dibayar, pembayaran.tahun_dibayar
");

$total_sisa = 0;
$total_dibayar = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $sisa = $row['nominal'] - $row['total_dibayar'];
    if ($sisa < 0) $sisa = 0;
    $total_sisa += $sisa;
    $total_dibayar += $row['total_dibayar'];
}

echo json_encode([
    'pembayaran' => number_format($total_dibayar),
    'sisa' => number_format($total_sisa)
]);
