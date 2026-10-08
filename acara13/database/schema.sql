CREATE DATABASE IF NOT EXISTS si_akademik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE si_akademik;

CREATE TABLE IF NOT EXISTS prodi (
  id INT NOT NULL AUTO_INCREMENT,
  kode VARCHAR(10) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_prodi_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mahasiswa (
  id INT NOT NULL AUTO_INCREMENT,
  nim VARCHAR(20) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  prodi_id INT NOT NULL,
  angkatan YEAR NOT NULL,
  status ENUM('aktif', 'cuti', 'lulus') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_mahasiswa_nim (nim),
  KEY idx_mahasiswa_prodi (prodi_id),
  CONSTRAINT fk_mahasiswa_prodi FOREIGN KEY (prodi_id) REFERENCES prodi (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS matakuliah (
  id INT NOT NULL AUTO_INCREMENT,
  kode VARCHAR(10) NOT NULL,
  nama VARCHAR(150) NOT NULL,
  sks TINYINT NOT NULL,
  prodi_id INT NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_matakuliah_kode (kode),
  KEY idx_matakuliah_prodi (prodi_id),
  CONSTRAINT fk_matakuliah_prodi FOREIGN KEY (prodi_id) REFERENCES prodi (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO prodi (id, kode, nama) VALUES
  (1, 'TI', 'Teknik Informatika'),
  (2, 'SI', 'Sistem Informasi'),
  (3, 'TK', 'Teknik Komputer')
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

INSERT INTO mahasiswa (id, nim, nama, email, prodi_id, angkatan, status) VALUES
  (1, '2401001', 'Budi Santoso', 'budi@email.com', 1, 2024, 'aktif'),
  (2, '2401002', 'Ani Wijaya', 'ani@email.com', 1, 2024, 'aktif'),
  (3, '2402001', 'Citra Lestari', 'citra@email.com', 2, 2024, 'aktif'),
  (4, '241250598', 'rizki pora', 'ambacong@poliblug.ac.id', 2, 2024, 'aktif')
ON DUPLICATE KEY UPDATE nama = VALUES(nama), email = VALUES(email), prodi_id = VALUES(prodi_id), angkatan = VALUES(angkatan), status = VALUES(status);

INSERT INTO matakuliah (id, kode, nama, sks, prodi_id) VALUES
  (1, 'TI101', 'Pemrograman Dasar', 3, 1),
  (2, 'TI102', 'Basis Data', 3, 1),
  (3, 'SI101', 'Pengantar SI', 2, 2)
ON DUPLICATE KEY UPDATE nama = VALUES(nama), sks = VALUES(sks), prodi_id = VALUES(prodi_id);

ALTER TABLE prodi AUTO_INCREMENT = 4;
ALTER TABLE mahasiswa AUTO_INCREMENT = 5;
ALTER TABLE matakuliah AUTO_INCREMENT = 4;
