<?php
// id_login is defined in siswa/index.php
$id_login = $_SESSION['siswa'];

if (isset($_POST['updateProfile'])) {
    $alert_msg = "";
    
    // 1. Data Personal
    $nama = mysqli_real_escape_string($con, $_POST['nama_siswa']);
    $tempat = mysqli_real_escape_string($con, $_POST['tempat_lahir']);
    $tgl = mysqli_real_escape_string($con, $_POST['tgl_lahir']);
    $jk = mysqli_real_escape_string($con, $_POST['jk']);
    $alamat = mysqli_real_escape_string($con, $_POST['alamat']);
    
    $suku = mysqli_real_escape_string($con, $_POST['suku']);
    $kewarganegaraan = mysqli_real_escape_string($con, $_POST['kewarganegaraan']);
    $asal_sekolah = mysqli_real_escape_string($con, $_POST['asal_sekolah']);
    $agama = mysqli_real_escape_string($con, $_POST['agama']);

    // 2. Data Alamat & Kontak
    $nik = mysqli_real_escape_string($con, $_POST['nik']);
    $jalan = mysqli_real_escape_string($con, $_POST['jalan']);
    $dusun = mysqli_real_escape_string($con, $_POST['dusun']);
    $kelurahan = mysqli_real_escape_string($con, $_POST['kelurahan']);
    $provinsi = mysqli_real_escape_string($con, $_POST['provinsi']);
    $kabupaten = mysqli_real_escape_string($con, $_POST['kabupaten']);
    $kecamatan = mysqli_real_escape_string($con, $_POST['kecamatan']);
    $jenis_tinggal = mysqli_real_escape_string($con, $_POST['jenis_tinggal']);
    $transportasi = mysqli_real_escape_string($con, $_POST['transportasi']);
    $rt = mysqli_real_escape_string($con, $_POST['rt']);
    $rw = mysqli_real_escape_string($con, $_POST['rw']);
    $kode_pos = mysqli_real_escape_string($con, $_POST['kode_pos']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $no_hp = mysqli_real_escape_string($con, $_POST['no_hp']);
    $no_hp_ortu = mysqli_real_escape_string($con, $_POST['no_hp_ortu']);

    // 3. Data Keluarga
    $nama_ayah = mysqli_real_escape_string($con, $_POST['nama_ayah']);
    $tgl_lahir_ayah = mysqli_real_escape_string($con, !empty($_POST['tgl_lahir_ayah']) ? $_POST['tgl_lahir_ayah'] : '0000-00-00');
    $pendidikan_ayah = mysqli_real_escape_string($con, $_POST['pendidikan_ayah']);
    $pekerjaan_ayah = mysqli_real_escape_string($con, $_POST['pekerjaan_ayah']);
    $penghasilan_ayah = mysqli_real_escape_string($con, $_POST['penghasilan_ayah']);

    $nama_ibu = mysqli_real_escape_string($con, $_POST['nama_ibu']);
    $tgl_lahir_ibu = mysqli_real_escape_string($con, !empty($_POST['tgl_lahir_ibu']) ? $_POST['tgl_lahir_ibu'] : '0000-00-00');
    $pendidikan_ibu = mysqli_real_escape_string($con, $_POST['pendidikan_ibu']);
    $pekerjaan_ibu = mysqli_real_escape_string($con, $_POST['pekerjaan_ibu']);
    $penghasilan_ibu = mysqli_real_escape_string($con, $_POST['penghasilan_ibu']);

    // Server-Side Validations
    if (!validate_name($nama, true)) {
        echo "<script>alert('Format nama siswa tidak valid atau terlalu panjang (maksimal 60 karakter)!'); window.history.back();</script>";
        exit();
    }
    if (!empty($no_hp) && !validate_phone($no_hp)) {
        echo "<script>alert('Nomor HP siswa hanya boleh berisi angka dan maksimal 13 digit!'); window.history.back();</script>";
        exit();
    }
    if (!empty($no_hp_ortu) && !validate_phone($no_hp_ortu)) {
        echo "<script>alert('Nomor HP orang tua hanya boleh berisi angka dan maksimal 13 digit!'); window.history.back();</script>";
        exit();
    }
    if (!empty($nama_ayah) && !validate_name($nama_ayah)) {
        echo "<script>alert('Format nama ayah tidak valid atau terlalu panjang (maksimal 60 karakter)!'); window.history.back();</script>";
        exit();
    }
    if (!empty($nama_ibu) && !validate_name($nama_ibu)) {
        echo "<script>alert('Format nama ibu tidak valid atau terlalu panjang (maksimal 60 karakter)!'); window.history.back();</script>";
        exit();
    }
    if (!empty($nik) && (!preg_match("/^[0-9]+$/", $nik) || strlen($nik) > 16)) {
        echo "<script>alert('NIK hanya boleh berisi angka dan maksimal 16 digit!'); window.history.back();</script>";
        exit();
    }
    if (!empty($kode_pos) && (!preg_match("/^[0-9]+$/", $kode_pos) || strlen($kode_pos) > 5)) {
        echo "<script>alert('Kode Pos hanya boleh berisi angka dan maksimal 5 digit!'); window.history.back();</script>";
        exit();
    }
    if (!empty($email) && !validate_email($email)) {
        echo "<script>alert('Format email tidak valid!'); window.history.back();</script>";
        exit();
    }

    $update_info = mysqli_query($con, "UPDATE tb_siswa SET 
        nama_siswa = '$nama',
        tempat_lahir = '$tempat',
        tgl_lahir = '$tgl',
        jk = '$jk',
        alamat = '$alamat',
        suku = '$suku',
        kewarganegaraan = '$kewarganegaraan',
        asal_sekolah = '$asal_sekolah',
        agama = '$agama',
        nik = '$nik',
        jalan = '$jalan',
        dusun = '$dusun',
        kelurahan = '$kelurahan',
        provinsi = '$provinsi',
        kabupaten = '$kabupaten',
        kecamatan = '$kecamatan',
        jenis_tinggal = '$jenis_tinggal',
        transportasi = '$transportasi',
        rt = '$rt',
        rw = '$rw',
        kode_pos = '$kode_pos',
        email = '$email',
        no_hp = '$no_hp',
        no_hp_ortu = '$no_hp_ortu',
        nama_ayah = '$nama_ayah',
        tgl_lahir_ayah = '$tgl_lahir_ayah',
        pendidikan_ayah = '$pendidikan_ayah',
        pekerjaan_ayah = '$pekerjaan_ayah',
        penghasilan_ayah = '$penghasilan_ayah',
        nama_ibu = '$nama_ibu',
        tgl_lahir_ibu = '$tgl_lahir_ibu',
        pendidikan_ibu = '$pendidikan_ibu',
        pekerjaan_ibu = '$pekerjaan_ibu',
        penghasilan_ibu = '$penghasilan_ibu'
        WHERE id_siswa = '$id_login'");
    
    if ($update_info) {
        $alert_msg .= "Detail profil & data keluarga berhasil disimpan! ";
    }

    // 4. Password change logic (Optional)
    if (!empty($_POST['pass1']) && !empty($_POST['pass2']) && !empty($_POST['pass3'])) {
        $passLama = $_POST['pass1'];
        $passBaru = $_POST['pass2'];
        $confPass = $_POST['pass3'];

        $verified = false;
        if (password_verify($passLama, $data['password'])) {
            $verified = true;
        } elseif (sha1($passLama) === $data['password']) {
            $verified = true;
        }

        if (!$verified) {
            echo "<script>alert('Password lama Anda salah!'); window.location='?page=profil';</script>";
            exit();
        } elseif ($passBaru != $confPass) {
            echo "<script>alert('Konfirmasi password tidak cocok!'); window.location='?page=profil';</script>";
            exit();
        } else {
            $hashedPassBaru = password_hash($passBaru, PASSWORD_DEFAULT);
            mysqli_query($con, "UPDATE tb_siswa SET password='$hashedPassBaru' WHERE id_siswa='$id_login'");
            $alert_msg .= "Password berhasil diperbarui! ";
        }
    }

    // 5. Photo upload logic (Optional)
    if (!empty($_FILES['foto']['name'])) {
        $upload = secure_upload_image('foto', '../assets/img/user/');
        if ($upload['status']) {
            $new_name = $upload['filename'];
            $target = "../assets/img/user/";
            // Delete old image if it is not a default avatar
            if ($data['foto'] != '17603.png' && $data['foto'] != '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg' && !empty($data['foto'])) {
                @unlink($target . $data['foto']);
            }
            mysqli_query($con, "UPDATE tb_siswa SET foto='$new_name' WHERE id_siswa='$id_login'");
            $alert_msg .= "Foto profil berhasil diperbarui! ";
        } else {
            $msg = mysqli_real_escape_string($con, $upload['msg']);
            echo "<script>alert('$msg');</script>";
        }
    }
    
    if (!empty($alert_msg)) {
        echo "<script>alert('$alert_msg'); window.location='?page=profil';</script>";
    } else {
        echo "<script>window.location='?page=profil';</script>";
    }
    exit();
}

// Check completeness of student profile
$is_complete = (!empty($data['nama_siswa']) && !empty($data['tempat_lahir']) && !empty($data['tgl_lahir']) && !empty($data['jk']) && !empty($data['alamat']) && $data['foto'] != '17603.png' && $data['foto'] != '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg');

// Prepare live KTS validation QR code
$qr_data_live = "NISN:" . $data['nis'] . "%7CNama:" . urlencode((string)$data['nama_siswa']) . "%7CKelas:" . urlencode((string)$data['nama_kelas']) . "%7CSMA_N_1_Pangkalan_Koto_Baru";
$qr_url_live = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $qr_data_live;
?>

<!-- iRaise Premium UI Styles -->
<style>
    .iraise-profile-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        text-align: center;
        padding-bottom: 25px;
        transition: all 0.3s ease;
    }
    .iraise-profile-header {
        background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
        height: 120px;
        position: relative;
    }
    .iraise-avatar-container {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        border: 5px solid #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        background: #fff;
        margin: -65px auto 15px auto;
        overflow: hidden;
        position: relative;
        z-index: 2;
    }
    .iraise-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .iraise-profile-name {
        font-family: 'Outfit', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #111;
        margin-bottom: 5px;
    }
    .iraise-profile-nis {
        font-size: 14px;
        color: #666;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .iraise-profile-class {
        display: inline-block;
        background: rgba(13, 71, 161, 0.1);
        color: #0d47a1;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 15px;
        border-radius: 30px;
        margin-bottom: 20px;
    }
    
    /* Tabs & Content Styling */
    .iraise-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }
    .iraise-card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #ebebeb;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .iraise-tabs {
        border-bottom: none;
        display: flex;
        gap: 6px;
    }
    .iraise-tab-item {
        margin-bottom: 0;
    }
    .iraise-tab-link {
        border: none !important;
        border-radius: 30px !important;
        color: #555 !important;
        font-weight: 600;
        font-size: 12.5px;
        padding: 8px 16px !important;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .iraise-tab-link.active {
        background-color: #0d47a1 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
    }
    .iraise-tab-link:hover:not(.active) {
        background-color: #eee !important;
        color: #111 !important;
    }

    .iraise-save-btn {
        background: #4caf50;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        border-radius: 30px;
        padding: 10px 24px;
        border: none;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .iraise-save-btn:hover {
        background: #43a047;
        transform: translateY(-2px);
    }

    .iraise-card-body {
        padding: 30px;
    }

    .form-group-label {
        font-weight: 700;
        color: #444;
        font-size: 13px;
        margin-bottom: 6px;
    }
    .form-control-iraise {
        border-radius: 8px;
        border: 1.5px solid #dcdcdc;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #fafafa;
    }
    .form-control-iraise:focus {
        border-color: #0d47a1;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1);
    }

    /* Green Checkbox Radio styling exactly like iRaise */
    .iraise-gender-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 5px 0;
    }
    .iraise-gender-option {
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
        user-select: none;
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }
    .iraise-gender-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    .iraise-checkmark {
        height: 18px;
        width: 18px;
        background-color: #eee;
        border: 1.5px solid #ccc;
        border-radius: 3px;
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .iraise-gender-option:hover input ~ .iraise-checkmark {
        background-color: #ddd;
    }
    .iraise-gender-option input:checked ~ .iraise-checkmark {
        background-color: #8bc34a;
        border-color: #7cb342;
    }
    .iraise-checkmark::after {
        content: "";
        display: none;
        width: 4.5px;
        height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) translate(-0.5px, -0.5px);
    }
    .iraise-gender-option input:checked ~ .iraise-checkmark::after {
        display: block;
    }

    /* Live Preview KTS styles */
    .kts-preview-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        padding: 15px;
        background: #fdfdfd;
        border-radius: 12px;
        border: 2px dashed #ddd;
    }
    .kts-card-mini {
        width: 100%;
        max-width: 480px;
        aspect-ratio: 1.58;
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #ebf7ee 30%, #b3ebd0 70%, #58a070 100%);
        border-radius: 6px;
        box-shadow: 0 8px 24px rgba(43, 76, 63, 0.15);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
    }
    
    /* Center logo watermark in preview */
    .kts-card-mini-wm-logo {
        position: absolute;
        top: 52%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 140px;
        height: 140px;
        object-fit: contain;
        opacity: 0.045;
        z-index: 1;
        pointer-events: none;
    }

    .kts-card-mini-header {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        padding: 8px 12px 4px 12px;
    }
    .kts-card-mini-logo-box {
        width: 42px;
        height: 42px;
        border: 1.2px solid #234c31;
        padding: 1.5px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kts-card-mini-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .kts-card-mini-header-text {
        flex: 1;
        text-align: center;
        margin-left: 8px;
    }
    .kts-card-mini-header-text h1 {
        color: #1a3c24;
        font-family: 'Outfit', sans-serif;
        font-size: 10.5px;
        font-weight: 800;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }
    .kts-card-mini-header-text h2 {
        color: #234c31;
        font-size: 8px;
        font-weight: 800;
        margin: 1px 0 0 0;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    .kts-card-mini-body {
        position: relative;
        z-index: 2;
        flex: 1;
        display: flex;
        padding: 4px 12px 10px 12px;
        align-items: center;
    }
    .kts-card-mini-details {
        flex: 1;
        font-size: 8.5px;
        font-weight: 800;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #1a3c24;
        gap: 3px;
    }
    .kts-card-mini-details div {
        display: flex;
    }
    .kts-card-mini-details span.lbl {
        width: 58px;
        color: #1a3c24;
        text-transform: uppercase;
    }
    .kts-card-mini-details span.val {
        flex: 1;
        color: #1a3c24;
        text-transform: uppercase;
    }
    .kts-card-mini-qr {
        width: 48px;
        height: 48px;
        display: block;
        margin: 0 6px;
        flex-shrink: 0;
    }
    .kts-card-mini-photo-frame {
        width: 82px;
        height: 105px;
        background: #c63333; /* Red backdrop */
        border: 1px solid #234c31;
        padding: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        margin-right: 0;
        flex-shrink: 0;
    }
    .kts-card-mini-photo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0;
    }

    /* Floating trigger upload photo button */
    .iraise-upload-trigger {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: #0d47a1;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        border: 2px solid #fff;
        transition: all 0.2s ease;
    }
    .iraise-upload-trigger:hover {
        background: #1565c0;
        transform: scale(1.1);
    }
</style>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Profil Saya</h4>
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
                <a href="#">Lengkapi Informasi & KTS</a>
            </li>
        </ul>
    </div>

    <!-- Master Form wrapping the entire page -->
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            
            <!-- LEFT PROFILE CARD (Pedro Masdy style) -->
            <div class="col-md-4">
                <div class="iraise-profile-card">
                    <div class="iraise-profile-header"></div>
                    <div class="iraise-avatar-container">
                        <img src="../assets/img/user/<?=$data['foto'];?>" id="avatar-preview" alt="Avatar" class="iraise-avatar-img">
                        <label for="foto-upload" class="iraise-upload-trigger" title="Ubah Foto Profil">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="foto-upload" name="foto" style="display: none;" onchange="previewImage(this);">
                    </div>
                    <div class="iraise-profile-name"><?=$data['nama_siswa'] ?></div>
                    <div class="iraise-profile-nis">NISN: <?=$data['nis'] ?></div>
                    <div class="iraise-profile-class">Kelas: <?=$data['nama_kelas'] ?></div>
                    
                    <hr class="mx-4 my-2">
                    
                    <div class="px-4 text-left">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Status Akun:</span>
                            <span class="badge badge-success">Aktif</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Tahun Angkatan:</span>
                            <span class="font-weight-bold small"><?=$data['th_angkatan'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Status KTS:</span>
                            <?php if ($is_complete) { ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Lengkap & Valid</span>
                            <?php } else { ?>
                                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Belum Lengkap</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT TABBED CARD (iRaise style) -->
            <div class="col-md-8">
                <div class="iraise-card">
                    
                    <!-- TAB NAVIGATION HEADER WITH GLOWING SAVE BUTTON -->
                    <div class="iraise-card-header">
                        <ul class="nav nav-tabs iraise-tabs" id="profileTabs" role="tablist">
                            <li class="iraise-tab-item" role="presentation">
                                <a class="iraise-tab-link active" id="identitas-tab" data-toggle="tab" href="#identitas" role="tab" aria-controls="identitas" aria-selected="true">
                                    <i class="fas fa-user-circle"></i> Identitas
                                </a>
                            </li>
                            <li class="iraise-tab-item" role="presentation">
                                <a class="iraise-tab-link" id="alamat-tab" data-toggle="tab" href="#alamat" role="tab" aria-controls="alamat" aria-selected="false">
                                    <i class="fas fa-map-marker-alt"></i> Alamat
                                </a>
                            </li>
                            <li class="iraise-tab-item" role="presentation">
                                <a class="iraise-tab-link" id="keluarga-tab" data-toggle="tab" href="#keluarga" role="tab" aria-controls="keluarga" aria-selected="false">
                                    <i class="fas fa-users"></i> Keluarga
                                </a>
                            </li>
                            <li class="iraise-tab-item" role="presentation">
                                <a class="iraise-tab-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">
                                    <i class="fas fa-shield-alt"></i> Keamanan
                                </a>
                            </li>
                            <li class="iraise-tab-item" role="presentation">
                                <a class="iraise-tab-link" id="kts-tab" data-toggle="tab" href="#kts" role="tab" aria-controls="kts" aria-selected="false">
                                    <i class="fas fa-id-card"></i> KTS Preview
                                </a>
                            </li>
                        </ul>
                        
                        <!-- SIMPAN BUTTON (iRaise header style) -->
                        <button type="submit" name="updateProfile" class="iraise-save-btn">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>

                    <!-- TAB CONTENT -->
                    <div class="iraise-card-body">
                        <div class="tab-content" id="profileTabsContent">
                            
                            <!-- TAB 1: IDENTITAS DIRI (Data Personal) -->
                            <div class="tab-pane fade show active" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">
                                <h3 class="font-weight-bold mb-4 text-primary" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-info-circle mr-2"></i>Data Personal</h3>
                                
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">NISN</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-iraise" value="<?=$data['nis'];?>" disabled style="background-color: #eee; cursor: not-allowed; font-weight: bold;">
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Nama Siswa</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-iraise" value="<?=$data['nama_siswa'];?>" disabled style="background-color: #eee; cursor: not-allowed; font-weight: bold;">
                                                <input type="hidden" name="nama_siswa" value="<?=$data['nama_siswa'];?>">
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-start mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right pt-2">Jenis Kelamin</label>
                                            <div class="col-sm-8">
                                                <div class="iraise-gender-container">
                                                    <label class="iraise-gender-option">
                                                        <input type="radio" name="jk" value="L" <?= $data['jk'] == 'L' ? 'checked' : ''; ?>>
                                                        <span class="iraise-checkmark"></span>
                                                        Laki-Laki
                                                    </label>
                                                    <label class="iraise-gender-option">
                                                        <input type="radio" name="jk" value="P" <?= $data['jk'] == 'P' ? 'checked' : ''; ?>>
                                                        <span class="iraise-checkmark"></span>
                                                        Perempuan
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Agama</label>
                                            <div class="col-sm-8">
                                                <select name="agama" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['agama']) ? 'selected' : ''; ?> hidden>Pilih Agama</option>
                                                    <option value="Islam" <?= $data['agama'] == 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                                    <option value="Kristen" <?= $data['agama'] == 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                                                    <option value="Katolik" <?= $data['agama'] == 'Katolik' ? 'selected' : ''; ?>>Katolik</option>
                                                    <option value="Hindu" <?= $data['agama'] == 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                                    <option value="Budha" <?= $data['agama'] == 'Budha' ? 'selected' : ''; ?>>Budha</option>
                                                    <option value="Khonghucu" <?= $data['agama'] == 'Khonghucu' ? 'selected' : ''; ?>>Khonghucu</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Suku</label>
                                            <div class="col-sm-8">
                                                <select name="suku" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['suku']) ? 'selected' : ''; ?> hidden>Pilih Suku</option>
                                                    <option value="Minangkabau" <?= $data['suku'] == 'Minangkabau' ? 'selected' : ''; ?>>Minangkabau</option>
                                                    <option value="Melayu" <?= $data['suku'] == 'Melayu' ? 'selected' : ''; ?>>Melayu</option>
                                                    <option value="Jawa" <?= $data['suku'] == 'Jawa' ? 'selected' : ''; ?>>Jawa</option>
                                                    <option value="Sunda" <?= $data['suku'] == 'Sunda' ? 'selected' : ''; ?>>Sunda</option>
                                                    <option value="Batak" <?= $data['suku'] == 'Batak' ? 'selected' : ''; ?>>Batak</option>
                                                    <option value="Lainnya" <?= $data['suku'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Kewarganegaraan</label>
                                            <div class="col-sm-8">
                                                <select name="kewarganegaraan" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['kewarganegaraan']) ? 'selected' : ''; ?> hidden>Pilih Kewarganegaraan</option>
                                                    <option value="Indonesia" <?= $data['kewarganegaraan'] == 'Indonesia' ? 'selected' : ''; ?>>Indonesia</option>
                                                    <option value="WNA" <?= $data['kewarganegaraan'] == 'WNA' ? 'selected' : ''; ?>>WNA</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Tgl. Lahir</label>
                                            <div class="col-sm-8">
                                                <input type="date" name="tgl_lahir" class="form-control form-control-iraise" value="<?=$data['tgl_lahir'];?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Tempat Lahir</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="tempat_lahir" class="form-control form-control-iraise" value="<?=$data['tempat_lahir'];?>" placeholder="Contoh: PEKANBARU" required style="text-transform: uppercase;">
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Asal Sekolah</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="asal_sekolah" class="form-control form-control-iraise" value="<?= !empty($data['asal_sekolah']) ? htmlspecialchars($data['asal_sekolah']) : 'SMAN 1 KEC PA'; ?>" placeholder="Nama Asal Sekolah">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: ALAMAT TINGGAL -->
                            <div class="tab-pane fade" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
                                <h3 class="font-weight-bold mb-4 text-primary" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-map-marked-alt mr-2"></i>Alamat Pribadi</h3>
                                
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">NIK</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="nik" class="form-control form-control-iraise" value="<?=$data['nik'];?>" placeholder="Nomor Induk Kependudukan" maxlength="16" pattern="[0-9]+" title="NIK hanya boleh berisi angka">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Jalan</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="jalan" class="form-control form-control-iraise" value="<?=$data['jalan'];?>" placeholder="Nama Jalan">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Dusun</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="dusun" class="form-control form-control-iraise" value="<?=$data['dusun'];?>" placeholder="Nama Dusun">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Kelurahan</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="kelurahan" class="form-control form-control-iraise" value="<?=$data['kelurahan'];?>" placeholder="Nama Kelurahan">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Provinsi</label>
                                            <div class="col-sm-8">
                                                <select name="provinsi" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['provinsi']) ? 'selected' : ''; ?> hidden>Pilih Provinsi</option>
                                                    <?php 
                                                    $provinsi_opts = [
                                                        'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 
                                                        'Jambi', 'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung', 
                                                        'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 
                                                        'Jawa Timur', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
                                                        'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 
                                                        'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara', 
                                                        'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
                                                    ];
                                                    $curr_prov = !empty($data['provinsi']) ? $data['provinsi'] : '';
                                                    if (!empty($curr_prov) && !in_array($curr_prov, $provinsi_opts) && !in_array(str_replace('Prop. ', '', $curr_prov), $provinsi_opts)) {
                                                        $provinsi_opts[] = $curr_prov;
                                                    }
                                                    foreach ($provinsi_opts as $opt) {
                                                        $sel = ($data['provinsi'] == $opt || str_replace('Prop. ', '', $data['provinsi']) == $opt) ? 'selected' : '';
                                                        echo "<option value='$opt' $sel>$opt</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Kabupaten/Kota</label>
                                            <div class="col-sm-8">
                                                <select name="kabupaten" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['kabupaten']) ? 'selected' : ''; ?> hidden>Pilih Kabupaten/Kota</option>
                                                    <?php 
                                                    $kabupaten_opts = [
                                                        'Kab. Lima Puluh Kota', 'Kota Payakumbuh', 'Kab. Agam', 'Kota Bukittinggi', 
                                                        'Kab. Tanah Datar', 'Kota Padang Panjang', 'Kota Padang', 'Kab. Padang Pariaman', 
                                                        'Kota Pariaman', 'Kab. Pesisir Selatan', 'Kab. Solok', 'Kota Solok', 
                                                        'Kab. Pasaman', 'Kab. Pasaman Barat', 'Kab. Kampar', 'Kota Pekanbaru', 
                                                        'Kab. Rokan Hulu', 'Kab. Rokan Hilir', 'Kab. Siak', 'Kab. Pelalawan', 
                                                        'Kab. Kuantan Singingi', 'Kab. Bengkalis', 'Kota Dumai', 'Kab. Solok Selatan',
                                                        'Kab. Dharmasraya', 'Kab. Sijunjung', 'Kota Sawahlunto', 'Kab. Kepulauan Mentawai', 'Lainnya'
                                                    ];
                                                    $curr_kab = !empty($data['kabupaten']) ? $data['kabupaten'] : '';
                                                    if (!empty($curr_kab) && !in_array($curr_kab, $kabupaten_opts)) {
                                                        $kabupaten_opts[] = $curr_kab;
                                                    }
                                                    foreach ($kabupaten_opts as $opt) {
                                                        $sel = ($data['kabupaten'] == $opt) ? 'selected' : '';
                                                        echo "<option value='$opt' $sel>$opt</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Kecamatan</label>
                                            <div class="col-sm-8">
                                                <select name="kecamatan" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['kecamatan']) ? 'selected' : ''; ?> hidden>Pilih Kecamatan</option>
                                                    <?php 
                                                    $kecamatan_opts = [
                                                        'Kec. Pangkalan Koto Baru', 'Kec. Harau', 'Kec. Kapur IX', 'Kec. Luak', 
                                                        'Kec. Lareh Sago Halaban', 'Kec. Mungka', 'Kec. Payakumbuh', 'Kec. Guguak', 
                                                        'Kec. Bukit Barisan', 'Kec. Akabiluru', 'Kec. Suliki', 'Kec. Gunuang Omeh', 
                                                        'Kec. Situjuah Limo Nagari', 'Kec. XIII Koto Kampar', 'Kec. Koto Kampar Hulu', 'Lainnya'
                                                    ];
                                                    $curr_kec = !empty($data['kecamatan']) ? $data['kecamatan'] : '';
                                                    if (!empty($curr_kec) && !in_array($curr_kec, $kecamatan_opts)) {
                                                        $kecamatan_opts[] = $curr_kec;
                                                    }
                                                    foreach ($kecamatan_opts as $opt) {
                                                        $sel = ($data['kecamatan'] == $opt) ? 'selected' : '';
                                                        echo "<option value='$opt' $sel>$opt</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Jenis Tinggal</label>
                                            <div class="col-sm-8">
                                                <select name="jenis_tinggal" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['jenis_tinggal']) ? 'selected' : ''; ?> hidden>Pilih Jenis Tinggal</option>
                                                    <option value="Kost" <?= $data['jenis_tinggal'] == 'Kost' ? 'selected' : ''; ?>>Kost</option>
                                                    <option value="Bersama Orang Tua" <?= $data['jenis_tinggal'] == 'Bersama Orang Tua' ? 'selected' : ''; ?>>Bersama Orang Tua</option>
                                                    <option value="Asrama" <?= $data['jenis_tinggal'] == 'Asrama' ? 'selected' : ''; ?>>Asrama</option>
                                                    <option value="Lainnya" <?= $data['jenis_tinggal'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Alat Transportasi</label>
                                            <div class="col-sm-8">
                                                <select name="transportasi" class="form-control form-control-iraise" required>
                                                    <option value="" disabled <?= empty($data['transportasi']) ? 'selected' : ''; ?> hidden>Pilih Alat Transportasi</option>
                                                    <option value="Jalan Kaki" <?= $data['transportasi'] == 'Jalan Kaki' ? 'selected' : ''; ?>>Jalan Kaki</option>
                                                    <option value="Sepeda Motor" <?= $data['transportasi'] == 'Sepeda Motor' ? 'selected' : ''; ?>>Sepeda Motor</option>
                                                    <option value="Mobil" <?= $data['transportasi'] == 'Mobil' ? 'selected' : ''; ?>>Mobil</option>
                                                    <option value="Angkutan Umum" <?= $data['transportasi'] == 'Angkutan Umum' ? 'selected' : ''; ?>>Angkutan Umum</option>
                                                    <option value="Lainnya" <?= $data['transportasi'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">RT/RW</label>
                                            <div class="col-sm-8 d-flex align-items-center gap-2">
                                                <input type="text" name="rt" class="form-control form-control-iraise text-center" value="<?=$data['rt'];?>" placeholder="0" style="width: 70px;">
                                                <span class="font-weight-bold">/</span>
                                                <input type="text" name="rw" class="form-control form-control-iraise text-center" value="<?=$data['rw'];?>" placeholder="0" style="width: 70px;">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Kode Pos</label>
                                            <div class="col-sm-8 d-flex align-items-center gap-2">
                                                <input type="text" name="kode_pos" class="form-control form-control-iraise" value="<?=$data['kode_pos'];?>" placeholder="26272" style="flex: 1;" maxlength="5" pattern="[0-9]+" title="Kode Pos hanya boleh berisi angka">
                                                <span class="text-muted small bg-light border px-2 py-2 rounded">ex. 12345</span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">Email</label>
                                            <div class="col-sm-8">
                                                <input type="email" name="email" class="form-control form-control-iraise" value="<?=$data['email'];?>" placeholder="contoh@gmail.com">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">No. HP</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="no_hp" class="form-control form-control-iraise" value="<?=$data['no_hp'];?>" placeholder="08XXXXXXXXXX" maxlength="13" pattern="[0-9]+" title="Nomor HP hanya boleh berisi angka">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center mb-3">
                                            <label class="col-sm-4 col-form-label form-group-label text-md-right">No. HP Orang Tua</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="no_hp_ortu" class="form-control form-control-iraise" value="<?=$data['no_hp_ortu'];?>" placeholder="08XXXXXXXXXX" maxlength="13" pattern="[0-9]+" title="Nomor HP hanya boleh berisi angka">
                                            </div>
                                        </div>
                                        
                                        <!-- General Alamat Textarea (Synchronized) -->
                                        <div class="form-group mt-4">
                                            <label class="form-group-label">Alamat Rumah Lengkap (Untuk KTS)</label>
                                            <textarea name="alamat" class="form-control form-control-iraise" rows="3" required placeholder="Masukkan alamat lengkap rumah untuk dicetak di KTS..."><?=$data['alamat'];?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: DATA KELUARGA -->
                            <div class="tab-pane fade" id="keluarga" role="tabpanel" aria-labelledby="keluarga-tab">
                                <h3 class="font-weight-bold mb-4 text-primary" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-users mr-2"></i>Data Keluarga</h3>
                                
                                <div class="row">
                                    <!-- Father's Profile (Left Column) -->
                                    <div class="col-md-6 border-right">
                                        <h5 class="font-weight-bold text-info mb-3"><i class="fas fa-user-tie mr-1"></i> Data Ayah</h5>
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Nama Ayah</label>
                                            <input type="text" name="nama_ayah" class="form-control form-control-iraise" value="<?=$data['nama_ayah'];?>" placeholder="Nama Lengkap Ayah Kandung" maxlength="60" pattern="^[a-zA-Z\s.,'`\(\)-]+$" title="Nama hanya boleh berisi huruf, spasi, titik, koma, tanda petik, tanda kurung, dan tanda hubung">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Tanggal Lahir Ayah</label>
                                            <input type="date" name="tgl_lahir_ayah" class="form-control form-control-iraise" value="<?= ($data['tgl_lahir_ayah'] != '0000-00-00') ? $data['tgl_lahir_ayah'] : ''; ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Pendidikan Terakhir Ayah</label>
                                            <select name="pendidikan_ayah" class="form-control form-control-iraise" required>
                                                <option value="" disabled <?= empty($data['pendidikan_ayah']) ? 'selected' : ''; ?> hidden>Pilih Pendidikan</option>
                                                <?php 
                                                $pendidikan_opts = ['SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'];
                                                foreach ($pendidikan_opts as $opt) {
                                                    $sel = ($data['pendidikan_ayah'] == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Pekerjaan Ayah</label>
                                            <select name="pekerjaan_ayah" class="form-control form-control-iraise" required>
                                                <option value="" disabled <?= empty($data['pekerjaan_ayah']) ? 'selected' : ''; ?> hidden>Pilih Pekerjaan</option>
                                                <?php 
                                                $pekerjaan_opts = [
                                                    'Tidak Bekerja / Ibu Rumah Tangga', 'Pegawai Negeri Sipil (PNS)', 'TNI', 'POLRI', 
                                                    'Karyawan Swasta', 'Karyawan BUMN', 'Wiraswasta / Pedagang', 'Petani / Pekebun', 
                                                    'Nelayan', 'Buruh Harian Lepas', 'Pensiunan', 'Sudah Meninggal', 'Lainnya'
                                                ];
                                                foreach ($pekerjaan_opts as $opt) {
                                                    $sel = ($data['pekerjaan_ayah'] == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Penghasilan Ayah</label>
                                            <select name="penghasilan_ayah" class="form-control form-control-iraise" required>
                                                <option value="" disabled <?= empty($data['penghasilan_ayah']) ? 'selected' : ''; ?> hidden>Pilih Penghasilan</option>
                                                <?php 
                                                $penghasilan_opts = ['Rp. 0 - Rp. 500,000', 'Rp. 500,000 - Rp. 999,999', 'Rp. 1,000,000 - Rp. 1,999,999', 'Rp. 2,000,000 - Rp. 4,999,999', 'Rp. 5,000,000+'];
                                                foreach ($penghasilan_opts as $opt) {
                                                    $sel = ($data['penghasilan_ayah'] == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Mother's Profile (Right Column) -->
                                    <div class="col-md-6">
                                        <h5 class="font-weight-bold text-danger mb-3"><i class="fas fa-female mr-1"></i> Data Ibu</h5>
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Nama Ibu</label>
                                            <input type="text" name="nama_ibu" class="form-control form-control-iraise" value="<?=$data['nama_ibu'];?>" placeholder="Nama Lengkap Ibu Kandung" maxlength="60" pattern="^[a-zA-Z\s.,'`\(\)-]+$" title="Nama hanya boleh berisi huruf, spasi, titik, koma, tanda petik, tanda kurung, dan tanda hubung">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Tanggal Lahir Ibu</label>
                                            <input type="date" name="tgl_lahir_ibu" class="form-control form-control-iraise" value="<?= ($data['tgl_lahir_ibu'] != '0000-00-00') ? $data['tgl_lahir_ibu'] : ''; ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Pendidikan Terakhir Ibu</label>
                                            <select name="pendidikan_ibu" class="form-control form-control-iraise" required>
                                                <option value="" disabled <?= empty($data['pendidikan_ibu']) ? 'selected' : ''; ?> hidden>Pilih Pendidikan</option>
                                                <?php 
                                                foreach ($pendidikan_opts as $opt) {
                                                    $sel = ($data['pendidikan_ibu'] == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Pekerjaan Ibu</label>
                                            <select name="pekerjaan_ibu" class="form-control form-control-iraise" required>
                                                <option value="" disabled <?= empty($data['pekerjaan_ibu']) ? 'selected' : ''; ?> hidden>Pilih Pekerjaan</option>
                                                <?php 
                                                $pekerjaan_ibu_opts = [
                                                    'Ibu Rumah Tangga', 'Pegawai Negeri Sipil (PNS)', 'TNI', 'POLRI', 
                                                    'Karyawan Swasta', 'Karyawan BUMN', 'Wiraswasta / Pedagang', 'Petani / Pekebun', 
                                                    'Nelayan', 'Buruh Harian Lepas', 'Pensiunan', 'Sudah Meninggal', 'Lainnya'
                                                ];
                                                foreach ($pekerjaan_ibu_opts as $opt) {
                                                    $sel = ($data['pekerjaan_ibu'] == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-group-label">Penghasilan Ibu</label>
                                            <select name="penghasilan_ibu" class="form-control form-control-iraise" required>
                                                <option value="" disabled <?= empty($data['penghasilan_ibu']) ? 'selected' : ''; ?> hidden>Pilih Penghasilan</option>
                                                <?php 
                                                foreach ($penghasilan_opts as $opt) {
                                                    $sel = ($data['penghasilan_ibu'] == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: KEAMANAN AKUN -->
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                <h3 class="font-weight-bold mb-4 text-primary" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-lock mr-2"></i>Ganti Password Keamanan</h3>
                                <div class="alert alert-info py-2 px-3 small mb-4" style="border-radius: 8px;">
                                    <i class="fas fa-info-circle mr-1"></i> Kosongkan seluruh kolom di bawah ini jika Anda <b>tidak</b> berniat mengubah password login Anda.
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="form-group-label">Password Lama</label>
                                        <input type="password" name="pass1" class="form-control form-control-iraise" placeholder="Masukkan password saat ini">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-group-label">Password Baru</label>
                                        <input type="password" name="pass2" class="form-control form-control-iraise" placeholder="Masukkan password baru">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-group-label">Konfirmasi Password</label>
                                        <input type="password" name="pass3" class="form-control form-control-iraise" placeholder="Ketik ulang password baru">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: PREVIEW KARTU SISWA -->
                            <div class="tab-pane fade" id="kts" role="tabpanel" aria-labelledby="kts-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="font-weight-bold mb-0 text-primary" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-id-badge mr-2"></i>Kartu Tanda Siswa (KTS)</h3>
                                    
                                    <a target="_blank" href="modul/user/cetak_kts.php?id=<?=$id_login;?>" class="btn btn-success btn-round px-4 btn-sm" style="font-weight: 700;">
                                        <i class="fas fa-print mr-1"></i> Cetak KTS Fisik
                                    </a>
                                </div>

                                <?php if (!$is_complete) { ?>
                                    <div class="alert alert-warning mb-4" role="alert" style="border-radius: 8px;">
                                        <i class="fas fa-exclamation-triangle mr-2"></i> <b>Perhatian:</b> Profil Anda belum lengkap! Harap lengkapi <b>Identitas Diri</b>, <b>Alamat Rumah</b>, dan **Unggah Foto Formal** Anda (bukan foto avatar bawaan) agar KTS dapat dicetak secara sah dan valid.
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-success mb-4" role="alert" style="border-radius: 8px;">
                                        <i class="fas fa-check-circle mr-2"></i> Profil lengkap dan foto formal valid! Anda dapat mencetak KTS fisik berkualitas tinggi dengan mengklik tombol cetak di atas.
                                    </div>
                                <?php } ?>

                                <div class="kts-preview-box">
                                    <div class="small text-muted font-weight-bold text-uppercase mb-2"><i class="fas fa-eye mr-1"></i>Pratinjau KTS Depan (Landscape)</div>
                                    
                                    <!-- Mini Mockup of front KTS card -->
                                    <div class="kts-card-mini">
                                        <!-- Transparent Logo Watermark -->
                                        <img src="../assets/img/LOGO1.jpg" class="kts-card-mini-wm-logo" alt="">
                                        
                                        <div class="kts-card-mini-header">
                                            <div class="kts-card-mini-logo-box">
                                                <img src="../assets/img/LOGO1.jpg" class="kts-card-mini-logo" alt="Logo">
                                            </div>
                                            <div class="kts-card-mini-header-text">
                                                <h1>SMA Negeri 1 Pangkalan Koto Baru</h1>
                                                <h2>KARTU TANDA SISWA</h2>
                                            </div>
                                        </div>
                                        <div class="kts-card-mini-body">
                                            <div class="kts-card-mini-details">
                                                <div>
                                                    <span class="lbl">NAMA</span>
                                                    <span class="val">: <?= !empty($data['nama_siswa']) ? htmlspecialchars((string)$data['nama_siswa']) : 'Nama Siswa'; ?></span>
                                                </div>
                                                <div>
                                                    <span class="lbl">NISN</span>
                                                    <span class="val">: <?= htmlspecialchars((string)$data['nis']); ?></span>
                                                </div>
                                                <div>
                                                    <span class="lbl">KELAS</span>
                                                    <span class="val">: <?= htmlspecialchars((string)$data['nama_kelas']); ?></span>
                                                </div>
                                                <div>
                                                    <span class="lbl">JURUSAN</span>
                                                    <span class="val">: -</span>
                                                </div>
                                                <div>
                                                    <span class="lbl">BERLAKU S/D</span>
                                                    <span class="val">: <?= $berlaku_tgl; ?></span>
                                                </div>
                                            </div>
                                            
                                            <!-- QR Code directly to the left of the photo -->
                                            <img src="<?=$qr_url_live;?>" class="kts-card-mini-qr" alt="QR">
                                            
                                            <div class="kts-card-mini-photo-frame">
                                                <img src="../assets/img/user/<?=$data['foto'];?>" alt="Foto">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<!-- Preview Avatar Image and Tab Switch on Invalid Field script -->
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-switch tab if there is an invalid required field hidden in a non-active tab
document.addEventListener('DOMContentLoaded', function() {
    var forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('invalid', function(e) {
            var invalidEl = e.target;
            var tabPane = invalidEl.closest('.tab-pane');
            if (tabPane && !tabPane.classList.contains('active')) {
                var tabId = tabPane.getAttribute('id');
                var tabLink = document.querySelector('a[href="#' + tabId + '"]');
                if (tabLink) {
                    // Activate the tab using Bootstrap's Tab plugin
                    $(tabLink).tab('show');
                    
                    // Delay focusing to allow tab transition animation to complete
                    setTimeout(function() {
                        invalidEl.focus();
                    }, 200);
                }
            }
        }, true); // use capture phase because 'invalid' event doesn't bubble
    });
});
</script>