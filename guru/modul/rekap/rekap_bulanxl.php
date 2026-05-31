<?php 
include '../../../config/db.php';
$time = time();

// Excel headers
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=REKAP_ABSEN-$time.xls");

$bulan = (int)$_GET['bulan'];
$kelas = $_GET['kelas'] ?? '';

// 1. Fetch schedule details
$kelasMengajar = mysqli_query($con, "SELECT * FROM tb_mengajar 
    INNER JOIN tb_guru ON tb_mengajar.id_guru = tb_guru.id_guru
    INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel = tb_master_mapel.id_mapel
    INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas = tb_mkelas.id_mkelas
    INNER JOIN tb_semester ON tb_mengajar.id_semester = tb_semester.id_semester
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran = tb_thajaran.id_thajaran
    WHERE (tb_mengajar.id_mengajar='$_GET[pelajaran]' OR (tb_mengajar.id_mapel='$_GET[pelajaran]' AND tb_mengajar.id_mkelas='$kelas')) 
    AND tb_mengajar.id_mkelas='$kelas' AND tb_thajaran.status=1
");
$d = mysqli_fetch_array($kelasMengajar);
$id_mengajar = isset($d['id_mengajar']) ? $d['id_mengajar'] : 0;

// 2. Fetch wali kelas
$walikelas = mysqli_query($con, "SELECT * FROM tb_walikelas 
    INNER JOIN tb_guru ON tb_walikelas.id_guru = tb_guru.id_guru 
    WHERE tb_walikelas.id_mkelas='$kelas'");
$walas = mysqli_fetch_array($walikelas);

// 3. Fetch ALL active students
$siswaQry = mysqli_query($con, "SELECT * FROM tb_siswa 
    WHERE id_mkelas='$kelas' AND status=1 
    ORDER BY nama_siswa ASC");

// 4. Calculate days in month
$tahun = date('Y');
$tglTerakhir = date('t', mktime(0, 0, 0, $bulan, 1, $tahun));

// 5. Prefetch ALL log data in one query
$logData = [];
$logQry = mysqli_query($con, "SELECT id_siswa, DAY(tgl_absen) AS hari, ket 
    FROM _logabsensi
    INNER JOIN tb_mengajar ON _logabsensi.id_mengajar = tb_mengajar.id_mengajar
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran = tb_thajaran.id_thajaran
    WHERE _logabsensi.id_mengajar='$id_mengajar' 
      AND MONTH(tgl_absen)='$bulan'
      AND YEAR(tgl_absen)='$tahun'
      AND tb_mengajar.id_mkelas='$kelas'
      AND tb_thajaran.status=1
    ORDER BY tgl_absen ASC");
while ($lr = mysqli_fetch_array($logQry)) {
    $logData[$lr['id_siswa']][$lr['hari']] = $lr['ket'];
}
?>
<style>body { font-family: arial; }</style>

<table width="100%">
    <tr>
        <td width="140" align="center">
            <img src="../../../assets/img/LOGO1.jpg" width="100">
        </td>
        <td>
            <center>
                <h1>ABSENSI SISWA <br><small>SMA NEGERI 1 PANGKALAN KOTO BARU</small></h1>
                <hr>
                <em>Jl. Lintas Sumbar-Riau Kec. Pangkalan Koto Baru Kab. Lima Puluh Kota <br>
                Provinsi Sumatera Barat, Kode Pos (26272) <br>
                <b>Email : sma1pangkalan@gmail.com Telp. 081222333444</b></em>
            </center>
        </td>
        <td>
            <table width="100%">
                <tr>
                    <td colspan="3"><b style="border:2px solid; padding:7px;">KELAS (<?=strtoupper($d['nama_kelas'] ?? '')?>)</b></td>
                    <td><b style="border:2px solid; padding:7px;"><?=$d['semester'] ?? ''?> | <?=$d['tahun_ajaran'] ?? ''?></b></td>
                    <td rowspan="5">
                        <p>H = Hadir</p><p>S = Sakit</p><p>I = Izin</p>
                        <p>T = Terlambat</p><p>A = Absen</p><p>C = Cabut</p>
                    </td>
                </tr>
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                <tr>
                    <td>Nama Guru</td><td>:</td><td><?=$d['nama_guru'] ?? ''?></td>
                </tr>
                <tr>
                    <td>Bidang Studi</td><td>:</td><td><?=$d['mapel'] ?? ''?></td>
                </tr>
                <tr>
                    <td>Wali Kelas</td><td>:</td><td><?=isset($walas['nama_guru']) ? $walas['nama_guru'] : '-'?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<hr style="height:3px; border:1px solid;">

<table width="100%" border="1" cellpadding="2" style="border-collapse:collapse;">
    <tr>
        <td rowspan="2" bgcolor="#EFEBE9" align="center"><b>NO</b></td>
        <td rowspan="2" bgcolor="#EFEBE9"><b>NAMA SISWA</b></td>
        <td rowspan="2" bgcolor="#EFEBE9" align="center"><b>L/P</b></td>
        <td colspan="<?=$tglTerakhir?>" style="padding:8px;">
            <b>PERTEMUAN KE- DAN BULAN : <?php echo strtoupper(namaBulan($bulan)); ?> <?=$tahun?></b>
        </td>
        <td colspan="5" align="center" bgcolor="#EFEBE9"><b>JUMLAH</b></td>
    </tr>
    <tr>
        <?php for ($i = 1; $i <= $tglTerakhir; $i++): ?>
        <td bgcolor="#EFEBE9" align="center"><?=$i?></td>
        <?php endfor; ?>
        <td bgcolor="#FFC107" align="center"><b>S</b></td>
        <td bgcolor="#4CAF50" align="center"><b>I</b></td>
        <td bgcolor="#D50000" align="center" style="color:#fff;"><b>A</b></td>
        <td bgcolor="#9E9E9E" align="center"><b>T</b></td>
        <td bgcolor="#9C27B0" align="center" style="color:#fff;"><b>C</b></td>
    </tr>

    <?php
    $no = 1;
    while ($ds = mysqli_fetch_array($siswaQry)):
        $sid = $ds['id_siswa'];
        $warna = ($no % 2 == 0) ? '#f0f0f0' : '#ffffff';
        $cnt = ['S'=>0,'I'=>0,'A'=>0,'T'=>0,'C'=>0];
    ?>
    <tr bgcolor="<?=$warna?>">
        <td align="center"><?=$no++?></td>
        <td><?=htmlspecialchars($ds['nama_siswa'])?></td>
        <td align="center"><?=$ds['jk'] == 'L' ? 'L' : 'P'?></td>
        <?php for ($i = 1; $i <= $tglTerakhir; $i++):
            $ket = isset($logData[$sid][$i]) ? $logData[$sid][$i] : '';
            if ($ket && $ket !== 'H') $cnt[$ket] = ($cnt[$ket] ?? 0) + 1;
        ?>
        <td align="center" bgcolor="white">
            <?php if ($ket): ?>
            <b style="color:<?php 
                $colors=['H'=>'#2196F3','S'=>'#FFC107','I'=>'#4CAF50','T'=>'#9E9E9E','A'=>'#D50000','C'=>'#9C27B0'];
                echo $colors[$ket] ?? '#333';
            ?>"><?=$ket?></b>
            <?php endif; ?>
        </td>
        <?php endfor; ?>
        <td align="center" style="font-weight:bold;"><?=$cnt['S'] ?: 0?></td>
        <td align="center" style="font-weight:bold;"><?=$cnt['I'] ?: 0?></td>
        <td align="center" style="font-weight:bold;"><?=$cnt['A'] ?: 0?></td>
        <td align="center" style="font-weight:bold;"><?=$cnt['T'] ?: 0?></td>
        <td align="center" style="font-weight:bold;"><?=$cnt['C'] ?: 0?></td>
    </tr>
    <?php endwhile; ?>
</table>

<p>&nbsp;</p>
<table width="100%">
    <tr>
        <td align="right">
            <p>Pangkalan Koto Baru, <?php echo date('d-F-Y'); ?></p>
            <p>Guru Pengajar</p>
            <br><br><br><br>
            <p><b><u><?=$d['nama_guru'] ?? ''?></u></b></p>
            <p>NIP. <?=$d['nip'] ?? '-'?></p>
        </td>
    </tr>
</table>
