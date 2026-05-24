<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table = 'pegawai';

    public function findUserByCredentials(string $nik, string $password): ?object
    {
        $jabatanColumns = $this->existingColumns('jabatan');
        $hasLevel       = in_array('level', $jabatanColumns, true);
        $hasNamaJabatan = in_array('nama_jabatan', $jabatanColumns, true);

        $select = ['pegawai.*'];
        if ($hasNamaJabatan) {
            $select[] = 'jabatan.nama_jabatan';
        }
        if ($hasLevel) {
            $select[] = 'jabatan.level';
        }

        $builder = $this->db->table('pegawai')
            ->select(implode(', ', $select));

        if ($hasNamaJabatan || $hasLevel) {
            $builder->join('jabatan', 'jabatan.id_jabatan = pegawai.id_jabatan', 'left');
        }

        $user = $builder->where('nik', $nik)->get()->getRow();

        if ($user === null) {
            return null;
        }

        if ($this->matchesPassword($password, (string) $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Return the list of columns that actually exist on the given table.
     * Returns [] if the table itself is missing — caller can decide how to handle.
     */
    private function existingColumns(string $table): array
    {
        try {
            return $this->db->getFieldNames($table) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function matchesPassword(string $plainPassword, string $storedHash): bool
    {
        if (str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$argon2')) {
            return password_verify($plainPassword, $storedHash);
        }

        return hash_equals(md5($plainPassword), $storedHash);
    }
}
