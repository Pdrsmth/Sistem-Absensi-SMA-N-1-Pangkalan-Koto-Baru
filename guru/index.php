<?php
@session_start();
if (function_exists('opcache_reset')) {
    @opcache_reset();
}
 include '../config/db.php';

if (!isset($_SESSION['guru'])) {
?> <script>
    alert('Maaf ! Anda Belum Login !!');
    window.location='../index.php';
 </script>
<?php
}
 ?>


   <?php
$id_login = @$_SESSION['guru'];
$sql = mysqli_query($con,"SELECT * FROM tb_guru
 WHERE id_guru = '$id_login'") or die(mysqli_error($con));
$data = mysqli_fetch_array($sql);

// tampilkan data mengajar
$mengajar = mysqli_query($con,"SELECT * FROM tb_mengajar 

INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel=tb_master_mapel.id_mapel
INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas=tb_mkelas.id_mkelas

INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
WHERE tb_mengajar.id_guru='$data[id_guru]' AND tb_thajaran.status=1 ");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Guru | Aplikasi Presensi</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../assets/img/icon.ico" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="../assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900", "Outfit:300,400,500,600,700,800"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['../assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/css/atlantis.min.css">

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="../assets/css/demo.css">
	
	<!-- Premium Custom Sidebar Styling -->
	<style>
		.sidebar {
			background: linear-gradient(180deg, #111e38 0%, #080f1d 100%) !important;
			box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15) !important;
			border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
		}
		
		.sidebar .sidebar-wrapper {
			background: transparent !important;
		}

		/* User Profile Panel inside Sidebar - only when expanded */
		body:not(.sidebar_minimize) .sidebar .user {
			border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
			padding: 15px !important;
			margin-bottom: 15px !important;
			display: flex !important;
			align-items: center !important;
			background: rgba(255, 255, 255, 0.02) !important;
			border-radius: 12px !important;
			margin: 15px 15px 20px 15px !important;
			backdrop-filter: blur(10px) !important;
			border: 1px solid rgba(255, 255, 255, 0.04) !important;
		}
		
		body:not(.sidebar_minimize) .sidebar .user .avatar-sm {
			width: 40px !important;
			height: 40px !important;
			border: 2px solid #1976d2 !important;
			box-shadow: 0 0 12px rgba(25, 118, 210, 0.4) !important;
			border-radius: 50% !important;
			overflow: hidden !important;
			transition: transform 0.3s ease !important;
			float: none !important;
			margin-right: 12px !important;
		}

		body:not(.sidebar_minimize) .sidebar .user:hover .avatar-sm {
			transform: scale(1.08) rotate(5deg) !important;
		}

		body:not(.sidebar_minimize) .sidebar .user .info a {
			color: #ffffff !important;
			font-weight: 700 !important;
			font-size: 13.5px !important;
			font-family: 'Outfit', sans-serif !important;
			text-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
			text-decoration: none !important;
		}

		body:not(.sidebar_minimize) .sidebar .user .info .user-level {
			color: #90caf9 !important;
			font-size: 11px !important;
			font-weight: 600 !important;
			margin-top: 2px !important;
			letter-spacing: 0.5px !important;
			text-transform: uppercase !important;
		}

		/* Navigation Menu Header Title */
		body:not(.sidebar_minimize) .sidebar .nav-section .text-section {
			color: rgba(255, 255, 255, 0.45) !important;
			font-size: 10px !important;
			font-weight: 800 !important;
			text-transform: uppercase !important;
			letter-spacing: 1.5px !important;
			margin: 15px 0 10px 25px !important;
		}

		/* Navigation Link Items */
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item > a {
			color: rgba(255, 255, 255, 0.75) !important;
			padding: 12px 20px !important;
			margin: 4px 12px !important;
			border-radius: 10px !important;
			font-weight: 600 !important;
			font-size: 13.5px !important;
			transition: all 0.25s ease !important;
			display: flex !important;
			align-items: center !important;
			position: relative !important;
			overflow: hidden !important;
			background: transparent !important;
		}

		/* Navigation Icons */
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item > a i {
			color: rgba(255, 255, 255, 0.55) !important;
			margin-right: 12px !important;
			font-size: 16px !important;
			width: 20px !important;
			text-align: center !important;
			transition: all 0.25s ease !important;
		}

		/* Hover States with Glow & Slide-in background */
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item > a:hover {
			background: rgba(255, 255, 255, 0.08) !important;
			color: #ffffff !important;
			transform: translateX(4px) !important;
		}

		body:not(.sidebar_minimize) .sidebar .nav > .nav-item > a:hover i {
			color: #90caf9 !important;
			transform: scale(1.15) !important;
		}

		/* Active States (Selected Sidebar Menu item) */
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.active > a {
			background: linear-gradient(90deg, #1976d2 0%, #0d47a1 100%) !important;
			color: #ffffff !important;
			box-shadow: 0 4px 15px rgba(13, 71, 161, 0.4) !important;
			font-weight: 700 !important;
		}

		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.active > a i {
			color: #ffffff !important;
			text-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
		}

		/* Custom Active Line Indicator */
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.active > a::before {
			content: '' !important;
			position: absolute !important;
			left: 0 !important;
			top: 0 !important;
			bottom: 0 !important;
			width: 4px !important;
			background: #ffb300 !important;
			border-radius: 0 4px 4px 0 !important;
		}

		/* Logout Item Styling (Red Glow on Hover) */
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.active.mt-3 > a {
			background: rgba(244, 67, 54, 0.05) !important;
			border: 1px solid rgba(244, 67, 54, 0.15) !important;
			box-shadow: none !important;
		}
		
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.mt-3 > a {
			border: 1px solid rgba(244, 67, 54, 0.15) !important;
			background: rgba(244, 67, 54, 0.03) !important;
			color: rgba(255, 255, 255, 0.75) !important;
		}
		
		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.mt-3 > a i {
			color: rgba(244, 67, 54, 0.6) !important;
		}

		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.mt-3 > a:hover {
			background: rgba(244, 67, 54, 0.12) !important;
			border-color: rgba(244, 67, 54, 0.35) !important;
			box-shadow: 0 4px 12px rgba(244, 67, 54, 0.2) !important;
			color: #ffffff !important;
		}

		body:not(.sidebar_minimize) .sidebar .nav > .nav-item.mt-3 > a:hover i {
			color: #ff5252 !important;
			transform: scale(1.15) rotate(-5deg) !important;
		}

		/* Smooth Transition for submenus */
		body:not(.sidebar_minimize) .sidebar .info .link-collapse {
			color: rgba(255, 255, 255, 0.65) !important;
			font-weight: 500 !important;
			font-size: 12px !important;
			transition: all 0.2s ease !important;
			text-decoration: none !important;
		}
		body:not(.sidebar_minimize) .sidebar .info .link-collapse:hover {
			color: #90caf9 !important;
			padding-left: 5px !important;
		}
	</style>
</head>
<body>
	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">
				
				<a href="index.php" class="logo d-flex align-items-center" style="text-decoration: none;">
					<img src="../assets/img/LOGO1.jpg" alt="Logo" class="navbar-brand mr-2" style="width: 35px; height: 35px; object-fit: contain; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.6);">
					<div class="brand-text-container" style="line-height: 1.2; display: flex; flex-direction: column; justify-content: center;">
						<span class="text-white font-weight-bold" style="font-size: 13px; display: block; font-family: 'Outfit', sans-serif; letter-spacing: 0.3px;">SMAN 1 Pangkalan</span>
						<span class="text-warning font-weight-bold" style="font-size: 9px; display: block; text-transform: uppercase; letter-spacing: 1px;">Koto Baru</span>
					</div>
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
				
				<div class="container-fluid">
				<!-- 	<div class="collapse" id="search-nav">
						<form class="navbar-left navbar-form nav-search mr-md-3">
							<div class="input-group">
								<div class="input-group-prepend">
									<button type="submit" class="btn btn-search pr-1">
										<i class="fa fa-search search-icon"></i>
									</button>
								</div>
								<input type="text" placeholder="Search ..." class="form-control">
							</div>
						</form>
					</div> -->
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<!-- <li class="nav-item toggle-nav-search hidden-caret">
							<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
								<i class="fa fa-search"></i>
							</a>
						</li> -->
						
						
						
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="../assets/img/user/<?=$data['foto'] ?>" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="avatar-lg"><img src="../assets/img/user/<?=$data['foto'] ?>" alt="image profile" class="avatar-img rounded"></div>
											<div class="u-text">
												<h4><?=$data['nama_guru'] ?></h4>
												<p class="text-muted"><?=$data['email'] ?></p>
												<a href="?page=jadwal" class="btn btn-xs btn-secondary btn-sm">Jadwal Mengajar</a>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="?page=akun" >Akun Saya</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="logout.php">Logout</a>
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>

		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">			
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="../assets/img/user/<?=$data['foto'] ?>" alt="..." class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									<?=$data['nama_guru'] ?>
									<span class="user-level"><?=$data['nip'] ?></span>
									<span class="caret"></span>
								</span>
							</a>
							<div class="clearfix"></div>

							<div class="collapse in" id="collapseExample">
								<ul class="nav">
									<li>
										<a href="?page=akun">
											<span class="link-collapse">Akun Saya</span>
										</a>
									</li>
									
								</ul>
							</div>
						</div>
					</div>
					<ul class="nav nav-primary">
						<li class="nav-item active">
							<a href="index.php" class="collapsed">
								<i class="fas fa-home"></i>
								<p>Dashboard</p>
							</a>							
						</li>
						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Main Utama</h4>
						</li>
						<li class="nav-item">
							<a href="?page=jadwal">
								<i class="fas fa-clipboard-check"></i>
								<p>Jadwal Mengajar</p>
							</a>
						
						</li>
						<li class="nav-item">
							<a data-toggle="collapse" href="#sidebarLayouts">
								<i class="fas fa-clipboard-list
"></i>
								<p>Presensi</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="sidebarLayouts">
								<ul class="nav nav-collapse">
									<?php
									$mengajar_sidebar = mysqli_query($con,"SELECT tb_mengajar.*, tb_mkelas.nama_kelas FROM tb_mengajar
									INNER JOIN tb_mkelas ON tb_mkelas.id_mkelas = tb_mengajar.id_mkelas
									WHERE tb_mengajar.id_guru='$data[id_guru]'
									ORDER BY tb_mengajar.id_mkelas ASC");

									foreach ($mengajar_sidebar as $dm) { ?>
									<li>
										<a href="?page=absen&pelajaran=<?=$dm['id_mengajar'] ?> ">
											<span class="sub-item"><!-- <?=strtoupper($dm['mapel']); ?> -->KELAS (<?=strtoupper($dm['nama_kelas']); ?>)</span>
										</a>
									</li>
								<?php } ?>
								</ul>
							</div>
						</li>
							<li class="nav-item">
							<a data-toggle="collapse" href="#rekapAbsen">
								<i class="fas fa-list-alt"></i>
								<p>Rekap Absen</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="rekapAbsen">
								<ul class="nav nav-collapse">
									<?php
										$rekap_sidebar = mysqli_query($con,"SELECT tb_mengajar.*, tb_mkelas.nama_kelas FROM tb_mengajar
										INNER JOIN tb_mkelas ON tb_mkelas.id_mkelas = tb_mengajar.id_mkelas
										WHERE tb_mengajar.id_guru='$data[id_guru]'
										ORDER BY tb_mengajar.id_mkelas ASC
										");
										foreach ($rekap_sidebar as $dm) { 
											?>
											<li>
										<a href="?page=rekap&pelajaran=<?=$dm['id_mengajar'] ?> ">
											<span class="sub-item"><!-- <?=strtoupper($dm['mapel']); ?> -->KELAS (<?=strtoupper($dm['nama_kelas']); ?>)</span>
										</a>
									</li>
								<?php } ?>
								</ul>
							</div>
						</li>
						<li class="nav-item active mt-3">
							<a href="logout.php" class="collapsed">
								<i class="fas fa-arrow-alt-circle-left"></i>
								<p>Logout</p>
							</a>							
						</li>
	
					<!-- 
						<li class="mx-4 mt-2">
							<a href="logout.php" class="btn btn-primary btn-block"><span class="btn-label"> <i class="fas fa-arrow-alt-circle-left"></i> </span> Logout</a> 
						</li> -->
					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->

		<div class="main-panel">
			<div class="content">

				<!-- Halaman dinamis -->
				<?php 
				error_reporting();
				$page= @$_GET['page'];
				$act = @$_GET['act'];

				if ($page=='absen') {
					if ($act=='') {
						include 'modul/absen/absen_kelas.php';
					}elseif ($act=='surat_view') {
						include 'modul/absen/view_surat_izin.php';
					}elseif ($act=='konfirmasi') {
						include 'modul/absen/konfirmasi_izin.php';
					}elseif ($act=='update') {
						include 'modul/absen/absen_kelas_update.php';
					}					
				}elseif ($page=='rekap') {
					if ($act=='') {
						include 'modul/rekap/rekap_absen.php';

					}					
				}elseif ($page=='jadwal') {
					if ($act=='') {
						include 'modul/jadwal/jadwal_megajar.php';

					}					
				}elseif ($page=='akun') {
					include 'modul/akun/akun.php';
				}

				elseif ($page=='') {
					include 'modul/home.php';
				}else{
					echo "<b>Tidak ada Halaman</b>";
				}


				 ?>


				<!-- end -->


				
			</div>
		<footer class="footer">
				<div class="container">
					<div class="copyright ml-auto">
						&copy; <?php echo date('Y');?> Absensi SMA Negeri 1 Pangkalan Koto Baru (<a href="https://pedromasdy.netlify.app" target="_blank">Pedro Masdy</a> | <?php echo date('Y'); ?>)
					</div>				
				</div>
			</footer>
		</div>
		
	
	</div>
	<!--   Core JS Files   -->
	<script src="../assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="../assets/js/core/popper.min.js"></script>
	<script src="../assets/js/core/bootstrap.min.js"></script>

	<!-- jQuery UI -->
	<script src="../assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="../assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

	<!-- jQuery Scrollbar -->
	<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>


	<!-- Datatables -->
	<script src="../assets/js/plugin/datatables/datatables.min.js"></script>



	<!-- Sweet Alert -->
	<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

	<!-- Atlantis JS -->
	<script src="../assets/js/atlantis.min.js"></script>

	<!-- Atlantis DEMO methods, don't include it in your project! -->
	<script src="../assets/js/setting-demo.js"></script>
	

</body>
</html>