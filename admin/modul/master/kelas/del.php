<?php 
$id_mkelas = intval($_GET['id']);
$del = mysqli_query($con,"DELETE FROM tb_mkelas WHERE id_mkelas=$id_mkelas");
if ($del) {
		echo " <script>
		alert('Data telah dihapus !');
		window.location='?page=master&act=kelas';
		</script>";	
}

 ?>