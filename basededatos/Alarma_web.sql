-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-10-2025 a las 07:51:03
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
          '"descripcion":"', REPLACE(COALESCE(OLD.descripcion,''),'"','\"'), '"',
        '}, "despues":{',
          '"nombre":"', COALESCE(NEW.nombre,''), '",',
          '"estado":', IFNULL(NEW.estado,'null'), ',',
          '"descripcion":"', REPLACE(COALESCE(NEW.descripcion,''),'"','\"'), '"',
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
    CONCAT('{"nombre":"', REPLACE(COALESCE(OLD.nombre,''),'"','\"'), '","id_tipo_sensor":', IFNULL(OLD.id_tipo_sensor,'null'), ',"estado":', IFNULL(OLD.estado,'null'), '}')
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sensores_insert` AFTER INSERT ON `sensores` FOR EACH ROW BEGIN
  INSERT INTO log_eventos (id_usuario, accion, tabla_afectada, id_registro_afectado, descripcion)
  VALUES (NULL,'INSERT','sensores', NEW.id_sensor,
    CONCAT('{"nombre":"', REPLACE(COALESCE(NEW.nombre,''),'"','\"'), '","id_tipo_sensor":', IFNULL(NEW.id_tipo_sensor,'null'), ',"estado":', IFNULL(NEW.estado,'null'), '}')
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
          '"nombre":"', REPLACE(COALESCE(OLD.nombre,''),'"','\"'), '",',
          '"estado":', IFNULL(OLD.estado,'null'),
        '}, "despues":{',
          '"nombre":"', REPLACE(COALESCE(NEW.nombre,''),'"','\"'), '",',
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
  `estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_user`, `nombre`, `apellido`, `contrasena`, `id_cargo`, `fecha_creacion`, `estado`) VALUES
(1, 'Juan', 'Pérez', '1234', 3, '2025-10-18 00:04:44', 1),
(2, 'María', 'Gómez', 'abcd', 3, '2025-10-18 00:04:44', 2),
(3, 'Carlos', 'López', 'admin', 1, '2025-10-18 00:04:44', 1),
(4, 'Juan', 'Perez', '1234', 2, '2025-10-18 03:14:18', 1),
(5, 'Ana', 'Gomez', 'abcd', 2, '2025-10-18 03:14:18', 1),
(6, 'Admin', 'Root', 'admin', 1, '2025-10-18 03:14:18', 1);

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
          '"descripcion":"', REPLACE(COALESCE(OLD.descripcion,''),'"','\"'), '",',
          '"estado":', IFNULL(OLD.estado,'null'),
        '}, "despues":{',
          '"descripcion":"', REPLACE(COALESCE(NEW.descripcion,''),'"','\"'), '",',
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
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

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
