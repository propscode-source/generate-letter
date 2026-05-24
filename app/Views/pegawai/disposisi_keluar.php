<?php
$dataDisposisiKeluar = $data_disposisi_keluar ?? [];
$showAddModal = $surat_id !== null;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-red">
            <div class="panel-heading">
                <?= 'Daftar ' . esc($judul) ?>
                <?php if ($showAddModal && $can_add_disposisi): ?>
                    &nbsp;&nbsp;
                    <button class="btn btn-success" data-toggle="modal" data-target="#tambah_surat_masuk">
                        <i class="fa fa-envelope"></i> Tambah <?= esc($judul) ?>
                    </button>&nbsp;&nbsp;
                <?php elseif ($showAddModal): ?>
                    <b style="color: white">(DISPOSISI SELESAI)</b>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tujuan Unit</th>
                        <th>Tujuan Pegawai</th>
                        <th>Tanggal Disposisi</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 0; ?>
                    <?php foreach ($dataDisposisiKeluar as $disposisiKeluar): ?>
                            <tr>
                                <td class="text-center" style="vertical-align: middle;"><?= ++$no ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiKeluar->nama_jabatan) ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiKeluar->nama_pegawai) ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiKeluar->tgl_disposisi) ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= esc($disposisiKeluar->keterangan) ?></td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <a href="<?= base_url('uploads/' . $disposisiKeluar->file_surat) ?>" class="btn btn-sm btn-success" style="width: 100%">Lihat Surat</a><br>
                                    <a href="<?= base_url('home/hapus_disposisi_pegawai/' . $disposisiKeluar->id_disposisi . '/' . $disposisiKeluar->id_surat) ?>" class="btn btn-sm btn-info" style="width: 100%; margin-top: 5%">Hapus</a>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($showAddModal): ?>
    <div class="modal fade" id="tambah_surat_masuk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" action="<?= base_url('home/tambah_disposisi_pegawai/' . $surat_id) ?>" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title text-center" id="myModalLabel">Tambah <?= esc($judul) ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tujuan Unit</label>
                            <select class="form-control" name="tujuan_unit" onchange="get_pegawai_id_by_jabatan(this.value)">
                                <option value="">-- Pilih Tujuan Unit --</option>
                                <?php if (isset($drop_down_jabatan)): ?>
                                    <?php foreach ($drop_down_jabatan as $jabatan): ?>
                                        <?php if ((int) $jabatan->id_jabatan !== $current_user_id_jabatan && (int) $jabatan->level > $current_user_level): ?>
                                            <option value="<?= $jabatan->id_jabatan ?>"><?= esc($jabatan->nama_jabatan) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tujuan Pegawai</label>
                            <select class="form-control" name="tujuan_pegawai" id="tujuan_pegawai">
                                <option value="">-- Pilih Nama Pegawai --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="5"></textarea>
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
<?php endif; ?>

<script>
    function get_pegawai_id_by_jabatan(id_jabatan) {
        $('#tujuan_pegawai').empty();
        $.getJSON('<?= base_url('home/get_pegawai_by_jabatan/') ?>' + id_jabatan, function (data) {
            $('#tujuan_pegawai').append('<option value="">-- Pilih Nama Pegawai --</option>');
            $.each(data, function (index, value) {
                $('#tujuan_pegawai').append('<option value="' + value.id_pegawai + '">' + value.nama_pegawai + '</option>');
            });
        });
    }
</script>
