<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Jalankan query DELETE berdasarkan ID
    $query = "DELETE FROM pembayaran WHERE id_pembayaran = $id";

    if (mysqli_query($koneksi, $query)) {
        // Berhasil menghapus
        header("Location: index_admin.php?page=history&status=sukses");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    echo "Parameter ID tidak ditemukan.";
}
?>
