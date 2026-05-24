<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table = 'pegawai';

    public function findUserByCredentials(string $nik, string $password): ?object
    {
        $user = $this->db->table('pegawai')
            ->select('pegawai.*, jabatan.nama_jabatan, jabatan.level')
            ->join('jabatan', 'jabatan.id_jabatan = pegawai.id_jabatan')
            ->where('nik', $nik)
            ->get()
            ->getRow();

        if ($user === null) {
            return null;
        }

        if ($this->matchesPassword($password, (string) $user->password)) {
            return $user;
        }

        return null;
    }

    private function matchesPassword(string $plainPassword, string $storedHash): bool
    {
        if (str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$argon2')) {
            return password_verify($plainPassword, $storedHash);
        }

        return hash_equals(md5($plainPassword), $storedHash);
    }
}
