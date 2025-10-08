<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Cek login siswa
$nisn_siswa = $_SESSION['siswa']['nisn'] ?? null;
if (!$nisn_siswa) {
    echo "Error: Anda belum login sebagai siswa.";
    exit;
}

// Ambil bulan & tahun dari GET jika tersedia
$selected_bulan = $_GET['bulan'] ?? '';
$selected_tahun = $_GET['tahun'] ?? '';

// Ambil tahun dari data pembayaran berdasarkan siswa yang login
$tahun_result = mysqli_query($koneksi, "
    SELECT DISTINCT tahun_dibayar 
    FROM pembayaran 
    WHERE nisn = '$nisn_siswa'
    ORDER BY tahun_dibayar DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Pembayaran Siswa</title>
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
        <h2>History Pembayaran SPP</h2>
    </header>

    <div class="container">
        <div class="form-section mb-4">
            <form method="GET" action="index_siswa.php">
                <input type="hidden" name="page" value="history_siswa">
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
                                $selected = ($selected_bulan == $label) ? 'selected' : '';
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
                                $selected = ($selected_tahun == $thn) ? 'selected' : '';
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

        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Petugas</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Tanggal Bayar</th>
                        <th>Bulan Dibayar</th>
                        <th>Tahun Dibayar</th>
                        <th>SPP</th>
                        <th>Jumlah Dibayar</th>
                        <th>Sisa Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total_dibayar_semua = 0;

                    $where = "WHERE pembayaran.nisn = '$nisn_siswa'";
                    if (!empty($selected_bulan)) {
                        $where .= " AND pembayaran.bulan_dibayar = '$selected_bulan'";
                    }
                    if (!empty($selected_tahun)) {
                        $where .= " AND pembayaran.tahun_dibayar = '$selected_tahun'";
                    }

                    $query = mysqli_query($koneksi, "
                        SELECT 
                            pembayaran.bulan_dibayar,
                            pembayaran.tahun_dibayar,
                            siswa.nisn,
                            siswa.nama,
                            spp.nominal,
                            SUM(pembayaran.jumlah_bayar) AS total_dibayar,
                            MAX(pembayaran.tgl_bayar) AS tgl_terakhir,
                            MAX(petugas.nama_petugas) AS nama_petugas
                        FROM pembayaran
                        LEFT JOIN petugas ON petugas.id_petugas = pembayaran.id_petugas 
                        LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn 
                        LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp 
                        $where
                        GROUP BY pembayaran.bulan_dibayar, pembayaran.tahun_dibayar
                        ORDER BY pembayaran.tahun_dibayar DESC, pembayaran.bulan_dibayar DESC
                    ");

                    if (mysqli_num_rows($query) === 0) {
                        echo '<tr><td colspan="11" class="text-center">Tidak ada data untuk filter yang dipilih.</td></tr>';
                    } else {
                        while ($data = mysqli_fetch_assoc($query)) {
                            $sisa = $data['nominal'] - $data['total_dibayar'];
                            if ($sisa < 0) $sisa = 0;
                            $status = ($sisa == 0) ? 'Lunas' : 'Belum Lunas';
                            $total_dibayar_semua += $data['total_dibayar'];
                    ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= htmlspecialchars($data['nama_petugas']); ?></td>
                            <td><?= htmlspecialchars($data['nisn']); ?></td>
                            <td><?= htmlspecialchars($data['nama']); ?></td>
                            <td><?= htmlspecialchars($data['tgl_terakhir']); ?></td>
                            <td><?= htmlspecialchars($data['bulan_dibayar']); ?></td>
                            <td><?= htmlspecialchars($data['tahun_dibayar']); ?></td>
                            <td><?= number_format($data['nominal']); ?></td>
                            <td><?= number_format($data['total_dibayar']); ?></td>
                            <td><?= number_format($sisa); ?></td>
                            <td>
                                <span class="badge bg-<?= $status == 'Lunas' ? 'success' : 'warning' ?>">
                                    <?= $status ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="text-end">Total Dibayar</th>
                        <th><?= number_format($total_dibayar_semua); ?></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
