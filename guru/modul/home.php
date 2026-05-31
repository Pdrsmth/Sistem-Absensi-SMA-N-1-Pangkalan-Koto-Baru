<?php
// $data is defined in guru/index.php and represents the active teacher row
$id_guru = $data['id_guru'];

// 1. Get Indonesian Day Name for Today
$hari_eng = date('l');
$day_map = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => "Jum'at",
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$hari_indo = isset($day_map[$hari_eng]) ? $day_map[$hari_eng] : 'Senin';
$hari_indo_escaped = mysqli_real_escape_string($con, $hari_indo);

// 2. Query Today's Teaching Schedule
$todaySchedule = mysqli_query($con, "
    SELECT tb_mengajar.*, tb_mkelas.nama_kelas, tb_master_mapel.mapel 
    FROM tb_mengajar 
    INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas 
    INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel 
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran = tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester = tb_semester.id_semester
    WHERE tb_mengajar.id_guru = '$id_guru' 
      AND tb_mengajar.hari = '$hari_indo_escaped'
      AND tb_thajaran.status = 1 
      AND tb_semester.status = 1
    ORDER BY tb_mengajar.jamke ASC
");
$total_today = mysqli_num_rows($todaySchedule);

// 3. Query Overall Schedule Count for this Teacher
$totalClassesQry = mysqli_query($con, "
    SELECT COUNT(DISTINCT id_mkelas) AS total_kelas, COUNT(*) AS total_jam 
    FROM tb_mengajar 
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran = tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester = tb_semester.id_semester
    WHERE id_guru = '$id_guru'
      AND tb_thajaran.status = 1 
      AND tb_semester.status = 1
");
$classesData = mysqli_fetch_array($totalClassesQry);
$total_kelas_binaan = isset($classesData['total_kelas']) ? $classesData['total_kelas'] : 0;
// 4. Query Pending Student Permits awaiting confirmation from classes this teacher teaches
$pendingPermitsQry = mysqli_query($con, "
    SELECT COUNT(*) AS total 
    FROM tb_izin 
    INNER JOIN tb_siswa ON tb_izin.id_siswa = tb_siswa.id_siswa
    WHERE tb_siswa.id_mkelas IN (
        SELECT DISTINCT id_mkelas FROM tb_mengajar WHERE id_guru = '$id_guru'
    ) AND tb_izin.ket_izin = '0'
");
$pendingData = mysqli_fetch_array($pendingPermitsQry);
$pending_count = isset($pendingData['total']) ? (int)$pendingData['total'] : 0;
?>

<!-- Premium Teacher Dashboard Styles -->
<style>
    .teacher-hero {
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 70%, #0a3578 100%);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(13, 71, 161, 0.15);
        color: #fff;
        padding: 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }
    .teacher-hero::before {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }
    .teacher-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .teacher-hero-subtitle {
        font-size: 14.5px;
        opacity: 0.9;
    }

    /* Stats summary widgets */
    .teacher-stat-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .teacher-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .teacher-stat-body {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .teacher-stat-val {
        font-size: 26px;
        font-weight: 800;
        color: #111;
        line-height: 1;
    }
    .teacher-stat-lbl {
        font-size: 11.5px;
        color: #666;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 5px;
    }
    .teacher-stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Today's schedule grid cards */
    .schedule-card-today {
        border-radius: 14px;
        border: 1px solid #eef0f3;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 20px;
        transition: all 0.2s ease;
        background: #fff;
    }
    .schedule-card-today:hover {
        border-color: #0d47a1;
        box-shadow: 0 6px 20px rgba(13, 71, 161, 0.08);
        transform: translateY(-2px);
    }
    .schedule-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f2f4f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .schedule-card-body {
        padding: 20px;
    }
    .schedule-card-actions {
        padding: 12px 20px;
        background: #fafbfc;
        border-top: 1px solid #f2f4f7;
        border-radius: 0 0 14px 14px;
        display: flex;
        gap: 10px;
    }
    .schedule-action-btn {
        flex: 1;
        border-radius: 30px;
        font-weight: 700;
        font-size: 12.5px;
        padding: 8px 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
</style>

<div class="page-inner">

    <!-- PENDING PERMIT ALERT NOTIFICATION BANNER -->
    <?php if ($pending_count > 0) { ?>
        <div class="alert alert-danger d-flex align-items-center justify-content-between p-3 mb-4" role="alert" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.15); border: none; background: #ffebee;">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 38px; height: 38px; background: #f44336; color:#fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-bell animate-bounce"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold text-danger mb-0" style="font-size: 14.5px;">Permohonan Izin Menunggu Konfirmasi</h5>
                    <p class="mb-0 text-muted small">Ada <b><?= $pending_count; ?> surat izin harian siswa</b> di kelas binaan Anda yang menunggu persetujuan.</p>
                </div>
            </div>
            <a href="?page=absen&act=konfirmasi" class="btn btn-danger btn-round btn-sm px-3 small font-weight-bold" style="font-size: 12px;">
                <i class="fas fa-check-circle mr-1"></i> Proses Sekarang
            </a>
        </div>
    <?php } ?>

    <!-- TEACHER GREETING HERO -->
    <div class="teacher-hero">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="teacher-hero-title">Selamat Datang Kembali, <?= htmlspecialchars((string)$data['nama_guru']); ?>!</div>
                <div class="teacher-hero-subtitle">Portal Guru SMA Negeri 1 Pangkalan Koto Baru. Mengelola presensi, melacak tingkat kehadiran siswa, dan rekap semesteran dengan mudah.</div>
            </div>
            <div class="col-md-3 text-center d-none d-md-block">
                <div class="avatar avatar-xxl">
                    <img src="../assets/img/user/<?= htmlspecialchars((string)$data['foto']); ?>" alt="..." class="avatar-img rounded-circle" style="border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 100px; height: 100px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <!-- GENERAL TEACHING STATS -->
    <div class="row">
        <!-- Total Kelas Binaan -->
        <div class="col-md-4 mb-4">
            <div class="card teacher-stat-card">
                <div class="teacher-stat-body">
                    <div>
                        <div class="teacher-stat-val"><?= $total_kelas_binaan; ?></div>
                        <div class="teacher-stat-lbl">Kelas Diajar</div>
                    </div>
                    <div class="teacher-stat-icon bg-primary-gradient text-white">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jam Mengajar (JP) -->
        <div class="col-md-4 mb-4">
            <div class="card teacher-stat-card">
                <div class="teacher-stat-body">
                    <div>
                        <div class="teacher-stat-val"><?= $total_jam_ajar; ?></div>
                        <div class="teacher-stat-lbl">Rombel / Jam Ajar</div>
                    </div>
                    <div class="teacher-stat-icon bg-info-gradient text-white">
                        <i class="fas fa-book-reader"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hari Ini status -->
        <div class="col-md-4 mb-4">
            <div class="card teacher-stat-card">
                <div class="teacher-stat-body">
                    <div>
                        <div class="teacher-stat-val text-primary" style="font-size: 20px; font-weight: 800;"><?= $hari_indo; ?></div>
                        <div class="teacher-stat-lbl"><?= date('d M Y'); ?></div>
                    </div>
                    <div class="teacher-stat-icon text-white" style="background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <!-- TODAY'S SCHEDULE COLUMN (LEFT) -->
        <div class="col-md-8">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-header bg-light d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid #eee;">
                    <h4 class="card-title font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                        <i class="far fa-clock text-primary mr-2"></i>Jadwal Mengajar Anda Hari Ini
                    </h4>
                    <span class="badge badge-primary font-weight-bold"><?= $total_today; ?> Jadwal</span>
                </div>
                <div class="card-body p-4">
                    <?php if ($total_today > 0) { ?>
                        <div class="row">
                            <?php foreach ($todaySchedule as $jd) { ?>
                                <div class="col-md-6">
                                    <div class="schedule-card-today">
                                        <div class="schedule-card-header">
                                            <span class="badge badge-info" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Kelas <?= $jd['nama_kelas']; ?></span>
                                            <span class="small font-weight-bold text-primary"><i class="far fa-clock mr-1"></i> JP <?= $jd['jamke']; ?></span>
                                        </div>
                                        <div class="schedule-card-body">
                                            <h4 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;"><?= $jd['mapel']; ?></h4>
                                            <p class="text-muted small mb-0"><i class="fas fa-hourglass-half mr-1"></i> Waktu: <?= $jd['jam_mengajar']; ?></p>
                                        </div>
                                        <div class="schedule-card-actions">
                                            <a href="?page=absen&pelajaran=<?= $jd['id_mengajar']; ?>" class="schedule-action-btn btn-primary">
                                                <i class="fas fa-clipboard-check"></i> Isi Absen
                                            </a>
                                            <a href="?page=rekap&pelajaran=<?= $jd['id_mengajar']; ?>" class="schedule-action-btn btn-light border text-dark">
                                                <i class="fas fa-chart-bar"></i> Rekap
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="text-center py-5">
                            <img src="../assets/img/LOGO1.jpg" width="60" class="mb-3 opacity-3" style="filter: grayscale(1);">
                            <h5 class="text-muted font-weight-bold">Tidak Ada Jadwal Mengajar Hari Ini</h5>
                            <p class="text-muted small mb-0">Hari ini Anda bisa memfokuskan waktu untuk memeriksa rekap absensi kelas atau beristirahat.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- QUICK ACCESS MENU & ABOUT (RIGHT) -->
        <div class="col-md-4">
            <!-- Quick Navigation -->
            <div class="card mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-header bg-light p-3" style="border-bottom: 1px solid #eee;">
                    <h5 class="font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-external-link-alt text-primary mr-2"></i>Akses Cepat Menu</h5>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush">
                        <?php
                        $allMengajar = mysqli_query($con, "
                            SELECT tb_mengajar.*, tb_mkelas.nama_kelas, tb_master_mapel.mapel 
                            FROM tb_mengajar 
                            INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas 
                            INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel 
                            WHERE tb_mengajar.id_guru = '$id_guru' 
                            ORDER BY tb_mkelas.nama_kelas ASC
                        ");
                        foreach ($allMengajar as $am) { ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-1">
                                <span class="small font-weight-bold text-dark"><?= $am['mapel']; ?> (<?= $am['nama_kelas']; ?>)</span>
                                <div class="d-flex gap-1">
                                    <a href="?page=absen&pelajaran=<?= $am['id_mengajar']; ?>" class="btn btn-xs btn-primary font-weight-bold mr-1" title="Isi Presensi"><i class="fas fa-pen-square"></i> Absen</a>
                                    <a href="?page=rekap&pelajaran=<?= $am['id_mengajar']; ?>" class="btn btn-xs btn-secondary font-weight-bold" title="Rekap Presensi"><i class="fas fa-list"></i> Rekap</a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <!-- School Card info -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div class="card-body text-center p-4">
                    <img src="../assets/img/LOGO1.jpg" width="70" class="mb-3" alt="Logo Sekolah" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.08));">
                    <h5 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #0d47a1;">SMA Negeri 1 Pangkalan Koto Baru</h5>
                    <p class="text-muted small mb-0">Aplikasi Presensi digital ini dirancang khusus untuk mendokumentasikan kehadiran secara real-time dan rapi.</p>
                </div>
            </div>
        </div>
    </div>

</div>