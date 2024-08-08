-- MySQL dump 10.13  Distrib 8.2.0, for macos13 (arm64)
--
-- Host: localhost    Database: app_inmueble
-- ------------------------------------------------------
-- Server version	8.2.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Alquiler`
--

DROP TABLE IF EXISTS `Alquiler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Alquiler` (
  `AlqId` int NOT NULL AUTO_INCREMENT,
  `ArrendadorId` int DEFAULT NULL,
  `ClienteId` int DEFAULT NULL,
  `AlqNombre` varchar(255) DEFAULT NULL,
  `AlqMontoTotal` decimal(10,2) NOT NULL,
  `AlqMontoMensual` decimal(10,2) NOT NULL,
  `AlqMontoPenalidad` decimal(10,2) NOT NULL,
  `AlqCantidadMeses` int NOT NULL,
  `AlqFechaInicio` datetime NOT NULL,
  `AlqFechaFin` datetime NOT NULL,
  `AlqEstado` tinyint DEFAULT '1',
  `AlqFinalizado` tinyint DEFAULT '0',
  `AlqTienePenalidad` tinyint DEFAULT '0',
  `AlqObservacionPenalidad` varchar(255) DEFAULT NULL,
  `AlqFechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`AlqId`),
  KEY `ArrendadorId` (`ArrendadorId`),
  KEY `ClienteId` (`ClienteId`),
  CONSTRAINT `alquiler_ibfk_1` FOREIGN KEY (`ArrendadorId`) REFERENCES `Persona` (`PerId`),
  CONSTRAINT `alquiler_ibfk_2` FOREIGN KEY (`ClienteId`) REFERENCES `Persona` (`PerId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Alquiler`
--

LOCK TABLES `Alquiler` WRITE;
/*!40000 ALTER TABLE `Alquiler` DISABLE KEYS */;
INSERT INTO `Alquiler` VALUES (8,1,3,'Alquiler de Inmueble - Oficinas Yarina',350.00,175.00,70.00,2,'2024-06-11 00:00:00','2024-08-11 00:00:00',0,1,1,NULL,'2024-07-11 23:59:06'),(9,1,4,'Alquiler de Inmueble - Real Plaza 2',150.00,50.00,0.00,3,'2024-07-12 00:00:00','2024-10-12 00:00:00',1,1,0,NULL,'2024-07-12 17:31:44'),(10,1,4,'Alquiler de Inmueble - Oficinas Yarina',650.00,325.00,97.50,2,'2024-05-12 00:00:00','2024-07-12 00:00:00',0,1,1,NULL,'2024-07-13 11:04:23'),(11,1,4,'Alquiler de Inmueble - Oficinas Maldini',200.00,100.00,40.00,2,'2024-07-13 00:00:00','2024-09-13 00:00:00',0,1,1,NULL,'2024-07-13 17:26:56'),(12,1,4,'Alquiler de Inmueble - Centro Comercial Pucallpa',110.00,55.00,0.00,2,'2024-04-14 00:00:00','2024-06-14 00:00:00',1,1,0,NULL,'2024-07-14 14:46:08');
/*!40000 ALTER TABLE `Alquiler` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AlquilerDetalle`
--

DROP TABLE IF EXISTS `AlquilerDetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `AlquilerDetalle` (
  `AlqDetId` int NOT NULL AUTO_INCREMENT,
  `AlqId` int DEFAULT NULL,
  `HabInmId` int DEFAULT NULL,
  `AlqDetMonto` decimal(10,2) NOT NULL,
  `AlqDetEstado` tinyint DEFAULT '1',
  `AlqDetFechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`AlqDetId`),
  KEY `AlqId` (`AlqId`),
  KEY `HabInmId` (`HabInmId`),
  CONSTRAINT `alquilerdetalle_ibfk_1` FOREIGN KEY (`AlqId`) REFERENCES `Alquiler` (`AlqId`),
  CONSTRAINT `alquilerdetalle_ibfk_2` FOREIGN KEY (`HabInmId`) REFERENCES `HabitacionInmueble` (`HabInmId`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AlquilerDetalle`
--

LOCK TABLES `AlquilerDetalle` WRITE;
/*!40000 ALTER TABLE `AlquilerDetalle` DISABLE KEYS */;
INSERT INTO `AlquilerDetalle` VALUES (8,8,6,200.00,1,'2024-07-11 23:59:06'),(9,8,7,150.00,1,'2024-07-11 23:59:06'),(10,9,79,50.00,1,'2024-07-12 17:31:44'),(11,9,80,100.00,1,'2024-07-12 17:31:44'),(12,10,6,200.00,1,'2024-07-13 11:04:23'),(13,10,48,300.00,1,'2024-07-13 11:04:23'),(14,10,7,150.00,1,'2024-07-13 11:04:23'),(15,11,84,200.00,1,'2024-07-13 17:26:56'),(16,12,49,50.00,1,'2024-07-14 14:46:08'),(17,12,50,60.00,1,'2024-07-14 14:46:08');
/*!40000 ALTER TABLE `AlquilerDetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `HabitacionInmueble`
--

DROP TABLE IF EXISTS `HabitacionInmueble`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `HabitacionInmueble` (
  `HabInmId` int NOT NULL AUTO_INCREMENT,
  `PisInmId` int DEFAULT NULL,
  `HabInmNombre` varchar(255) NOT NULL,
  `HabInmPrecio` decimal(10,2) NOT NULL,
  `HabInmEstado` tinyint DEFAULT '0',
  `HabInmOcupado` tinyint DEFAULT '0',
  `HabInmFechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`HabInmId`),
  KEY `PisInmId` (`PisInmId`),
  CONSTRAINT `habitacioninmueble_ibfk_1` FOREIGN KEY (`PisInmId`) REFERENCES `PisoInmueble` (`PisInmId`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `HabitacionInmueble`
--

LOCK TABLES `HabitacionInmueble` WRITE;
/*!40000 ALTER TABLE `HabitacionInmueble` DISABLE KEYS */;
INSERT INTO `HabitacionInmueble` VALUES (6,12,'Habitacion 1',200.00,1,0,'2024-07-07 15:37:43'),(7,12,'Habitacion 2',150.00,1,0,'2024-07-07 15:37:43'),(8,12,'Habitacion 3',50.00,1,0,'2024-07-07 15:37:43'),(9,12,'Habitacion 4',0.00,0,0,'2024-07-07 15:37:43'),(26,16,'Habitacion 1',75.00,1,0,'2024-07-07 19:44:46'),(27,16,'Habitacion 2',0.00,0,0,'2024-07-07 19:44:46'),(28,16,'Habitacion 3',0.00,0,0,'2024-07-07 19:44:46'),(29,16,'Habitacion 4',0.00,0,0,'2024-07-07 19:44:46'),(30,16,'Habitacion 5',0.00,0,0,'2024-07-07 19:44:46'),(48,12,'Habitacion 5',300.00,1,0,'2024-07-08 21:50:02'),(49,20,'Habitacion 1',50.00,1,0,'2024-07-09 23:11:46'),(50,20,'Habitacion 2',60.00,1,0,'2024-07-09 23:11:46'),(51,20,'Habitacion 3',0.00,0,0,'2024-07-09 23:11:46'),(52,20,'Habitacion 4',0.00,0,0,'2024-07-09 23:11:46'),(53,20,'Habitacion 5',0.00,0,0,'2024-07-09 23:11:46'),(79,26,'Habitacion 1',50.00,1,0,'2024-07-12 17:28:49'),(80,26,'Habitacion 2',100.00,1,0,'2024-07-12 17:28:49'),(81,26,'Habitacion 3',0.00,0,0,'2024-07-12 17:28:49'),(82,26,'Habitacion 4',0.00,0,0,'2024-07-12 17:28:49'),(83,26,'Habitacion 5',0.00,0,0,'2024-07-12 17:28:49'),(84,27,'Pent house ',200.00,1,0,'2024-07-13 17:13:50'),(85,27,'Habitacion 2',200.00,1,0,'2024-07-13 17:13:50'),(86,27,'Habitacion 3',0.00,0,0,'2024-07-13 17:13:50'),(87,27,'Habitacion 4',0.00,0,0,'2024-07-13 17:13:50'),(89,27,'Habitacion 5',0.00,0,0,'2024-07-13 17:15:48');
/*!40000 ALTER TABLE `HabitacionInmueble` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Inmueble`
--

DROP TABLE IF EXISTS `Inmueble`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Inmueble` (
  `InmId` int NOT NULL AUTO_INCREMENT,
  `TipInmId` int DEFAULT NULL,
  `UsuId` int DEFAULT NULL,
  `InmNombre` varchar(255) NOT NULL,
  `InmDescripcion` text NOT NULL,
  `InmDireccion` varchar(255) NOT NULL,
  `InmFoto` varchar(255) DEFAULT NULL,
  `InmEstado` tinyint DEFAULT '0',
  `InmOcupado` tinyint DEFAULT '0',
  `InmFechaDadoAlta` datetime DEFAULT NULL,
  `InmFechaDadoBaja` datetime DEFAULT NULL,
  `InmFechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`InmId`),
  KEY `TipInmId` (`TipInmId`),
  KEY `UsuId` (`UsuId`),
  CONSTRAINT `inmueble_ibfk_1` FOREIGN KEY (`TipInmId`) REFERENCES `TipoInmueble` (`TipInmId`),
  CONSTRAINT `inmueble_ibfk_2` FOREIGN KEY (`UsuId`) REFERENCES `Usuario` (`UsuId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Inmueble`
--

LOCK TABLES `Inmueble` WRITE;
/*!40000 ALTER TABLE `Inmueble` DISABLE KEYS */;
INSERT INTO `Inmueble` VALUES (1,1,2,'Centro Comercial Pucallpa','El centro comercial mas grande de la ciudad de Pucallpa','Jr. Centenario 123','files/inmuebles/fotos/172072861066903c225192a.jpg',1,0,'2024-07-12 17:06:36','2024-07-12 17:06:28','2024-07-11 15:10:10'),(2,2,2,'Oficinas Yarina','Oficinas administrativas del distrito de YarinaCocha','Jr. Yarina 1220','files/inmuebles/fotos/1720371499668ac92b86833.jpg',1,0,'2024-07-07 12:04:06',NULL,'2024-07-07 11:58:19'),(3,1,2,'Real Plaza 2','Centro comercial de la ciudad de Pucallpa','Av. Centenario 1212','files/inmuebles/fotos/17208232506691add2bf5e5.jpg',1,0,'2024-07-12 17:30:32',NULL,'2024-07-12 17:28:37'),(4,2,2,'Oficinas Maldini','Las mejores oficinas administrativas de Yarina.','Jr. Las Perlas 123','files/inmuebles/fotos/17209087526692fbd0582fd.jpg',1,0,'2024-07-13 17:16:59',NULL,'2024-07-13 17:12:32');
/*!40000 ALTER TABLE `Inmueble` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PagoMensualidad`
--

DROP TABLE IF EXISTS `PagoMensualidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `PagoMensualidad` (
  `PagMenId` int NOT NULL AUTO_INCREMENT,
  `AlqId` int DEFAULT NULL,
  `PagMenMontoPago` decimal(10,2) NOT NULL,
  `PagMenMontoPagado` decimal(10,2) NOT NULL,
  `PagMenEstado` tinyint DEFAULT '0',
  `PagMenFechaPago` datetime DEFAULT NULL,
  PRIMARY KEY (`PagMenId`),
  KEY `AlqId` (`AlqId`),
  CONSTRAINT `pagomensualidad_ibfk_1` FOREIGN KEY (`AlqId`) REFERENCES `Alquiler` (`AlqId`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PagoMensualidad`
--

LOCK TABLES `PagoMensualidad` WRITE;
/*!40000 ALTER TABLE `PagoMensualidad` DISABLE KEYS */;
INSERT INTO `PagoMensualidad` VALUES (13,8,175.00,175.00,1,'2024-07-13 09:50:52'),(14,8,175.00,245.00,1,'2024-07-13 10:44:23'),(15,9,50.00,50.00,1,'2024-07-12 21:22:14'),(16,9,50.00,50.00,1,'2024-07-12 21:45:05'),(17,9,50.00,50.00,1,'2024-07-12 21:45:09'),(18,10,325.00,325.00,1,'2024-07-13 17:51:34'),(19,10,325.00,422.50,1,'2024-07-13 17:51:50'),(20,11,100.00,100.00,1,'2024-07-13 17:44:35'),(21,11,100.00,140.00,1,'2024-07-13 17:44:35'),(22,12,55.00,55.00,1,'2024-07-14 14:46:26'),(23,12,55.00,55.00,1,'2024-07-14 14:46:28');
/*!40000 ALTER TABLE `PagoMensualidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Persona`
--

DROP TABLE IF EXISTS `Persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Persona` (
  `PerId` int NOT NULL AUTO_INCREMENT,
  `PerDocumentoIdentidad` varchar(50) NOT NULL,
  `PerApellidoPaterno` varchar(255) NOT NULL,
  `PerApellidoMaterno` varchar(255) NOT NULL,
  `PerNombres` varchar(255) NOT NULL,
  `PerFechaNacimiento` date NOT NULL,
  `PerSexo` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `PerCorreo` varchar(255) NOT NULL,
  `PerDireccion` varchar(255) NOT NULL,
  `PerEstado` tinyint DEFAULT '1',
  `PerFechaCreacion` datetime NOT NULL,
  `TipEviId` int DEFAULT NULL,
  `PerTipoEvidenciaArchivo` varchar(255) DEFAULT NULL,
  `PerReferenciaAlquiler` text,
  PRIMARY KEY (`PerId`),
  KEY `TipEviId` (`TipEviId`),
  CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`TipEviId`) REFERENCES `TipoEvidencia` (`TipEviId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Persona`
--

LOCK TABLES `Persona` WRITE;
/*!40000 ALTER TABLE `Persona` DISABLE KEYS */;
INSERT INTO `Persona` VALUES (1,'87654321','Melendez','Perez','Juan Antonio','2000-01-13','M','antonio@gmail.com','Jr Sin Nombre 123',1,'2024-07-11 13:51:00',NULL,NULL,NULL),(3,'22222222','Mendoza','Flores','Valentino','2004-09-28','M','valentino@gmail.com','Jr Los Cedros 240',1,'2024-07-11 15:11:06',2,'files/evidencias/personas/172072866666903c5a30e67.pdf',NULL),(4,'72155069','Mendoza','Flores','Jamt','2000-10-24','M','pupilojamt2014@gmail.com','Jr. Los Cedros 240',1,'2024-07-12 17:22:11',1,'files/evidencias/personas/17208229316691ac93a4a0b.pdf',NULL),(5,'99999999','Fernandez','Portocarrero','Gina','1999-02-13','F','gina@gmail.com','Jr Yarina 123',1,'2024-07-14 13:29:22',2,'files/evidencias/personas/172098176266941902e06bc.pdf',NULL);
/*!40000 ALTER TABLE `Persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PisoInmueble`
--

DROP TABLE IF EXISTS `PisoInmueble`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `PisoInmueble` (
  `PisInmId` int NOT NULL AUTO_INCREMENT,
  `InmId` int DEFAULT NULL,
  `PisInmNumeroPiso` int NOT NULL,
  `PisInmEstado` tinyint DEFAULT '0',
  `PisInmOcupado` tinyint DEFAULT '0',
  `PisInmFechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`PisInmId`),
  KEY `InmId` (`InmId`),
  CONSTRAINT `pisoinmueble_ibfk_1` FOREIGN KEY (`InmId`) REFERENCES `Inmueble` (`InmId`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PisoInmueble`
--

LOCK TABLES `PisoInmueble` WRITE;
/*!40000 ALTER TABLE `PisoInmueble` DISABLE KEYS */;
INSERT INTO `PisoInmueble` VALUES (12,2,1,1,0,'2024-07-07 15:37:43'),(16,2,2,1,0,'2024-07-07 19:44:46'),(20,1,1,1,0,'2024-07-09 23:11:46'),(26,3,1,1,0,'2024-07-12 17:28:49'),(27,4,1,1,0,'2024-07-13 17:13:50');
/*!40000 ALTER TABLE `PisoInmueble` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Rol`
--

DROP TABLE IF EXISTS `Rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rol` (
  `RolId` int NOT NULL AUTO_INCREMENT,
  `RolNombre` varchar(255) NOT NULL,
  `RolEstado` tinyint NOT NULL,
  PRIMARY KEY (`RolId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Rol`
--

LOCK TABLES `Rol` WRITE;
/*!40000 ALTER TABLE `Rol` DISABLE KEYS */;
INSERT INTO `Rol` VALUES (1,'Administrador',1),(2,'Arrendador',1),(3,'Cliente',1);
/*!40000 ALTER TABLE `Rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoEvidencia`
--

DROP TABLE IF EXISTS `TipoEvidencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoEvidencia` (
  `TipEviId` int NOT NULL AUTO_INCREMENT,
  `TipEviNombre` varchar(255) NOT NULL,
  `TipEviEstado` tinyint DEFAULT '1',
  PRIMARY KEY (`TipEviId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoEvidencia`
--

LOCK TABLES `TipoEvidencia` WRITE;
/*!40000 ALTER TABLE `TipoEvidencia` DISABLE KEYS */;
INSERT INTO `TipoEvidencia` VALUES (1,'Aval Bancario',1),(2,'Contrato de Trabajo',1),(3,'Avalado por otra persona',1);
/*!40000 ALTER TABLE `TipoEvidencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoInmueble`
--

DROP TABLE IF EXISTS `TipoInmueble`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoInmueble` (
  `TipInmId` int NOT NULL AUTO_INCREMENT,
  `TipInmNombre` varchar(255) NOT NULL,
  `TipInmEstado` tinyint DEFAULT '1',
  PRIMARY KEY (`TipInmId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoInmueble`
--

LOCK TABLES `TipoInmueble` WRITE;
/*!40000 ALTER TABLE `TipoInmueble` DISABLE KEYS */;
INSERT INTO `TipoInmueble` VALUES (1,'Local Comercial',1),(2,'Oficina',1);
/*!40000 ALTER TABLE `TipoInmueble` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuario`
--

DROP TABLE IF EXISTS `Usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Usuario` (
  `UsuId` int NOT NULL AUTO_INCREMENT,
  `UsuUsername` varchar(255) NOT NULL,
  `UsuContrasena` varchar(255) NOT NULL,
  `RolId` int DEFAULT NULL,
  `PerId` int DEFAULT NULL,
  `UsuEstado` tinyint DEFAULT '0',
  `UsuFechaDadoAlta` datetime DEFAULT NULL,
  `UsuFechaDadoBaja` datetime DEFAULT NULL,
  `UsuFechaCreacion` datetime NOT NULL,
  PRIMARY KEY (`UsuId`),
  KEY `RolId` (`RolId`),
  KEY `PerId` (`PerId`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`RolId`) REFERENCES `Rol` (`RolId`),
  CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`PerId`) REFERENCES `Persona` (`PerId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuario`
--

LOCK TABLES `Usuario` WRITE;
/*!40000 ALTER TABLE `Usuario` DISABLE KEYS */;
INSERT INTO `Usuario` VALUES (1,'webmaster','$2y$12$QMspmHj15C3I7PDRGaltreetsEAwTlrN9Q7XJffXTDHgccFhMxWIe',1,NULL,1,NULL,NULL,'2024-07-04 16:06:21'),(2,'arrendador','$2y$12$QMspmHj15C3I7PDRGaltreetsEAwTlrN9Q7XJffXTDHgccFhMxWIe',2,1,1,NULL,NULL,'2024-07-04 16:06:21'),(4,'valentino','$2y$12$jfNzWwTP91fBDDH1ETeh2u0GqZG9nQOm084IMWheUTNfRd9Tr9TSq',3,3,1,'2024-07-11 18:00:40',NULL,'2024-07-11 15:11:06'),(5,'jamt','$2y$12$VyGl7eKropAe5LmJIWcpu.zH.Nhtfch/FFKVFlrRRumQmo4jE0n2y',3,4,1,'2024-07-12 17:23:34',NULL,'2024-07-12 17:22:11'),(6,'gina','$2y$12$EKMQeARxVDeVPcyX3nVMq.PKjiKT/VghyD68O8l3hd203S.MO8QCu',3,5,0,NULL,NULL,'2024-07-14 13:29:23');
/*!40000 ALTER TABLE `Usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'app_inmueble'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-07 22:03:33
