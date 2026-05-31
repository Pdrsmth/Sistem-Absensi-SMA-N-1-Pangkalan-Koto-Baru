<?php 
$id_walikelas = intval($_GET['id']);
$del = mysqli_query($con,"DELETE FROM tb_walikelas WHERE id_walikelas=$id_walikelas");
if ($del) {
		echo " <script>
		alert('Data telah dihapus !');
		window.location='?page=walas';
		</script>";	
}

 ?>