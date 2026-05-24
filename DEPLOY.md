# Deploy Pengelolaan Surat ke Hostinger VPS + Coolify

Panduan lengkap deploy aplikasi CodeIgniter 4 ini ke **Hostinger VPS** menggunakan **Coolify** sebagai panel manajemen.

---

## Ringkasan Arsitektur

```
Internet
   │
   ▼
[Hostinger VPS]  ── Coolify (panel)
   │
   ├── Container: app (PHP 8.2 + Apache)  ← image dari Dockerfile repo ini
   ├── Container: mariadb (database)
   ├── Volume: uploads_public, uploads_root, writable  (persist file user)
   └── Volume: db_data                                  (persist data DB)
```

Coolify menangani: build dari Git, SSL otomatis (Let's Encrypt), domain, env, log.

---

## Prasyarat

- ✅ Akun Hostinger dengan paket **VPS** (KVM 1 / KVM 2 disarankan, RAM ≥ 2 GB).
- ✅ Akun GitHub / GitLab tempat push repo ini.
- ✅ Domain (opsional tapi sangat disarankan; bisa subdomain dari Hostinger).

---

## Langkah 1 — Siapkan VPS Hostinger

1. Login ke **hpanel.hostinger.com** → menu **VPS** → pilih VPS Anda.
2. Saat instalasi OS, pilih **Ubuntu 22.04 LTS (64-bit)** — versi bersih, bukan template.
3. Setelah VPS aktif, catat **IP publik** dan **password root** (atau setup SSH key).
4. Login via SSH:

   ```bash
   ssh root@IP_VPS_ANDA
   ```

5. Update sistem:

   ```bash
   apt update && apt upgrade -y
   apt install -y curl ufw
   ```

6. Aktifkan firewall dasar (Coolify perlu port 80, 443, 8000, dan SSH 22):

   ```bash
   ufw allow 22/tcp
   ufw allow 80/tcp
   ufw allow 443/tcp
   ufw allow 8000/tcp
   ufw enable
   ```

---

## Langkah 2 — Install Coolify di VPS

Coolify punya installer satu baris:

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

Proses ~3–10 menit (download Docker, image Coolify, dll). Setelah selesai, Coolify jalan di:

```
http://IP_VPS_ANDA:8000
```

Buka di browser → buat akun admin pertama (email + password kuat).

> 💡 **Sangat disarankan**: arahkan subdomain (mis. `panel.domain-anda.com`) ke IP VPS, lalu di Coolify → Settings → set instance URL ke subdomain itu agar SSL panel juga aktif.

---

## Langkah 3 — Push Project ke Git

Di mesin lokal Anda (folder `D:\Pengelolaan-Surat-main`):

```bash
git init
git add .
git commit -m "Persiapan deploy Coolify"
git branch -M main
git remote add origin https://github.com/USERNAME/pengelolaan-surat.git
git push -u origin main
```

> ⚠️ Pastikan `.env` **TIDAK** ikut ter-commit (sudah diblokir di `.gitignore`).
> File `db_letter.sql` boleh dipush (akan di-import manual nanti), tapi jangan dipakai di produksi sebagai konfigurasi.

---

## Langkah 4 — Buat Project di Coolify

1. Login ke Coolify → **Projects** → **+ New Project** → beri nama `pengelolaan-surat`.
2. Klik project → **+ New Resource** → pilih **Docker Compose**.
3. Pilih **Public Repository** (atau hubungkan akun GitHub untuk auto-deploy).
4. Isi:
   - **Repository URL**: `https://github.com/USERNAME/pengelolaan-surat`
   - **Branch**: `main`
   - **Docker Compose Location**: `/docker-compose.yml`
5. Klik **Save**.

---

## Langkah 5 — Set Environment Variables

Di tab **Environment Variables** resource Anda, tambahkan:

| Key                  | Value                                  | Build? |
|----------------------|----------------------------------------|--------|
| `APP_BASE_URL`       | `https://surat.domain-anda.com/`       | ❌     |
| `CI_ENVIRONMENT`     | `production`                            | ❌     |
| `MYSQL_ROOT_PASSWORD`| (klik **Generate Random** — simpan!)   | ❌     |
| `MYSQL_DATABASE`     | `db_letter`                             | ❌     |
| `MYSQL_USER`         | `letter_user`                           | ❌     |
| `MYSQL_PASSWORD`     | (klik **Generate Random** — simpan!)   | ❌     |

> 🔒 Simpan `MYSQL_ROOT_PASSWORD` dan `MYSQL_PASSWORD` di password manager. Anda akan butuh untuk import SQL nanti.

---

## Langkah 6 — Atur Domain & SSL

1. Di Hostinger / penyedia DNS Anda, buat **A record**:
   ```
   surat.domain-anda.com  →  IP_VPS_ANDA
   ```
2. Di Coolify → tab **Domains** resource → masukkan `https://surat.domain-anda.com`.
3. Coolify otomatis mengurus SSL Let's Encrypt (tunggu ~1 menit).

---

## Langkah 7 — Deploy

1. Klik tombol **Deploy** (kanan atas).
2. Coolify akan:
   - Clone repo
   - Build image dari `Dockerfile` (~3–5 menit pertama kali)
   - Start `mariadb` → tunggu healthy
   - Start `app` → tunggu healthy
3. Lihat log real-time di tab **Logs**.

Jika sukses, buka `https://surat.domain-anda.com` — Anda akan melihat halaman CodeIgniter / login aplikasi Anda.

---

## Langkah 8 — Import Database

Aplikasi baru jalan tapi tabel kosong. Import `db_letter.sql`:

### Opsi A — Via terminal Coolify (paling mudah)

1. Di Coolify → resource Anda → tab **Terminal** → pilih container `mariadb`.
2. Jalankan (ganti password):

   ```bash
   mysql -u root -p db_letter < /dev/stdin
   ```

   Tapi karena file SQL ada di mesin lokal, lebih praktis pakai opsi B.

### Opsi B — Upload via SCP lalu import

Dari laptop Anda:

```bash
# 1. Upload SQL ke VPS
scp db_letter.sql root@IP_VPS_ANDA:/tmp/

# 2. SSH ke VPS
ssh root@IP_VPS_ANDA

# 3. Cari container id mariadb
docker ps | grep mariadb

# 4. Import (ganti CONTAINER_ID dan MYSQL_ROOT_PASSWORD)
docker exec -i CONTAINER_ID mysql -uroot -pMYSQL_ROOT_PASSWORD db_letter < /tmp/db_letter.sql
```

> ⚠️ File `db_letter.sql` Anda saat ini berisi `CREATE DATABASE simplo_db_letter`. Sebelum import, **edit baris 23–24** menjadi `CREATE DATABASE IF NOT EXISTS db_letter` dan `USE db_letter` agar cocok dengan env. Atau hapus saja kedua baris itu — toh database sudah dibuat MariaDB lewat env `MYSQL_DATABASE`.

### Opsi C — phpMyAdmin (paling nyaman)

Tambahkan service phpMyAdmin di `docker-compose.yml` (sementara), atau install via Coolify marketplace, lalu import via UI.

---

## Langkah 9 — Verifikasi

1. Buka `https://surat.domain-anda.com` → login dengan user dari dump:
   - **NIK**: `999`  **Password**: `admin` (hash MD5 `21232f297a57a5a743894a0e4a801fc3`)
2. Coba upload sebuah surat → cek apakah file tetap ada setelah redeploy (test volume persist).
3. Cek log error di Coolify → tab **Logs** jika ada masalah.

---

## Auto-Deploy dari Git (opsional)

Di Coolify → tab **Configuration** → aktifkan **Automatic Deployment** dan tambahkan webhook GitHub. Setelah itu, setiap `git push origin main` → otomatis re-deploy.

---

## Backup

**Database** — di Coolify → tab **Backups** resource MariaDB → set jadwal harian, simpan ke S3 / local.

**Uploads** — backup volume `uploads_public` & `uploads_root` rutin:

```bash
docker run --rm \
  -v pengelolaan-surat_uploads_public:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/uploads-$(date +%F).tar.gz -C /data .
```

---

## Troubleshooting

| Gejala                              | Penyebab umum / solusi                                                                 |
|-------------------------------------|----------------------------------------------------------------------------------------|
| 500 Internal Server Error           | Cek log app: `writable/` belum writable → entrypoint.sh harusnya sudah fix; restart.    |
| "Unable to connect to your database"| Env `database.default.*` belum benar atau container `mariadb` belum healthy.            |
| File upload hilang setelah deploy   | Volume tidak ter-mount. Pastikan `docker-compose.yml` tidak di-edit untuk hilangkan vol.|
| CSS/JS 404                          | `app.baseURL` belum diset dengan trailing slash dan https://.                          |
| "mixed content" di browser          | Set `app.forceGlobalSecureRequests=true` dan pastikan baseURL pakai `https://`.        |
| Login berhasil tapi langsung logout | Session disimpan di volume `writable` → cek mount path benar.                          |

Cek log terstruktur: Coolify → **Logs** → filter container `app` atau `mariadb`.

---

## Update Aplikasi

1. Push perubahan ke Git: `git push origin main`.
2. Coolify (jika auto-deploy aktif) langsung rebuild & deploy.
3. Manual: klik **Redeploy** di Coolify.

Volume `uploads_*` dan `db_data` **tidak terhapus** saat redeploy — aman.

---

## Checklist Akhir

- [ ] VPS up, firewall aktif (22/80/443/8000).
- [ ] Coolify terpasang, akun admin dibuat.
- [ ] Repo di-push ke GitHub.
- [ ] Project Coolify dibuat, env terisi semua.
- [ ] Domain & SSL aktif.
- [ ] Deploy sukses, container `app` & `mariadb` healthy.
- [ ] `db_letter.sql` ter-import.
- [ ] Bisa login, upload file, dan file persist setelah redeploy.
- [ ] Backup database dijadwalkan.

Selamat — aplikasi pengelolaan surat Anda sudah live! 🎉
