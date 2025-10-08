<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Cek apakah user adalah admin
if (!isset($_SESSION['user']) || $_SESSION['user']['level'] !== 'admin') {
    die("Akses ditolak. Halaman ini hanya dapat diakses oleh admin.");
}

$id_petugas_login = $_SESSION['user']['id_petugas'];

$selected_bulan = $_GET['bulan'] ?? '';
$selected_tahun = $_GET['tahun'] ?? '';

// Filter berdasarkan bulan & tahun
$where = "WHERE pembayaran.id_petugas = '$id_petugas_login'";
if (!empty($selected_bulan)) {
    $where .= " AND pembayaran.bulan_dibayar = '$selected_bulan'";
}
if (!empty($selected_tahun)) {
    $where .= " AND pembayaran.tahun_dibayar = '$selected_tahun'";
}

// Ambil tahun unik
$tahun_result = mysqli_query($koneksi, "
    SELECT DISTINCT tahun_dibayar 
    FROM pembayaran 
    WHERE id_petugas = '$id_petugas_login'
    ORDER BY tahun_dibayar DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        header {
            padding: 20px 0;
            text-align: left;
        }
        .form-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        table {
            margin-top: 30px;
            background-color: white;
        }
        th {
            background-color: #eaeaea;
        }
        .table-bordered th, .table-bordered td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-cetak {
            background-color: gold;
            color: black;
            font-weight: bold;
            border-radius: 4px;
        }
        @media print {
            .btn, form, header {
                display: none;
            }
        }
    </style>
</head>
<body>

    <header>
        <h2>Laporan Pembayaran SPP</h2>
    </header>

    <div class="container">
        <div class="form-section mb-4">
            <form method="GET" action="index_admin.php">
                <input type="hidden" name="page" value="laporan">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            <?php
                            $nama_bulan = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                            foreach ($nama_bulan as $val => $label) {
                                $selected = ($label === $selected_bulan) ? 'selected' : '';
                                echo "<option value='$label' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            <?php while ($tahun = mysqli_fetch_assoc($tahun_result)): ?>
                                <?php
                                $thn = $tahun['tahun_dibayar'];
                                $selected = ($thn == $selected_tahun) ? 'selected' : '';
                                ?>
                                <option value="<?= $thn ?>" <?= $selected ?>><?= $thn ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Laporan -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Admin</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Tanggal Bayar</th>
                        <th>Bulan Dibayar</th>
                        <th>Tahun Dibayar</th>
                        <th>SPP</th>
                        <th>Jumlah Dibayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total = 0;
                    $query = mysqli_query($koneksi, "
                        SELECT * FROM pembayaran 
                        LEFT JOIN petugas ON petugas.id_petugas = pembayaran.id_petugas 
                        LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn 
                        LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp 
                        $where
                        ORDER BY pembayaran.tgl_bayar DESC
                    ");

                    if (mysqli_num_rows($query) > 0) {
                        while ($data = mysqli_fetch_array($query)) {
                            $total += $data['jumlah_bayar'];
                    ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($data['nama_petugas']); ?></td>
                                <td><?= $data['nisn']; ?></td>
                                <td><?= htmlspecialchars($data['nama']); ?></td>
                                <td><?= $data['tgl_bayar']; ?></td>
                                <td><?= $data['bulan_dibayar']; ?></td>
                                <td><?= $data['tahun_dibayar']; ?></td>
                                <td>Rp <?= number_format($data['nominal'], 0, ',', '.'); ?></td>
                                <td>Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="invoice_spp.php?id=<?= $data['id_pembayaran']; ?>" target="_blank"
                                        class="btn btn-warning btn-sm">Cetak</a>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        echo "<tr><td colspan='10' class='text-center'>Tidak ada data pembayaran untuk bulan dan/atau tahun yang dipilih.</td></tr>";
                    }

                    if ($total > 0): ?>
                        <tr class="table-primary fw-bold">
                            <td colspan="8" class="text-end">Total Jumlah Dibayar:</td>
                            <td colspan="2">Rp <?= number_format($total, 0, ',', '.'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
