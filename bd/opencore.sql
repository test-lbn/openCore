-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-03-2019 a las 21:36:09
-- Versión del servidor: 5.7.24
-- Versión de PHP: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `opencore`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `sp_contarUsuarios`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_contarUsuarios` (IN `estado_usu` VARCHAR(1), OUT `nproductos` INT)  SELECT COUNT(*) INTO nproductos
FROM core_usuarios u
WHERE u.estado=estado_usu$$

DROP PROCEDURE IF EXISTS `sp_traerUsuarios`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_traerUsuarios` (`usu` VARCHAR(25))  SELECT * 
FROM core_usuarios u
WHERE u.usuario = usu$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_menus`
--

DROP TABLE IF EXISTS `core_menus`;
CREATE TABLE IF NOT EXISTS `core_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sub` int(11) NOT NULL,
  `icon_mod` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `des_mod` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `orden` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_menus`
--

INSERT INTO `core_menus` (`id`, `id_sub`, `icon_mod`, `des_mod`, `orden`) VALUES
(1, 0, 'fa-gears', 'Admin', '1'),
(2, 0, 'fa-circle', 'Prueba', '2'),
(3, 2, 'fa-circle', 'Prueba1', '4'),
(4, 0, 'fa-circle', 'Prueba2', '3'),
(5, 0, 'fa-circle', 'Menu_prueba', '1'),
(6, 0, 'fa-user', 'Menu_1', '1'),
(7, 0, 'fa-user', 'Menu_2', '1'),
(8, 0, 'fa-user', 'Sura', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_permisos`
--

DROP TABLE IF EXISTS `core_permisos`;
CREATE TABLE IF NOT EXISTS `core_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `id_programa_opcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_permisos`
--

INSERT INTO `core_permisos` (`id`, `id_rol`, `id_programa_opcion`) VALUES
(121, '1', '1'),
(122, '1', '2'),
(123, '1', '3'),
(124, '1', '4'),
(125, '1', '5'),
(126, '1', '15'),
(127, '1', '23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_programas`
--

DROP TABLE IF EXISTS `core_programas`;
CREATE TABLE IF NOT EXISTS `core_programas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `id_menu` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `autenticado` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `programa` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_programas`
--

INSERT INTO `core_programas` (`id`, `descripcion`, `id_menu`, `autenticado`, `programa`, `orden`) VALUES
(1, 'Inicio de session', '1', 'N', 'LOGIN', 1),
(2, 'HOME', '1', 'S', 'INICIO', 2),
(3, 'NUEVO PROGRAMA', '1', 'S', 'PROGRAMA', 4),
(4, 'USUARIOS', '1', 'S', 'USUARIO', 3),
(5, 'MENU', '1', 'S', 'MENU', 6),
(6, 'ROLES', '1', 'S', 'ROL', 5),
(33, 'CONFIGURACION', '1', 'S', 'CONFIGURACION', 0),
(39, 'PRUEBA SP SURA', '8', 'S', 'INFORME', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_programas_opciones`
--

DROP TABLE IF EXISTS `core_programas_opciones`;
CREATE TABLE IF NOT EXISTS `core_programas_opciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_programa` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `opcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_programas_opciones`
--

INSERT INTO `core_programas_opciones` (`id`, `id_programa`, `opcion`, `descripcion`) VALUES
(1, '2', 'A', 'Acceso'),
(2, '3', 'A', 'Acceso'),
(3, '4', 'A', 'Acceso'),
(4, '5', 'A', 'Acceso'),
(5, '6', 'A', 'Acceso'),
(13, '28', 'A', 'ACCESO'),
(15, '33', 'A', 'Acceso'),
(23, '39', 'A', 'Acceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_roles`
--

DROP TABLE IF EXISTS `core_roles`;
CREATE TABLE IF NOT EXISTS `core_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_roles`
--

INSERT INTO `core_roles` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'ADMINISTRADOR', 'Rol administrador', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_token`
--

DROP TABLE IF EXISTS `core_token`;
CREATE TABLE IF NOT EXISTS `core_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `dominio` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `token` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `vigencia` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `fecha_hora` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_token`
--

INSERT INTO `core_token` (`id`, `id_usuario`, `dominio`, `token`, `vigencia`, `fecha_hora`) VALUES
(1, '1', 'localhost', '9d5712112f8cb8e3dabc8efed10b5c39bef18228', '2019-01-03', '2019-01-03 12:18:25'),
(2, '1', '35.231.186.156', '16a96a13dacbd0f7613873fa0843b8727895c932', '2019-02-05', '2019-02-05 18:02:31'),
(3, '2', '35.231.186.156', 'e6308a97c6d555171f6f4e05f61eaf68278c159b', '2019-03-01', '2019-03-01 10:58:50'),
(4, '4', '35.231.186.156', '28f5b0e5cd12f9486ca2db3222f0f7eae6c9d414', '2019-03-11', '2019-03-11 09:21:52'),
(5, '4', 'localhost', '619d5604f5604f59d09a541b219ecef689972f32', '2019-03-19', '2019-03-19 10:00:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_usuarios`
--

DROP TABLE IF EXISTS `core_usuarios`;
CREATE TABLE IF NOT EXISTS `core_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `usr_pass` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `f_expira_p` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_usuarios`
--

INSERT INTO `core_usuarios` (`id`, `usuario`, `usr_pass`, `nombre`, `estado`, `apellidos`, `correo`, `f_expira_p`) VALUES
(1, 'sistem04', 'bb5d45dc845c0f46a0b37b086c20e9b0b99ada1d', 'Alvaro', 'A', 'Pulgarin', 'aepulgarin@gmail.com', '2018-12-18'),
(2, 'lrivera', '8cb2237d0679ca88db6464eac60da96345513964', 'Leonel', 'A', 'Rivera', 'lfriverac@hotmail.com', '2019-01-08'),
(4, 'brian', '760e7dab2836853c63805033e514668301fa9c47', 'Brian', 'A', 'Rodriguez', 'brian.alber@hotmail.com', '2019-01-09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `core_usuarios_roles`
--

DROP TABLE IF EXISTS `core_usuarios_roles`;
CREATE TABLE IF NOT EXISTS `core_usuarios_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `id_rol` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `core_usuarios_roles`
--

INSERT INTO `core_usuarios_roles` (`id`, `id_usuario`, `id_rol`) VALUES
(14, '1', '1'),
(15, '2', '1'),
(16, '4', '1');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_usuarios_permisos`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_usuarios_permisos`;
CREATE TABLE IF NOT EXISTS `v_usuarios_permisos` (
`id_menu` varchar(100)
,`id_usuario` int(11)
,`id_programa` int(11)
,`id_menu_parent` int(11)
,`programa` varchar(100)
,`nombre_menu` varchar(100)
,`descripcion_programa` varchar(100)
,`icono` varchar(100)
,`orden_menu` varchar(100)
,`orden_programa` int(11)
,`id_rol` varchar(100)
,`opcion` varchar(100)
,`id_programa_opcion` int(11)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_usuarios_permisos`
--
DROP TABLE IF EXISTS `v_usuarios_permisos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`openCore`@`%` SQL SECURITY DEFINER VIEW `v_usuarios_permisos`  AS  select `pr`.`id_menu` AS `id_menu`,`us`.`id` AS `id_usuario`,`pr`.`id` AS `id_programa`,`m`.`id_sub` AS `id_menu_parent`,`pr`.`programa` AS `programa`,`m`.`des_mod` AS `nombre_menu`,convert(`pr`.`descripcion` using utf8) AS `descripcion_programa`,`m`.`icon_mod` AS `icono`,`m`.`orden` AS `orden_menu`,`pr`.`orden` AS `orden_programa`,`pe`.`id_rol` AS `id_rol`,`pro`.`opcion` AS `opcion`,`pro`.`id` AS `id_programa_opcion` from (((((`core_programas` `pr` join `core_programas_opciones` `pro`) join `core_permisos` `pe`) join `core_usuarios_roles` `ur`) join `core_usuarios` `us`) join `core_menus` `m`) where ((`pr`.`id` = `pro`.`id_programa`) and (`pro`.`id` = `pe`.`id_programa_opcion`) and (`pe`.`id_programa_opcion` = `pro`.`id`) and (`pe`.`id_rol` = `ur`.`id_rol`) and (`ur`.`id_usuario` = `us`.`id`) and (`pr`.`id_menu` = `m`.`id`)) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
