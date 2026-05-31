<?php 
include '../../../config/db.php';

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
    AND tb_mengajar.id_mkelas='$kelas' AND tb_thajaran.status=1 AND tb_semester.status=1
");
$d = mysqli_fetch_array($kelasMengajar);
$id_mengajar = isset($d['id_mengajar']) ? $d['id_mengajar'] : 0;

// 2. Fetch wali kelas
$walikelas = mysqli_query($con, "SELECT * FROM tb_walikelas 
    INNER JOIN tb_guru ON tb_walikelas.id_guru = tb_guru.id_guru 
    WHERE tb_walikelas.id_mkelas='$kelas'");
$walas = mysqli_fetch_array($walikelas);

// 3. Fetch ALL active students in this class (even those with zero logs)
$siswaQry = mysqli_query($con, "SELECT * FROM tb_siswa 
    WHERE id_mkelas='$kelas' AND status=1 
    ORDER BY nama_siswa ASC");

// 4. Calculate last day of the month
$tahun    = date('Y');
$tglTerakhir = date('t', mktime(0, 0, 0, $bulan, 1, $tahun));

// 5. PREFETCH all attendance data for this class & month in ONE query
// Structure: $logData[id_siswa][day] = 'H'/'S'/'I'/'T'/'A'/'C'
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

// 6. Fetch Kepsek
$kepsek = mysqli_query($con, "SELECT * FROM tb_kepsek WHERE status='Y' LIMIT 1");
$dataKepsek = mysqli_fetch_array($kepsek);
$nama_kepsek = isset($dataKepsek['nama_kepsek']) ? $dataKepsek['nama_kepsek'] : 'Amiruddin Aziz M.Pd';
$nip_kepsek  = isset($dataKepsek['nip']) ? $dataKepsek['nip'] : '123456789011';

function namaKet($ket) {
    $map = ['H'=>'H','S'=>'S','I'=>'I','T'=>'T','A'=>'A','C'=>'C'];
    return isset($map[$ket]) ? $ket : '';
}
function warnKet($ket) {
    $colors = ['H'=>'#2196F3','S'=>'#FFC107','I'=>'#4CAF50','T'=>'#9E9E9E','A'=>'#D50000','C'=>'#9C27B0'];
    return $colors[$ket] ?? '#333';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Bulan <?php echo strtoupper(namaBulan($bulan)); ?> | <?php echo $d['mapel'] ?? ''; ?></title>
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #111; margin: 15px; }
    h1 { font-size: 16px; margin: 0; }
    small { font-size: 12px; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .header-table td { vertical-align: middle; }
    .info-box { border: 2px solid #000; padding: 4px 8px; font-weight: bold; display: inline-block; text-transform: uppercase; }
    hr.bold { border: none; border-top: 2px solid #000; margin: 8px 0; }
    .rekap-table { width: 100%; border-collapse: collapse; }
    .rekap-table th, .rekap-table td {
        border: 1px solid #aaa;
        padding: 3px 2px;
        text-align: center;
        font-size: 9px;
    }
    .rekap-table th { background: #EFEBE9; font-weight: bold; }
    .rekap-table td.name-col { text-align: left; padding-left: 4px; white-space: nowrap; }
    .legend-list { list-style: none; padding: 0; margin: 0; }
    .legend-list li { margin: 2px 0; }
    .sig-table { width: 100%; margin-top: 25px; border-collapse: collapse; }
    .sig-table td { text-align: center; border: none; font-size: 11px; }
    @media print {
        @page { size: landscape; margin: 1cm; }
        body { margin: 0; }
    }
</style>
</head>
<body>

<!-- Header -->
<table class="header-table">
    <tr>
        <td width="100" align="center">
            <img src="../../../assets/img/LOGO1.jpg" width="80">
        </td>
        <td>
            <center>
                <h1>ABSENSI SISWA<br><small>SMA NEGERI 1 PANGKALAN KOTO BARU</small></h1>
                <hr style="border-top:1px solid #999; margin:4px 0;">
                <em>Jl. Lintas Sumbar-Riau Kec. Pangkalan Koto Baru Kab. Lima Puluh Kota<br>
                Provinsi Sumatera Barat, Kode Pos (26272)<br>
                <b>Email : sma1pangkalan@gmail.com &nbsp; Telp. 081222333444</b></em>
            </center>
        </td>
        <td width="35%" valign="top">
            <table width="100%">
                <tr>
                    <td colspan="3"><b class="info-box">KELAS (<?=strtoupper($d['nama_kelas'] ?? '') ?>)</b>&nbsp;<b class="info-box"><?=$d['semester'] ?? ''?> | <?=$d['tahun_ajaran'] ?? ''?></b></td>
                </tr>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td>Nama Guru</td><td>:</td><td><?=$d['nama_guru'] ?? ''?></td>
                </tr>
                <tr>
                    <td>Bidang Studi</td><td>:</td><td><?=$d['mapel'] ?? ''?></td>
                </tr>
                <tr>
                    <td>Wali Kelas</td><td>:</td><td><?=isset($walas['nama_guru']) ? $walas['nama_guru'] : '-'?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <ul class="legend-list">
                            <li>H = Hadir</li>
                            <li>S = Sakit</li>
                            <li>I = Izin</li>
                            <li>T = Terlambat</li>
                            <li>A = Absen</li>
                            <li>C = Cabut</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<hr class="bold">

<!-- Main Table -->
<table class="rekap-table">
    <thead>
        <tr>
            <th rowspan="2">NO</th>
            <th rowspan="2">NAMA SISWA</th>
            <th rowspan="2">L/P</th>
            <th colspan="<?=$tglTerakhir?>" style="padding:5px;">
                PERTEMUAN BULAN : <b><?php echo strtoupper(namaBulan($bulan)); ?> <?=$tahun?></b>
            </th>
            <th colspan="5">JUMLAH</th>
        </tr>
        <tr>
            <?php for ($i = 1; $i <= $tglTerakhir; $i++): ?>
            <th><?=$i?></th>
            <?php endfor; ?>
            <th style="background:#FFC107;">S</th>
            <th style="background:#4CAF50;color:#fff;">I</th>
            <th style="background:#D50000;color:#fff;">A</th>
            <th style="background:#9E9E9E;">T</th>
            <th style="background:#9C27B0;color:#fff;">C</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    while ($ds = mysqli_fetch_array($siswaQry)):
        $sid = $ds['id_siswa'];
        $warna = ($no % 2 == 0) ? '#f9f9f9' : '#ffffff';
        $cnt = ['S'=>0, 'I'=>0, 'A'=>0, 'T'=>0, 'C'=>0];
    ?>
    <tr style="background:<?=$warna?>;">
        <td><?=$no++?></td>
        <td class="name-col"><?=htmlspecialchars($ds['nama_siswa'])?></td>
        <td><?=$ds['jk'] == 'L' ? 'L' : 'P'?></td>
        <?php for ($i = 1; $i <= $tglTerakhir; $i++):
            $ket = isset($logData[$sid][$i]) ? $logData[$sid][$i] : '';
            if ($ket && $ket !== 'H') $cnt[$ket] = ($cnt[$ket] ?? 0) + 1;
        ?>
        <td>
            <?php if ($ket): ?>
            <b style="color:<?=warnKet($ket)?>"><?=$ket?></b>
            <?php endif; ?>
        </td>
        <?php endfor; ?>
        <td style="font-weight:bold;"><?=$cnt['S'] ?: ''?></td>
        <td style="font-weight:bold;"><?=$cnt['I'] ?: ''?></td>
        <td style="font-weight:bold;"><?=$cnt['A'] ?: ''?></td>
        <td style="font-weight:bold;"><?=$cnt['T'] ?: ''?></td>
        <td style="font-weight:bold;"><?=$cnt['C'] ?: ''?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<!-- Signature -->
<table class="sig-table">
    <tr>
        <td width="60%">&nbsp;</td>
        <td width="40%">
            <p>Pangkalan Koto Baru, <?php echo date('d F Y'); ?></p>
            <p>Guru Pengajar</p>
            <br><br><br><br>
            <p><b><u><?=$d['nama_guru'] ?? ''?></u></b></p>
            <p>NIP. <?=$d['nip'] ?? '-'?></p>
        </td>
    </tr>
</table>

<script>window.print();</script>
</body>
</html>