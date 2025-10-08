<?php
session_start();
include 'koneksi.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Cek login sebagai petugas/admin
	$hashed_password_petugas = md5($password);
	$query_petugas = "SELECT * FROM petugas WHERE username = '$username' AND password = '$hashed_password_petugas'";
	$cek_petugas = mysqli_query($koneksi, $query_petugas);

	if (mysqli_num_rows($cek_petugas) > 0) {
		$data = mysqli_fetch_assoc($cek_petugas); // Ambil data user
		$_SESSION['user'] = $data; // simpan semua data ke dalam session

		if ($data['level'] === 'admin') {
			echo '<script>
			alert("Login berhasil sebagai admin!");
			window.location.href = "index_admin.php";
		</script>';
		} elseif ($data['level'] === 'petugas') {
			echo '<script>
			alert("Login berhasil sebagai petugas!");
			window.location.href = "index_petugas.php";
		</script>';
		} else {
			echo "Level tidak dikenali";
		}
		exit;
	}
	// Cek login sebagai siswa
	$hashed_password = md5($password);
	$query_siswa = "SELECT * FROM siswa WHERE nisn = '$username' AND password = '$hashed_password'";
	$cek_siswa = mysqli_query($koneksi, $query_siswa);

	if (mysqli_num_rows($cek_siswa) > 0) {
		$data_siswa = mysqli_fetch_assoc($cek_siswa);
		$_SESSION['siswa'] = $data_siswa;

		echo '<script>alert("Login berhasil sebagai siswa!"); window.location.href="index_siswa.php";</script>';
		exit;
	}

	// Jika semua gagal
	echo '<script>alert("Username atau Password salah!"); window.location.href="login.php";</script>';
}

?>
<?php
if (isset($_GET['alert']) && $_GET['alert'] === 'belum_login') {
	echo '<script>alert("Kamu belum login!");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords"
		content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">
	<!-- BOXICONS -->
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<!-- CSS -->
	<link rel="stylesheet" href="css/style.css">

	<title>Login</title>
</head>

<body>
	<div class="container">
		<h2 style="text-align: center; margin-bottom: 20px; color: #0D1936;">
			Aplikasi Pembayaran SPP
		</h2>
		<div class="wrapper" style="transform: scale(0.85); transform-origin: top center;">
			<div class="form-header">
				<div class="titles">
					<div class="title-login">Login</div>
				</div>
			</div>
			<form action="" method="POST" class="login-form" autocomplete="off">
				<div class="input-box">
					<input type="text" class="input-field" name="username" required>
					<label for="log-username" class="label">Username/NISN</label>
					<i class='bx bx-envelope icon'></i>
				</div>
				<div class="input-box" style="position: relative;">
					<input type="password" class="input-field" name="password" id="log-pass" required>
					<label for="log-pass" class="label">Password</label>


					<!-- Tombol mata di sebelah kanan input -->
					<button type="button" id="toggle-pass" onmousedown="showPassword()" onmouseup="hidePassword()"
						onmouseleave="hidePassword()" style="
			position: absolute;
			right: 24px;
			top: 50%;
			transform: translateY(-50%);
			background: none;
			border: none;
			cursor: pointer;
			font-size: 20px;
		">
						<i class='bx bx-show'></i>
					</button>
				</div>

				<div class="input-box">
					<button type="submit" class="btn-submit">Sign In <i class='bx bx-log-in'></i></button>
				</div>
			</form>
		</div>
	</div>

	<script src="js/script.js"></script>
	<script>
		function showPassword() {
			document.getElementById('log-pass').type = 'text';
		}

		function hidePassword() {
			document.getElementById('log-pass').type = 'password';
		}
	</script>

	<script>
		// Cegah kembali ke halaman sebelumnya
		history.pushState(null, null, location.href);
		window.onpopstate = function () {
			history.go(1);
		};
	</script>


</body>

</html>


<!--
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

	

	<link rel="stylesheet" href="css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<main class="wrapper">
		<div class="form-header">
			<div class="titles">
				<div class="title-login">Login</div>
							<h1 class="h2">Aplikasi Pembayaran SPP</h1>
							
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-3">
									<form method="post">
										<div class="mb-3">
											<label class="form-label">Username/NISN</label>
											<input class="form-control form-control-lg" type="text" name="username" placeholder="Masukkan Username/NISN" />
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Masukan Password" />
										</div>
										<div>
										</div>
										<div class="text-center mt-3">
											<button type="submit" class="btn btn-lg btn-primary">Sign in</a>
										</div>
									</form>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</main>
	<script src="js/app.js"></script>

</body>

</html>
-->