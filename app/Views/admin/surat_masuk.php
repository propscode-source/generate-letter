<?php
$baseUrl = base_url();
$dataSuratMasuk = $data_surat_masuk ?? [];

$session = session();
$level = $session->get('level');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-green">
            <div class="panel-heading">
                <?= 'Daftar ' . esc($judul) ?>&nbsp;&nbsp;
                <?php if ($level == 1): ?>
                <button class="btn btn-info" data-toggle="modal" data-target="#tambah_surat_masuk">
                    <i class="fa fa-envelope"></i> Tambah <?= esc($judul) ?>
                </button><?php endif; ?>
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>ORMAWA</th>
                        <th>Judul Kegiatan</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <?php if ($level == 1): ?> <!-- hanya sekretaris -->
                        <th>Action</th><?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($dataSuratMasuk as $suratMasuk): ?>
                        <tr>
                            <td class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratMasuk->nama_ormawa ?? '-') ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratMasuk->judul ?? '-') ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratMasuk->tgl_terima ?? '-') ?></td>
                            <!-- <td class="text-center" style="vertical-align: middle;"><?= esc($suratMasuk->status_proposal ?? '-') ?></td> -->
                             <td class="text-center" style="vertical-align: middle;">
                                <?php
                                    $status = strtolower($suratMasuk->status_proposal ?? '');

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
                            <td class="text-center" style="vertical-align: middle;"><?= esc($suratMasuk->catatan ?? '-') ?></td>
                            <?php if ($level == 1): ?> <!-- hanya sekretaris -->
                            <td class="text-center" style="vertical-align: middle;">
                                <!-- <a href="<?php //base_url('/uploads/' . $suratMasuk->file_surat) ?>" class="btn btn-sm btn-info">Lihat</a> -->
                                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#ubah_surat_masuk" onclick="ubah_surat(<?= $suratMasuk->id_surat ?>)">Ubah</button>
                                <!-- <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#ubah_file_surat_masuk" onclick="ubah_surat(<?= $suratMasuk->id_surat ?>)">Ubah Surat</button> -->
                                <!-- <a href="<?= base_url('home/disposisi/' . $suratMasuk->id_surat) ?>" class="btn btn-sm btn-primary">Disposisi</a> -->
                                <a href="<?= base_url('home/hapus_surat_masuk/' . $suratMasuk->id_surat) ?>" class="btn btn-sm btn-danger">Hapus</a>
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

<div class="modal fade" id="tambah_surat_masuk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?= base_url('home/tambah_surat_masuk') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center" id="myModalLabel">Tambah <?= esc($judul) ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <!-- <label>Nama ORMAWA</label>
                        <input class="form-control" type="text" name="nomor_surat" required> -->
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
                    </div>
                    <div class="form-group">
                        <label>Judul Proposal Kegiatan</label>
                        <input class="form-control" type="text" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input class="form-control" type="date" name="tgl_terima" required>
                    </div>
                    <div class="form-group">
                        <!-- <label>Status Proposal</label> -->
                        <!-- <input class="form-control" type="text" name="pengirim" required> -->
                        <label>Status Proposal</label>
                        <select class="form-control" name="status_proposal" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="diajukan">Diajukan</option>
                            <option value="diproses">Diproses</option>
                            <option value="revisi">Revisi</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <input class="form-control" type="text" name="catatan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Tutup</button>
                    <input type="submit" value="Tambah <?= esc($judul) ?>" name="submit" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ubah_surat_masuk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?= base_url('home/ubah_surat_masuk') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center" id="myModalLabel">Ubah <?= esc($judul) ?></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ubah_id_surat" id="ubah_id_surat">
                    <div class="form-group">
                        <label>Judul Kegiatan</label>
                        <input class="form-control" type="text" name="ubah_judul" id="ubah_judul" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal masuk</label>
                        <input class="form-control" type="date" name="ubah_tgl_terima" id="ubah_tgl_terima" required>
                    </div>
                    <div class="form-group">
                        <label>Status Proposal</label>
                        <select class="form-control" name="ubah_status_proposal" id="ubah_status_proposal" required>
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
                        <input class="form-control" type="text" name="ubah_catatan" id="ubah_catatan" required>
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

<div class="modal fade" id="ubah_file_surat_masuk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?= base_url('home/ubah_file_surat_masuk') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center" id="myModalLabel">Ubah File <?= esc($judul) ?></h4>
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
                    <input type="submit" value="Ubah File <?= esc($judul) ?>" name="submit" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function ubah_surat(id_surat) {
        const fields = [
            '#ubah_id_surat',
            '#ubah_judul',
            '#ubah_tgl_terima',
            '#ubah_status_proposal',
            '#ubah_catatan'
        ];

        fields.forEach(function (selector) {
            $(selector).val('');
        });

        $.getJSON('<?= base_url('home/get_surat_masuk_by_id/') ?>' + id_surat, function (data) {
            $('#ubah_id_surat').val(data.id_surat);
            $('#ubah_judul').val(data.judul);
            $('#ubah_tgl_terima').val(data.tgl_terima);
            $('#ubah_status_proposal').val(data.status_proposal);
            $('#ubah_catatan').val(data.catatan);
        });
    }
</script>
