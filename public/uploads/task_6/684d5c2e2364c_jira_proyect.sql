-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 10-06-2025 a las 14:45:03
-- Versión del servidor: 8.0.30
-- Versión de PHP: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `jira_proyect`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configurations`
--

CREATE TABLE `configurations` (
  `id` int UNSIGNED NOT NULL,
  `name_app` varchar(45) NOT NULL,
  `icon_app` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `intro` text,
  `footer` text,
  `register` enum('active','inactive') NOT NULL DEFAULT 'active',
  `meta_description` text,
  `meta_keywords` text,
  `background_image` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `background_img_vertical` varchar(255) DEFAULT NULL,
  `primary_color` varchar(100) NOT NULL,
  `secundary_color` varchar(100) NOT NULL,
  `captcha` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `configurations`
--

INSERT INTO `configurations` (`id`, `name_app`, `icon_app`, `email`, `intro`, `footer`, `register`, `meta_description`, `meta_keywords`, `background_image`, `favicon`, `background_img_vertical`, `primary_color`, `secundary_color`, `captcha`) VALUES
(1, 'Iplanet', '', '', NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '66bb6a', '', 'active');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE `menus` (
  `id` int UNSIGNED NOT NULL,
  `option` varchar(40) NOT NULL,
  `url` varchar(100) NOT NULL,
  `icon` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `position` int DEFAULT NULL,
  `type` enum('primario','secundario') NOT NULL DEFAULT 'primario',
  `references` int DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `component` enum('table','controller') NOT NULL DEFAULT 'table',
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `table` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id`, `option`, `url`, `icon`, `position`, `type`, `references`, `status`, `component`, `title`, `description`, `table`) VALUES
(1, 'Lista de tareas', 'task/list', 'ri-list-ordered', 1, 'primario', NULL, 'active', 'controller', NULL, NULL, NULL),
(2, 'Tablero', 'task/tablero', 'ri-table-line', 2, 'primario', NULL, 'active', 'controller', NULL, NULL, NULL),
(3, 'Configuraciones tareas', '', 'ri-settings-5-line', 3, 'primario', NULL, 'active', 'table', NULL, NULL, NULL),
(4, 'Estados', 'task_states', NULL, 1, 'secundario', 3, 'active', 'table', 'Estados de tareas', NULL, 'task_states'),
(5, 'Sprints', 'task_sprints', NULL, 2, 'secundario', 3, 'active', 'table', 'Sprints', NULL, 'task_sprints'),
(6, 'Actividades', 'task_activities', NULL, 3, 'secundario', 3, 'active', 'table', 'Actividades', NULL, 'task_activities');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2020-06-20-144019', 'App\\Database\\Migrations\\Roles', 'default', 'App', 1749194875, 1),
(2, '2020-06-20-144024', 'App\\Database\\Migrations\\Users', 'default', 'App', 1749194875, 1),
(3, '2020-06-20-150439', 'App\\Database\\Migrations\\Menus', 'default', 'App', 1749194875, 1),
(4, '2020-06-20-150449', 'App\\Database\\Migrations\\Permissions', 'default', 'App', 1749194875, 1),
(5, '2020-06-22-212120', 'App\\Database\\Migrations\\Notification', 'default', 'App', 1749194875, 1),
(6, '2020-06-23-162342', 'App\\Database\\Migrations\\Configuration', 'default', 'App', 1749194875, 1),
(7, '2024-07-18-213057', 'App\\Database\\Migrations\\Passwords', 'default', 'App', 1749194875, 1),
(12, '2025-06-06-071950', 'App\\Database\\Migrations\\TaskState', 'default', 'App', 1749196219, 2),
(13, '2025-06-06-072001', 'App\\Database\\Migrations\\TaskSprint', 'default', 'App', 1749196219, 2),
(14, '2025-06-06-072006', 'App\\Database\\Migrations\\TaskActivity', 'default', 'App', 1749196219, 2),
(16, '2025-06-06-072009', 'App\\Database\\Migrations\\Task', 'default', 'App', 1749199034, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `icon` varchar(45) NOT NULL,
  `color` enum('','cyan','amber','orange','purple','red darken-1') NOT NULL DEFAULT 'cyan',
  `created_at` datetime DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `passwords`
--

CREATE TABLE `passwords` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `temporary` enum('Si','No') NOT NULL DEFAULT 'No',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `passwords`
--

INSERT INTO `passwords` (`id`, `user_id`, `password`, `attempts`, `temporary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '$2y$10$NKQJmyXNhetlSm6j6yhQX.iIOqpKNpeaLYzJWihDaL2F0D0JLPFzm', 0, 'No', 'active', '2025-06-06 07:28:42', '2025-06-06 07:28:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int UNSIGNED NOT NULL,
  `role_id` int UNSIGNED NOT NULL,
  `menu_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Superadministrador'),
(2, 'Administrador'),
(3, 'usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` int UNSIGNED NOT NULL,
  `task_state_id` int UNSIGNED DEFAULT NULL,
  `task_sprint_id` int UNSIGNED DEFAULT NULL,
  `task_activity_id` int UNSIGNED DEFAULT NULL,
  `task_user_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `orden` int NOT NULL DEFAULT '0',
  `date_task` date DEFAULT NULL,
  `date_state` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_activities`
--

CREATE TABLE `task_activities` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_sprints`
--

CREATE TABLE `task_sprints` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `task_states`
--

CREATE TABLE `task_states` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `color_background` varchar(255) NOT NULL,
  `color_font` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `task_states`
--

INSERT INTO `task_states` (`id`, `name`, `color_background`, `color_font`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sin asignar', 'grey lighten-5', 'grey darken-4', 'active', '2025-06-06 07:51:38', '2025-06-06 07:52:42'),
(2, 'Pendiente', 'amber lighten-5', 'amber darken-4', 'active', '2025-06-06 07:52:08', '2025-06-06 07:52:08'),
(3, 'En curso', 'blue lighten-5', 'blue darken-4', 'active', '2025-06-06 07:53:02', '2025-06-06 07:53:02'),
(4, 'Finalizado', 'green lighten-5', 'green darken-4', 'active', '2025-06-06 07:53:23', '2025-06-06 07:53:58'),
(5, 'Cancelado', 'pink lighten-5', 'pink darken-4', 'active', '2025-06-06 07:53:45', '2025-06-06 07:53:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(40) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `photo` varchar(100) DEFAULT NULL,
  `role_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `status`, `photo`, `role_id`) VALUES
(1, 'Administrador', 'iplanet@iplanetcolombia.com', 'root', 'active', '', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `passwords`
--
ALTER TABLE `passwords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passwords_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_role_id_foreign` (`role_id`),
  ADD KEY `permissions_menu_id_foreign` (`menu_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_task_state_id_foreign` (`task_state_id`),
  ADD KEY `tasks_task_sprint_id_foreign` (`task_sprint_id`),
  ADD KEY `tasks_task_activity_id_foreign` (`task_activity_id`),
  ADD KEY `tasks_task_user_id_foreign` (`task_user_id`);

--
-- Indices de la tabla `task_activities`
--
ALTER TABLE `task_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `task_sprints`
--
ALTER TABLE `task_sprints`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `task_states`
--
ALTER TABLE `task_states`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configurations`
--
ALTER TABLE `configurations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `passwords`
--
ALTER TABLE `passwords`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `task_activities`
--
ALTER TABLE `task_activities`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `task_sprints`
--
ALTER TABLE `task_sprints`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `task_states`
--
ALTER TABLE `task_states`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `passwords`
--
ALTER TABLE `passwords`
  ADD CONSTRAINT `passwords_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`),
  ADD CONSTRAINT `permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_task_activity_id_foreign` FOREIGN KEY (`task_activity_id`) REFERENCES `task_activities` (`id`),
  ADD CONSTRAINT `tasks_task_sprint_id_foreign` FOREIGN KEY (`task_sprint_id`) REFERENCES `task_sprints` (`id`),
  ADD CONSTRAINT `tasks_task_state_id_foreign` FOREIGN KEY (`task_state_id`) REFERENCES `task_states` (`id`),
  ADD CONSTRAINT `tasks_task_user_id_foreign` FOREIGN KEY (`task_user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
