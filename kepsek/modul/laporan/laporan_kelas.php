<?php
// If kelas is not selected, show list of classes
if (empty($_GET['kelas'])) {
?>
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Laporan Presensi</h4>
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
                <a href="#">Pilih Kelas</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <?php
        $kelas = mysqli_query($con, "SELECT * FROM tb_mkelas ORDER BY id_mkelas ASC");
        while ($k = mysqli_fetch_array($kelas)) {
            // Count students in this class
            $jmlSiswa = mysqli_num_rows(mysqli_query($con, "SELECT * FROM tb_siswa WHERE id_mkelas='$k[id_mkelas]' AND status=1"));
        ?>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">Kelas</p>
                                <h4 class="card-title"><?= strtoupper($k['nama_kelas']); ?></h4>
                                <span class="badge badge-primary"><?= $jmlSiswa; ?> Siswa</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <a href="?page=laporan&kelas=<?= $k['id_mkelas']; ?>" class="btn btn-primary btn-block btn-sm">
                        <i class="fa fa-folder-open"></i> Buka Laporan
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php
} else {
    // If kelas is selected, show list of subjects/lessons in that class
    $id_kelas = mysqli_real_escape_string($con, $_GET['kelas']);
    $kelasInfo = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tb_mkelas WHERE id_mkelas='$id_kelas'"));
    $namaKelas = isset($kelasInfo['nama_kelas']) ? $kelasInfo['nama_kelas'] : 'Tidak Diketahui';

    $kelasMengajar = mysqli_query($con, "SELECT tb_mengajar.*, tb_master_mapel.mapel, tb_master_mapel.kode_mapel, tb_guru.nama_guru FROM tb_mengajar
        INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel
        INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas
        INNER JOIN tb_guru ON tb_mengajar.id_guru = tb_guru.id_guru
        WHERE tb_mengajar.id_mkelas = '$id_kelas'");
?>
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Laporan Presensi</h4>
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
                <a href="?page=laporan">Pilih Kelas</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Kelas <?= strtoupper($namaKelas); ?></a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="card-title text-white">Daftar Mata Pelajaran - Kelas <?= strtoupper($namaKelas); ?></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Pelajaran</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($kelasMengajar) == 0) {
                                    echo "<tr><td colspan='5' class='text-center'>Belum ada jadwal mengajar di kelas ini.</td></tr>";
                                } else {
                                    while ($mp = mysqli_fetch_array($kelasMengajar)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $mp['kode_mapel']; ?></td>
                                    <td><strong><?= $mp['mapel']; ?></strong></td>
                                    <td><?= $mp['nama_guru']; ?></td>
                                    <td>
                                        <a href="?page=laporan&act=laporan-perbulan&pelajaran=<?= $mp['id_mengajar'] ?>&kelas=<?= $id_kelas ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-clipboard-list"></i> Lihat Laporan
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="?page=laporan" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
