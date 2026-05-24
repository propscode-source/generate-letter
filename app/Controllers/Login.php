<?php

namespace App\Controllers;

use App\Models\LoginModel;

class Login extends BaseController
{
    private LoginModel $loginModel;

    public function __construct()
    {
        $this->loginModel = new LoginModel();
    }

    public function index()
    {
        if ($this->isLoggedIn()) {
            return $this->redirectTo(self::ROUTE_HOME);
        }

        return view('login');
    }

    public function validateCredentials()
    {
        if ($this->isLoggedIn()) {
            return $this->redirectTo(self::ROUTE_HOME);
        }

        if (! $this->validate($this->loginValidationRules())) {
            return $this->redirectWithNotif(self::ROUTE_LOGIN, $this->validationErrorMessage());
        }

        try {
            $user = $this->attemptLogin(
                (string) $this->request->getPost('nik'),
                (string) $this->request->getPost('password'),
            );
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Login DB error ({class}): {message} @ {file}:{line}',
                [
                    'class'   => $e::class,
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => (string) $e->getLine(),
                ]
            );

            return $this->redirectWithNotif(
                self::ROUTE_LOGIN,
                $this->describeLoginException($e)
            );
        }

        if ($user === null) {
            return $this->redirectWithNotif(self::ROUTE_LOGIN, 'NIK atau Password salah!');
        }

        try {
            $this->storeAuthenticatedUser($user);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Login session error ({class}): {message} @ {file}:{line}',
                [
                    'class'   => $e::class,
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => (string) $e->getLine(),
                ]
            );

            return $this->redirectWithNotif(
                self::ROUTE_LOGIN,
                'Sesi tidak dapat disimpan: ' . esc($e->getMessage())
                    . ' — pastikan writable/session writable oleh user www-data.'
            );
        }

        return $this->redirectWithNotif(self::ROUTE_HOME, 'Login berhasil!');
    }

    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->session->destroy();
        }

        return $this->redirectTo(self::ROUTE_LOGIN);
    }

    private function loginValidationRules(): array
    {
        return [
            'nik'      => 'required|numeric',
            'password' => 'required',
        ];
    }

    private function attemptLogin(string $nik, string $password): ?object
    {
        return $this->loginModel->findUserByCredentials($nik, $password);
    }

    /**
     * Convert a raw DB / framework exception into a user-facing message that
     * tells the admin exactly what is wrong and how to fix it. We intentionally
     * expose the underlying message because this is an internal tool — flip
     * this back to a generic message once the cause is identified.
     */
    private function describeLoginException(\Throwable $e): string
    {
        $raw  = $e->getMessage();
        $hint = $this->loginExceptionHint($raw);

        // esc() is provided by url/form helpers loaded via $helpers in BaseController.
        return 'Gagal login: ' . esc($raw) . ($hint !== '' ? ' — ' . $hint : '');
    }

    private function loginExceptionHint(string $message): string
    {
        $msg = strtolower($message);

        return match (true) {
            str_contains($msg, "table") && str_contains($msg, "doesn't exist")
                => "Tabel belum dibuat. Import db_letter.sql ke MariaDB (lihat DEPLOY.md langkah 8).",
            str_contains($msg, 'unknown column')
                => "Skema DB lama / tidak sinkron. Re-import db_letter.sql atau jalankan ALTER TABLE yang sesuai.",
            str_contains($msg, 'access denied') || str_contains($msg, 'authentication')
                => "Kredensial DB salah. Cek env MYSQL_USER / MYSQL_PASSWORD di Coolify.",
            str_contains($msg, 'unable to connect') || str_contains($msg, "can't connect") || str_contains($msg, 'connection refused')
                => "Container MariaDB tidak terjangkau. Cek apakah service mariadb sudah healthy.",
            str_contains($msg, 'unknown database')
                => "Database belum dibuat. Cek env MYSQL_DATABASE di Coolify.",
            default => 'Cek writable/logs/log-' . date('Y-m-d') . '.php di container app untuk detail.',
        };
    }

    private function storeAuthenticatedUser(object $user): void
    {
        $this->session->regenerate();
        $this->session->set([
            'logged_in'    => true,
            'nik'          => $user->nik,
            'id_pegawai'   => $user->id_pegawai,
            'id_jabatan'   => $user->id_jabatan,
            'nama_pegawai' => $user->nama_pegawai,
            'nama_jabatan' => $user->nama_jabatan,
            'level'        => $user->level ?? null,
            'ormawa_id'    => $user->ormawa_id ?? null,
        ]);
    }
}
