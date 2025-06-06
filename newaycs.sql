-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: newaycs
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `Id_Cliente` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Número` varchar(20) NOT NULL,
  `Dirección` varchar(100) NOT NULL,
  PRIMARY KEY (`Id_Cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `Id_Producto` int(11) NOT NULL AUTO_INCREMENT,
  `Cantidad` decimal(10,0) NOT NULL,
  PRIMARY KEY (`Id_Producto`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`Id_Producto`) REFERENCES `productos` (`Id_Productos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kit`
--

DROP TABLE IF EXISTS `kit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kit` (
  `Id_Kit` int(11) NOT NULL AUTO_INCREMENT,
  `Precio` decimal(10,2) NOT NULL,
  `Id_productos` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Kit`),
  KEY `Id_productos` (`Id_productos`),
  CONSTRAINT `kit_ibfk_1` FOREIGN KEY (`Id_productos`) REFERENCES `productos` (`Id_Productos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kit`
--

LOCK TABLES `kit` WRITE;
/*!40000 ALTER TABLE `kit` DISABLE KEYS */;
/*!40000 ALTER TABLE `kit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `Id_Productos` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(500) NOT NULL,
  `Costo` decimal(10,2) NOT NULL,
  `Precio_Publico` decimal(10,2) DEFAULT '0.00',
  `Precio_Instalador` decimal(10,2) DEFAULT '0.00',
  `Margen_Utilidad` decimal(5,2) DEFAULT '0.00',
  `Ganancia` decimal(10,2) DEFAULT '0.00',
  `Foto` varchar(500) DEFAULT NULL,
  `Categoria` int(11) UNSIGNED DEFAULT NULL,
  `Id_Proveedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Productos`),
  KEY `Categoria` (`Categoria`),
  KEY `Id_Proveedor` (`Id_Proveedor`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`Categoria`) REFERENCES `categories` (`id`),
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`Id_Proveedor`) REFERENCES `proveedores` (`Id_Proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--


LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicio`
--

DROP TABLE IF EXISTS `servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicio` (
  `Id_Servicio` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Descripción` varchar(50) NOT NULL,
  `Costo` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Id_Servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio`
--

LOCK TABLES `servicio` WRITE;
/*!40000 ALTER TABLE `servicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venta`
--

DROP TABLE IF EXISTS `venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venta` (
  `Folio` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` date NOT NULL,
  `Id_Servicio` int(11) DEFAULT NULL,
  `Id_Cliente` int(11) NOT NULL,
  `Id_Productos` int(11) DEFAULT NULL,
  PRIMARY KEY (`Folio`),
  KEY `Id_Servicio` (`Id_Servicio`),
  KEY `Id_Cliente` (`Id_Cliente`),
  KEY `Id_Productos` (`Id_Productos`),
  CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`Id_Servicio`) REFERENCES `servicio` (`Id_Servicio`),
  CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`Id_Cliente`) REFERENCES `clientes` (`Id_Cliente`),
  CONSTRAINT `venta_ibfk_3` FOREIGN KEY (`Id_Productos`) REFERENCES `productos` (`Id_Productos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_level` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no_image.jpg',
  `status` int(1) NOT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `user_level`, `image`, `status`, `last_login`) VALUES
(1, 'Admin Users', 'admin', 'admin', 1, 'pzg9wa7o1.jpg', 1, '2020-06-16 07:11:11'),
(2, 'Special User', 'special', 'special', 2, 'no_image.jpg', 1, '2025-06-45 07:11:26'),
(3, 'Default User', 'user', 'user', 3, 'no_image.jpg', 1, '2023-06-17 07:11:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(150) NOT NULL,
  `group_level` int(11) NOT NULL,
  `group_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `user_groups`
--

INSERT INTO `user_groups` (`id`, `group_name`, `group_level`, `group_status`) VALUES
(1, 'Admin', 1, 1),
(2, 'Special', 2, 0),
(3, 'User', 3, 1);

--
-- Estructura de tabla para la tabla `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venta`
--

LOCK TABLES `venta` WRITE;
/*!40000 ALTER TABLE `venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `venta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-12 17:57:56

--
--CAMBIOS DEL DIA 19 DE MAYO DEL 2025 AGREGUE LO DE COTIZACIÓN Y PERSONZALIZAR PERFIL DEL INICIO`
-- Estructura de tabla para la tabla `quotes`
--

CREATE TABLE `quotes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NULL, -- Eliminado UNSIGNED para que coincida con clientes.Id_Cliente
  `client_name` varchar(100) NOT NULL, -- Guardar nombre aunque sea cliente registrado, para historial
  `client_phone` varchar(20) NULL,
  `client_location` varchar(255) NULL,
  `quote_date` date NOT NULL,
  `quote_type` varchar(100) NULL,
  `subtotal` decimal(25,2) DEFAULT '0.00',
  `discount_percentage` decimal(5,2) DEFAULT '0.00',
  `total_amount` decimal(25,2) DEFAULT '0.00',
  `observations` text NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clientes`(`Id_Cliente`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
--CAMBIOS DEL DIA 19 DE MAYO DEL 2025 AGREGUE LO DE COTIZACIÓN Y PERSONZALIZAR PERFIL DEL INICIO
-- Estructura de tabla para la tabla `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) NULL, -- Eliminado UNSIGNED para que coincida con productos.Id_Productos
  `service_id` int(11) NULL, -- Eliminado UNSIGNED para que coincida con servicio.Id_Servicio
  `description` varchar(255) NOT NULL, -- Descripción del ítem (puede sobrescribir producto/servicio)
  `quantity` int(11) NOT NULL DEFAULT '1',
  `unit_price` decimal(25,2) NOT NULL,
  `total_price` decimal(25,2) NOT NULL, -- Cantidad * Precio Unitario
  `image` varchar(255) NULL, -- Ruta a la imagen ilustrativa
  PRIMARY KEY (`id`),
  FOREIGN KEY (`quote_id`) REFERENCES `quotes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `productos`(`Id_Productos`) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (`service_id`) REFERENCES `servicio`(`Id_Servicio`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_title` varchar(255) DEFAULT 'Bienvenido al Sistema',
  `banner_text` text DEFAULT 'Sistema de Gestión de Inventario',
  `banner_image` varchar(255) DEFAULT 'libs/images/default-banner.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO settings (banner_title, banner_text, banner_image)
SELECT 'Bienvenido al Sistema', 'Sistema de Gestión de Inventario', 'libs/images/default-banner.jpg'
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE id = 1);

--
-- Estructura de tabla para la tabla `kits`
--

CREATE TABLE `kits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio_base` decimal(10,2) NOT NULL,
  `precio_por_camara` decimal(10,2) NOT NULL DEFAULT '450.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `kit_items`
--

CREATE TABLE `kit_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kit_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_base` int(11) NOT NULL,
  `cantidad_por_camara` decimal(10,2) NOT NULL DEFAULT '0.00',
  `es_por_camara` tinyint(1) NOT NULL DEFAULT '0',
  `es_servicio` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `kit_id` (`kit_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `kit_items_ibfk_1` FOREIGN KEY (`kit_id`) REFERENCES `kits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kit_items_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`Id_Productos`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `quote_kit_items`
--

CREATE TABLE `quote_kit_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) UNSIGNED NOT NULL,
  `kit_id` int(11) NOT NULL,
  `cantidad_camaras` int(11) NOT NULL,
  `precio_por_camara` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `observaciones` text,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `kit_id` (`kit_id`),
  CONSTRAINT `quote_kit_items_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quote_kit_items_ibfk_2` FOREIGN KEY (`kit_id`) REFERENCES `kits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
