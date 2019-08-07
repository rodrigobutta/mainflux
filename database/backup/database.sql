-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.21 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5289
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table mainflux.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `login_as_user_id` int(10) unsigned DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_module_id` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_login_as_user_id_foreign` (`login_as_user_id`),
  CONSTRAINT `activity_logs_login_as_user_id_foreign` FOREIGN KEY (`login_as_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.activity_logs: ~89 rows (approximately)
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
REPLACE INTO `activity_logs` (`id`, `user_id`, `login_as_user_id`, `user_agent`, `module`, `module_id`, `sub_module`, `sub_module_id`, `activity`, `ip`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-08 19:08:46', '2019-07-08 19:08:46'),
	(2, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task_category', '1', NULL, NULL, 'added', '::1', '2019-07-08 19:56:00', '2019-07-08 19:56:00'),
	(3, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task_category', '2', NULL, NULL, 'added', '::1', '2019-07-08 19:56:09', '2019-07-08 19:56:09'),
	(4, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task_category', '3', NULL, NULL, 'added', '::1', '2019-07-08 19:56:21', '2019-07-08 19:56:21'),
	(5, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task_priority', '1', NULL, NULL, 'added', '::1', '2019-07-08 19:56:37', '2019-07-08 19:56:37'),
	(6, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task_priority', '2', NULL, NULL, 'added', '::1', '2019-07-08 19:56:46', '2019-07-08 19:56:46'),
	(7, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'task', NULL, 'saved', '::1', '2019-07-08 19:57:16', '2019-07-08 19:57:16'),
	(18, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'system', NULL, 'saved', '::1', '2019-07-09 11:22:45', '2019-07-09 11:22:45'),
	(19, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'authentication', NULL, 'saved', '::1', '2019-07-09 12:34:19', '2019-07-09 12:34:19'),
	(20, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'company_logo', NULL, 'uploaded', '::1', '2019-07-09 12:55:59', '2019-07-09 12:55:59'),
	(21, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'company_logo', NULL, 'uploaded', '::1', '2019-07-09 12:58:19', '2019-07-09 12:58:19'),
	(22, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'company_logo', NULL, 'removed', '::1', '2019-07-09 12:58:27', '2019-07-09 12:58:27'),
	(23, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'company_logo', NULL, 'uploaded', '::1', '2019-07-09 12:58:44', '2019-07-09 12:58:44'),
	(24, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'company_logo', NULL, 'uploaded', '::1', '2019-07-09 12:59:05', '2019-07-09 12:59:05'),
	(25, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'company_logo', NULL, 'uploaded', '::1', '2019-07-09 12:59:54', '2019-07-09 12:59:54'),
	(26, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'basic', NULL, 'saved', '::1', '2019-07-09 13:00:32', '2019-07-09 13:00:32'),
	(27, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'mail', NULL, 'saved', '::1', '2019-07-09 13:01:07', '2019-07-09 13:01:07'),
	(28, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'mail', NULL, 'saved', '::1', '2019-07-09 13:02:23', '2019-07-09 13:02:23'),
	(29, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'mail', NULL, 'saved', '::1', '2019-07-09 13:04:45', '2019-07-09 13:04:45'),
	(30, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'mail', NULL, 'saved', '::1', '2019-07-09 13:04:51', '2019-07-09 13:04:51'),
	(31, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'system', NULL, 'saved', '::1', '2019-07-09 13:05:46', '2019-07-09 13:05:46'),
	(32, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'sms', NULL, 'saved', '::1', '2019-07-09 13:10:27', '2019-07-09 13:10:27'),
	(33, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-09 16:09:39', '2019-07-09 16:09:39'),
	(34, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'location', '2', NULL, NULL, 'added', '::1', '2019-07-09 16:10:23', '2019-07-09 16:10:23'),
	(35, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'location', '3', NULL, NULL, 'added', '::1', '2019-07-09 16:10:35', '2019-07-09 16:10:35'),
	(36, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'location', '4', NULL, NULL, 'added', '::1', '2019-07-09 16:11:41', '2019-07-09 16:11:41'),
	(37, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'location', '5', NULL, NULL, 'added', '::1', '2019-07-09 16:11:47', '2019-07-09 16:11:47'),
	(38, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'location', '6', NULL, NULL, 'added', '::1', '2019-07-09 16:11:54', '2019-07-09 16:11:54'),
	(39, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'system', NULL, 'saved', '::1', '2019-07-09 16:13:12', '2019-07-09 16:13:12'),
	(40, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', 'avatar', NULL, 'uploaded', '::1', '2019-07-09 16:13:33', '2019-07-09 16:13:33'),
	(41, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', 'avatar', NULL, 'uploaded', '::1', '2019-07-09 16:13:44', '2019-07-09 16:13:44'),
	(42, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'updated', '::1', '2019-07-09 16:16:05', '2019-07-09 16:16:05'),
	(43, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'designation', '2', NULL, NULL, 'added', '::1', '2019-07-09 16:21:29', '2019-07-09 16:21:29'),
	(44, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '2', NULL, NULL, 'created', '::1', '2019-07-09 16:22:32', '2019-07-09 16:22:32'),
	(45, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'added', '::1', '2019-07-09 16:24:40', '2019-07-09 16:24:40'),
	(46, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'question_set', '1', NULL, NULL, 'added', '::1', '2019-07-09 16:25:46', '2019-07-09 16:25:46'),
	(47, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'question_set', '2', NULL, NULL, 'added', '::1', '2019-07-09 16:26:44', '2019-07-09 16:26:44'),
	(48, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'question_set', '2', NULL, NULL, 'deleted', '::1', '2019-07-09 16:27:05', '2019-07-09 16:27:05'),
	(49, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'question_set', '3', NULL, NULL, 'added', '::1', '2019-07-09 16:27:21', '2019-07-09 16:27:21'),
	(50, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-09 16:27:31', '2019-07-09 16:27:31'),
	(51, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-09 16:28:23', '2019-07-09 16:28:23'),
	(52, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task_comment', '1', NULL, NULL, 'commented', '::1', '2019-07-09 16:29:21', '2019-07-09 16:29:21'),
	(53, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', 'progress', NULL, 'updated', '::1', '2019-07-09 16:29:35', '2019-07-09 16:29:35'),
	(54, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-09 16:29:47', '2019-07-09 16:29:47'),
	(55, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', 'answer', NULL, 'updated', '::1', '2019-07-09 16:30:03', '2019-07-09 16:30:03'),
	(56, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', 'answer', NULL, 'updated', '::1', '2019-07-09 16:30:20', '2019-07-09 16:30:20'),
	(57, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'backup', '1', NULL, NULL, 'generated', '::1', '2019-07-09 16:32:47', '2019-07-09 16:32:47'),
	(58, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-09 19:15:53', '2019-07-09 19:15:53'),
	(59, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'permission', NULL, NULL, NULL, 'assigned', '::1', '2019-07-09 19:17:42', '2019-07-09 19:17:42'),
	(60, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '3', NULL, NULL, 'logged_in', '::1', '2019-07-09 19:17:52', '2019-07-09 19:17:52'),
	(61, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '3', NULL, NULL, 'logged_out', '::1', '2019-07-09 19:18:24', '2019-07-09 19:18:24'),
	(62, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-09 19:18:33', '2019-07-09 19:18:33'),
	(63, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '3', NULL, NULL, 'logged_in', '::1', '2019-07-09 19:19:28', '2019-07-09 19:19:28'),
	(64, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'department', '2', NULL, NULL, 'added', '::1', '2019-07-09 20:24:15', '2019-07-09 20:24:15'),
	(65, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'department', '3', NULL, NULL, 'added', '::1', '2019-07-09 20:24:21', '2019-07-09 20:24:21'),
	(66, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'department', '1', NULL, NULL, 'updated', '::1', '2019-07-09 20:24:28', '2019-07-09 20:24:28'),
	(67, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-10 11:18:09', '2019-07-10 11:18:09'),
	(68, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-10 11:53:09', '2019-07-10 11:53:09'),
	(69, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'todo', '1', NULL, NULL, 'added', '::1', '2019-07-10 13:15:14', '2019-07-10 13:15:14'),
	(70, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'todo', '2', NULL, NULL, 'added', '::1', '2019-07-10 13:15:30', '2019-07-10 13:15:30'),
	(71, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-10 14:36:03', '2019-07-10 14:36:03'),
	(72, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-10 16:39:09', '2019-07-10 16:39:09'),
	(73, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'todo', '2', NULL, NULL, 'updated', '::1', '2019-07-10 16:45:58', '2019-07-10 16:45:58'),
	(74, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'todo', '2', NULL, NULL, 'updated', '::1', '2019-07-10 16:46:07', '2019-07-10 16:46:07'),
	(75, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'todo', '1', NULL, NULL, 'updated', '::1', '2019-07-10 16:46:08', '2019-07-10 16:46:08'),
	(76, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-11 01:54:36', '2019-07-11 01:54:36'),
	(77, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-11 10:54:27', '2019-07-11 10:54:27'),
	(78, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-12 11:01:01', '2019-07-12 11:01:01'),
	(79, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'configuration', NULL, 'authentication', NULL, 'saved', '::1', '2019-07-12 11:03:02', '2019-07-12 11:03:02'),
	(80, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_out', '::1', '2019-07-12 11:03:08', '2019-07-12 11:03:08'),
	(81, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-12 15:06:47', '2019-07-12 15:06:47'),
	(82, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-12 15:19:14', '2019-07-12 15:19:14'),
	(83, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-12 15:55:40', '2019-07-12 15:55:40'),
	(84, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-12 16:21:27', '2019-07-12 16:21:27'),
	(85, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-12 17:45:02', '2019-07-12 17:45:02'),
	(86, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-12 18:05:12', '2019-07-12 18:05:12'),
	(87, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-13 12:38:43', '2019-07-13 12:38:43'),
	(88, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-13 13:32:05', '2019-07-13 13:32:05'),
	(89, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-13 13:42:08', '2019-07-13 13:42:08'),
	(90, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-13 14:20:08', '2019-07-13 14:20:08'),
	(91, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_out', '::1', '2019-07-13 15:27:46', '2019-07-13 15:27:46'),
	(92, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-13 17:06:27', '2019-07-13 17:06:27'),
	(93, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-13 19:50:36', '2019-07-13 19:50:36'),
	(94, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-13 20:32:52', '2019-07-13 20:32:52'),
	(95, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '1', NULL, NULL, 'sent', '::1', '2019-07-13 20:54:47', '2019-07-13 20:54:47'),
	(96, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '3', NULL, NULL, 'logged_in', '::1', '2019-07-13 20:55:07', '2019-07-13 20:55:07'),
	(97, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '2', NULL, NULL, 'replied', '::1', '2019-07-13 20:55:31', '2019-07-13 20:55:31'),
	(98, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-13 22:43:27', '2019-07-13 22:43:27'),
	(99, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '4', NULL, NULL, 'sent', '::1', '2019-07-13 22:45:34', '2019-07-13 22:45:34'),
	(100, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-13 23:12:30', '2019-07-13 23:12:30'),
	(101, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-14 01:55:55', '2019-07-14 01:55:55'),
	(102, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '5', NULL, NULL, 'replied', '::1', '2019-07-14 02:35:26', '2019-07-14 02:35:26'),
	(103, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '6', NULL, NULL, 'replied', '::1', '2019-07-14 02:35:49', '2019-07-14 02:35:49'),
	(104, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'user', '3', NULL, NULL, 'logged_in', '::1', '2019-07-14 02:52:14', '2019-07-14 02:52:14'),
	(105, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '7', NULL, NULL, 'replied', '::1', '2019-07-14 02:52:36', '2019-07-14 02:52:36'),
	(106, 3, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36', 'message', '8', NULL, NULL, 'sent', '::1', '2019-07-14 03:06:08', '2019-07-14 03:06:08'),
	(107, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-21 20:13:54', '2019-07-21 20:13:54'),
	(108, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-21 20:19:54', '2019-07-21 20:19:54'),
	(109, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-22 14:37:55', '2019-07-22 14:37:55'),
	(110, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'announcement', '1', NULL, NULL, 'added', '::1', '2019-07-22 15:08:43', '2019-07-22 15:08:43'),
	(111, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'location', '2', NULL, NULL, 'updated', '::1', '2019-07-22 15:30:48', '2019-07-22 15:30:48'),
	(112, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-07-22 16:39:04', '2019-07-22 16:39:04'),
	(113, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'task', '1', NULL, NULL, 'updated', '::1', '2019-07-23 16:08:10', '2019-07-23 16:08:10'),
	(114, 1, NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36', 'user', '1', NULL, NULL, 'logged_in', '::1', '2019-08-05 11:57:29', '2019-08-05 11:57:29');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;

-- Dumping structure for table mainflux.announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `restricted_to` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `upload_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_user_id_foreign` (`user_id`),
  CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.announcements: ~0 rows (approximately)
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
REPLACE INTO `announcements` (`id`, `uuid`, `user_id`, `title`, `is_public`, `restricted_to`, `description`, `start_date`, `end_date`, `upload_token`, `created_at`, `updated_at`) VALUES
	(1, '9fab1818-0e09-43d9-91ec-6cc491dd3aea', 1, 'Anuncio', 1, NULL, NULL, '2019-07-01', '2019-07-31', '593770c9-f082-403d-bb02-e21d07cffa90', '2019-07-22 15:08:43', '2019-07-22 15:08:43');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;

-- Dumping structure for table mainflux.announcement_designation
CREATE TABLE IF NOT EXISTS `announcement_designation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` int(10) unsigned NOT NULL,
  `designation_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `announcement_designation_announcement_id_foreign` (`announcement_id`),
  KEY `announcement_designation_designation_id_foreign` (`designation_id`),
  CONSTRAINT `announcement_designation_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcement_designation_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.announcement_designation: ~0 rows (approximately)
/*!40000 ALTER TABLE `announcement_designation` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcement_designation` ENABLE KEYS */;

-- Dumping structure for table mainflux.announcement_location
CREATE TABLE IF NOT EXISTS `announcement_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `announcement_location_announcement_id_foreign` (`announcement_id`),
  KEY `announcement_location_location_id_foreign` (`location_id`),
  CONSTRAINT `announcement_location_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcement_location_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.announcement_location: ~0 rows (approximately)
/*!40000 ALTER TABLE `announcement_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcement_location` ENABLE KEYS */;

-- Dumping structure for table mainflux.announcement_user
CREATE TABLE IF NOT EXISTS `announcement_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `announcement_user_announcement_id_foreign` (`announcement_id`),
  KEY `announcement_user_user_id_foreign` (`user_id`),
  CONSTRAINT `announcement_user_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcement_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.announcement_user: ~0 rows (approximately)
/*!40000 ALTER TABLE `announcement_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcement_user` ENABLE KEYS */;

-- Dumping structure for table mainflux.answers
CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned DEFAULT NULL,
  `question_id` int(10) unsigned DEFAULT NULL,
  `answer` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `answers_task_id_foreign` (`task_id`),
  KEY `answers_question_id_foreign` (`question_id`),
  CONSTRAINT `answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `answers_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.answers: ~2 rows (approximately)
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
REPLACE INTO `answers` (`id`, `task_id`, `question_id`, `answer`, `description`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 'Ya hice el paso 1 1', '2019-07-09 16:30:03', '2019-07-09 16:30:03'),
	(2, 1, 2, 0, '', '2019-07-09 16:30:03', '2019-07-09 16:30:03'),
	(3, 1, 3, 0, '', '2019-07-09 16:30:03', '2019-07-09 16:30:03');
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;

-- Dumping structure for table mainflux.backups
CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `backups_user_id_foreign` (`user_id`),
  CONSTRAINT `backups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.backups: ~0 rows (approximately)
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
REPLACE INTO `backups` (`id`, `user_id`, `file`, `created_at`, `updated_at`) VALUES
	(1, 1, 'backup_2019_07_09_16_32_47.sql.gz', '2019-07-09 16:32:47', '2019-07-09 16:32:47');
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;

-- Dumping structure for table mainflux.config
CREATE TABLE IF NOT EXISTS `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeric_value` bigint(20) DEFAULT NULL,
  `text_value` text COLLATE utf8mb4_unicode_ci,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.config: ~94 rows (approximately)
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
REPLACE INTO `config` (`id`, `name`, `numeric_value`, `text_value`, `is_private`) VALUES
	(1, 'color_theme', NULL, 'blue', 0),
	(2, 'direction', NULL, 'ltr', 0),
	(3, 'locale', NULL, 'en', 0),
	(4, 'timezone', NULL, 'America/Argentina/Buenos_Aires', 0),
	(5, 'notification_position', NULL, 'toast-bottom-right', 0),
	(6, 'date_format', NULL, 'DD-MM-YYYY', 0),
	(7, 'time_format', NULL, 'H:mm', 0),
	(8, 'page_length', 10, NULL, 0),
	(9, 'driver', NULL, 'smtp', 0),
	(10, 'from_address', NULL, 'info@mainflux.com', 0),
	(11, 'from_name', NULL, 'Info', 0),
	(12, 'token_lifetime', 120, NULL, 0),
	(13, 'reset_password_token_lifetime', 30, NULL, 0),
	(14, 'activity_log', 1, NULL, 0),
	(15, 'email_log', 1, NULL, 0),
	(16, 'reset_password', 1, NULL, 0),
	(17, 'registration', 1, NULL, 0),
	(18, 'mode', 1, NULL, 0),
	(19, 'footer_credit', NULL, 'Copyright 2019. BETA', 0),
	(20, 'multilingual', 1, NULL, 0),
	(21, 'announcement', 1, NULL, 0),
	(22, 'ip_filter', 1, NULL, 0),
	(23, 'email_template', 1, NULL, 0),
	(24, 'message', 1, NULL, 0),
	(25, 'backup', 1, NULL, 0),
	(26, 'todo', 1, NULL, 0),
	(27, 'task_progress_type', NULL, 'manual', 0),
	(28, 'task_rating_type', NULL, 'task_based', 0),
	(29, 'task_number_prefix', NULL, 'TSK', 0),
	(30, 'task_number_digit', 5, NULL, 0),
	(31, 'designation_subordinate_level', 1, NULL, 0),
	(32, 'location_subordinate_level', 1, NULL, 0),
	(33, 'show_user_menu', 1, NULL, 0),
	(34, 'show_todo_menu', 1, NULL, 0),
	(35, 'show_message_menu', 1, NULL, 0),
	(36, 'show_configuration_menu', 1, NULL, 0),
	(37, 'show_backup_menu', 1, NULL, 0),
	(38, 'show_email_template_menu', 1, NULL, 0),
	(39, 'show_email_log_menu', 1, NULL, 0),
	(40, 'show_activity_log_menu', 1, NULL, 0),
	(41, 'show_department_menu', 1, NULL, 0),
	(42, 'show_designation_menu', 1, NULL, 0),
	(43, 'show_location_menu', 1, NULL, 0),
	(44, 'show_announcement_menu', 1, NULL, 0),
	(45, 'show_task_menu', 1, NULL, 0),
	(46, 'show_about_menu', 1, NULL, 0),
	(47, 'show_support_menu', 1, NULL, 0),
	(48, 'https', 0, NULL, 0),
	(49, 'error_display', 1, NULL, 0),
	(50, 'maintenance_mode', 0, NULL, 0),
	(51, 'maintenance_mode_message', NULL, '', 0),
	(52, 'facebook_login', 1, NULL, 0),
	(53, 'facebook_client', NULL, '644213442759498', 1),
	(54, 'facebook_secret', NULL, '11ec9ff6b773a7291399c33714c7560f', 1),
	(55, 'facebook_redirect_url', NULL, 'http://local.mainflux.com/auth/facebook/callback', 0),
	(56, 'twitter_login', 0, NULL, 0),
	(57, 'github_login', 0, NULL, 0),
	(58, 'email_verification', 0, NULL, 0),
	(59, 'account_approval', 0, NULL, 0),
	(60, 'terms_and_conditions', 0, NULL, 0),
	(61, 'two_factor_security', 0, NULL, 0),
	(62, 'recaptcha', 0, NULL, 0),
	(63, 'recaptcha_key', NULL, '', 0),
	(64, 'recaptcha_secret', NULL, '', 0),
	(65, 'login_recaptcha', 0, NULL, 0),
	(66, 'reset_password_recaptcha', 0, NULL, 0),
	(67, 'register_recaptcha', 0, NULL, 0),
	(68, 'social_login', 1, NULL, 0),
	(69, 'lock_screen', 0, NULL, 0),
	(70, 'lock_screen_timeout', NULL, '', 0),
	(71, 'login_throttle', 0, NULL, 0),
	(72, 'login_throttle_attempt', NULL, '', 0),
	(73, 'login_throttle_timeout', NULL, '', 0),
	(74, 'sidebar_logo', NULL, 'uploads/logo/5d24b9c9421b0.png', 0),
	(75, 'main_logo', NULL, 'uploads/logo/5d24b9fa09ff9.png', 0),
	(76, 'company_name', NULL, 'Mainflux', 0),
	(77, 'contact_person', NULL, 'Rodrigo Butta', 0),
	(78, 'address_line_1', NULL, 'Humboldt 895', 0),
	(79, 'address_line_2', NULL, '2 C', 0),
	(80, 'city', NULL, 'Buenos Aires', 0),
	(81, 'state', NULL, 'Capital Federal', 0),
	(82, 'zipcode', 1414, NULL, 0),
	(83, 'country', NULL, 'Argentina', 0),
	(84, 'phone', NULL, '', 0),
	(85, 'fax', NULL, '', 0),
	(86, 'email', NULL, 'rbutta@gmail.com', 0),
	(87, 'website', NULL, '', 0),
	(88, 'smtp_host', NULL, 'smtp.mailtrap.io', 0),
	(89, 'smtp_port', 2525, NULL, 0),
	(90, 'smtp_username', NULL, '58a9187b5e005b', 1),
	(91, 'smtp_password', NULL, 'd2ca064aa5eb5f', 1),
	(92, 'smtp_encryption', NULL, 'tls', 0),
	(93, 'mailgun_host', NULL, '', 0),
	(94, 'mailgun_port', NULL, '', 0),
	(95, 'mailgun_username', NULL, '', 1),
	(96, 'mailgun_password', NULL, '', 1),
	(97, 'mailgun_encryption', NULL, '', 0),
	(98, 'mailgun_domain', NULL, '', 0),
	(99, 'mailgun_secret', NULL, '', 1),
	(100, 'mandrill_secret', NULL, '', 1),
	(101, 'nexmo_api_key', NULL, 'b4a8aa1d', 1),
	(102, 'nexmo_api_secret', NULL, '7Qe5YMYHvrgE7mBc', 1),
	(103, 'nexmo_sender_mobile', 12345678901, NULL, 0),
	(104, 'nexmo_receiver_mobile', 5491141722423, NULL, 0),
	(105, 'google_login', 1, NULL, 0),
	(106, 'google_client', NULL, '729728661582-9e1rd959g1caskgtg1d0h8mr0062uv35.apps.googleusercontent.com', 0),
	(107, 'google_secret', NULL, 'HaCz1LiUnhhq52s_CdPSnZni', 0),
	(108, 'google_redirect_url', NULL, 'http://local.mainflux.com/auth/google/callback', 0);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;

-- Dumping structure for table mainflux.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.departments: ~2 rows (approximately)
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
REPLACE INTO `departments` (`id`, `name`, `description`, `is_hidden`, `created_at`, `updated_at`) VALUES
	(1, 'Departamento 1', '', 1, '2019-07-08 12:49:44', '2019-07-09 20:24:28'),
	(2, 'Departamento 2', '', 0, '2019-07-09 20:24:15', '2019-07-09 20:24:15'),
	(3, 'Departamento 3', '', 0, '2019-07-09 20:24:21', '2019-07-09 20:24:21');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;

-- Dumping structure for table mainflux.designations
CREATE TABLE IF NOT EXISTS `designations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(10) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `top_designation_id` int(10) unsigned DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designations_department_id_foreign` (`department_id`),
  KEY `designations_top_designation_id_foreign` (`top_designation_id`),
  CONSTRAINT `designations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `designations_top_designation_id_foreign` FOREIGN KEY (`top_designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.designations: ~2 rows (approximately)
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
REPLACE INTO `designations` (`id`, `department_id`, `name`, `description`, `top_designation_id`, `is_hidden`, `is_default`, `created_at`, `updated_at`) VALUES
	(1, 1, 'System Administrator', NULL, NULL, 1, 0, '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(2, 1, 'Contratista', '', 1, 0, 0, '2019-07-09 16:21:29', '2019-07-09 16:21:29');
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;

-- Dumping structure for table mainflux.email_logs
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.email_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;

-- Dumping structure for table mainflux.email_templates
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `body` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.email_templates: ~8 rows (approximately)
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
REPLACE INTO `email_templates` (`id`, `is_default`, `name`, `slug`, `category`, `subject`, `body`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Welcome Email User', 'welcome-email-user', 'user', 'Welcome Email User | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Account Created</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">Welcome to our company. Your account has been created. Please use below credentials to log into your account:</p><table class="table table-bordered"><tbody><tr><td>Email</td><td>[EMAIL]</td></tr><tr><td>Password</td><td>[PASSWORD]</td></tr></tbody></table><p style="margin-top:0px; color:#bbbbbb;"><br></p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(2, 1, 'Anniversary Email User', 'anniversary-email-user', 'user', 'Wish You a Very Happy Anniversary [NAME] | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Happy Anniversary</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">We wish you a Very Happy Anniversary.</p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(3, 1, 'Birthday Email User', 'birthday-email-user', 'user', 'Happy Birthday [NAME] | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Happy Birthday</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">We wish you a Very Happy Birthday.</p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(4, 1, 'Task Assign Email', 'task-assign-email', 'task', 'Task #[TASK_NUMBER] - [TASK_TITLE] Assigned | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Task #[TASK_NUMBER] - [TASK_TITLE] Assigned</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">You have been assigned with a task <b><u>[TASK_TITLE]</u></b> with start date [TASK_START_DATE] and due date [TASK_DUE_DATE] by [TASK_OWNER_NAME]. Please visit the task at [TASK_URL].</p>\r\n                <p>[TASK_OWNER_NAME]<br />[TASK_OWNER_EMAIL]</p>\r\n              </td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(5, 1, 'Task Sign Off Request', 'task-sign-off-request', 'task', 'Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Request Received | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Request Received</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [TASK_OWNER_NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">Task Sign Off request is received for your task [TASK_TITLE] on [CURRENT_DATE]. Please review the sign off request at [TASK_URL].</p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(6, 1, 'Task Sign Off Approve', 'task-sign-off-approve', 'task', 'Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Approved | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Approved</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">Task sign off is approved for your task [TASK_TITLE] on [CURRENT_DATE]. Please visit the task at [TASK_URL].</p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(7, 1, 'Task Sign Off Reject', 'task-sign-off-reject', 'task', 'Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Rejected | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Rejected</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">Task sign off is rejected for your task [TASK_TITLE] on [CURRENT_DATE]. Please review the task at [TASK_URL].</p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL),
	(8, 1, 'Task Sign Off Request Cancel', 'task-sign-off-request-cancel', 'task', 'Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Request Cancelled | [COMPANY_NAME]', '<div style="margin:0px; background: #f8f8f8; ">\r\n  <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">\r\n    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">\r\n      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">\r\n        <tbody>\r\n          <tr>\r\n            <td style="vertical-align: top; padding-bottom:30px;" align="center">[COMPANY_LOGO]</td>\r\n          </tr>\r\n          <tr>\r\n            <td><h5 style="text-align:center;">Task #[TASK_NUMBER] - [TASK_TITLE] Sign Off Cancelled</h5></td>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div style="padding: 40px; background: #fff;">\r\n        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">\r\n          <tbody>\r\n            <tr>\r\n              <td style="border-bottom:1px solid #f6f6f6;"><h1 style="font-size:14px; font-family:arial; margin:0px; font-weight:bold;">Dear [TASK_OWNER_NAME],</h1>\r\n                <p style="margin-top:0px; color:#bbbbbb;">Task Sign Off request is cancelled for your task [TASK_TITLE] on [CURRENT_DATE]. Please visit the task at [TASK_URL].</p></td>\r\n            </tr>\r\n            <tr>\r\n              <td style="padding:10px 0 30px 0;"><p>Have a Good Day!</p>\r\n                <b>- Best Wishes ([COMPANY_NAME])</b> </td>\r\n            </tr>\r\n          </tbody>\r\n        </table>\r\n      </div>\r\n      <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">\r\n        <p> [COMPANY_NAME] <br>\r\n        [COMPANY_EMAIL] | [COMPANY_PHONE] | [COMPANY_WEBSITE]</p>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n', NULL, NULL);
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;

-- Dumping structure for table mainflux.ip_filters
CREATE TABLE IF NOT EXISTS `ip_filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.ip_filters: ~0 rows (approximately)
/*!40000 ALTER TABLE `ip_filters` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_filters` ENABLE KEYS */;

-- Dumping structure for table mainflux.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

-- Dumping structure for table mainflux.locales
CREATE TABLE IF NOT EXISTS `locales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.locales: ~0 rows (approximately)
/*!40000 ALTER TABLE `locales` DISABLE KEYS */;
REPLACE INTO `locales` (`id`, `name`, `locale`, `created_at`, `updated_at`) VALUES
	(1, 'English', 'en', '2019-07-08 12:49:44', '2019-07-08 12:49:44');
/*!40000 ALTER TABLE `locales` ENABLE KEYS */;

-- Dumping structure for table mainflux.locations
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `top_location_id` int(10) unsigned DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `locations_top_location_id_foreign` (`top_location_id`),
  CONSTRAINT `locations_top_location_id_foreign` FOREIGN KEY (`top_location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.locations: ~6 rows (approximately)
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
REPLACE INTO `locations` (`id`, `name`, `description`, `top_location_id`, `is_default`, `created_at`, `updated_at`) VALUES
	(1, 'Head office', NULL, NULL, 0, '2019-07-08 12:49:44', '2019-07-22 15:30:48'),
	(2, 'Suboffice 1', '', 1, 1, '2019-07-09 16:10:23', '2019-07-22 15:30:48'),
	(3, 'Suboffice 2', '', 1, 0, '2019-07-09 16:10:35', '2019-07-22 15:30:48'),
	(4, 'Subsuboffice 2 A', '', 3, 0, '2019-07-09 16:11:41', '2019-07-22 15:30:48'),
	(5, 'Subsuboffice 2 B', '', 3, 0, '2019-07-09 16:11:47', '2019-07-22 15:30:48'),
	(6, 'Subsuboffice 2 C', '', 3, 0, '2019-07-09 16:11:54', '2019-07-22 15:30:48');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;

-- Dumping structure for table mainflux.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned DEFAULT NULL,
  `reply_id` int(10) unsigned DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `is_important_by_sender` tinyint(1) NOT NULL DEFAULT '0',
  `is_important_by_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `is_deleted_by_sender` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted_by_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `has_attachment` tinyint(1) NOT NULL DEFAULT '0',
  `upload_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_from_user_id_foreign` (`from_user_id`),
  KEY `messages_to_user_id_foreign` (`to_user_id`),
  KEY `messages_reply_id_foreign` (`reply_id`),
  CONSTRAINT `messages_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_reply_id_foreign` FOREIGN KEY (`reply_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.messages: ~8 rows (approximately)
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
REPLACE INTO `messages` (`id`, `uuid`, `is_draft`, `from_user_id`, `to_user_id`, `reply_id`, `subject`, `body`, `is_important_by_sender`, `is_important_by_receiver`, `is_read`, `read_at`, `is_deleted_by_sender`, `is_deleted_by_receiver`, `has_attachment`, `upload_token`, `created_at`, `updated_at`) VALUES
	(1, 'b59445d7-4560-4135-9c7a-1b2909e7d66f', 0, 1, 3, NULL, 'prueba mensaje 1 a test user 1', '<p>sa daskjd sañldjsa ñlsakj dklñasjd aklñdj asñlkj sa daskjd sañldjsa ñlsakj dklñasjd aklñdj asñlkj sa daskjd sañldjsa ñlsakj dklñasjd aklñdj asñlkj</p><p>sa daskjd sañldjsa ñlsakj dklñasjd aklñdj asñlkj</p><p>sa daskjd sañldjsa ñlsakj dklñasjd aklñdj asñlkj<br></p>', 0, 0, 1, '2019-07-14 02:54:44', 0, 0, 0, 'a9a18b31-b68b-4af5-a28a-f0c923d985e7', '2019-07-13 20:54:47', '2019-07-14 02:54:44'),
	(2, 'f5eb636b-906f-4231-9b5b-53f134841c67', 0, 3, 1, 1, 'Re: prueba mensaje 1 a test user 1', 'soy Test 1 y te estoy respondiendo Rodrigo', 0, 0, 1, '2019-07-14 03:24:18', 0, 0, 0, '1a413891-664d-435d-bfaa-5b3520cf21e3', '2019-07-13 20:55:31', '2019-07-14 03:24:18'),
	(3, 'f5eb636b-906f-4231-9b5b-53f134841c22', 0, 3, 1, NULL, 'prueba de mensaje 2', 'este es mansaje 22222', 0, 0, 0, NULL, 0, 0, 0, 'f5eb636b-906f-4231-9b5b-53f134841c11', '2019-07-13 22:42:36', '2019-07-13 22:42:37'),
	(4, '9fe4b6a1-3bf4-4657-92ba-97fe0520aaf2', 0, 3, 1, NULL, 'prueba mensaje 3333', '<p>ds sa sa jkasj kl;saj a;kldj askljas;lkd jaskl;dj askldj kasjd kasjdad</p><p>asd jaksd jaksld jaskl;d jas;lkd jaskl;d jaksl;d jaksl;d ksladj ;asjdka sjdkals;j dklas;d jaskdj ksal</p>', 0, 0, 1, '2019-07-14 03:23:11', 0, 0, 1, 'c390cd42-168a-41c6-a777-d5660cf65729', '2019-07-13 22:45:34', '2019-07-14 03:23:11'),
	(5, 'a58aeed2-0e63-42ff-800d-4ceb7f50b2ee', 0, 1, 3, 1, 'Re: prueba mensaje 1 a test user 1', 'mando otra resíesya para el mensaje 2', 0, 0, 0, NULL, 0, 0, 0, 'dec594dd-1a48-4dad-bdaf-dac69677b10d', '2019-07-14 02:35:26', '2019-07-14 02:35:26'),
	(6, 'a69e1774-5479-4467-8dc3-3aec1dacfb0a', 0, 1, 3, 1, 'Re: prueba mensaje 1 a test user 1', 'ah la otra respuesta era del usuario test 1 y no mia', 0, 0, 0, NULL, 0, 0, 1, '', '2019-07-14 02:35:49', '2019-07-14 02:35:49'),
	(7, '6bbb4e72-17b7-42f7-b88e-e8ebc6226195', 0, 3, 1, 1, 'Re: prueba mensaje 1 a test user 1', 'Yo TEST 1 le respondo a RODRIGO BUTTA&nbsp;', 0, 0, 1, '2019-07-14 03:24:18', 0, 0, 0, 'c796c54f-9380-4d2b-898b-cf4e1c456934', '2019-07-14 02:52:36', '2019-07-14 03:24:18'),
	(8, '7165307d-3fcc-4934-ba64-0bd8abf6ad65', 0, 3, 1, NULL, 'nuevo mensaje para interval', 'prueba mensaje para interval', 0, 0, 0, NULL, 0, 0, 0, '2989e86a-252e-46e2-b6c1-04a923fd1064', '2019-07-14 03:06:08', '2019-07-14 03:06:08');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;

-- Dumping structure for table mainflux.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.migrations: 39 rows
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2017_10_27_053833_create_permission_tables', 1),
	(4, '2017_10_27_074549_create_config_table', 1),
	(5, '2017_10_27_074635_create_locales_table', 1),
	(6, '2017_10_27_074811_create_backups_table', 1),
	(7, '2017_10_27_110555_create_todos_table', 1),
	(8, '2017_10_27_110722_create_activity_logs_table', 1),
	(9, '2017_10_27_114519_create_profiles_table', 1),
	(10, '2017_10_27_121507_create_email_logs_table', 1),
	(11, '2017_10_27_121726_create_ip_filters_table', 1),
	(12, '2017_10_27_122411_create_messages_table', 1),
	(13, '2017_10_27_122805_create_jobs_table', 1),
	(14, '2017_10_27_130426_create_user_preferences_table', 1),
	(15, '2017_11_03_035042_create_uploads_table', 1),
	(16, '2017_11_13_100342_create_email_templates_table', 1),
	(17, '2017_12_26_032513_create_departments_table', 1),
	(18, '2017_12_26_032521_create_designations_table', 1),
	(19, '2017_12_26_032527_create_locations_table', 1),
	(20, '2017_12_26_032537_create_task_categories_table', 1),
	(21, '2017_12_26_032544_create_task_priorities_table', 1),
	(22, '2017_12_26_032549_create_tasks_table', 1),
	(23, '2017_12_26_032557_create_sub_tasks_table', 1),
	(24, '2017_12_26_145154_create_task_user_table', 1),
	(25, '2017_12_26_145404_create_starred_tasks_table', 1),
	(26, '2017_12_27_064242_create_task_signoff_logs_table', 1),
	(27, '2017_12_27_095429_create_announcements_table', 1),
	(28, '2017_12_27_095510_create_announcement_designation_table', 1),
	(29, '2017_12_27_095511_create_announcement_location_table', 1),
	(30, '2017_12_27_095512_create_announcement_user_table', 1),
	(31, '2017_12_27_113713_create_task_comments_table', 1),
	(32, '2017_12_27_113726_create_task_notes_table', 1),
	(33, '2017_12_27_113742_create_task_attachments_table', 1),
	(34, '2017_12_27_170336_create_sub_task_ratings_table', 1),
	(35, '2018_01_24_065103_create_notifications_table', 1),
	(36, '2018_05_03_131216_create_question_sets_table', 1),
	(37, '2018_05_03_131226_create_answers_table', 1),
	(38, '2018_05_03_131257_create_questions_table', 1),
	(39, '2018_05_04_034437_create_relations', 1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table mainflux.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.model_has_permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;

-- Dumping structure for table mainflux.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` int(10) unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.model_has_roles: ~2 rows (approximately)
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
REPLACE INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\User', 1),
	(2, 'App\\User', 2),
	(2, 'App\\User', 3),
	(2, 'App\\User', 4);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;

-- Dumping structure for table mainflux.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.notifications: ~3 rows (approximately)
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
REPLACE INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('3d2f04ab-b474-4413-9931-4ffca46fd49c', 'App\\Notifications\\TaskAssignation', 'App\\User', 1, '{"unid":"8a6e3e53-b644-43d2-8d93-12c16c9b8d90","user":"USU: Rodrigo Butta","taskId":1,"link":"#customLink","linkTarget":"_self","text":"Asignacion de tarea Tarea 1 2"}', '2019-07-13 20:52:03', '2019-07-13 14:20:08', '2019-07-13 20:52:03'),
	('5f8991fc-1228-4f32-bab9-46a9f41720ea', 'App\\Notifications\\TaskAssignation', 'App\\User', 1, '{"unid":"bbdf835f-69f2-4563-ac48-dd91e26d783f","user":"USU: Rodrigo Butta","taskId":1,"link":"#customLink","linkTarget":"_self","text":"Asignacion de tarea Tarea 1 2"}', '2019-07-21 20:11:37', '2019-07-13 20:32:51', '2019-07-21 20:11:37'),
	('b490ba39-2711-4c0c-b17e-ac29ca79fce1', 'App\\Notifications\\TaskAssignation', 'App\\User', 1, '{"unid":"ec47cb8d-54c5-4aef-982c-6226c0c79480","user":"USU: Rodrigo Butta","taskId":1,"link":"#customLink","linkTarget":"_self","text":"Asignacion de tarea Tarea 1 2"}', NULL, '2019-07-23 16:08:10', '2019-07-23 16:08:10');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;

-- Dumping structure for table mainflux.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.password_resets: ~2 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
REPLACE INTO `password_resets` (`email`, `token`, `created_at`) VALUES
	('test1@gmail.com', 'd30a3d55-27de-402f-8d8d-bf8c7bc34141', '2019-07-09 19:14:42');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table mainflux.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.permissions: ~40 rows (approximately)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
REPLACE INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'access-configuration', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(2, 'list-user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(3, 'create-user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(4, 'edit-user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(5, 'delete-user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(6, 'force-reset-user-password', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(7, 'email-user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(8, 'change-status-user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(9, 'access-message', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(10, 'access-todo', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(11, 'enable-login', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(12, 'list-department', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(13, 'create-department', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(14, 'edit-department', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(15, 'delete-department', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(16, 'list-designation', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(17, 'create-designation', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(18, 'edit-designation', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(19, 'delete-designation', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(20, 'access-all-designation', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(21, 'access-subordinate-designation', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(22, 'list-location', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(23, 'create-location', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(24, 'edit-location', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(25, 'delete-location', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(26, 'access-all-location', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(27, 'access-subordinate-location', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(28, 'list-announcement', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(29, 'create-announcement', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(30, 'edit-announcement', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(31, 'delete-announcement', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(32, 'list-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(33, 'create-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(34, 'edit-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(35, 'delete-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(36, 'access-all-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(37, 'access-subordinate-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(38, 'create-sub-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(39, 'edit-sub-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44'),
	(40, 'delete-sub-task', 'web', '2019-07-08 12:49:44', '2019-07-08 12:49:44');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table mainflux.profiles
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `designation_id` int(10) unsigned DEFAULT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_unique_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_anniversary` date DEFAULT NULL,
  `address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_profile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_profile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_plus_profile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_profile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profiles_user_id_foreign` (`user_id`),
  KEY `profiles_designation_id_foreign` (`designation_id`),
  KEY `profiles_location_id_foreign` (`location_id`),
  CONSTRAINT `profiles_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profiles_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.profiles: ~3 rows (approximately)
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
REPLACE INTO `profiles` (`id`, `user_id`, `designation_id`, `location_id`, `first_name`, `last_name`, `provider`, `provider_unique_id`, `gender`, `avatar`, `phone`, `date_of_birth`, `date_of_anniversary`, `address_line_1`, `address_line_2`, `city`, `state`, `zipcode`, `country_id`, `facebook_profile`, `twitter_profile`, `google_plus_profile`, `linkedin_profile`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 'Rodrigo', 'Butta', NULL, NULL, 'male', 'uploads/avatar/5d24e767b11ae.jpg', '+5491141722423', '1983-12-08', NULL, 'Humboldt 895', '2do Piso C', 'Capital Federal', 'Buenos Aires', '1414', '11', NULL, NULL, NULL, NULL, '2019-07-08 12:49:45', '2019-07-09 16:16:05'),
	(2, 2, 2, 4, 'Andrés', 'Petrillo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Calle 1', '2do Piso', 'Capital Federal', 'Buenos Aires', '1414', '11', NULL, NULL, NULL, NULL, '2019-07-09 16:22:32', '2019-07-09 16:22:32'),
	(3, 3, NULL, NULL, 'Test', 'User 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-09 19:13:35', '2019-07-09 19:13:35'),
	(4, 4, NULL, NULL, '', 'Doe', 'password', '4ZRaYsZmH9hjkkBjuibLBQIXmfv1', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-23 12:36:54', '2019-07-23 12:36:54');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;

-- Dumping structure for table mainflux.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_set_id` int(10) unsigned DEFAULT NULL,
  `question` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_question_set_id_foreign` (`question_set_id`),
  CONSTRAINT `questions_question_set_id_foreign` FOREIGN KEY (`question_set_id`) REFERENCES `question_sets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.questions: ~4 rows (approximately)
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
REPLACE INTO `questions` (`id`, `question_set_id`, `question`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Paso 1 1', '2019-07-09 16:25:46', '2019-07-09 16:25:46'),
	(2, 1, 'Paso 1 2', '2019-07-09 16:25:46', '2019-07-09 16:25:46'),
	(3, 1, 'Paso 1 3', '2019-07-09 16:25:46', '2019-07-09 16:25:46'),
	(5, 3, 'Pasos 2 1', '2019-07-09 16:27:21', '2019-07-09 16:27:21'),
	(6, 3, 'Pasos 2 2', '2019-07-09 16:27:21', '2019-07-09 16:27:21');
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;

-- Dumping structure for table mainflux.question_sets
CREATE TABLE IF NOT EXISTS `question_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.question_sets: ~2 rows (approximately)
/*!40000 ALTER TABLE `question_sets` DISABLE KEYS */;
REPLACE INTO `question_sets` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Set pasos 1', '', '2019-07-09 16:25:46', '2019-07-09 16:25:46'),
	(3, 'Set pasos 2', '', '2019-07-09 16:27:21', '2019-07-09 16:27:21');
/*!40000 ALTER TABLE `question_sets` ENABLE KEYS */;

-- Dumping structure for table mainflux.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.roles: ~2 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
REPLACE INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43'),
	(2, 'user', 'web', '2019-07-08 12:49:43', '2019-07-08 12:49:43');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table mainflux.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.role_has_permissions: ~49 rows (approximately)
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
REPLACE INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(12, 1),
	(13, 1),
	(14, 1),
	(15, 1),
	(16, 1),
	(17, 1),
	(18, 1),
	(19, 1),
	(20, 1),
	(21, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(26, 1),
	(27, 1),
	(28, 1),
	(29, 1),
	(30, 1),
	(31, 1),
	(32, 1),
	(33, 1),
	(34, 1),
	(35, 1),
	(36, 1),
	(37, 1),
	(38, 1),
	(39, 1),
	(40, 1),
	(9, 2),
	(10, 2),
	(11, 2),
	(32, 2),
	(33, 2),
	(34, 2),
	(35, 2),
	(37, 2),
	(38, 2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;

-- Dumping structure for table mainflux.starred_tasks
CREATE TABLE IF NOT EXISTS `starred_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `starred_tasks_task_id_foreign` (`task_id`),
  KEY `starred_tasks_user_id_foreign` (`user_id`),
  CONSTRAINT `starred_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `starred_tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.starred_tasks: ~0 rows (approximately)
/*!40000 ALTER TABLE `starred_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `starred_tasks` ENABLE KEYS */;

-- Dumping structure for table mainflux.sub_tasks
CREATE TABLE IF NOT EXISTS `sub_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` datetime DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `upload_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_tasks_task_id_foreign` (`task_id`),
  KEY `sub_tasks_user_id_foreign` (`user_id`),
  CONSTRAINT `sub_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sub_tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.sub_tasks: ~0 rows (approximately)
/*!40000 ALTER TABLE `sub_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_tasks` ENABLE KEYS */;

-- Dumping structure for table mainflux.sub_task_ratings
CREATE TABLE IF NOT EXISTS `sub_task_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_task_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_task_ratings_sub_task_id_foreign` (`sub_task_id`),
  KEY `sub_task_ratings_user_id_foreign` (`user_id`),
  CONSTRAINT `sub_task_ratings_sub_task_id_foreign` FOREIGN KEY (`sub_task_id`) REFERENCES `sub_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sub_task_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.sub_task_ratings: ~0 rows (approximately)
/*!40000 ALTER TABLE `sub_task_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_task_ratings` ENABLE KEYS */;

-- Dumping structure for table mainflux.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_set_id` int(10) unsigned DEFAULT NULL,
  `task_category_id` int(10) unsigned DEFAULT NULL,
  `task_priority_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `progress` int(11) NOT NULL DEFAULT '0',
  `progress_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sign_off_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `is_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurrence_start_date` date DEFAULT NULL,
  `recurrence_end_date` date DEFAULT NULL,
  `recurring_frequency` int(11) NOT NULL DEFAULT '0',
  `next_recurring_date` date DEFAULT NULL,
  `recurring_task_id` int(10) unsigned DEFAULT NULL,
  `upload_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_task_category_id_foreign` (`task_category_id`),
  KEY `tasks_task_priority_id_foreign` (`task_priority_id`),
  KEY `tasks_user_id_foreign` (`user_id`),
  KEY `tasks_recurring_task_id_foreign` (`recurring_task_id`),
  KEY `tasks_question_set_id_foreign` (`question_set_id`),
  CONSTRAINT `tasks_question_set_id_foreign` FOREIGN KEY (`question_set_id`) REFERENCES `question_sets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_recurring_task_id_foreign` FOREIGN KEY (`recurring_task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_task_category_id_foreign` FOREIGN KEY (`task_category_id`) REFERENCES `task_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_task_priority_id_foreign` FOREIGN KEY (`task_priority_id`) REFERENCES `task_priorities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.tasks: ~1 rows (approximately)
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
REPLACE INTO `tasks` (`id`, `uuid`, `question_set_id`, `task_category_id`, `task_priority_id`, `title`, `description`, `start_date`, `due_date`, `completed_at`, `progress`, `progress_type`, `rating_type`, `sign_off_status`, `user_id`, `is_cancelled`, `is_archived`, `is_recurring`, `recurrence_start_date`, `recurrence_end_date`, `recurring_frequency`, `next_recurring_date`, `recurring_task_id`, `upload_token`, `created_at`, `updated_at`) VALUES
	(1, 'ab5123ce-de79-41d4-95bf-586f1c604434', 1, 1, 1, 'Tarea 1 2', '<h3 style="margin: 15px 0px; padding: 0px; font-weight: 700; font-size: 14px; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif;">The standard Lorem Ipsum passage, used since the 1500s</h3><p style="margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p><h3 style="margin: 15px 0px; padding: 0px; font-weight: 700; font-size: 14px; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif;">Section 1.10.32 of "de Finibus Bonorum et Malorum", written by Cicero in 45 BC</h3><p style="margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; text-align: justify; font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px;">"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"</p>', '2019-07-10', '2019-07-13', NULL, 0, 'question', 'task_based', NULL, 1, 0, 0, 0, NULL, NULL, 0, NULL, NULL, '3db6eeff-619e-4ecd-ba64-8bade3d135a0', '2019-07-09 16:24:40', '2019-07-12 15:06:47');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_attachments
CREATE TABLE IF NOT EXISTS `task_attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `upload_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_attachments_task_id_foreign` (`task_id`),
  KEY `task_attachments_user_id_foreign` (`user_id`),
  CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_attachments: ~0 rows (approximately)
/*!40000 ALTER TABLE `task_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_attachments` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_categories
CREATE TABLE IF NOT EXISTS `task_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_categories: ~2 rows (approximately)
/*!40000 ALTER TABLE `task_categories` DISABLE KEYS */;
REPLACE INTO `task_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Categoría 1', 'Descripción de la categoría 1', '2019-07-08 19:56:00', '2019-07-08 19:56:00'),
	(2, 'Categoría 2', 'Descripción de la categoría 2', '2019-07-08 19:56:09', '2019-07-08 19:56:09'),
	(3, 'Categoría 3', 'Descripción de la categoría 3', '2019-07-08 19:56:21', '2019-07-08 19:56:21');
/*!40000 ALTER TABLE `task_categories` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_comments
CREATE TABLE IF NOT EXISTS `task_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_comments_task_id_foreign` (`task_id`),
  KEY `task_comments_user_id_foreign` (`user_id`),
  KEY `task_comments_reply_id_foreign` (`reply_id`),
  CONSTRAINT `task_comments_reply_id_foreign` FOREIGN KEY (`reply_id`) REFERENCES `task_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_comments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_comments: ~0 rows (approximately)
/*!40000 ALTER TABLE `task_comments` DISABLE KEYS */;
REPLACE INTO `task_comments` (`id`, `task_id`, `user_id`, `comment`, `reply_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Comentario 1', NULL, '2019-07-09 16:29:21', '2019-07-09 16:29:21');
/*!40000 ALTER TABLE `task_comments` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_notes
CREATE TABLE IF NOT EXISTS `task_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `upload_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_notes_task_id_foreign` (`task_id`),
  KEY `task_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `task_notes_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_notes: ~0 rows (approximately)
/*!40000 ALTER TABLE `task_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_notes` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_priorities
CREATE TABLE IF NOT EXISTS `task_priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_priorities: ~2 rows (approximately)
/*!40000 ALTER TABLE `task_priorities` DISABLE KEYS */;
REPLACE INTO `task_priorities` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Prioridad 1', 'Descripciób de la prioridad 1', '2019-07-08 19:56:37', '2019-07-08 19:56:37'),
	(2, 'Prioridad 2', 'Descripción de la prioridad 2', '2019-07-08 19:56:46', '2019-07-08 19:56:46');
/*!40000 ALTER TABLE `task_priorities` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_signoff_logs
CREATE TABLE IF NOT EXISTS `task_signoff_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_signoff_logs_task_id_foreign` (`task_id`),
  KEY `task_signoff_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `task_signoff_logs_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_signoff_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_signoff_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `task_signoff_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_signoff_logs` ENABLE KEYS */;

-- Dumping structure for table mainflux.task_user
CREATE TABLE IF NOT EXISTS `task_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_user_task_id_foreign` (`task_id`),
  KEY `task_user_user_id_foreign` (`user_id`),
  CONSTRAINT `task_user_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.task_user: ~0 rows (approximately)
/*!40000 ALTER TABLE `task_user` DISABLE KEYS */;
REPLACE INTO `task_user` (`id`, `task_id`, `user_id`, `rating`, `remarks`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, NULL, '2019-07-12 15:55:40', '2019-07-12 15:55:40');
/*!40000 ALTER TABLE `task_user` ENABLE KEYS */;

-- Dumping structure for table mainflux.todos
CREATE TABLE IF NOT EXISTS `todos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `todos_user_id_foreign` (`user_id`),
  CONSTRAINT `todos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.todos: ~2 rows (approximately)
/*!40000 ALTER TABLE `todos` DISABLE KEYS */;
REPLACE INTO `todos` (`id`, `user_id`, `title`, `description`, `status`, `date`, `completed_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 'tarea texto nombre titulo', 'asd d sjdkas djsa;kd jaskd jaksd;ljsak sajkda ;sjdka djaks djkasd ;jaskd ;jak', 1, '2019-07-17', '2019-07-10 16:46:08', '2019-07-10 13:15:14', '2019-07-10 16:46:08'),
	(2, 1, 'todo 12321 jkjksadj sakjdas', 'sadsa jdkas jdkal; sdjaks dj;asldk jas;ld jasl;kdj askld jaskd ;aksdj askjda', 0, '2019-07-19', NULL, '2019-07-10 13:15:30', '2019-07-10 16:46:07');
/*!40000 ALTER TABLE `todos` ENABLE KEYS */;

-- Dumping structure for table mainflux.uploads
CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `upload_token` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_temp_delete` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uploads_user_id_foreign` (`user_id`),
  CONSTRAINT `uploads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.uploads: ~0 rows (approximately)
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
REPLACE INTO `uploads` (`id`, `uuid`, `user_id`, `module`, `module_id`, `upload_token`, `user_filename`, `filename`, `is_temp_delete`, `status`, `created_at`, `updated_at`) VALUES
	(1, '0098c319-debf-4679-bed2-8efd08f067a6', 3, 'message', 6, 'c390cd42-168a-41c6-a777-d5660cf65729', 'AFIP _ Formulario 184.pdf', 'uploads/message/gcCNX2mOfDWv6nnCrTgq8lblqfrGM1CtEirXBek1.pdf', 0, 1, '2019-07-13 22:45:31', '2019-07-14 02:35:49');
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;

-- Dumping structure for table mainflux.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.users: ~2 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
REPLACE INTO `users` (`id`, `uuid`, `email`, `password`, `activation_token`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'eb768679-1503-4d5f-aa59-9ecd5b3d0342', 'rbutta@gmail.com', '$2y$10$IqoDBBU7eptdjABYR08w6.ERDDnEjIk9j//NUqyPa/Q3tBX1UKbAK', 'd2b2a990-8cfb-4ee6-8230-831ad6c99607', 'activated', NULL, '2019-07-08 12:49:45', '2019-07-08 12:49:45'),
	(2, '33e09e60-06f3-4be5-a19c-8e411bf41116', 'andrespetrillo@mindbot.com.ar', '$2y$10$nWg8BHakPQ6FGfqNDSsGquVctyjGKNSM8PvExOHFT2K4cXEwQVaj6', 'b03f7cf3-3862-4d9c-bea1-5fdc54d5a80a', 'activated', NULL, '2019-07-09 16:22:32', '2019-07-09 16:22:32'),
	(3, '7288de9c-bf54-4d62-8de3-9608e440b9d5', 'test1@gmail.com', '$2y$10$V4h.ryiTQxvQyeDwJkZdBe/wkWuy5rr3E.LXdCqiumURT14Wiw6iS', 'd702c1c5-1c31-44df-9fd6-0c120cbcd875', 'activated', NULL, '2019-07-09 19:13:35', '2019-07-09 19:15:12'),
	(4, NULL, 'test2@gmail.com', NULL, 'b299a5c3-3610-4f49-9172-7c0fe3cc589c', 'activated', NULL, '2019-07-23 12:36:53', '2019-07-23 12:36:53');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table mainflux.user_preferences
CREATE TABLE IF NOT EXISTS `user_preferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direction` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_theme` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_preferences_user_id_foreign` (`user_id`),
  CONSTRAINT `user_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mainflux.user_preferences: ~2 rows (approximately)
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
REPLACE INTO `user_preferences` (`id`, `user_id`, `locale`, `direction`, `color_theme`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, NULL, NULL, '2019-07-08 12:49:45', '2019-07-08 12:49:45'),
	(2, 2, NULL, NULL, NULL, '2019-07-09 16:22:32', '2019-07-09 16:22:32'),
	(3, 3, NULL, NULL, NULL, '2019-07-09 19:13:35', '2019-07-09 19:13:35'),
	(4, 4, NULL, NULL, NULL, '2019-07-23 12:36:54', '2019-07-23 12:36:54');
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
