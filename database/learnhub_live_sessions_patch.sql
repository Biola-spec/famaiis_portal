-- Run this in phpMyAdmin if php artisan migrate is not used
-- Adds live video sessions + quiz game stats to FamaiisStudyHub

CREATE TABLE IF NOT EXISTS `learnhub_live_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `status` enum('scheduled','live','ended') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learnhub_live_sessions_room_name_unique` (`room_name`),
  KEY `learnhub_live_sessions_subject_id_status_index` (`subject_id`,`status`),
  CONSTRAINT `learnhub_live_sessions_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `learnhub_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `learnhub_live_sessions_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `learnhub_lessons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `learnhub_live_sessions_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `learnhub_cbt_attempts`
  ADD COLUMN IF NOT EXISTS `game_points` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `passed`,
  ADD COLUMN IF NOT EXISTS `max_streak` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `game_points`,
  ADD COLUMN IF NOT EXISTS `time_seconds` int(10) UNSIGNED DEFAULT NULL AFTER `max_streak`;
