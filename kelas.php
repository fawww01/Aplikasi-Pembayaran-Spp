<?php
include 'koneksi.php';

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Konfigurasi pagination
$batas = 5; // jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Ambil total data kelas
$result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kelas");
$total_data = mysqli_fetch_assoc($result)['total'];
$total_halaman = ceil($total_data / $batas);

// Ambil data kelas untuk halaman sekarang
$query = mysqli_query($koneksi, "SELECT * FROM kelas LIMIT $mulai, $batas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>

<div class="container mt-4">
    <h1 class="h3 mb-3">Data Kelas</h1>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a href="?page=kelas_tambah" class="btn btn-primary mb-3">+ Tambah Data</a>
                    <hr>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kelas</th>
                                <th>Kompetensi Keahlian</th>
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
                                    <td><?= htmlspecialchars($data['nama_kelas']); ?></td>
                                    <td><?= htmlspecialchars($data['kompetensi_keahlian']); ?></td>
                                    <td>
                                        <a href="?page=kelas_ubah&id=<?= $data['id_kelas']; ?>" class="btn btn-warning btn-sm">Ubah</a>
                                        <a href="?page=kelas_hapus&id=<?= $data['id_kelas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center">Tidak ada data kelas.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=kelas&halaman=<?= $halaman - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                                <li class="page-item <?= ($x == $halaman) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=kelas&halaman=<?= $x; ?>"><?= $x; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=kelas&halaman=<?= $halaman + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <!-- End Pagination -->

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
