-- ============================================================================
-- Dump database: db_letter
-- Target     : MariaDB 10.11 / MySQL 8.x (container Coolify)
-- Charset    : utf8mb4 / utf8mb4_general_ci
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

START TRANSACTION;

-- Database `db_letter` sudah dibuat otomatis oleh container MariaDB
-- (lewat env MYSQL_DATABASE). Kita hanya pilih database:
-- USE db_letter;   -- biarkan client yang pilih, atau aktifkan jika import manual.

-- ----------------------------------------------------------------------------
-- Table: ormawa  (parent — harus dibuat lebih dulu karena dirujuk FK)
-- ----------------------------------------------------------------------------
CREATE TABLE `ormawa` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_ormawa` VARCHAR(100)     NOT NULL,
  `singkatan`   VARCHAR(100)     NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ormawa` (`id`, `nama_ormawa`, `singkatan`) VALUES
(1,  'Badan Eksekutif Mahasiswa Fasilkom',     'BEM Fasilkom'),
(2,  'DPM Fasilkom Unsri',                     'DPM Fasilkom Unsri'),
(3,  'Himpunan Mahasiswa Sistem Informasi',    'HIMSI'),
(4,  'Himpunan Mahasiswa Teknik Informatika',  'HMIF'),
(5,  'Himpunan Mahasiswa Sistem Komputer',     'HIMASISKO'),
(6,  'Himpunan Mahasiswa Diploma Komputer',    'HIMDIKO'),
(7,  'Intel Fasilkom',                         'Intel Fasilkom'),
(8,  'NAC',                                    'NAC'),
(9,  'SBI WIFI',                               'SBI WIFI'),
(10, 'FASCO',                                  'FASCO');

-- ----------------------------------------------------------------------------
-- Table: jabatan
-- ----------------------------------------------------------------------------
CREATE TABLE `jabatan` (
  `id_jabatan`   INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_jabatan` VARCHAR(100) NOT NULL,
  `level`        INT(11)      NOT NULL,
  PRIMARY KEY (`id_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `jabatan` (`id_jabatan`, `nama_jabatan`, `level`) VALUES
(1, 'Sekretaris',       1),
(2, 'Kepala Marketing', 2),
(3, 'Supervisor',       3),
(4, 'ORMAWA',           4),
(6, 'ORMAWA',           2);

-- ----------------------------------------------------------------------------
-- Table: pegawai
--   Catatan: ormawa_id bukan UNIQUE (memungkinkan 1 ormawa punya >1 pegawai).
--   Password disimpan sebagai MD5 (default akun seed = 'admin').
-- ----------------------------------------------------------------------------
CREATE TABLE `pegawai` (
  `id_pegawai`   INT(11)              NOT NULL AUTO_INCREMENT,
  `nik`          INT(11)              NOT NULL,
  `nama_pegawai` VARCHAR(200)         NOT NULL,
  `id_jabatan`   INT(11)              NOT NULL,
  `password`     TEXT                 NOT NULL,
  `ormawa_id`    INT(10) UNSIGNED     DEFAULT NULL,
  PRIMARY KEY (`id_pegawai`),
  KEY `idx_pegawai_jabatan` (`id_jabatan`),
  KEY `idx_pegawai_ormawa` (`ormawa_id`),
  CONSTRAINT `fk_pegawai_jabatan`
    FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pegawai_ormawa`
    FOREIGN KEY (`ormawa_id`) REFERENCES `ormawa` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pegawai` (`id_pegawai`, `nik`, `nama_pegawai`, `id_jabatan`, `password`, `ormawa_id`) VALUES
(1, 999, 'Toni Haikal',         1, '21232f297a57a5a743894a0e4a801fc3', NULL),
(2, 888, 'Felix Satya',         2, '21232f297a57a5a743894a0e4a801fc3', NULL),
(3, 777, 'Armaningtyas Utami',  3, '21232f297a57a5a743894a0e4a801fc3', NULL),
(4, 666, 'DPM Fasilkom Unsri',  4, '21232f297a57a5a743894a0e4a801fc3', 2),
(5, 555, 'HIMSI',               4, '21232f297a57a5a743894a0e4a801fc3', NULL);

-- ----------------------------------------------------------------------------
-- Table: proposal
-- ----------------------------------------------------------------------------
CREATE TABLE `proposal` (
  `id_surat`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ormawa_id`       INT(10) UNSIGNED DEFAULT NULL,
  `judul`           VARCHAR(255)     DEFAULT NULL,
  `tgl_terima`      DATE             DEFAULT NULL,
  `status_proposal` VARCHAR(50)      DEFAULT NULL,
  `catatan`         TEXT             DEFAULT NULL,
  PRIMARY KEY (`id_surat`),
  KEY `idx_proposal_ormawa` (`ormawa_id`),
  CONSTRAINT `fk_proposal_ormawa`
    FOREIGN KEY (`ormawa_id`) REFERENCES `ormawa` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `proposal` (`id_surat`, `ormawa_id`, `judul`, `tgl_terima`, `status_proposal`, `catatan`) VALUES
(2, 2, 'Kegiatan INAGURASI Tahun 2025/2026',                  '2026-04-22', 'diterima', '-'),
(3, 2, 'Kunjungan Panti Asuhan di Palembang',                 '2026-04-21', 'direvisi', 'Revisi RAB'),
(4, 4, 'Musyawarah Besar Pemilihan Ketua Umum DPM Fasilkom',  '2026-04-21', 'diajukan', '-');

-- ----------------------------------------------------------------------------
-- Table: surat_keluar
--   Catatan: ormawa_id bukan UNIQUE (1 ormawa boleh punya banyak surat).
-- ----------------------------------------------------------------------------
CREATE TABLE `surat_keluar` (
  `id_surat`       INT(11)          NOT NULL AUTO_INCREMENT,
  `ormawa_id`      INT(10) UNSIGNED DEFAULT NULL,
  `judul_j`        VARCHAR(200)     NOT NULL,
  `tgl_kirim`      DATE             NOT NULL,
  `status_laporan` VARCHAR(100)     NOT NULL,
  `catatan`        VARCHAR(100)     NOT NULL,
  PRIMARY KEY (`id_surat`),
  KEY `idx_surat_keluar_ormawa` (`ormawa_id`),
  CONSTRAINT `fk_surat_keluar_ormawa`
    FOREIGN KEY (`ormawa_id`) REFERENCES `ormawa` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `surat_keluar` (`id_surat`, `ormawa_id`, `judul_j`, `tgl_kirim`, `status_laporan`, `catatan`) VALUES
(1, 10, 'asasa',  '2026-05-13', 'diterima', 'asasa'),
(2, 2,  'aaaavv', '2026-05-04', 'direvisi', 'asas');

-- ----------------------------------------------------------------------------
-- Table: surat_masuk1  (dirujuk FK oleh disposisi)
-- ----------------------------------------------------------------------------
CREATE TABLE `surat_masuk1` (
  `id_surat`        INT(11)          NOT NULL AUTO_INCREMENT,
  `ormawa_id`       INT(10) UNSIGNED DEFAULT NULL,
  `judul`           VARCHAR(200)     NOT NULL,
  `tgl_terima`      DATE             NOT NULL,
  `status_proposal` VARCHAR(100)     NOT NULL,
  `catatan`         VARCHAR(200)     NOT NULL,
  PRIMARY KEY (`id_surat`),
  KEY `idx_surat_masuk1_ormawa` (`ormawa_id`),
  CONSTRAINT `fk_surat_masuk1_ormawa`
    FOREIGN KEY (`ormawa_id`) REFERENCES `ormawa` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------------------
-- Table: surat_keluar1  (legacy — dipertahankan untuk kompatibilitas data lama)
-- ----------------------------------------------------------------------------
CREATE TABLE `surat_keluar1` (
  `id_surat`       INT(11)      NOT NULL AUTO_INCREMENT,
  `judul_j`        VARCHAR(200) NOT NULL,
  `tgl_kirim`      DATE         NOT NULL,
  `status_laporan` VARCHAR(200) NOT NULL,
  `catatan`        VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id_surat`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `surat_keluar1` (`id_surat`, `judul_j`, `tgl_kirim`, `status_laporan`, `catatan`) VALUES
(4, 'INM/129/KPW',  '2018-02-01', 'SMK DB Jombang', 'Undangan Pernikahannn'),
(5, 'KOB/1212/FFF', '2018-01-31', 'SMPN 2 Jombang', 'Undangan HUT Telkom');

-- ----------------------------------------------------------------------------
-- Table: disposisi
-- ----------------------------------------------------------------------------
CREATE TABLE `disposisi` (
  `id_disposisi`        INT(11)   NOT NULL AUTO_INCREMENT,
  `id_surat`            INT(11)   NOT NULL,
  `id_pegawai_pengirim` INT(11)   NOT NULL,
  `id_pegawai_penerima` INT(11)   NOT NULL,
  `tgl_disposisi`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `keterangan`          TEXT      NOT NULL,
  PRIMARY KEY (`id_disposisi`),
  KEY `idx_disposisi_surat` (`id_surat`),
  KEY `idx_disposisi_penerima` (`id_pegawai_penerima`),
  KEY `idx_disposisi_pengirim` (`id_pegawai_pengirim`),
  CONSTRAINT `fk_disposisi_surat`
    FOREIGN KEY (`id_surat`) REFERENCES `surat_masuk1` (`id_surat`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_disposisi_penerima`
    FOREIGN KEY (`id_pegawai_penerima`) REFERENCES `pegawai` (`id_pegawai`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_disposisi_pengirim`
    FOREIGN KEY (`id_pegawai_pengirim`) REFERENCES `pegawai` (`id_pegawai`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
