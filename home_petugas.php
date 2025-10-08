<?php

date_default_timezone_set('Asia/Jakarta');


// Anti cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect kalau belum login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<head>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>

<h1 class="h3 mb-3">Dashboard</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"> Selamat datang di Aplikasi Pembayaran SPP</h5>
            </div>
            <div class="card-body">
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td width="200">Nama</td>
                        <td width="1">:</td>
                        <td><?php echo $_SESSION['user']['nama_petugas']; ?></td>
                    </tr>
                    <tr>
                        <td width="200">level User</td>
                        <td width="1">:</td>
                        <td><?php echo $_SESSION['user']['level']; ?></td>
                    </tr>
                    <tr>
                        <td width="200">Tanggal Login</td>
                        <td width="1">:</td>
                        <td><?php echo date('d-m-Y | H:i:s'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background: #f0f2f5;
            margin: 0;
        }

        .container {
            display: flex;
            gap: 20px;
            justify-content: left;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        .box {
            width: 280px;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 18px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .box:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .label {
            font-size: 16px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .box.blue {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }

        .box.yellow {
            background: linear-gradient(135deg, #FFC107, #FFA000);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Box untuk Jumlah Transaksi Hari Ini -->
        <div class="box blue">
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <div class="number" id="jumlahTransaksi">Loading...</div>
            <div class="label">Transaksi Hari Ini</div>
        </div>
        <!-- Box untuk Pembayaran Bulan Ini -->
        <div class="box yellow">
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <div class="number" id="totalPembayaran">Loading...</div>
            <div class="label">Total Pembayaran Bulan Ini</div>
        </div>
    </div>

    <script>
        fetch("get_dashboard_data_petugas.php")
            .then(res => res.json())
            .then(data => {
                document.getElementById("totalPembayaran").innerText = data.pembayaran;
                document.getElementById("jumlahTransaksi").innerText = data.transaksiHariIni;
            })
            .catch(err => {
                console.error("Gagal memuat data dashboard", err);
            });
    </script>
</body>

</html>