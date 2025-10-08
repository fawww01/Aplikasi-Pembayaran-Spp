<?php
include 'koneksi.php';
if(isset($_POST['tahun'])){

    $tahun = $_POST['tahun'];
    $nominal = $_POST['nominal'];

    $query = mysqli_query($koneksi, "INSERT INTO spp (tahun, nominal) values ('$tahun', '$nominal')");

    if($query) {
        echo '<script>alert("Tambah data berhasil"); window.location.href="index_admin.php?page=spp";</script>';
    } else {
        echo '<script>alert("Tambah data gagal"); ; window.location.href="index_admin.php?page=spp";</script>';

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
</head>
<body>
    
</body>
</html>

<h1 class="h3 mb-3"> Tambah Data SPP</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="?page=spp" class="btn btn-primary">Kembali</a>  
                <hr>

                <form method="post">
                    <table class="table">
                        <tr>
                            <td width="200">Tahun</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="number" name="tahun"></td>
                        </tr>
                        <tr>
                            <td width="200">Nominal</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="number" name="nominal"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><button class="btn btn-success" type="submit">Simpan</button></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
