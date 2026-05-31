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
                <div class="col-lg-12">
                  <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Form Entry Siswa</h3>
                    </div>
                    <div class="card-body">
                 <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NIS/NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tahun Masuk</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>  
                    <tbody>
                        <?php 
                        $no=1;
			$siswa = mysqli_query($con,"SELECT * FROM tb_siswa
                --  INNER JOIN tb_mkelas ON tb_siswa.id_mkelas
                 ORDER BY id_siswa ASC
                ");
                //while ($row = $siswa->fetch_assoc()) {
                  //print_r($row);
              //}
                        foreach ($siswa as $g) {?>
                        <tr>
                            <td><?=$no++;?>.</td>                          
                            <td><?=$g['nis'];?></td>
                            <td><?=$g['nama_siswa'];?></td>
                            <?php
                              $nama_kelas = "-";
                              $kelas = mysqli_query($con, "SELECT * FROM tb_mkelas WHERE id_mkelas = $g[id_mkelas]");
                              while ($row = $kelas->fetch_assoc()) {
                                $nama_kelas = $row['nama_kelas'];
                            }
                            ?>
                            <td><?=$nama_kelas;?></td>
                            <td><?=$g['th_angkatan'];?></td>
                            <td><?php if ($g['status']==1) {
                                echo "<span class='badge badge-success'>Aktif</span>";
                                
                            }else {
                                echo "<span class='badge badge-danger'>Off</span>";
                            } ?></td>
                            <td><img src="../assets/img/user/<?=$g['foto'] ?>" width="45" height="45"></td>
                              <td>
                              <a class="btn btn-danger btn-sm" onclick="return confirm('Yakin Hapus Data ??')" href="?page=siswa&act=del&id=<?=$g['id_siswa'] ?>"><i class="fas fa-trash"></i></a>
                            <a class="btn btn-info btn-sm" href="?page=siswa&act=edit&id=<?=$g['id_siswa'] ?>"><i class="far fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>                        
                </table>
</div>
</div>
</div>
</div>
</div>
