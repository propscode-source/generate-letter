<?php
$dataDisposisiMasuk = $data_disposisi_masuk ?? [];
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-green">
            <div class="panel-heading">
                <?= 'Daftar ' . esc($judul) ?>
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Unit Pengirim</th>
                        <th>Nama Pengirim</th>
                        <th>Tanggal Disposisi</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 0; ?>
                    <?php foreach ($dataDisposisiMasuk as $disposisiMasuk): ?>
                            <tr>
                                <td class="text-center" style="vertical-align: middle;"><?= ++$no ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiMasuk->nama_jabatan) ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiMasuk->nama_pegawai) ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiMasuk->tgl_disposisi) ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiMasuk->keterangan) ?></td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <a href="<?= base_url('uploads/' . $disposisiMasuk->file_surat) ?>" class="btn btn-sm btn-success" style="width: 100%">Lihat Surat</a><br>
                                    <a href="<?= base_url('home/disposisi_keluar_pegawai/' . $disposisiMasuk->id_surat) ?>" class="btn btn-sm btn-info" style="width: 100%; margin-top: 5%">Tambah Disposisi</a>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
