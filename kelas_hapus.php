<?php
include 'koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "DELETE FROM kelas WHERE id_kelas=$id");
if($query) {
    echo '<script>alert("Data berhasil di hapus."); location.href="index_admin.php?page=kelas";</script>';
} else{
    echo '<script>alert("Data gagal di hapus."); location.href="index_admin.php?page=kelas";</script>';
}

?>