-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para plataformarsugrupo9
CREATE DATABASE IF NOT EXISTS `plataformarsugrupo9`;
USE `plataformarsugrupo9`;

-- Volcando estructura para tabla plataformarsugrupo9.asistencias
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personal_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `estado` enum('presente','ausente','tardanza','permiso','vacaciones') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'presente',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asistencias_fecha_estado_index` (`fecha`,`estado`),
  KEY `asistencias_personal_id_foreign` (`personal_id`),
  CONSTRAINT `asistencias_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.asistencias: ~57 rows (aproximadamente)
INSERT INTO `asistencias` (`id`, `personal_id`, `fecha`, `hora_entrada`, `hora_salida`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-10-11', '07:05:00', '16:17:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(2, 2, '2025-10-11', '07:29:00', '16:30:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(3, 3, '2025-10-11', '07:05:00', '16:30:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(4, 4, '2025-10-11', '07:00:00', '16:24:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(5, 5, '2025-10-11', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(6, 6, '2025-10-11', '07:07:00', '16:01:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(7, 7, '2025-10-11', '07:28:00', '16:24:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(8, 8, '2025-10-11', '07:03:00', '16:24:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(9, 9, '2025-10-11', '07:26:00', '16:04:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(10, 1, '2025-10-13', '07:04:00', '16:03:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(11, 2, '2025-10-13', '07:15:00', '16:26:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(12, 3, '2025-10-13', '07:05:00', '16:00:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(13, 4, '2025-10-13', '07:17:00', '16:09:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(14, 5, '2025-10-13', '07:22:00', '16:18:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(15, 6, '2025-10-13', '07:10:00', '16:03:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(16, 7, '2025-10-13', '07:18:00', '16:06:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(17, 8, '2025-10-13', '07:07:00', '16:14:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(18, 9, '2025-10-13', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(19, 1, '2025-10-14', '07:27:00', '16:05:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(20, 2, '2025-10-14', '07:00:00', '16:27:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(21, 3, '2025-10-14', '07:29:00', '16:11:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(22, 4, '2025-10-14', '07:07:00', '16:03:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(23, 5, '2025-10-14', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(24, 6, '2025-10-14', '07:16:00', '16:19:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(25, 7, '2025-10-14', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(26, 8, '2025-10-14', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(27, 9, '2025-10-14', '07:12:00', '16:04:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(28, 1, '2025-10-15', '07:23:00', '16:03:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(29, 2, '2025-10-15', '07:03:00', '16:15:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(30, 3, '2025-10-15', '07:23:00', '16:28:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(31, 4, '2025-10-15', '07:25:00', '16:11:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(32, 5, '2025-10-15', '07:21:00', '16:13:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(33, 6, '2025-10-15', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(34, 7, '2025-10-15', '07:25:00', '16:05:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(35, 8, '2025-10-15', '07:20:00', '16:18:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(36, 9, '2025-10-15', '07:02:00', '16:30:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(37, 1, '2025-10-16', '07:27:00', '16:19:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(38, 2, '2025-10-16', NULL, NULL, 'ausente', 'Inasistencia', '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(39, 3, '2025-10-16', '07:06:00', '16:20:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(40, 4, '2025-10-16', '07:22:00', '16:23:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(41, 5, '2025-10-16', '07:08:00', '16:11:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(42, 6, '2025-10-16', '07:02:00', '16:18:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(43, 7, '2025-10-16', '07:03:00', '16:07:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(44, 8, '2025-10-16', '07:19:00', '16:02:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(45, 9, '2025-10-16', '07:10:00', '16:22:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(46, 1, '2025-10-17', '07:22:00', '16:21:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(47, 2, '2025-10-17', '07:29:00', '16:16:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(48, 3, '2025-10-17', '07:22:00', '16:24:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(49, 4, '2025-10-17', '07:29:00', '16:26:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(50, 5, '2025-10-17', '07:19:00', '16:08:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(51, 6, '2025-10-17', '07:01:00', '16:25:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(52, 7, '2025-10-17', '07:16:00', '16:13:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(53, 8, '2025-10-17', '07:01:00', '16:18:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(54, 9, '2025-10-17', '07:00:00', '16:25:00', 'presente', NULL, '2025-10-17 06:16:37', '2025-10-17 06:16:37'),
	(55, 9, '2025-10-28', NULL, NULL, 'ausente', NULL, '2025-10-28 08:32:41', '2025-11-05 05:43:19'),
	(56, 2, '2025-11-25', '09:00:00', '12:00:00', 'presente', 'iniunonoi', '2025-11-26 06:37:03', '2025-11-26 06:37:03'),
	(57, 2, '2025-11-26', '09:00:00', '12:00:00', 'presente', NULL, '2025-11-26 07:37:43', '2025-11-26 07:37:43'),
	(58, 6, '2025-11-26', '09:00:00', '12:00:00', 'presente', NULL, '2025-11-26 07:38:05', '2025-11-26 07:38:05'),
	(59, 2, '2025-11-27', '09:00:00', '12:00:00', 'presente', NULL, '2025-11-27 05:33:57', '2025-11-27 05:33:57'),
	(60, 7, '2025-11-27', '09:00:00', '12:00:00', 'presente', NULL, '2025-11-27 05:48:46', '2025-11-27 05:48:46');

-- Volcando estructura para tabla plataformarsugrupo9.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.cache: ~2 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.cache_locks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.cambios
CREATE TABLE IF NOT EXISTS `cambios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `programacion_id` bigint unsigned NOT NULL,
  `tipo_cambio` enum('turno','vehiculo','personal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_anterior` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_nuevo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_anterior_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_nuevo_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_id` bigint unsigned DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cambios_programacion_id_foreign` (`programacion_id`),
  CONSTRAINT `cambios_programacion_id_foreign` FOREIGN KEY (`programacion_id`) REFERENCES `programaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.cambios: ~11 rows (aproximadamente)
INSERT INTO `cambios` (`id`, `programacion_id`, `tipo_cambio`, `valor_anterior`, `valor_nuevo`, `valor_anterior_nombre`, `valor_nuevo_nombre`, `motivo_id`, `notas`, `user_id`, `created_at`, `updated_at`) VALUES
	(3, 41, 'turno', '1', '4', 'Mañana', 'tarde', 2, 'dtyduy', 1, '2025-11-05 05:26:56', '2025-11-05 05:26:56'),
	(4, 52, 'turno', '1', '4', 'Mañana', 'tarde', 1, 'contingencia', 1, '2025-11-05 07:25:43', '2025-11-05 07:25:43'),
	(5, 52, 'vehiculo', '1', '2', 'RSU-001', 'RSU-002', 2, 'falla', 1, '2025-11-05 07:25:43', '2025-11-05 07:25:43'),
	(6, 50, 'vehiculo', '2', '2', 'RSU-001', 'RSU-002', 1, NULL, 1, '2025-11-14 04:53:59', '2025-11-14 04:53:59'),
	(7, 51, 'vehiculo', '2', '2', 'RSU-001', 'RSU-002', 1, NULL, 1, '2025-11-14 04:53:59', '2025-11-14 04:53:59'),
	(8, 53, 'turno', '1', '4', 'Mañana', 'tarde', 2, 'falla', 1, '2025-11-14 06:29:56', '2025-11-14 06:29:56'),
	(9, 53, 'vehiculo', '2', '2', 'RSU-001', 'RSU-002', 2, NULL, 1, '2025-11-14 06:31:46', '2025-11-14 06:31:46'),
	(10, 54, 'vehiculo', '2', '2', 'RSU-001', 'RSU-002', 2, NULL, 1, '2025-11-14 06:31:46', '2025-11-14 06:31:46'),
	(11, 55, 'vehiculo', '2', '2', 'RSU-001', 'RSU-002', 2, NULL, 1, '2025-11-14 06:31:46', '2025-11-14 06:31:46'),
	(12, 53, 'turno', '1', '1', 'tarde', 'Mañana', 1, 'sdcds', 1, '2025-11-26 10:59:32', '2025-11-26 10:59:32'),
	(38, 82, 'personal', '6', '7', 'Pedro Antonio Sánchez Gutiérrez', 'Luis Alberto Castro Morales', 3, 'dsd', 1, '2025-11-27 07:40:40', '2025-11-27 07:40:40');

-- Volcando estructura para tabla plataformarsugrupo9.colores
CREATE TABLE IF NOT EXISTS `colores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_rgb` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.colores: ~13 rows (aproximadamente)
INSERT INTO `colores` (`id`, `nombre`, `codigo_rgb`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Blanco', '#FFFFFF', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(2, 'Negro', '#000000', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(3, 'Gris', '#808080', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(4, 'Plata', '#C0C0C0', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(5, 'Rojo', '#FF0000', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(6, 'Azul', '#0000FF', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(7, 'Verde', '#008000', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(8, 'Amarillo', '#FFFF00', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(9, 'Naranja', '#FFA500', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(10, 'Beige', '#F5F5DC', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(11, 'Verde Oscuro', '#006400', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(12, 'Celeste', '#87CEEB', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(13, 'dfvfv', '#6016E9', 0, '2025-10-20 01:59:41', '2025-10-20 02:00:03', '2025-10-20 02:00:03');

-- Volcando estructura para tabla plataformarsugrupo9.confgrupos
CREATE TABLE IF NOT EXISTS `confgrupos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grupopersonal_id` bigint unsigned NOT NULL,
  `personal_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `confgrupos_grupo_personal_unique` (`grupopersonal_id`,`personal_id`),
  KEY `confgrupos_grupopersonal_id_index` (`grupopersonal_id`),
  KEY `confgrupos_personal_id_index` (`personal_id`),
  CONSTRAINT `confgrupos_grupopersonal_id_foreign` FOREIGN KEY (`grupopersonal_id`) REFERENCES `grupospersonal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `confgrupos_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.confgrupos: ~8 rows (aproximadamente)
INSERT INTO `confgrupos` (`id`, `grupopersonal_id`, `personal_id`, `created_at`, `updated_at`) VALUES
	(19, 4, 1, '2025-11-05 06:44:16', '2025-11-05 06:44:16'),
	(20, 4, 5, '2025-11-05 06:44:16', '2025-11-05 06:44:16'),
	(21, 4, 6, '2025-11-05 06:44:16', '2025-11-05 06:44:16'),
	(22, 5, 1, '2025-11-14 06:39:35', '2025-11-14 06:39:35'),
	(23, 5, 6, '2025-11-14 06:39:35', '2025-11-14 06:39:35'),
	(24, 5, 7, '2025-11-14 06:39:35', '2025-11-14 06:39:35'),
	(27, 2, 1, '2025-11-26 04:27:10', '2025-11-26 04:27:10'),
	(28, 2, 6, '2025-11-26 04:27:10', '2025-11-26 04:27:10');

-- Volcando estructura para tabla plataformarsugrupo9.contratos
CREATE TABLE IF NOT EXISTS `contratos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personal_id` bigint unsigned NOT NULL,
  `tipo_contrato` enum('permanente','temporal','nombrado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Permanente o Temporal de 3 meses',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL COMMENT 'NULL para permanentes',
  `salario` decimal(10,2) DEFAULT NULL,
  `periodo_prueba` int DEFAULT NULL COMMENT 'En meses',
  `departamento_id` bigint unsigned DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `motivo_terminacion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Solo un contrato activo por persona',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contratos_personal_id_activo_fecha_inicio_fecha_fin_index` (`personal_id`,`activo`,`fecha_inicio`,`fecha_fin`),
  KEY `contratos_departamento_id_foreign` (`departamento_id`),
  CONSTRAINT `contratos_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.contratos: ~10 rows (aproximadamente)
INSERT INTO `contratos` (`id`, `personal_id`, `tipo_contrato`, `fecha_inicio`, `fecha_fin`, `salario`, `periodo_prueba`, `departamento_id`, `observaciones`, `motivo_terminacion`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'permanente', '2020-01-15', NULL, NULL, NULL, NULL, 'Contrato permanente desde el inicio del programa', NULL, 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(2, 2, 'permanente', '2021-03-01', NULL, NULL, NULL, NULL, 'Contrato permanente', NULL, 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(3, 3, 'temporal', '2024-10-01', '2024-12-01', NULL, NULL, NULL, 'Contrato temporal por 2 meses', NULL, 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(4, 4, 'nombrado', '2022-06-15', NULL, 1500.00, NULL, 1, 'Contrato permanente', NULL, 1, '2025-10-17 06:16:37', '2025-10-29 06:09:48', NULL),
	(5, 5, 'permanente', '2021-08-01', NULL, NULL, NULL, NULL, 'Contrato permanente', NULL, 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(6, 6, 'temporal', '2024-09-01', '2025-12-30', NULL, NULL, NULL, 'Contrato temporal por 2 meses', NULL, 1, '2025-10-17 06:16:37', '2025-11-14 06:24:47', NULL),
	(7, 7, 'permanente', '2023-02-01', NULL, NULL, NULL, NULL, 'Contrato permanente', NULL, 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(8, 8, 'temporal', '2024-10-15', '2025-01-15', 1500.00, NULL, NULL, 'Contrato temporal por 2 meses', NULL, 0, '2025-10-17 06:16:37', '2025-10-29 06:22:46', NULL),
	(9, 9, 'permanente', '2019-05-01', NULL, NULL, NULL, NULL, 'Supervisor desde el inicio', NULL, 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(10, 8, 'nombrado', '2025-10-08', NULL, 1200.00, 3, 1, 'hiybjkb lnp', NULL, 1, '2025-10-29 06:22:27', '2025-10-29 06:22:27', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.departamentos
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departamentos_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.departamentos: ~0 rows (aproximadamente)
INSERT INTO `departamentos` (`id`, `codigo`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'LAM', 'Lambayeque', 1, '2025-10-24 01:55:57', '2025-10-24 01:55:57');

-- Volcando estructura para tabla plataformarsugrupo9.dias_mantenimiento
CREATE TABLE IF NOT EXISTS `dias_mantenimiento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `horario_mantenimiento_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realizado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dias_mantenimiento_horario_mantenimiento_id_fecha_unique` (`horario_mantenimiento_id`,`fecha`),
  KEY `dias_mantenimiento_horario_mantenimiento_id_index` (`horario_mantenimiento_id`),
  KEY `dias_mantenimiento_fecha_index` (`fecha`),
  CONSTRAINT `dias_mantenimiento_horario_mantenimiento_id_foreign` FOREIGN KEY (`horario_mantenimiento_id`) REFERENCES `horarios_mantenimiento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.dias_mantenimiento: ~14 rows (aproximadamente)
INSERT INTO `dias_mantenimiento` (`id`, `horario_mantenimiento_id`, `fecha`, `observacion`, `imagen`, `realizado`, `created_at`, `updated_at`) VALUES
	(5, 1, '2025-11-03', 'completado', 'mantenimiento/xfkGrUy61x0rMgAQ7VHbUs9hjDPkKf8HLspks37a.jpg', 1, '2025-11-19 07:28:41', '2025-11-19 07:29:15'),
	(6, 1, '2025-11-10', NULL, NULL, 0, '2025-11-19 07:28:41', '2025-11-19 07:28:41'),
	(7, 1, '2025-11-17', NULL, NULL, 0, '2025-11-19 07:28:41', '2025-11-19 07:28:41'),
	(8, 1, '2025-11-24', NULL, NULL, 0, '2025-11-19 07:28:41', '2025-11-19 07:28:41'),
	(9, 2, '2025-12-01', NULL, NULL, 0, '2025-11-19 07:52:21', '2025-11-19 07:52:21'),
	(10, 2, '2025-12-08', NULL, NULL, 0, '2025-11-19 07:52:21', '2025-11-19 07:52:21'),
	(11, 2, '2025-12-15', NULL, NULL, 0, '2025-11-19 07:52:21', '2025-11-19 07:52:21'),
	(12, 2, '2025-12-22', NULL, NULL, 0, '2025-11-19 07:52:21', '2025-11-19 07:52:21'),
	(13, 2, '2025-12-29', NULL, NULL, 0, '2025-11-19 07:52:21', '2025-11-19 07:52:21'),
	(14, 3, '2025-12-01', NULL, NULL, 0, '2025-11-19 07:52:54', '2025-11-19 07:52:54'),
	(15, 3, '2025-12-08', 'CompletAdo', 'mantenimiento/jKqdta0DqJSQRSApfDCdtMCU1450RCBJvQbFKNuH.jpg', 1, '2025-11-19 07:52:54', '2025-11-19 07:53:18'),
	(16, 3, '2025-12-15', NULL, NULL, 0, '2025-11-19 07:52:54', '2025-11-19 07:52:54'),
	(17, 3, '2025-12-22', NULL, NULL, 0, '2025-11-19 07:52:54', '2025-11-19 07:52:54'),
	(18, 3, '2025-12-29', NULL, NULL, 0, '2025-11-19 07:52:54', '2025-11-19 07:52:54');

-- Volcando estructura para tabla plataformarsugrupo9.distritos
CREATE TABLE IF NOT EXISTS `distritos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provincia_id` bigint unsigned NOT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `distritos_codigo_unique` (`codigo`),
  KEY `distritos_provincia_id_foreign` (`provincia_id`),
  CONSTRAINT `distritos_provincia_id_foreign` FOREIGN KEY (`provincia_id`) REFERENCES `provincias` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.distritos: ~1 rows (aproximadamente)
INSERT INTO `distritos` (`id`, `provincia_id`, `codigo`, `nombre`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(2, 1, 'JLO', 'José Leonardo Ortiz', 1, '2025-10-24 01:55:57', '2025-10-24 01:55:57', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.funciones
CREATE TABLE IF NOT EXISTS `funciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `es_predefinida` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Conductor y Ayudante no pueden eliminarse',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.funciones: ~7 rows (aproximadamente)
INSERT INTO `funciones` (`id`, `nombre`, `descripcion`, `es_predefinida`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Conductor', 'Responsable de conducir el vehículo de recolección', 1, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(2, 'Ayudante', 'Asiste al conductor en la recolección de residuos', 1, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(3, 'Supervisor', 'Supervisa las operaciones de recolección', 0, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(4, 'Mecánico', 'Encargado del mantenimiento de vehículos', 0, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(5, 'Coordinador', 'Coordina las  rutas y programación', 0, 1, '2025-10-17 06:16:35', '2025-10-28 17:35:57', NULL),
	(6, 'cx', 'cxcxdcvd', 0, 0, '2025-10-25 00:20:14', '2025-10-25 20:39:41', '2025-10-25 20:39:41'),
	(7, 'fdvdv', 'vddvf', 0, 1, '2025-10-25 20:39:10', '2025-10-25 20:39:10', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.grupospersonal
CREATE TABLE IF NOT EXISTS `grupospersonal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zona_id` bigint unsigned NOT NULL,
  `turno_id` bigint unsigned NOT NULL,
  `vehiculo_id` bigint unsigned NOT NULL,
  `dias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Días de la semana para este grupo (ej: Lunes, Martes, Miércoles)',
  `estado` tinyint NOT NULL DEFAULT '1' COMMENT 'Estado del grupo (ej: 1=Activo, 0=Inactivo)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grupospersonal_turno_id_foreign` (`turno_id`),
  KEY `grupospersonal_vehiculo_id_foreign` (`vehiculo_id`),
  KEY `grupospersonal_zona_id_turno_id_estado_index` (`zona_id`,`turno_id`,`estado`),
  CONSTRAINT `grupospersonal_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grupospersonal_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grupospersonal_zona_id_foreign` FOREIGN KEY (`zona_id`) REFERENCES `zonas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.grupospersonal: ~2 rows (aproximadamente)
INSERT INTO `grupospersonal` (`id`, `nombre`, `zona_id`, `turno_id`, `vehiculo_id`, `dias`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(2, 'zona1', 1, 1, 1, 'Lunes,Martes,Miércoles,Jueves', 1, '2025-10-29 04:47:52', '2025-11-26 04:27:10', NULL),
	(4, 'cd', 3, 4, 3, 'Lunes,Jueves', 1, '2025-11-05 06:41:37', '2025-11-05 06:44:16', NULL),
	(5, 'zona 2', 2, 1, 3, 'Lunes,Martes,Jueves', 1, '2025-11-14 06:39:35', '2025-11-14 06:39:35', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.horarios_mantenimiento
CREATE TABLE IF NOT EXISTS `horarios_mantenimiento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mantenimiento_id` bigint unsigned NOT NULL,
  `vehiculo_id` bigint unsigned NOT NULL,
  `responsable_id` bigint unsigned NOT NULL,
  `tipo_mantenimiento` enum('Preventivo','Limpieza','Reparación') COLLATE utf8mb4_unicode_ci NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `horarios_mantenimiento_responsable_id_foreign` (`responsable_id`),
  KEY `idx_horario_mant_dia_veh` (`mantenimiento_id`,`dia_semana`,`vehiculo_id`),
  KEY `idx_vehiculo_dia` (`vehiculo_id`,`dia_semana`),
  CONSTRAINT `horarios_mantenimiento_mantenimiento_id_foreign` FOREIGN KEY (`mantenimiento_id`) REFERENCES `mantenimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `horarios_mantenimiento_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `personal` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `horarios_mantenimiento_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.horarios_mantenimiento: ~2 rows (aproximadamente)
INSERT INTO `horarios_mantenimiento` (`id`, `mantenimiento_id`, `vehiculo_id`, `responsable_id`, `tipo_mantenimiento`, `dia_semana`, `hora_inicio`, `hora_fin`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 1, 'Preventivo', 'Lunes', '09:00:00', '12:00:00', '2025-11-19 06:44:40', '2025-11-19 07:49:23'),
	(2, 2, 1, 1, 'Preventivo', 'Lunes', '08:00:00', '10:00:00', '2025-11-19 07:52:21', '2025-11-19 07:52:21'),
	(3, 2, 2, 2, 'Preventivo', 'Lunes', '08:00:00', '10:00:00', '2025-11-19 07:52:54', '2025-11-19 07:52:54');

-- Volcando estructura para tabla plataformarsugrupo9.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.job_batches: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.mantenimientos
CREATE TABLE IF NOT EXISTS `mantenimientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mantenimientos_fecha_inicio_index` (`fecha_inicio`),
  KEY `mantenimientos_fecha_fin_index` (`fecha_fin`),
  KEY `mantenimientos_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.mantenimientos: ~2 rows (aproximadamente)
INSERT INTO `mantenimientos` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Mantenimiento Noviembre 2025', '2025-11-01', '2025-11-30', 1, '2025-11-19 06:34:58', '2025-11-19 06:34:58'),
	(2, 'diciembre 2025', '2025-12-01', '2025-12-30', 1, '2025-11-19 07:51:52', '2025-11-19 07:51:52');

-- Volcando estructura para tabla plataformarsugrupo9.marcas
CREATE TABLE IF NOT EXISTS `marcas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.marcas: ~7 rows (aproximadamente)
INSERT INTO `marcas` (`id`, `nombre`, `descripcion`, `logo`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Volvo', 'Fabricante sueco de vehículos comerciales y de construcción', NULL, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(2, 'Mercedes-Benz', 'Fabricante alemán de vehículos comerciales', NULL, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(3, 'Hino-', 'Fabricante japonés de camiones', 'marcas/lcwPc0ozSI44ff6pzJ52Bh0OtpflQxCR66g5sfM3.jpg', 1, '2025-10-17 06:16:35', '2025-10-22 05:09:50', NULL),
	(4, 'Hyundai', 'Fabricante coreano de vehículos comerciales', NULL, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(5, 'JAC', 'Fabricante chino de vehículos comerciales', NULL, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(6, 'Isuzu', 'Fabricante japonés de camiones ligeros y medianos', NULL, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(7, 'Scania', 'Fabricante sueco de camiones pesados', NULL, 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.migrations: ~26 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(18, '0001_01_01_000000_create_users_table', 1),
	(19, '0001_01_01_000001_create_cache_table', 1),
	(20, '0001_01_01_000002_create_jobs_table', 1),
	(21, '2025_10_14_233605_add_two_factor_columns_to_users_table', 1),
	(22, '2025_10_14_233648_create_personal_access_tokens_table', 1),
	(23, '2025_10_16_125629_create_marcas_table', 1),
	(24, '2025_10_16_125640_create_tipos_vehiculo_table', 1),
	(25, '2025_10_16_125809_create_colores_table', 1),
	(26, '2025_10_16_125815_create_modelos_table', 1),
	(27, '2025_10_16_125823_create_vehiculos_table', 1),
	(28, '2025_10_16_125828_create_vehiculo_imagenes_table', 1),
	(29, '2025_10_16_125855_create_funciones_table', 1),
	(30, '2025_10_16_125902_create_personal_table', 1),
	(31, '2025_10_16_125908_create_contratos_table', 1),
	(32, '2025_10_16_125913_create_vacaciones_table', 1),
	(33, '2025_10_16_125919_create_vacaciones_periodos_table', 1),
	(34, '2025_10_16_125925_create_asistencias_table', 1),
	(35, '2025_10_16_185226_create_departamentos_table', 1),
	(36, '2025_10_16_185233_create_provincias_table', 1),
	(37, '2025_10_16_185241_create_distritos_table', 1),
	(38, '2025_10_16_185247_create_zonas_table', 1),
	(39, '2025_10_16_185256_create_rutas_table', 1),
	(40, '2025_10_16_185305_create_turnos_table', 1),
	(41, '2025_10_16_185311_create_programaciones_table', 1),
	(42, '2025_10_16_185318_create_recorridos_table', 1),
	(43, '2025_10_16_185328_create_recorrido_incidencias_table', 1),
	(44, '2025_10_21_044040_agregar_ocupacion', 2),
	(45, '2025_10_21_232828_add_logo_to_marcas_table', 3),
	(46, '2025_10_22_005250_add_capacidad_compactacion_combustible_to_vehiculos_table', 4),
	(47, '2025_10_22_024842_add_dias_totales_to_vacaciones_table', 5),
	(48, '2025_10_26_181104_remove_unique_constraint_from_asistencias_table', 6),
	(49, '2025_10_27_165747_add_campos_adicionales_to_contratos_table', 7),
	(50, '2025_10_27_172106_add_campos_contratos_con_departamento_fk', 8),
	(51, '2025_10_28_042718_create_turnos_table', 9),
	(52, '2025_10_28_042853_create_grupospersonal_table', 10),
	(53, '2025_10_28_043004_create_confgrupos_table', 11),
	(54, '2025_11_02_020635_create_motivos_table', 12),
	(55, '2025_11_02_024351_create_progrmacionpersonal_table', 13),
	(56, '2025_11_19_002702_create_mantenimientos_table', 14),
	(57, '2025_11_19_002856_create_dias_mantenimiento_table', 15),
	(58, '2025_11_19_010844_indice_horarios_mantenimiento', 16);

-- Volcando estructura para tabla plataformarsugrupo9.modelos
CREATE TABLE IF NOT EXISTS `modelos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `marca_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modelos_marca_id_foreign` (`marca_id`),
  CONSTRAINT `modelos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.modelos: ~16 rows (aproximadamente)
INSERT INTO `modelos` (`id`, `marca_id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'FM 370', 'Camión pesado para recolección', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(2, 1, 'FE 280', 'Camión mediano', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(3, 2, 'Atego 1726', 'Camión de distribución urbana', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(4, 2, 'Accelo 1016', 'Camión ligero', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(5, 3, 'Serie 300', 'Camión liviano para ciudad', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(6, 3, 'Serie 500', 'Camión mediano', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(7, 3, 'GH', 'Camión pesado', 0, '2025-10-17 06:16:35', '2025-10-20 01:54:46', '2025-10-20 01:54:46'),
	(8, 4, 'HD78', 'Camión ligero', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(9, 4, 'Mighty', 'Camión de carga', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(10, 5, 'N Series', 'Camión ligero económico', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(11, 5, 'K Series', 'Camión mediano', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(12, 6, 'NKR', 'Camión ligero urbano', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(13, 6, 'FVR', 'Camión pesado', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(14, 7, 'P 320', 'Camión para distribución', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(15, 7, 'G 410', 'Camión de construcción', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(16, 6, 'AMARANTO', 'csdsdcscd', 1, '2025-10-20 01:54:35', '2025-10-20 01:54:35', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.motivos
CREATE TABLE IF NOT EXISTS `motivos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.motivos: ~5 rows (aproximadamente)
INSERT INTO `motivos` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Contingencia operativa', 'Cambio debido a una situación imprevista en la operación', 1, '2025-11-02 18:49:17', '2025-11-02 18:49:17'),
	(2, 'Falla de vehículo', 'El vehículo asignado presenta fallas mecánicas', 1, '2025-11-02 18:49:17', '2025-11-02 18:49:17'),
	(3, 'Ausencia de personal', 'El personal asignado no puede asistir', 1, '2025-11-02 18:49:17', '2025-11-27 07:17:09'),
	(4, 'Emergencia médica', 'Personal con emergencia médica o de salud', 1, '2025-11-02 18:49:17', '2025-11-02 18:49:17'),
	(5, 'Reprogramación administrativa', 'Cambio solicitado por la administración', 1, '2025-11-02 18:49:17', '2025-11-02 18:49:17'),
	(6, 'Situacion Familiar', 'Situacion o emergencia familiar', 1, '2025-11-27 07:17:52', '2025-11-27 07:17:52');

-- Volcando estructura para tabla plataformarsugrupo9.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.personal
CREATE TABLE IF NOT EXISTS `personal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dni` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `licencia_conducir` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_vencimiento_licencia` date DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `funcion_id` bigint unsigned NOT NULL,
  `clave` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_dni_unique` (`dni`),
  UNIQUE KEY `personal_email_unique` (`email`),
  KEY `personal_funcion_id_foreign` (`funcion_id`),
  CONSTRAINT `personal_funcion_id_foreign` FOREIGN KEY (`funcion_id`) REFERENCES `funciones` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.personal: ~10 rows (aproximadamente)
INSERT INTO `personal` (`id`, `dni`, `nombres`, `apellido_paterno`, `apellido_materno`, `fecha_nacimiento`, `telefono`, `email`, `direccion`, `licencia_conducir`, `fecha_vencimiento_licencia`, `foto`, `funcion_id`, `clave`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, '12345678', 'Carlos Alberto', 'García', 'Pérez', '1985-03-15', '987654321', 'carlos.garcia@jlo.gob.pe', 'Av. Luis Gonzales 123, José Leonardo Ortiz', 'A-IIB-12345678', '2026-12-31', 'personal/personal_12345678_1761451382.jpg', 1, '$2y$12$vciCCQ4IJUevBe/fkd8eIev.qpRfTMZNC.LgWVacLeF63UDtVXovi', 1, '2025-10-17 06:16:35', '2025-11-26 06:34:56', NULL),
	(2, '23456789', 'Miguel Ángel', 'Rodríguez', 'Silva', '1990-07-22', '998877665', 'miguel.rodriguez@jlo.gob.pe', 'Jr. San Martín 456, José Leonardo Ortiz', 'A-IIB-23456789', '2027-06-30', NULL, 1, '$2y$12$heZrIkmvvLdb4Nmj0EIYaewTozWsq7FQwJNuU4Yoelil2QDg6bepu', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(3, '34567890', 'José Luis', 'Fernández', 'Torres', '1988-11-10', '955443322', 'jose.fernandez@jlo.gob.pe', 'Calle Los Pinos 789, José Leonardo Ortiz', 'A-IIB-34567890', '2025-12-27', 'personal/personal_34567890_1761697878.jpg', 1, '$2y$12$.U89L0GY6vN.mVUKHkoo1u3Nx47E1DBiL7bEDlyRg2X8viNnu1E6S', 1, '2025-10-17 06:16:36', '2025-10-29 05:31:20', NULL),
	(4, '45678901', 'Roberto Carlos', 'Mendoza', 'Vargas', '1992-05-18', '966554433', 'roberto.mendoza@jlo.gob.pe', 'Av. Chiclayo 234, José Leonardo Ortiz', 'A-IIB-45678901', '2028-09-20', NULL, 1, '$2y$12$FO4yk4P01S.BdSvlLA6hkOQdzlgF2T/g.v8ZN9Zc0tU6Tsu1uSmsy', 1, '2025-10-17 06:16:36', '2025-10-17 06:16:36', NULL),
	(5, '56789012', 'Juan Carlos', 'López', 'Ramírez', '1995-02-28', '977665544', 'juan.lopez@jlo.gob.pe', 'Jr. La Victoria 567, José Leonardo Ortiz', NULL, NULL, NULL, 2, '$2y$12$fmieSCUxMqnfX93O0j8nn.Mu48WvZBCmCWnbDVi5YTAPCADv3VtYK', 1, '2025-10-17 06:16:36', '2025-10-17 06:16:36', NULL),
	(6, '67890123', 'Pedro Antonio', 'Sánchez', 'Gutiérrez', '1993-08-14', '988776655', 'pedro.sanchez@jlo.gob.pe', 'Calle Real 890, José Leonardo Ortiz', NULL, NULL, NULL, 2, '$2y$12$GkdPVtTDmm3cirktlIgBruC2PAsmAbIZWts8TxB7GWRdWAIqJ8zjG', 1, '2025-10-17 06:16:36', '2025-10-17 06:16:36', NULL),
	(7, '78901234', 'Luis Alberto', 'Castro', 'Morales', '1996-12-05', '999887766', 'luis.castro@jlo.gob.pe', 'Av. Bolognesi 123, José Leonardo Ortiz', NULL, NULL, NULL, 2, '$2y$12$GLiFuVyjMJdb8cnsB78KT.J8vSoNTdLudW0hVUo/eOEfsjCypnEb2', 1, '2025-10-17 06:16:36', '2025-10-17 06:16:36', NULL),
	(8, '89012345', 'Mario Enrique', 'Vega', 'Ríos', '1994-04-25', '955667788', 'mario.vega@jlo.gob.pe', 'Jr. Tacna 456, José Leonardo Ortiz', NULL, NULL, NULL, 2, '$2y$12$s2yBGVADsAMmZngt/huJXO9K7XqYikCQ9baBVKTD7evABsziWUwOe', 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(9, '90123456', 'Ricardo Manuel', 'Díaz', 'Flores', '1980-09-30', '944556677', 'ricardo.diaz@jlo.gob.pe', 'Av. Grau 789, José Leonardo Ortiz', 'A-IIB-90123456', '2027-11-15', NULL, 3, '$2y$12$KOea0CQlS37Vj97yUDoRc.X6um3gHBKxXm3fiMrnvrx8eDfLPh2Am', 1, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(10, '73448893', 'EDWARD', 'sdsd', 'MALDONADO', '2004-10-08', '945406346', 'fabianhc2501@gmail.com', 'Urb Latina, Conquista\r\nCasa', NULL, NULL, NULL, 7, '$2y$12$xJHzsasnXksx.CiN.1ALcusRHfRlUQYY7Jf.i4apg4CsPy4U74QcG', 0, '2025-10-26 09:06:00', '2025-10-27 20:31:30', '2025-10-27 20:31:30');

-- Volcando estructura para tabla plataformarsugrupo9.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.personal_access_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.programaciones
CREATE TABLE IF NOT EXISTS `programaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grupo_id` bigint unsigned NOT NULL,
  `turno_id` bigint unsigned NOT NULL,
  `zona_id` bigint unsigned NOT NULL,
  `vehiculo_id` bigint unsigned DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `status` int DEFAULT '1' COMMENT '0=Cancelada, 1=Programada, 2=Iniciada, 3=Completada, 4=Reprogramada',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_programaciones_grupo` (`grupo_id`),
  KEY `fk_programaciones_turno` (`turno_id`),
  KEY `fk_programaciones_zona` (`zona_id`),
  KEY `fk_programaciones_vehiculo` (`vehiculo_id`),
  CONSTRAINT `fk_programaciones_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupospersonal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_programaciones_turno` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_programaciones_vehiculo` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_programaciones_zona` FOREIGN KEY (`zona_id`) REFERENCES `zonas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.programaciones: ~43 rows (aproximadamente)
INSERT INTO `programaciones` (`id`, `grupo_id`, `turno_id`, `zona_id`, `vehiculo_id`, `fecha_inicio`, `fecha_fin`, `status`, `notes`, `created_at`, `updated_at`) VALUES
	(40, 2, 1, 1, 1, '2025-11-03', '2025-11-03', 1, NULL, '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(41, 2, 4, 1, 1, '2025-11-10', '2025-11-10', 4, NULL, '2025-11-05 05:26:21', '2025-11-05 05:26:56'),
	(42, 2, 1, 1, 1, '2025-11-04', '2025-11-04', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(43, 2, 1, 1, 1, '2025-11-06', '2025-11-06', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(44, 2, 1, 1, 1, '2025-11-10', '2025-11-10', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(45, 2, 1, 1, 1, '2025-11-11', '2025-11-11', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(46, 2, 1, 1, 1, '2025-11-14', '2025-11-14', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(47, 2, 1, 1, 1, '2025-11-17', '2025-11-17', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(48, 2, 1, 1, 1, '2025-11-18', '2025-11-18', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(49, 2, 1, 1, 1, '2025-11-20', '2025-11-20', 1, NULL, '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(50, 2, 1, 1, 2, '2025-11-24', '2025-11-24', 4, NULL, '2025-11-05 07:23:38', '2025-11-14 04:53:58'),
	(51, 2, 1, 1, 2, '2025-11-25', '2025-11-25', 4, NULL, '2025-11-05 07:23:38', '2025-11-14 04:53:59'),
	(52, 2, 4, 1, 2, '2025-11-27', '2025-11-27', 4, NULL, '2025-11-05 07:23:38', '2025-11-05 07:25:43'),
	(53, 2, 1, 1, 2, '2025-12-01', '2025-12-01', 4, NULL, '2025-11-14 06:25:04', '2025-11-26 10:59:32'),
	(54, 2, 1, 1, 2, '2025-12-02', '2025-12-02', 4, NULL, '2025-11-14 06:25:04', '2025-11-14 06:31:46'),
	(55, 2, 1, 1, 2, '2025-12-04', '2025-12-04', 4, NULL, '2025-11-14 06:25:04', '2025-11-14 06:31:46'),
	(56, 2, 1, 1, 1, '2025-12-08', '2025-12-08', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(57, 2, 1, 1, 1, '2025-12-09', '2025-12-09', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(58, 2, 1, 1, 1, '2025-12-10', '2025-12-10', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(59, 2, 1, 1, 1, '2025-12-11', '2025-12-11', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(60, 5, 1, 2, 3, '2025-12-08', '2025-12-08', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(61, 5, 1, 2, 3, '2025-12-09', '2025-12-09', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(62, 5, 1, 2, 3, '2025-12-10', '2025-12-10', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(63, 5, 1, 2, 3, '2025-12-11', '2025-12-11', 1, NULL, '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(64, 2, 1, 1, 1, '2025-11-05', '2025-11-05', 1, NULL, '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(65, 2, 1, 1, 1, '2025-11-12', '2025-11-12', 1, NULL, '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(66, 2, 1, 1, 1, '2025-11-13', '2025-11-13', 1, NULL, '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(67, 5, 1, 2, 3, '2025-11-03', '2025-11-03', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(68, 5, 1, 2, 3, '2025-11-04', '2025-11-04', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(69, 5, 1, 2, 3, '2025-11-05', '2025-11-05', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(70, 5, 1, 2, 3, '2025-11-06', '2025-11-06', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(71, 5, 1, 2, 3, '2025-11-10', '2025-11-10', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(72, 5, 1, 2, 3, '2025-11-11', '2025-11-11', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(73, 5, 1, 2, 3, '2025-11-12', '2025-11-12', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(74, 5, 1, 2, 3, '2025-11-13', '2025-11-13', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(75, 5, 1, 2, 3, '2025-11-17', '2025-11-17', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(76, 5, 1, 2, 3, '2025-11-18', '2025-11-18', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(77, 5, 1, 2, 3, '2025-11-19', '2025-11-19', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(78, 5, 1, 2, 3, '2025-11-20', '2025-11-20', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(79, 5, 1, 2, 3, '2025-11-24', '2025-11-24', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(80, 5, 1, 2, 3, '2025-11-25', '2025-11-25', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(81, 5, 1, 2, 3, '2025-11-26', '2025-11-26', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(82, 5, 1, 2, 3, '2025-11-27', '2025-11-27', 1, NULL, '2025-11-26 07:36:43', '2025-11-26 07:36:43');

-- Volcando estructura para tabla plataformarsugrupo9.programacion_personal
CREATE TABLE IF NOT EXISTS `programacion_personal` (
  `personal_id` bigint unsigned NOT NULL,
  `programacion_id` bigint unsigned NOT NULL,
  `fecha_dia` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`personal_id`,`programacion_id`,`fecha_dia`),
  KEY `programacion_id` (`programacion_id`),
  CONSTRAINT `programacion_personal_ibfk_1` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `programacion_personal_ibfk_2` FOREIGN KEY (`programacion_id`) REFERENCES `programaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.programacion_personal: ~125 rows (aproximadamente)
INSERT INTO `programacion_personal` (`personal_id`, `programacion_id`, `fecha_dia`, `created_at`, `updated_at`) VALUES
	(1, 53, '2025-12-01', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(1, 54, '2025-12-02', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(1, 55, '2025-12-04', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(1, 56, '2025-12-08', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 57, '2025-12-09', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 58, '2025-12-10', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 59, '2025-12-11', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 60, '2025-12-08', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 61, '2025-12-09', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 62, '2025-12-10', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(1, 63, '2025-12-11', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(2, 40, '2025-11-03', '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(2, 41, '2025-11-10', '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(2, 42, '2025-11-04', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 43, '2025-11-06', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 44, '2025-11-10', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 45, '2025-11-11', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 46, '2025-11-13', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 47, '2025-11-17', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 48, '2025-11-18', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 49, '2025-11-20', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 50, '2025-11-24', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 51, '2025-11-25', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 52, '2025-11-27', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(2, 64, '2025-11-05', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(2, 65, '2025-11-12', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(2, 66, '2025-11-13', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(2, 67, '2025-11-03', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 68, '2025-11-04', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 69, '2025-11-05', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 70, '2025-11-06', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 71, '2025-11-10', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 72, '2025-11-11', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 73, '2025-11-12', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 74, '2025-11-13', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 75, '2025-11-17', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 76, '2025-11-18', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 77, '2025-11-19', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 78, '2025-11-20', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 79, '2025-11-24', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 80, '2025-11-25', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 81, '2025-11-26', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(2, 82, '2025-11-27', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(5, 40, '2025-11-03', '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(5, 41, '2025-11-10', '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(5, 42, '2025-11-04', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 43, '2025-11-06', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 44, '2025-11-10', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 45, '2025-11-11', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 46, '2025-11-13', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 47, '2025-11-17', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 48, '2025-11-18', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 49, '2025-11-20', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 50, '2025-11-24', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(5, 51, '2025-11-25', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(6, 53, '2025-12-01', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(6, 54, '2025-12-02', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(6, 55, '2025-12-04', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(6, 56, '2025-12-08', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 57, '2025-12-09', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 58, '2025-12-10', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 59, '2025-12-11', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 60, '2025-12-08', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 61, '2025-12-09', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 62, '2025-12-10', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 63, '2025-12-11', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(6, 64, '2025-11-05', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(6, 65, '2025-11-12', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(6, 66, '2025-11-13', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(6, 67, '2025-11-03', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 68, '2025-11-04', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 69, '2025-11-05', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 70, '2025-11-06', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 71, '2025-11-10', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 72, '2025-11-11', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 73, '2025-11-12', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 74, '2025-11-13', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 75, '2025-11-17', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 76, '2025-11-18', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 77, '2025-11-19', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 78, '2025-11-20', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 79, '2025-11-24', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 80, '2025-11-25', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(6, 81, '2025-11-26', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 40, '2025-11-03', '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(7, 41, '2025-11-10', '2025-11-05 05:26:21', '2025-11-05 05:26:21'),
	(7, 42, '2025-11-04', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 43, '2025-11-06', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 44, '2025-11-10', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 45, '2025-11-11', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 46, '2025-11-13', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 47, '2025-11-17', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 48, '2025-11-18', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 49, '2025-11-20', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 50, '2025-11-24', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 51, '2025-11-25', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 52, '2025-11-27', '2025-11-05 07:23:38', '2025-11-05 07:23:38'),
	(7, 53, '2025-12-01', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(7, 54, '2025-12-02', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(7, 55, '2025-12-04', '2025-11-14 06:25:04', '2025-11-14 06:25:04'),
	(7, 60, '2025-12-08', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(7, 61, '2025-12-09', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(7, 62, '2025-12-10', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(7, 63, '2025-12-11', '2025-11-23 07:08:20', '2025-11-23 07:08:20'),
	(7, 64, '2025-11-05', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(7, 65, '2025-11-12', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(7, 66, '2025-11-13', '2025-11-26 04:30:24', '2025-11-26 04:30:24'),
	(7, 67, '2025-11-03', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 68, '2025-11-04', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 69, '2025-11-05', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 70, '2025-11-06', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 71, '2025-11-10', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 72, '2025-11-11', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 73, '2025-11-12', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 74, '2025-11-13', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 75, '2025-11-17', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 76, '2025-11-18', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 77, '2025-11-19', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 78, '2025-11-20', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 79, '2025-11-24', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 80, '2025-11-25', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 81, '2025-11-26', '2025-11-26 07:36:43', '2025-11-26 07:36:43'),
	(7, 82, '2025-11-27', '2025-11-26 07:36:43', '2025-11-26 07:36:43');

-- Volcando estructura para tabla plataformarsugrupo9.provincias
CREATE TABLE IF NOT EXISTS `provincias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `departamento_id` bigint unsigned NOT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provincias_codigo_unique` (`codigo`),
  KEY `provincias_departamento_id_foreign` (`departamento_id`),
  CONSTRAINT `provincias_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.provincias: ~0 rows (aproximadamente)
INSERT INTO `provincias` (`id`, `departamento_id`, `codigo`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'CHI', 'Chiclayo', 1, '2025-10-24 01:55:57', '2025-10-24 01:55:57');

-- Volcando estructura para tabla plataformarsugrupo9.recorridos
CREATE TABLE IF NOT EXISTS `recorridos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.recorridos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.recorrido_incidencias
CREATE TABLE IF NOT EXISTS `recorrido_incidencias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.recorrido_incidencias: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.rutas
CREATE TABLE IF NOT EXISTS `rutas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zona_id` bigint unsigned NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `punto_partida_nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `punto_partida_latitud` decimal(10,7) DEFAULT NULL,
  `punto_partida_longitud` decimal(10,7) DEFAULT NULL,
  `punto_fin_nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `punto_fin_latitud` decimal(10,7) DEFAULT NULL,
  `punto_fin_longitud` decimal(10,7) DEFAULT NULL,
  `trayecto` json DEFAULT NULL COMMENT 'Array de coordenadas del recorrido completo',
  `distancia_km` decimal(8,2) DEFAULT NULL COMMENT 'Distancia en kilómetros',
  `tiempo_estimado_minutos` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rutas_codigo_unique` (`codigo`),
  KEY `rutas_zona_id_foreign` (`zona_id`),
  CONSTRAINT `rutas_zona_id_foreign` FOREIGN KEY (`zona_id`) REFERENCES `zonas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.rutas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla plataformarsugrupo9.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.sessions: ~3 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('UrHqjwWKyZHe5SN6TGOZtgEOotEB8hnv2iUf2s8N', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiM25oQlkyUVNBRkJGdUJ0N3VtWkNpTUplM21wSElKWFlrTU1BNnQ2diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9wbGF0YWZvcm1hcnN1Z3J1cG85LnRlc3QvY2FtYmlvcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEyJDY5S0FNNW50VVhhM2R4M0twc3d1eC42RWdOdk1yVzlYZGtxUW12WWxsd2xZenhxRVh6R2tLIjt9', 1764218312);

-- Volcando estructura para tabla plataformarsugrupo9.tipos_vehiculo
CREATE TABLE IF NOT EXISTS `tipos_vehiculo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.tipos_vehiculo: ~8 rows (aproximadamente)
INSERT INTO `tipos_vehiculo` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Camión Compactador', 'Vehículo especializado para compactar residuos sólidos', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(2, 'Camión Baranda', 'Camión con baranda para transporte de residuos voluminosos', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(3, 'Camión Tolva', 'Camión con tolva volcadora para residuos', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(4, 'Camioneta', 'Vehículo ligero para zonas de difícil acceso', 1, '2025-10-17 06:16:35', '2025-10-20 01:55:24', NULL),
	(5, 'Camión Cisterna', 'Vehículo para limpieza y riego de calles', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(6, 'Motocarga', 'Vehículo motorizado de tres ruedas para recolección menor', 1, '2025-10-17 06:16:35', '2025-10-17 06:16:35', NULL),
	(7, 'cds', 'cdscsd', 1, '2025-10-20 01:54:58', '2025-10-20 01:55:31', '2025-10-20 01:55:31'),
	(8, 'cd', 'cdcd', 0, '2025-10-21 00:08:09', '2025-10-21 00:08:59', '2025-10-21 00:08:59');

-- Volcando estructura para tabla plataformarsugrupo9.turnos
CREATE TABLE IF NOT EXISTS `turnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `hour_in` time NOT NULL,
  `hour_out` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.turnos: ~1 rows (aproximadamente)
INSERT INTO `turnos` (`id`, `name`, `description`, `hour_in`, `hour_out`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Mañana', 'turno en la mañana', '07:00:00', '12:00:00', 1, '2025-10-28 10:11:32', '2025-10-28 10:28:02', NULL),
	(4, 'tarde', 'Turno en la tarde', '13:00:00', '18:00:00', 1, '2025-10-28 10:29:48', '2025-10-28 18:16:39', NULL),
	(5, 'media tarde', NULL, '03:00:00', '05:00:00', 1, '2025-11-05 04:34:28', '2025-11-05 04:34:28', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.users: ~0 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
	(1, 'Fabian Guillermo', 'fabianhc2501@gmail.com', NULL, '$2y$12$69KAM5ntUXa3dx3Kpswux.6EgNvMrW9XdkqQmvYllwlYzxqEXzGkK', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-17 04:16:22', '2025-10-17 04:16:22');

-- Volcando estructura para tabla plataformarsugrupo9.vacaciones
CREATE TABLE IF NOT EXISTS `vacaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personal_id` bigint unsigned NOT NULL,
  `anio` year NOT NULL,
  `dias_totales` int NOT NULL DEFAULT '30' COMMENT 'Máximo 30 días por año',
  `dias_programados` int NOT NULL DEFAULT '0' COMMENT 'Días de vacaciones programadas',
  `dias_pendientes` int NOT NULL DEFAULT '30' COMMENT 'Días pendientes por tomar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vacaciones_personal_id_anio_unique` (`personal_id`,`anio`),
  CONSTRAINT `vacaciones_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.vacaciones: ~12 rows (aproximadamente)
INSERT INTO `vacaciones` (`id`, `personal_id`, `anio`, `dias_totales`, `dias_programados`, `dias_pendientes`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, '2024', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-24 06:08:08', NULL),
	(2, 1, '2025', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(3, 2, '2024', 30, 15, 15, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(4, 2, '2025', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(5, 4, '2024', 30, 15, 15, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(6, 4, '2025', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(7, 5, '2024', 30, 15, 15, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(8, 5, '2025', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(9, 7, '2024', 30, 15, 15, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(10, 7, '2025', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(11, 9, '2024', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-27 07:39:33', NULL),
	(12, 9, '2025', 30, 0, 30, '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL);

-- Volcando estructura para tabla plataformarsugrupo9.vacaciones_periodos
CREATE TABLE IF NOT EXISTS `vacaciones_periodos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vacaciones_id` bigint unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_utilizados` int NOT NULL,
  `estado` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacaciones_periodos_vacaciones_id_foreign` (`vacaciones_id`),
  CONSTRAINT `vacaciones_periodos_vacaciones_id_foreign` FOREIGN KEY (`vacaciones_id`) REFERENCES `vacaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.vacaciones_periodos: ~7 rows (aproximadamente)
INSERT INTO `vacaciones_periodos` (`id`, `vacaciones_id`, `fecha_inicio`, `fecha_fin`, `dias_utilizados`, `estado`, `observaciones`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, '2025-11-08', '2025-11-25', 17, 'aprobado', 'Vacaciones de medio año', '2025-10-17 06:16:37', '2025-10-29 07:15:38', NULL),
	(2, 3, '2024-07-15', '2024-07-29', 15, 'finalizado', 'Vacaciones de medio año', '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(3, 5, '2024-07-15', '2024-07-29', 15, 'finalizado', 'Vacaciones de medio año', '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(4, 7, '2024-07-15', '2024-07-29', 15, 'finalizado', 'Vacaciones de medio año', '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(5, 9, '2024-07-15', '2024-07-29', 15, 'finalizado', 'Vacaciones de medio año', '2025-10-17 06:16:37', '2025-10-17 06:16:37', NULL),
	(6, 11, '2025-12-03', '2025-12-17', 15, 'aprobado', 'Vacaciones de final de año', '2025-10-17 06:16:37', '2025-10-27 07:43:02', NULL),
	(7, 10, '2025-12-08', '2025-12-23', 15, 'aprobado', 'nt', '2025-10-29 07:11:36', '2025-11-26 04:05:03', '2025-11-26 04:05:03');

-- Volcando estructura para tabla plataformarsugrupo9.vehiculos
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `placa` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca_id` bigint unsigned NOT NULL,
  `modelo_id` bigint unsigned NOT NULL,
  `tipo_vehiculo_id` bigint unsigned NOT NULL,
  `color_id` bigint unsigned NOT NULL,
  `anio` year NOT NULL,
  `numero_motor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacidad_carga` decimal(8,2) DEFAULT NULL COMMENT 'En toneladas',
  `capacidad_ocupacion` int DEFAULT NULL,
  `capacidad_compactacion` decimal(10,2) DEFAULT NULL,
  `capacidad_combustible` decimal(10,2) DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehiculos_codigo_unique` (`codigo`),
  UNIQUE KEY `vehiculos_placa_unique` (`placa`),
  KEY `vehiculos_marca_id_foreign` (`marca_id`),
  KEY `vehiculos_modelo_id_foreign` (`modelo_id`),
  KEY `vehiculos_tipo_vehiculo_id_foreign` (`tipo_vehiculo_id`),
  KEY `vehiculos_color_id_foreign` (`color_id`),
  CONSTRAINT `vehiculos_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colores` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `vehiculos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `vehiculos_modelo_id_foreign` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `vehiculos_tipo_vehiculo_id_foreign` FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipos_vehiculo` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.vehiculos: ~8 rows (aproximadamente)
INSERT INTO `vehiculos` (`id`, `codigo`, `placa`, `marca_id`, `modelo_id`, `tipo_vehiculo_id`, `color_id`, `anio`, `numero_motor`, `nombre`, `capacidad_carga`, `capacidad_ocupacion`, `capacidad_compactacion`, `capacidad_combustible`, `observaciones`, `disponible`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'RSU-001', 'T1X-234', 1, 1, 1, 7, '2021', NULL, 'VCH-2020-FM370-001', 12.00, 3, 3.00, 3.00, 'Vehículo en óptimas condiciones', 1, 1, '2025-10-17 06:16:35', '2025-10-29 05:32:04', NULL),
	(2, 'RSU-002', 'ABC-123', 3, 6, 1, 7, '2019', NULL, 'HCH-2019-500-002', 10.00, 3, 3.00, 3.00, NULL, 1, 1, '2025-10-17 06:16:35', '2025-10-29 05:32:20', NULL),
	(3, 'RSU-003', 'XYZ-789', 2, 3, 2, 1, '2021', NULL, 'MBCH-2021-AT-003', 8.00, 3, 3.00, 3.00, 'Requiere mantenimiento programado', 1, 1, '2025-10-17 06:16:35', '2025-10-29 05:32:41', NULL),
	(4, 'RSU-004', 'P4Q-567', 4, 8, 3, 8, '2022', NULL, 'HYCH-2022-HD78-004', 6.00, 3, 3.00, 3.00, NULL, 1, 1, '2025-10-17 06:16:35', '2025-10-29 05:32:54', NULL),
	(5, 'RSU-005', 'RST-456', 5, 10, 4, 5, '2023', NULL, 'JCCH-2023-NS-005', 2.00, 3, 3.00, 3.00, 'Vehículo nuevo para zonas estrechas', 1, 1, '2025-10-17 06:16:35', '2025-10-29 05:33:13', NULL),
	(6, 'RSU-006', 'UVW-321', 6, 12, 1, 11, '2019', NULL, 'IZCH-2018-NKR-006', 17.00, 5, 5.00, 2.00, 'En revisión técnica', 0, 1, '2025-10-17 06:16:35', '2025-10-29 05:33:40', NULL),
	(7, '5079', 'T1X-287', 3, 7, 2, 10, '1992', 'VM-FH12-2020-003', 'VCH-2020-FM370-003', 0.03, 3, NULL, NULL, 'sadasd', 0, 1, '2025-10-17 07:34:39', '2025-10-18 01:41:11', '2025-10-18 01:41:11'),
	(8, 'FDFD', 'T1X-271', 4, 8, 5, 6, '1990', 'VM-FH12-2020-007', 'VCH-2020-FM370-097', 0.04, 1, NULL, NULL, 'fd', 0, 1, '2025-10-21 10:12:28', '2025-10-21 10:12:48', '2025-10-21 10:12:48');

-- Volcando estructura para tabla plataformarsugrupo9.vehiculo_imagenes
CREATE TABLE IF NOT EXISTS `vehiculo_imagenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehiculo_id` bigint unsigned NOT NULL,
  `ruta_imagen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_perfil` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehiculo_imagenes_vehiculo_id_foreign` (`vehiculo_id`),
  CONSTRAINT `vehiculo_imagenes_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.vehiculo_imagenes: ~2 rows (aproximadamente)
INSERT INTO `vehiculo_imagenes` (`id`, `vehiculo_id`, `ruta_imagen`, `es_perfil`, `orden`, `created_at`, `updated_at`) VALUES
	(1, 1, 'vehiculos/mGRGL8m8qzSVF2uDex32GjrZyj5UpvSqIlePnE9P.jpg', 1, 1, '2025-10-17 07:05:52', '2025-10-17 07:05:52'),
	(2, 1, 'vehiculos/JdShktXr2gxVUa9vNP1SKEffqdgay8RAOpjYySEb.jpg', 0, 2, '2025-10-17 07:25:33', '2025-10-17 07:25:33');

-- Volcando estructura para tabla plataformarsugrupo9.zonas
CREATE TABLE IF NOT EXISTS `zonas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distrito_id` bigint unsigned NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `perimetro` json DEFAULT NULL COMMENT 'Coordenadas del perímetro en formato GeoJSON',
  `area` decimal(10,2) DEFAULT NULL COMMENT 'Área en km²',
  `poblacion_estimada` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zonas_codigo_unique` (`codigo`),
  KEY `zonas_distrito_id_foreign` (`distrito_id`),
  CONSTRAINT `zonas_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla plataformarsugrupo9.zonas: ~4 rows (aproximadamente)
INSERT INTO `zonas` (`id`, `codigo`, `nombre`, `distrito_id`, `descripcion`, `perimetro`, `area`, `poblacion_estimada`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, '001', 'zona1', 2, 'ddef', '"{\\"type\\":\\"Feature\\",\\"properties\\":{},\\"geometry\\":{\\"type\\":\\"Polygon\\",\\"coordinates\\":[[[-79.842568,-6.770104],[-79.842541,-6.771457],[-79.840913,-6.771511],[-79.840785,-6.770083],[-79.842568,-6.770104]]]}}"', 0.03, 11, 1, '2025-10-24 05:42:10', '2025-10-24 05:43:12', NULL),
	(2, '002', 'zona2', 2, 'zona2', '"{\\"type\\":\\"Feature\\",\\"properties\\":{},\\"geometry\\":{\\"type\\":\\"Polygon\\",\\"coordinates\\":[[[-79.840564,-6.769955],[-79.840597,-6.771767],[-79.838016,-6.771894],[-79.838156,-6.770184],[-79.840564,-6.769955]]]}}"', 0.05, 11, 1, '2025-10-24 05:46:34', '2025-10-29 06:59:17', NULL),
	(3, '2657', 'Santa Ana', 2, NULL, '"{\\"type\\":\\"Feature\\",\\"properties\\":{},\\"geometry\\":{\\"type\\":\\"Polygon\\",\\"coordinates\\":[[[-79.86643,-6.75738],[-79.861172,-6.758552],[-79.862181,-6.760811],[-79.866967,-6.759617],[-79.86643,-6.75738]]]}}"', 0.15, 556, 1, '2025-10-29 07:01:45', '2025-10-29 07:04:23', NULL),
	(4, '265788', 'zona4', 2, NULL, '"{\\"type\\":\\"Feature\\",\\"properties\\":{},\\"geometry\\":{\\"type\\":\\"Polygon\\",\\"coordinates\\":[[[-79.843041,-6.764578],[-79.841796,-6.764876],[-79.841904,-6.766432],[-79.843234,-6.766197],[-79.843041,-6.764578]]]}}"', 0.03, 888, 1, '2025-10-29 07:03:33', '2025-10-29 07:04:49', NULL),
	(5, 'zona4', 'zona 4', 2, 'zona 4', '"{\\"type\\":\\"Feature\\",\\"properties\\":{},\\"geometry\\":{\\"type\\":\\"Polygon\\",\\"coordinates\\":[[[-79.844479,-6.768465],[-79.839951,-6.76872],[-79.840144,-6.767271],[-79.844436,-6.767186],[-79.844479,-6.768465]]]}}"', 0.07, 4, 1, '2025-10-29 07:09:21', '2025-10-29 07:09:21', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
