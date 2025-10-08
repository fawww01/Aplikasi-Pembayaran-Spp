<?php
include 'koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "DELETE FROM siswa WHERE nisn=$id");
if($query) {
    echo '<script>alert("Data berhasil di hapus."); location.href="index_admin.php?page=siswa";</script>';
} else{
    echo '<script>alert("Data gagal di hapus."); location.href="index_admin.php?page=siswa";</script>';
}

?>