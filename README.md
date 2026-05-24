# Pengelolaan Surat

Proyek ini sudah dimigrasikan dari CodeIgniter 3 ke CodeIgniter 4 terbaru.

## Tentang aplikasi

Aplikasi berbasis web ini digunakan untuk mempermudah sekretaris dalam suatu instansi untuk mengelola surat masuk, surat keluar, dan disposisi.

## Screenshot aplikasi

<img src="https://image.ibb.co/noZAox/Screen_Shot_2018_02_08_at_19_19_13.png" alt="Screenshot dashboard aplikasi">
<img src="https://image.ibb.co/ftfTac/Screen_Shot_2018_02_08_at_19_19_40.png" alt="Screenshot data surat masuk">
<img src="https://image.ibb.co/e0CKgH/Screen_Shot_2018_02_08_at_19_19_52.png" alt="Screenshot data surat keluar">
<img src="https://image.ibb.co/mJYEFc/Screen_Shot_2018_02_08_at_19_22_12.png" alt="Screenshot data disposisi">
<img src="https://image.ibb.co/goJQMH/Screen_Shot_2018_02_08_at_19_22_30.png" alt="Screenshot detail aplikasi">

## Versi utama

- CodeIgniter: `4.7.2`
- PHP minimum: `8.2`

## Requirement PHP

Aktifkan extension berikut di environment runtime:

- `intl`
- `mbstring`
- `json`
- `mysqlnd`
- `curl`

`ext-intl` sekarang wajib untuk CodeIgniter 4.7.2.

## Menjalankan proyek

1. Salin `env` menjadi `.env` bila ingin override konfigurasi.
2. Sesuaikan `app.baseURL` dan koneksi database bila perlu.
3. Import `db_letter.sql`.
4. Pastikan web server mengarah ke `public/`, atau gunakan entrypoint root `index.php` yang sudah disediakan untuk kompatibilitas.

## Database default

Konfigurasi bawaan di [`app/Config/Database.php`](app/Config/Database.php):

- host: `localhost`
- user: `root`
- password: kosong
- database: `db_letter`

## Catatan migrasi

- Struktur CI3 lama `application/` dan `system/` sudah dihapus.
- Asset lama tetap dipakai dan dihubungkan ke `public/`.
- Upload file surat tetap menggunakan folder `uploads/` agar data lama tidak putus.
"# generate-letter" 
