<?php
// id_login is defined in siswa/index.php
$id_login = $_SESSION['siswa'];
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Izin / Sakit</h4>
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
                <a href="#">Ajukan Permohonan</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="card-title text-white">Form Pengajuan Izin / Sakit</div>
                </div>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Tanggal Izin</label>
                            <input type="date" name="tgl_izin" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kat" class="form-control" required>
                                <option value="I">Izin</option>
                                <option value="S">Sakit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alasan Izin</label>
                            <textarea name="alasan_izin" class="form-control" rows="3" placeholder="Tuliskan alasan detail..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Hubungan Penanggung Jawab</label>
                            <select name="pj" class="form-control" required>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Wali">Wali</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Penanggung Jawab</label>
                            <input type="text" name="nama_pj" class="form-control" placeholder="Nama Orang Tua/Wali..." maxlength="60" pattern="^[a-zA-Z\s.,'`\(\)-]+$" title="Nama hanya boleh berisi huruf, spasi, titik, koma, tanda petik, tanda kurung, dan tanda hubung" required>
                        </div>
                        <div class="form-group">
                            <label>No. HP Penanggung Jawab</label>
                            <input type="text" name="hp_pj" class="form-control" placeholder="Contoh: 08XXXXXXXXXX..." maxlength="13" pattern="[0-9]+" title="Nomor HP hanya boleh berisi angka" required>
                        </div>
                        <div class="form-group">
                            <label>Foto Bukti / Surat Dokter (JPG/PNG)</label>
                            <input type="file" name="file_bukti" class="form-control-file" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="kirim" class="btn btn-primary btn-block">
                                <i class="fa fa-paper-plane"></i> Kirim Permohonan
                            </button>
                        </div>
                    </form>

                    <?php
                    if (isset($_POST['kirim'])) {
                        $tgl_izin = $_POST['tgl_izin'];
                        $kat = $_POST['kat'];
                        $alasan_izin = $_POST['alasan_izin'];
                        $pj = $_POST['pj'];
                        $nama_pj = $_POST['nama_pj'];
                        $hp_pj = $_POST['hp_pj'];

                        if (!validate_name($nama_pj, true)) {
                            echo "<script>alert('Format nama penanggung jawab tidak valid atau terlalu panjang (maksimal 60 karakter)!'); window.history.back();</script>";
                            exit();
                        }
                        if (!validate_phone($hp_pj, true)) {
                            echo "<script>alert('Nomor HP penanggung jawab hanya boleh berisi angka dan maksimal 13 digit!'); window.history.back();</script>";
                            exit();
                        }

                        // Upload file
                        $file_name = $_FILES['file_bukti']['name'];
                        $file_tmp = $_FILES['file_bukti']['tmp_name'];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $allowed = array('jpg', 'jpeg', 'png');

                        if (in_array($file_ext, $allowed)) {
                            $new_file_name = time() . '_' . $id_login . '.' . $file_ext;
                            $target_dir = "../assets/img/bukti_izin/";
                            
                            if (move_uploaded_file($file_tmp, $target_dir . $new_file_name)) {
                                $insert = mysqli_query($con, "INSERT INTO tb_izin (id_siswa, tgl_izin, alasan_izin, kat, pj, nama_pj, hp_pj, file_bukti, ket_izin, persetujuan) VALUES ('$id_login', '$tgl_izin', '$alasan_izin', '$kat', '$pj', '$nama_pj', '$hp_pj', '$new_file_name', '0', '0')");

                                if ($insert) {
                                    echo "
                                    <script type='text/javascript'>
                                    setTimeout(function () { 
                                        swal('Berhasil', 'Permohonan izin telah terkirim!', {
                                            icon : 'success',
                                            buttons: { confirm: { className : 'btn btn-success' } }
                                        });    
                                    }, 10);  
                                    window.setTimeout(function(){ 
                                        window.location.replace('?page=izin');
                                    }, 2000);   
                                    </script>";
                                } else {
                                    echo "<script>alert('Gagal menyimpan data ke database');</script>";
                                }
                            } else {
                                echo "<script>alert('Gagal mengupload file ke direktori');</script>";
                            }
                        } else {
                            echo "<script>alert('Format file tidak didukung! Hanya diperbolehkan JPG, JPEG, atau PNG.');</script>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-info">
                    <div class="card-title text-white">Riwayat Permohonan Izin / Sakit</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-head-bg-info">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                    <th>Persetujuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $riwayat = mysqli_query($con, "SELECT * FROM tb_izin WHERE id_siswa='$id_login' ORDER BY tgl_izin DESC");
                                if (mysqli_num_rows($riwayat) == 0) {
                                    echo "<tr><td colspan='6' class='text-center'>Belum ada pengajuan.</td></tr>";
                                } else {
                                    while ($r = mysqli_fetch_array($riwayat)) {
                                        $tipe = ($r['kat'] == 'I') ? 'Izin' : 'Sakit';
                                        
                                        // Status Keterangan
                                        if ($r['ket_izin'] == '0') {
                                            $ket = "<span class='badge badge-warning'>Menunggu Konfirmasi</span>";
                                        } else {
                                            $ket = "<span class='badge badge-success'>Dikonfirmasi</span>";
                                        }

                                        // Status Persetujuan
                                        if ($r['persetujuan'] == '0') {
                                            $persetujuan = "<span class='badge badge-secondary'>Proses</span>";
                                        } elseif ($r['persetujuan'] == 'Disetujui') {
                                            $persetujuan = "<span class='badge badge-success'>Disetujui</span>";
                                        } else {
                                            $persetujuan = "<span class='badge badge-danger'>Ditolak</span>";
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= tglIndonesia($r['tgl_izin']); ?></td>
                                            <td><?= $tipe; ?></td>
                                            <td><?= $ket; ?></td>
                                            <td><?= $persetujuan; ?></td>
                                            <td>
                                                <a href="?page=izin&act=surat_view&izin=<?= $r['id_izin']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fa fa-search"></i> Detail
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
                </div>
            </div>
        </div>
    </div>
</div>
