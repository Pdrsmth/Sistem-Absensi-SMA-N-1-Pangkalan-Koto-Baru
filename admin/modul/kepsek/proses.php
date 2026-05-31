<?php 

if (isset($_POST['saveKepsek'])) {
	$nip = mysqli_real_escape_string($con, $_POST['nip']);
	$nama = mysqli_real_escape_string($con, $_POST['nama']);
	$email = mysqli_real_escape_string($con, $_POST['email']);

	if (!validate_name($nama, true)) {
		echo "<script>alert('Format nama kepala sekolah tidak valid atau terlalu panjang (maksimal 60 karakter, tanpa angka/simbol)!'); window.history.back();</script>";
		exit();
	}
	if (!validate_email($email, true)) {
		echo "<script>alert('Format email tidak valid!'); window.history.back();</script>";
		exit();
	}

	$pass = password_hash($nip, PASSWORD_DEFAULT);

	$nama_gambar = '';
	if (!empty($_FILES['foto']['name'])) {
		$upload = secure_upload_image('foto', '../assets/img/user/');
		if ($upload['status']) {
			$nama_gambar = $upload['filename'];
		} else {
			$msg = mysqli_real_escape_string($con, $upload['msg']);
			echo "<script>alert('$msg'); window.location.replace('?page=kepsek');</script>";
			exit();
		}
	} else {
		$nama_gambar = '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg';
	}

	$save= mysqli_query($con,"INSERT INTO tb_kepsek (nip, nama_kepsek, email, password, foto, status) VALUES ('$nip','$nama','$email','$pass','$nama_gambar','Y') ");
	if ($save) {
		$nama_escaped = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
		echo "
		<script type='text/javascript'>
		setTimeout(function () { 

		swal('$nama_escaped', 'Berhasil disimpan', {
		icon : 'success',
		buttons: {        			
		confirm: {
		className : 'btn btn-success'
		}
		},
		});    
		},10);  
		window.setTimeout(function(){ 
		window.location.replace('?page=kepsek');
		} ,3000);   
		</script>";
	}

} elseif (isset($_POST['editKepsek'])) {
	$id_kepsek = mysqli_real_escape_string($con, $_POST['id']);
	$nama = mysqli_real_escape_string($con, $_POST['nama']);
	$email = mysqli_real_escape_string($con, $_POST['email']);

	if (!validate_name($nama, true)) {
		echo "<script>alert('Format nama kepala sekolah tidak valid atau terlalu panjang (maksimal 60 karakter, tanpa angka/simbol)!'); window.history.back();</script>";
		exit();
	}
	if (!validate_email($email, true)) {
		echo "<script>alert('Format email tidak valid!'); window.history.back();</script>";
		exit();
	}

	if (!empty($_FILES['foto']['name'])) {
		$upload = secure_upload_image('foto', '../assets/img/user/');
		if ($upload['status']) {
			$gambar = $upload['filename'];
			// Delete old photo if it is not default
			$q_old = mysqli_query($con, "SELECT foto FROM tb_kepsek WHERE id_kepsek='$id_kepsek'");
			if ($q_old && $data_old = mysqli_fetch_array($q_old)) {
				if ($data_old['foto'] != '355-3553881_stockvader-predicted-adig-user-profile-icon-png-clipart.jpg' && !empty($data_old['foto'])) {
					@unlink("../assets/img/user/" . $data_old['foto']);
				}
			}
			mysqli_query($con,"UPDATE tb_kepsek SET foto='$gambar' WHERE id_kepsek='$id_kepsek' ");
		} else {
			$msg = mysqli_real_escape_string($con, $upload['msg']);
			echo "<script>alert('$msg'); window.location.replace('?page=kepsek');</script>";
			exit();
		}
	}
	
	$editKepsek= mysqli_query($con,"UPDATE tb_kepsek SET nama_kepsek='$nama',email='$email' WHERE id_kepsek='$id_kepsek' ");
	if ($editKepsek) {
		$nama_escaped = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
		echo "
			<script type='text/javascript'>
			setTimeout(function () { 

			swal('$nama_escaped', 'Berhasil diubah', {
			icon : 'success',
			buttons: {        			
			confirm: {
			className : 'btn btn-success'
			}
			},
			});    
			},10);  
			window.setTimeout(function(){ 
			window.location.replace('?page=kepsek');
			} ,3000);   
			</script>";
	}
}
?>