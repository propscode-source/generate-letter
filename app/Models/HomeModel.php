<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{
    public function getJumlahSurat(): array
    {
        return [
            'proposal'  => $this->countRows('proposal', 'id_surat', 'total_proposal'),
            'surat_keluar' => $this->countRows('surat_keluar', 'id_surat', 'total_surat_keluar'),
        ];
    }

    public function getJumlahSuratByOrmawa(int $ormawaId): array
    {
        $proposal = $this->db->table('proposal');
        $suratKeluar = $this->db->table('surat_keluar');

        if ($ormawaId !== null) {
            $proposal->where('ormawa_id', $ormawaId);
            $suratKeluar->where('ormawa_id', $ormawaId);
        }

        return [
            'proposal' => $proposal->countAllResults(),
            'surat_keluar' => $suratKeluar->countAllResults(),
        ];
    }

    public function getJumlahDisposisi(int $idPegawai): array
    {
        return [
            'disposisi_keluar' => $this->countRows(
                'disposisi',
                'id_pegawai_pengirim',
                'total_disposisi_keluar',
                'id_pegawai_pengirim',
                $idPegawai
            ),
            'disposisi_masuk' => $this->countRows(
                'disposisi',
                'id_pegawai_penerima',
                'total_disposisi_masuk',
                'id_pegawai_penerima',
                $idPegawai
            ),
        ];
    }

    public function tambahSuratKeluar(array $data): bool
    {
        return $this->db->table('surat_keluar')->insert($data);
    }

    public function tambahSuratMasuk(array $data): bool
    {
        return $this->db->table('proposal')->insert($data);
    }

    public function tambahDisposisi(int $idSurat, array $data): bool
    {
        return $this->db->table('disposisi')->insert($this->disposisiData($idSurat, $data));
    }

    public function disposisiSelesai(int $idSurat): bool
    {
        return $this->db->table('proposal')
            ->where('id_surat', $idSurat)
            ->update(['status' => 'selesai']);
    }

    public function ubahSuratKeluar(int $idSurat, array $data): bool
    {
        return $this->db->table('surat_keluar')
            ->where('id_surat', $idSurat)
            ->update($data);
    }

    public function ubahSuratMasuk(int $idSurat, array $data): bool
    {
        return $this->db->table('proposal')
            ->where('id_surat', $idSurat)
            ->update($data);
    }

    public function ubahFileSuratKeluar(int $idSurat, string $fileName): bool
    {
        return $this->db->table('surat_keluar')
            ->where('id_surat', $idSurat)
            ->update(['file_surat' => $fileName]);
    }

    public function ubahFileSuratMasuk(int $idSurat, string $fileName): bool
    {
        return $this->db->table('proposal')
            ->where('id_surat', $idSurat)
            ->update(['file_surat' => $fileName]);
    }

    public function getDisposisi(int $idSurat): array
    {
        return $this->disposisiBaseQuery()
            ->where('disposisi.id_surat', $idSurat)
            ->get()
            ->getResult();
    }

    public function getDisposisiMasuk(int $idPegawai): array
    {
        return $this->disposisiIncomingBaseQuery()
            ->where('id_pegawai_penerima', $idPegawai)
            ->get()
            ->getResult();
    }

    public function getDisposisiKeluar(int $idSurat, int $idPegawai): array
    {
        return $this->disposisiBaseQueryForReceiver()
            ->where('disposisi.id_pegawai_pengirim', $idPegawai)
            ->where('disposisi.id_surat', $idSurat)
            ->get()
            ->getResult();
    }

    public function getAllDisposisiKeluar(int $idPegawai): array
    {
        return $this->disposisiBaseQueryForReceiver()
            ->where('disposisi.id_pegawai_pengirim', $idPegawai)
            ->get()
            ->getResult();
    }

    public function getSuratKeluar(): array
    {
        return $this->db->table('surat_keluar')
            ->select('surat_keluar.*, ormawa.nama_ormawa')
            ->join('ormawa', 'ormawa.id = surat_keluar.ormawa_id', 'left')
            ->get()
            ->getResult();
    }

    public function getSuratMasuk(): array
    {
        return $this->db->table('proposal')
            ->select('
                proposal.id_surat,
                proposal.judul,
                proposal.tgl_terima,
                proposal.status_proposal,
                proposal.catatan,
                proposal.ormawa_id,
                ormawa.nama_ormawa
            ')
            ->join('ormawa', 'ormawa.id = proposal.ormawa_id', 'left')
            ->get()
            ->getResult();
    }

    public function getSuratKeluarById(int $idSurat): ?object
    {
        return $this->findRowById('surat_keluar', $idSurat);
    }

    public function getSuratMasukById(int $idSurat): ?object
    {
        return $this->db->table('proposal')
            ->select('proposal.id_surat, proposal.judul, proposal.tgl_terima, proposal.status_proposal, proposal.catatan, proposal.ormawa_id, ormawa.nama_ormawa')
            ->join('ormawa', 'ormawa.id = proposal.ormawa_id', 'left')
            ->where('proposal.id_surat', $idSurat)
            ->get()
            ->getRow();
    }

    public function getSuratMasukByOrmawa($ormawaId)
    {
        return $this->db->table('proposal')
            ->join('ormawa', 'ormawa.id = proposal.ormawa_id')
            ->where('proposal.ormawa_id', $ormawaId)
            ->get()
            ->getResult();
    }

    public function getSuratKeluarByOrmawa($ormawaId)
    {
        return $this->db->table('surat_keluar')
            ->join('ormawa', 'ormawa.id = surat_keluar.ormawa_id')
            ->where('surat_keluar.ormawa_id', $ormawaId)
            ->get()
            ->getResult();
    }

    public function getNamaFileSuratKeluar(int $idSurat): ?string
    {
        return $this->getFileNameFromSurat('surat_keluar', $idSurat);
    }

    // public function getNamaFileSuratMasuk(int $idSurat): ?string
    // {
    //     return $this->getFileNameFromSurat('proposal', $idSurat);
    // }

    public function getJabatan(): array
    {
        return $this->db->table('jabatan')->get()->getResult();
    }

    public function getPegawaiByJabatan(int $idJabatan): array
    {
        return $this->db->table('pegawai')
            ->where('id_jabatan', $idJabatan)
            ->get()
            ->getResult();
    }

    public function cekStatusSuratMasuk(int $idSurat): bool
    {
        $row = $this->findRowById('proposal', $idSurat, 'status');

        return ($row?->status) === 'proses';
    }

    public function hapusSuratKeluar(int $idSurat): bool
    {
        return $this->db->table('surat_keluar')
            ->where('id_surat', $idSurat)
            ->delete();
    }

    public function hapusSuratMasuk(int $idSurat): bool
    {
        return $this->db->table('proposal')
            ->where('id_surat', $idSurat)
            ->delete();
    }

    public function hapusDisposisi(int $idDisposisi): bool
    {
        return $this->db->table('disposisi')
            ->where('id_disposisi', $idDisposisi)
            ->delete();
    }

    private function countRows(
        string $table,
        string $column,
        string $alias,
        ?string $whereColumn = null,
        ?int $whereValue = null
    ): int {
        $builder = $this->db->table($table)
            ->selectCount($column, $alias);

        if ($whereColumn !== null) {
            $builder->where($whereColumn, $whereValue);
        }

        $row = $builder->get()->getRow();

        return (int) ($row->{$alias} ?? 0);
    }

    private function findRowById(string $table, int $idSurat, string $select = '*'): ?object
    {
        return $this->db->table($table)
            ->select($select)
            ->where('id_surat', $idSurat)
            ->get()
            ->getRow();
    }

    private function getFileNameFromSurat(string $table, int $idSurat): ?string
    {
        $row = $this->findRowById($table, $idSurat, 'file_surat');

        return $row?->file_surat;
    }

    private function disposisiData(int $idSurat, array $data): array
    {
        return [
            'id_surat'            => $idSurat,
            'id_pegawai_pengirim' => $data['id_pegawai_pengirim'],
            'id_pegawai_penerima' => $data['id_pegawai_penerima'],
            'keterangan'          => $data['keterangan'],
        ];
    }

    private function disposisiBaseQuery()
    {
        return $this->db->table('proposal')
            ->select('proposal.*, disposisi.*, jabatan.nama_jabatan, penerima.nama_pegawai')
            ->join('disposisi', 'disposisi.id_surat = proposal.id_surat')
            ->join('pegawai pengirim', 'disposisi.id_pegawai_pengirim = pengirim.id_pegawai')
            ->join('jabatan', 'pengirim.id_jabatan = jabatan.id_jabatan')
            ->join('pegawai penerima', 'disposisi.id_pegawai_penerima = penerima.id_pegawai');
    }

    private function disposisiIncomingBaseQuery()
    {
        return $this->db->table('proposal')
            ->join('disposisi', 'disposisi.id_surat = proposal.id_surat')
            ->join('pegawai pengirim', 'disposisi.id_pegawai_pengirim = pengirim.id_pegawai')
            ->join('jabatan', 'jabatan.id_jabatan = pengirim.id_jabatan');
    }

    private function disposisiBaseQueryForReceiver()
    {
        return $this->db->table('proposal')
            ->select('proposal.*, disposisi.*, jabatan.nama_jabatan, penerima.nama_pegawai')
            ->join('disposisi', 'disposisi.id_surat = proposal.id_surat')
            ->join('pegawai penerima', 'disposisi.id_pegawai_penerima = penerima.id_pegawai')
            ->join('jabatan', 'jabatan.id_jabatan = penerima.id_jabatan');
    }
}
