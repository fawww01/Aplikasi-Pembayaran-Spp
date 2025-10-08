<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$nisn = $_SESSION['siswa']['nisn'] ?? null;
if (!$nisn) {
    die("NISN tidak ditemukan dalam session. Harap login terlebih dahulu.");
}

$query = mysqli_query($koneksi, "SELECT * FROM pembayaran 
    JOIN siswa ON pembayaran.nisn = siswa.nisn 
    JOIN spp ON pembayaran.id_spp = spp.id_spp 
    WHERE pembayaran.nisn = '$nisn'");

?>

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />


<h2>Laporan Pembayaran Siswa</h2>

<table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Tanggal Bayar</th>
            <th>Bulan Dibayar</th>
            <th>Tahun Dibayar</th>
            <th>Nominal SPP</th>
            <th>Jumlah Bayar</th>
        </tr>

    <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($query)) {
            echo "<tr>
                <td>{$no}</td>
                <td>{$row['tgl_bayar']}</td>
                <td>{$row['bulan_dibayar']}</td>
                <td>{$row['tahun_dibayar']}</td>
                <td>" . number_format($row['nominal']) . "</td>
                <td>" . number_format($row['jumlah_bayar']) . "</td>
            </tr>";
            $no++;
        }
    ?>
</table>
