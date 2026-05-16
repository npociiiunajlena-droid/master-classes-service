CREATE DATABASE IF NOT EXISTS `master_classes_service`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `master_classes_service`;

DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `master_classes`;
DROP TABLE IF EXISTS `creativity_types`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `role` ENUM('visitor', 'master') NOT NULL DEFAULT 'visitor',
  `photo_path` VARCHAR(255) NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `creativity_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `hero_image_path` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `creativity_types_name_unique` (`name`),
  UNIQUE KEY `creativity_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `master_classes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativity_type_id` BIGINT UNSIGNED NOT NULL,
  `master_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `class_date` DATE NOT NULL,
  `class_time` TIME NOT NULL,
  `max_participants` SMALLINT UNSIGNED NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_date_time_unique` (`master_id`, `class_date`, `class_time`),
  KEY `master_classes_class_date_index` (`class_date`),
  KEY `master_classes_class_time_index` (`class_time`),
  KEY `type_date_time_idx` (`creativity_type_id`, `class_date`, `class_time`),
  CONSTRAINT `master_classes_creativity_type_id_foreign`
    FOREIGN KEY (`creativity_type_id`) REFERENCES `creativity_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `master_classes_master_id_foreign`
    FOREIGN KEY (`master_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `enrollments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `master_class_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollments_master_user_unique` (`master_class_id`, `user_id`),
  KEY `enrollments_user_id_index` (`user_id`),
  CONSTRAINT `enrollments_master_class_id_foreign`
    FOREIGN KEY (`master_class_id`) REFERENCES `master_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `creativity_types` (`id`, `name`, `slug`, `description`, `hero_image_path`, `created_at`, `updated_at`) VALUES
(1, 'Архитектурное моделирование', 'architectural-modeling', 'Изготовление макетов зданий и сооружений, работа с конструкциями и композицией.', 'media/architectural-model-pictures-cool-architectural-model-.jpg', NOW(), NOW()),
(2, 'Кулинария', 'cooking', 'Практика приготовления стейков, десертов и других блюд с разбором технологий.', 'media/steak-1024x678.jpg', NOW(), NOW()),
(3, 'Резьба по дереву', 'wood-carving', 'Базовые техники геометрической резьбы и создание деревянных изделий.', 'media/rd-1.jpg', NOW(), NOW());

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `role`, `photo_path`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Иванова Ольга Ивановна', 'master.olga@example.com', '+79001111111', 'master', 'assets/img/driver1.png', '$2y$12$akMbcmO.spOGGMUGzqcgnO5scnM/GCN1R0pP.romYV2bxhYW.SbO6', NOW(), NOW()),
(2, 'Смирнов Андрей Петрович', 'master.andrey@example.com', '+79002222222', 'master', 'assets/img/driver2.png', '$2y$12$akMbcmO.spOGGMUGzqcgnO5scnM/GCN1R0pP.romYV2bxhYW.SbO6', NOW(), NOW()),
(3, 'Иванов Иван Иванович', 'visitor.ivan@example.com', '+79003333333', 'visitor', NULL, '$2y$12$akMbcmO.spOGGMUGzqcgnO5scnM/GCN1R0pP.romYV2bxhYW.SbO6', NOW(), NOW()),
(4, 'Петрова Анна Сергеевна', 'visitor.anna@example.com', '+79004444444', 'visitor', NULL, '$2y$12$akMbcmO.spOGGMUGzqcgnO5scnM/GCN1R0pP.romYV2bxhYW.SbO6', NOW(), NOW());

INSERT INTO `master_classes` (`id`, `creativity_type_id`, `master_id`, `title`, `description`, `class_date`, `class_time`, `max_participants`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Моделирование моделей транспорта', 'Основы проектирования и сборки моделей транспорта.', '2026-06-05', '09:00:00', 6, 1800.00, NOW(), NOW()),
(2, 1, 1, 'Моделирование зданий и сооружений', 'Создание макетов малоэтажных зданий и фасадов.', '2026-06-06', '13:00:00', 8, 2100.00, NOW(), NOW()),
(3, 2, 2, 'Приготовление стейков', 'Разбор мяса, степеней прожарки и приготовление соусов.', '2026-06-05', '11:00:00', 10, 2500.00, NOW(), NOW()),
(4, 2, 2, 'Шоколадные поделки', 'Практика темперирования и создания шоколадных фигур.', '2026-06-07', '15:00:00', 12, 2300.00, NOW(), NOW()),
(5, 3, 1, 'Геометрическая резьба по дереву', 'Базовые элементы геометрической резьбы для начинающих.', '2026-06-08', '11:00:00', 7, 1950.00, NOW(), NOW()),
(6, 3, 2, 'Деревянные игрушки', 'Изготовление фигурок животных из натуральной древесины.', '2026-06-09', '09:00:00', 6, 1700.00, NOW(), NOW());

INSERT INTO `enrollments` (`master_class_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 3, NOW(), NOW()),
(3, 4, NOW(), NOW()),
(5, 3, NOW(), NOW());
