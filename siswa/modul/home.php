<?php
// id_login is defined in siswa/index.php
$id_login = $_SESSION['siswa'];

// Fetch optimized statistics for student attendance
$statsQry = mysqli_query($con, "
    SELECT 
        COUNT(CASE WHEN ket = 'H' THEN 1 END) AS hadir,
        COUNT(CASE WHEN ket = 'S' THEN 1 END) AS sakit,
        COUNT(CASE WHEN ket = 'I' THEN 1 END) AS izin,
        COUNT(CASE WHEN ket = 'T' THEN 1 END) AS terlambat,
        COUNT(CASE WHEN ket = 'A' THEN 1 END) AS alfa,
        COUNT(CASE WHEN ket = 'C' THEN 1 END) AS cabut
    FROM _logabsensi 
    WHERE id_siswa = '$id_login'
");
$stats = mysqli_fetch_array($statsQry);

$hadir = isset($stats['hadir']) ? (int)$stats['hadir'] : 0;
$sakit = isset($stats['sakit']) ? (int)$stats['sakit'] : 0;
$izin = isset($stats['izin']) ? (int)$stats['izin'] : 0;
$terlambat = isset($stats['terlambat']) ? (int)$stats['terlambat'] : 0;
$alfa = isset($stats['alfa']) ? (int)$stats['alfa'] : 0;
$cabut = isset($stats['cabut']) ? (int)$stats['cabut'] : 0;

$total_absen = $hadir + $sakit + $izin + $terlambat + $alfa + $cabut;
$persentase_hadir = $total_absen > 0 ? round((($hadir + $terlambat) / $total_absen) * 100, 1) : 0;

// Check completeness of student profile
$is_complete = (!empty($data['nama_siswa']) && !empty($data['tempat_lahir']) && !empty($data['tgl_lahir']) && !empty($data['jk']) && !empty($data['alamat']) && $data['foto'] != '17603.png' && $data['foto'] != '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg');
?>

<!-- Custom Premium Dashboard Styles -->
<style>
    .welcome-card-premium {
        background: linear-gradient(135deg, #0d47a1 0%, #1565c0 70%, #1e88e5 100%);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(13, 71, 161, 0.2);
        color: #fff;
        border: none;
        padding: 30px;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
    }
    .welcome-card-premium::before {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .welcome-card-premium::after {
        content: '';
        position: absolute;
        right: 40px;
        bottom: -50px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .welcome-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .welcome-subtitle {
        font-size: 15px;
        opacity: 0.9;
        margin-bottom: 20px;
    }
    .welcome-btn {
        background: #ffb300;
        color: #0d47a1;
        font-weight: 700;
        border-radius: 30px;
        padding: 10px 25px;
        font-size: 14px;
        border: none;
        box-shadow: 0 4px 15px rgba(255, 179, 0, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .welcome-btn:hover {
        background: #ffa000;
        color: #0d47a1;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 179, 0, 0.4);
    }

    /* Info Stats Grid */
    .stat-card-premium {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .stat-card-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .stat-body {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #111;
        line-height: 1;
    }
    .stat-label {
        font-size: 12px;
        color: #666;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    /* Circular Progress Circle */
    .attendance-circle-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: none;
        padding: 25px;
        text-align: center;
    }
    
    /* Quick Action tiles */
    .quick-tile {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
        transition: all 0.2s ease;
        cursor: pointer;
        display: block;
        text-decoration: none !important;
    }
    .quick-tile:hover {
        border-color: #0d47a1;
        box-shadow: 0 6px 20px rgba(13, 71, 161, 0.08);
        transform: translateY(-2px);
    }
    .quick-tile-icon {
        font-size: 28px;
        color: #0d47a1;
        margin-bottom: 12px;
    }
    .quick-tile-title {
        font-weight: 700;
        font-size: 14px;
        color: #222;
    }
</style>

<div class="page-inner">
    
    <!-- WELCOME HERO BANNER -->
    <div class="welcome-card-premium">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="welcome-title">Selamat Datang Kembali, <?= htmlspecialchars((string)$data['nama_siswa']); ?>!</div>
                <div class="welcome-subtitle">Aplikasi Presensi Mandiri SMA Negeri 1 Pangkalan Koto Baru. Pantau kehadiran Anda dan cetak KTS fisik Anda kapan saja.</div>
                
                <?php if (!$is_complete) { ?>
                    <a href="?page=profil" class="welcome-btn">
                        <i class="fas fa-edit"></i> Lengkapi Profil & KTS Anda Sekarang
                    </a>
                <?php } else { ?>
                    <a target="_blank" href="modul/user/cetak_kts.php?id=<?=$id_login;?>" class="welcome-btn" style="background: #4caf50; color: #fff; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                        <i class="fas fa-print"></i> Cetak Kartu Siswa (KTS)
                    </a>
                <?php } ?>
            </div>
            <div class="col-md-4 text-center d-none d-md-block">
                <div class="avatar avatar-xxl" style="width: 120px; height: 120px;">
                    <img src="../assets/img/user/<?= htmlspecialchars((string)$data['foto']); ?>" alt="..." class="avatar-img rounded-circle" style="border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 120px; height: 120px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN STATS GRID -->
    <h4 class="font-weight-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-chart-pie text-primary mr-2"></i>Statistik Kehadiran Anda</h4>
    
    <div class="row">
        <!-- HADIR (H) -->
        <div class="col-sm-6 col-md-3 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value"><?= $hadir; ?></div>
                        <div class="stat-label">Hadir</div>
                    </div>
                    <div class="stat-icon bg-success-gradient text-white">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAKIT (S) -->
        <div class="col-sm-6 col-md-3 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value"><?= $sakit; ?></div>
                        <div class="stat-label">Sakit</div>
                    </div>
                    <div class="stat-icon bg-warning-gradient text-white">
                        <i class="fas fa-ambulance"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- IZIN (I) -->
        <div class="col-sm-6 col-md-3 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value"><?= $izin; ?></div>
                        <div class="stat-label">Izin</div>
                    </div>
                    <div class="stat-icon bg-info-gradient text-white">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- TERLAMBAT (T) -->
        <div class="col-sm-6 col-md-3 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value"><?= $terlambat; ?></div>
                        <div class="stat-label">Terlambat</div>
                    </div>
                    <div class="stat-icon text-white" style="background: linear-gradient(135deg, #76FF03 0%, #64DD17 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ALFA (A) -->
        <div class="col-sm-6 col-md-4 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value"><?= $alfa; ?></div>
                        <div class="stat-label">Tanpa Keterangan</div>
                    </div>
                    <div class="stat-icon bg-danger-gradient text-white">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- CABUT (C) -->
        <div class="col-sm-6 col-md-4 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value"><?= $cabut; ?></div>
                        <div class="stat-label">Cabut / Membolos</div>
                    </div>
                    <div class="stat-icon text-white" style="background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
                        <i class="fas fa-running"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERSENTASE KEHADIRAN -->
        <div class="col-sm-12 col-md-4 mb-4">
            <div class="card stat-card-premium">
                <div class="stat-body">
                    <div>
                        <div class="stat-value text-primary"><?= $persentase_hadir; ?>%</div>
                        <div class="stat-label">Tingkat Kehadiran</div>
                    </div>
                    <div class="stat-icon bg-primary-gradient text-white">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK TILES & PROFILE SUMMARY -->
    <div class="row mt-2">
        
        <!-- School Card info -->
        <div class="col-md-6 mb-4">
            <div class="card full-height" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-body text-center p-4">
                    <img src="../assets/img/LOGO1.jpg" width="85" class="mb-3" alt="Logo Sekolah" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));">
                    <h4 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #0d47a1;">SMA Negeri 1 Pangkalan Koto Baru</h4>
                    <p class="text-muted small mb-3">Jl. Lintas Sumbar-Riau Kec. Pangkalan Koto Baru Kab. Lima Puluh Kota, Sumatera Barat (26272)</p>
                    <hr>
                    <div class="d-flex justify-content-center gap-4 text-muted small mt-2">
                        <span><i class="fas fa-envelope mr-1 text-primary"></i> sma1pangkalan@gmail.com</span>
                        <span class="mx-3">|</span>
                        <span><i class="fas fa-phone mr-1 text-primary"></i> 081222333444</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access tiles -->
        <div class="col-md-6 mb-4">
            <div class="card full-height" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold text-dark mb-4" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-external-link-alt text-primary mr-2"></i>Akses Cepat Layanan</h5>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="?page=kehadiran" class="quick-tile">
                                <div class="quick-tile-icon"><i class="fas fa-clipboard-check"></i></div>
                                <div class="quick-tile-title">Riwayat Presensi</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="?page=izin" class="quick-tile">
                                <div class="quick-tile-icon" style="color: #03a9f4;"><i class="fas fa-paper-plane"></i></div>
                                <div class="quick-tile-title">Ajukan Surat Izin</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="?page=profil" class="quick-tile">
                                <div class="quick-tile-icon" style="color: #4caf50;"><i class="fas fa-user-edit"></i></div>
                                <div class="quick-tile-title">Lengkapi KTS</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="logout.php" class="quick-tile">
                                <div class="quick-tile-icon" style="color: #f44336;"><i class="fas fa-sign-out-alt"></i></div>
                                <div class="quick-tile-title">Keluar Sistem</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>