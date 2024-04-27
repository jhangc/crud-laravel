-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-04-2024 a las 22:26:09
-- Versión del servidor: 8.2.0
-- Versión de PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crud_laravel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_identidad` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `direccion_laboral` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lugar_nacimiento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `profesion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_civil` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conyugue` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dni_conyugue` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` blob,
  `dni_pdf` blob,
  PRIMARY KEY (`id`),
  KEY `clientes_sucursal_id_foreign` (`sucursal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `sucursal_id`, `nombre`, `documento_identidad`, `telefono`, `email`, `direccion`, `activo`, `created_at`, `updated_at`, `direccion_laboral`, `lugar_nacimiento`, `fecha_nacimiento`, `profesion`, `estado_civil`, `conyugue`, `dni_conyugue`, `foto`, `dni_pdf`) VALUES
(1, 1, 'JHAN MICHAEL GARCIA CAMPOVERDE', '75618866', '951741427', 'gcjhan2001@gmail.com', '612', 0, '2024-04-18 08:51:03', '2024-04-27 05:46:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'CLIENTE 2', '74215148', '987458414', 'cliente1@cliente.com', 'fedfdsfd', 0, '2024-04-18 08:52:50', '2024-04-27 05:45:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'CLIENTE 3', '75854565', '951741427', 'cliente2@cliente.com', '6125452', 0, '2024-04-18 09:15:21', '2024-04-18 09:15:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 'JHAN MICHAEL GARCIA', '74215148', '951741427', 'juanperez@gmail.com', '612', 0, '2024-04-19 06:18:08', '2024-04-27 05:45:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 'JHAN MICHAEL GARCIA', '75618866', '951741427', 'gcjhan200gfdgf1@gmail.com', '612', 0, '2024-04-26 09:05:02', '2024-04-27 06:50:00', '612', 'gfdgdfgfd', '2024-04-25', 'gfdgdfg', 'Casado', '456122', NULL, 0x666f746f735f636c69656e7465732f495a537158434e355a54366546426a52654b617043744165674649376a504954394441764a5941422e6a7067, 0x646f63756d656e746f735f636c69656e7465732f724876725a306c354146474e386675364f785842684f3871314630395a32744f6a7a69643030414f2e706466),
(6, 1, 'JHAN MICHAEL GARCIA', '75618867', '951741427', 'gcjhanFDSFD2001@gmail.com', '612', 1, '2024-04-26 10:30:15', '2024-04-26 10:30:15', '612', 'FDSFDFD', '2004-05-02', 'FDSFDF', 'Soltero', 'FSDFDSFDSFD', NULL, 0x7075626c69632f666f746f735f636c69656e7465732f624d6f4161715139384a5a663371576a6a7742757644613342714e3231343976724a6f53617451372e6a7067, NULL),
(7, 1, 'gfdsgfds', 'gfdsgfsdg', 'fdgfsdg', 'fdsgsfdg@fdsfsdgsfdg', 'gfdsgfds', 0, '2024-04-27 05:43:40', '2024-04-27 05:45:33', 'gsdfgfdsg', 'fdsgsfdgdg', NULL, 'gfdgdg', 'Soltero', NULL, NULL, NULL, NULL),
(8, 1, 'prueba 03', '12345678', '987456321', 'prueba03@gmail.com', 'prueba', 1, '2024-04-27 05:45:23', '2024-04-27 05:45:23', 'prueba trabajo', 'nmo me acuerdo', NULL, 'agricultor', 'Casado', 'maria', '14562874', 0x7075626c69632f666f746f735f636c69656e7465732f4d3553347655347175583251673434784e597957415765523365353574676f467a67783345434b6e2e706e67, 0x646f63756d656e746f735f636c69656e7465732f7963415675446c55627075564e79426348785072544d7047515168304936783779726335494d68452e706466),
(9, 1, 'prueba 04', '75618888', '951741427', 'prueba04@gmail.com', 'dirccion de prueba', 1, '2024-04-27 06:44:16', '2024-04-27 06:44:16', 'direccion de prueba trabajo', 'chiclayo', NULL, 'agricultor', 'Casado', 'maria', '74586912', 0x7075626c69632f666f746f735f636c69656e7465732f54386b57364a595a35625a396e7177336f7a443264704d31365149656c766859577751414847346d2e6a7067, 0x646f63756d656e746f735f636c69656e7465732f556952624e7846373630504457466d464b4243585175377a66327843465a7133757476564a3757702e706466);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creditos`
--

DROP TABLE IF EXISTS `creditos`;
CREATE TABLE IF NOT EXISTS `creditos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `tipo_credito` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `tasa_interes` decimal(5,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `creditos_cliente_id_foreign` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuotas`
--

DROP TABLE IF EXISTS `cuotas`;
CREATE TABLE IF NOT EXISTS `cuotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `credito_id` int DEFAULT NULL,
  `numero_cuota` int DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `monto_cuota` decimal(10,2) DEFAULT NULL,
  `estado` varchar(50) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `pagada` tinyint(1) DEFAULT '0',
  `notas` text COLLATE utf8mb4_spanish_ci,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `credito_id` (`credito_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_04_10_064949_create_permission_tables', 1),
(7, '2024_04_10_074802_add_direccion_to_users_table', 2),
(8, '2024_04_18_023928_create_sucursales_table', 3),
(9, '2024_04_18_024150_create_clientes_table', 4),
(10, '2024_04_18_024844_create_sucursales_table', 5),
(11, '2024_04_18_025126_create_creditos_pagos_reportes_tables', 6),
(12, '2024_04_18_025855_add_foreign_keys_to_tables', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `credito_id` bigint UNSIGNED NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto_pago` decimal(10,2) NOT NULL,
  `mora` decimal(10,2) DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_credito_id_foreign` (`credito_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin.index', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(2, 'usuarios.index', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(3, 'usuarios.create', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(4, 'usuarios.store', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(5, 'usuarios.show', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(6, 'usuarios.edit', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(7, 'usuarios.update', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(8, 'usuarios.destroy', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(9, 'gestionar_usuarios', 'web', '2024-04-10 21:35:10', '2024-04-10 21:35:10'),
(10, 'ver_reportes', 'web', '2024-04-10 21:35:10', '2024-04-10 21:35:10'),
(11, 'editar_configuraciones', 'web', '2024-04-10 21:35:10', '2024-04-10 21:35:10'),
(12, 'aprobar_creditos', 'web', '2024-04-10 21:35:10', '2024-04-10 21:35:10'),
(13, 'supervisar_operaciones', 'web', '2024-04-10 21:35:10', '2024-04-10 21:35:10'),
(14, 'eliminar_registros', 'web', '2024-04-10 21:35:10', '2024-04-10 21:35:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

DROP TABLE IF EXISTS `reportes`;
CREATE TABLE IF NOT EXISTS `reportes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_reporte` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_generacion` date NOT NULL,
  `detalles` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(2, 'prestamista', 'web', '2024-04-10 12:19:56', '2024-04-10 12:19:56'),
(3, 'Administrador', 'web', '2024-04-10 21:13:12', '2024-04-10 21:13:12'),
(4, 'Asesor de creditos', 'web', '2024-04-10 21:13:12', '2024-04-10 21:13:12'),
(5, 'Cajera', 'web', '2024-04-10 21:13:12', '2024-04-10 21:13:12'),
(6, 'Gestor de Cobranza', 'web', '2024-04-10 21:13:12', '2024-04-10 21:13:12'),
(7, 'Contabilidad', 'web', '2024-04-10 21:13:12', '2024-04-10 21:13:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 3),
(14, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `email`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Tarapoto', 'Av. Tarapoto', '123456789', 'sucursalA@example.com', 1, '2024-04-18 03:48:05', '2024-04-18 03:48:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `direccion`) VALUES
(1, 'admin', 'admin@admin.com', NULL, '$2y$12$ryXxo0Vh76iHFg6AuL3O7.kLmCG23ATg1exe79xLYY3l7uDJyuN8G', NULL, '2024-04-10 12:19:56', '2024-04-10 12:19:56', NULL),
(4, 'prueba2', 'prueba2@prueba2.com', NULL, '$2y$12$vCh1RUYJxlxS63rJMQaOxOl.Kl4m3.eiLTn8ADK4XdxrLAus2BRAW', NULL, '2024-04-10 13:02:26', '2024-04-10 13:02:26', 'direccion de prueba 2'),
(5, 'admin2', 'admin2@admin.com', NULL, '$2y$12$DZ7OIpL3OoePn9ckhBrPLOgD2aGfoObACJy.A5oZ8c6Z2exZP154a', NULL, '2024-04-10 13:10:43', '2024-04-10 13:10:43', 'prueba de direccion admin2'),
(6, 'prestamista22', 'prestamista22@gmail.com', NULL, '$2y$12$Hk7gEYZMcByXbHAH/OP.h.n.ssQDaE/17k9TwgAysviobnS3s/Fqa', NULL, '2024-04-10 13:13:56', '2024-04-10 13:13:56', 'dadsadas'),
(8, 'JHAN MICHAEL GARCIA', 'gcjhan2001@gmail.com', NULL, '$2y$12$fOcM1bflb55tS0Wgw3AJz.UtZaXGjhJRk4p2jP/HZddP6PGzA6qSO', NULL, '2024-04-10 13:17:38', '2024-04-10 13:17:38', '612'),
(11, 'prueba20', 'prueba20@prueba20.com', NULL, '$2y$12$1ZNBBfAD.tJTUm59JOqON.wbETFJJ2DsazwQPuDque/s.mOV1Ak1O', NULL, '2024-04-10 21:39:14', '2024-04-10 21:39:14', 'edfrewrew');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `creditos`
--
ALTER TABLE `creditos`
  ADD CONSTRAINT `creditos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_credito_id_foreign` FOREIGN KEY (`credito_id`) REFERENCES `creditos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
