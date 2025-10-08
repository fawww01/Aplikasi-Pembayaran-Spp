<?php
include 'koneksi.php';
$id = $_GET['id'];
if(isset($_POST['nama_kelas'])){

    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi_keahlian = $_POST['kompetensi_keahlian'];

    $query = mysqli_query($koneksi, "UPDATE kelas SET nama_kelas='$nama_kelas', kompetensi_keahlian='$kompetensi_keahlian' WHERE id_kelas=$id");

    if($query) {
        echo '<script>alert("Ubah data berhasil"); window.location.href="index_admin.php?page=kelas";</script>';
    } else {
        echo '<script>alert("Ubah data gagal"); window.location.href="index_admin.php?page=kelas";</script>';

    }
}
$query = mysqli_query($koneksi, "SELECT*FROM kelas WHERE id_kelas=$id");
$data = mysqli_fetch_array($query)

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

<h1 class="h3 mb-3"> Edit Data Kelas</h1>

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
                            <td><input class="form-control" type="text" name="nama_kelas" value="<?php echo $data['nama_kelas']; ?>"></td>
                        </tr>
                        <tr>
                            <td width="200">Kompetensi Keahlian</td>
                            <td width="1">:</td>
                            <td><input class="form-control" type="text" value="<?php echo $data['kompetensi_keahlian']; ?>" name="kompetensi_keahlian"></td>
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
