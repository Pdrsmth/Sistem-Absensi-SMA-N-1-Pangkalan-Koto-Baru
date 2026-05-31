	<?php 

$edit_q = mysqli_query($con,"SELECT * FROM tb_guru WHERE id_guru='$_GET[id]' ");
$d = mysqli_fetch_array($edit_q);
?>
<div class="page-inner">
          <div class="page-header">
            <h4 class="page-title">Guru</h4>
            <ul class="breadcrumbs">
              <li class="nav-home">
                <a href="#">
                  <i class="flaticon-home"></i>
                </a>
              </li>
              <li class="separator">
                <i class="flaticon-right-arrow"></i>
              </li>
              <li class="nav-item">
                <a href="#">Data Guru</a>
              </li>
              <li class="separator">
                <i class="flaticon-right-arrow"></i>
              </li>
              <li class="nav-item">
                <a href="#">Edit Guru</a>
              </li>
            </ul>
          </div>
          <div class="row">
                <div class="col-lg-8">
                  <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Form Edit Guru</h3>
                    </div>
                    <div class="card-body">
						<form action="?page=guru&act=proses" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label>NIP/NUPTK</label>
								<input type="hidden" name="id" value="<?=$d['id_guru'] ?>">
								<input name="nip" type="text" class="form-control" value="<?=$d['nip'] ?>" readonly>								
							</div>

							<div class="form-group">
								<label>Nama Guru</label>
								<input name="nama" type="text" class="form-control" value="<?=$d['nama_guru'] ?>" maxlength="60" pattern="^[a-zA-Z\s.,'`\(\)-]+$" title="Nama hanya boleh berisi huruf, spasi, titik, koma, tanda petik, tanda kurung, dan tanda hubung" required>								
							</div>

							<div class="form-group">
								<label>Email</label>
								<!-- <span class="text-danger"><em>Email digunakan unruk Password default</em></span> -->
								<input name="email" type="email" class="form-control" value="<?=$d['email'] ?>" required>
							</div>
<!-- 
							<div class="form-group">
								<label>Pangkat</label>
								<input name="pangkat" type="text" class="form-control" value="<?=$d['pangkat'] ?>">
							</div>

							<div class="form-group">
								<label>Golongan</label>
								<input name="golongan" type="text" class="form-control" value="<?=$d['golongan'] ?>">
							</div> -->
					


							<div class="form-group">
								<label>Mata Pelajaran yang Diampu</label>
								<select name="id_mapel" class="form-control" required>
									<option value="">- Pilih Mata Pelajaran -</option>
									<?php 
									$mapel = mysqli_query($con,"SELECT * FROM tb_master_mapel ORDER BY mapel ASC");
									foreach ($mapel as $m) {
										$selected = ($m['id_mapel'] == $d['id_mapel']) ? "selected" : "";
										echo "<option value='$m[id_mapel]' $selected>[ $m[kode_mapel] ] $m[mapel]</option>";
									}
									?>
								</select>
							</div>

							<div class="form-group">
								<p>
									<img src="../assets/img/user/<?=$d['foto']; ?>" class="img-fluid rounded-circle kotak" style="height: 65px; width: 65px;">
								</p>
								<label>Foto</label>
								<input type="file" name="foto">
							</div>

							

							<div class="form-group">
								<button name="editGuru" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
								<a href="javascript:history.back()" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Batal</a>
							</div>


						</form>
			
					
</div>
</div>
</div>
</div>
</div>
