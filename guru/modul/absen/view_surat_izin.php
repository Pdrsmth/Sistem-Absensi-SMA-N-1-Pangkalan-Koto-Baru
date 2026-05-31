<?php 
$izin = mysqli_query($con,"SELECT * FROM tb_izin
	INNER JOIN tb_siswa ON tb_izin.id_siswa=tb_siswa.id_siswa
	WHERE tb_izin.id_izin='$_GET[izin]'");
$d = mysqli_fetch_array($izin);
?>

<div class="col-md-12 mt-3">
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        <center>
            <div class="avatar">
                <img src="../assets/img/user/<?=$d['foto'];?>" alt="..." class="avatar-img rounded">
            </div>
            <hr>
        </center>
        
        <table cellpadding="3" class="text-warning">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong> <?=$d['nama_siswa'];?> </strong></td>
            </tr>
            <tr>
                <td>Nis</td>
                <td>:</td>
                <td><?=$d['nis'];?></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>:</td>
                <td><?=$d['jk'];?></td>
            </tr>
        </table>
        <hr>

        <table cellpadding="4">
            <tr>
                <td>Hari/Tanggal</td>
                <td>:</td>
                <td><?=namahari($d['tgl_izin']); ?>/<?= tglIndonesia($d['tgl_izin']);?></td>
            </tr>
            <tr>
                <td>Alasan/Isi Surat</td>
                <td>:</td>
                <td><?=$d['alasan_izin'];?></td>
            </tr>
            <tr>
                <td>Penanggung Jawab</td>
                <td>:</td>
                <td><?=$d['pj'];?></td>
            </tr>
            <tr>
                <td>Nama PJ</td>
                <td>:</td>
                <td><?=$d['nama_pj'];?></td>
            </tr>
            <tr>
                <td>Kontak PJ</td>
                <td>:</td>
                <td><?=$d['hp_pj'];?></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>
                    <?php
                    if ($d['ket_izin']=='0') {
                        echo "<span class='badge badge-warning'> Belum disetujui !</span>";
                    } else {
                        echo "<span class='badge badge-success'> Telah disetujui !</span>";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Bukti</td>
                <td colspan="2">
                    <a href="../assets/img/bukti_izin/<?=$d['file_bukti'];?>" target="_blank">
                        <img src="../assets/img/bukti_izin/<?=$d['file_bukti'];?>" class="img-thumbnail" style="max-height: 300px;">
                    </a>
                </td>
            </tr>
        </table>

        <hr>
        <p>
            <a href="?page=absen&act=konfirmasi&id=<?=$d['id_izin'];?>&persetujuan=Yes&pelajaran=<?=$_GET['pelajaran'];?>" class="btn btn-info btn-sm">
                <i class="fa fa-check"></i> Konfirmasi
            </a>

            <a target="_blank" href="https://api.whatsapp.com/send?phone=<?=$d['hp_pj'];?>&text=Permohonan Izin Ananda (<?=$d['nama_siswa']; ?>) <?php
                if ($d['ket_izin']=='0') {
                    echo "Belum disetujui !";
                }else{
                    echo "Telah disetujui !";
                }
                ?> " class="btn btn-success btn-sm">
                <i class="fab fa-whatsapp"></i> Hubungi Pj
            </a>

            <a onclick="return confirm('Yakin akan menolak permohonan ?')" href="?page=absen&act=konfirmasi&id=<?=$d['id_izin'];?>&persetujuan=No&pelajaran=<?=$_GET['pelajaran'];?>" class="btn btn-danger btn-sm">
                <i class="fa fa-times"></i> Tolak
            </a>
            
            <a href="?page=absen&pelajaran=<?=$_GET['pelajaran'];?>" class="btn btn-default btn-sm ml-2">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </p>
    </div>
</div>
