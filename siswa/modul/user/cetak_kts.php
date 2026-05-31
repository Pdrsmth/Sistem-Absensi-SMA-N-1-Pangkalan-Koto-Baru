<?php
@session_start();
include '../../../config/db.php';

if (!isset($_SESSION['siswa']) && !isset($_SESSION['admin']) && !isset($_SESSION['guru']) && !isset($_SESSION['kepsek'])) {
    echo "<script>alert('Akses ditolak! Silakan login terlebih dahulu.'); window.close();</script>";
    exit();
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($con, $_GET['id']) : '';
if (empty($id)) { echo "ID Siswa tidak ditemukan."; exit(); }

$query = mysqli_query($con, "SELECT * FROM tb_siswa 
    LEFT JOIN tb_mkelas ON tb_siswa.id_mkelas = tb_mkelas.id_mkelas
    WHERE tb_siswa.id_siswa = '$id'");
$siswa = mysqli_fetch_array($query);
if (!$siswa) { echo "Siswa tidak ditemukan."; exit(); }

$kepsekQuery = mysqli_query($con, "SELECT * FROM tb_kepsek WHERE status='Y' LIMIT 1");
$kepsek = mysqli_fetch_array($kepsekQuery);
$nama_kepsek = isset($kepsek['nama_kepsek']) ? $kepsek['nama_kepsek'] : 'Amiruddin Aziz M.Pd';
$nip_kepsek  = isset($kepsek['nip'])         ? $kepsek['nip']         : '123456789011';

$qr_data = "NISN:" . ($siswa['nis'] ?? '') . "|Nama:" . ($siswa['nama_siswa'] ?? '') . "|Kelas:" . ($siswa['nama_kelas'] ?? 'Belum Ditentukan') . "|SMA_N_1_Pangkalan";
$qr_url  = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);

// Barcode URL using bwip-js API (High resolution, no text/numbers, Code 128)
$barcode_url = "https://api-bwipjs.metafloor.com/?bcid=code128&text=" . urlencode($siswa['nis']) . "&scale=4&height=12&backgroundcolor=ffffff";

// Berlaku s/d: end of current school year
$bulan = (int)date('m');
$berlaku = ($bulan >= 7) ? date('Y') + 1 : date('Y');
$berlaku_tgl = '01-07-' . $berlaku;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>KTS - <?= htmlspecialchars((string)$siswa['nama_siswa']); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    background: #e4ebed;
    font-family: Arial, Helvetica, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 20px;
    min-height: 100vh;
}

.page-title {
    font-family: 'Outfit', sans-serif;
    font-size: 18px;
    font-weight: 800;
    color: #2b4c3f;
    margin-bottom: 25px;
    letter-spacing: 1px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Card wrapper */
.kts-wrapper {
    display: flex;
    gap: 30px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 35px;
}

/* ======================== EXACT COLOR & SHADOW ======================== */
.kts-card {
    width: 538px;
    height: 338px;
    position: relative;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 12px 36px rgba(43,76,63,0.18);
    /* White and green premium gradient */
    background: linear-gradient(135deg, #ffffff 0%, #ebf7ee 30%, #b3ebd0 70%, #58a070 100%);
    flex-shrink: 0;
    page-break-inside: avoid;
    border: 1px solid rgba(0,0,0,0.06);
}

/* ======================== WATERMARK EXACT PATTERN ======================== */
.wm-container {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

.wm-logo-center {
    position: absolute;
    top: 52%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 175px;
    height: 175px;
    object-fit: contain;
    opacity: 0.045; /* Clean light green blend */
    z-index: 2;
}

.wm-diagonal-lines {
    position: absolute;
    top: -50px;
    left: -50px;
    width: 150%;
    height: 150%;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    transform: rotate(-18deg);
    z-index: 1;
}

.wm-line {
    display: flex;
    justify-content: space-between;
    white-space: nowrap;
}

.wm-text {
    font-size: 15px;
    font-weight: 800;
    color: rgba(35,76,49,0.07); /* Subtle green diagonal text watermark */
    text-transform: uppercase;
    letter-spacing: 3px;
    padding: 0 20px;
}

.wm-large-bottom {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 28px;
    font-weight: 800;
    color: rgba(35,76,49,0.05); /* Subtle green center bottom watermark */
    letter-spacing: 6px;
    text-transform: uppercase;
    white-space: nowrap;
    z-index: 3;
}

/* ======================== HEADER LAYOUT ======================== */
.kts-header {
    position: relative;
    z-index: 5;
    display: flex;
    align-items: center;
    padding: 10px 15px 6px 15px;
}

.kts-logo-box {
    width: 58px;
    height: 58px;
    border: 1.5px solid #234c31; /* Green border box like UIN logo box */
    padding: 2px;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.kts-logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.kts-header-text {
    flex: 1;
    text-align: center;
    margin-left: 10px;
    color: #1a3c24; /* Solid dark text color */
}

.kts-header-text .line-dept {
    font-size: 13.5px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.kts-header-text .line-school {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.3px;
    margin-top: 1px;
}

.kts-header-text .line-school-en {
    font-size: 8.5px;
    font-weight: 800;
    letter-spacing: 0.2px;
    margin-top: 1px;
}

.kts-header-text .line-address {
    font-size: 6px;
    font-weight: 800;
    margin-top: 1px;
    letter-spacing: 0.1px;
}

/* ======================== SUBTITLE BAND ======================== */
.kts-subtitle {
    position: relative;
    z-index: 5;
    text-align: center;
    padding: 2px 0 6px 0;
    font-size: 13px;
    font-weight: 800;
    color: #234c31;
    letter-spacing: 3px;
    text-transform: uppercase;
}

/* ======================== BODY ======================== */
.kts-body {
    position: relative;
    z-index: 5;
    display: flex;
    padding: 0 15px;
    height: 165px;
    align-items: center;
}

/* Info Column */
.kts-info-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.kts-info-table {
    width: 100%;
    border-collapse: collapse;
}

.kts-info-table tr td {
    padding: 4.5px 0;
    font-size: 11px;
    color: #1a3c24;
    line-height: 1.2;
    vertical-align: top;
}

.kts-info-table .lbl {
    font-weight: 800;
    width: 90px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kts-info-table .sep {
    width: 12px;
    font-weight: 800;
    text-align: center;
}

.kts-info-table .val {
    font-weight: 800;
    text-transform: uppercase;
}

/* QR Code Column */
.kts-qr-col {
    width: 68px;
    margin: 0 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kts-qr-img {
    width: 60px;
    height: 60px;
    display: block;
}

/* Photo Column */
.kts-photo-col {
    width: 106px;
    height: 136px;
    background: #c63333; /* Red background pasfoto */
    border: 1px solid #234c31;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    flex-shrink: 0;
}

.kts-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ======================== BACK CARD SPECIFIC ======================== */
.back-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 25px 25px 15px 25px;
    justify-content: space-between;
    align-items: center;
}

.back-visi-header {
    text-align: center;
    color: #1a3c24;
    position: relative;
    z-index: 5;
}

.back-visi-title {
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 4px;
}

.back-school-name {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 2px;
}

.back-visi-text {
    font-size: 11px;
    color: #1a3c24;
    line-height: 1.5;
    text-align: center;
    font-weight: 600;
    padding: 0 10px;
    margin-top: 15px;
}

/* Clean Giant Barcode Area */
.back-barcode-strip {
    width: 90%;
    background: #ffffff;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    position: relative;
    z-index: 5;
}

.back-barcode-img {
    width: 100%;
    height: 52px;
    object-fit: fill; /* Stretch barcode bars horizontally to span the entire white strip */
    image-rendering: pixelated;
    image-rendering: crisp-edges; /* High-clarity razor sharp rendering */
    display: block;
}

/* ======================== PRINT BUTTONS ======================== */
.action-bar {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.btn-act {
    padding: 12px 30px;
    border-radius: 30px;
    font-family: 'Outfit', sans-serif;
    font-size: 14px;
    font-weight: 800;
    border: none;
    cursor: pointer;
    transition: 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-print { 
    background: #2b4c3f; 
    color: #fff; 
    box-shadow: 0 4px 15px rgba(43,76,99,0.25); 
}
.btn-print:hover { 
    background: #1f372d; 
    transform: translateY(-2px); 
}
.btn-back  { 
    background: #fff; 
    color: #333; 
    border: 1px solid #ccc; 
}
.btn-back:hover { 
    background: #f0f0f0; 
}

/* ======================== PRINT STYLES ======================== */
@media print {
    body { 
        background: transparent; 
        padding: 0; 
    }
    .action-bar { display: none !important; }
    .page-title { display: none !important; }
    .kts-wrapper { gap: 40px; margin: 0; }
    .kts-card {
        box-shadow: none;
        border: none;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
</head>
<body>

<div class="page-title">🪪 PRATINJAU CETAK KARTU TANDA SISWA (KTS)</div>

<div class="kts-wrapper">

    <!-- ===================== CARD FRONT ===================== -->
    <div class="kts-card">

        <!-- Watermark background -->
        <div class="wm-container">
            <!-- Center transparent logo watermark -->
            <img src="../../../assets/img/LOGO1.jpg" class="wm-logo-center" alt="">
            
            <div class="wm-diagonal-lines">
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
            </div>
            <div class="wm-large-bottom">SMAN 1 PANGKALAN</div>
        </div>

        <!-- Header matching UIN Suska exactly but with School info -->
        <div class="kts-header">
            <div class="kts-logo-box">
                <img src="../../../assets/img/LOGO1.jpg" class="kts-logo-img" alt="Logo">
            </div>
            <div class="kts-header-text">
                <div class="line-dept">KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</div>
                <div class="line-school">SMA NEGERI 1 PANGKALAN KOTO BARU</div>
                <div class="line-school-en">STATE SENIOR HIGH SCHOOL 1 PANGKALAN KOTO BARU</div>
                <div class="line-address">JALAN LINTAS SUMBAR-RIAU KEC. PANGKALAN KOTO BARU, KAB. LIMA PULUH KOTA, SUMATERA BARAT, 26272</div>
            </div>
        </div>

        <!-- Subtitle -->
        <div class="kts-subtitle">KARTU TANDA SISWA</div>

        <!-- Body -->
        <div class="kts-body">
            <!-- Left: Info list -->
            <div class="kts-info-col">
                <table class="kts-info-table">
                    <tr>
                        <td class="lbl">NAMA</td>
                        <td class="sep">:</td>
                        <td class="val"><?= htmlspecialchars((string)$siswa['nama_siswa']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">NIS / NISN</td>
                        <td class="sep">:</td>
                        <td class="val"><?= htmlspecialchars((string)$siswa['nis']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">KELAS</td>
                        <td class="sep">:</td>
                        <td class="val"><?= !empty($siswa['nama_kelas']) ? htmlspecialchars((string)$siswa['nama_kelas']) : 'Belum Ditentukan'; ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">JURUSAN</td>
                        <td class="sep">:</td>
                        <td class="val">-</td>
                    </tr>
                    <tr>
                        <td class="lbl">BERLAKU S/D</td>
                        <td class="sep">:</td>
                        <td class="val"><?= $berlaku_tgl; ?></td>
                    </tr>
                </table>
            </div>

            <!-- Middle-Right: QR Code -->
            <div class="kts-qr-col">
                <img src="<?= $qr_url; ?>" class="kts-qr-img" alt="QR">
            </div>

            <!-- Right: Portrait Photo inside frame -->
            <div class="kts-photo-col">
                <img src="../../../assets/img/user/<?= htmlspecialchars((string)$siswa['foto']); ?>" class="kts-photo-img" alt="Foto Siswa" onerror="this.src='../../../assets/img/default_avatar.png'">
            </div>
        </div>

    </div><!-- /card front -->


    <!-- ===================== CARD BACK ===================== -->
    <div class="kts-card">

        <!-- Watermark background -->
        <div class="wm-container">
            <!-- Center transparent logo watermark -->
            <img src="../../../assets/img/LOGO1.jpg" class="wm-logo-center" alt="">
            
            <div class="wm-diagonal-lines">
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
                <div class="wm-line">
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                    <span class="wm-text">KTS SMAN 1 PANGKALAN</span>
                </div>
            </div>
            <div class="wm-large-bottom">SMAN 1 PANGKALAN</div>
        </div>

        <div class="back-content">
            <!-- Visi -->
            <div class="back-visi-header">
                <div class="back-visi-title">VISI</div>
                <div class="back-school-name">SMA NEGERI 1 PANGKALAN KOTO BARU</div>
                <div class="back-visi-text">
                    "Terwujudnya peserta didik yang beriman, bertaqwa, berakhlak mulia, unggul dalam prestasi, berwawasan lingkungan, serta berdaya saing di tingkat nasional dan internasional."
                </div>
            </div>

            <!-- Huge Barcode Strip exactly like KTM -->
            <div class="back-barcode-strip">
                <img src="<?= $barcode_url; ?>" class="back-barcode-img" alt="Barcode" onerror="this.style.display='none'">
            </div>
        </div>

    </div><!-- /card back -->

</div><!-- /wrapper -->

<!-- Action Buttons -->
<div class="action-bar">
    <button class="btn-act btn-print" onclick="window.print()">
        🖨️ &nbsp;Cetak KTS Sekarang
    </button>
    <button class="btn-act btn-back" onclick="window.close()">
        ✕ &nbsp;Tutup
    </button>
</div>

</body>
</html>
