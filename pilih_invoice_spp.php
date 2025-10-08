<?php
session_start();
include 'koneksi.php';

// Tangkap input pencarian (jika ada)
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// Query data pembayaran
$where = "";
if (!empty($keyword)) {
    $where = "WHERE siswa.nama LIKE '%$keyword%'";
}

$query = mysqli_query($koneksi, "
    SELECT pembayaran.id_pembayaran, siswa.nisn, siswa.nama, pembayaran.tgl_bayar
    FROM pembayaran
    LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn
    $where
    ORDER BY pembayaran.tgl_bayar DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Invoice Pembayaran SPP</title>
    <style>
        body {
            font-family: Arial;
            background: #f9f9f9;
        }
        .container {
            width: 85%;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px #ccc;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 300px;
            font-size: 16px;
        }
        input[type="submit"] {
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }
        a.button {
            background: #28a745;
            color: white;
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Daftar Pembayaran SPP</h2>

    <form method="GET" action="">
        <input type="text" name="keyword" placeholder="Cari nama siswa..." value="<?php echo htmlspecialchars($keyword); ?>">
        <input type="submit" value="Cari">
    </form>

    <table>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>Tanggal Bayar</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_array($query)) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $data['nisn'] ?></td>
            <td><?= $data['nama'] ?></td>
            <td><?= $data['tgl_bayar'] ?></td>
            <td>
                <a href="invoice_spp.php?id=<?= $data['id_pembayaran'] ?>" target="_blank" class="button">Cetak Invoice</a>
            </td>
        </tr>
        <?php }} else { ?>
        <tr>
            <td colspan="5">Data tidak ditemukan.</td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
