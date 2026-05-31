<?php 
// tampilkan data mengajar
$kelasMengajar = mysqli_query($con,"SELECT * FROM tb_mengajar 
INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel=tb_master_mapel.id_mapel
INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas=tb_mkelas.id_mkelas
INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
WHERE tb_mengajar.id_guru='$data[id_guru]' AND tb_mengajar.id_mengajar='$_GET[pelajaran]'  AND tb_thajaran.status=1  ");

$d = mysqli_fetch_array($kelasMengajar);
?>

<div class="page-inner">
    <div class="page-header">
        <ul class="breadcrumbs font-weight-bold" style="font-size: 13px;">
            <li class="nav-home">
                <a href="index.php"><i class="flaticon-home"></i></a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item">
                <a href="#">Absensi</a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item">
                <a href="#">Kelas <?=strtoupper($d['nama_kelas']) ?></a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item">
                <a href="#"><?=strtoupper($d['mapel']) ?></a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item">
                <a href="#">Update Absensi</a>
            </li>
        </ul>
    </div>

    <!-- Custom Premium CSS for Attendance Table -->
    <style>
        .presence-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.06);
            border: none;
            overflow: hidden;
        }
        .presence-card-header {
            background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
            color: #fff;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .presence-table th {
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #444 !important;
            vertical-align: middle !important;
        }
        .presence-table td {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #222 !important;
            vertical-align: middle !important;
        }
        
        /* Modern Radio Badges */
        .presence-option {
            display: inline-block;
            margin: 0 4px;
            cursor: pointer;
        }
        .presence-radio {
            display: none !important;
        }
        .presence-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #ddd;
            font-weight: 700;
            font-size: 12px;
            color: #777;
            transition: all 0.2s ease;
        }
        
        /* Hover triggers specific border color */
        .presence-option:hover .presence-badge {
            border-color: currentColor;
            color: currentColor;
        }
        
        /* Predefined colors for selected states */
        .presence-option.text-success .presence-radio:checked + .presence-badge {
            background-color: #4caf50;
            border-color: #4caf50;
            color: #fff;
            box-shadow: 0 3px 8px rgba(76, 175, 80, 0.35);
        }
        .presence-option.text-warning .presence-radio:checked + .presence-badge {
            background-color: #ffb300;
            border-color: #ffb300;
            color: #fff;
            box-shadow: 0 3px 8px rgba(255, 179, 0, 0.35);
        }
        .presence-option.text-info .presence-radio:checked + .presence-badge {
            background-color: #2196f3;
            border-color: #2196f3;
            color: #fff;
            box-shadow: 0 3px 8px rgba(33, 150, 243, 0.35);
        }
        .presence-option.text-secondary .presence-radio:checked + .presence-badge {
            background-color: #9c27b0;
            border-color: #9c27b0;
            color: #fff;
            box-shadow: 0 3px 8px rgba(156, 39, 176, 0.35);
        }
        .presence-option.text-danger .presence-radio:checked + .presence-badge {
            background-color: #f44336;
            border-color: #f44336;
            color: #fff;
            box-shadow: 0 3px 8px rgba(244, 67, 54, 0.35);
        }
        .presence-option.text-muted .presence-radio:checked + .presence-badge {
            background-color: #757575;
            border-color: #757575;
            color: #fff;
            box-shadow: 0 3px 8px rgba(117, 117, 117, 0.35);
        }

        .avatar-frame-mini {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
            background: #eee;
            border: 1px solid #ddd;
        }
    </style>

    <div class="row">
        <!-- MAIN ATTENDANCE FORM CARD -->
        <div class="col-md-12">
            <?php 
            // Calculate active pertemuan ke
            $last_pertemuan = mysqli_query($con,"SELECT DISTINCT pertemuan_ke FROM _logabsensi WHERE id_mengajar='$_GET[pelajaran]' ORDER BY pertemuan_ke DESC LIMIT 1  ");
            $cekPertemuan = mysqli_num_rows($last_pertemuan);
            $jml = mysqli_fetch_array($last_pertemuan);
            $pertemuan = ($cekPertemuan > 0) ? $jml['pertemuan_ke'] : 1;
            ?>

            <div class="presence-card">
                <div class="presence-card-header">
                    <div>
                        <h4 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-edit mr-2"></i>Edit Absensi Kelas <?=strtoupper($d['nama_kelas']) ?></h4>
                        <p class="mb-0 small text-white-50 font-weight-bold">Mata Pelajaran: <?=strtoupper($d['mapel']) ?> &bull; Koreksi kehadiran hari ini</p>
                    </div>
                    <span class="badge badge-light font-weight-bold px-3 py-2 text-dark" style="border-radius: 30px; font-size: 13px;">
                        <i class="fas fa-bookmark mr-1"></i> Pertemuan Ke: <?= $pertemuan; ?>
                    </span>
                </div>

                <div class="card-body p-4">
                    <form action="" method="post">
                        <!-- Hidden fields -->
                        <input type="hidden" name="pertemuan" value="<?=$pertemuan; ?>">
                        <input type="hidden" name="pelajaran" value="<?=$_GET['pelajaran'] ?>">

                        <!-- Date & Quick Actions -->
                        <div class="row align-items-center mb-4">
                            <div class="col-md-4">
                                <label class="font-weight-bold text-muted small text-uppercase mb-1 d-block"><i class="far fa-calendar-alt mr-1"></i> Tanggal Absensi</label>
                                <input type="date" name="tgl" class="form-control" value="<?=date('Y-m-d') ?>" disabled style="border-radius: 8px; font-weight: 700; background: #e9ecef; cursor: not-allowed;">
                            </div>
                            <div class="col-md-8 text-md-right mt-3 mt-md-0">
                                <button type="button" class="btn btn-outline-success btn-round btn-sm font-weight-bold" onclick="setAllHadir();" style="border: 2px solid; padding: 6px 16px;">
                                    <i class="fas fa-check-double mr-1"></i> Set Semua Hadir (H)
                                </button>
                            </div>
                        </div>

                        <!-- Main Table -->
                        <div class="table-responsive">
                            <table class="table table-hover presence-table" style="width: 100%;">
                                <thead>
                                    <tr class="bg-light">
                                        <th style="width: 60px;" class="text-center">No</th>
                                        <th style="width: 50px;">Foto</th>
                                        <th>Nama Lengkap Siswa</th>
                                        <th style="width: 80px;" class="text-center">L/P</th>
                                        <th style="width: 320px; text-align: center;">Kehadiran (Pilih Salah Satu)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $tgl_hari_ini = date('Y-m-d');
                                    $siswa = mysqli_query($con,"SELECT * FROM _logabsensi INNER JOIN tb_siswa ON _logabsensi.id_siswa=tb_siswa.id_siswa WHERE _logabsensi.tgl_absen='$tgl_hari_ini' AND _logabsensi.id_mengajar='$_GET[pelajaran]' ORDER BY tb_siswa.nama_siswa ASC  ");
                                    $jumlahSiswa = mysqli_num_rows($siswa);
                                    
                                    if ($jumlahSiswa == 0) {
                                        echo '<tr><td colspan="5" class="text-center py-5 text-muted">Belum ada catatan absensi yang diinput hari ini</td></tr>';
                                    }

                                    foreach ($siswa as $i => $s) {
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $i + 1; ?></td>
                                            <td>
                                                <div class="avatar-frame-mini">
                                                    <img src="../assets/img/user/<?=$s['foto'] ?>" alt="..." style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td>
                                                <span class="d-block text-dark font-weight-bold"><?=$s['nama_siswa'] ?></span>
                                                <small class="text-muted">NISN: <?=$s['nis'] ?></small>
                                                <input type="hidden" name="id_siswa-<?=$i;?>" value="<?=$s['id_siswa'] ?>">
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light font-weight-bold" style="border-radius: 5px;"><?=strtoupper($s['jk']) ?></span>
                                            </td>
                                            <td>
                                                <!-- Radio Group -->
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <label class="presence-option text-success" title="Hadir">
                                                        <input type="radio" name="ket-<?=$i;?>" value="H" class="presence-radio" <?= ($s['ket'] == 'H') ? 'checked' : ''; ?>>
                                                        <span class="presence-badge">H</span>
                                                    </label>
                                                    <label class="presence-option text-warning" title="Izin">
                                                        <input type="radio" name="ket-<?=$i;?>" value="I" class="presence-radio" <?= ($s['ket'] == 'I') ? 'checked' : ''; ?>>
                                                        <span class="presence-badge">I</span>
                                                    </label>
                                                    <label class="presence-option text-info" title="Sakit">
                                                        <input type="radio" name="ket-<?=$i;?>" value="S" class="presence-radio" <?= ($s['ket'] == 'S') ? 'checked' : ''; ?>>
                                                        <span class="presence-badge">S</span>
                                                    </label>
                                                    <label class="presence-option text-secondary" title="Terlambat">
                                                        <input type="radio" name="ket-<?=$i;?>" value="T" class="presence-radio" <?= ($s['ket'] == 'T') ? 'checked' : ''; ?>>
                                                        <span class="presence-badge">T</span>
                                                    </label>
                                                    <label class="presence-option text-danger" title="Alfa">
                                                        <input type="radio" name="ket-<?=$i;?>" value="A" class="presence-radio" <?= ($s['ket'] == 'A') ? 'checked' : ''; ?>>
                                                        <span class="presence-badge">A</span>
                                                    </label>
                                                    <label class="presence-option text-muted" title="Cabut">
                                                        <input type="radio" name="ket-<?=$i;?>" value="C" class="presence-radio" <?= ($s['ket'] == 'C') ? 'checked' : ''; ?>>
                                                        <span class="presence-badge">C</span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Form Submission -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <button type="submit" name="update" class="btn btn-success btn-round px-5 font-weight-bold">
                                <i class="fa fa-check mr-1"></i> Simpan Perubahan Absensi
                            </button>
                            <a href="javascript:history.back()" class="btn btn-light btn-round px-4 ml-2" style="border: 1px solid #ddd; color: #444;">
                                <i class="fas fa-arrow-circle-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Helper script to mark all Hadir -->
<script>
function setAllHadir() {
    var radios = document.querySelectorAll('input[type="radio"][value="H"]');
    radios.forEach(function(radio) {
        radio.checked = true;
    });
}
</script>

<?php 
if (isset($_POST['update'])) {
    $total = $jumlahSiswa - 1;
    $hari_sekarang = date('Y-m-d');
    $success_flag = true;

    for ($i = 0; $i <= $total; $i++) {
        $id_siswa = $_POST['id_siswa-' . $i];
        $pelajaran = $_POST['pelajaran'];
        $ket = $_POST['ket-' . $i];

        if (empty($ket)) {
            $ket = 'A';
        }

        $updte_absen = mysqli_query($con,"UPDATE _logabsensi SET ket='$ket' WHERE id_mengajar='$pelajaran' AND id_siswa='$id_siswa' AND tgl_absen='$hari_sekarang' ");
        if (!$updte_absen) {
            $success_flag = false;
        }
    }

    if ($success_flag) {
        echo "
        <script type='text/javascript'>
        setTimeout(function () { 
            swal('Berhasil!', 'Absensi hari ini berhasil diperbarui!', {
                icon : 'success',
                buttons: { confirm: { className : 'btn btn-success' } }
            });    
        }, 10);  
        window.setTimeout(function(){ 
            window.location.replace('?page=absen&act=update&pelajaran=$_GET[pelajaran]');
        }, 3000);   
        </script>";
    } else {
        echo "
        <script type='text/javascript'>
        setTimeout(function () { 
            swal('Gagal!', 'Terjadi kesalahan saat memperbarui absensi!', {
                icon : 'error',
                buttons: { confirm: { className : 'btn btn-danger' } }
            });    
        }, 10);  
        </script>";
    }
}
?>