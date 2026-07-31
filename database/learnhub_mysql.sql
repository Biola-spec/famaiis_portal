-- LearnHub Platform Tables for phpMyAdmin / MySQL
-- Import this file into your SMS database (e.g. via phpMyAdmin > Import)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `learnhub_cbt_attempts`;
DROP TABLE IF EXISTS `learnhub_student_progress`;
DROP TABLE IF EXISTS `learnhub_cbt_questions`;
DROP TABLE IF EXISTS `learnhub_lessons`;
DROP TABLE IF EXISTS `learnhub_weeks`;
DROP TABLE IF EXISTS `learnhub_subjects`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `learnhub_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `total_weeks` int(10) UNSIGNED NOT NULL DEFAULT 12,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `learnhub_subjects_teacher_id_index` (`teacher_id`),
  CONSTRAINT `learnhub_subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `learnhub_weeks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `week_number` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learnhub_weeks_subject_id_week_number_unique` (`subject_id`,`week_number`),
  KEY `learnhub_weeks_subject_id_index` (`subject_id`),
  CONSTRAINT `learnhub_weeks_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `learnhub_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `learnhub_lessons` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `week_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `learnhub_lessons_week_id_unique` (`week_id`),
  KEY `learnhub_lessons_week_id_index` (`week_id`),
  CONSTRAINT `learnhub_lessons_week_id_foreign` FOREIGN KEY (`week_id`) REFERENCES `learnhub_weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `learnhub_cbt_questions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `question_number` int(10) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_answer` enum('A','B','C','D') NOT NULL,
  `explanation` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `learnhub_cbt_questions_lesson_id_index` (`lesson_id`),
  CONSTRAINT `learnhub_cbt_questions_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `learnhub_lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `learnhub_student_progress` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `learnhub_student_progress_student_id_lesson_id_unique` (`student_id`,`lesson_id`),
  KEY `learnhub_student_progress_student_id_index` (`student_id`),
  KEY `learnhub_student_progress_lesson_id_index` (`lesson_id`),
  CONSTRAINT `learnhub_student_progress_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `learnhub_lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `learnhub_student_progress_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `learnhub_cbt_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `answers` json NOT NULL,
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_questions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `learnhub_cbt_attempts_student_id_index` (`student_id`),
  KEY `learnhub_cbt_attempts_lesson_id_index` (`lesson_id`),
  CONSTRAINT `learnhub_cbt_attempts_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `learnhub_lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `learnhub_cbt_attempts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
