<?php
include 'koneksi.php';
if(isset($_POST['nama'])){

    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi, "INSERT INTO siswa (nisn,nis,nama,id_kelas,alamat,no_telp,password) values ('$nisn','$nis','$nama','$id_kelas','$alamat','$no_telp','$password')");

    if($query) {
    echo '<script>alert("Tambah data berhasil"); window.location.href="index_admin.php?page=siswa";</script>';
    exit;
} else {
    echo '<script>alert("Tambah data gagal"); window.location.href="index_admin.php?page=siswa";</script>';
    exit;
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

<h1 class="h3 mb-3"> Tambah Data Siswa</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="?page=siswa" class="btn btn-primary">Kembali</a>  
                <hr>

                <form method="post" action="">
                    <table class="table">
                        <tr>
                            <td width="200">NISN</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="number" name="nisn" required></td>
                        </tr>
                        <tr>
                            <td width="200">NIS</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="number" name="nis" required></td>
                        </tr>
                        <tr>
                            <td width="200">Nama Siswa</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="nama" required></td>
                        </tr>
                        <tr>
                            <td width="200">Kelas</td>
                            <td width="1">:</td>
                            <td>
                                <select class="form-control" name="id_kelas">
                                    <?php
                                        $kel = mysqli_query($koneksi,"SELECT*FROM kelas");
                                        while ($kelas = mysqli_fetch_array($kel)) {
                                            
                                    ?>
                                    <option value="<?php echo $kelas['id_kelas']; ?>"><?php echo $kelas['nama_kelas']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="200">Alamat</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="alamat" required></td>
                        </tr>
                        <tr>
                            <td width="200">No. Telepon</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" name="no_telp" required></td>
                        </tr>
                        <tr>
                            <td width="200">Password</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="password" name="password" required></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><button class="btn btn-success" type="submit" name="submit">Simpan</button></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
