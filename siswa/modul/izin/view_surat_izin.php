<?php 
$izin = mysqli_query($con,"SELECT * FROM tb_izin
	INNER JOIN tb_siswa ON tb_izin.id_siswa=tb_siswa.id_siswa
	WHERE tb_izin.id_izin='$_GET[izin]' AND tb_izin.id_siswa='$id_login'");
$d = mysqli_fetch_array($izin);
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Detail Surat Izin</h4>
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
                <a href="?page=izin">Daftar Pengajuan</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Detail</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8 ml-auto mr-auto">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="card-title text-white">Detail Pengajuan Izin / Sakit</div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td width="30%">Nama Siswa</td>
                            <td><strong><?= $d['nama_siswa']; ?></strong></td>
                        </tr>
                        <tr>
                            <td>NIS</td>
                            <td><?= $d['nis']; ?></td>
                        </tr>
                        <tr>
                            <td>Hari / Tanggal Izin</td>
                            <td><?= namahari($d['tgl_izin']); ?>, <?= tglIndonesia($d['tgl_izin']); ?></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td><?= ($d['kat'] == 'I') ? 'Izin' : 'Sakit'; ?></td>
                        </tr>
                        <tr>
                            <td>Alasan / Isi Surat</td>
                            <td><?= $d['alasan_izin']; ?></td>
                        </tr>
                        <tr>
                            <td>Penanggung Jawab (PJ)</td>
                            <td><?= $d['pj']; ?> (<?= $d['nama_pj']; ?>)</td>
                        </tr>
                        <tr>
                            <td>Kontak PJ</td>
                            <td><?= $d['hp_pj']; ?></td>
                        </tr>
                        <tr>
                            <td>Status Keterangan</td>
                            <td>
                                <?php
                                if ($d['ket_izin'] == '0') {
                                    echo "<span class='badge badge-warning'>Menunggu Konfirmasi</span>";
                                } else {
                                    echo "<span class='badge badge-success'>Dikonfirmasi</span>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Persetujuan</td>
                            <td>
                                <?php
                                if ($d['persetujuan'] == '0') {
                                    echo "<span class='badge badge-secondary'>Proses</span>";
                                } elseif ($d['persetujuan'] == 'Disetujui') {
                                    echo "<span class='badge badge-success'>Disetujui</span>";
                                } else {
                                    echo "<span class='badge badge-danger'>Ditolak</span>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Bukti Surat / Foto</td>
                            <td>
                                <a href="../assets/img/bukti_izin/<?= $d['file_bukti']; ?>" target="_blank">
                                    <img src="../assets/img/bukti_izin/<?= $d['file_bukti']; ?>" class="img-thumbnail" style="max-height: 300px;">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="?page=izin" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
