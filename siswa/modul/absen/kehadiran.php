<?php
// id_login is defined in siswa/index.php
$id_login = $_SESSION['siswa'];

// Pre-fetch overall statistics for widgets
$stat_hadir = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) AS total FROM _logabsensi 
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    WHERE _logabsensi.id_siswa='$id_login' AND _logabsensi.ket='H' AND tb_thajaran.status=1 AND tb_semester.status=1"));

$stat_sakit = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) AS total FROM _logabsensi 
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    WHERE _logabsensi.id_siswa='$id_login' AND _logabsensi.ket='S' AND tb_thajaran.status=1 AND tb_semester.status=1"));

$stat_izin = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) AS total FROM _logabsensi 
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    WHERE _logabsensi.id_siswa='$id_login' AND _logabsensi.ket='I' AND tb_thajaran.status=1 AND tb_semester.status=1"));

$stat_terlambat = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) AS total FROM _logabsensi 
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    WHERE _logabsensi.id_siswa='$id_login' AND _logabsensi.ket='T' AND tb_thajaran.status=1 AND tb_semester.status=1"));

$stat_alfa = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) AS total FROM _logabsensi 
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    WHERE _logabsensi.id_siswa='$id_login' AND _logabsensi.ket='A' AND tb_thajaran.status=1 AND tb_semester.status=1"));

$stat_cabut = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) AS total FROM _logabsensi 
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    WHERE _logabsensi.id_siswa='$id_login' AND _logabsensi.ket='C' AND tb_thajaran.status=1 AND tb_semester.status=1"));

$total_absen = $stat_hadir['total'] + $stat_sakit['total'] + $stat_izin['total'] + $stat_terlambat['total'] + $stat_alfa['total'] + $stat_cabut['total'];
$presence_rate = ($total_absen > 0) ? round((($stat_hadir['total'] + $stat_sakit['total'] + $stat_izin['total'] + $stat_terlambat['total']) / $total_absen) * 100, 1) : 0;
?>

<!-- Custom Premium CSS for Attendance Panel -->
<style>
    .presence-header-gradient {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 16px;
        color: #fff;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(30, 60, 114, 0.15);
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }
    .presence-header-gradient::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        pointer-events: none;
    }
    
    .presence-widget-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        border: none;
        padding: 20px;
        display: flex;
        align-items: center;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .presence-widget-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .presence-widget-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
    }
    .widget-hadir::before { background: #4caf50; }
    .widget-sakit::before { background: #2196f3; }
    .widget-izin::before { background: #ffc107; }
    .widget-terlambat::before { background: #9c27b0; }
    .widget-alfa::before { background: #f44336; }
    .widget-cabut::before { background: #757575; }

    .widget-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 15px;
    }
    .widget-hadir .widget-icon-wrapper { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
    .widget-sakit .widget-icon-wrapper { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
    .widget-izin .widget-icon-wrapper { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .widget-terlambat .widget-icon-wrapper { background: rgba(156, 39, 176, 0.1); color: #9c27b0; }
    .widget-alfa .widget-icon-wrapper { background: rgba(244, 67, 54, 0.1); color: #f44336; }
    .widget-cabut .widget-icon-wrapper { background: rgba(117, 117, 117, 0.1); color: #757575; }

    .widget-data h3 {
        font-size: 24px;
        font-weight: 800;
        color: #111;
        margin: 0;
        line-height: 1.1;
    }
    .widget-data span {
        font-size: 12px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Overall rate badge */
    .overall-rate-box {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 15px 25px;
        display: inline-flex;
        align-items: center;
        gap: 15px;
    }
    .rate-circle-mini {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: conic-gradient(#4caf50 <?= ($presence_rate * 3.6); ?>deg, rgba(255, 255, 255, 0.2) 0deg);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .rate-circle-mini-inner {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #1e3c72;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        color: #fff;
    }

    /* Tabs Styling */
    .presence-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: none;
        overflow: hidden;
    }
    .presence-card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #ebebeb;
        padding: 15px 25px;
    }
    .presence-tab-link {
        border: none !important;
        border-radius: 30px !important;
        color: #555 !important;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 20px !important;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .presence-tab-link.active {
        background-color: #1e3c72 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }
    .presence-tab-link:hover:not(.active) {
        background-color: #eee !important;
    }

    /* Monthly summary card list */
    .monthly-presence-card {
        background: #fafafa;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }
    .monthly-presence-card:hover {
        background: #fff;
        border-color: #1e3c72;
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.08);
    }

    .badge-status-chip {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-h { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
    .badge-s { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
    .badge-i { background: rgba(255, 193, 7, 0.1); color: #ffb300; }
    .badge-t { background: rgba(156, 39, 176, 0.1); color: #9c27b0; }
    .badge-a { background: rgba(244, 67, 54, 0.1); color: #f44336; }
    .badge-c { background: rgba(117, 117, 117, 0.1); color: #757575; }

    .tbl-details th {
        font-weight: 700 !important;
        color: #444 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .tbl-details td {
        font-size: 13.5px !important;
        font-weight: 600 !important;
        vertical-align: middle !important;
    }
</style>

<div class="page-inner">
    <!-- Breadcrumbs -->
    <div class="page-header">
        <h4 class="page-title" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Kehadiran Saya</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="index.php">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Kehadiran</a>
            </li>
        </ul>
    </div>

    <!-- 1. PREMIUM HEADER SUMMARY -->
    <div class="presence-header-gradient">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2 class="font-weight-bold mb-2" style="font-family: 'Outfit', sans-serif;">Halo, <?= htmlspecialchars((string)$data['nama_siswa']); ?>!</h2>
                <p class="mb-0 text-white-50" style="font-size: 14px;">Berikut adalah rekapitulasi data absensi Anda sepanjang semester aktif ini. Pastikan tingkat kehadiran Anda selalu berada di atas batas minimal kelulusan sekolah.</p>
            </div>
            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                <div class="overall-rate-box">
                    <div class="rate-circle-mini">
                        <div class="rate-circle-mini-inner"><?= $presence_rate; ?>%</div>
                    </div>
                    <div class="text-left" style="line-height: 1.2;">
                        <span class="small text-white-50 font-weight-bold d-block text-uppercase">Persentase Kehadiran</span>
                        <span class="font-weight-bold" style="font-size: 16px; color: #ffb300;">Sangat Baik / Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. COLORFUL SUMMARY CARDS GRID -->
    <div class="row mb-4">
        <div class="col-6 col-md-2 mb-3">
            <div class="presence-widget-card widget-hadir">
                <div class="widget-icon-wrapper"><i class="fas fa-check-circle"></i></div>
                <div class="widget-data">
                    <h3><?= $stat_hadir['total']; ?></h3>
                    <span>Hadir</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="presence-widget-card widget-sakit">
                <div class="widget-icon-wrapper"><i class="fas fa-heartbeat"></i></div>
                <div class="widget-data">
                    <h3><?= $stat_sakit['total']; ?></h3>
                    <span>Sakit</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="presence-widget-card widget-izin">
                <div class="widget-icon-wrapper"><i class="fas fa-envelope"></i></div>
                <div class="widget-data">
                    <h3><?= $stat_izin['total']; ?></h3>
                    <span>Izin</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="presence-widget-card widget-terlambat">
                <div class="widget-icon-wrapper"><i class="fas fa-clock"></i></div>
                <div class="widget-data">
                    <h3><?= $stat_terlambat['total']; ?></h3>
                    <span>Terlambat</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="presence-widget-card widget-alfa">
                <div class="widget-icon-wrapper"><i class="fas fa-times-circle"></i></div>
                <div class="widget-data">
                    <h3><?= $stat_alfa['total']; ?></h3>
                    <span>Alfa</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 mb-3">
            <div class="presence-widget-card widget-cabut">
                <div class="widget-icon-wrapper"><i class="fas fa-running"></i></div>
                <div class="widget-data">
                    <h3><?= $stat_cabut['total']; ?></h3>
                    <span>Cabut</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. TABBED CARD (MONTHLY vs DETAILED LOGS) -->
    <div class="presence-card">
        <div class="presence-card-header">
            <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link presence-tab-link active" id="pills-rekap-tab" data-toggle="pill" href="#pills-rekap" role="tab" aria-controls="pills-rekap" aria-selected="true">
                        <i class="fas fa-calendar-alt"></i> Rekap Bulanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link presence-tab-link" id="pills-history-tab" data-toggle="pill" href="#pills-history" role="tab" aria-controls="pills-history" aria-selected="false">
                        <i class="fas fa-list-ol"></i> Riwayat Absen Detail
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="pills-tabContent">
                
                <!-- TAB 1: REKAP BULANAN -->
                <div class="tab-pane fade show active" id="pills-rekap" role="tabpanel" aria-labelledby="pills-rekap-tab">
                    <h5 class="font-weight-bold mb-4" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-chart-bar mr-2 text-primary"></i>Rincian Kehadiran Setiap Bulan</h5>
                    
                    <div class="row">
                        <?php 
                        $qry = mysqli_query($con,"SELECT MONTH(tgl_absen) AS bulan, MAX(tgl_absen) AS tgl_absen FROM _logabsensi
                            INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
                            INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
                            INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
                            WHERE _logabsensi.id_siswa='$id_login' AND tb_thajaran.status=1 AND tb_semester.status=1
                            GROUP BY MONTH(tgl_absen) ORDER BY MONTH(tgl_absen) DESC");

                        if (mysqli_num_rows($qry) == 0) {
                            echo '<div class="col-12 text-center py-5"><img src="../assets/img/icon.ico" style="opacity: 0.2; width: 60px; filter: grayscale(100%);" class="mb-3"><p class="text-muted font-weight-bold">Belum ada catatan kehadiran bulan ini</p></div>';
                        }

                        foreach ($qry as $bulan_row) { 
                            $bulan_m = date('m', strtotime($bulan_row['tgl_absen']));
                            
                            // Fetch specific counts for this month
                            $m_h = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as cnt FROM _logabsensi WHERE id_siswa='$id_login' AND ket='H' AND MONTH(tgl_absen)='$bulan_m'"));
                            $m_s = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as cnt FROM _logabsensi WHERE id_siswa='$id_login' AND ket='S' AND MONTH(tgl_absen)='$bulan_m'"));
                            $m_i = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as cnt FROM _logabsensi WHERE id_siswa='$id_login' AND ket='I' AND MONTH(tgl_absen)='$bulan_m'"));
                            $m_t = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as cnt FROM _logabsensi WHERE id_siswa='$id_login' AND ket='T' AND MONTH(tgl_absen)='$bulan_m'"));
                            $m_a = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as cnt FROM _logabsensi WHERE id_siswa='$id_login' AND ket='A' AND MONTH(tgl_absen)='$bulan_m'"));
                            $m_c = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as cnt FROM _logabsensi WHERE id_siswa='$id_login' AND ket='C' AND MONTH(tgl_absen)='$bulan_m'"));
                            
                            $m_total = $m_h['cnt'] + $m_s['cnt'] + $m_i['cnt'] + $m_t['cnt'] + $m_a['cnt'] + $m_c['cnt'];
                            $m_rate = ($m_total > 0) ? round((($m_h['cnt'] + $m_s['cnt'] + $m_i['cnt'] + $m_t['cnt']) / $m_total) * 100) : 0;
                        ?>
                            <div class="col-md-6">
                                <div class="monthly-presence-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="font-weight-bold mb-0 text-primary" style="font-family: 'Outfit', sans-serif;">
                                            <i class="far fa-calendar-check mr-2"></i><?= namaBulan($bulan_m); ?> <?= date('Y', strtotime($bulan_row['tgl_absen'])) ?>
                                        </h4>
                                        <span class="badge badge-success font-weight-bold" style="border-radius: 20px; font-size: 11px;"><?= $m_rate; ?>% Kehadiran</span>
                                    </div>

                                    <!-- Monthly progress bar -->
                                    <div class="progress mb-4" style="height: 8px; border-radius: 30px; background: #eee;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $m_rate; ?>%; border-radius: 30px;" aria-valuenow="<?= $m_rate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    <!-- Status stats chips -->
                                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                                        <span class="badge-status-chip badge-h"><i class="fas fa-check"></i> Hadir: <b><?= $m_h['cnt']; ?></b></span>
                                        <span class="badge-status-chip badge-s"><i class="fas fa-heart"></i> Sakit: <b><?= $m_s['cnt']; ?></b></span>
                                        <span class="badge-status-chip badge-i"><i class="fas fa-envelope"></i> Izin: <b><?= $m_i['cnt']; ?></b></span>
                                        <span class="badge-status-chip badge-t"><i class="fas fa-clock"></i> Terlambat: <b><?= $m_t['cnt']; ?></b></span>
                                        <span class="badge-status-chip badge-a"><i class="fas fa-times"></i> Alfa: <b><?= $m_a['cnt']; ?></b></span>
                                        <span class="badge-status-chip badge-c"><i class="fas fa-running"></i> Cabut: <b><?= $m_c['cnt']; ?></b></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- TAB 2: DETAILED HISTORY TABLE -->
                <div class="tab-pane fade" id="pills-history" role="tabpanel" aria-labelledby="pills-history-tab">
                    <h5 class="font-weight-bold mb-4" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-history mr-2 text-primary"></i>Catatan Log Absensi Pelajaran Lengkap</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover tbl-details" id="detailedLogsTable" style="width: 100%;">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 50px;">No</th>
                                    <th>Hari / Tanggal</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Pertemuan Ke</th>
                                    <th>Nama Guru</th>
                                    <th style="width: 150px; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $logs_sql = mysqli_query($con, "SELECT _logabsensi.*, tb_mengajar.hari, tb_master_mapel.mapel, tb_guru.nama_guru
                                    FROM _logabsensi
                                    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
                                    INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel=tb_master_mapel.id_mapel
                                    INNER JOIN tb_guru ON tb_mengajar.id_guru=tb_guru.id_guru
                                    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
                                    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
                                    WHERE _logabsensi.id_siswa='$id_login' AND tb_thajaran.status=1 AND tb_semester.status=1
                                    ORDER BY _logabsensi.tgl_absen DESC, _logabsensi.id_presensi DESC");
                                
                                $no = 1;
                                while ($log = mysqli_fetch_array($logs_sql)) {
                                    $status_class = '';
                                    $status_label = '';
                                    switch($log['ket']) {
                                        case 'H': $status_class = 'badge-h'; $status_label = '<i class="fas fa-check-circle mr-1"></i> Hadir'; break;
                                        case 'S': $status_class = 'badge-s'; $status_label = '<i class="fas fa-heartbeat mr-1"></i> Sakit'; break;
                                        case 'I': $status_class = 'badge-i'; $status_label = '<i class="fas fa-envelope mr-1"></i> Izin'; break;
                                        case 'T': $status_class = 'badge-t'; $status_label = '<i class="fas fa-clock mr-1"></i> Terlambat'; break;
                                        case 'A': $status_class = 'badge-a'; $status_label = '<i class="fas fa-times-circle mr-1"></i> Alfa'; break;
                                        case 'C': $status_class = 'badge-c'; $status_label = '<i class="fas fa-running mr-1"></i> Cabut'; break;
                                    }
                                ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td>
                                            <span class="d-block font-weight-bold text-dark"><?= namahari($log['tgl_absen']); ?></span>
                                            <small class="text-muted"><?= date('d F Y', strtotime($log['tgl_absen'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="d-block font-weight-bold" style="color: #1e3c72;"><?= htmlspecialchars($log['mapel']); ?></span>
                                            <small class="text-muted"><i class="far fa-clock mr-1"></i> Hari <?= $log['hari']; ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-count" style="background: rgba(30,60,114,0.1); color: #1e3c72; font-weight: 700; border-radius: 6px;">Ke-<?= $log['pertemuan_ke']; ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($log['nama_guru']); ?></td>
                                        <td style="text-align: center;">
                                            <span class="badge-status-chip <?= $status_class; ?>" style="min-width: 110px; justify-content: center;">
                                                <?= $status_label; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- BACK ACTION BUTTON -->
    <div class="mt-4">
        <a href="javascript:history.back()" class="btn btn-light px-4 btn-round" style="border: 1px solid #ddd; font-weight: 700; color: #444; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <i class="fas fa-arrow-circle-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Load and Initialize Datatables on Riwayat Absen Detail -->
<script>
    $(document).ready(function() {
        $('#detailedLogsTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari data:",
                "lengthMenu": "Tampilkan _MENU_ baris per halaman",
                "zeroRecords": "Tidak ada data log yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari total _MAX_ baris)",
                "paginate": {
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>