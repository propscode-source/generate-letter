<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected const ROUTE_HOME = 'home';
    protected const ROUTE_LOGIN = 'login';
    protected const ROUTE_LOGOUT = 'logout';

    protected $helpers = ['form', 'url'];
    protected Session $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = session();
    }

    protected function isLoggedIn(): bool
    {
        return (bool) $this->session->get('logged_in');
    }

    protected function isSekretaris(): bool
    {
        return $this->session->get('nama_jabatan') === 'Sekretaris';
    }


    protected function isOrmawa(): bool
    {
        return (int) $this->session->get('level') === 4;
    }

    protected function requireLogin(): ?RedirectResponse
    {
        if (! $this->isLoggedIn()) {
            return $this->redirectTo(self::ROUTE_LOGIN);
        }

        return null;
    }

    protected function requireSekretaris(): ?RedirectResponse
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        if (! $this->isSekretaris()) {
            return $this->redirectTo(self::ROUTE_LOGOUT);
        }

        return null;
    }

    protected function render(string $view, array $data = []): string
    {
        return view('template', $data + [
            'content' => view($view, $data),
        ]);
    }

    protected function validationErrorMessage(): string
    {
        $errors = service('validation')->getErrors();

        return $errors === [] ? 'Validasi gagal.' : implode('<br>', array_values($errors));
    }

    protected function redirectWithNotif(string $path, string $message): RedirectResponse
    {
        return $this->redirectTo($path)->with('notif', $message);
    }

    protected function redirectTo(string $path): RedirectResponse
    {
        return redirect()->to(site_url($path));
    }
}
