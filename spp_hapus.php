<?php
include 'koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "DELETE FROM spp WHERE id_spp=$id");
if($query) {
    echo '<script>alert("Data berhasil di hapus."); location.href="index_admin.php?page=spp";</script>';
} else{
    echo '<script>alert("Data gagal di hapus."); location.href="index_admin.php?page=spp";</script>';
}

?>