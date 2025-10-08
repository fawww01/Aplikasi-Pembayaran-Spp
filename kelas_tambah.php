<?php
include 'koneksi.php';
if(isset($_POST['nama_kelas'])){

    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi_keahlian = $_POST['kompetensi_keahlian'];

    $query = mysqli_query($koneksi, "INSERT INTO kelas (nama_kelas, kompetensi_keahlian) values ('$nama_kelas', '$kompetensi_keahlian')");

    if($query) {
        echo '<script>alert("Tambah data berhasil"); window.location.href="index_admin.php?page=kelas";</script>';
    } else {
        echo '<script>alert("Tambah data gagal"); window.location.href="index_admin.php?page=kelas";</script>';

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

<h1 class="h3 mb-3"> Tambah Data Kelas</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="?page=kelas" class="btn btn-primary">Kembali</a>  
                <hr>

                <form method="post">
                    <table class="table">
                        <tr>
                            <td width="200">Nama Kelas</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="nama_kelas"></td>
                        </tr>
                        <tr>
                            <td width="200">Kompetensi Keahlian</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="kompetensi_keahlian"></td>
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
