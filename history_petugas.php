<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Anti cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Pagination setup
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Filter data sesuai petugas login (hapus filter bulan/tahun)
$where = "WHERE 1=1"; // supaya mudah concat AND

if (isset($_SESSION['user']) && $_SESSION['user']['level'] === 'petugas') {
    $id_petugas = (int) $_SESSION['user']['id_petugas'];
    $where .= " AND pembayaran.id_petugas = $id_petugas";
}

// Hitung total data
$total_result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pembayaran $where");
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_halaman = ceil($total_data / $batas);

// Ambil data pembayaran semua tanpa filter bulan/tahun
$query = mysqli_query($koneksi, "
    SELECT 
        pembayaran.nisn,
        siswa.nama,
        spp.nominal,
        pembayaran.bulan_dibayar,
        pembayaran.tahun_dibayar,
        MAX(pembayaran.tgl_bayar) AS tgl_bayar,
        MAX(petugas.nama_petugas) AS nama_petugas,
        SUM(pembayaran.jumlah_bayar) AS total_bayar
    FROM pembayaran
    LEFT JOIN siswa ON siswa.nisn = pembayaran.nisn
    LEFT JOIN spp ON spp.id_spp = pembayaran.id_spp
    LEFT JOIN petugas ON petugas.id_petugas = pembayaran.id_petugas
    $where
    GROUP BY pembayaran.nisn, pembayaran.bulan_dibayar, pembayaran.tahun_dibayar
    ORDER BY pembayaran.tahun_dibayar DESC, pembayaran.bulan_dibayar DESC
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

<body>
    <!-- Pindahkan container pencarian ke sini -->
    <div style="position: relative; max-width: 300px; margin-left: auto; margin-bottom: 1rem;">
        <input type="text" id="search" placeholder="Cari nama..." autocomplete="off" style="
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
    </div>
</body>

</head>

<body>

</body>

</html>

<h1 class="h3 mb-3">Data Pembayaran</h1>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- Form Pilih Periode -->

                <!-- Tabel History -->
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
                            <th>Sisa Pembayaran</th> <!-- Tambahan kolom -->
                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['level'] === 'petugas'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>



                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $i = $mulai + 1;
                        if ($query && mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                                $sisa_pembayaran = $data['nominal'] - $data['total_bayar'];
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
                                    <td><?= "Rp " . number_format($data['total_bayar'], 0, ',', '.'); ?></td>
                                    <td><?= "Rp " . number_format($sisa_pembayaran, 0, ',', '.'); ?></td>

                                    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['level'], ['admin', 'petugas'])): ?>
                                        <td>
                                            <a href="?page=history_detail&nisn=<?= $data['nisn']; ?>&bulan=<?= $data['bulan_dibayar']; ?>&tahun=<?= $data['tahun_dibayar']; ?>"
                                                class="btn btn-sm btn-info ms-1">Detail</a>
                                        </td>
                                    <?php endif; ?>

                                </tr>

                                <?php
                            }
                        } else {
                            $colspan = isset($_SESSION['user']) && $_SESSION['user']['level'] === 'admin' ? 11 : 10;
                            echo "<tr><td colspan='$colspan' class='text-center'>Tidak ada data pembayaran.</td></tr>";
                        }

                        ?>
                    </tbody>

                </table>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const input = document.getElementById("search");
                        const suggestionsContainer = document.getElementById("suggestions");
                        const tableRows = document.querySelectorAll("table tbody tr");

                        input.addEventListener("input", () => {
                            const query = input.value.toLowerCase().trim();
                            suggestionsContainer.innerHTML = "";

                            if (query === "") {
                                suggestionsContainer.style.display = "none";
                                filterTable("");
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

            </div>
        </div>
    </div>
</div>