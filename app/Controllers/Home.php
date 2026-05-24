<?php

namespace App\Controllers;

use App\Models\HomeModel;
use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    private HomeModel $homeModel;

    public function __construct()
    {
        $this->homeModel = new HomeModel();
    }

    public function index()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $judul = 'Selamat Datang, ' . $this->currentUserName() . '!';

        // if ($this->isSekretaris()) {
        //     return $this->render('admin/dashboard', [
        //         'judul'        => $judul,
        //         'jumlah_surat' => $this->homeModel->getJumlahSurat(),
        //         'data_surat'   => null,
        //     ]);
        // }

        if ($this->isSekretaris()) {
            // ADMIN / SEKRETARIS → semua data
            $jumlahSurat = $this->homeModel->getJumlahSurat();
        } elseif ($this->isOrmawa()) {
            // ORMAWA → hanya milik dia
            $ormawaId = (int) $this->session->get('ormawa_id');
            $jumlahSurat = $this->homeModel->getJumlahSuratByOrmawa($ormawaId);
        } else {
            // fallback (kalau ada role lain)
            $jumlahSurat = [
                'proposal' => 0,
                'surat_keluar' => 0
            ];
        }

        // return $this->render('pegawai/dashboard', [
        //     'judul'            => $judul,
        //     'jumlah_disposisi' => $this->homeModel->getJumlahDisposisi($this->currentUserPegawaiId()),
        //     'data_surat'       => null,
        // ]);

        return $this->render('admin/dashboard', [
                'judul'        => $judul,
                'jumlah_surat' => $jumlahSurat,
                'data_surat'   => null,
        ]);
    }

    public function surat_masuk()
    {
        
        // if ($redirect = $this->requireSekretaris()) {
        //     return $redirect;
        // }

        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $ormawaModel = new \App\Models\OrmawaModel();

        $ormawaId = $this->isOrmawa() ? $this->session->get('ormawa_id') : null;

        if ($this->isOrmawa()) {
            $dataSuratMasuk = $this->homeModel->getSuratMasukByOrmawa($ormawaId);
        } else {
            $dataSuratMasuk = $this->homeModel->getSuratMasuk();
        }

        return $this->render('admin/surat_masuk', [
            'judul'            => 'Proposal Kegiatan ORMAWA',
            'data_surat'       => null,
            'data_surat_masuk' => $dataSuratMasuk, // ✅ pakai variabel hasil filter
            'ormawa'           => $ormawaModel->findAll(), // 🔥 TAMBAH
        ]);

        // return $this->render('admin/surat_masuk', [
        //     'judul'            => 'Proposal',
        //     'data_surat'       => null,
        //     'data_surat_masuk' => $this->homeModel->getSuratMasuk(),
        // ]);
    }

    public function surat_keluar()
    {
        // if ($redirect = $this->requireSekretaris()) {
        //     return $redirect;
        // }

        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $ormawaModel = new \App\Models\OrmawaModel();

        $ormawaId = $this->isOrmawa() ? $this->session->get('ormawa_id') : null;

        if ($this->isOrmawa()) {
            $dataSuratKeluar = $this->homeModel->getSuratKeluarByOrmawa($ormawaId);
        } else {
            $dataSuratKeluar = $this->homeModel->getSuratKeluar();
        }

        return $this->render('admin/surat_keluar', [
            'judul'             => 'Laporan Kegiatan ORMAWA',
            'data_surat'        => null,
            'data_surat_keluar' => $dataSuratKeluar,
            'ormawa'            => $ormawaModel->findAll(),
        ]);
    }

    public function disposisi_selesai(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if (! $this->homeModel->cekStatusSuratMasuk($id_surat)) {
            return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Disposisi surat ini telah selesai!');
        }

        if ($this->homeModel->disposisiSelesai($id_surat)) {
            return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Disposisi surat ini telah selesai!');
        }

        return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Gagal update status disposisi!');
    }

    public function disposisi(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        return $this->render('admin/disposisi', [
            'judul'                   => 'Disposisi Surat',
            'data_surat'              => $this->homeModel->getSuratMasukById($id_surat),
            'surat_id'                => $id_surat,
            'can_manage_disposisi'    => $this->homeModel->cekStatusSuratMasuk($id_surat),
            'drop_down_jabatan'       => $this->homeModel->getJabatan(),
            'data_disposisi'          => $this->homeModel->getDisposisi($id_surat),
            'current_user_id_jabatan' => $this->currentUserJabatanId(),
        ]);
    }

    public function disposisi_keluar()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        return $this->render('pegawai/disposisi_keluar', $this->pegawaiDisposisiKeluarPayload(
            dataDisposisiKeluar: $this->homeModel->getAllDisposisiKeluar($this->currentUserPegawaiId())
        ));
    }

    public function disposisi_masuk()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        return $this->render('pegawai/disposisi_masuk', [
            'judul'                => 'Disposisi Masuk',
            'data_surat'           => null,
            'data_disposisi_masuk' => $this->homeModel->getDisposisiMasuk($this->currentUserPegawaiId()),
        ]);
    }

    public function disposisi_keluar_pegawai(int $id_surat)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        return $this->render('pegawai/disposisi_keluar', $this->pegawaiDisposisiKeluarPayload(
            dataSurat: $this->homeModel->getSuratMasukById($id_surat),
            suratId: $id_surat,
            canAddDisposisi: $this->homeModel->cekStatusSuratMasuk($id_surat),
            dropDownJabatan: $this->homeModel->getJabatan(),
            dataDisposisiKeluar: $this->homeModel->getDisposisiKeluar($id_surat, $this->currentUserPegawaiId())
        ));
    }

    public function tambah_disposisi(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if (! $this->validate($this->disposisiRules())) {
            return $this->redirectWithNotif('home/disposisi/' . $id_surat, $this->validationErrorMessage());
        }

        if ($this->homeModel->tambahDisposisi($id_surat, $this->disposisiPayload())) {
            return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Tambah disposisi surat berhasil!');
        }

        return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Tambah disposisi surat gagal!');
    }

    public function tambah_disposisi_pegawai(int $id_surat)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        if (! $this->validate($this->disposisiRules())) {
            return $this->redirectWithNotif('home/disposisi_keluar_pegawai/' . $id_surat, $this->validationErrorMessage());
        }

        if ($this->homeModel->tambahDisposisi($id_surat, $this->disposisiPayload())) {
            return $this->redirectWithNotif('home/disposisi_keluar_pegawai/' . $id_surat, 'Tambah disposisi surat berhasil!');
        }

        return $this->redirectWithNotif('home/disposisi_keluar_pegawai/' . $id_surat, 'Tambah disposisi surat gagal!');
    }

    public function tambah_surat_keluar()
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if (! $this->validate($this->suratKeluarRules())) {
            return $this->redirectWithNotif('home/surat_keluar', $this->validationErrorMessage());
        }

        // $upload = $this->uploadPdf('file_surat');
        // if (isset($upload['error'])) {
        //     return $this->redirectWithNotif('home/surat_keluar', $upload['error']);
        // }

        // $success = $this->homeModel->tambahSuratKeluar($this->suratKeluarPayload($upload['file_name']));

        // if (! $success) {
        //     $this->deleteUploadedFile($upload['file_name']);

        //     return $this->redirectWithNotif('home/surat_keluar', 'Tambah Surat Gagal!');
        // }

        $success = $this->homeModel->tambahSuratKeluar(
        $this->suratKeluarPayload()
        );

        if (! $success) {
            return $this->redirectWithNotif('home/surat_keluar', 'Tambah Surat Keluar Gagal!');
        }

        return $this->redirectWithNotif('home/surat_keluar', 'Tambah Surat Keluar Berhasil!');

        }

    public function tambah_surat_masuk()
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if (! $this->validate($this->suratMasukRules())) {
            return $this->redirectWithNotif('home/surat_masuk', $this->validationErrorMessage());
        }

        // $upload = $this->uploadPdf('file_surat');
        // if (isset($upload['error'])) {
        //     return $this->redirectWithNotif('home/surat_masuk', $upload['error']);
        // }

        // $success = $this->homeModel->tambahSuratMasuk($this->suratMasukPayload($upload['file_name']));

        // if (! $success) {
        //     $this->deleteUploadedFile($upload['file_name']);

        //     return $this->redirectWithNotif('home/surat_masuk', 'Tambah Surat Gagal!');
        // }


        $success = $this->homeModel->tambahSuratMasuk($this->suratMasukPayload());

        if (! $success) {
            return $this->redirectWithNotif('home/surat_masuk', 'Tambah Surat Gagal!');
        }

        return $this->redirectWithNotif('home/surat_masuk', 'Tambah Surat Berhasil!');
    }

    public function ubah_surat_keluar()
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if (! $this->validate($this->ubahSuratKeluarRules())) {
            return $this->redirectWithNotif('home/surat_keluar', $this->validationErrorMessage());
        }

        if ($this->homeModel->ubahSuratKeluar(
            $this->postInt('ubah_id_surat'),
            $this->ubahSuratKeluarPayload()
        )) {
            return $this->redirectWithNotif('home/surat_keluar', 'Ubah Surat Keluar Berhasil!');
        }

        return $this->redirectWithNotif('home/surat_keluar', 'Ubah Surat Keluar Gagal!');
    }

    public function ubah_surat_masuk()
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if (! $this->validate($this->ubahSuratMasukRules())) {
            return $this->redirectWithNotif('home/surat_masuk', $this->validationErrorMessage());
        }

        if ($this->homeModel->ubahSuratMasuk(
            $this->postInt('ubah_id_surat'),
            $this->ubahSuratMasukPayload()
        )) {
            return $this->redirectWithNotif('home/surat_masuk', 'Update Surat Masuk Berhasil!');
        }

        return $this->redirectWithNotif('home/surat_masuk', 'Update Surat Masuk Gagal!');
    }

    public function ubah_file_surat_keluar()
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        return $this->replaceSuratFile(
            'home/surat_keluar',
            $this->postInt('ubah_file_surat_id'),
            fn (int $idSurat, string $fileName) => $this->homeModel->ubahFileSuratKeluar($idSurat, $fileName),
            fn (int $idSurat) => $this->homeModel->getNamaFileSuratKeluar($idSurat),
            'Ubah file surat keluar gagal!',
            'Ubah file surat keluar berhasil!'
        );
    }

    public function ubah_file_surat_masuk()
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        return $this->replaceSuratFile(
            'home/surat_masuk',
            $this->postInt('ubah_file_surat_id'),
            fn (int $idSurat, string $fileName) => $this->homeModel->ubahFileSuratMasuk($idSurat, $fileName),
            fn (int $idSurat) => $this->homeModel->getNamaFileSuratMasuk($idSurat),
            'Ubah file surat masuk gagal!',
            'Ubah file surat masuk berhasil!'
        );
    }

    public function get_surat_keluar_by_id(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        return $this->response->setJSON($this->homeModel->getSuratKeluarById($id_surat));
    }

    public function get_surat_masuk_by_id(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        return $this->response->setJSON($this->homeModel->getSuratMasukById($id_surat));
    }

    public function get_pegawai_by_jabatan(int $id_jabatan)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        return $this->response->setJSON($this->homeModel->getPegawaiByJabatan($id_jabatan));
    }

    public function hapus_surat_keluar(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        $fileName = $this->homeModel->getNamaFileSuratKeluar($id_surat);

        if (! $this->homeModel->hapusSuratKeluar($id_surat)) {
            return $this->redirectWithNotif('home/surat_keluar', 'Hapus surat keluar gagal');
        }

        $this->deleteUploadedFile($fileName);

        return $this->redirectWithNotif('home/surat_keluar', 'Hapus surat keluar berhasil!');
    }

    public function hapus_surat_masuk(int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        $fileName = $this->homeModel->getNamaFileSuratMasuk($id_surat);

        if (! $this->homeModel->hapusSuratMasuk($id_surat)) {
            return $this->redirectWithNotif('home/surat_masuk', 'Tidak dapat menghapus surat!');
        }

        $this->deleteUploadedFile($fileName);

        return $this->redirectWithNotif('home/surat_masuk', 'Hapus Surat Berhasil!');
    }

    public function hapus_disposisi(int $id_disposisi, int $id_surat)
    {
        if ($redirect = $this->requireSekretaris()) {
            return $redirect;
        }

        if ($this->homeModel->hapusDisposisi($id_disposisi)) {
            return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Hapus Disposisi Surat Berhasil!');
        }

        return $this->redirectWithNotif('home/disposisi/' . $id_surat, 'Hapus Disposisi Surat Gagal!');
    }

    public function hapus_disposisi_pegawai(int $id_disposisi, int $id_surat)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        if ($this->homeModel->hapusDisposisi($id_disposisi)) {
            return $this->redirectWithNotif('home/disposisi_keluar_pegawai/' . $id_surat, 'Hapus Disposisi Surat Berhasil!');
        }

        return $this->redirectWithNotif('home/disposisi_keluar_pegawai/' . $id_surat, 'Hapus Disposisi Surat Gagal!');
    }

    private function currentUserPegawaiId(): int
    {
        return (int) $this->session->get('id_pegawai');
    }

    private function currentUserJabatanId(): int
    {
        return (int) $this->session->get('id_jabatan');
    }

    private function currentUserLevel(): int
    {
        return (int) $this->session->get('level');
    }

    private function currentUserName(): string
    {
        return (string) $this->session->get('nama_pegawai');
    }

    private function postInt(string $key): int
    {
        return (int) $this->request->getPost($key);
    }

    private function postString(string $key): string
    {
        return (string) $this->request->getPost($key);
    }

    private function disposisiRules(): array
    {
        return [
            'tujuan_unit'    => 'required',
            'tujuan_pegawai' => 'required|integer',
            'keterangan'     => 'required',
        ];
    }

    private function disposisiPayload(): array
    {
        return [
            'id_pegawai_pengirim' => $this->currentUserPegawaiId(),
            'id_pegawai_penerima' => $this->postInt('tujuan_pegawai'),
            'keterangan'          => $this->postString('keterangan'),
        ];
    }

    private function suratKeluarRules(): array
    {
        return [
            'judul_j' => 'required',
            'tgl_kirim'   => 'required',
            'status_laporan'      => 'required',
            'catatan'     => 'required',
            'ormawa_id' => 'required|integer',
        ];
    }

    private function suratKeluarPayload(): array
    {
        return [
            'judul_j' => $this->postString('judul_j'),
            'tgl_kirim'   => $this->postString('tgl_kirim'),
            'status_laporan'      => $this->postString('status_laporan'),
            'catatan'     => $this->postString('catatan'),
            'ormawa_id'   => $this->postInt('ormawa_id'),
        ];
    }

    private function suratMasukRules(): array
    {
        return [
                'judul' => 'required',
                'tgl_terima'  => 'required',
                'status_proposal' => 'required',
                'catatan' => 'required',
                'ormawa_id' => 'required|integer',
        ];
    }

    private function suratMasukPayload(): array
    {
        $ormawaId = $this->isOrmawa()
            ? $this->session->get('ormawa_id') // 🔥 paksa dari session
            : $this->postInt('ormawa_id');

        return [
            'judul' => $this->postString('judul'),
            'tgl_terima'  => $this->postString('tgl_terima'),
            'status_proposal'    => $this->postString('status_proposal'),
            'catatan'    => $this->postString('catatan'),
            'ormawa_id' => $ormawaId,
        ];
    }

    private function ubahSuratKeluarRules(): array
    {
        return [
            'ubah_id_surat'    => 'required|integer',
            'ubah_judul_j' => 'required',
            'ubah_tgl_kirim'   => 'required',
            'ubah_status_laporan'      => 'required',
            'ubah_catatan'     => 'required',
        ];
    }

    private function ubahSuratKeluarPayload(): array
    {
        return [
            'judul_j' => $this->postString('ubah_judul_j'),
            'tgl_kirim'   => $this->postString('ubah_tgl_kirim'),
            'status_laporan'      => $this->postString('ubah_status_laporan'),
            'catatan'     => $this->postString('ubah_catatan'),
        ];
    }

    private function ubahSuratMasukRules(): array
    {
        return [
            'ubah_id_surat'    => 'required|integer',
            'ubah_judul' => 'required',
            'ubah_tgl_terima'  => 'required',
            'ubah_status_proposal'    => 'required',
            'ubah_catatan'    => 'required',
        ];
    }

    private function ubahSuratMasukPayload(): array
    {
        return [
            'judul' => $this->postString('ubah_judul'),
            'tgl_terima'  => $this->postString('ubah_tgl_terima'),
            'status_proposal'    => $this->postString('ubah_status_proposal'),
            'catatan'    => $this->postString('ubah_catatan'),
        ];
    }

    private function pegawaiDisposisiKeluarPayload(
        ?object $dataSurat = null,
        ?int $suratId = null,
        bool $canAddDisposisi = false,
        array $dropDownJabatan = [],
        array $dataDisposisiKeluar = []
    ): array {
        return [
            'judul'                   => 'Disposisi Keluar',
            'data_surat'              => $dataSurat,
            'surat_id'                => $suratId,
            'can_add_disposisi'       => $canAddDisposisi,
            'drop_down_jabatan'       => $dropDownJabatan,
            'data_disposisi_keluar'   => $dataDisposisiKeluar,
            'current_user_id_jabatan' => $this->currentUserJabatanId(),
            'current_user_level'      => $this->currentUserLevel(),
        ];
    }

    private function uploadPdf(string $field): array
    {
        $file = $this->request->getFile($field);

        if ($file === null || ! $file->isValid()) {
            return ['error' => $file?->getErrorString() ?? 'File surat wajib diunggah.'];
        }

        if (strtolower($file->getExtension()) !== 'pdf') {
            return ['error' => 'File surat harus berformat PDF.'];
        }

        $uploadPath = ROOTPATH . 'uploads';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $fileName = $file->getRandomName();
        $file->move($uploadPath, $fileName);

        return ['file_name' => $fileName];
    }

    private function replaceSuratFile(
        string $redirectPath,
        int $idSurat,
        callable $updateFile,
        callable $getOldFile,
        string $failureMessage,
        string $successMessage
    ): RedirectResponse {
        $oldFile = $getOldFile($idSurat);
        $upload = $this->uploadPdf('ubah_file_surat');

        if (isset($upload['error'])) {
            return $this->redirectWithNotif($redirectPath, $upload['error']);
        }

        if (! $updateFile($idSurat, $upload['file_name'])) {
            $this->deleteUploadedFile($upload['file_name']);

            return $this->redirectWithNotif($redirectPath, $failureMessage);
        }

        $this->deleteUploadedFile($oldFile);

        return $this->redirectWithNotif($redirectPath, $successMessage);
    }

    private function deleteUploadedFile(?string $fileName): void
    {
        if ($fileName === null || $fileName === '') {
            return;
        }

        $path = ROOTPATH . 'uploads/' . $fileName;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
