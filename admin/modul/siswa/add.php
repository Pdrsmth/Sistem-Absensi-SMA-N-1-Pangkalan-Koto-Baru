<div class="page-inner">
          <div class="page-header">
            <h4 class="page-title">Siswa</h4>
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
                <a href="#">Data Siswa</a>
              </li>
              <li class="separator">
                <i class="flaticon-right-arrow"></i>
              </li>
              <li class="nav-item">
                <a href="#">Tambah Siswa</a>
              </li>
            </ul>
          </div>
          <div class="row">
                <div class="col-lg-7">
                  <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Form Entry Siswa</h3>
                    </div>
                    <div class="card-body">
                      <!-- Simplified Flow Alert Notice -->
                      <div class="alert alert-info" role="alert" style="border-radius: 8px;">
                        <i class="fas fa-info-circle mr-2"></i> <b>Info Pendaftaran Mudah:</b><br>
                        Anda hanya wajib menginputkan <b>Nama Lengkap</b>, <b>NIS/NISN</b>, dan <b>Kelas Siswa</b>. Kolom lainnya bersifat <b>opsional</b> dan dapat dilengkapi secara mandiri oleh siswa melalui menu profil mereka sendiri. Password default akun siswa adalah <b>NIS/NISN</b> mereka.
                      </div>
                      
<form action="?page=siswa&act=proses" method="post" enctype="multipart/form-data">

<table cellpadding="3" style="font-weight: bold; width: 100%;">
  <tr>
    <td width="30%">Nama Lengkap <span class="text-danger">*</span></td>
    <td width="3%">:</td>
    <td><input type="text" class="form-control" name="nama" placeholder="Nama lengkap siswa" maxlength="60" pattern="^[a-zA-Z\s.,'`\(\)-]+$" title="Nama hanya boleh berisi huruf, spasi, titik, koma, tanda petik, tanda kurung, dan tanda hubung" required></td>
  </tr>
  <tr>
    <td>NIS/NISN <span class="text-danger">*</span></td>
    <td>:</td>
    <td><input name="nis" type="text" class="form-control" placeholder="Nomor NIS/NISN siswa" required>	</td>
  </tr>
  <tr>
    <td>Tempat Lahir <span class="text-muted">(Opsional)</span></td>
    <td>:</td>
    <td><input type="text" class="form-control" name="tempat" placeholder="Tempat Lahir"></td>
  </tr>
  <tr>
    <td>Tanggal Lahir <span class="text-muted">(Opsional)</span></td>
    <td>:</td>
    <td><input type="date" class="form-control" name="tgl"></td>
  </tr>
  <tr>
    <td>Jenis Kelamin <span class="text-muted">(Opsional)</span></td>
    <td>:</td>
    <td>
    	<select name="jk" class="form-control">
    		<option value="L">Laki-laki</option>
    		<option value="P">Perempuan</option>
    	</select>
    </td>
  </tr>

  <tr>
    <td>Alamat Rumah <span class="text-muted">(Opsional)</span></td>
    <td>:</td>
    <td><textarea name="alamat" class="form-control" placeholder="Alamat lengkap..."></textarea></td>
  </tr>
  <tr>
    <td colspan="3"><hr><b>Akademik & Akun</b></td>
  </tr>
  <tr>
    <td>Kelas Siswa <span class="text-danger">*</span></td>
    <td>:</td>
	<td>
    <select class="form-control" name="kelas" required>
    <option value="">Pilih Kelas</option>
    <?php
    $sqlKelas=mysqli_query($con, "SELECT * FROM tb_mkelas 
    ORDER BY id_mkelas ASC");
    while($kelas=mysqli_fetch_array($sqlKelas)){
    echo "<option value='$kelas[id_mkelas]'>$kelas[nama_kelas]</option>";
    }
    ?>
    </select>
	</td>
  </tr>
   <tr>
    <td>Tahun Masuk <span class="text-muted">(Opsional)</span></td>
    <td>:</td>
    <td><input name="th_masuk" type="number" class="form-control" placeholder="<?= date('Y'); ?>" value="<?= date('Y'); ?>"></td>
  </tr>
  <tr>
    <td>Pas Foto Formal <span class="text-muted">(Opsional)</span></td>
    <td>:</td>
    <td><input type="file" class="form-control" name="foto"></td>
  </tr>
  <tr>
    <td colspan="3">
		<button name="saveSiswa" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Siswa</button>
		<a href="javascript:history.back()" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Batal</a>
    </td>
  </tr>
</table>
</form>
</div>
</div>
</div>

<div class="col-lg-5">
  <div class="card">
    <div class="card-header bg-primary text-white d-flex align-items-center">
      <h3 class="h4 text-white"><i class="fas fa-file-excel"></i> Import Siswa (CSV/Excel)</h3>
    </div>
    <div class="card-body">
      <form action="?page=siswa&act=proses" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label>Pilih Kelas untuk Impor</label>
          <select class="form-control" name="kelas" required>
            <option value="">Pilih Kelas</option>
            <?php
            $sqlKelas=mysqli_query($con, "SELECT * FROM tb_mkelas ORDER BY id_mkelas ASC");
            while($kelas=mysqli_fetch_array($sqlKelas)){
              echo "<option value='$kelas[id_mkelas]'>$kelas[nama_kelas]</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Pilih File CSV</label>
          <input type="file" name="file_csv" class="form-control-file" accept=".csv" required>
          <small class="text-muted">Pastikan file bertipe .CSV (Koma Terpisah).</small>
        </div>
        <div class="form-group">
          <a href="modul/siswa/download_template.php" class="btn btn-info btn-xs btn-block">
            <i class="fa fa-download"></i> Unduh Template CSV
          </a>
        </div>
        <hr>
        <div class="form-group">
          <button name="importSiswa" type="submit" class="btn btn-success btn-block">
            <i class="fa fa-upload"></i> Mulai Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>
