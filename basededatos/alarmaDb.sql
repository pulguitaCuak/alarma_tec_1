-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 05-07-2025 a las 11:39:36
-- Versión del servidor: 8.0.42-0ubuntu0.20.04.1
-- Versión de PHP: 7.4.3-4ubuntu2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `alarmaDb`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertarUsuario` (IN `user` VARCHAR(50), IN `password` INT, IN `idCharge` INT)  BEGIN
         insert into user (usuario,contreseña,cargo)
          values (user,password,idCharge);
     END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `charge`
--

CREATE TABLE `charge` (
  `idCharge` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `charge`
--

INSERT INTO `charge` (`idCharge`, `name`) VALUES
(1, 'user'),
(2, 'superUser'),
(3, 'guest');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sensors`
--

CREATE TABLE `sensors` (
  `idSensors` int NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `idZone` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `statusSensor`
--

CREATE TABLE `statusSensor` (
  `idStatusSensor` int NOT NULL,
  `dateTime` timestamp NULL DEFAULT NULL,
  `description` varchar(250) NOT NULL,
  `idZone` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team`
--

CREATE TABLE `team` (
  `idTeam` int NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `idZone` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `idUser` int NOT NULL,
  `user` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `idCharge` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `userTeam`
--

CREATE TABLE `userTeam` (
  `idUserTeam` int NOT NULL,
  `idUser` int NOT NULL,
  `idTeam` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zone`
--

CREATE TABLE `zone` (
  `idZone` int NOT NULL,
  `description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `zone`
--

INSERT INTO `zone` (`idZone`, `description`) VALUES
(1, 'puerta');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `charge`
--
ALTER TABLE `charge`
  ADD PRIMARY KEY (`idCharge`);

--
-- Indices de la tabla `sensors`
--
ALTER TABLE `sensors`
  ADD PRIMARY KEY (`idSensors`),
  ADD KEY `fk_idZone` (`idZone`);

--
-- Indices de la tabla `statusSensor`
--
ALTER TABLE `statusSensor`
  ADD PRIMARY KEY (`idStatusSensor`),
  ADD KEY `cod_IdZone` (`idZone`);

--
-- Indices de la tabla `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`idTeam`),
  ADD KEY `cod_idZone` (`idZone`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idUser`),
  ADD KEY `fk_idCharge` (`idCharge`);

--
-- Indices de la tabla `userTeam`
--
ALTER TABLE `userTeam`
  ADD PRIMARY KEY (`idUserTeam`),
  ADD KEY `fk_IdUser` (`idUser`),
  ADD KEY `fk_idTeam` (`idTeam`);

--
-- Indices de la tabla `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`idZone`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `charge`
--
ALTER TABLE `charge`
  MODIFY `idCharge` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sensors`
--
ALTER TABLE `sensors`
  MODIFY `idSensors` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `statusSensor`
--
ALTER TABLE `statusSensor`
  MODIFY `idStatusSensor` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `team`
--
ALTER TABLE `team`
  MODIFY `idTeam` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `idUser` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `userTeam`
--
ALTER TABLE `userTeam`
  MODIFY `idUserTeam` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `zone`
--
ALTER TABLE `zone`
  MODIFY `idZone` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sensors`
--
ALTER TABLE `sensors`
  ADD CONSTRAINT `sensors_ibfk_1` FOREIGN KEY (`idZone`) REFERENCES `zone` (`idZone`);

--
-- Filtros para la tabla `statusSensor`
--
ALTER TABLE `statusSensor`
  ADD CONSTRAINT `statusSensor_ibfk_1` FOREIGN KEY (`idZone`) REFERENCES `zone` (`idZone`);

--
-- Filtros para la tabla `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`idZone`) REFERENCES `zone` (`idZone`);

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`idCharge`) REFERENCES `charge` (`idCharge`);

--
-- Filtros para la tabla `userTeam`
--
ALTER TABLE `userTeam`
  ADD CONSTRAINT `userTeam_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`),
  ADD CONSTRAINT `userTeam_ibfk_2` FOREIGN KEY (`idTeam`) REFERENCES `team` (`idTeam`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
