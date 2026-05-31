<?php 
$kelas = mysqli_real_escape_string($con, $_GET['kelas']);
$pelajaran = mysqli_real_escape_string($con, $_GET['pelajaran']);

// tampilkan data mengajar
$kelasMengajar = mysqli_query($con,"SELECT * FROM tb_mengajar 
	INNER JOIN tb_guru ON tb_mengajar.id_guru=tb_guru.id_guru
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    INNER JOIN tb_mkelas ON tb_mkelas.id_mkelas = $kelas
    INNER JOIN tb_master_mapel ON tb_master_mapel.id_mapel=tb_mengajar.id_mapel
    WHERE tb_mengajar.id_mengajar = $pelajaran
");

$d = mysqli_fetch_array($kelasMengajar);

// tampilkan data walikelas
$walikelas = mysqli_query($con,"SELECT * FROM tb_walikelas INNER JOIN tb_guru ON tb_walikelas.id_guru=tb_guru.id_guru WHERE tb_walikelas.id_mkelas=$kelas ");
$walas = mysqli_fetch_array($walikelas);
?>

<div class="page-inner">
	<div class="page-header">
	<h4 class="page-title"><?=mb_strtoupper($d['mapel']) ?> </h4> 
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
			    <a href="?page=laporan">Laporan Presensi</a>
			</li>
			<li class="separator">
			    <i class="flaticon-right-arrow"></i>
			</li>
			<li class="nav-item">
			    <a href="?page=laporan&kelas=<?=$kelas;?>">Kelas (<?=($d['nama_kelas']) ?> )</a>
			</li>
		</ul>
	</div>

<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class="card">
			<div class="card-header">
                <table width="100%">
                    <tr>
                        <td width="140" align="center">
                            <img src="../assets/img/LOGO1.jpg" width="100">
                        </td>
                        <td>
                            <center>
                                <h1>
                                    ABSENSI SISWA <br>
                                    <small>SMA NEGERI 1 PANGKALAN KOTO BARU</small>
                                </h1>
                                <hr>
                                <em>
                                    Jl. Lintas Sumbar-Riau Kec. Pangkalan Koto Baru Kab. Lima Puluh Kota <br> Provinsi Sumatera Barat, Kode Pos (26272) <br>
                                <b>Email : sma1pangkalan@gmail.com Telp. 081222333444</b> 
                                </em>
                            </center>
                        </td>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td colspan="2"><b style="border: 2px solid;padding: 7px;">
                                        KELAS ( <?=strtoupper($d['nama_kelas']) ?> )
                                    </b> </td>
                                    <td>
                                        <b style="border: 2px solid;padding: 7px;">
                                            <?=$d['semester'] ?> | <?=$d['tahun_ajaran'] ?>
                                        </b>
                                    </td>
                                    <td rowspan="5">
                                        <p class="text-info"> H = Hadir</p>
                                        <p class="text-success"> I = Izin</p>
                                        <p class="text-warning"> S = Sakit</p>
                                        <p class="text-danger"> A = Absen</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Nama Guru </td>
                                    <td>:</td>
                                    <td><?=$d['nama_guru'] ?></td>
                                </tr>
                                <tr>
                                    <td>Bidang Studi </td>
                                    <td>:</td>
                                    <td><?=$d['mapel'] ?></td>
                                </tr>
                                <tr>
                                    <td>Wali Kelas </td>
                                    <td>:</td>
                                    <td><?= isset($walas['nama_guru']) ? $walas['nama_guru'] : '-' ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

			<div class="card-body">
				<a target="_blank" href="../guru/modul/rekap/rekap_persemester.php?pelajaran=<?=$pelajaran ?>&kelas=<?=$kelas ?>" class="btn btn-default">
                    <span class="btn-label">
                        <i class="fas fa-print"></i>
                    </span>
                    REKAP (<?=strtoupper($d['semester']);?> - <?=strtoupper($d['tahun_ajaran']);?>)
                </a>

				<?php 
                // tampilkan data absen setiap bulan, berdasarkan tahun ajaran yg aktif
                $qry = mysqli_query($con,"SELECT MONTH(tgl_absen) AS bulan, MAX(tgl_absen) AS tgl_absen FROM _logabsensi
                    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar=tb_mengajar.id_mengajar
                    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
                    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
                    WHERE _logabsensi.id_mengajar='$pelajaran' and tb_thajaran.status=1 and tb_semester.status=1
                    GROUP BY MONTH(tgl_absen) ORDER BY MONTH(tgl_absen) DESC
                ");

                foreach ($qry as $bulanRow) {
                    $bulan = date('m',strtotime($bulanRow['tgl_absen']));
                    $tglTerakhir = date('t',strtotime($bulanRow['tgl_absen']));
                ?>
				<div class="alert alert-warning alert-dismissible mt-2" role="alert">
				    <b class="text-warning" style="text-transform: uppercase;">BULAN <?=namaBulan($bulan); ?> <?= date('Y') ?></b>
				    <hr>
                    <p>
                        <a target="_blank" href="../guru/modul/rekap/rekap_bulanxl.php?pelajaran=<?=$pelajaran ?>&bulan=<?=$bulan;?>&kelas=<?=$kelas ?>" class="btn btn-success">
                            <span class="btn-label"><i class="far fa-file-excel"></i></span> Export Excel
                        </a>
                        <a target="_blank" href="../guru/modul/rekap/rekap_bulan.php?pelajaran=<?=$pelajaran ?>&bulan=<?=$bulan;?>&kelas=<?=$kelas ?>" class="btn btn-default">
                            <span class="btn-label"><i class="fas fa-print"></i></span> CETAK BULAN (<?php echo strtoupper(namaBulan($bulan));?>)
                        </a>
                    </p>

                    <table width="100%" border="1" cellpadding="2" style="border-collapse: collapse; background-color: white; color: black;">
                        <thead>
                            <tr bgcolor="#F5F5F5">
                                <td rowspan="2" align="center"><b>NO</b></td>
                                <td rowspan="2"><b>NAMA SISWA</b></td>
                                <td rowspan="2" align="center"><b>L/P</b></td>
                                <td colspan="<?=$tglTerakhir;?>" style="padding: 8px;">PERTEMUAN KE- DAN BULAN : <b style="text-transform: uppercase;"><?php echo namaBulan($bulan);?> <?= date('Y',strtotime($bulanRow['tgl_absen'])); ?></b></td>
                                <td colspan="3" align="center"><b>JUMLAH</b></td>
                            </tr>
                            <tr bgcolor="#F5F5F5">
                                <?php 
                                for ($i = 1; $i <=$tglTerakhir ; $i++) {
                                    echo "<td align='center'><b>".$i."</b></td>";
                                }
                                ?> 
                                <td bgcolor="#FFC107" align="center"><b>S</b></td>
                                <td bgcolor="#4CAF50" align="center"><b>I</b></td>
                                <td bgcolor="#D50000" align="center"><b>A</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no=1;
                            $qryAbsen = mysqli_query($con,"SELECT DISTINCT tb_siswa.id_siswa, tb_siswa.nama_siswa, tb_siswa.jk FROM _logabsensi INNER JOIN tb_siswa ON _logabsensi.id_siswa=tb_siswa.id_siswa
                                WHERE MONTH(tgl_absen)='$bulan' AND _logabsensi.id_mengajar='$pelajaran'
                                ORDER BY tb_siswa.id_siswa ASC ");
                            
                            foreach ($qryAbsen as $ds) {
                                $warna = ($no % 2 == 1) ? "#ffffff" : "#f9f9f9";
                            ?>
                            <tr bgcolor="<?=$warna; ?>">
                                <td align="center"><?=$no++; ?></td>
                                <td><?=$ds['nama_siswa'];?></td>
                                <td align="center"><?=$ds['jk'];?></td>
                                <?php 
                                for ($i = 1; $i <=$tglTerakhir ; $i++) {
                                ?>
                                <td align="center" bgcolor="white">
                                    <?php 
                                    $ket = mysqli_query($con,"SELECT * FROM _logabsensi WHERE DAY(tgl_absen)='$i' AND id_siswa='$ds[id_siswa]' AND id_mengajar='$pelajaran' AND MONTH(tgl_absen)='$bulan' ");
                                    foreach ($ket as $h) {
                                        if ($h['ket']=='H') {
                                            echo "<b style='color:#2196F3;'>H</b>";				
                                        }elseif ($h['ket']=='I') {
                                            echo "<b style='color:#4CAF50;'>I</b>";
                                        }elseif ($h['ket']=='S') {
                                            echo "<b style='color:#FFC107;'>S</b>";
                                        }elseif($h['ket']=='A'){
                                            echo "<b style='color:#D50000;'>A</b>";
                                        }
                                    }
                                    ?>
                                </td>
                                <?php } ?>
                                <td align="center" style="font-weight: bold;">
                                    <?php 
                                    $sakit = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(ket) AS sakit FROM _logabsensi WHERE id_siswa='$ds[id_siswa]' and ket='S' and MONTH(tgl_absen)='$bulan' and id_mengajar='$pelajaran' "));
                                    echo $sakit['sakit'];
                                    ?>
                                </td>
                                <td align="center" style="font-weight: bold;">
                                    <?php 
                                    $izin = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(ket) AS izin FROM _logabsensi WHERE id_siswa='$ds[id_siswa]' and ket='I' and MONTH(tgl_absen)='$bulan' and id_mengajar='$pelajaran' "));
                                    echo $izin['izin'];
                                    ?>
                                </td>
                                <td align="center" style="font-weight: bold;">
                                    <?php 
                                    $alfa = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(ket) AS alfa FROM _logabsensi WHERE id_siswa='$ds[id_siswa]' and ket='A' and MONTH(tgl_absen)='$bulan' and id_mengajar='$pelajaran' "));
                                    echo $alfa['alfa'];
                                    ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
				</div>
                <?php } ?>
			</div>
            <div class="card-footer">
                <a href="?page=laporan&kelas=<?=$kelas;?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
		</div>
	</div>
</div>
</div>
