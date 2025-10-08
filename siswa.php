<?php
include 'koneksi.php';

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Konfigurasi pagination
$batas = 5; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Ambil jumlah total data
$result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM siswa");
$total_data = mysqli_fetch_assoc($result)['total'];
$total_halaman = ceil($total_data / $batas);

// Ambil data untuk halaman sekarang
$query = mysqli_query($koneksi, "
    SELECT * FROM siswa 
    LEFT JOIN kelas ON siswa.id_kelas = kelas.id_kelas 
    LIMIT $mulai, $batas
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
    
</body>
</html>

<h1 class="h3 mb-3">Data Siswa</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="?page=siswa_tambah" class="btn btn-primary mb-3">+ Tambah Data</a>  
                <hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Alamat</th>
                            <th>No Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = $mulai + 1;
                        if ($query && mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_array($query)) {
                        ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $data['nisn']; ?></td>
                                    <td><?= $data['nis']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td><?= $data['nama_kelas']; ?></td>
                                    <td><?= $data['alamat']; ?></td>
                                    <td><?= $data['no_telp']; ?></td>
                                    <td>
                                        <a href="?page=siswa_ubah&id=<?= $data['nisn']; ?>" class="btn btn-warning btn-sm">Ubah</a>
                                        <a href="?page=siswa_hapus&id=<?= $data['nisn']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center">Tidak ada data siswa.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Navigasi Pagination -->
                <!-- Navigasi Pagination (Kece) -->
<nav>
    <ul class="pagination justify-content-center">
        <!-- Tombol Previous -->
        <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=siswa&halaman=<?= $halaman - 1; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <!-- Nomor halaman -->
        <?php for ($x = 1; $x <= $total_halaman; $x++) : ?>
            <li class="page-item <?= ($x == $halaman) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=siswa&halaman=<?= $x; ?>"><?= $x; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=siswa&halaman=<?= $halaman + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>


            </div>
        </div>
    </div>
</div>
