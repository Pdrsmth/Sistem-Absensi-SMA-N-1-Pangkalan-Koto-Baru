<?php 

if (isset($_POST['importSiswa'])) {
    $kelas = mysqli_real_escape_string($con, $_POST['kelas']);
    $file_csv = $_FILES['file_csv']['tmp_name'];
    
    if (($handle = fopen($file_csv, "r")) !== FALSE) {
        // Skip header row
        fgetcsv($handle, 1000, ",");
        
        $imported_count = 0;
        $failed_count = 0;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Check if row has valid columns (NIS and Nama are minimum required)
            if (empty($data[0]) || empty($data[1])) {
                $failed_count++;
                continue;
            }
            
            $nis = mysqli_real_escape_string($con, $data[0]);
            $nama = mysqli_real_escape_string($con, $data[1]);
            $tempat = mysqli_real_escape_string($con, isset($data[2]) ? $data[2] : '');
            $tgl = mysqli_real_escape_string($con, isset($data[3]) ? $data[3] : '');
            $jk = mysqli_real_escape_string($con, isset($data[4]) ? $data[4] : 'L');
            $alamat = mysqli_real_escape_string($con, isset($data[5]) ? $data[5] : '');
            $th_masuk = mysqli_real_escape_string($con, isset($data[6]) ? $data[6] : date('Y'));
            
            $pass = password_hash($nis, PASSWORD_DEFAULT);
            // Default avatar based on gender (L = male blue avatar, P = female standard avatar)
            $nama_gambar = (strtoupper($jk) == 'P' || strtoupper($jk) == 'PEREMPUAN') ? '17603.png' : '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg';
            
            // Check if student with same NIS already exists to prevent duplicate key errors
            $checkExist = mysqli_query($con, "SELECT id_siswa FROM tb_siswa WHERE nis='$nis'");
            if (mysqli_num_rows($checkExist) > 0) {
                $failed_count++;
                continue;
            }
            
            $insert = mysqli_query($con, "INSERT INTO tb_siswa (nis, nama_siswa, tempat_lahir, tgl_lahir, jk, alamat, password, foto, status, th_angkatan, id_mkelas) VALUES ('$nis', '$nama', '$tempat', '$tgl', '$jk', '$alamat', '$pass', '$nama_gambar', '1', '$th_masuk', '$kelas')");
            if ($insert) {
                $imported_count++;
            } else {
                $failed_count++;
            }
        }
        fclose($handle);
        
        echo "
        <script type='text/javascript'>
        setTimeout(function () { 
            swal('Impor Selesai', 'Berhasil mengimpor $imported_count siswa! ($failed_count baris dilewati/gagal)', {
                icon : 'success',
                buttons: { confirm: { className : 'btn btn-success' } }
            });    
        }, 10);  
        window.setTimeout(function(){ 
            window.location.replace('?page=siswa');
        }, 3000);   
        </script>";
    } else {
        echo "<script>alert('Gagal membuka file CSV'); window.location.replace('?page=siswa&act=add');</script>";
    }
} elseif (isset($_POST['saveSiswa'])) {

        $nis = mysqli_real_escape_string($con, $_POST['nis']);
        $nama = mysqli_real_escape_string($con, $_POST['nama']);

        if (!validate_name($nama, true)) {
            echo "<script>alert('Format nama siswa tidak valid atau terlalu panjang (maksimal 60 karakter, tanpa angka/simbol)!'); window.history.back();</script>";
            exit();
        }
        $tempat = mysqli_real_escape_string($con, isset($_POST['tempat']) ? $_POST['tempat'] : '');
        $tgl = mysqli_real_escape_string($con, (!empty($_POST['tgl'])) ? $_POST['tgl'] : '0000-00-00');
        $jk = mysqli_real_escape_string($con, isset($_POST['jk']) ? $_POST['jk'] : 'L');
        $alamat = mysqli_real_escape_string($con, isset($_POST['alamat']) ? $_POST['alamat'] : '');
        $th_masuk = mysqli_real_escape_string($con, (!empty($_POST['th_masuk'])) ? $_POST['th_masuk'] : date('Y'));
        $kelas = mysqli_real_escape_string($con, $_POST['kelas']);
        $pass = password_hash($nis, PASSWORD_DEFAULT);

        // Check if student with same NIS already exists to prevent duplicate key errors
        $checkExist = mysqli_query($con, "SELECT id_siswa FROM tb_siswa WHERE nis='$nis'");
        if (mysqli_num_rows($checkExist) > 0) {
            echo "<script>alert('Siswa dengan NIS/NISN $nis sudah terdaftar!'); window.location.replace('?page=siswa&act=add');</script>";
            exit();
        }

        // Photo logic: optional
        $nama_gambar = '';
        if (!empty($_FILES['foto']['name'])) {
            $upload = secure_upload_image('foto', '../assets/img/user/');
            if ($upload['status']) {
                $nama_gambar = $upload['filename'];
            } else {
                $msg = mysqli_real_escape_string($con, $upload['msg']);
                echo "<script>alert('$msg'); window.location.replace('?page=siswa&act=add');</script>";
                exit();
            }
        } else {
            // Assign gender-based default avatar
            $nama_gambar = (strtoupper($jk) == 'P') ? '17603.png' : '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg';
        }

        $save = mysqli_query($con, "INSERT INTO tb_siswa (nis, nama_siswa, tempat_lahir, tgl_lahir, jk, alamat, password, foto, status, th_angkatan, id_mkelas) VALUES ('$nis', '$nama', '$tempat', '$tgl', '$jk', '$alamat', '$pass', '$nama_gambar', '1', '$th_masuk', '$kelas')");
        if ($save) {
            echo "
            <script type='text/javascript'>
            setTimeout(function () { 

            swal('$nama', 'Berhasil disimpan', {
            icon : 'success',
            buttons: {        			
            confirm: {
            className : 'btn btn-success'
            }
            },
            });    
            }, 10);  
            window.setTimeout(function(){ 
            window.location.replace('?page=siswa');
            }, 3000);   
            </script>";
        } else {
            echo "<script>alert('Gagal menyimpan data siswa: " . mysqli_error($con) . "'); window.location.replace('?page=siswa&act=add');</script>";
        }

  }elseif (isset($_POST['editSiswa'])) {

  		$gambar = @$_FILES['foto']['name'];
  		$id_siswa = mysqli_real_escape_string($con, $_POST['id']);
  		$nama = mysqli_real_escape_string($con, $_POST['nama']);

        if (!validate_name($nama, true)) {
            echo "<script>alert('Format nama siswa tidak valid atau terlalu panjang (maksimal 60 karakter, tanpa angka/simbol)!'); window.history.back();</script>";
            exit();
        }
  		$tempat = mysqli_real_escape_string($con, $_POST['tempat']);
  		$tgl = mysqli_real_escape_string($con, $_POST['tgl']);
  		$jk = mysqli_real_escape_string($con, $_POST['jk']);
  		$alamat = mysqli_real_escape_string($con, $_POST['alamat']);
  		$kelas = mysqli_real_escape_string($con, $_POST['kelas']);
  		$th_masuk = mysqli_real_escape_string($con, $_POST['th_masuk']);

  		if (!empty($_FILES['foto']['name'])) {
  			$upload = secure_upload_image('foto', '../assets/img/user/');
  			if ($upload['status']) {
  				$gambar = $upload['filename'];
  				// Delete old photo if it is not default
  				$q_old = mysqli_query($con, "SELECT foto FROM tb_siswa WHERE id_siswa='$id_siswa'");
  				if ($q_old && $data_old = mysqli_fetch_array($q_old)) {
  					if ($data_old['foto'] != '17603.png' && $data_old['foto'] != '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg' && !empty($data_old['foto'])) {
  						@unlink("../assets/img/user/" . $data_old['foto']);
  					}
  				}
  				mysqli_query($con,"UPDATE tb_siswa SET foto='$gambar' WHERE id_siswa='$id_siswa' ");
  			} else {
  				$msg = mysqli_real_escape_string($con, $upload['msg']);
  				echo "<script>alert('$msg'); window.location.replace('?page=siswa&act=edit&id=$id_siswa');</script>";
  				exit();
  			}
  		}

  		$editSiswa= mysqli_query($con,"UPDATE tb_siswa SET nama_siswa='$nama',tempat_lahir='$tempat',tgl_lahir='$tgl',jk='$jk',alamat='$alamat',id_mkelas='$kelas',th_angkatan='$th_masuk' WHERE id_siswa='$id_siswa' ");
  		if ($editSiswa) {
  				$nama_escaped = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
  				echo "
  				<script type='text/javascript'>
  				setTimeout(function () { 

  				swal('$nama_escaped', 'Berhasil di Ubah', {
  				icon : 'success',
  				buttons: {        			
  				confirm: {
  				className : 'btn btn-success'
  				}
  				},
  				});    
  				},10);  
  				window.setTimeout(function(){ 
  				window.location.replace('?page=siswa');
  				} ,3000);   
  				</script>";
  		}
  }
 ?>