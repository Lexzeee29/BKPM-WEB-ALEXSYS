ALTER TABLE mahasiswa
  ADD COLUMN status ENUM('aktif', 'cuti', 'lulus') NOT NULL DEFAULT 'aktif' AFTER angkatan;

UPDATE mahasiswa
SET status = 'aktif'
WHERE status IS NULL OR status = '';
