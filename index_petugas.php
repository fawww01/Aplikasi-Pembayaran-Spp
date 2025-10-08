<?php
session_start();
include "koneksi.php";

// Mencegah akses kembali setelah logout dengan tombol Back
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user'])) {
	header('Location: login.php');
	exit;
}

$user = $_SESSION['user']; // Ambil data dari session

// Menentukan nama yang akan ditampilkan
$nama = isset($user['nama_petugas']) ? $user['nama_petugas'] : $user['nama_petugas'];

// Agar setelah logout tidak bisa tekan tombol "Back" di browser
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">

	<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />


	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/" />

	<title>Aplikasi Pembayaran SPP</title>

	<link href="css/app.css" rel="stylesheet">
	<link rel="stylesheet" href="css/style_Ai.css" />

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>

	<div class="chatbot-container" id="chatbot">
		<div class="chat-header">🤖 AI Chatbot</div>
			<div class="chat-messages" id="chat-messages">
			<div class="message bot">Halo! Saya chatbot AI. Silakan tanya apa saja 😊</div>
		</div>
			<div class="chat-input">
			<input type="text" id="user-input" placeholder="Tanyakan apa saja..." />
			<button onclick="sendMessage()">Kirim</button>
		</div>
  	</div>
	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="index.html">
					<span class="align-middle">Pembayaran SPP</span>
				</a>

				<ul class="sidebar-nav">
					<li class="sidebar-header">
						Pages
					</li>

					<li class="sidebar-item <?= ($_GET['page'] ?? '') == 'home_petugas' ? 'active' : '' ?>">
						<a class="sidebar-link" href="index_petugas.php?page=home_petugas">
							<i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-item <?= ($_GET['page'] ?? '') == 'pembayaran_petugas' ? 'active' : '' ?>">
						<a class="sidebar-link" href="index_petugas.php?page=pembayaran_petugas">
							<i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Transaksi Pembayaran</span>
						</a>
					</li>

					<li class="sidebar-item <?= ($_GET['page'] ?? '') == 'history_petugas' ? 'active' : '' ?>">
						<a class="sidebar-link" href="index_petugas.php?page=history_petugas">
							<i class="align-middle" data-feather="clock"></i> <span class="align-middle">History Pembayaran</span>
						</a>
					</li>

					<li class="sidebar-item <?= ($_GET['page'] ?? '') == 'laporan_petugas' ? 'active' : '' ?>">
						<a class="sidebar-link" href="index_petugas.php?page=laporan_petugas">
							<i class="align-middle" data-feather="printer"></i> <span class="align-middle">Generate Laporan</span>
						</a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="logout.php" onclick="return confirmLogout();">
							<i class="align-middle" data-feather="log-in"></i> <span class="align-middle">Logout</span>
						</a>
					</li>

				</ul>


			</div>
		</nav>

		<div class="main">
			<main class="content">
				<div class="container-fluid p-0">

					<?php
					$page = isset($_GET['page']) ? $_GET['page'] : 'home_petugas';
					include $page . '.php';

					?>

					
				</div>
			</main>


			</footer>
		</div>
	</div>

	<script src="js/app.js"></script>
	<script>
		function confirmLogout() {
			return confirm("Yakin ingin logout?");
		}
	</script>


</body>

</html>