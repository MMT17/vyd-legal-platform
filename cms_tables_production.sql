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
  UNIQUE KEY `pages_slug_unique` (`slug`)
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
  CONSTRAINT `page_sections_page_id_foreign`
    FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `team_members` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `position` VARCHAR(255) NULL,
  `short_description` TEXT NULL,
  `bio` LONGTEXT NULL,
  `specialties` JSON NULL,
  `education` JSON NULL,
  `experience` JSON NULL,
  `activities` JSON NULL,
  `photo_path` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `linkedin_url` VARCHAR(255) NULL,
  `is_partner` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_members_name_unique` (`name`),
  KEY `team_members_is_partner_sort_order_index` (`is_partner`, `sort_order`),
  KEY `team_members_is_active_sort_order_index` (`is_active`, `sort_order`)
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
  UNIQUE KEY `practice_areas_slug_unique` (`slug`)
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (
  `title`, `slug`, `meta_title`, `meta_description`, `is_active`, `sort_order`, `created_at`, `updated_at`
) VALUES
  (
    'Home',
    'home',
    'VYD Abogados | Estudio Jurídico',
    'Asesoría jurídica clara, estratégica y cercana.',
    1,
    1,
    NOW(),
    NOW()
  ),
  (
    'Nosotros',
    'nosotros',
    'Nosotros | VYD Abogados',
    'Conoce el enfoque profesional de VYD Abogados.',
    1,
    2,
    NOW(),
    NOW()
  )
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `is_active` = VALUES(`is_active`),
  `sort_order` = VALUES(`sort_order`),
  `updated_at` = NOW();

INSERT INTO `practice_areas` (
  `title`, `slug`, `excerpt`, `content`, `icon`, `image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`
) VALUES
  (
    'Derecho Civil',
    'derecho-civil',
    'Asesoría y representación en materias civiles, contratos, obligaciones y resolución de conflictos.',
    'Prestamos asesoría en materias civiles con foco en soluciones claras, prevención de riesgos y representación estratégica en conflictos.',
    NULL,
    NULL,
    1,
    1,
    NOW(),
    NOW()
  ),
  (
    'Derecho Laboral',
    'derecho-laboral',
    'Acompañamiento legal en relaciones laborales, prevención de contingencias y defensa judicial.',
    'Apoyamos a personas y empresas en asuntos laborales, revisión de contratos, cumplimiento normativo y litigios asociados.',
    NULL,
    NULL,
    2,
    1,
    NOW(),
    NOW()
  ),
  (
    'Derecho de Familia',
    'derecho-de-familia',
    'Orientación jurídica cercana en materias familiares sensibles y procedimientos relacionados.',
    'Entregamos asesoría en materias de familia con una mirada responsable, confidencial y orientada a proteger los intereses de nuestros clientes.',
    NULL,
    NULL,
    3,
    1,
    NOW(),
    NOW()
  ),
  (
    'Derecho Comercial',
    'derecho-comercial',
    'Asesoría para empresas, contratos, negocios y estructuras comerciales.',
    'Acompañamos a empresas y emprendedores en materias comerciales, contratos, negociación y gestión legal preventiva.',
    NULL,
    NULL,
    4,
    1,
    NOW(),
    NOW()
  )
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `excerpt` = VALUES(`excerpt`),
  `content` = VALUES(`content`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();

INSERT INTO `team_members` (
  `name`, `position`, `short_description`, `bio`, `specialties`, `education`, `experience`, `activities`,
  `photo_path`, `email`, `linkedin_url`, `is_partner`, `sort_order`, `is_active`, `created_at`, `updated_at`
)
VALUES (
  'Mauricio Domínguez',
  'Socio',
  'Profesional con experiencia en asuntos legales complejos, comprometido con soluciones efectivas y confidenciales.',
  NULL,
  '["Derecho Administrativo","Derecho Corporativo","Derecho Civil","Litigación","Propiedad Intelectual","Compras Públicas"]',
  '["Abogado (2019)","Diplomado, “Cumplimiento Normativo y Gestión de riesgos de la Empresa”, Pontificia Universidad Católica de Valparaíso.","Diplomado, “Derecho Administrativo”, Pontificia Universidad Católica de Valparaíso.","Acreditación en Compras Públicas, Mercado Público.","Curso “Inducción General de la Administración del Estado”, Contraloría General de la República."]',
  '["Hospital Carlos Van Buren.","Servicio de Salud Valparaíso-San Antonio."]',
  '["Relator en capacitación “Traspaso del personal de Educación Municipal a los Servicios Locales de Educación”."]',
  NULL,
  NULL,
  NULL,
  1,
  1,
  1,
  NOW(),
  NOW()
)
ON DUPLICATE KEY UPDATE
  `position` = VALUES(`position`),
  `short_description` = VALUES(`short_description`),
  `specialties` = VALUES(`specialties`),
  `education` = VALUES(`education`),
  `experience` = VALUES(`experience`),
  `activities` = VALUES(`activities`),
  `is_partner` = VALUES(`is_partner`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();

INSERT INTO `team_members` (
  `name`, `position`, `short_description`, `bio`, `specialties`, `education`, `experience`, `activities`,
  `photo_path`, `email`, `linkedin_url`, `is_partner`, `sort_order`, `is_active`, `created_at`, `updated_at`
)
VALUES (
  'Cristián Vicencio',
  'Socio',
  'Abogado con experiencia en derecho civil, derecho del trabajo, regulación eléctrica, litigación y asuntos administrativos.',
  NULL,
  '["Derecho Civil","Derecho del Trabajo","Regulación Eléctrica","Litigación","Derecho Administrativo","Negociaciones"]',
  '["Abogado","Diplomado, “Tributación Aplicada a la Empresa”, Universidad Adolfo Ibáñez.","Diplomado, “Regulación Eléctrica”, Universidad de Chile.","Diplomado, “Compras Públicas”, Universidad de Viña del Mar."]',
  '["Estudio Jurídico, Navia & Torres y Cía.","Estudio Jurídico, Vasseur Abogados.","1º Juzgado Civil de Quilpué.","Estudio Jurídico, Vicencio y Castro Abogados Limitada.","Estudio Jurídico, Vicencio y Domínguez Abogados Limitada."]',
  NULL,
  NULL,
  NULL,
  NULL,
  1,
  2,
  1,
  NOW(),
  NOW()
)
ON DUPLICATE KEY UPDATE
  `position` = VALUES(`position`),
  `short_description` = VALUES(`short_description`),
  `specialties` = VALUES(`specialties`),
  `education` = VALUES(`education`),
  `experience` = VALUES(`experience`),
  `activities` = VALUES(`activities`),
  `is_partner` = VALUES(`is_partner`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();

INSERT INTO `team_members` (
  `name`, `position`, `short_description`, `bio`, `specialties`, `education`, `experience`, `activities`,
  `photo_path`, `email`, `linkedin_url`, `is_partner`, `sort_order`, `is_active`, `created_at`, `updated_at`
)
VALUES (
  'Esteban Carrasco',
  'Abogado',
  'Abogado orientado a la litigación y asesoría en materias civiles, penales y concursales.',
  NULL,
  '["Derecho Civil","Derecho Penal","Derecho Concursal"]',
  '["Abogado (2023)","Diplomado en Compliance y Derecho Penal Económico - Actualización Ley N° 21.595, Ley de Delitos Económicos y Medioambientales – Universidad Adolfo Ibáñez."]',
  '["Salazar e Hidalgo Abogados.","Estudio Jurídico Lena y Cía."]',
  NULL,
  NULL,
  NULL,
  NULL,
  0,
  3,
  1,
  NOW(),
  NOW()
)
ON DUPLICATE KEY UPDATE
  `position` = VALUES(`position`),
  `short_description` = VALUES(`short_description`),
  `specialties` = VALUES(`specialties`),
  `education` = VALUES(`education`),
  `experience` = VALUES(`experience`),
  `activities` = VALUES(`activities`),
  `is_partner` = VALUES(`is_partner`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();

INSERT INTO `team_members` (
  `name`, `position`, `short_description`, `bio`, `specialties`, `education`, `experience`, `activities`,
  `photo_path`, `email`, `linkedin_url`, `is_partner`, `sort_order`, `is_active`, `created_at`, `updated_at`
)
VALUES (
  'Aldo Honorato Soto',
  'Abogado',
  'Abogado con práctica en materias civiles, penales, laborales, familia, policía local y litigación.',
  NULL,
  '["Derecho Civil","Derecho Penal","Derecho Laboral","Derecho de Familia","Juzgados de Policía Local","Litigación"]',
  '["Abogado (2021)"]',
  '["Estudio Jurídico, Demaría Varas.","Ejercicio libre de la profesión."]',
  NULL,
  NULL,
  NULL,
  NULL,
  0,
  4,
  1,
  NOW(),
  NOW()
)
ON DUPLICATE KEY UPDATE
  `position` = VALUES(`position`),
  `short_description` = VALUES(`short_description`),
  `specialties` = VALUES(`specialties`),
  `education` = VALUES(`education`),
  `experience` = VALUES(`experience`),
  `activities` = VALUES(`activities`),
  `is_partner` = VALUES(`is_partner`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();
