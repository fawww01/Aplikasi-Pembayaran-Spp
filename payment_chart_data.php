<?php
include 'koneksi.php';

$data = [];
$query = mysqli_query($koneksi, "
    SELECT bulan_dibayar, SUM(jumlah_bayar) AS total_bayar
    FROM pembayaran
    GROUP BY bulan_dibayar
    ORDER BY FIELD(bulan_dibayar, 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember')
");

while ($row = mysqli_fetch_assoc($query)) {
    $data['labels'][] = $row['bulan_dibayar'];
    $data['values'][] = $row['total_bayar'];
}

echo json_encode($data);
?>
