CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------
-- Tabel: tugas
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS `tugas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `nama_tugas` VARCHAR(200) NOT NULL,
  `mata_kuliah` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT,
  `tanggal_diberikan` DATE NOT NULL,
  `tenggat_waktu` DATE NOT NULL,
  `prioritas` ENUM('Rendah','Sedang','Tinggi') NOT NULL DEFAULT 'Sedang',
  `status` ENUM('Belum Dikerjakan','Sedang Dikerjakan','Selesai') NOT NULL DEFAULT 'Belum Dikerjakan',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tugas_user` (`user_id`),
  CONSTRAINT `fk_tugas_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
