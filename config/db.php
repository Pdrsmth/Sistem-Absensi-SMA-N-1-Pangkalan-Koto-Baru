<?PHP error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); ?>
<?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  $con = new mysqli ("localhost","root","","db_imas") or die(mysqli_error($con));
  $res_mode = $con->query("SELECT @@sql_mode as mode");
  if ($res_mode) {
      $row_mode = $res_mode->fetch_assoc();
      $new_mode = str_replace('ONLY_FULL_GROUP_BY', '', $row_mode['mode']);
      $new_mode = preg_replace('/,+/', ',', trim($new_mode, ','));
      $con->query("SET SESSION sql_mode = '$new_mode'");
  }



   function namaBulan($angka){
	switch ($angka) {
		case '1':
			$bulan = "Januari";
			break;
		case '2':
			$bulan = "Februari";
			break;
		case '3':
			$bulan = "Maret";
			break;
		case '4':
			$bulan = "April";
			break;
		case '5':
			$bulan = "Mei";
			break;
		case '6':
			$bulan = "Juni";
			break;
		case '7':
			$bulan = "Juli";
			break;
		case '8':
			$bulan = "Agustus";
			break;
		case '9':
			$bulan = "September";
			break;
		case '10':
			$bulan = "Oktober";
			break;
		case '11':
			$bulan = "November";
			break;
		case '12':
			$bulan = "Desember";
			break;
		default:
			$bulan = "Tidak ada bulan yang dipilih...";
			break;
	}

	return $bulan;
}

function tglIndonesia($tgl){
	$tanggal = substr($tgl,8,2);
	$bulan = namaBulan(substr($tgl,5,2));
	$tahun = substr($tgl,0,4);
	return $tanggal.' '.$bulan.' '.$tahun;		 
}



// function untuk menampilkan nama hari ini dalam bahasa indonesia
// di buat oleh malasngoding.com

function hari_ini(){
	$hari = date ("D");

	switch($hari){
		case 'Sun':
			$hari_ini = "Minggu";
		break;

		case 'Mon':			
			$hari_ini = "Senin";
		break;

		case 'Tue':
			$hari_ini = "Selasa";
		break;

		case 'Wed':
			$hari_ini = "Rabu";
		break;

		case 'Thu':
			$hari_ini = "Kamis";
		break;

		case 'Fri':
			$hari_ini = "Jumat";
		break;

		case 'Sat':
			$hari_ini = "Sabtu";
		break;
		
		default:
			$hari_ini = "Tidak di ketahui";		
		break;
	}

	return "<b>" .$hari_ini . "</b>";

}


function namahari($tanggalnya){
    //fungsi mencari namahari
    //format $tgl YYYY-MM-DD
    //harviacode.com
    $tgl=substr($tanggalnya,8,2);
    $bln=substr($tanggalnya,5,2);
    $thn=substr($tanggalnya,0,4);
    $info=date('w', mktime(0,0,0,$bln,$tgl,$thn));
    switch($info){
        case '0': return "Minggu"; break;
        case '1': return "Senin"; break;
        case '2': return "Selasa"; break;
        case '3': return "Rabu"; break;
        case '4': return "Kamis"; break;
        case '5': return "Jumat"; break;
        case '6': return "Sabtu"; break;
    };
}

/* =========================================================================
   SECURITY HELPERS (Bcrypt Password Hashing, SQLi Prevention, Safe Uploads)
   ========================================================================= */

/**
 * Validates and saves an uploaded image safely using whitelists, MIME-type verification,
 * and randomized filenames to prevent Path Traversal and Remote Code Execution (RCE).
 */
function secure_upload_image($file_field, $target_dir = "../assets/img/user/") {
    if (!isset($_FILES[$file_field]) || $_FILES[$file_field]['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'msg' => 'Tidak ada file yang diunggah atau terdapat kesalahan.'];
    }

    $file = $_FILES[$file_field];
    $tmp_name = $file['tmp_name'];
    $filename = basename($file['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // 1. Whitelist extension check
    $allowed_exts = ['jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowed_exts)) {
        return ['status' => false, 'msg' => 'Format file tidak didukung! Hanya diperbolehkan JPG, JPEG, atau PNG.'];
    }

    // 2. MIME type check using getimagesize
    $image_info = @getimagesize($tmp_name);
    if ($image_info === false) {
        return ['status' => false, 'msg' => 'File bukan gambar valid!'];
    }

    $allowed_mimes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($image_info['mime'], $allowed_mimes)) {
        return ['status' => false, 'msg' => 'Tipe MIME file tidak diizinkan!'];
    }

    // 3. Randomize filename using unique token to prevent RCE
    $new_filename = time() . "_" . bin2hex(random_bytes(8)) . "." . $ext;

    // Create target directory if it does not exist
    if (!is_dir($target_dir)) {
        @mkdir($target_dir, 0755, true);
    }

    if (move_uploaded_file($tmp_name, $target_dir . $new_filename)) {
        return ['status' => true, 'filename' => $new_filename];
    } else {
        return ['status' => false, 'msg' => 'Gagal menyimpan file ke direktori server.'];
    }
}

/**
 * Securely verifies and transparently migrates users' legacy SHA1 passwords to safe
 * Bcrypt hashes upon their first successful login. This helper also completely sanitizes
 * inputs to prevent SQL Injection during the authentication phase.
 */
function secure_verify_and_migrate_password($con, $table, $id_col, $username_col, $username_val, $plain_pass) {
    $username_escaped = mysqli_real_escape_string($con, $username_val);
    
    // Fetch the user details safely
    $q = mysqli_query($con, "SELECT * FROM $table WHERE $username_col = '$username_escaped' LIMIT 1");
    if (!$q || mysqli_num_rows($q) == 0) {
        return false; // User not found
    }
    
    $user = mysqli_fetch_array($q);
    $db_pass = $user['password'];
    $id_val = $user[$id_col];
    
    // 1. Verify using modern Bcrypt
    if (password_verify($plain_pass, $db_pass)) {
        return $user; // Successfully verified
    }
    
    // 2. Fallback verify using legacy SHA1 for transparent auto-migration
    $sha1_pass = sha1($plain_pass);
    if ($sha1_pass === $db_pass) {
        // Legitimate legacy password match! Auto-migrate to secure Bcrypt hash
        $bcrypt_hash = password_hash($plain_pass, PASSWORD_DEFAULT);
        $update = mysqli_query($con, "UPDATE $table SET password = '$bcrypt_hash' WHERE $id_col = '$id_val'");
        if ($update) {
            $user['password'] = $bcrypt_hash; // Reflect in active memory
        }
        return $user;
    }
    
    return false; // Password incorrect
}

/**
 * Validates a person's name format (letters, spaces, and standard punctuation, no numbers or weird symbols).
 * Maximum length is 60 characters.
 */
function validate_name($name, $required = false) {
    if (empty($name)) {
        return !$required;
    }
    // Length check (max 60)
    if (strlen($name) > 60) {
        return false;
    }
    // Regex check (allows a-z, A-Z, spaces, and punctuation like . , ' - ( ) )
    return preg_match("/^[a-zA-Z\s.,'`\(\)-]+$/", $name);
}

/**
 * Validates a phone number format (only digits, maximum 13 digits).
 */
function validate_phone($phone, $required = false) {
    if (empty($phone)) {
        return !$required;
    }
    // Length check (max 13)
    if (strlen($phone) > 13) {
        return false;
    }
    // Only numbers
    return preg_match("/^[0-9]+$/", $phone);
}

/**
 * Validates an email address.
 */
function validate_email($email, $required = false) {
    if (empty($email)) {
        return !$required;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>