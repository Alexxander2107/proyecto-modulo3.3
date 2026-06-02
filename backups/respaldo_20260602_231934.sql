-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: marketplace_express
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auditoria_cambios`
--

DROP TABLE IF EXISTS `auditoria_cambios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria_cambios` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `tabla` varchar(100) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_cambios`
--

LOCK TABLES `auditoria_cambios` WRITE;
/*!40000 ALTER TABLE `auditoria_cambios` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria_cambios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Accesorios'),(2,'Almacenamiento'),(3,'Audio'),(4,'Computadoras'),(5,'Electrónica'),(6,'Hogar'),(7,'Oficina'),(8,'Redes'),(9,'Telefonía'),(10,'Videojuegos');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_cliente_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Ana','Ramirez','ana@gmail.com','7000-4444','La Libertad'),(2,'Elena','Cruz','elena@gmail.com','7000-6666','Usulutan'),(3,'Pedro','Vasquez','pedro@gmail.com','7000-7777','Ahuachapan'),(4,'Laura','Torres','laura@gmail.com','7000-1010','Morazan'),(5,'Eliseo ','Martinez','Eliseo.martinez@gmail.com','2121-2828','San Salvador'),(6,'Juan ','Perez','juan@example.com','555-0101',''),(12,'Maria Gomez','','maria@example.com','555-0102',NULL),(13,'Carlos Ruiz','','carlos@example.com','555-0103',NULL),(14,'Ana Lopez','','ana@example.com','555-0104',NULL),(15,'Luis Martinez','','luis@example.com','555-0105',NULL),(16,'Sofia Garcia','','sofia@example.com','555-0106',NULL),(17,'Diego Hernandez','','diego@example.com','555-0107',NULL),(18,'Valeria Lopez','','valeria@example.com','555-0108',NULL),(19,'David Gonzalez','','david@example.com','555-0109',NULL),(20,'Isabella Diaz','','isabella@example.com','555-0110',NULL),(21,'Javier Ramirez','','javier@example.com','555-0111',NULL),(22,'Marco Torres','','marco@example.com','555-0112',NULL),(23,'Luis Castro','','lcastro@example.com','555-0113',NULL),(24,'Pedro Morales','','pedro@example.com','555-0114',NULL),(25,'Fernando Ortiz','','fernando@example.com','555-0115',NULL),(26,'Jorge Vargas','','jorge@example.com','555-0116',NULL),(27,'Eduardo Jimenez','','eduardo@example.com','555-0117',NULL),(28,'Ricardoendoza','','ricardo@example.com','555-0118',NULL),(29,'Alejandro Flores','','alejandro@example.com','555-0119',NULL),(30,'Miguel Diaz','','miguel@example.com','555-0120',NULL),(151,'Juan','Perez','juan@gmail.com','70000001','San Salvador'),(152,'Maria','Lopez','maria@gmail.com','70000002','Santa Ana'),(153,'Carlos','Martinez','carlos@gmail.com','70000003','Sonsonate'),(154,'Ana','Hernandez','ana1@gmail.com','70000004','La Libertad'),(155,'Luis','Garcia','luis@gmail.com','70000005','San Miguel'),(156,'Sofia','Ramos','sofia@gmail.com','70000006','Usulutan'),(157,'Pedro','Cruz','pedro2@gmail.com','70000007','Ahuachapan'),(158,'Daniela','Ruiz','daniela@gmail.com','70000008','La Union'),(159,'Jose','Flores','jose@gmail.com','70000009','Chalatenango'),(160,'Laura','Mendez','laura5@gmail.com','70000010','Cuscatlan'),(161,'Miguel','Castro','miguel@gmail.com','70000011','San Salvador'),(162,'Patricia','Vega','patricia@gmail.com','70000012','Santa Ana'),(163,'Ricardo','Morales','ricardo@gmail.com','70000013','San Miguel'),(164,'Gabriela','Santos','gabriela@gmail.com','70000014','La Libertad'),(165,'Fernando','Ortiz','fernando@gmail.com','70000015','Usulutan'),(166,'Elena','Campos','elena1@gmail.com','70000016','Sonsonate'),(167,'Kevin','Rivera','kevin@gmail.com','70000017','Ahuachapan'),(168,'Andrea','Silva','andrea@gmail.com','70000018','La Union'),(169,'Jorge','Rojas','jorge@gmail.com','70000019','San Vicente'),(170,'Lucia','Mejia','lucia2@gmail.com','70000020','Morazan'),(171,'Oscar','Diaz','oscar@gmail.com','70000021','Cuscatlan'),(172,'Monica','Pineda','monica@gmail.com','70000022','San Salvador'),(173,'David','Escobar','david@gmail.com','70000023','Santa Ana'),(174,'Carla','Reyes','carla@gmail.com','70000024','La Paz'),(175,'Mario','Portillo','mario2@gmail.com','70000025','San Miguel'),(176,'Karen','Salazar','karen@gmail.com','70000026','Sonsonate'),(177,'Hector','Alvarado','hector@gmail.com','70000027','La Libertad'),(178,'Paola','Molina','paola@gmail.com','70000028','Ahuachapan'),(179,'Victor','Aguilar','victor@gmail.com','70000029','La Union'),(180,'Natalia','Luna','natalia@gmail.com','70000030','San Salvador'),(181,'luis','diaz','luchodiaz@gmail.com',NULL,NULL),(182,'Mauri','Sixcevito','maurifox@gmail.com','1122-3344','Londres');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_backup`
--

DROP TABLE IF EXISTS `clientes_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes_backup` (
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_backup`
--

LOCK TABLES `clientes_backup` WRITE;
/*!40000 ALTER TABLE `clientes_backup` DISABLE KEYS */;
INSERT INTO `clientes_backup` VALUES (1,'Carlos','Lopez','carlos@gmail.com','7000-1111','San Salvador'),(2,'Maria','Hernandez','maria@gmail.com','7000-2222','Santa Ana'),(3,'Jose','Martinez','jose@gmail.com','7000-3333','San Miguel'),(4,'Ana','Ramirez','ana@gmail.com','7000-4444','La Libertad'),(5,'Luis','Gomez','luis@gmail.com','7000-5555','Sonsonate'),(6,'Elena','Cruz','elena@gmail.com','7000-6666','Usulutan'),(7,'Pedro','Vasquez','pedro@gmail.com','7000-7777','Ahuachapan'),(8,'Sofia','Reyes','sofia@gmail.com','7000-8888','Chalatenango'),(9,'Miguel','Flores','miguel@gmail.com','7000-9999','Cuscatlan'),(10,'Laura','Torres','laura@gmail.com','7000-1010','Morazan');
/*!40000 ALTER TABLE `clientes_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_venta` (`id_venta`),
  KEY `fk_detalle_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (5,4,7,1,0.00,0.00,700.00),(8,6,9,1,0.00,0.00,150.00),(9,7,8,1,0.00,0.00,60.00),(10,10,10,1,0.00,0.00,80.00),(89,4,131,2,18.99,0.00,18.99),(90,4,132,1,45.00,0.00,45.00),(91,4,133,1,12.50,0.00,12.50),(92,6,135,1,45.00,0.00,45.00),(93,6,136,1,89.99,0.00,89.99),(94,6,137,2,9.99,0.00,9.99),(95,7,139,1,65.00,0.00,65.00),(96,7,140,1,110.00,0.00,110.00),(97,10,121,1,31.00,0.00,31.00),(98,10,122,1,32.00,0.00,32.00),(99,10,123,1,33.00,0.00,33.00);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `cargo` varchar(50) NOT NULL DEFAULT 'usuario',
  PRIMARY KEY (`id_empleado`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'Administrador','admin@marketplace.com','$2y$10$U2zAhWTsCzdkvJZIUQakfuKxN.n1rs/vMeq8BQqFwDXIme83qNd9e','admin'),(2,'Juan Pérez','juan.perez@marketplace.com','$2y$10$npUQOW1V7d9Kk7XnO4Y4xOCK1ND6MtSIRXHa4NSlqF2rOheKjPYwy','vendedor'),(3,'María López','maria.lopez@marketplace.com','$2y$10$9LDnkmWJqRoJJqMIfMcFBuNlMcOTXZYtn7OvL0.igLDJ5dM.XxhTu','usuario'),(4,'alexander','alexander@gmail.com','$2y$10$COU5SYuAwxJwOQn0zTn1sOIs/v74p/TnqzlS2MEgCkHcuoLpjiCXm','usuario'),(5,'francisco martinez','franciscomartinez1@gmail.com','$2y$10$MSr2cKAVV8kFTUPMycc6VeeVoLBfcgz1Svvjh8w4H9IL/LwP2ZWl6','auditor'),(6,'Mario Ramirez','mario@marketplace.com','123456','Administrador'),(7,'Lucia Perez','lucia@marketplace.com','123456','Vendedor'),(8,'Roberto Castro','roberto@marketplace.com','123456','Vendedor'),(9,'Sandra Lopez','sandra@marketplace.com','123456','Bodega'),(10,'Eduardo Martinez','eduardo@marketplace.com','123456','Gerente'),(11,'Patricia Ruiz','patricia@marketplace.com','123456','Vendedor'),(12,'Julio Hernandez','julio@marketplace.com','123456','Bodega'),(13,'Diana Flores','diana@marketplace.com','123456','Caja'),(14,'Kevin Morales','kevin@marketplace.com','123456','Soporte'),(15,'Andrea Mendez','andrea@marketplace.com','123456','Caja');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `fk_producto_categoria` (`id_categoria`),
  KEY `idx_producto_nombre` (`nombre`),
  CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (116,'Producto26',26.00,50,6),(117,'Producto27',27.00,50,7),(118,'Producto28',28.00,50,8),(119,'Producto29',29.00,50,9),(120,'Producto30',30.00,50,10),(121,'Producto31',31.00,50,1),(122,'Producto32',32.00,50,2),(123,'Producto33',33.00,50,3),(124,'Producto34',34.00,50,4),(125,'Producto35',35.00,50,5),(126,'Producto36',36.00,50,6),(127,'Producto37',37.00,50,7),(128,'Producto38',38.00,50,8),(129,'Producto39',39.00,50,9),(130,'Producto40',40.00,50,10),(131,'Mouse Logitech G203',18.99,50,1),(132,'Teclado Mecánico Redragon',45.00,30,1),(133,'Mouse Pad XL Gamer',12.50,40,1),(134,'Webcam Full HD',35.99,25,1),(135,'SSD Kingston 480GB',45.00,20,2),(136,'SSD Samsung 1TB',89.99,15,2),(137,'Memoria USB 64GB',9.99,60,2),(138,'Disco Duro Externo 2TB',75.00,18,2),(139,'Auriculares Sony WH-CH520',65.00,20,3),(140,'Bocina JBL Flip 6',110.00,15,3),(141,'Micrófono HyperX SoloCast',59.99,12,3),(142,'Audífonos Bluetooth Xiaomi',29.99,25,3),(143,'Laptop HP 15\"',650.00,10,4),(144,'Laptop Dell Inspiron',799.99,8,4),(145,'Mini PC Lenovo ThinkCentre',450.00,7,4),(146,'Monitor LG 24\"',180.00,15,4),(147,'Smart TV Samsung 43\"',420.00,6,5),(148,'Smart Watch Huawei',120.00,20,5),(149,'Proyector Epson',350.00,5,5),(150,'Tablet Samsung Galaxy Tab',280.00,10,5),(151,'Aspiradora Robot Xiaomi',250.00,8,6),(152,'Ventilador de Torre',65.00,12,6),(153,'Lámpara LED Inteligente',22.00,30,6),(154,'Cafetera Oster',55.00,10,6),(155,'Impresora HP LaserJet',220.00,8,7),(156,'Silla Ergonómica',145.00,10,7),(157,'Escritorio Ejecutivo',180.00,6,7),(158,'Trituradora de Papel',75.00,8,7),(159,'Router TP-Link AX1800',85.00,15,8),(160,'Switch TP-Link 8 Puertos',40.00,20,8),(161,'Access Point Ubiquiti',120.00,10,8),(162,'Cable de Red Cat6 20m',15.00,40,8),(163,'iPhone 14',899.99,7,9),(164,'Samsung Galaxy S24',850.00,10,9),(165,'Xiaomi Redmi Note 14',320.00,15,9),(166,'Cargador USB-C 25W',18.00,35,9),(167,'PlayStation 5',650.00,5,10),(168,'Xbox Series X',620.00,5,10),(169,'Nintendo Switch OLED',380.00,8,10),(170,'Control Inalámbrico Xbox',65.00,20,10);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_auditoria_productos
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_cambios
    (tabla, accion, descripcion)
    VALUES
    (
        'productos',
        'UPDATE',
        CONCAT('Producto actualizado ID: ', OLD.id_producto)
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `v_clientes_gerente`
--

DROP TABLE IF EXISTS `v_clientes_gerente`;
/*!50001 DROP VIEW IF EXISTS `v_clientes_gerente`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_clientes_gerente` AS SELECT
 1 AS `id_cliente`,
  1 AS `nombre`,
  1 AS `apellido`,
  1 AS `email`,
  1 AS `telefono`,
  1 AS `direccion` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_clientes_vendedor`
--

DROP TABLE IF EXISTS `v_clientes_vendedor`;
/*!50001 DROP VIEW IF EXISTS `v_clientes_vendedor`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_clientes_vendedor` AS SELECT
 1 AS `id_cliente`,
  1 AS `nombre`,
  1 AS `apellido`,
  1 AS `telefono` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_productos_basico`
--

DROP TABLE IF EXISTS `v_productos_basico`;
/*!50001 DROP VIEW IF EXISTS `v_productos_basico`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_productos_basico` AS SELECT
 1 AS `id_producto`,
  1 AS `nombre`,
  1 AS `precio`,
  1 AS `stock`,
  1 AS `id_categoria` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_reportes_auditoria`
--

DROP TABLE IF EXISTS `v_reportes_auditoria`;
/*!50001 DROP VIEW IF EXISTS `v_reportes_auditoria`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_reportes_auditoria` AS SELECT
 1 AS `id_auditoria`,
  1 AS `tabla`,
  1 AS `accion`,
  1 AS `descripcion`,
  1 AS `fecha` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_reportes_ventas`
--

DROP TABLE IF EXISTS `v_reportes_ventas`;
/*!50001 DROP VIEW IF EXISTS `v_reportes_ventas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_reportes_ventas` AS SELECT
 1 AS `id_venta`,
  1 AS `fecha`,
  1 AS `cliente`,
  1 AS `vendedor`,
  1 AS `total` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_ventas_basico`
--

DROP TABLE IF EXISTS `v_ventas_basico`;
/*!50001 DROP VIEW IF EXISTS `v_ventas_basico`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_ventas_basico` AS SELECT
 1 AS `id_venta`,
  1 AS `fecha`,
  1 AS `id_cliente`,
  1 AS `id_empleado`,
  1 AS `total` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_venta`),
  KEY `fk_venta_cliente` (`id_cliente`),
  KEY `fk_venta_empleado` (`id_empleado`),
  KEY `idx_ventas_fecha` (`fecha`),
  CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_venta_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (4,'2026-05-04',1,1,700.00),(6,'2026-05-06',2,5,150.00),(7,'2026-05-07',3,6,60.00),(10,'2026-05-10',4,9,80.00);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `v_clientes_gerente`
--

/*!50001 DROP VIEW IF EXISTS `v_clientes_gerente`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_clientes_gerente` AS select `clientes`.`id_cliente` AS `id_cliente`,`clientes`.`nombre` AS `nombre`,`clientes`.`apellido` AS `apellido`,`clientes`.`email` AS `email`,`clientes`.`telefono` AS `telefono`,`clientes`.`direccion` AS `direccion` from `clientes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_clientes_vendedor`
--

/*!50001 DROP VIEW IF EXISTS `v_clientes_vendedor`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_clientes_vendedor` AS select `clientes`.`id_cliente` AS `id_cliente`,`clientes`.`nombre` AS `nombre`,`clientes`.`apellido` AS `apellido`,`clientes`.`telefono` AS `telefono` from `clientes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_productos_basico`
--

/*!50001 DROP VIEW IF EXISTS `v_productos_basico`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_productos_basico` AS select `productos`.`id_producto` AS `id_producto`,`productos`.`nombre` AS `nombre`,`productos`.`precio` AS `precio`,`productos`.`stock` AS `stock`,`productos`.`id_categoria` AS `id_categoria` from `productos` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_reportes_auditoria`
--

/*!50001 DROP VIEW IF EXISTS `v_reportes_auditoria`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_reportes_auditoria` AS select `auditoria_cambios`.`id_auditoria` AS `id_auditoria`,`auditoria_cambios`.`tabla` AS `tabla`,`auditoria_cambios`.`accion` AS `accion`,`auditoria_cambios`.`descripcion` AS `descripcion`,`auditoria_cambios`.`fecha` AS `fecha` from `auditoria_cambios` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_reportes_ventas`
--

/*!50001 DROP VIEW IF EXISTS `v_reportes_ventas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_reportes_ventas` AS select `v`.`id_venta` AS `id_venta`,`v`.`fecha` AS `fecha`,`c`.`nombre` AS `cliente`,`e`.`nombre` AS `vendedor`,`v`.`total` AS `total` from ((`ventas` `v` left join `clientes` `c` on(`v`.`id_cliente` = `c`.`id_cliente`)) left join `empleados` `e` on(`v`.`id_empleado` = `e`.`id_empleado`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_ventas_basico`
--

/*!50001 DROP VIEW IF EXISTS `v_ventas_basico`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_ventas_basico` AS select `ventas`.`id_venta` AS `id_venta`,`ventas`.`fecha` AS `fecha`,`ventas`.`id_cliente` AS `id_cliente`,`ventas`.`id_empleado` AS `id_empleado`,`ventas`.`total` AS `total` from `ventas` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-02 15:19:35
