<?php 
if (isset($_GET['id']) && isset($_GET['persetujuan'])) {
	if ($_GET['persetujuan']=='Yes') {
		$setuju = mysqli_query($con,"UPDATE tb_izin SET ket_izin='1',persetujuan='Disetujui' WHERE id_izin='$_GET[id]' ");
		if ($setuju) {
			echo "
			<script type='text/javascript'>
			setTimeout(function () { 
				swal('OK', 'Permintaan telah di konfirmasi', {
					icon : 'success',
					buttons: { confirm: { className : 'btn btn-success' } }
				});    
			},10);  
			window.setTimeout(function(){ 
				window.location.replace('?page=absen&pelajaran=$_GET[pelajaran]');
			} ,3000);   
			</script>";
		}
	} else {
		$tdaksetuju = mysqli_query($con,"UPDATE tb_izin SET ket_izin='1', persetujuan='Ditolak' WHERE id_izin='$_GET[id]' ");
		if ($tdaksetuju) {
			echo "
			<script type='text/javascript'>
			setTimeout(function () { 
				swal('OK', 'Permintaan Ditolak', {
					icon : 'success',
					buttons: { confirm: { className : 'btn btn-success' } }
				});    
			},10);  
			window.setTimeout(function(){ 
				window.location.replace('?page=absen&pelajaran=$_GET[pelajaran]');
			} ,3000);   
			</script>";
		}
	}
} else {
	// List all pending permits for classes taught by this teacher
	$id_guru = $data['id_guru'];
	$pending_list = mysqli_query($con, "
		SELECT tb_izin.*, tb_siswa.nama_siswa, tb_siswa.nis, tb_siswa.foto, tb_mkelas.nama_kelas, tb_siswa.id_mkelas
		FROM tb_izin
		INNER JOIN tb_siswa ON tb_izin.id_siswa = tb_siswa.id_siswa
		INNER JOIN tb_mkelas ON tb_siswa.id_mkelas = tb_mkelas.id_mkelas
		WHERE tb_siswa.id_mkelas IN (
			SELECT DISTINCT id_mkelas FROM tb_mengajar WHERE id_guru = '$id_guru'
		) AND tb_izin.ket_izin = '0' AND tb_izin.persetujuan = '0'
		ORDER BY tb_izin.tgl_izin DESC
	");
	?>
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title font-weight-bold" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-bell text-danger mr-2"></i>Daftar Permohonan Izin Menunggu Konfirmasi</h4>
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
					<a href="#">Proses Izin Siswa</a>
				</li>
			</ul>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
					<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between p-3" style="border-radius: 16px 16px 0 0;">
						<h4 class="card-title text-white mb-0 font-weight-bold"><i class="fas fa-file-alt mr-2"></i>Surat Izin / Sakit</h4>
						<span class="badge badge-warning text-dark font-weight-bold px-3 py-2" style="border-radius: 30px;">
							<?= mysqli_num_rows($pending_list); ?> Permohonan
						</span>
					</div>
					<div class="card-body p-4">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr class="bg-light">
										<th style="width: 50px;">No</th>
										<th style="width: 80px;">Foto</th>
										<th>Siswa</th>
										<th>Tanggal Izin</th>
										<th>Kategori</th>
										<th>Alasan</th>
										<th class="text-center" style="width: 150px;">Aksi</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									if (mysqli_num_rows($pending_list) == 0) {
										echo '<tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-check-circle text-success fa-3x mb-3 d-block"></i>Tidak ada permohonan izin yang menunggu konfirmasi saat ini.</td></tr>';
									} else {
										$no = 1;
										while ($r = mysqli_fetch_array($pending_list)) {
											// Get matching id_mengajar for this teacher in this class to construct the URL
											$mengajar_check = mysqli_query($con, "SELECT id_mengajar FROM tb_mengajar WHERE id_guru='$id_guru' AND id_mkelas='$r[id_mkelas]' LIMIT 1");
											$m_data = mysqli_fetch_array($mengajar_check);
											$id_mengajar = isset($m_data['id_mengajar']) ? $m_data['id_mengajar'] : '';
											
											$tipe_badge = ($r['kat'] == 'I') ? 'badge-info' : 'badge-warning';
											$tipe_text = ($r['kat'] == 'I') ? 'Izin' : 'Sakit';
											?>
											<tr>
												<td><?= $no++; ?></td>
												<td>
													<div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 1px solid #ddd; background: #eee;">
														<img src="../assets/img/user/<?= $r['foto']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
													</div>
												</td>
												<td>
													<strong class="text-dark d-block"><?= htmlspecialchars((string)$r['nama_siswa']); ?></strong>
													<small class="text-muted">NISN: <?= htmlspecialchars((string)$r['nis']); ?> &bull; Kelas: <?= htmlspecialchars((string)$r['nama_kelas']); ?></small>
												</td>
												<td class="font-weight-bold"><?= tglIndonesia($r['tgl_izin']); ?></td>
												<td>
													<span class="badge <?= $tipe_badge; ?> font-weight-bold"><?= $tipe_text; ?></span>
												</td>
												<td><span class="text-muted"><?= htmlspecialchars((string)$r['alasan_izin']); ?></span></td>
												<td class="text-center">
													<a href="?page=absen&act=surat_view&izin=<?= $r['id_izin']; ?>&pelajaran=<?= $id_mengajar; ?>" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 20px;">
														<i class="fa fa-search mr-1"></i> Proses / Detail
													</a>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="text-center mt-4">
							<a href="index.php" class="btn btn-light border btn-round font-weight-bold px-4">
								<i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>
