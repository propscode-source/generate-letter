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

        $user = $this->attemptLogin(
            (string) $this->request->getPost('nik'),
            (string) $this->request->getPost('password'),
        );

        if ($user === null) {
            return $this->redirectWithNotif(self::ROUTE_LOGIN, 'NIK atau Password salah!');
        }

        $this->storeAuthenticatedUser($user);

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
            'level'        => $user->level,
            'ormawa_id'     => $user->ormawa_id,
        ]);
    }
}
