<?php
$baseUrl = base_url();
$dataSuratKeluar = $data_surat_keluar ?? [];

$session = session();
$level = $session->get('level');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-red">
            <div class="panel-heading">
                <?= 'Daftar ' . esc($judul) ?>&nbsp;&nbsp;
                <?php if ($level == 1): ?>
                <button class="btn btn-warning" data-toggle="modal" data-target="#tambah_surat_keluar">
                    <i class="fa fa-envelope"></i> Tambah <?= esc($judul) ?>
                </button>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>ORMAWA</th>
                        <th>Judul Laporan Kegiatan</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <?php if ($level == 1): ?><th>Action</th><?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($dataSuratKeluar as $suratKeluar): ?>
                        <tr>
                            <td class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratKeluar->nama_ormawa) ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratKeluar->judul_j) ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratKeluar->tgl_kirim) ?></td>
                            <!-- <td class="text-center" style="vertical-align: middle;"><?= esc($suratKeluar->status_laporan) ?></td> -->
                              <td class="text-center" style="vertical-align: middle;">
                                <?php
                                    $status = strtolower($suratKeluar->status_laporan ?? '');

                                    switch ($status) {
                                        case 'diterima':
                                            $class = 'label label-success';
                                            break;
                                        case 'diproses':
                                            $class = 'label label-primary';
                                            break;
                                        case 'direvisi':
                                            $class = 'label label-warning';
                                            break;
                                        case 'ditolak':
                                            $class = 'label label-danger';
                                            break;
                                        default:
                                            $class = 'label label-default';
                                    }
                                ?>
                                <span class="<?= $class ?>" style="border-radius: 12px; padding: 5px 10px;">
                                    <?= esc(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratKeluar->catatan) ?></td>
                            <?php if ($level == 1): ?>
                            <td class="text-center" style="vertical-align: middle;">
                                <!-- <a href="<?php //base_url('uploads/' . $suratKeluar->file_surat) ?>" class="btn btn-info btn-sm">Lihat</a> -->
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#ubah_surat_keluar" onclick="ubah_surat(<?= $suratKeluar->id_surat ?>)">Ubah</button>
                                <!-- <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ubah_file_surat_keluar" onclick="ubah_surat(<?php //$suratKeluar->id_surat ?>)">Ubah Surat</button> -->
                                <a href="<?= base_url('home/hapus_surat_keluar/' . $suratKeluar->id_surat) ?>" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambah_surat_keluar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?= base_url('home/tambah_surat_keluar') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center" id="myModalLabel">Tambah <?= esc($judul) ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Laporan Kegiatan</label>
                        <input class="form-control" type="text" name="judul_j" required>
                    </div>
                    <div class="form-group">
                        <label>Nama ORMAWA</label>
                            <select class="form-control" name="ormawa_id" required>
                                <option value="">-- Pilih ORMAWA --</option>
                                <?php foreach ($ormawa as $o): ?>
                                    <option value="<?= $o['id'] ?>">
                                        <?= $o['nama_ormawa'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kirim</label>
                        <input class="form-control" type="date" name="tgl_kirim" required>
                    </div>
                    <div class="form-group">
                        <label>Status Laporan</label>
                        <select class="form-control" name="status_laporan" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="diajukan">Diajukan</option>
                            <option value="diproses">Diproses</option>
                            <option value="direvisi">Revisi</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" rows="1" name="catatan" required></textarea>
                    </div>
                    <!-- <div class="form-group">
                        <label>File Surat</label>
                        <input class="form-control" type="file" accept="application/pdf" name="file_surat" required>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Tutup</button>
                    <input type="submit" value="Tambah <?= esc($judul) ?>" name="submit" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ubah_surat_keluar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?= base_url('home/ubah_surat_keluar') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center" id="myModalLabel">Ubah <?= esc($judul) ?></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ubah_id_surat" id="ubah_id_surat">
                    <div class="form-group">
                        <label>Judul Laporan Kegiatan</label>
                        <input class="form-control" type="text" name="ubah_judul_j" id="ubah_judul_j" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kirim</label>
                        <input class="form-control" type="date" name="ubah_tgl_kirim" id="ubah_tgl_kirim" required>
                    </div>
                    <div class="form-group">
                        <label>Status Laporan</label>
                        <select class="form-control" name="ubah_status_laporan" id="ubah_status_laporan"required>
                            <option value="">-- Pilih Status --</option>
                            <option value="diajukan">Diajukan</option>
                            <option value="diproses">Diproses</option>
                            <option value="direvisi">Revisi</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                                    
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" rows="1" name="ubah_catatan" id="ubah_catatan"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Tutup</button>
                    <input type="submit" value="Ubah <?= esc($judul) ?>" name="submit" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade" id="ubah_file_surat_keluar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?php// base_url('home/ubah_file_surat_keluar') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center" id="myModalLabel">Ubah File <?php// esc($judul) ?></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ubah_file_surat_id" id="ubah_file_surat_id">
                    <div class="form-group">
                        <label>File Surat</label>
                        <input class="form-control" type="file" accept="application/pdf" name="ubah_file_surat"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Tutup</button>
                    <input type="submit" value="Ubah File <?php// esc($judul) ?>" name="submit" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div> -->

<script>
    function ubah_surat(id_surat) {
        const fields = [
            '#ubah_id_surat',
            '#ubah_judul_j',
            '#ubah_tgl_kirim',
            '#ubah_status_laporan',
            '#ubah_catatan',
        ];

        fields.forEach(function (selector) {
            $(selector).val('');
        });

        $.getJSON('<?= base_url('home/get_surat_keluar_by_id/') ?>' + id_surat, function (data) {
            $('#ubah_id_surat').val(data.id_surat);
            $('#ubah_judul_j').val(data.judul_j);
            $('#ubah_tgl_kirim').val(data.tgl_kirim);
            $('#ubah_status_laporan').val(data.status_laporan);
            $('#ubah_catatan').val(data.catatan);
        });
    }
</script>
