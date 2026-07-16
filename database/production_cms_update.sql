-- VYD Abogados - Actualizacion CMS publico para produccion
-- Ejecutar desde phpMyAdmin sobre la base de datos de produccion.
-- No borra datos existentes.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `meta_title` VARCHAR(255) NULL,
  `meta_description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`),
  KEY `pages_is_active_index` (`is_active`),
  KEY `pages_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_sections` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` BIGINT UNSIGNED NOT NULL,
  `key` VARCHAR(255) NULL,
  `title` VARCHAR(255) NULL,
  `subtitle` VARCHAR(255) NULL,
  `content` LONGTEXT NULL,
  `image_path` VARCHAR(255) NULL,
  `button_text` VARCHAR(255) NULL,
  `button_url` VARCHAR(255) NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_sections_page_id_index` (`page_id`),
  KEY `page_sections_key_index` (`key`),
  KEY `page_sections_sort_order_index` (`sort_order`),
  KEY `page_sections_is_active_index` (`is_active`),
  CONSTRAINT `page_sections_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `team_members` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NULL,
  `position` VARCHAR(255) NULL,
  `short_description` TEXT NULL,
  `bio` LONGTEXT NULL,
  `specialties` JSON NULL,
  `education` JSON NULL,
  `experience` JSON NULL,
  `activities` JSON NULL,
  `photo_path` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `phone` VARCHAR(255) NULL,
  `linkedin_url` VARCHAR(255) NULL,
  `is_partner` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_members_slug_unique` (`slug`),
  KEY `team_members_is_partner_index` (`is_partner`),
  KEY `team_members_sort_order_index` (`sort_order`),
  KEY `team_members_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `practice_areas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `excerpt` TEXT NULL,
  `content` LONGTEXT NULL,
  `icon` VARCHAR(255) NULL,
  `image_path` VARCHAR(255) NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `practice_areas_slug_unique` (`slug`),
  KEY `practice_areas_sort_order_index` (`sort_order`),
  KEY `practice_areas_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NULL,
  `subject` VARCHAR(255) NULL,
  `message` LONGTEXT NOT NULL,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_messages_read_at_index` (`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Columnas agregadas de forma condicional para instalaciones que ya tienen tablas CMS.
SET @db := DATABASE();

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'slug') = 0, 'ALTER TABLE `team_members` ADD COLUMN `slug` VARCHAR(255) NULL AFTER `name`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'position') = 0, 'ALTER TABLE `team_members` ADD COLUMN `position` VARCHAR(255) NULL AFTER `slug`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'short_description') = 0, 'ALTER TABLE `team_members` ADD COLUMN `short_description` TEXT NULL AFTER `position`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'bio') = 0, 'ALTER TABLE `team_members` ADD COLUMN `bio` LONGTEXT NULL AFTER `short_description`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'specialties') = 0, 'ALTER TABLE `team_members` ADD COLUMN `specialties` JSON NULL AFTER `bio`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'education') = 0, 'ALTER TABLE `team_members` ADD COLUMN `education` JSON NULL AFTER `specialties`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'experience') = 0, 'ALTER TABLE `team_members` ADD COLUMN `experience` JSON NULL AFTER `education`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'activities') = 0, 'ALTER TABLE `team_members` ADD COLUMN `activities` JSON NULL AFTER `experience`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'photo_path') = 0, 'ALTER TABLE `team_members` ADD COLUMN `photo_path` VARCHAR(255) NULL AFTER `activities`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'email') = 0, 'ALTER TABLE `team_members` ADD COLUMN `email` VARCHAR(255) NULL AFTER `photo_path`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'linkedin_url') = 0, 'ALTER TABLE `team_members` ADD COLUMN `linkedin_url` VARCHAR(255) NULL AFTER `email`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'is_partner') = 0, 'ALTER TABLE `team_members` ADD COLUMN `is_partner` TINYINT(1) NOT NULL DEFAULT 0 AFTER `linkedin_url`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'sort_order') = 0, 'ALTER TABLE `team_members` ADD COLUMN `sort_order` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `is_partner`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND COLUMN_NAME = 'is_active') = 0, 'ALTER TABLE `team_members` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sort_order`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'practice_areas' AND COLUMN_NAME = 'image_path') = 0, 'ALTER TABLE `practice_areas` ADD COLUMN `image_path` VARCHAR(255) NULL AFTER `icon`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'team_members' AND INDEX_NAME = 'team_members_slug_unique') = 0, 'ALTER TABLE `team_members` ADD UNIQUE KEY `team_members_slug_unique` (`slug`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'practice_areas' AND INDEX_NAME = 'practice_areas_slug_unique') = 0, 'ALTER TABLE `practice_areas` ADD UNIQUE KEY `practice_areas_slug_unique` (`slug`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO `team_members`
  (`name`, `slug`, `position`, `short_description`, `bio`, `specialties`, `education`, `experience`, `activities`, `photo_path`, `email`, `linkedin_url`, `is_partner`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
  ('Mauricio Domínguez', 'mauricio-dominguez', 'Socio', NULL, NULL,
   JSON_ARRAY('Derecho Administrativo', 'Derecho Corporativo', 'Derecho Civil', 'Litigación', 'Propiedad Intelectual', 'Compras Públicas'),
   JSON_ARRAY('Abogado (2019)', 'Diplomado, “Cumplimiento Normativo y Gestión de riesgos de la Empresa”, Pontificia Universidad Católica de Valparaíso.', 'Diplomado, “Derecho Administrativo”, Pontificia Universidad Católica de Valparaíso.', 'Acreditación en Compras Públicas, Mercado Público.', 'Curso “Inducción General de la Administración del Estado”, Contraloría General de la República.'),
   NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NOW(), NOW()),
  ('Cristián Vicencio', 'cristian-vicencio', 'Socio', NULL, NULL,
   JSON_ARRAY('Derecho Civil', 'Derecho del Trabajo', 'Regulación Eléctrica', 'Litigación', 'Derecho Administrativo', 'Negociaciones'),
   JSON_ARRAY('Abogado', 'Diplomado, “Tributación Aplicada a la Empresa”, Universidad Adolfo Ibáñez.', 'Diplomado, “Regulación Eléctrica”, Universidad de Chile.', 'Diplomado, “Compras Públicas”, Universidad de Viña del Mar.'),
   NULL, NULL, NULL, NULL, NULL, 1, 2, 1, NOW(), NOW()),
  ('Esteban Carrasco', 'esteban-carrasco', 'Abogado', NULL, NULL,
   JSON_ARRAY('Derecho Civil', 'Derecho Penal', 'Derecho Concursal'),
   JSON_ARRAY('Abogado (2023)', 'Diplomado en Compliance y Derecho Penal Económico - Actualización Ley N° 21.595, Ley de Delitos Económicos y Medioambientales – Universidad Adolfo Ibáñez.'),
   NULL, NULL, NULL, NULL, NULL, 0, 3, 1, NOW(), NOW()),
  ('Aldo Honorato Soto', 'aldo-honorato-soto', 'Abogado', NULL, NULL,
   JSON_ARRAY('Derecho Civil', 'Derecho Penal', 'Derecho Laboral', 'Derecho de Familia', 'Juzgados de Policía Local', 'Litigación'),
   JSON_ARRAY('Abogado (2021)'),
   NULL, NULL, NULL, NULL, NULL, 0, 4, 1, NOW(), NOW()),
  ('Sebastián Rojas', 'sebastian-rojas', 'Abogado', NULL, NULL,
   JSON_ARRAY('Derecho Laboral', 'Derecho Aduanero', 'Derecho Regulación Eléctrica', 'Derecho Civil', 'Litigación'),
   JSON_ARRAY('Abogado (2014)', 'Diplomado, “Derecho Aduanero”, Universidad Andrés Bello.', 'Diplomado, “Derecho Laboral de la Empresa”, Universidad de Los Andes.', 'Magíster en “Derecho del Trabajo”, Universidad de Los Andes.', 'Inglés jurídico, Universidad de Los Andes.', 'Máster en Derecho y Gestión Aduanera, Universidad de Barcelona.', 'Certified Shortsea Logistics, Escuela Europea.', 'Diplomado, “Regulación del Sector Eléctrico”, Universidad de Chile.'),
   NULL, NULL, NULL, NULL, NULL, 0, 5, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `position` = VALUES(`position`),
  `short_description` = VALUES(`short_description`),
  `bio` = VALUES(`bio`),
  `specialties` = VALUES(`specialties`),
  `education` = VALUES(`education`),
  `experience` = NULL,
  `activities` = NULL,
  `photo_path` = COALESCE(`team_members`.`photo_path`, VALUES(`photo_path`)),
  `email` = COALESCE(`team_members`.`email`, VALUES(`email`)),
  `linkedin_url` = COALESCE(`team_members`.`linkedin_url`, VALUES(`linkedin_url`)),
  `is_partner` = VALUES(`is_partner`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();

INSERT INTO `practice_areas`
  (`title`, `slug`, `excerpt`, `content`, `icon`, `image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
  ('Derecho Administrativo', 'derecho-administrativo', NULL, NULL, NULL, NULL, 1, 1, NOW(), NOW()),
  ('Derecho Corporativo', 'derecho-corporativo', NULL, NULL, NULL, NULL, 2, 1, NOW(), NOW()),
  ('Derecho Civil', 'derecho-civil', NULL, NULL, NULL, NULL, 3, 1, NOW(), NOW()),
  ('Litigación', 'litigacion', NULL, NULL, NULL, NULL, 4, 1, NOW(), NOW()),
  ('Propiedad Intelectual', 'propiedad-intelectual', NULL, NULL, NULL, NULL, 5, 1, NOW(), NOW()),
  ('Compras Públicas', 'compras-publicas', NULL, NULL, NULL, NULL, 6, 1, NOW(), NOW()),
  ('Derecho del Trabajo', 'derecho-del-trabajo', NULL, NULL, NULL, NULL, 7, 1, NOW(), NOW()),
  ('Regulación Eléctrica', 'regulacion-electrica', NULL, NULL, NULL, NULL, 8, 1, NOW(), NOW()),
  ('Derecho Penal', 'derecho-penal', NULL, NULL, NULL, NULL, 9, 1, NOW(), NOW()),
  ('Derecho Concursal', 'derecho-concursal', NULL, NULL, NULL, NULL, 10, 1, NOW(), NOW()),
  ('Derecho Laboral', 'derecho-laboral', NULL, NULL, NULL, NULL, 11, 1, NOW(), NOW()),
  ('Derecho de Familia', 'derecho-de-familia', NULL, NULL, NULL, NULL, 12, 1, NOW(), NOW()),
  ('Juzgados de Policía Local', 'juzgados-de-policia-local', NULL, NULL, NULL, NULL, 13, 1, NOW(), NOW()),
  ('Derecho Aduanero', 'derecho-aduanero', NULL, NULL, NULL, NULL, 14, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `excerpt` = NULL,
  `content` = NULL,
  `icon` = NULL,
  `image_path` = COALESCE(`practice_areas`.`image_path`, VALUES(`image_path`)),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();
