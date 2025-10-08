<?php
include "koneksi.php";

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_POST['id_petugas'])) {
    $id_petugas = $_POST['id_petugas'];
    $nisn = $_POST['nisn'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $id_spp = $_POST['id_spp'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $bulan_dibayar = $_POST['bulan_dibayar'];
    $tahun_dibayar = $_POST['tahun_dibayar'];

    // Ambil nominal SPP
    $spp_query = mysqli_query($koneksi, "SELECT nominal FROM spp WHERE id_spp = '$id_spp'");
    $spp_data = mysqli_fetch_assoc($spp_query);
    $nominal_spp = $spp_data['nominal'];

    // Hitung total pembayaran yang sudah dilakukan siswa untuk bulan dan tahun yang sama
    $cek_pembayaran = mysqli_query($koneksi, "
    SELECT SUM(jumlah_bayar) AS total_bayar
    FROM pembayaran
    WHERE nisn = '$nisn'
    AND bulan_dibayar = '$bulan_dibayar'
    AND tahun_dibayar = '$tahun_dibayar'
");
    $data_bayar = mysqli_fetch_assoc($cek_pembayaran);
    $total_sudah_dibayar = $data_bayar['total_bayar'] ?? 0;

    // Cek apakah pembayaran akan melebihi nominal SPP
    if (($total_sudah_dibayar + $jumlah_bayar) > $nominal_spp) {
        $maks = $nominal_spp - $total_sudah_dibayar;
        echo "<script>alert('Jumlah pembayaran melebihi nominal SPP! Maksimal: Rp " . number_format($maks, 0, ',', '.') . "'); window.history.back();</script>";
        exit;
    }

    // Insert data pembayaran
    $query = mysqli_query($koneksi, "INSERT INTO pembayaran (id_petugas, nisn, tgl_bayar, id_spp, jumlah_bayar, bulan_dibayar, tahun_dibayar) VALUES (
        '$id_petugas', '$nisn', '$tgl_bayar', '$id_spp', '$jumlah_bayar', '$bulan_dibayar', '$tahun_dibayar'
    )");

    if ($query) {
        // Ambil data petugas berdasarkan ID yang diinput
        $result_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE id_petugas = '$id_petugas'");
        if ($row = mysqli_fetch_assoc($result_petugas)) {
            $_SESSION['id_petugas'] = $row['id_petugas'];
            $_SESSION['nama_petugas'] = $row['nama_petugas'];
        }

        echo '<script>alert("Entri Pembayaran Berhasil"); window.location.href = "index_admin.php?page=history";</script>';
    } else {
        echo '<script>alert("Entri Pembayaran Gagal");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>

<body>

</body>

</html>

<h1 class="h3 mb-3"> Entri Data Pembayaran</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <table class="table">
                        <tr>
                            <td>Admin</td>
                            <td width="1">:</td>
                            <td>
                                <input class="form-control" type="text"
                                    value="<?php echo htmlspecialchars($_SESSION['user']['nama_petugas']); ?>" disabled>
                                <input type="hidden" name="id_petugas"
                                    value="<?php echo htmlspecialchars($_SESSION['user']['id_petugas']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Siswa</td>
                            <td width="1">:</td>
                            <td>
                                <select name="nisn" id="select-nisn" class="form-select">
                                    <?php
                                    $p = mysqli_query($koneksi, "SELECT * FROM siswa");
                                    while ($siswa = mysqli_fetch_array($p)) {
                                        ?>
                                        <option value="<?php echo htmlspecialchars($siswa['nisn']); ?>">
                                            <?php echo htmlspecialchars($siswa['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="200">Tanggal Bayar</td>
                            <td width="1">:</td>
                            <td>
                                <input class="form-control" type="date" name="tgl_bayar" required>
                            </td>
                        </tr>
                        <tr>
                            <td width="200">Bulan Dibayar</td>
                            <td width="1">:</td>
                            <td>
                                <select name="bulan_dibayar" class="form-select" required>
                                    <option value="">--Pilih Bulan--</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="200">Tahun Dibayar</td>
                            <td width="1">:</td>
                            <td>
                                <input class="form-control" type="number" name="tahun_dibayar" required min="2000"
                                    max="<?php echo date('Y') + 1; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>SPP</td>
                            <td width="1">:</td>
                            <td>
                                <select name="id_spp" class="form-control">
                                    <?php
                                    $p = mysqli_query($koneksi, "SELECT * FROM spp");
                                    while ($spp = mysqli_fetch_array($p)) {
                                        $nominal_rp = number_format($spp['nominal'], 0, ',', '.');
                                        echo "<option value='{$spp['id_spp']}'>Rp {$nominal_rp},-</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="200">Jumlah Bayar</td>
                            <td width="1">:</td>
                            <td>
                                <input class="form-control" type="number" name="jumlah_bayar" required min="1">
                            </td>
                        </tr>
                        <tr>
                            <td width="200"></td>
                            <td width="1"></td>
                            <td>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>