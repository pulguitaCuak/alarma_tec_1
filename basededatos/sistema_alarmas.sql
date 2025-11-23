-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2025 a las 01:24:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_alarmas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Técnico'),
(3, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `id_equipo` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`id_equipo`, `nombre`, `contrasena`, `estado`, `descripcion`) VALUES
(1, 'Central Hogar A1', 'eq001', 1, 'aaasssddff'),
(2, 'Central Oficina B2', 'eq002', 1, ''),
(3, 'Central Depósito C3', 'eq003', 2, '');

--
-- Disparadores `equipo`
--
DELIMITER $$
CREATE TRIGGER `trg_equipo_delete` AFTER DELETE ON `equipo` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'DELETE',
    'equipo',
    OLD.id_equipo,
    CONCAT('{"nombre":"', COALESCE(OLD.nombre,''), '","estado":', IFNULL(OLD.estado,'null'), '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_equipo_insert` AFTER INSERT ON `equipo` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'INSERT',
    'equipo',
    NEW.id_equipo,
    CONCAT('{"nombre":"', COALESCE(NEW.nombre,''), '","estado":', IFNULL(NEW.estado,'null'), '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_equipo_update` AFTER UPDATE ON `equipo` FOR EACH ROW BEGIN
  IF (OLD.nombre <> NEW.nombre) OR (OLD.estado <> NEW.estado) OR (COALESCE(OLD.descripcion,'') <> COALESCE(NEW.descripcion,'')) THEN
    INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
    VALUES (
      NULL,
      'UPDATE',
      'equipo',
      NEW.id_equipo,
      CONCAT(
        '{"antes":{',
          '"nombre":"', COALESCE(OLD.nombre,''), '",',
          '"estado":', IFNULL(OLD.estado,'null'), ',',
          '"descripcion":"', REPLACE(COALESCE(OLD.descripcion,''),'"','"'), '"',
        '}, "despues":{',
          '"nombre":"', COALESCE(NEW.nombre,''), '",',
          '"estado":', IFNULL(NEW.estado,'null'), ',',
          '"descripcion":"', REPLACE(COALESCE(NEW.descripcion,''),'"','"'), '"',
        '}}'
      )
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_zona`
--

CREATE TABLE `equipo_zona` (
  `id_equipo_zona` int(11) NOT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `fecha_vinculo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipo_zona`
--

INSERT INTO `equipo_zona` (`id_equipo_zona`, `id_equipo`, `id_zona`, `fecha_vinculo`) VALUES
(1, 1, 1, '2025-10-18 00:04:44'),
(2, 1, 2, '2025-10-18 00:04:44'),
(3, 2, 3, '2025-10-18 00:04:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id_estado`, `descripcion`) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'Pendiente'),
(4, 'Suspendido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_eventos`
--

CREATE TABLE `log_eventos` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `id_registro_afectado` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `log_eventos`
--

INSERT INTO `log_eventos` (`id_log`, `id_usuario`, `accion`, `tabla_afectada`, `id_registro_afectado`, `fecha`, `descripcion`) VALUES
(1, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 12:25:00', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(2, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:17:29', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(3, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:18:58', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(4, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:20:34', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(5, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:22:32', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(6, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:23:44', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(7, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:25:57', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(8, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:28:13', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(9, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:28:22', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(10, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:30:04', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(11, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:30:28', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(12, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:31:45', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(13, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:34:15', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(14, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:37:21', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(15, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:40:28', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(16, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:53:23', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(17, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:55:02', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(18, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 13:57:22', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(19, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:00:46', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(20, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:01:41', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(21, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:03:00', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(22, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:05:59', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(23, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:07:40', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(24, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:13:44', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(25, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:14:18', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(26, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:36:29', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(27, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:36:32', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(28, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:37:06', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(29, NULL, 'UPDATE', 'usuarios', 1, '2025-11-01 14:39:59', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(30, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 20:57:34', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(31, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 21:03:58', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(33, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:29:37', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(34, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:37:43', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(35, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:39:07', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(36, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:40:05', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(37, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:40:24', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(38, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:40:28', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(39, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 22:44:22', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(40, NULL, 'UPDATE', 'usuarios', 6, '2025-11-04 23:05:23', '{\"antes\":{\"nombre\":\"Admin\",\"apellido\":\"Root\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Admin\",\"apellido\":\"Root\",\"id_cargo\":1,\"estado\":2}}'),
(41, 6, 'BAJA', 'usuarios', 6, '2025-11-04 23:05:23', 'Usuario dado de baja'),
(42, NULL, 'UPDATE', 'usuarios', 6, '2025-11-04 23:05:29', '{\"antes\":{\"nombre\":\"Admin\",\"apellido\":\"Root\",\"id_cargo\":1,\"estado\":2}, \"despues\":{\"nombre\":\"Admin\",\"apellido\":\"Root\",\"id_cargo\":1,\"estado\":1}}'),
(43, 6, 'ALTA', 'usuarios', 6, '2025-11-04 23:05:29', 'Usuario dado de alta'),
(44, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:09:48', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(45, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:11:27', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(46, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:16:57', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(47, 1, 'UPDATE', 'usuarios', 1, '2025-11-04 23:16:57', 'Cambio de contraseña por recuperación'),
(48, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:19:34', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(49, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:20:13', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(50, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:20:51', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(51, 1, 'UPDATE', 'usuarios', 1, '2025-11-04 23:20:51', 'Cambio de contraseña por recuperación'),
(52, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:26:44', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(53, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:28:23', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(54, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:28:23', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(55, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:29:10', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(56, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:29:10', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(57, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:29:40', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(58, 1, 'UPDATE', 'usuarios', 1, '2025-11-04 23:29:40', 'Cambio de contraseña por recuperación'),
(59, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:42:17', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(60, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:42:17', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(61, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:49:50', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(62, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:49:50', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(63, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:50:15', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(64, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:50:15', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(65, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:58:42', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(66, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:58:42', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(67, NULL, 'UPDATE', 'usuarios', 2, '2025-11-04 23:58:58', '{\"antes\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}, \"despues\":{\"nombre\":\"María\",\"apellido\":\"Gómez\",\"id_cargo\":3,\"estado\":2}}'),
(68, NULL, 'UPDATE', 'usuarios', 1, '2025-11-04 23:59:39', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(69, 1, 'UPDATE', 'usuarios', 1, '2025-11-04 23:59:39', 'Cambio de contraseña por recuperación'),
(70, NULL, 'UPDATE', 'usuarios', 1, '2025-11-05 00:03:06', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(71, NULL, 'UPDATE', 'usuarios', 1, '2025-11-05 00:06:06', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(72, NULL, 'UPDATE', 'usuarios', 1, '2025-11-05 00:07:17', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(73, 1, 'UPDATE', 'usuarios', 1, '2025-11-05 00:07:17', 'Cambio de contraseña por recuperación'),
(74, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:42:12', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(75, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:43:47', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(76, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:44:29', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(77, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:44:31', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(78, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:47:13', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(79, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:49:18', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(80, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:51:59', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(81, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:54:57', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(82, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 12:55:56', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(83, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:00:44', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(84, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:06:16', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(85, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:12:11', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(86, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:13:49', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(87, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:20:03', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(88, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:21:34', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(89, 1, 'UPDATE', 'usuarios', 1, '2025-11-08 13:21:34', 'Cambio de contraseña por recuperación'),
(90, NULL, 'UPDATE', 'usuarios', 3, '2025-11-08 13:46:26', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(91, NULL, 'UPDATE', 'usuarios', 3, '2025-11-08 13:46:50', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(92, NULL, 'UPDATE', 'usuarios', 3, '2025-11-08 13:48:15', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(93, NULL, 'UPDATE', 'usuarios', 1, '2025-11-08 13:49:36', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(94, NULL, 'UPDATE', 'zona_sensor', 1, '2025-11-08 13:55:11', '{\"id_zona\":1,\"id_sensor\":1,\"antes_estado\":1,\"despues_estado\":2}'),
(95, NULL, 'UPDATE', 'zona_sensor', 1, '2025-11-08 13:55:11', '{\"id_zona\":1,\"id_sensor\":1,\"antes_estado\":2,\"despues_estado\":1}'),
(96, NULL, 'UPDATE', 'usuarios', 1, '2025-11-09 00:05:40', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(97, NULL, 'UPDATE', 'usuarios', 1, '2025-11-09 00:23:17', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(98, NULL, 'UPDATE', 'usuarios', 1, '2025-11-09 00:33:26', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(99, NULL, 'UPDATE', 'usuarios', 1, '2025-11-09 00:34:24', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(100, NULL, 'UPDATE', 'usuarios', 1, '2025-11-09 01:17:49', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(101, NULL, 'UPDATE', 'usuarios', 1, '2025-11-09 01:19:04', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(102, NULL, 'UPDATE', 'usuarios', 3, '2025-11-09 02:09:17', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(103, NULL, 'UPDATE', 'usuarios', 3, '2025-11-09 02:09:30', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(104, NULL, 'UPDATE', 'usuarios', 3, '2025-11-09 02:10:42', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(105, NULL, 'UPDATE', 'usuarios', 3, '2025-11-09 02:16:03', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(106, 3, 'UPDATE', 'usuarios', 3, '2025-11-09 02:16:03', 'Cambio de contraseña por recuperación'),
(107, NULL, 'UPDATE', 'usuarios', 4, '2025-11-12 20:59:13', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}}'),
(108, NULL, 'UPDATE', 'usuarios', 4, '2025-11-12 20:59:37', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}}'),
(109, NULL, 'UPDATE', 'usuarios', 4, '2025-11-12 21:00:20', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}}'),
(110, 4, 'UPDATE', 'usuarios', 4, '2025-11-12 21:00:20', 'Cambio de contraseña por recuperación'),
(111, NULL, 'UPDATE', 'usuarios', 1, '2025-11-12 21:02:58', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(112, NULL, 'UPDATE', 'usuarios', 1, '2025-11-23 00:24:13', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Pérez\",\"id_cargo\":3,\"estado\":1}}'),
(113, NULL, 'UPDATE', 'usuarios', 3, '2025-11-23 00:24:20', '{\"antes\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}, \"despues\":{\"nombre\":\"Carlos\",\"apellido\":\"López\",\"id_cargo\":1,\"estado\":1}}'),
(114, NULL, 'UPDATE', 'usuarios', 4, '2025-11-23 00:24:29', '{\"antes\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}, \"despues\":{\"nombre\":\"Juan\",\"apellido\":\"Perez\",\"id_cargo\":2,\"estado\":1}}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_sensores`
--

CREATE TABLE `log_sensores` (
  `id_log_sensor` int(11) NOT NULL,
  `id_sensor` int(11) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `cambio` varchar(255) DEFAULT NULL,
  `valor_anterior` varchar(255) DEFAULT NULL,
  `valor_nuevo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_historico`
--

CREATE TABLE `registro_historico` (
  `id_registro` int(11) NOT NULL,
  `estado` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_zona` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_sensor_estado`
--

CREATE TABLE `registro_sensor_estado` (
  `id_registro_sensor` int(11) NOT NULL,
  `id_sensor` int(11) DEFAULT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) DEFAULT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sensores`
--

CREATE TABLE `sensores` (
  `id_sensor` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `id_tipo_sensor` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `fecha_instalacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sensores`
--

INSERT INTO `sensores` (`id_sensor`, `nombre`, `id_tipo_sensor`, `estado`, `fecha_instalacion`, `descripcion`) VALUES
(1, 'Sensor Movimiento Living', 1, 1, '2025-10-18 00:04:44', 'Detecta presencia en living'),
(2, 'Sensor Puerta Cocina', 2, 1, '2025-10-18 00:04:44', 'Detecta apertura en cocina'),
(3, 'Sensor Humo Patio', 3, 2, '2025-10-18 00:04:44', 'Detecta humo en patio');

--
-- Disparadores `sensores`
--
DELIMITER $$
CREATE TRIGGER `trg_sensores_delete` AFTER DELETE ON `sensores` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (NULL,'DELETE','sensores', OLD.id_sensor,
    CONCAT('{"nombre":"', REPLACE(COALESCE(OLD.nombre,''),'"','"'), '","id_tipo_sensor":', IFNULL(OLD.id_tipo_sensor,'null'), ',"estado":', IFNULL(OLD.estado,'null'), '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sensores_insert` AFTER INSERT ON `sensores` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (NULL,'INSERT','sensores', NEW.id_sensor,
    CONCAT('{"nombre":"', REPLACE(COALESCE(NEW.nombre,''),'"','"'), '","id_tipo_sensor":', IFNULL(NEW.id_tipo_sensor,'null'), ',"estado":', IFNULL(NEW.estado,'null'), '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sensores_update` AFTER UPDATE ON `sensores` FOR EACH ROW BEGIN
  -- registrar sólo si cambió el estado o el nombre
  IF (OLD.estado <> NEW.estado) OR (COALESCE(OLD.nombre,'') <> COALESCE(NEW.nombre,'')) THEN
    INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
    VALUES (
      NULL,
      'UPDATE',
      'sensores',
      NEW.id_sensor,
      CONCAT(
        '{"antes":{',
          '"nombre":"', REPLACE(COALESCE(OLD.nombre,''),'"','"'), '",',
          '"estado":', IFNULL(OLD.estado,'null'),
        '}, "despues":{',
          '"nombre":"', REPLACE(COALESCE(NEW.nombre,''),'"','"'), '",',
          '"estado":', IFNULL(NEW.estado,'null'),
        '}}'
      )
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

CREATE TABLE `suscripciones` (
  `id_suscripcion` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `duracion_meses` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `suscripciones`
--

INSERT INTO `suscripciones` (`id_suscripcion`, `nombre`, `duracion_meses`, `precio`) VALUES
(1, 'Básico', 6, 1200.00),
(2, 'Premium', 12, 2400.00),
(3, 'Pro', 24, 4000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_sensor`
--

CREATE TABLE `tipo_sensor` (
  `id_tipo_sensor` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_sensor`
--

INSERT INTO `tipo_sensor` (`id_tipo_sensor`, `nombre`, `descripcion`) VALUES
(1, 'Movimiento', 'Sensor de movimiento PIR'),
(2, 'Puerta', 'Sensor magnético de apertura'),
(3, 'Humo', 'Detector de humo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` int(11) DEFAULT NULL,
  `mail` varchar(25) DEFAULT NULL,
  `token_recuperacion` varchar(65) DEFAULT NULL,
  `token_recuperacion_expirado` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_user`, `nombre`, `apellido`, `contrasena`, `id_cargo`, `fecha_creacion`, `estado`, `mail`, `token_recuperacion`, `token_recuperacion_expirado`) VALUES
(1, 'Juan', 'Pérez', '$2y$10$I8Ub./gVB392zRsYH7OvL.gGoNJ6mSGh.iLokDkuVHPcV/EJ3BNua', 3, '2025-10-18 00:04:44', 1, 'usuario1@gmail.com', 'e357ed00b5eb0ee84b00090f87c5a0daedd527760794f58db2b2d68a8cccca4c', '2025-11-12'),
(2, 'María', 'Gómez', 'abcd', 3, '2025-10-18 00:04:44', 2, NULL, 'c6ed3eea8c8bca9614a128c4e4b723b5f99ce42ce28a75a60d7895e84d7a7a48', '2025-11-05'),
(3, 'Carlos', 'López', '$2y$10$Kv0tsDev8DWt77n95cg.Pe7VdbUry8lcq17X6BXGyWJZd8CeYh5JW', 1, '2025-10-18 00:04:44', 1, 'usuario@gmail.com', NULL, NULL),
(4, 'Juan', 'Perez', '$2y$10$GEtiwyx9KhORKXt6WHdtMe5gyczoAvy3SKLzOD0yLPyIJIq.xjqxm', 2, '2025-10-18 03:14:18', 1, 'usuario2@gmail.com', NULL, NULL),
(5, 'Ana', 'Gomez', 'abcd', 2, '2025-10-18 03:14:18', 1, NULL, NULL, NULL),
(6, 'Admin', 'Root', 'admin', 1, '2025-10-18 03:14:18', 1, NULL, NULL, NULL);

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `trg_usuarios_delete` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'DELETE',
    'usuarios',
    OLD.id_user,
    CONCAT('{"nombre":"', COALESCE(OLD.nombre,''), '","apellido":"', COALESCE(OLD.apellido,''), '"}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_usuarios_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'INSERT',
    'usuarios',
    NEW.id_user,
    CONCAT(
      '{"nombre":"', COALESCE(NEW.nombre,''), '",',
      '"apellido":"', COALESCE(NEW.apellido,''), '",',
      '"id_cargo":', IFNULL(NEW.id_cargo,'null'), ',',
      '"estado":', IFNULL(NEW.estado,'null'), '}'
    )
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_usuarios_update` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'UPDATE',
    'usuarios',
    NEW.id_user,
    CONCAT(
      '{"antes":{',
        '"nombre":"', COALESCE(OLD.nombre,''), '",',
        '"apellido":"', COALESCE(OLD.apellido,''), '",',
        '"id_cargo":', IFNULL(OLD.id_cargo,'null'), ',',
        '"estado":', IFNULL(OLD.estado,'null'),
      '}, "despues":{',
        '"nombre":"', COALESCE(NEW.nombre,''), '",',
        '"apellido":"', COALESCE(NEW.apellido,''), '",',
        '"id_cargo":', IFNULL(NEW.id_cargo,'null'), ',',
        '"estado":', IFNULL(NEW.estado,'null'),
      '}}'
    )
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_equipos`
--

CREATE TABLE `usuario_equipos` (
  `id_user_equipo` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_equipos`
--

INSERT INTO `usuario_equipos` (`id_user_equipo`, `id_usuario`, `id_equipo`, `fecha_asignacion`) VALUES
(1, 1, 1, '2025-10-18 00:04:44'),
(2, 1, 2, '2025-10-18 00:04:44'),
(3, 2, 3, '2025-10-18 00:04:44');

--
-- Disparadores `usuario_equipos`
--
DELIMITER $$
CREATE TRIGGER `trg_usuario_equipos_delete` AFTER DELETE ON `usuario_equipos` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'DELETE',
    'usuario_equipos',
    OLD.id_user_equipo,
    CONCAT('{"id_usuario":', OLD.id_usuario, ',"id_equipo":', OLD.id_equipo, '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_usuario_equipos_insert` AFTER INSERT ON `usuario_equipos` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'INSERT',
    'usuario_equipos',
    NEW.id_user_equipo,
    CONCAT('{"id_usuario":', NEW.id_usuario, ',"id_equipo":', NEW.id_equipo, '}')
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_suscripcion`
--

CREATE TABLE `usuario_suscripcion` (
  `id_usuario_suscripcion` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_suscripcion` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_suscripcion`
--

INSERT INTO `usuario_suscripcion` (`id_usuario_suscripcion`, `id_usuario`, `id_suscripcion`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 1, 2, '2025-01-01', '2025-12-31', 1),
(2, 2, 1, '2025-06-01', '2025-12-01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas`
--

CREATE TABLE `zonas` (
  `id_zona` int(11) NOT NULL,
  `estado` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zonas`
--

INSERT INTO `zonas` (`id_zona`, `estado`, `descripcion`) VALUES
(1, 1, 'Zona Living'),
(2, 1, 'Zona Cocina'),
(3, 2, 'Zona Patio');

--
-- Disparadores `zonas`
--
DELIMITER $$
CREATE TRIGGER `trg_zonas_update` AFTER UPDATE ON `zonas` FOR EACH ROW BEGIN
  IF (COALESCE(OLD.descripcion,'') <> COALESCE(NEW.descripcion,'')) OR (OLD.estado <> NEW.estado) THEN
    INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
    VALUES (
      NULL,
      'UPDATE',
      'zonas',
      NEW.id_zona,
      CONCAT(
        '{"antes":{',
          '"descripcion":"', REPLACE(COALESCE(OLD.descripcion,''),'"','"'), '",',
          '"estado":', IFNULL(OLD.estado,'null'),
        '}, "despues":{',
          '"descripcion":"', REPLACE(COALESCE(NEW.descripcion,''),'"','"'), '",',
          '"estado":', IFNULL(NEW.estado,'null'),
        '}}'
      )
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zona_sensor`
--

CREATE TABLE `zona_sensor` (
  `id_zona_sensor` int(11) NOT NULL,
  `id_zona` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zona_sensor`
--

INSERT INTO `zona_sensor` (`id_zona_sensor`, `id_zona`, `id_sensor`, `estado`, `fecha_asignacion`) VALUES
(1, 1, 1, 1, '2025-10-18 03:05:00'),
(2, 2, 2, 1, '2025-10-18 03:05:00'),
(3, 3, 3, 1, '2025-10-18 03:05:00'),
(4, 1, 2, 1, '2025-10-18 03:06:00'),
(5, 2, 1, 1, '2025-10-18 03:06:00');

--
-- Disparadores `zona_sensor`
--
DELIMITER $$
CREATE TRIGGER `trg_zona_sensor_delete` AFTER DELETE ON `zona_sensor` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'DELETE',
    'zona_sensor',
    OLD.id_zona_sensor,
    CONCAT('{"id_zona":', OLD.id_zona, ',"id_sensor":', OLD.id_sensor, ',"estado":', IFNULL(OLD.estado,'null'), '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_zona_sensor_insert` AFTER INSERT ON `zona_sensor` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (
    NULL,
    'INSERT',
    'zona_sensor',
    NEW.id_zona_sensor,
    CONCAT('{"id_zona":', NEW.id_zona, ',"id_sensor":', NEW.id_sensor, ',"estado":', NEW.estado, '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_zona_sensor_update` AFTER UPDATE ON `zona_sensor` FOR EACH ROW BEGIN
  IF OLD.estado <> NEW.estado THEN
    INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
    VALUES (
      NULL,
      'UPDATE',
      'zona_sensor',
      NEW.id_zona_sensor,
      CONCAT(
        '{"id_zona":', NEW.id_zona, ',"id_sensor":', NEW.id_sensor, ',',
        '"antes_estado":', IFNULL(OLD.estado,'null'), ',"despues_estado":', IFNULL(NEW.estado,'null'), '}'
      )
    );
  END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `idx_equipo_estado` (`estado`),
  ADD KEY `idx_equipo_nombre` (`nombre`),
  ADD KEY `idx_equipo_descripcion` (`descripcion`);

--
-- Indices de la tabla `equipo_zona`
--
ALTER TABLE `equipo_zona`
  ADD PRIMARY KEY (`id_equipo_zona`),
  ADD KEY `id_zona` (`id_zona`),
  ADD KEY `idx_equipo_zona_equipo_zona` (`id_equipo`,`id_zona`),
  ADD KEY `idx_equipo_zona_fecha` (`fecha_vinculo`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `log_eventos`
--
ALTER TABLE `log_eventos`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_log_eventos_usuario_fecha` (`id_usuario`,`fecha`);

--
-- Indices de la tabla `log_sensores`
--
ALTER TABLE `log_sensores`
  ADD PRIMARY KEY (`id_log_sensor`),
  ADD KEY `id_zona` (`id_zona`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_log_sensores_sensor_fecha` (`id_sensor`,`fecha`);

--
-- Indices de la tabla `registro_historico`
--
ALTER TABLE `registro_historico`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `estado` (`estado`),
  ADD KEY `idx_registro_historico_zona_fecha` (`id_zona`,`fecha`);

--
-- Indices de la tabla `registro_sensor_estado`
--
ALTER TABLE `registro_sensor_estado`
  ADD PRIMARY KEY (`id_registro_sensor`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_registro_sensor_estado_sensor_fecha` (`id_sensor`,`fecha_inicio`);

--
-- Indices de la tabla `sensores`
--
ALTER TABLE `sensores`
  ADD PRIMARY KEY (`id_sensor`),
  ADD KEY `estado` (`estado`),
  ADD KEY `idx_sensores_tipo_estado` (`id_tipo_sensor`,`estado`),
  ADD KEY `idx_sensores_nombre` (`nombre`);

--
-- Indices de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD PRIMARY KEY (`id_suscripcion`);

--
-- Indices de la tabla `tipo_sensor`
--
ALTER TABLE `tipo_sensor`
  ADD PRIMARY KEY (`id_tipo_sensor`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `idx_usuarios_nombre_apellido` (`nombre`,`apellido`),
  ADD KEY `idx_usuarios_estado` (`estado`),
  ADD KEY `idx_usuarios_cargo_estado` (`id_cargo`,`estado`);

--
-- Indices de la tabla `usuario_equipos`
--
ALTER TABLE `usuario_equipos`
  ADD PRIMARY KEY (`id_user_equipo`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `idx_usuario_equipos_usuario_equipo` (`id_usuario`,`id_equipo`),
  ADD KEY `idx_usuario_equipos_fecha_asignacion` (`fecha_asignacion`);

--
-- Indices de la tabla `usuario_suscripcion`
--
ALTER TABLE `usuario_suscripcion`
  ADD PRIMARY KEY (`id_usuario_suscripcion`),
  ADD KEY `id_suscripcion` (`id_suscripcion`),
  ADD KEY `estado` (`estado`),
  ADD KEY `idx_usuario_suscripcion_usuario_estado` (`id_usuario`,`estado`),
  ADD KEY `idx_usuario_suscripcion_fecha` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD PRIMARY KEY (`id_zona`),
  ADD KEY `idx_zonas_estado` (`estado`),
  ADD KEY `idx_zonas_descripcion` (`descripcion`);

--
-- Indices de la tabla `zona_sensor`
--
ALTER TABLE `zona_sensor`
  ADD PRIMARY KEY (`id_zona_sensor`),
  ADD KEY `id_zona` (`id_zona`),
  ADD KEY `id_sensor` (`id_sensor`),
  ADD KEY `estado` (`estado`),
  ADD KEY `idx_zona_sensor_zona_sensor_estado` (`id_zona`,`id_sensor`,`estado`),
  ADD KEY `idx_zona_sensor_fecha_asignacion` (`fecha_asignacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `equipo_zona`
--
ALTER TABLE `equipo_zona`
  MODIFY `id_equipo_zona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `log_eventos`
--
ALTER TABLE `log_eventos`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `log_sensores`
--
ALTER TABLE `log_sensores`
  MODIFY `id_log_sensor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_historico`
--
ALTER TABLE `registro_historico`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_sensor_estado`
--
ALTER TABLE `registro_sensor_estado`
  MODIFY `id_registro_sensor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sensores`
--
ALTER TABLE `sensores`
  MODIFY `id_sensor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  MODIFY `id_suscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_sensor`
--
ALTER TABLE `tipo_sensor`
  MODIFY `id_tipo_sensor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario_equipos`
--
ALTER TABLE `usuario_equipos`
  MODIFY `id_user_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario_suscripcion`
--
ALTER TABLE `usuario_suscripcion`
  MODIFY `id_usuario_suscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `zonas`
--
ALTER TABLE `zonas`
  MODIFY `id_zona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `zona_sensor`
--
ALTER TABLE `zona_sensor`
  MODIFY `id_zona_sensor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD CONSTRAINT `equipo_ibfk_1` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `equipo_zona`
--
ALTER TABLE `equipo_zona`
  ADD CONSTRAINT `equipo_zona_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipo` (`id_equipo`),
  ADD CONSTRAINT `equipo_zona_ibfk_2` FOREIGN KEY (`id_zona`) REFERENCES `zonas` (`id_zona`);

--
-- Filtros para la tabla `log_eventos`
--
ALTER TABLE `log_eventos`
  ADD CONSTRAINT `log_eventos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `log_sensores`
--
ALTER TABLE `log_sensores`
  ADD CONSTRAINT `log_sensores_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id_sensor`),
  ADD CONSTRAINT `log_sensores_ibfk_2` FOREIGN KEY (`id_zona`) REFERENCES `zonas` (`id_zona`),
  ADD CONSTRAINT `log_sensores_ibfk_3` FOREIGN KEY (`id_equipo`) REFERENCES `equipo` (`id_equipo`),
  ADD CONSTRAINT `log_sensores_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `registro_historico`
--
ALTER TABLE `registro_historico`
  ADD CONSTRAINT `registro_historico_ibfk_1` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`),
  ADD CONSTRAINT `registro_historico_ibfk_2` FOREIGN KEY (`id_zona`) REFERENCES `zonas` (`id_zona`);

--
-- Filtros para la tabla `registro_sensor_estado`
--
ALTER TABLE `registro_sensor_estado`
  ADD CONSTRAINT `registro_sensor_estado_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id_sensor`),
  ADD CONSTRAINT `registro_sensor_estado_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `sensores`
--
ALTER TABLE `sensores`
  ADD CONSTRAINT `sensores_ibfk_1` FOREIGN KEY (`id_tipo_sensor`) REFERENCES `tipo_sensor` (`id_tipo_sensor`),
  ADD CONSTRAINT `sensores_ibfk_3` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `usuario_equipos`
--
ALTER TABLE `usuario_equipos`
  ADD CONSTRAINT `usuario_equipos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_user`),
  ADD CONSTRAINT `usuario_equipos_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipo` (`id_equipo`);

--
-- Filtros para la tabla `usuario_suscripcion`
--
ALTER TABLE `usuario_suscripcion`
  ADD CONSTRAINT `usuario_suscripcion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_user`),
  ADD CONSTRAINT `usuario_suscripcion_ibfk_2` FOREIGN KEY (`id_suscripcion`) REFERENCES `suscripciones` (`id_suscripcion`),
  ADD CONSTRAINT `usuario_suscripcion_ibfk_3` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD CONSTRAINT `zonas_ibfk_1` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `zona_sensor`
--
ALTER TABLE `zona_sensor`
  ADD CONSTRAINT `zona_sensor_ibfk_1` FOREIGN KEY (`id_zona`) REFERENCES `zonas` (`id_zona`),
  ADD CONSTRAINT `zona_sensor_ibfk_2` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id_sensor`),
  ADD CONSTRAINT `zona_sensor_ibfk_estado` FOREIGN KEY (`estado`) REFERENCES `estado` (`id_estado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
