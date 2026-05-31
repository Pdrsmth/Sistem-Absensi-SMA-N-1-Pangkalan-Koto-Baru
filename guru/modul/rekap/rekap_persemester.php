<?php 
include '../../../config/db.php';

// Fetch schedule details (pelajaran parameter can be id_mengajar or id_mapel depending on which portal calls it)
$kelasMengajar = mysqli_query($con, "SELECT * FROM tb_mengajar 
	INNER JOIN tb_guru ON tb_mengajar.id_guru=tb_guru.id_guru
    INNER JOIN tb_master_mapel ON tb_mengajar.id_mapel=tb_master_mapel.id_mapel
    INNER JOIN tb_mkelas ON tb_mengajar.id_mkelas=tb_mkelas.id_mkelas
    INNER JOIN tb_semester ON tb_mengajar.id_semester=tb_semester.id_semester
    INNER JOIN tb_thajaran ON tb_mengajar.id_thajaran=tb_thajaran.id_thajaran
    WHERE (tb_mengajar.id_mengajar='$_GET[pelajaran]' OR (tb_mengajar.id_mapel='$_GET[pelajaran]' AND tb_mengajar.id_mkelas='$_GET[kelas]')) 
    AND tb_mengajar.id_mkelas='$_GET[kelas]'
");
$d = mysqli_fetch_array($kelasMengajar);
$id_mengajar = isset($d['id_mengajar']) ? $d['id_mengajar'] : 0;

// Fetch students list
$siswaQry = mysqli_query($con, "SELECT * FROM tb_siswa 
    WHERE id_mkelas='$_GET[kelas]' AND status=1 
    ORDER BY nama_siswa ASC");
$num_students = mysqli_num_rows($siswaQry);

// Fetch walikelas
$walikelas = mysqli_query($con, "SELECT * FROM tb_walikelas 
    INNER JOIN tb_guru ON tb_walikelas.id_guru=tb_guru.id_guru 
    WHERE tb_walikelas.id_mkelas='$_GET[kelas]'");
$walas = mysqli_fetch_array($walikelas);

// Fetch Kepsek
$kepsek = mysqli_query($con, "SELECT * FROM tb_kepsek WHERE status='Y' LIMIT 1");
$dataKepsek = mysqli_fetch_array($kepsek);
$nama_kepsek = isset($dataKepsek['nama_kepsek']) ? $dataKepsek['nama_kepsek'] : 'Amiruddin Aziz M.Pd';
$nip_kepsek = isset($dataKepsek['nip']) ? $dataKepsek['nip'] : '123456789011';

// Calculate total effective school days
$totalDaysQuery = mysqli_query($con, "SELECT COUNT(DISTINCT tgl_absen) AS total_days FROM _logabsensi WHERE id_mengajar='$id_mengajar'");
$totalDaysData = mysqli_fetch_array($totalDaysQuery);
$total_effective_days = isset($totalDaysData['total_days']) && $totalDaysData['total_days'] > 0 ? $totalDaysData['total_days'] : 1;

// Define months based on active semester
$sem = isset($d['semester']) ? $d['semester'] : '';
if ($sem !== '' && strtolower($sem) == 'ganjil') {
    $months = [
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'Nopember',
        12 => 'Desember'
    ];
} else {
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni'
    ];
}

// Prefetch all attendance counts for the entire class to optimize performance
$absensiData = [];
$logQuery = mysqli_query($con, "SELECT id_siswa, MONTH(tgl_absen) as bln, ket, COUNT(*) as cnt 
    FROM _logabsensi 
    WHERE id_mengajar='$id_mengajar' 
    GROUP BY id_siswa, MONTH(tgl_absen), ket");
while ($logRow = mysqli_fetch_array($logQuery)) {
    $absensiData[$logRow['id_siswa']][$logRow['bln']][$logRow['ket']] = $logRow['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekap Semester | <?= $d['mapel']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
        }
        .title-school {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle-school {
            font-size: 10px;
            color: #555;
            margin: 5px 0 0 0;
        }
        .info-table {
            border-collapse: collapse;
            font-size: 11px;
        }
        .info-table td {
            padding: 2px 5px;
            border: none;
        }
        .info-box {
            border: 2px solid #000;
            padding: 5px 10px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }
        .sheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .sheet-table th, .sheet-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-size: 10px;
        }
        .sheet-table th {
            background-color: #F2F2F2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .col-left {
            text-align: left !important;
            padding-left: 5px !important;
        }
        .highlight-jml {
            background-color: #FFF2CC !important;
            font-weight: bold;
        }
        .highlight-total {
            background-color: #E2EFDA !important;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            text-align: center;
            font-size: 11px;
        }
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>

<!-- School Header -->
<table class="header-table">
    <tr>
        <td width="100">
            <img src="../../../assets/img/LOGO1.jpg" width="80">
        </td>
        <td>
            <h1 class="title-school">Absensi Siswa Semester <?= $d['semester']; ?></h1>
            <h2 style="font-size: 14px; margin: 3px 0 0 0; text-transform: uppercase;">SMA Negeri 1 Pangkalan Koto Baru</h2>
            <p class="subtitle-school">
                Jl. Lintas Sumbar-Riau Kec. Pangkalan Koto Baru Kab. Lima Puluh Kota, Prov. Sumatera Barat, Kode Pos (26272)<br>
                <b>Email : sma1pangkalan@gmail.com Telp. 081222333444</b>
            </p>
        </td>
        <td align="right" width="40%">
            <table class="info-table" align="right">
                <tr>
                    <td><div class="info-box">Kelas: <?= $d['nama_kelas']; ?></div></td>
                    <td><div class="info-box"><?= $d['semester']; ?> | <?= $d['tahun_ajaran']; ?></div></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left"><b>Mata Pelajaran</b></td>
                    <td>: <?= $d['mapel']; ?></td>
                </tr>
                <tr>
                    <td align="left"><b>Guru Pengajar</b></td>
                    <td>: <?= $d['nama_guru']; ?></td>
                </tr>
                <tr>
                    <td align="left"><b>Wali Kelas</b></td>
                    <td>: <?= isset($walas['nama_guru']) ? $walas['nama_guru'] : '-'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<hr style="border: 1px solid #000; margin-bottom: 10px;">

<!-- Sheet-style Table -->
<table class="sheet-table">
    <thead>
        <tr>
            <th rowspan="3" width="3%">NO</th>
            <th rowspan="3" width="7%">INDUK</th>
            <th rowspan="3" width="20%">NAMA SISWA</th>
            <th rowspan="3" width="4%">L/P</th>
            <th colspan="24">JUMLAH KETIDAK HADIRAN</th>
            <th colspan="3" rowspan="2">JUMLAH TOTAL</th>
            <th rowspan="3" class="highlight-total" width="5%">JML TOTAL</th>
            <th rowspan="3" width="5%">Jml Tdk Hadir</th>
            <th rowspan="3" width="6%">Jml Prosentase Hadir (%)</th>
        </tr>
        <tr>
            <?php foreach ($months as $m_num => $m_name) { ?>
            <th colspan="4"><?= $m_name; ?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php for ($i = 0; $i < 6; $i++) { ?>
            <th>S</th>
            <th>I</th>
            <th>A</th>
            <th class="highlight-jml">JML</th>
            <?php } ?>
            <th>S</th>
            <th>I</th>
            <th>A</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        // Arrays to store bottom sums
        $month_sums = [];
        $total_s = 0;
        $total_i = 0;
        $total_a = 0;
        $total_absences_all = 0;

        foreach ($siswaQry as $ds) {
            $gender = $ds['jk'] == 'L' ? 'L' : 'P';
            
            // Calculate monthly absence counts
            $row_absences = 0;
            $student_s = 0;
            $student_i = 0;
            $student_a = 0;
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $ds['nis']; ?></td>
            <td class="col-left"><?= $ds['nama_siswa']; ?></td>
            <td><?= $gender; ?></td>

            <?php 
            foreach ($months as $m_num => $m_name) {
                $s = isset($absensiData[$ds['id_siswa']][$m_num]['S']) ? $absensiData[$ds['id_siswa']][$m_num]['S'] : 0;
                $i = isset($absensiData[$ds['id_siswa']][$m_num]['I']) ? $absensiData[$ds['id_siswa']][$m_num]['I'] : 0;
                $a = isset($absensiData[$ds['id_siswa']][$m_num]['A']) ? $absensiData[$ds['id_siswa']][$m_num]['A'] : 0;
                $jml = $s + $i + $a;

                // Accumulate row totals
                $student_s += $s;
                $student_i += $i;
                $student_a += $a;
                $row_absences += $jml;

                // Accumulate column sums for bottom row
                $month_sums[$m_num]['S'] = ($month_sums[$m_num]['S'] ?? 0) + $s;
                $month_sums[$m_num]['I'] = ($month_sums[$m_num]['I'] ?? 0) + $i;
                $month_sums[$m_num]['A'] = ($month_sums[$m_num]['A'] ?? 0) + $a;
                $month_sums[$m_num]['JML'] = ($month_sums[$m_num]['JML'] ?? 0) + $jml;
            ?>
            <td><?= $s > 0 ? $s : ''; ?></td>
            <td><?= $i > 0 ? $i : ''; ?></td>
            <td><?= $a > 0 ? $a : ''; ?></td>
            <td class="highlight-jml"><?= $jml > 0 ? $jml : ''; ?></td>
            <?php } ?>

            <!-- Semester Totals -->
            <td><?= $student_s > 0 ? $student_s : ''; ?></td>
            <td><?= $student_i > 0 ? $student_i : ''; ?></td>
            <td><?= $student_a > 0 ? $student_a : ''; ?></td>
            <td class="highlight-total"><?= $row_absences > 0 ? $row_absences : ''; ?></td>
            <td><?= $row_absences; ?></td>
            <?php 
            $hadir_pct = round((($total_effective_days - $row_absences) / $total_effective_days) * 100);
            if ($hadir_pct < 0) $hadir_pct = 0;
            ?>
            <td style="font-weight: bold;"><?= $hadir_pct; ?>%</td>
        </tr>
        <?php 
            // Accumulate grand totals
            $total_s += $student_s;
            $total_i += $student_i;
            $total_a += $student_a;
            $total_absences_all += $row_absences;
        } 
        ?>

        <!-- Bottom Rows: Absence Percentage -->
        <tr>
            <td colspan="4" style="font-weight: bold;">Prosentase Ketidakhadiran</td>
            <?php 
            foreach ($months as $m_num => $m_name) {
                $col_s = $month_sums[$m_num]['S'] ?? 0;
                $col_i = $month_sums[$m_num]['I'] ?? 0;
                $col_a = $month_sums[$m_num]['A'] ?? 0;
                $col_jml = $month_sums[$m_num]['JML'] ?? 0;

                $pct_s = $num_students > 0 ? round(($col_s / ($num_students * $total_effective_days)) * 100, 1) : 0;
                $pct_i = $num_students > 0 ? round(($col_i / ($num_students * $total_effective_days)) * 100, 1) : 0;
                $pct_a = $num_students > 0 ? round(($col_a / ($num_students * $total_effective_days)) * 100, 1) : 0;
                $pct_jml = $num_students > 0 ? round(($col_jml / ($num_students * $total_effective_days)) * 100, 1) : 0;
            ?>
            <td><?= $pct_s > 0 ? $pct_s . '%' : '-'; ?></td>
            <td><?= $pct_i > 0 ? $pct_i . '%' : '-'; ?></td>
            <td><?= $pct_a > 0 ? $pct_a . '%' : '-'; ?></td>
            <td class="highlight-jml"><?= $pct_jml > 0 ? $pct_jml . '%' : '-'; ?></td>
            <?php } ?>

            <!-- Grand Totals Percentages -->
            <?php
            $pct_total_s = $num_students > 0 ? round(($total_s / ($num_students * $total_effective_days)) * 100, 1) : 0;
            $pct_total_i = $num_students > 0 ? round(($total_i / ($num_students * $total_effective_days)) * 100, 1) : 0;
            $pct_total_a = $num_students > 0 ? round(($total_a / ($num_students * $total_effective_days)) * 100, 1) : 0;
            $pct_total_all = $num_students > 0 ? round(($total_absences_all / ($num_students * $total_effective_days)) * 100, 1) : 0;
            ?>
            <td><?= $pct_total_s > 0 ? $pct_total_s . '%' : '-'; ?></td>
            <td><?= $pct_total_i > 0 ? $pct_total_i . '%' : '-'; ?></td>
            <td><?= $pct_total_a > 0 ? $pct_total_a . '%' : '-'; ?></td>
            <td class="highlight-total"><?= $pct_total_all > 0 ? $pct_total_all . '%' : '-'; ?></td>
            <td>-</td>
            <td>-</td>
        </tr>

        <!-- Bottom Rows: Presence Percentage -->
        <tr>
            <td colspan="4" style="font-weight: bold;">Prosentase Kehadiran</td>
            <?php 
            foreach ($months as $m_num => $m_name) {
                $col_jml = $month_sums[$m_num]['JML'] ?? 0;
                $pct_jml = $num_students > 0 ? round(($col_jml / ($num_students * $total_effective_days)) * 100, 1) : 0;
                $presence_pct = 100 - $pct_jml;
            ?>
            <td colspan="4" class="highlight-jml" style="font-weight: bold;"><?= $presence_pct; ?>%</td>
            <?php } ?>

            <!-- Overall Presence Percentage -->
            <?php
            $overall_presence_pct = 100 - $pct_total_all;
            ?>
            <td colspan="3" style="font-weight: bold;">Overall:</td>
            <td class="highlight-total"><?= $overall_presence_pct; ?>%</td>
            <td>-</td>
            <td style="font-weight: bold;"><?= $overall_presence_pct; ?>%</td>
        </tr>
    </tbody>
</table>

<!-- Signatures -->
<table class="signature-table">
    <tr>
        <td width="30%">
            <p>Mengetahui,</p>
            <p>Wali Kelas</p>
            <br><br><br><br>
            <p><b><u><?= isset($walas['nama_guru']) ? $walas['nama_guru'] : 'Wali Kelas'; ?></u></b></p>
            <p>NIP. <?= isset($walas['nip']) ? $walas['nip'] : '-'; ?></p>
        </td>
        <td width="40%">&nbsp;</td>
        <td width="30%">
            <p>Pangkalan Koto Baru, <?= date('d F Y'); ?></p>
            <p>Kepala Sekolah</p>
            <br><br><br><br>
            <p><b><u><?= $nama_kepsek; ?></u></b></p>
            <p>NIP. <?= $nip_kepsek; ?></p>
        </td>
    </tr>
</table>

<script>
    window.print();
</script>
</body>
</html>