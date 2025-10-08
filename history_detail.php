<?php
include 'koneksi.php';

if (!isset($_GET['nisn'], $_GET['bulan'], $_GET['tahun'])) {
    echo "Parameter tidak lengkap.";
    exit;
}

$nisn = mysqli_real_escape_string($koneksi, $_GET['nisn']);
$bulan = mysqli_real_escape_string($koneksi, $_GET['bulan']);
$tahun = mysqli_real_escape_string($koneksi, $_GET['tahun']);

// Query data siswa lengkap dengan pembayaran
$query = mysqli_query($koneksi, "
    SELECT 
        siswa.nisn,
        siswa.nama,
        siswa.alamat,
        siswa.no_telp,
        siswa.id_kelas,
        kelas.nama_kelas,
        pembayaran.bulan_dibayar,
        pembayaran.tahun_dibayar,
        spp.nominal,
        petugas.nama_petugas,
        MAX(pembayaran.tgl_bayar) AS tgl_bayar,
        SUM(pembayaran.jumlah_bayar) AS total_dibayar
    FROM pembayaran
    LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn
    LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp
    LEFT JOIN petugas ON petugas.id_petugas = pembayaran.id_petugas
    LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas
    WHERE pembayaran.nisn = '$nisn'
      AND pembayaran.bulan_dibayar = '$bulan'
      AND pembayaran.tahun_dibayar = '$tahun'
    GROUP BY pembayaran.nisn, pembayaran.bulan_dibayar, pembayaran.tahun_dibayar
    LIMIT 1
");

if (!$query || mysqli_num_rows($query) === 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

// Hitung sisa pembayaran
$sisa_pembayaran = $data['nominal'] - $data['total_dibayar'];
if ($sisa_pembayaran < 0)
    $sisa_pembayaran = 0;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Detail Pembayaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-4">
        <h2>Detail Pembayaran Siswa</h2>
        <table class="table table-bordered">
            <tr>
                <th>NISN</th>
                <td><?= htmlspecialchars($data['nisn']) ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?= htmlspecialchars($data['nama']) ?></td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td><?= htmlspecialchars($data['nama_kelas']) ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?= htmlspecialchars($data['alamat']) ?></td>
            </tr>
            <tr>
                <th>No Telepon</th>
                <td><?= htmlspecialchars($data['no_telp']) ?></td>
            </tr>
            <tr>
                <th>Bulan Dibayar</th>
                <td><?= htmlspecialchars($data['bulan_dibayar']) ?></td>
            </tr>
            <tr>
                <th>Tahun Dibayar</th>
                <td><?= htmlspecialchars($data['tahun_dibayar']) ?></td>
            </tr>
            <tr>
                <th>SPP</th>
                <td><?= "Rp " . number_format($data['nominal'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Total Dibayar</th>
                <td><?= "Rp " . number_format($data['total_dibayar'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Sisa Pembayaran</th>
                <td><?= "Rp " . number_format($sisa_pembayaran, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Petugas</th>
                <td><?= htmlspecialchars($data['nama_petugas']) ?></td>
            </tr>
            <tr>
                <th>Tanggal Bayar Terakhir</th>
                <td><?= htmlspecialchars($data['tgl_bayar']) ?></td>
            </tr>
        </table>

        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $kembali = 'history'; // default
        if (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] === 'petugas') {
            $kembali = 'history_petugas';
        }
        ?>
        <a href="?page=<?= $kembali ?>" class="btn btn-secondary">Kembali</a>

    </div>
</body>

</html>