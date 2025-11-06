-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 10:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prueba_tecnica`
--

-- --------------------------------------------------------

--
-- Table structure for table `carro`
--

CREATE TABLE `carro` (
  `idcarro` int(11) NOT NULL,
  `placa` varchar(45) DEFAULT NULL,
  `color` varchar(45) DEFAULT NULL,
  `fecha_ingreso` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carro`
--

INSERT INTO `carro` (`idcarro`, `placa`, `color`, `fecha_ingreso`) VALUES
(1, 'AAA123', 'Fucsia', '2025-07-10 21:36:14'),
(2, 'BBB456', 'Verde', '2025-07-28 21:36:14'),
(3, 'CCC789', 'Rojo', '2025-08-08 21:36:14'),
(4, 'DDD963', 'Azul', '2025-08-20 21:36:15'),
(5, 'EEE852', 'Rojo', '2025-08-28 21:36:15'),
(6, 'FFF741', 'Azul', '2025-10-15 21:36:15'),
(8, 'RRR123', 'Fucsia', '2025-11-12 05:00:00'),
(9, 'RRR153', 'Rojo', '2025-03-10 21:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `ciudad`
--

CREATE TABLE `ciudad` (
  `idciudad` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ciudad`
--

INSERT INTO `ciudad` (`idciudad`, `nombre`, `activo`) VALUES
(1, 'Cali', 1),
(2, 'Bogota', 0),
(3, 'Medellin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `password` varchar(250) NOT NULL,
  `fecha_registro_usuario` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `usuario`, `password`, `fecha_registro_usuario`) VALUES
(1, 'Juan Pacho', 'Juan', '$2y$10$k8CvxnNB3Ig5TGs9JfBaNuSWtN703AnZ7y4syPFmnMp/03rjn3Jbq', '2025-11-06 21:03:35'),
(2, 'Juan rrr', 'Juan1', '$2y$10$.s9vt5fempx92Gi3gGefUup5ElZ52Xs74fTywn8S87u01mR7LVH46', '2025-11-06 20:49:28'),
(3, 'Perla Castro', 'Perla1', '$2y$12$FwcxaZhOYuWY1r0u0st1Vukn4EcQ1J9ZhIbq7X1g0qSD54AqG7k.G', '2025-11-06 20:03:26'),
(4, 'Juan Rodriguez', 'Juan3', '$2y$12$uyFiSOCBpz7ye44..m9iF.6NBOjfdhSW2iFYSmBIP42DEdrmGXOAS', '2025-11-06 21:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `viaje`
--

CREATE TABLE `viaje` (
  `idviaje` int(11) NOT NULL,
  `idcarro` int(11) NOT NULL,
  `idciudad_origen` int(11) DEFAULT NULL,
  `idciudad_destino` int(11) DEFAULT NULL,
  `tiempo_horas` int(11) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `viaje`
--

INSERT INTO `viaje` (`idviaje`, `idcarro`, `idciudad_origen`, `idciudad_destino`, `tiempo_horas`, `fecha`) VALUES
(1, 1, 2, 1, 8, '2025-09-15 22:03:53'),
(2, 2, 2, 3, 6, '2025-09-25 22:03:53'),
(3, 2, 3, 1, 12, '2025-09-29 22:03:53'),
(4, 3, 1, 2, 10, '2025-10-08 22:04:35'),
(5, 1, 1, 3, 15, '2025-10-10 22:23:29'),
(6, 6, 1, 3, 10, '2024-12-04 15:03:53'),
(7, 3, 2, 1, 9, '2025-10-29 22:23:54'),
(8, 4, 1, 3, 6, '2025-11-05 15:45:00'),
(9, 2, 1, 3, 8, '2025-10-15 22:03:53'),
(10, 5, 1, 3, 20, '2025-09-15 22:03:53'),
(11, 6, 3, 2, 8, '2025-10-20 22:03:53'),
(12, 5, 3, 2, 30, '2025-11-05 22:03:53'),
(13, 2, 1, 2, 3, '2025-11-04 15:03:53'),
(14, 2, 1, 2, 3, '2025-11-04 15:03:53'),
(15, 6, 1, 3, 10, '2024-12-04 15:03:53'),
(16, 6, 1, 3, 10, '2024-12-04 15:03:53'),
(17, 6, 1, 3, 50, '2025-11-06 19:04:00'),
(18, 2, 1, 3, 1, '2024-12-04 15:03:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carro`
--
ALTER TABLE `carro`
  ADD PRIMARY KEY (`idcarro`);

--
-- Indexes for table `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`idciudad`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indexes for table `viaje`
--
ALTER TABLE `viaje`
  ADD PRIMARY KEY (`idviaje`),
  ADD KEY `const_id_user` (`idcarro`),
  ADD KEY `const_id_ciudad_o` (`idciudad_origen`),
  ADD KEY `const_id_ciudad_d` (`idciudad_destino`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carro`
--
ALTER TABLE `carro`
  MODIFY `idcarro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ciudad`
--
ALTER TABLE `ciudad`
  MODIFY `idciudad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `viaje`
--
ALTER TABLE `viaje`
  MODIFY `idviaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `viaje`
--
ALTER TABLE `viaje`
  ADD CONSTRAINT `const_id_ciudad_d` FOREIGN KEY (`idciudad_destino`) REFERENCES `ciudad` (`idciudad`),
  ADD CONSTRAINT `const_id_ciudad_o` FOREIGN KEY (`idciudad_origen`) REFERENCES `ciudad` (`idciudad`),
  ADD CONSTRAINT `const_id_user` FOREIGN KEY (`idcarro`) REFERENCES `carro` (`idcarro`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
