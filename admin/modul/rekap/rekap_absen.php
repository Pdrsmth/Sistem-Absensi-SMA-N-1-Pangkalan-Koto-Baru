<?php 
// Cek apakah form tanggal telah di-submit
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01'); // Default: awal bulan ini
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t'); // Default: akhir bulan ini

// Query untuk mengambil data mengajar dan absensi berdasarkan rentang tanggal yang dipilih
// $kelasMengajar = mysqli_query($con, "SELECT * FROM _logabsensi 
// INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel
// INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas
// INNER JOIN tb_guru ON tb_mengajar.id_guru = tb_guru.id_guru
// INNER JOIN tb_semester ON tb_mengajar.id_semester = tb_semester.id_semester
// INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran = tb_thajaran.id_thajaran
// INNER JOIN tb_absensi ON tb_mengajar.id_mengajar = tb_absensi.id_mengajar
// WHERE tb_mengajar.id_mkelas = '$_GET[kelas]' 
// AND tb_thajaran.status = 1 
// AND tb_semester.id_semester = 1
// AND tb_absensi.tanggal BETWEEN '$startDate' AND '$endDate'");

$kelasMengajar = mysqli_query($con, "SELECT * FROM tb_master_mapel
-- SELECT * FROM tb_mengajar
INNER JOIN tb_mkelas ON tb_mkelas.id_mkelas = '{$_GET['kelas']}'
-- INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel
");

// INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas=tb_mkelas.id_mkelas
$namaKelas = null;

// Cek apakah ada data yang diambil
//while ($row = $kelasMengajar->fetch_assoc()) {
    // print_r($row);
//}


if ($kelasMengajar && mysqli_num_rows($kelasMengajar) > 0) {
    foreach ($kelasMengajar as $dat) {
        $namaKelas = $dat['nama_kelas'] ?? 'Kelas Tidak Diketahui';
    }
} else {
    $namaKelas = 'Kelas Tidak Diketahui 1';
}
?>


<div class="page-inner">

    <div class="page-header">
        <h4 class="page-title">Rekap Absen</h4> 
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
                <a href="#">KELAS (<?= strtoupper($namaKelas) ?>)</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
        </ul>
    </div>

    <!-- Form untuk memilih rentang tanggal -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="start_date">Tanggal Mulai:</label>
            <input type="date" name="start_date" id="start_date" value="<?= $startDate ?>" required>
        </div>
        <div class="form-group">
            <label for="end_date">Tanggal Akhir:</label>
            <input type="date" name="end_date" id="end_date" value="<?= $endDate ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan Rekap</button>
    </form>

    <div class="row">
        <div class="col-md-12 col-xs-12">    
            <div class="card">
                <div class="card-body">
                    <table class="table table-head-bg-danger table-xs">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th>Kode Pelajaran</th>
                                <th scope="col">Mata Pelajaran</th>
                                <th scope="col">Absensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($kelasMengajar as $mp) { 
                                //print_r($mp); ?>
                                <tr>
                                    <td><?= $no++; ?>.</td>
                                    <td><?= $mp['kode_mapel']; ?></td>
                                    <td>
                                        <b><?= $mp['mapel']; ?></b><br>
                                        <code><?= $mp['nama_guru']; ?></code>
                                    </td>
                                    <td>
                                        <a href="?page=rekap&act=rekap-perbulan&pelajaran=<?= $mp['id_mapel'] ?>&kelas=<?= $_GET['kelas'] ?>" class="btn btn-default">
                                            <span class="btn-label">
                                                <i class="fas fa-clipboard"></i>
                                            </span>
                                            Rekap Absen
                                        </a>
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