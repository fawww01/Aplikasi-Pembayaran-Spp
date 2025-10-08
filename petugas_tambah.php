<?php
include 'koneksi.php';
if(isset($_POST['nama_petugas'])){

    $nama_petugas = $_POST['nama_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $level = $_POST['level'];

    $query = mysqli_query($koneksi, "INSERT INTO petugas (nama_petugas, username, password, level) values ('$nama_petugas', '$username', '$password', '$level')");

    if($query) {
        echo '<script>alert("Tambah data berhasil"); window.location.href="index_admin.php?page=petugas";</script>';
    } else {
        echo '<script>alert("Tambah data gagal"); window.location.href="index_admin.php?page=petugas";</script>';

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

<h1 class="h3 mb-3"> Tambah Data petugas</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="?page=petugas" class="btn btn-primary">Kembali</a>  
                <hr>

                <form method="post">
                    <table class="table">
                        <tr>
                            <td width="200">Nama Petugas</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="nama_petugas"></td>
                        </tr>
                        <tr>
                            <td width="200">Username</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="username"></td>
                        </tr>
                        <tr>
                            <td width="200">Password</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="password" name="password"></td>
                        </tr>
                        <tr>
                            <td width="200">Level</td>
                            <td width="1">:</td>
                            <td>
                                <select name="level" class="form-select">
                                    <option value="petugas">Petugas</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </td>
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
