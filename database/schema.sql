-- ============================================================================
-- O BARDO - SCHEMA RELACIONAL MARIADB 10.11 (PHP 8.3 CORE)
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `campaign_events`;
DROP TABLE IF EXISTS `plot_twists`;
DROP TABLE IF EXISTS `handouts`;
DROP TABLE IF EXISTS `chronicles`;
DROP TABLE IF EXISTS `audio_sync_states`;
DROP TABLE IF EXISTS `audio_tracks`;
DROP TABLE IF EXISTS `dice_rolls`;
DROP TABLE IF EXISTS `characters`;
DROP TABLE IF EXISTS `campaign_members`;
DROP TABLE IF EXISTS `campaigns`;
DROP TABLE IF EXISTS `users`;

-- 1. USUÁRIOS & AUTENTICAÇÃO
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `token` VARCHAR(255) NULL,
  `token_expires_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CAMPANHAS DE RPG
CREATE TABLE `campaigns` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `gm_user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `rule_system` VARCHAR(50) NOT NULL DEFAULT 'TORMENTA20',
  `description` TEXT NULL,
  `cover_url` VARCHAR(500) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`gm_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_campaigns_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. MEMBROS DA CAMPANHA
CREATE TABLE `campaign_members` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `member_role` ENUM('gm', 'co_gm', 'player', 'observer') NOT NULL DEFAULT 'player',
  `joined_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_campaign_user` (`campaign_id`, `user_id`),
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. FICHAS DE PERSONAGENS (TORMENTA20 / D&D D20 SYSTEM)
CREATE TABLE `characters` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `class_and_level` VARCHAR(100) NOT NULL DEFAULT 'Guerreiro 1',
  `race_origin` VARCHAR(100) NOT NULL DEFAULT 'Humano',
  `hp_current` INT NOT NULL DEFAULT 20,
  `hp_max` INT NOT NULL DEFAULT 20,
  `mp_current` INT NOT NULL DEFAULT 6,
  `mp_max` INT NOT NULL DEFAULT 6,
  `armor_class` INT NOT NULL DEFAULT 15,
  `attributes_data` JSON NOT NULL,
  `skills_data` JSON NULL,
  `inventory_data` JSON NULL,
  `spells_data` JSON NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_characters_campaign` (`campaign_id`),
  INDEX `idx_characters_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. HISTÓRICO DE ROLAGENS DE DADOS
CREATE TABLE `dice_rolls` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `character_id` BIGINT UNSIGNED NULL,
  `roller_name` VARCHAR(150) NOT NULL,
  `formula` VARCHAR(50) NOT NULL,
  `label` VARCHAR(150) NOT NULL DEFAULT 'Rolagem',
  `dice_breakdown` JSON NOT NULL,
  `modifier` INT NOT NULL DEFAULT 0,
  `total_result` INT NOT NULL,
  `is_critical` TINYINT(1) NOT NULL DEFAULT 0,
  `is_fumble` TINYINT(1) NOT NULL DEFAULT 0,
  `is_secret` TINYINT(1) NOT NULL DEFAULT 0,
  `rolled_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_dice_campaign_rolled` (`campaign_id`, `rolled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BIBLIOTECA DE ÁUDIO & EFEITOS
CREATE TABLE `audio_tracks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `category` ENUM('tavern', 'dungeon', 'combat', 'travel', 'sfx') NOT NULL DEFAULT 'tavern',
  `file_url` VARCHAR(500) NOT NULL,
  `duration_seconds` INT NOT NULL DEFAULT 0,
  `is_preset` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. ESTADO DE SINCRONIZAÇÃO DE ÁUDIO POR CAMPANHA
CREATE TABLE `audio_sync_states` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `active_track_id` BIGINT UNSIGNED NULL,
  `active_layer` VARCHAR(50) NOT NULL DEFAULT 'music',
  `playback_state` ENUM('playing', 'paused', 'stopped') NOT NULL DEFAULT 'stopped',
  `server_timestamp` BIGINT NOT NULL DEFAULT 0,
  `start_offset_ms` INT NOT NULL DEFAULT 0,
  `last_sfx_name` VARCHAR(100) NULL,
  `last_sfx_timestamp` BIGINT NOT NULL DEFAULT 0,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. CRÔNICAS & DIÁRIO DA GUILDA
CREATE TABLE `chronicles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `author_user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(250) NOT NULL,
  `content_markdown` TEXT NOT NULL,
  `session_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`author_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. HANDOUTS & PISTAS REVELADAS
CREATE TABLE `handouts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `image_url` VARCHAR(500) NULL,
  `is_revealed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. REVIRAVOLTAS DA TRAMA (LIVE PLOT TWISTS)
CREATE TABLE `plot_twists` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `twist_type` VARCHAR(50) NOT NULL DEFAULT 'traicao',
  `headline` VARCHAR(250) NOT NULL,
  `description` TEXT NOT NULL,
  `audio_sfx` VARCHAR(100) NOT NULL DEFAULT 'thunder',
  `triggered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. FILA DE EVENTOS EM TEMPO REAL (PARA SSE STREAM)
CREATE TABLE `campaign_events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `payload` JSON NOT NULL,
  `created_at_micro` DECIMAL(16,4) NOT NULL,
  INDEX `idx_events_stream` (`campaign_id`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
