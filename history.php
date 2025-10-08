<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Konfigurasi pagination dan pencarian
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$is_searching = isset($_GET['cari']) && !empty(trim($_GET['cari']));
$search_nama = $is_searching ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';


// Ambil bulan & tahun dari GET atau default sekarang
$selected_bulan = $_GET['bulan'] ?? '';
$selected_tahun = $_GET['tahun'] ?? '';


// Escape input agar aman
$selected_bulan = mysqli_real_escape_string($koneksi, $selected_bulan);
$selected_tahun = mysqli_real_escape_string($koneksi, $selected_tahun);

// Ambil daftar tahun unik dari pembayaran
$tahun_result = mysqli_query($koneksi, "SELECT DISTINCT tahun_dibayar FROM pembayaran ORDER BY tahun_dibayar DESC");

// Mulai buat kondisi WHERE filter sesuai bulan dan tahun
$where = "WHERE 1=1"; // supaya gampang nambah kondisi lain

if (!empty($selected_bulan) && !empty($selected_tahun)) {
    $selected_bulan = mysqli_real_escape_string($koneksi, $selected_bulan);
    $selected_tahun = mysqli_real_escape_string($koneksi, $selected_tahun);
    $where .= " AND MONTH(pembayaran.tgl_bayar) = '$selected_bulan' AND YEAR(pembayaran.tgl_bayar) = '$selected_tahun'";
}


// Filter berdasarkan user login
if (isset($_SESSION['user'])) {
    $level = $_SESSION['user']['level'];
    if ($level === 'siswa') {
        $nisn = mysqli_real_escape_string($koneksi, $_SESSION['user']['nisn']);
        $where .= " AND pembayaran.nisn = '$nisn'";
    } elseif (in_array($level, ['petugas', 'admin'])) {
        $id_petugas = (int) $_SESSION['user']['id_petugas'];
        $where .= " AND pembayaran.id_petugas = $id_petugas";
    }
}

// Filter pencarian nama siswa jika sedang mencari
if ($is_searching) {
    $where .= " AND siswa.nama LIKE '%$search_nama%'";
}

// Hitung total data sesuai filter
if ($is_searching) {
    $total_halaman = 1; // Tidak perlu pagination saat cari
    $limit_clause = ""; // Jangan batasi hasil
} else {
    $total_result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pembayaran $where");
    $total_data = mysqli_fetch_assoc($total_result)['total'];
    $total_halaman = ceil($total_data / $batas);
    $limit_clause = $is_searching ? "" : "LIMIT $mulai, $batas";
}




// Ambil data dengan limit untuk pagination
// Query diubah agar total pembayaran disatukan per siswa, bulan, tahun
$is_searching = isset($_GET['cari']) && !empty(trim($_GET['cari']));


$query = mysqli_query($koneksi, "
    SELECT 
        pembayaran.nisn,
        siswa.nama,
        pembayaran.bulan_dibayar,
        pembayaran.tahun_dibayar,
        spp.nominal,
        petugas.nama_petugas,
        MAX(pembayaran.tgl_bayar) AS tgl_bayar,
        SUM(pembayaran.jumlah_bayar) AS total_dibayar
    FROM pembayaran
    LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn
    LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp
    LEFT JOIN petugas ON petugas.id_petugas = pembayaran.id_petugas
    $where
    GROUP BY pembayaran.nisn, pembayaran.bulan_dibayar, pembayaran.tahun_dibayar
    ORDER BY pembayaran.tahun_dibayar DESC, pembayaran.bulan_dibayar DESC
    $limit_clause
");


?>

<?php
// Ambil semua nama siswa dari database
$result_nama = mysqli_query($koneksi, "SELECT nama FROM siswa ORDER BY nama ASC");

$nama_siswa = [];
while ($row = mysqli_fetch_assoc($result_nama)) {
    $nama_siswa[] = $row['nama'];
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>History Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />



<body>
    <!-- Pindahkan container pencarian ke sini -->
    <form method="GET" action="" class="mb-3" style="position: relative; max-width: 300px; margin-left: auto;">
        <input type="hidden" name="page" value="history">
        <input type="hidden" name="bulan" value="<?= $selected_bulan ?>">
        <input type="hidden" name="tahun" value="<?= $selected_tahun ?>">
        <input type="text" name="cari" id="search" placeholder="Cari nama..."
            value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" autocomplete="off" style="
        width: 100%;
        padding: 8px 15px;
        border-radius: 25px;
        border: 1px solid #ccc;
        font-size: 16px;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    ">
        <div id="suggestions" class="autocomplete-suggestions" style="
        border: 1px solid #ccc;
        max-height: 180px;
        overflow-y: auto;
        background: white;
        border-radius: 10px;
        position: absolute;
        width: 100%;
        top: 40px;
        left: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
    "></div>
    </form>

    </div>
</body>



</head>

<body>


    <?php
    // Ambil semua nama siswa dari hasil query pembayaran
    $nama_siswa = [];
    if ($query && mysqli_num_rows($query) > 0) {
        mysqli_data_seek($query, 0); // Reset pointer hasil query
        while ($row = mysqli_fetch_assoc($query)) {
            $nama_siswa[] = $row['nama'];
        }
        // Reset pointer lagi sebelum looping utama tabel
        mysqli_data_seek($query, 0);
    }
    ?>

    <style>
        input {
            font-family: 'Times New Roman', Times, serif;
            height: 30px;
            width: 20x;
            border-radius: 50px;
            position: absolute;
            right: 10px;
        }

        .autocomplete-suggestion {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background 0.2s;
        }

        #search {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 40px;
            border-radius: 25px;
            padding: 0 20px;
            font-size: 16px;
        }
    </style>
    </head>

    <body>

        <div class="container mt-4">
            <h1 class="h3 mb-3">History Pembayaran</h1>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">


                            <!-- Tabel Data -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Petugas</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Bulan Dibayar</th>
                                        <th>Tahun Dibayar</th>
                                        <th>SPP</th>
                                        <th>Jumlah Dibayar</th>
                                        <th>Sisa Pembayaran</th>
                                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['level'] === 'admin'): ?>
                                            <th>Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = $is_searching ? 1 : ($mulai + 1);
                                    if ($query && mysqli_num_rows($query) > 0) {
                                        while ($data = mysqli_fetch_assoc($query)) {
                                            // Hitung sisa pembayaran
                                            $sisa_pembayaran = $data['nominal'] - $data['total_dibayar'];
                                            if ($sisa_pembayaran < 0)
                                                $sisa_pembayaran = 0;
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= htmlspecialchars($data['nama_petugas']); ?></td>
                                                <td><?= htmlspecialchars($data['nisn']); ?></td>
                                                <td><?= htmlspecialchars($data['nama']); ?></td>
                                                <td><?= htmlspecialchars($data['tgl_bayar']); ?></td>
                                                <td><?= htmlspecialchars($data['bulan_dibayar']); ?></td>
                                                <td><?= htmlspecialchars($data['tahun_dibayar']); ?></td>
                                                <td><?= "Rp " . number_format($data['nominal'], 0, ',', '.'); ?></td>
                                                <td><?= "Rp " . number_format($data['total_dibayar'], 0, ',', '.'); ?></td>
                                                <td><?= "Rp " . number_format($sisa_pembayaran, 0, ',', '.'); ?></td>
                                                <?php if (isset($_SESSION['user']) && $_SESSION['user']['level'] === 'admin'): ?>
                                                    <td>
                                                        <!-- Jika ingin hapus semua pembayaran siswa bulan ini, kamu harus sesuaikan link dan logikanya -->
                                                        <a href="?page=history_detail&nisn=<?= $data['nisn']; ?>&bulan=<?= $data['bulan_dibayar']; ?>&tahun=<?= $data['tahun_dibayar']; ?>"
                                                            class="btn btn-sm btn-info ms-1">Detail</a>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="11" class="text-center">Tidak ada data pembayaran.</td></tr>';
                                    }

                                    ?>
                                </tbody>
                            </table>


                            <!-- Pagination -->
                            <?php if (!$is_searching): ?>
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous -->
                                        <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=history&bulan=<?= $selected_bulan ?>&tahun=<?= $selected_tahun ?>&halaman=<?= $halaman - 1; ?>"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        <!-- Nomor halaman -->
                                        <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                                            <li class="page-item <?= ($x == $halaman) ? 'active' : ''; ?>">
                                                <a class="page-link"
                                                    href="?page=history&bulan=<?= $selected_bulan ?>&tahun=<?= $selected_tahun ?>&halaman=<?= $x; ?>"><?= $x; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- Next -->
                                        <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=history&bulan=<?= $selected_bulan ?>&tahun=<?= $selected_tahun ?>&halaman=<?= $halaman + 1; ?>"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>



                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const input = document.getElementById("search");
                const suggestionsContainer = document.getElementById("suggestions");
                const tableRows = document.querySelectorAll("table tbody tr");

                input.addEventListener("input", () => {
                    const query = input.value.toLowerCase().trim();
                    suggestionsContainer.innerHTML = "";

                    // Sembunyikan pagination saat sedang mencari
                    if (query !== "") {
                        document.querySelector(".pagination")?.classList.add("d-none");
                    }

                    // Jika input kosong, reload halaman agar tampilkan semua data & pagination
                    if (query === "") {
                        suggestionsContainer.style.display = "none";
                        window.location.href = "?page=history&bulan=<?= $selected_bulan ?>&tahun=<?= $selected_tahun ?>";
                        return;
                    }

                    const matchedNames = Array.from(tableRows)
                        .map(row => row.cells[3]?.textContent.trim())
                        .filter(name => name && name.toLowerCase().includes(query));

                    const uniqueNames = [...new Set(matchedNames)];

                    if (uniqueNames.length > 0) {
                        uniqueNames.forEach(name => {
                            const div = document.createElement("div");
                            div.classList.add("autocomplete-suggestion");
                            div.textContent = name;
                            div.style.padding = "8px 15px";
                            div.style.cursor = "pointer";
                            div.style.borderBottom = "1px solid #eee";
                            div.addEventListener("mouseover", () => div.style.background = "#f0f0f0");
                            div.addEventListener("mouseout", () => div.style.background = "white");

                            div.addEventListener("click", () => {
                                input.value = name;
                                suggestionsContainer.innerHTML = "";
                                suggestionsContainer.style.display = "none";
                                filterTable(name.toLowerCase());
                            });

                            suggestionsContainer.appendChild(div);
                        });
                        suggestionsContainer.style.display = "block";
                    } else {
                        suggestionsContainer.style.display = "none";
                    }

                    filterTable(query);
                });



                function filterTable(searchValue) {
                    tableRows.forEach(row => {
                        const namaCell = row.cells[3]; // kolom ke-4 adalah Nama Siswa
                        if (namaCell) {
                            const namaText = namaCell.textContent.toLowerCase();
                            if (namaText.includes(searchValue)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                }
            });
        </script>


    </body>

</html>