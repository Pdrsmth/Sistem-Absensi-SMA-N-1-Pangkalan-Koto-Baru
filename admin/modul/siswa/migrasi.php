<?php
// admin/modul/siswa/migrasi.php

// Fetch classes for dropdowns
$classes = mysqli_query($con, "SELECT * FROM tb_mkelas ORDER BY id_mkelas ASC");

$kelas_asal = isset($_GET['kelas_asal']) ? mysqli_real_escape_string($con, $_GET['kelas_asal']) : '';
$kelas_tujuan = isset($_GET['kelas_tujuan']) ? mysqli_real_escape_string($con, $_GET['kelas_tujuan']) : '';

// Process migration request
if (isset($_POST['proses_migrasi'])) {
    $asal = mysqli_real_escape_string($con, $_POST['asal']);
    $tujuan = mysqli_real_escape_string($con, $_POST['tujuan']);
    $siswa_ids = isset($_POST['siswa']) ? $_POST['siswa'] : [];

    if (empty($siswa_ids)) {
        echo "<script>alert('Pilih minimal satu siswa untuk dimigrasikan!'); window.location='?page=migrasi&kelas_asal=$asal&kelas_tujuan=$tujuan';</script>";
        exit();
    }

    $success_count = 0;
    foreach ($siswa_ids as $id_raw) {
        $id_siswa = intval($id_raw);
        if ($tujuan === 'lulus') {
            // Deactivate and mark as graduated (status = 0)
            $query = "UPDATE tb_siswa SET status = '0' WHERE id_siswa = $id_siswa";
        } else {
            // Update class
            $tujuan_escaped = intval($tujuan);
            $query = "UPDATE tb_siswa SET id_mkelas = $tujuan_escaped WHERE id_siswa = $id_siswa";
        }
        
        if (mysqli_query($con, $query)) {
            $success_count++;
        }
    }

    $msg = "Berhasil memproses kenaikan/migrasi $success_count siswa!";
    echo "
    <script type='text/javascript'>
    setTimeout(function () { 
        swal('Migrasi Berhasil', '$msg', {
            icon : 'success',
            buttons: { confirm: { className : 'btn btn-success' } }
        });    
    }, 10);  
    window.setTimeout(function(){ 
        window.location.replace('?page=siswa');
    }, 3000);   
    </script>";
    exit();
}
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Kenaikan & Migrasi Kelas</h4>
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
                <a href="#">Siswa</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Kenaikan Kelas Massal</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="card-title">
                        <i class="fas fa-random mr-2"></i> Form Migrasi / Kenaikan Kelas Massal
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="" class="form-horizontal">
                        <input type="hidden" name="page" value="migrasi">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">1. Pilih Kelas Asal (Sumber)</label>
                                    <select name="kelas_asal" class="form-control" onchange="this.form.submit()" required>
                                        <option value="" disabled selected hidden>-- Pilih Kelas Asal --</option>
                                        <?php foreach ($classes as $c) { 
                                            $sel = ($kelas_asal == $c['id_mkelas']) ? 'selected' : '';
                                        ?>
                                            <option value="<?=$c['id_mkelas'];?>" <?=$sel;?>><?=strtoupper($c['nama_kelas']);?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">2. Pilih Kelas Tujuan (Kenaikan)</label>
                                    <select name="kelas_tujuan" class="form-control" required>
                                        <option value="" disabled selected hidden>-- Pilih Kelas Tujuan --</option>
                                        <option value="lulus" <?=($kelas_tujuan == 'lulus') ? 'selected' : '';?>>🏆 LULUS / ALUMNI (Nonaktifkan Akun)</option>
                                        <?php 
                                        // Reset classes query pointer
                                        mysqli_data_seek($classes, 0);
                                        foreach ($classes as $c) { 
                                            $sel = ($kelas_tujuan == $c['id_mkelas']) ? 'selected' : '';
                                        ?>
                                            <option value="<?=$c['id_mkelas'];?>" <?=$sel;?>><?=strtoupper($c['nama_kelas']);?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end mb-3">
                                <button type="button" class="btn btn-secondary btn-block font-weight-bold" onclick="loadSiswaList()">
                                    <i class="fas fa-users-cog mr-2"></i> Tampilkan Daftar Siswa
                                </button>
                            </div>
                        </div>
                    </form>

                    <script>
                        function loadSiswaList() {
                            const asal = document.querySelector('select[name="kelas_asal"]').value;
                            const tujuan = document.querySelector('select[name="kelas_tujuan"]').value;
                            if (!asal || !tujuan) {
                                swal('Peringatan', 'Harap pilih Kelas Asal dan Kelas Tujuan terlebih dahulu!', 'warning');
                                return;
                            }
                            window.location.href = `?page=migrasi&kelas_asal=${asal}&kelas_tujuan=${tujuan}`;
                        }
                    </script>

                    <?php if (!empty($kelas_asal) && !empty($kelas_tujuan)) { 
                        // Query students from source class
                        $students = mysqli_query($con, "SELECT * FROM tb_siswa WHERE id_mkelas = '$kelas_asal' AND status = 1 ORDER BY nama_siswa ASC");
                        $num_students = mysqli_num_rows($students);
                        
                        $asal_name = '';
                        $tujuan_name = ($kelas_tujuan === 'lulus') ? 'LULUS / ALUMNI' : '';
                        
                        mysqli_data_seek($classes, 0);
                        foreach ($classes as $c) {
                            if ($c['id_mkelas'] == $kelas_asal) $asal_name = strtoupper($c['nama_kelas']);
                            if ($c['id_mkelas'] == $kelas_tujuan) $tujuan_name = strtoupper($c['nama_kelas']);
                        }
                    ?>
                        <hr>
                        <div class="alert alert-info d-flex align-items-center" role="alert" style="border-left: 5px solid #17a2b8; background: #e7f8fa;">
                            <i class="fas fa-info-circle mr-3 fa-2x text-info"></i>
                            <div class="text-dark">
                                Mentransfer siswa dari kelas <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 13px;"><?=$asal_name;?></span> ke kelas <span class="badge badge-success font-weight-bold text-white" style="font-size: 13px;"><?=$tujuan_name;?></span>. 
                                Silakan tinjau daftar siswa di bawah. <strong>Hapus centang</strong> untuk siswa yang tinggal kelas atau tidak lulus.
                            </div>
                        </div>

                        <form method="post" action="" onsubmit="return confirmProsesMigrasi()">
                            <input type="hidden" name="asal" value="<?=$kelas_asal;?>">
                            <input type="hidden" name="tujuan" value="<?=$kelas_tujuan;?>">

                            <div class="table-responsive mt-4">
                                <table class="table table-striped table-hover">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th style="width: 8%">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="checkAll" checked>
                                                    <label class="custom-control-label" for="checkAll"></label>
                                                </div>
                                            </th>
                                            <th style="width: 8%">No</th>
                                            <th style="width: 25%">NISN / NIS</th>
                                            <th style="width: 45%">Nama Lengkap Siswa</th>
                                            <th style="width: 14%">Jenis Kelamin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($num_students > 0) {
                                            $no = 1;
                                            while ($s = mysqli_fetch_array($students)) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="siswa[]" value="<?=$s['id_siswa'];?>" class="custom-control-input checkbox-item" id="siswa_<?=$s['id_siswa'];?>" checked>
                                                        <label class="custom-control-label" for="siswa_<?=$s['id_siswa'];?>"></label>
                                                    </div>
                                                </td>
                                                <td><?=$no++;?></td>
                                                <td><span class="badge badge-secondary" style="font-size: 11.5px;"><?=$s['nis'];?></span></td>
                                                <td><strong class="text-dark" style="font-size: 13.5px;"><?=$s['nama_siswa'];?></strong></td>
                                                <td>
                                                    <?php if (strtoupper($s['jk']) == 'L' || strtoupper($s['jk']) == 'LAKI-LAKI') { ?>
                                                        <span class="badge badge-info"><i class="fas fa-mars mr-1"></i> L</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-danger" style="background-color: #e83e8c !important;"><i class="fas fa-venus mr-1"></i> P</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } } else { ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted p-5">
                                                    <i class="fas fa-user-slash fa-3x mb-3 d-block text-secondary"></i>
                                                    Tidak ada siswa aktif di kelas <strong><?=$asal_name;?></strong>.
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($num_students > 0) { ?>
                                <div class="mt-4 text-right">
                                    <button type="submit" name="proses_migrasi" class="btn btn-success btn-lg px-4" style="font-size: 14.5px; border-radius: 8px; box-shadow: 0 4px 12px rgba(40,167,69,0.3);">
                                        <i class="fas fa-check-circle mr-2"></i> Proses Migrasi Kelas Massal
                                    </button>
                                </div>
                            <?php } ?>
                        </form>

                        <script>
                            // Toggle all checkboxes
                            document.getElementById('checkAll').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('.checkbox-item');
                                checkboxes.forEach(cb => cb.checked = this.checked);
                            });

                            function confirmProsesMigrasi() {
                                const checkedCount = document.querySelectorAll('.checkbox-item:checked').length;
                                if (checkedCount === 0) {
                                    swal('Kesalahan', 'Harap pilih minimal satu siswa untuk dimigrasikan!', 'error');
                                    return false;
                                }
                                return confirm(`Apakah Anda yakin ingin memproses kenaikan/migrasi kelas untuk ${checkedCount} siswa ini?`);
                            }
                        </script>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
