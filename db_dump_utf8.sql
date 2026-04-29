-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: parkingapp
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
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(50) DEFAULT NULL,
  `car_model` varchar(50) DEFAULT NULL,
  `car_color` varchar(50) DEFAULT NULL,
  `slot_id` varchar(10) NOT NULL,
  `parking_guid` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'occupied',
  PRIMARY KEY (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cars`
--

LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES (1,'5555',NULL,NULL,'B1','',NULL,'occupied'),(2,'0231',NULL,NULL,'C3','',NULL,'occupied'),(3,'7019',NULL,NULL,'A2','',NULL,'occupied'),(4,'ABC-447',NULL,NULL,'B2','',NULL,'occupied'),(5,'ABC-264',NULL,NULL,'M5','',NULL,'occupied'),(6,'ABC-226',NULL,NULL,'B1','',NULL,'occupied'),(7,'ABC-278','Heavy Duty Truck','White','M1','PARK-69b6a1ad8eb0a',NULL,'occupied'),(8,'ABC-405','Sedan Car','White','A3','PARK-69b6a280bae4e',NULL,'occupied'),(9,'ABC-647','Heavy Duty Truck','White','E10','PARK-69b6a90bcd501',NULL,'occupied'),(10,'ABC-883','Sedan Car','White','A1','PARK-69b6b08816e2b',NULL,'occupied'),(11,'ABC-844','Sedan Car','White','C8','PARK-69b6b27553532',NULL,'occupied'),(12,'ABC-892','Sedan Car','White','C9','PARK-69b7e753810a9',NULL,'occupied'),(13,'ABC-952','Heavy Duty Truck','White','C18','PARK-69b9385b3b75b',NULL,'occupied'),(14,'ABC-955','Heavy Duty Truck','White','D5','PARK-69b94f1eb5175',NULL,'occupied'),(15,'ABC-592','Sedan Car','White','D7','PARK-69b9522f9d0b5',NULL,'occupied'),(16,'ABC-391','Sedan Car','White','D8','PARK-69b9562f670a2',NULL,'occupied'),(17,'ABC-466','Sedan Car','White','D9','PARK-69b957bf8f2cb',NULL,'occupied'),(18,'ABC-623','Sedan Car','White','D4','PARK-69b959350b1c3',NULL,'reserved'),(19,'ABC-275','Sedan Car','White','D3','PARK-69b95deff4114',NULL,'reserved'),(20,'ABC-923','Heavy Duty Truck','White','E8','PARK-69be9f1964d77',NULL,'reserved');
/*!40000 ALTER TABLE `cars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` VALUES (1,'mariam nesem','test@mail.com','╪▓╪¡┘à┘ç ╪¼╪»╪º','2026-03-08 11:26:32'),(2,'mora nesem','test@mail.com','┘ä┘è╪│ ┘è┘ê╪¼╪» ┘à╪┤┘â┘ä┘ç\r\n','2026-03-09 10:34:29'),(3,'mariam','test@mail.com','┘ä╪º ┘è┘ê╪¼╪» ┘à┘â╪º┘å','2026-03-09 11:16:34');
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parking_history`
--

DROP TABLE IF EXISTS `parking_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `plate_number` varchar(50) NOT NULL,
  `slot_id` varchar(20) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `total_fee` decimal(10,2) DEFAULT 0.00,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'completed',
  `cost` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_history`
--

LOCK TABLES `parking_history` WRITE;
/*!40000 ALTER TABLE `parking_history` DISABLE KEYS */;
INSERT INTO `parking_history` VALUES (1,NULL,NULL,'ABC-955','D5','2026-03-17 14:54:55',NULL,0.00,NULL,'completed',0.00),(2,NULL,NULL,'ABC-592','D7','2026-03-17 15:07:59',NULL,0.00,NULL,'completed',0.00),(3,NULL,NULL,'ABC-391','D8','2026-03-17 15:25:03',NULL,0.00,NULL,'completed',0.00),(4,NULL,NULL,'ABC-466','D9','2026-03-17 15:31:43',NULL,0.00,NULL,'completed',0.00),(5,NULL,NULL,'ABC-623','D4','2026-03-17 15:37:57',NULL,0.00,NULL,'completed',0.00),(6,NULL,NULL,'ABC-275','D3','2026-03-17 15:58:09',NULL,0.00,NULL,'completed',0.00),(7,NULL,NULL,'ABC-923','E8','2026-03-21 15:37:29',NULL,0.00,NULL,'completed',0.00),(8,NULL,NULL,'ABC-701','E7','2026-03-23 14:33:33','2026-03-23 13:34:03',0.00,NULL,'completed',10.00),(9,NULL,NULL,'NEW-305','A1','2026-03-23 14:47:43','2026-03-23 13:48:06',0.00,NULL,'completed',10.00),(10,NULL,NULL,'NEW-710','A1','2026-03-23 14:49:01','2026-03-23 13:49:17',0.00,NULL,'completed',10.00),(11,NULL,NULL,'NEW-638','A1','2026-03-23 14:49:31','2026-04-06 11:57:16',0.00,NULL,'completed',3330.00),(12,NULL,NULL,'NEW-912','A1','2026-04-06 11:57:49','2026-04-06 11:58:58',0.00,NULL,'completed',10.00);
/*!40000 ALTER TABLE `parking_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parking_slots`
--

DROP TABLE IF EXISTS `parking_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parking_slots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_label` varchar(10) NOT NULL,
  `status` varchar(20) DEFAULT 'available',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_slots`
--

LOCK TABLES `parking_slots` WRITE;
/*!40000 ALTER TABLE `parking_slots` DISABLE KEYS */;
INSERT INTO `parking_slots` VALUES (1,'A1','available'),(2,'A2','available'),(3,'A3','available'),(4,'A4','available'),(5,'A5','available'),(6,'A6','available'),(7,'A7','available'),(8,'A8','available'),(9,'A9','available'),(10,'A10','available'),(11,'B1','available'),(12,'B2','available'),(13,'B3','available'),(14,'B4','available'),(15,'B5','available'),(16,'B6','available'),(17,'B7','available'),(18,'B8','available'),(19,'B9','available'),(20,'B10','available'),(21,'C1','available'),(22,'C2','available'),(23,'C3','available'),(24,'C4','available'),(25,'C5','available'),(26,'C6','available'),(27,'C7','available'),(28,'C8','available'),(29,'C9','available'),(30,'C10','available'),(31,'D1','available'),(32,'D2','available'),(33,'D3','reserved'),(34,'D4','reserved'),(35,'D5','available'),(36,'D6','available'),(37,'D7','available'),(38,'D8','occupied'),(39,'D9','occupied'),(40,'D10','available'),(41,'E1','available'),(42,'E2','available'),(43,'E3','available'),(44,'E4','available'),(45,'E5','available'),(46,'E6','available'),(47,'E7','available'),(48,'E8','reserved'),(49,'E9','available'),(50,'E10','available'),(51,'A1','available'),(52,'A2','available'),(53,'A3','available'),(54,'A4','available'),(55,'A5','available'),(56,'A6','available'),(57,'A7','available'),(58,'A8','available'),(59,'A9','available'),(60,'A10','available'),(61,'B1','available'),(62,'B2','available'),(63,'B3','available'),(64,'B4','available'),(65,'B5','available'),(66,'B6','available'),(67,'B7','available'),(68,'B8','available'),(69,'B9','available'),(70,'B10','available'),(71,'C1','available'),(72,'C2','available'),(73,'C3','available'),(74,'C4','available'),(75,'C5','available'),(76,'C6','available'),(77,'C7','available'),(78,'C8','available'),(79,'C9','available'),(80,'C10','available'),(81,'D1','available'),(82,'D2','available'),(83,'D3','reserved'),(84,'D4','reserved'),(85,'D5','available'),(86,'D6','available'),(87,'D7','available'),(88,'D8','occupied'),(89,'D9','occupied'),(90,'D10','available'),(91,'E1','available'),(92,'E2','available'),(93,'E3','available'),(94,'E4','available'),(95,'E5','available'),(96,'E6','available'),(97,'E7','available'),(98,'E8','reserved'),(99,'E9','available'),(100,'E10','available');
/*!40000 ALTER TABLE `parking_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slots`
--

DROP TABLE IF EXISTS `slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_number` varchar(10) NOT NULL,
  `status` enum('available','occupied') DEFAULT 'available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slot_number` (`slot_number`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slots`
--

LOCK TABLES `slots` WRITE;
/*!40000 ALTER TABLE `slots` DISABLE KEYS */;
INSERT INTO `slots` VALUES (1,'D1','available'),(2,'D2','available'),(3,'D3','available'),(4,'D4','available'),(5,'D5','available'),(6,'D6','available'),(7,'D7','available'),(8,'D8','available');
/*!40000 ALTER TABLE `slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `car_plate_number` varchar(50) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `role` enum('admin','user','scanner') DEFAULT 'user',
  `mobile_phone` varchar(20) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@parking.com','123',0.00,'admin',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-06 12:05:49
