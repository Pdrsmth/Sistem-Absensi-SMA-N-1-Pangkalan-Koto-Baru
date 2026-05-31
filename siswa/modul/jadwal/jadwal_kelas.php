<?php
// $data is defined in siswa/index.php and represents the active student row
$id_mkelas = $data['id_mkelas'];

// Fetch class name for display (tb_siswa doesn't have nama_kelas directly)
$kelasInfo = mysqli_fetch_array(mysqli_query($con, "SELECT tb_mkelas.nama_kelas, tb_semester.semester 
    FROM tb_mkelas 
    LEFT JOIN tb_mengajar ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas
    LEFT JOIN tb_semester ON tb_mengajar.id_semester = tb_semester.id_semester AND tb_semester.status = 1
    WHERE tb_mkelas.id_mkelas = '$id_mkelas' LIMIT 1"));
$namaKelas = isset($kelasInfo['nama_kelas']) ? $kelasInfo['nama_kelas'] : '-';
$semesterAktif = isset($kelasInfo['semester']) ? $kelasInfo['semester'] : 'Aktif';


// Query the weekly schedule for this class
$scheduleQry = mysqli_query($con, "
    SELECT tb_mengajar.*, tb_guru.nama_guru, tb_master_mapel.mapel 
    FROM tb_mengajar 
    INNER JOIN tb_guru ON tb_mengajar.id_guru = tb_guru.id_guru 
    INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel 
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran = tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester = tb_semester.id_semester
    WHERE tb_mengajar.id_mkelas = '$id_mkelas'
      AND tb_thajaran.status = 1
      AND tb_semester.status = 1
    ORDER BY FIELD(tb_mengajar.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), tb_mengajar.jamke ASC
");

// Group schedules by Day
$schedulesByDay = [];
$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
foreach ($days as $day) {
    $schedulesByDay[$day] = [];
}

while ($row = mysqli_fetch_array($scheduleQry)) {
    $day = $row['hari'];
    // Map Jum'at to Jumat if stored inconsistently
    $norm_day = str_replace("'", "", $day); 
    if (in_array($norm_day, $days)) {
        $schedulesByDay[$norm_day][] = $row;
    } else {
        $schedulesByDay[$day][] = $row;
    }
}
?>

<!-- Premium Student Weekly Schedule Styles -->
<style>
    .schedule-header-premium {
        background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
        border-radius: 14px;
        color: #fff;
        padding: 20px 25px;
        box-shadow: 0 4px 15px rgba(13, 71, 161, 0.1);
        margin-bottom: 25px;
    }
    .day-column-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        margin-bottom: 25px;
        background: #fff;
        overflow: hidden;
    }
    .day-header-blue {
        background: #f8f9fa;
        border-bottom: 1.5px solid #0d47a1;
        padding: 12px 20px;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 15px;
        color: #0d47a1;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .schedule-item-row {
        padding: 15px 20px;
        border-bottom: 1px solid #f2f4f7;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .schedule-item-row:last-child {
        border-bottom: none;
    }
    .schedule-item-row:hover {
        background-color: #f9fbfd;
    }
    .subject-title {
        font-weight: 700;
        color: #222;
        font-size: 14px;
        margin-bottom: 3px;
    }
    .teacher-name {
        font-size: 12px;
        color: #666;
    }
    .time-slot-badge {
        background: rgba(13, 71, 161, 0.08);
        color: #0d47a1;
        font-weight: 700;
        font-size: 11.5px;
        padding: 5px 12px;
        border-radius: 30px;
        text-align: right;
    }
</style>

<div class="page-inner">
    
    <!-- SCHEDULE PAGE HEADER -->
    <div class="schedule-header-premium d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="font-weight-bold text-white mb-1" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-calendar-alt mr-2"></i>Jadwal Pelajaran Kelas Anda</h3>
            <p class="mb-0 text-white-50 small">Jadwal pelajaran mingguan aktif untuk <b>Kelas <?= htmlspecialchars($namaKelas); ?></b>.</p>
        </div>
        <span class="badge badge-warning font-weight-bold px-3 py-2" style="font-size: 13px; color: #0d47a1; border-radius: 30px;">
            Semester Aktif: <?= htmlspecialchars($semesterAktif); ?>
        </span>
    </div>

    <!-- WEEKLY TIMETABLE GRID -->
    <div class="row">
        <?php foreach ($days as $day) { 
            $day_schedules = isset($schedulesByDay[$day]) ? $schedulesByDay[$day] : [];
            $count = count($day_schedules);
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="day-column-card">
                    <div class="day-header-blue">
                        <span><i class="far fa-calendar-check mr-2"></i><?= $day; ?></span>
                        <span class="badge badge-pill badge-primary small" style="font-size: 10px;"><?= $count; ?> Pelajaran</span>
                    </div>
                    <div class="day-body">
                        <?php if ($count > 0) { ?>
                            <?php foreach ($day_schedules as $item) { ?>
                                <div class="schedule-item-row">
                                    <div>
                                        <div class="subject-title"><?= htmlspecialchars($item['mapel']); ?></div>
                                        <div class="teacher-name"><i class="fas fa-chalkboard-teacher text-muted mr-1"></i> <?= htmlspecialchars($item['nama_guru']); ?></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="time-slot-badge">JP <?= htmlspecialchars($item['jamke']); ?></div>
                                        <span class="d-block text-muted small mt-1" style="font-size: 9.5px;"><?= htmlspecialchars($item['jam_mengajar']); ?></span>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center py-4 text-muted small">
                                <i class="fas fa-mug-hot text-muted opacity-4 mb-2" style="font-size: 20px;"></i>
                                <div class="font-italic">Tidak ada jadwal belajar</div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

</div>
