<?php
session_start();
include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');
$tanggalCetak = date("d-m-Y H:i:s");

$id = $_GET['id'];

$query = mysqli_query($koneksi, "
    SELECT * FROM pembayaran 
    LEFT JOIN petugas ON petugas.id_petugas = pembayaran.id_petugas 
    LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn 
    LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp 
    WHERE pembayaran.id_pembayaran = '$id'
");

$data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembayaran SPP</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 20px;
        }

        .invoice-box {
            max-width: 700px;
            margin: auto;
            background: white;
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 30px 40px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            color: rgb(16, 73, 134);
        }

        .school-name {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .tanggal-cetak {
            text-align: center;
            font-size: 13px;
            color: #888;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            line-height: inherit;
            border-collapse: collapse;
        }

        table td, table th {
            padding: 10px;
            border: 1px solid #e0e0e0;
        }

        table th {
            background-color:rgb(16, 73, 134);
            color: white;
            text-align: left;
        }

        .thank-you {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
            color: #555;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 50px;
            color: #aaa;
        }
    </style>

    
</head>
<body>
    <div class="invoice-box">
        <h2>INVOICE PEMBAYARAN SPP</h2>
        <div class="school-name">SMK NEGERI 7 BALEENDAH</div>
        <div class="tanggal-cetak">Dicetak pada: <?= $tanggalCetak; ?> WIB</div>

        <table>
            <tr>
                <th>NISN</th>
                <td><?= $data['nisn']; ?></td>
            </tr>
            <tr>
                <th>Nama Siswa</th>
                <td><?= $data['nama']; ?></td>
            </tr>
            <tr>
                <th>Petugas</th>
                <td><?= $data['nama_petugas']; ?></td>
            </tr>
            <tr>
                <th>Tanggal Bayar</th>
                <td><?= $data['tgl_bayar']; ?></td>
            </tr>
            <tr>
                <th>Bulan & Tahun Dibayar</th>
                <td><?= $data['bulan_dibayar'] . " / " . $data['tahun_dibayar']; ?></td>
            </tr>
            <tr>
                <th>Nominal SPP</th>
                <td>Rp <?= number_format($data['nominal'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Jumlah Bayar</th>
                <td>Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="thank-you">
            “Terima kasih sudah melakukan pembayaran tepat waktu.”
        </div>

        <div class="footer">
            Dicetak otomatis oleh sistem pembayaran SPP
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
