<?php
// $k is defined in kepsek/index.php and represents the active headmaster row
$nama_kepsek = isset($k['nama_kepsek']) ? $k['nama_kepsek'] : 'Amiruddin Aziz M.Pd';
$foto_kepsek = isset($k['foto']) ? $k['foto'] : 'kepala.png';

// 1. Get Teacher, Student, and Class Counts
$totalTeachersQry = mysqli_query($con, "SELECT COUNT(*) AS total FROM tb_guru WHERE status='Y'");
$teachersData = mysqli_fetch_array($totalTeachersQry);
$total_teachers = isset($teachersData['total']) ? $teachersData['total'] : 0;

$totalStudentsQry = mysqli_query($con, "SELECT COUNT(*) AS total FROM tb_siswa WHERE status='1'");
$studentsData = mysqli_fetch_array($totalStudentsQry);
$total_students = isset($studentsData['total']) ? $studentsData['total'] : 0;

$totalClassesQry = mysqli_query($con, "SELECT COUNT(*) AS total FROM tb_mkelas");
$classesData = mysqli_fetch_array($totalClassesQry);
$total_classes = isset($classesData['total']) ? $classesData['total'] : 0;

// 2. Real-Time Daily Attendance Statistics for Today
$tgl_hari_ini = date('Y-m-d');
$todayAbsenQry = mysqli_query($con, "
    SELECT 
        COUNT(CASE WHEN ket = 'H' THEN 1 END) AS hadir,
        COUNT(CASE WHEN ket = 'S' THEN 1 END) AS sakit,
        COUNT(CASE WHEN ket = 'I' THEN 1 END) AS izin,
        COUNT(CASE WHEN ket = 'T' THEN 1 END) AS terlambat,
        COUNT(CASE WHEN ket = 'A' THEN 1 END) AS alfa,
        COUNT(CASE WHEN ket = 'C' THEN 1 END) AS cabut
    FROM _logabsensi 
    WHERE tgl_absen = '$tgl_hari_ini'
");
$logsToday = mysqli_fetch_array($todayAbsenQry);

$hadir_t = isset($logsToday['hadir']) ? (int)$logsToday['hadir'] : 0;
$sakit_t = isset($logsToday['sakit']) ? (int)$logsToday['sakit'] : 0;
$izin_t = isset($logsToday['izin']) ? (int)$logsToday['izin'] : 0;
$terlambat_t = isset($logsToday['terlambat']) ? (int)$logsToday['terlambat'] : 0;
$alfa_t = isset($logsToday['alfa']) ? (int)$logsToday['alfa'] : 0;
$cabut_t = isset($logsToday['cabut']) ? (int)$logsToday['cabut'] : 0;

$sum_total = $hadir_t + $sakit_t + $izin_t + $terlambat_t + $alfa_t + $cabut_t;
$attendance_rate = $sum_total > 0 ? round((($hadir_t + $terlambat_t) / $sum_total) * 100, 1) : 0;
?>

<!-- Premium Headmaster Dashboard Styles -->
<style>
    .kepsek-hero {
        background: linear-gradient(135deg, #0d47a1 0%, #1565c0 70%, #ffb300 120%);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(13, 71, 161, 0.15);
        color: #fff;
        padding: 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }
    .kepsek-hero::before {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 170px;
        height: 170px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }
    .kepsek-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .kepsek-hero-subtitle {
        font-size: 14.5px;
        opacity: 0.9;
    }

    /* Stats summary widgets */
    .kepsek-stat-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .kepsek-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .kepsek-stat-body {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kepsek-stat-val {
        font-size: 26px;
        font-weight: 800;
        color: #111;
        line-height: 1;
    }
    .kepsek-stat-lbl {
        font-size: 11.5px;
        color: #666;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 5px;
    }
    .kepsek-stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Circular Progress Indicator for overall attendance */
    .attendance-meter-box {
        text-align: center;
        padding: 25px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .meter-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: radial-gradient(circle, #fff 55%, transparent 56%),
                    conic-gradient(#4caf50 <?= $attendance_rate; ?>%, #e0e0e0 0);
        margin: 15px auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        color: #1b5e20;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
</style>

<div class="page-inner">

    <!-- HEADMASTER HERO GREETING -->
    <div class="kepsek-hero">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="kepsek-hero-title">Selamat Datang Kembali, <?= htmlspecialchars($nama_kepsek); ?>!</div>
                <div class="kepsek-hero-subtitle">Portal Kepala Sekolah SMA Negeri 1 Pangkalan Koto Baru. Memantau kehadiran guru, melacak kedisiplinan siswa harian, serta mengunduh rekapitulasi data akademik.</div>
            </div>
            <div class="col-md-3 text-center d-none d-md-block">
                <div class="avatar avatar-xxl">
                    <img src="../assets/img/user/<?= htmlspecialchars($foto_kepsek); ?>" alt="..." class="avatar-img rounded-circle" style="border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 100px; height: 100px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <!-- GENERAL SCHOOL OVERVIEW STATS -->
    <div class="row">
        <!-- Total Guru Aktif -->
        <div class="col-md-4 mb-4">
            <div class="card kepsek-stat-card">
                <div class="kepsek-stat-body">
                    <div>
                        <div class="kepsek-stat-val"><?= $total_teachers; ?></div>
                        <div class="kepsek-stat-lbl">Guru Aktif</div>
                    </div>
                    <div class="kepsek-stat-icon bg-primary-gradient text-white">
                        <i class="fas fa-users-cog"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Siswa Aktif -->
        <div class="col-md-4 mb-4">
            <div class="card kepsek-stat-card">
                <div class="kepsek-stat-body">
                    <div>
                        <div class="kepsek-stat-val"><?= $total_students; ?></div>
                        <div class="kepsek-stat-lbl">Siswa Terdaftar</div>
                    </div>
                    <div class="kepsek-stat-icon bg-success-gradient text-white">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-md-4 mb-4">
            <div class="card kepsek-stat-card">
                <div class="kepsek-stat-body">
                    <div>
                        <div class="kepsek-stat-val"><?= $total_classes; ?></div>
                        <div class="kepsek-stat-lbl">Rombongan Belajar</div>
                    </div>
                    <div class="kepsek-stat-icon bg-info-gradient text-white">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <!-- REAL-TIME TODAY'S ATTENDANCE WIDGET (LEFT) -->
        <div class="col-md-8 mb-4">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06); height: 100%;">
                <div class="card-header bg-light d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid #eee;">
                    <h4 class="card-title font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                        <i class="fas fa-chart-line text-primary mr-2"></i>Status Kehadiran Siswa Hari Ini (<?= date('d M Y'); ?>)
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($sum_total > 0) { ?>
                        <div class="row text-center">
                            <!-- Hadir -->
                            <div class="col-4 col-sm-2 mb-3">
                                <h3 class="font-weight-bold text-success mb-1"><?= $hadir_t; ?></h3>
                                <span class="text-muted small">Hadir</span>
                            </div>
                            <!-- Sakit -->
                            <div class="col-4 col-sm-2 mb-3">
                                <h3 class="font-weight-bold text-warning mb-1"><?= $sakit_t; ?></h3>
                                <span class="text-muted small">Sakit</span>
                            </div>
                            <!-- Izin -->
                            <div class="col-4 col-sm-2 mb-3">
                                <h3 class="font-weight-bold text-info mb-1"><?= $izin_t; ?></h3>
                                <span class="text-muted small">Izin</span>
                            </div>
                            <!-- Terlambat -->
                            <div class="col-4 col-sm-2 mb-3">
                                <h3 class="font-weight-bold mb-1" style="color: #76FF03;"><?= $terlambat_t; ?></h3>
                                <span class="text-muted small">Terlambat</span>
                            </div>
                            <!-- Alfa -->
                            <div class="col-4 col-sm-2 mb-3">
                                <h3 class="font-weight-bold text-danger mb-1"><?= $alfa_t; ?></h3>
                                <span class="text-muted small">Alfa</span>
                            </div>
                            <!-- Cabut -->
                            <div class="col-4 col-sm-2 mb-3">
                                <h3 class="font-weight-bold text-purple mb-1"><?= $cabut_t; ?></h3>
                                <span class="text-muted small">Cabut</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <h5 class="font-weight-bold text-dark mb-2">Tingkat Kehadiran Sekolah</h5>
                                <p class="text-muted small mb-0">Rasio persentase kehadiran bersih (Hadir + Terlambat) dibandingkan total siswa yang melakukan presensi hari ini.</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="attendance-meter-box py-2" style="box-shadow: none;">
                                    <div class="meter-circle"><?= $attendance_rate; ?>%</div>
                                    <div class="small font-weight-bold text-success"><i class="fas fa-check-circle mr-1"></i> Data Kehadiran Sehat</div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="text-center py-5">
                            <img src="../assets/img/LOGO1.jpg" width="65" class="mb-3 opacity-3" style="filter: grayscale(1);">
                            <h5 class="text-muted font-weight-bold">Belum Ada Presensi Masuk Hari Ini</h5>
                            <p class="text-muted small mb-0">Data statistik tingkat kehadiran akan otomatis dikalkulasi dan ditampilkan di sini setelah guru memulai presensi hari ini.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- QUICK ACCESS MENU & ABOUT (RIGHT) -->
        <div class="col-md-4 mb-4">
            <!-- Quick Navigation -->
            <div class="card mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-header bg-light p-3" style="border-bottom: 1px solid #eee;">
                    <h5 class="font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-file-contract text-primary mr-2"></i>Akses Cepat Laporan</h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2" style="gap: 10px;">
                        <a href="?page=laporan" class="btn btn-outline-primary btn-block text-left font-weight-bold py-2 mb-2" style="border-radius: 8px;">
                            <i class="fas fa-list-alt mr-2 text-primary"></i> Rekapitulasi Presensi
                        </a>
                        <a href="?page=laporan" class="btn btn-outline-info btn-block text-left font-weight-bold py-2 mb-2" style="border-radius: 8px;">
                            <i class="fas fa-file-excel mr-2 text-info"></i> Laporan Bulanan Excel
                        </a>
                        <a href="?page=laporan" class="btn btn-outline-success btn-block text-left font-weight-bold py-2" style="border-radius: 8px;">
                            <i class="fas fa-print mr-2 text-success"></i> Cetak Rekap Semester
                        </a>
                    </div>
                </div>
            </div>

            <!-- School Card info -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-body text-center p-4">
                    <img src="../assets/img/LOGO1.jpg" width="70" class="mb-3" alt="Logo Sekolah" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.08));">
                    <h5 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #0d47a1;">SMA Negeri 1 Pangkalan Koto Baru</h5>
                    <p class="text-muted small mb-0">Jl. Lintas Sumbar-Riau Kec. Pangkalan Koto Baru Kab. Lima Puluh Kota, Sumatera Barat (26272)</p>
                </div>
            </div>
        </div>
    </div>

</div>