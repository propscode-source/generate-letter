<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('logout', 'Login::logout');

$routes->group('login', static function ($routes): void {
    $routes->get('/', 'Login::index');
    $routes->post('validate', 'Login::validateCredentials');
    $routes->get('logout', 'Login::logout');
});

$routes->group('home', static function ($routes): void {
    $routes->get('/', 'Home::index');
    $routes->get('surat_masuk', 'Home::surat_masuk');
    $routes->get('surat_keluar', 'Home::surat_keluar');
    $routes->get('disposisi/(:num)', 'Home::disposisi/$1');
    $routes->get('disposisi_selesai/(:num)', 'Home::disposisi_selesai/$1');
    $routes->get('disposisi_keluar', 'Home::disposisi_keluar');
    $routes->get('disposisi_masuk', 'Home::disposisi_masuk');
    $routes->get('disposisi_keluar_pegawai/(:num)', 'Home::disposisi_keluar_pegawai/$1');

    $routes->post('tambah_disposisi/(:num)', 'Home::tambah_disposisi/$1');
    $routes->post('tambah_disposisi_pegawai/(:num)', 'Home::tambah_disposisi_pegawai/$1');
    $routes->post('tambah_surat_keluar', 'Home::tambah_surat_keluar');
    $routes->post('tambah_surat_masuk', 'Home::tambah_surat_masuk');
    $routes->post('ubah_surat_keluar', 'Home::ubah_surat_keluar');
    $routes->post('ubah_surat_masuk', 'Home::ubah_surat_masuk');
    $routes->post('ubah_file_surat_keluar', 'Home::ubah_file_surat_keluar');
    $routes->post('ubah_file_surat_masuk', 'Home::ubah_file_surat_masuk');

    $routes->get('get_surat_keluar_by_id/(:num)', 'Home::get_surat_keluar_by_id/$1');
    $routes->get('get_surat_masuk_by_id/(:num)', 'Home::get_surat_masuk_by_id/$1');
    $routes->get('get_pegawai_by_jabatan/(:num)', 'Home::get_pegawai_by_jabatan/$1');

    $routes->get('hapus_surat_keluar/(:num)', 'Home::hapus_surat_keluar/$1');
    $routes->get('hapus_surat_masuk/(:num)', 'Home::hapus_surat_masuk/$1');
    $routes->get('hapus_disposisi/(:num)/(:num)', 'Home::hapus_disposisi/$1/$2');
    $routes->get('hapus_disposisi_pegawai/(:num)/(:num)', 'Home::hapus_disposisi_pegawai/$1/$2');
});
