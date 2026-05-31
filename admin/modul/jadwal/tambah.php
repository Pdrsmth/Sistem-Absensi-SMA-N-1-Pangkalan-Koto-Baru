<?php 
$taAktif = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM tb_thajaran WHERE status=1 "));
$semAktif = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM tb_semester WHERE status=1 "));

// Helper functions for JP validation and time calculation
function get_jp_time($start, $end) {
    $jp_start_times = [
        1 => '07:30',
        2 => '08:15',
        3 => '09:00',
        4 => '10:15',
        5 => '11:00',
        6 => '11:45',
        7 => '13:00',
        8 => '13:45',
        9 => '14:30'
    ];
    $jp_end_times = [
        1 => '08:15',
        2 => '09:00',
        3 => '09:45',
        4 => '11:00',
        5 => '11:45',
        6 => '12:30',
        7 => '13:45',
        8 => '14:30',
        9 => '15:15'
    ];
    
    $start = (int)$start;
    $end = (int)$end;
    
    $s_time = isset($jp_start_times[$start]) ? $jp_start_times[$start] : '07:30';
    $e_time = isset($jp_end_times[$end]) ? $jp_end_times[$end] : '15:15';
    
    return $s_time . ' - ' . $e_time;
}

function parse_jp_span($jamke_str) {
    $cleaned = str_replace(' ', '', $jamke_str);
    if (strpos($cleaned, '-') !== false) {
        $parts = explode('-', $cleaned);
        return [(int)$parts[0], (int)$parts[1]];
    }
    return [(int)$cleaned, (int)$cleaned];
}

function is_jp_overlapping($start1, $end1, $start2, $end2) {
    return max($start1, $start2) <= min($end1, $end2);
}
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Jadwal</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="#">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Jadwal Mengajar</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Tambah Jadwal</a>
            </li>
        </ul>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="card-title text-white"><i class="fas fa-calendar-plus mr-2"></i>Tambah Jadwal Mengajar Baru</div>
                </div>
                <div class="card-body">
                    <form action="" method="post">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kode">Kode Pelajaran</label>
                                    <input name="kode" type="text" class="form-control" id="kode" value="MPL-<?=time();?>" readonly style="background-color: #f0f0f0;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tahun Pelajaran</label>
                                    <input type="hidden" name="ta" value="<?=$taAktif['id_thajaran'] ?>">
                                    <input type="hidden" name="semester" value="<?=$semAktif['id_semester'] ?>">
                                    <input type="text" class="form-control" placeholder="<?=$taAktif['tahun_ajaran'] ?>" readonly style="background-color: #f0f0f0;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kode">Semester</label>
                                    <input type="text" class="form-control" placeholder="<?=$semAktif['semester'] ?>" readonly style="background-color: #f0f0f0;">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Guru Mata Pelajaran</label>
                                    <select name="guru" id="guru_select" class="form-control" onchange="updateSubject()" required>
                                        <option value="">- Pilih Guru -</option>
                                        <?php 
                                        $guru = mysqli_query($con,"SELECT tb_guru.*, tb_master_mapel.mapel FROM tb_guru LEFT JOIN tb_master_mapel ON tb_guru.id_mapel = tb_master_mapel.id_mapel ORDER BY tb_guru.nama_guru ASC");
                                        foreach ($guru as $g) {
                                            $mapelName = !empty($g['mapel']) ? htmlspecialchars($g['mapel'], ENT_QUOTES) : '';
                                            $mapelId = !empty($g['id_mapel']) ? $g['id_mapel'] : '';
                                            echo "<option value='$g[id_guru]' data-mapel-id='$mapelId' data-mapel-name='$mapelName'>$g[nama_guru]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Mata Pelajaran</label>
                                    <input type="text" id="mapel_display" class="form-control" placeholder="Pilih guru untuk melihat mata pelajaran..." readonly style="background-color: #f0f0f0;">
                                    <input type="hidden" name="mapel" id="mapel_hidden" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">											
                                <div class="form-check">
                                    <label class="font-weight-bold">Hari</label><br/>
                                    <?php 
                                    $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    foreach ($hari_list as $h) {
                                    ?>
                                        <label class="form-radio-label mr-3">
                                            <input class="form-radio-input" type="radio" name="hari" value="<?= $h; ?>" required>
                                            <span class="form-radio-sign"><?= $h; ?></span>
                                        </label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-6">	
                                <label class="font-weight-bold">Kelas Siswa</label>
                                <select name="kelas" class="form-control" required>
                                    <option value="">- Pilih Kelas -</option>
                                    <?php 
                                    $kelas = mysqli_query($con,"SELECT * FROM tb_mkelas ORDER BY nama_kelas ASC");
                                    foreach ($kelas as $k) {
                                        echo "<option value='$k[id_mkelas]'>Kelas $k[nama_kelas]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Standardized Jam Pelajaran Selection Row -->
                        <div class="row mt-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef; margin: 10px 0;">
                            <div class="col-md-12">
                                <label class="font-weight-bold text-primary"><i class="far fa-clock mr-1"></i> Atur Jam Pelajaran (JP)</label>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label>Dari JP Ke</label>
                                    <select name="start_jp" id="start_jp" class="form-control" onchange="updateJPTime()" required>
                                        <?php for($i=1; $i<=9; $i++) { echo "<option value='$i'>JP $i</option>"; } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label>Sampai JP Ke</label>
                                    <select name="end_jp" id="end_jp" class="form-control" onchange="updateJPTime()" required>
                                        <?php for($i=1; $i<=9; $i++) { echo "<option value='$i'>JP $i</option>"; } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                <div class="small font-weight-bold text-uppercase text-muted">Estimasi Waktu:</div>
                                <div class="h5 font-weight-bold text-dark mb-0" id="time-preview">07:30 - 08:15</div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" name="save" class="btn btn-primary">
                                        <i class="far fa-save mr-1"></i> Simpan Jadwal
                                    </button>
                                    <a href="javascript:history.back()" class="btn btn-danger">
                                        <i class="fas fa-angle-double-left mr-1"></i> Batal / Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <?php 
                    if (isset($_POST['save'])) {
                        $kode = $_POST['kode'];
                        $ta = $_POST['ta'];
                        $semester = $_POST['semester'];
                        $guru = $_POST['guru'];
                        $mapel = $_POST['mapel'];
                        $hari = $_POST['hari'];
                        $kelas = $_POST['kelas'];
                        $start_jp = (int)$_POST['start_jp'];
                        $end_jp = (int)$_POST['end_jp'];

                        // 1. JP Span validation: cannot end before start
                        if ($end_jp < $start_jp) {
                            echo "
                            <script type='text/javascript'>
                            setTimeout(function () { 
                                swal('Input Tidak Valid', 'Sampai JP tidak boleh lebih kecil dari Dari JP!', {
                                    icon : 'error',
                                    buttons: { confirm: { className : 'btn btn-danger' } }
                                });    
                            }, 10);
                            </script>";
                        } else {
                            $jamke = ($start_jp == $end_jp) ? "$start_jp" : "$start_jp - $end_jp";
                            $waktu = get_jp_time($start_jp, $end_jp);

                            // Fetch teacher name and class name for user-friendly notifications
                            $g_data = mysqli_fetch_array(mysqli_query($con, "SELECT nama_guru FROM tb_guru WHERE id_guru='$guru'"));
                            $k_data = mysqli_fetch_array(mysqli_query($con, "SELECT nama_kelas FROM tb_mkelas WHERE id_mkelas='$kelas'"));
                            $m_data = mysqli_fetch_array(mysqli_query($con, "SELECT mapel FROM tb_master_mapel WHERE id_mapel='$mapel'"));

                            // 2. Schedule Clash Validation - Teacher
                            $teacher_clash = false;
                            $check_t = mysqli_query($con, "SELECT * FROM tb_mengajar 
                                INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas
                                WHERE id_guru = '$guru' AND hari = '$hari' 
                                AND id_semester = '$semester' AND id_thajaran = '$ta'");
                            while ($row = mysqli_fetch_array($check_t)) {
                                list($s2, $e2) = parse_jp_span($row['jamke']);
                                if (is_jp_overlapping($start_jp, $end_jp, $s2, $e2)) {
                                    $teacher_clash = $row;
                                    break;
                                }
                            }

                            // 3. Schedule Clash Validation - Class
                            $class_clash = false;
                            $check_c = mysqli_query($con, "SELECT * FROM tb_mengajar 
                                INNER JOIN tb_guru ON tb_mengajar.id_guru = tb_guru.id_guru
                                INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel
                                WHERE tb_mengajar.id_mkelas = '$kelas' AND hari = '$hari' 
                                AND id_semester = '$semester' AND id_thajaran = '$ta'");
                            while ($row = mysqli_fetch_array($check_c)) {
                                list($s2, $e2) = parse_jp_span($row['jamke']);
                                if (is_jp_overlapping($start_jp, $end_jp, $s2, $e2)) {
                                    $class_clash = $row;
                                    break;
                                }
                            }

                            // 4. Duplicate Subject Validation - Same subject for same class in the active semester
                            $subject_clash = false;
                            $check_s = mysqli_query($con, "SELECT * FROM tb_mengajar 
                                INNER JOIN tb_guru ON tb_mengajar.id_guru = tb_guru.id_guru
                                INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel
                                WHERE tb_mengajar.id_mkelas = '$kelas' AND tb_mengajar.id_mapel = '$mapel'
                                AND id_semester = '$semester' AND id_thajaran = '$ta'");
                            if (mysqli_num_rows($check_s) > 0) {
                                $subject_clash = mysqli_fetch_array($check_s);
                            }

                            if ($teacher_clash) {
                                echo "
                                <script type='text/javascript'>
                                setTimeout(function () { 
                                    swal('Bentrok Guru!', 'Guru " . addslashes($g_data['nama_guru']) . " sudah dijadwalkan mengajar di Kelas " . addslashes($teacher_clash['nama_kelas']) . " pada Jam Ke " . $teacher_clash['jamke'] . " hari " . $hari . "!', 'error');
                                }, 10);
                                </script>";
                            } elseif ($class_clash) {
                                echo "
                                <script type='text/javascript'>
                                setTimeout(function () { 
                                    swal('Bentrok Kelas!', 'Kelas " . addslashes($k_data['nama_kelas']) . " sudah diisi pelajaran " . addslashes($class_clash['mapel']) . " oleh Guru " . addslashes($class_clash['nama_guru']) . " pada Jam Ke " . $class_clash['jamke'] . " hari " . $hari . "!', 'error');
                                }, 10);
                                </script>";
                            } elseif ($subject_clash) {
                                echo "
                                <script type='text/javascript'>
                                setTimeout(function () { 
                                    swal('Mata Pelajaran Sudah Ada!', 'Mata Pelajaran " . addslashes($m_data['mapel']) . " sudah diajarkan di Kelas " . addslashes($k_data['nama_kelas']) . " oleh Guru " . addslashes($subject_clash['nama_guru']) . " pada semester ini!', 'error');
                                }, 10);
                                </script>";
                            } else {
                                // Save the schedule
                                $insert = mysqli_query($con, "INSERT INTO tb_mengajar VALUES (NULL, '$kode', '$hari', '$waktu', '$jamke', '$guru', '$mapel', '$kelas', '$semester', '$ta')");
                                if ($insert) {
                                    echo "
                                    <script type='text/javascript'>
                                    setTimeout(function () { 
                                        swal('Sukses', 'Jadwal mengajar berhasil ditambahkan!', 'success');
                                    }, 10);  
                                    window.setTimeout(function(){ 
                                        window.location.replace('?page=jadwal');
                                    }, 2500);   
                                    </script>";
                                } else {
                                    echo "<script>alert('Gagal menyimpan jadwal: " . mysqli_error($con) . "');</script>";
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Timetable Info Helper Box -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <div class="card-title text-dark"><i class="fas fa-info-circle mr-2"></i>Panduan Jam Pelajaran</div>
                </div>
                <div class="card-body">
                    <p class="small">Satu Jam Pelajaran (JP) setara dengan <b>45 menit</b>. Berikut adalah pembagian waktu standarnya:</p>
                    <table class="table table-bordered table-striped table-sm text-center small">
                        <thead>
                            <tr class="bg-light">
                                <th>JP</th>
                                <th>Rentang Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>JP 1</td><td>07:30 - 08:15</td></tr>
                            <tr><td>JP 2</td><td>08:15 - 09:00</td></tr>
                            <tr><td>JP 3</td><td>09:00 - 09:45</td></tr>
                            <tr class="bg-light font-weight-bold text-muted"><td colspan="2">Istirahat Pagi (09:45 - 10:15)</td></tr>
                            <tr><td>JP 4</td><td>10:15 - 11:00</td></tr>
                            <tr><td>JP 5</td><td>11:00 - 11:45</td></tr>
                            <tr><td>JP 6</td><td>11:45 - 12:30</td></tr>
                            <tr class="bg-light font-weight-bold text-muted"><td colspan="2">Istirahat Sholat/Siang (12:30 - 13:00)</td></tr>
                            <tr><td>JP 7</td><td>13:00 - 13:45</td></tr>
                            <tr><td>JP 8</td><td>13:45 - 14:30</td></tr>
                            <tr><td>JP 9</td><td>14:30 - 15:15</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle real-time time preview calculation -->
<script>
function updateJPTime() {
    var start_jp = parseInt(document.getElementById('start_jp').value);
    var end_jp = parseInt(document.getElementById('end_jp').value);
    
    var jp_start_times = {
        1: '07:30', 2: '08:15', 3: '09:00',
        4: '10:15', 5: '11:00', 6: '11:45',
        7: '13:00', 8: '13:45', 9: '14:30'
    };
    
    var jp_end_times = {
        1: '08:15', 2: '09:00', 3: '09:45',
        4: '11:00', 5: '11:45', 6: '12:30',
        7: '13:45', 8: '14:30', 9: '15:15'
    };
    
    if (end_jp < start_jp) {
        document.getElementById('time-preview').innerHTML = '<span class="text-danger small font-italic">JP tidak valid!</span>';
        return;
    }
    
    var s_time = jp_start_times[start_jp];
    var e_time = jp_end_times[end_jp];
    
    document.getElementById('time-preview').innerHTML = s_time + ' - ' + e_time;
}

function updateSubject() {
    var select = document.getElementById('guru_select');
    var selectedOption = select.options[select.selectedIndex];
    var mapelName = selectedOption.getAttribute('data-mapel-name');
    var mapelId = selectedOption.getAttribute('data-mapel-id');
    
    var display = document.getElementById('mapel_display');
    var hidden = document.getElementById('mapel_hidden');
    
    if (mapelId && mapelName) {
        display.value = mapelName;
        hidden.value = mapelId;
    } else {
        display.value = select.value ? 'Guru belum memiliki mata pelajaran!' : 'Pilih guru untuk melihat mata pelajaran...';
        hidden.value = '';
    }
}

// Run initial time calculation on load
window.onload = function() {
    updateJPTime();
    updateSubject();
};
</script>
