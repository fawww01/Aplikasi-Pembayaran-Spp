<?php
include 'koneksi.php';

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Pagination config
$batas = 5; // data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Ambil total data spp untuk pagination
$total_result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM spp");
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_halaman = ceil($total_data / $batas);

// Query data spp dengan limit
$query = mysqli_query($koneksi, "SELECT * FROM spp ORDER BY tahun DESC LIMIT $mulai, $batas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>

<div class="container mt-4">
    <h1 class="h3 mb-3">Data SPP</h1>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a href="?page=spp_tambah" class="btn btn-primary mb-3">+ Tambah Data</a>  
                    <hr>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tahun</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($query && mysqli_num_rows($query) > 0) {
                            $no = $mulai + 1;
                            while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($data['tahun']); ?></td>
                                <td><?= "Rp " . number_format($data['nominal'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="?page=spp_ubah&id=<?= $data['id_spp']; ?>" class="btn btn-warning btn-sm">Ubah</a>
                                    <a href="?page=spp_hapus&id=<?= $data['id_spp']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center">Tidak ada data SPP.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=spp&halaman=<?= $halaman - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Nomor halaman -->
                            <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                                <li class="page-item <?= ($x == $halaman) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=spp&halaman=<?= $x; ?>"><?= $x; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=spp&halaman=<?= $halaman + 1; ?>" aria-label="Next">
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
