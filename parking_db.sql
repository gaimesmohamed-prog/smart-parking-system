-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: parkingapp
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
  `status` varchar(50) NOT NULL DEFAULT 'occupied',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`car_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cars`
--

LOCK TABLES `cars` WRITE;
/*!40000 ALTER TABLE `cars` DISABLE KEYS */;
INSERT INTO `cars` VALUES (1,'5555',NULL,NULL,'B1','','occupied',NULL),(2,'0231',NULL,NULL,'C3','','occupied',NULL),(3,'7019',NULL,NULL,'A2','','occupied',NULL),(4,'ABC-447',NULL,NULL,'B2','','occupied',NULL),(5,'ABC-264',NULL,NULL,'M5','','occupied',NULL),(6,'ABC-226',NULL,NULL,'B1','','occupied',NULL),(7,'ABC-278','Heavy Duty Truck','White','M1','PARK-69b6a1ad8eb0a','occupied',NULL),(8,'ABC-405','Sedan Car','White','A3','PARK-69b6a280bae4e','occupied',NULL),(9,'ABC-647','Heavy Duty Truck','White','E10','PARK-69b6a90bcd501','occupied',NULL),(10,'ABC-883','Sedan Car','White','A1','PARK-69b6b08816e2b','occupied',NULL),(11,'ABC-844','Sedan Car','White','C8','PARK-69b6b27553532','occupied',NULL),(12,'ABC-892','Sedan Car','White','C9','PARK-69b7e753810a9','occupied',NULL),(13,'ABC-952','Heavy Duty Truck','White','C18','PARK-69b9385b3b75b','occupied',NULL),(14,'ABC-955','Heavy Duty Truck','White','D5','PARK-69b94f1eb5175','occupied',NULL),(15,'ABC-592','Sedan Car','White','D7','PARK-69b9522f9d0b5','occupied',NULL),(16,'ABC-391','Sedan Car','White','D8','PARK-69b9562f670a2','occupied',NULL),(17,'ABC-466','Sedan Car','White','D9','PARK-69b957bf8f2cb','occupied',NULL),(18,'ABC-623','Sedan Car','White','D4','PARK-69b959350b1c3','reserved',NULL),(19,'ABC-275','Sedan Car','White','D3','PARK-69b95deff4114','completed',NULL),(20,'ABC-923','Heavy Duty Truck','White','E8','PARK-69be9f1964d77','completed',NULL),(26,'ABC-551','Sedan Car','White','A5','PARK-69e91db7a178b-1776885175','completed',NULL),(27,'TEST-123','Sedan Car','White','S10','PARK-69e9240d2ef9b-1776886797','completed',NULL),(28,'ABC-755','Sedan Car','White','A4','PARK-69e9314d5f047-1776890189','completed',NULL),(29,'ABC-870','Sedan Car','White','A4','PARK-69e93347e6145-1776890695','completed',NULL),(30,'ABC-193','Sedan Car','White','A7','PARK-69e93962ecf1e-1776892258','completed',NULL),(31,'ABC-850','Sedan Car','White','A5','PARK-69ea02ac6bc73-1776943788','completed',NULL),(32,'ABC-708','Sedan Car','White','A5','PARK-69ea03cd483f0-1776944077','completed',NULL),(33,'ABC-917','Sedan Car','White','A7','PARK-69ea05188fd1b-1776944408','completed',NULL),(34,'ABC-346','Sedan Car','White','A5','PARK-69ea069923752-1776944793','reserved',NULL),(35,'ABC-697','Sedan Car','White','A9','PARK-69ea074eb6678-1776944974','reserved',NULL),(36,'ABC-125','Sedan Car','White','A4','PARK-69ea67f45149c-1776969716','completed',19),(37,'ABC-305','Sedan Car','White','M2','PARK-69ea68693b479-1776969833','completed',19);
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
INSERT INTO `complaints` VALUES (1,'mariam nesem','test@mail.com','Ôò¬ÔûôÔò¬┬íÔöÿ├áÔöÿ├º Ôò¬┬╝Ôò¬┬╗Ôò¬┬║','2026-03-08 09:26:32'),(2,'mora nesem','test@mail.com','Ôöÿ├ñÔöÿ├¿Ôò¬Ôöé Ôöÿ├¿Ôöÿ├¬Ôò¬┬╝Ôò¬┬╗ Ôöÿ├áÔò¬ÔöñÔöÿ├óÔöÿ├ñÔöÿ├º\r\n','2026-03-09 08:34:29'),(3,'mariam','test@mail.com','Ôöÿ├ñÔò¬┬║ Ôöÿ├¿Ôöÿ├¬Ôò¬┬╝Ôò¬┬╗ Ôöÿ├áÔöÿ├óÔò¬┬║Ôöÿ├Ñ','2026-03-09 09:16:34');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_history`
--

LOCK TABLES `parking_history` WRITE;
/*!40000 ALTER TABLE `parking_history` DISABLE KEYS */;
INSERT INTO `parking_history` VALUES (1,NULL,NULL,'ABC-955','D5','2026-03-17 14:54:55',NULL,0.00,NULL,'completed',0.00),(2,NULL,NULL,'ABC-592','D7','2026-03-17 15:07:59',NULL,0.00,NULL,'completed',0.00),(3,NULL,NULL,'ABC-391','D8','2026-03-17 15:25:03',NULL,0.00,NULL,'completed',0.00),(4,NULL,NULL,'ABC-466','D9','2026-03-17 15:31:43',NULL,0.00,NULL,'completed',0.00),(5,NULL,NULL,'ABC-623','D4','2026-03-17 15:37:57',NULL,0.00,NULL,'completed',0.00),(6,NULL,NULL,'ABC-275','D3','2026-03-17 15:58:09','2026-04-22 22:32:24',0.00,NULL,'completed',100.00),(7,NULL,NULL,'ABC-923','E8','2026-03-21 15:37:29','2026-04-22 22:32:12',0.00,NULL,'completed',100.00),(8,NULL,NULL,'ABC-701','E7','2026-03-23 14:33:33','2026-03-23 13:34:03',0.00,NULL,'completed',10.00),(9,NULL,NULL,'NEW-305','A1','2026-03-23 14:47:43','2026-03-23 13:48:06',0.00,NULL,'completed',10.00),(10,NULL,NULL,'NEW-710','A1','2026-03-23 14:49:01','2026-03-23 13:49:17',0.00,NULL,'completed',10.00),(11,NULL,NULL,'NEW-638','A1','2026-03-23 14:49:31','2026-04-06 11:57:16',0.00,NULL,'completed',3330.00),(12,NULL,NULL,'NEW-912','A1','2026-04-06 11:57:49','2026-04-06 11:58:58',0.00,NULL,'completed',10.00),(13,2,NULL,'ABC-551','A5','2026-04-22 21:12:55','2026-04-22 22:31:55',0.00,NULL,'completed',100.00),(14,4,NULL,'TEST-123','S10','2026-04-22 21:39:57','2026-04-22 22:31:44',0.00,NULL,'completed',100.00),(15,8,NULL,'ABC-755','A4','2026-04-22 22:36:29','2026-04-22 22:36:57',0.00,NULL,'completed',100.00),(16,9,NULL,'ABC-870','A4','2026-04-22 22:44:56','2026-04-22 22:45:37',0.00,NULL,'completed',100.00),(17,11,NULL,'ABC-193','A7','2026-04-22 23:10:59','2026-04-22 23:14:17',0.00,NULL,'completed',100.00),(18,11,NULL,'ABC-850','A5','2026-04-23 13:29:48','2026-04-23 13:34:12',0.00,NULL,'completed',100.00),(19,11,NULL,'ABC-708','A5','2026-04-23 13:34:37','2026-04-23 13:35:08',0.00,NULL,'completed',100.00),(20,11,NULL,'ABC-917','A7','2026-04-23 13:40:08','2026-04-23 13:41:08',0.00,NULL,'completed',100.00),(21,1,NULL,'ABC-346','A5','2026-04-23 13:46:33',NULL,0.00,NULL,'completed',0.00),(22,1,NULL,'ABC-697','A9','2026-04-23 13:49:34',NULL,0.00,NULL,'completed',0.00),(23,19,NULL,'ABC-125','A4','2026-04-23 20:41:56','2026-04-23 20:42:23',0.00,NULL,'completed',100.00),(24,19,NULL,'ABC-305','M2','2026-04-23 20:43:53','2026-04-23 20:44:05',0.00,NULL,'completed',100.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_slots`
--

LOCK TABLES `parking_slots` WRITE;
/*!40000 ALTER TABLE `parking_slots` DISABLE KEYS */;
INSERT INTO `parking_slots` VALUES (1,'A1','available'),(2,'A2','available'),(3,'A3','available'),(4,'A4','available'),(5,'A5','reserved'),(6,'A6','available'),(7,'A7','available'),(8,'A8','available'),(9,'A9','reserved'),(10,'A10','available'),(11,'B1','available'),(12,'B2','available'),(13,'B3','available'),(14,'B4','available'),(15,'B5','available'),(16,'B6','available'),(17,'B7','available'),(18,'B8','available'),(19,'B9','available'),(20,'B10','available'),(21,'C1','available'),(22,'C2','available'),(23,'C3','available'),(24,'C4','available'),(25,'C5','available'),(26,'C6','available'),(27,'C7','available'),(28,'C8','available'),(29,'C9','available'),(30,'C10','available'),(31,'D1','available'),(32,'D2','available'),(33,'D3','available'),(34,'D4','reserved'),(35,'D5','available'),(36,'D6','available'),(37,'D7','available'),(38,'D8','occupied'),(39,'D9','occupied'),(40,'D10','available'),(41,'E1','available'),(42,'E2','available'),(43,'E3','available'),(44,'E4','available'),(45,'E5','available'),(46,'E6','available'),(47,'E7','available'),(48,'E8','available'),(49,'E9','available'),(50,'E10','available'),(51,'A1','available'),(52,'A2','available'),(53,'A3','available'),(54,'A4','available'),(55,'A5','reserved'),(56,'A6','available'),(57,'A7','available'),(58,'A8','available'),(59,'A9','reserved'),(60,'A10','available'),(61,'B1','available'),(62,'B2','available'),(63,'B3','available'),(64,'B4','available'),(65,'B5','available'),(66,'B6','available'),(67,'B7','available'),(68,'B8','available'),(69,'B9','available'),(70,'B10','available'),(71,'C1','available'),(72,'C2','available'),(73,'C3','available'),(74,'C4','available'),(75,'C5','available'),(76,'C6','available'),(77,'C7','available'),(78,'C8','available'),(79,'C9','available'),(80,'C10','available'),(81,'D1','available'),(82,'D2','available'),(83,'D3','available'),(84,'D4','reserved'),(85,'D5','available'),(86,'D6','available'),(87,'D7','available'),(88,'D8','occupied'),(89,'D9','occupied'),(90,'D10','available'),(91,'E1','available'),(92,'E2','available'),(93,'E3','available'),(94,'E4','available'),(95,'E5','available'),(96,'E6','available'),(97,'E7','available'),(98,'E8','available'),(99,'E9','available'),(100,'E10','available'),(101,'S10','available'),(102,'A1','available'),(103,'A2','available'),(104,'A3','available'),(105,'A4','available'),(106,'B1','available'),(107,'B2','available'),(108,'B3','available'),(109,'B4','available'),(110,'A1','available'),(111,'A2','available'),(112,'A3','available'),(113,'A4','available'),(114,'B1','available'),(115,'B2','available'),(116,'B3','available'),(117,'B4','available'),(118,'M2','available');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
INSERT INTO `user_profiles` VALUES (1,2,'','gaimesmohamed@gmail.com','',NULL,NULL,'2026-04-22 19:06:12'),(2,3,'','admin@admin.com','',NULL,NULL,'2026-04-22 19:30:17'),(3,4,'','admin@gmail.com','',NULL,NULL,'2026-04-22 19:31:05'),(4,5,'','mohamed@gmail.com','',NULL,NULL,'2026-04-22 19:32:14'),(5,6,'','mohamed.reda@gmail.com','',NULL,NULL,'2026-04-22 19:34:10'),(6,7,'','gaimesmohamed@gmail.com','',NULL,NULL,'2026-04-22 19:54:30'),(7,8,'','gaimesmohamed@gmail.com','',NULL,NULL,'2026-04-22 20:10:22'),(8,9,'','hamadaghanem124@gmail.com','',NULL,NULL,'2026-04-22 20:43:28'),(9,11,'','mariammahrous1392004@gmail.com','',NULL,NULL,'2026-04-22 21:04:38'),(10,12,'','mennasheble2004@gmail.com','',NULL,NULL,'2026-04-22 21:06:31'),(11,14,'','test@gmail.com','',NULL,NULL,'2026-04-22 21:26:20'),(12,15,'','test@gmail.com','',NULL,NULL,'2026-04-23 11:35:34'),(13,16,'','testrom@gmail.com','',NULL,NULL,'2026-04-23 11:37:44'),(14,17,'','mora@gmail.com','',NULL,NULL,'2026-04-23 11:39:40'),(15,18,'','mora@gmail.com','',NULL,NULL,'2026-04-23 11:40:42'),(16,19,'','sanfore5000@gmail.com','',NULL,NULL,'2026-04-23 18:18:46');
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
  `mobile_phone` varchar(20) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'user',
  `balance` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@parking.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te',NULL,NULL,'admin',0.00),(2,'‪Mohamed reda‬‏','gaimesmohamed@gmail.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te','',NULL,'admin',0.00),(3,'admin','admin@admin.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te','',NULL,'user',0.00),(4,'admin','admin@gmail.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te','',NULL,'user',0.00),(5,'Mohamed Reda','mohamed@gmail.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te','',NULL,'user',0.00),(6,'Mohamed Reda','mohamed.reda@gmail.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te','',NULL,'user',0.00),(7,'mohamed reda','gaimesmohamed@gmail.com','$2y$10$BUdQGGV/L2igEjp3jDzn4.jiaV7YzrSK7Yrh44qFa8DrukhdI65te','',NULL,'user',0.00),(8,'mohamed reda','gaimesmohamed@gmail.com','$2y$10$FRRp.z/NP9Zep6zDWglIneTz5zxk4cqnuG8wUGO2lad8n7ZAGVd6q','',NULL,'user',0.00),(9,'mada','hamadaghanem124@gmail.com','$2y$10$wnLkrcq/X4PmnkWl.ptncepyqtErk6u0OXibENG6uYotZzl2IZpV6','',NULL,'user',0.00),(10,'Admin','admin','$2y$10$LhUNCF1F7E1.U6iUnIuWfOWzqyhMchVM4VNR4JXpQvl7dKPWsYDN2',NULL,NULL,'admin',0.00),(11,'Mariam','mariammahrous1392004@gmail.com','$2y$10$nU.W7TFB0SrmmghOTVt5zufVbMh5OVIWYFVEQ1haj6kDyEsiJuAQS','',NULL,'user',0.00),(12,'Menna','mennasheble2004@gmail.com','$2y$10$xD1EPLgkJ6TAENnW1XenYe8UtnSZi76W7xnH5HuwL/4Mtt4c739U6','',NULL,'user',0.00),(13,'Admin','admin','$2y$10$ujO.5t5.CZkHdmMQMnmiIuScLB3WUXMVhuThqEIXIgUteYjwjL16C',NULL,NULL,'admin',0.00),(14,'Mariam nesem','test@gmail.com','$2y$10$NVvR95iBkFqARZnpe7A60elv5PkrhPsZoIlvJ4nMwyY4wL6ovkHV2','',NULL,'user',0.00),(15,'Mariam eid','test@gmail.com','$2y$10$RjIZudZgBRslDfVwp7Ywwu927LUULAPTnFIGtBfcbrKU2ggcZ8CGC','',NULL,'user',0.00),(16,'Mariam','testrom@gmail.com','$2y$10$hA3D0NZPQ27qz6HuIq/CdOyjvQnvhjvMRgsaJR3TtbVZoBrSfL6qq','',NULL,'user',0.00),(17,'Mora','mora@gmail.com','$2y$10$Hn7h9580OO9worcuvfO3sOd6X5syxrzrwKiqxHJui5jP/CyBTQvHO','',NULL,'user',0.00),(18,'Mora','mora@gmail.com','$2y$10$Niq7TZCK0uiHjmAJBSeWFOUO99foYg4.oWhYhSIIruYAPUqwZrHvO','',NULL,'user',0.00),(19,'Mohamed reda','sanfore5000@gmail.com','$2y$10$gfuq1Jo5PkjUbVVq6CuBLe.zpSyNADVXzDcSn1ppLFLMaXQFmuabe','',NULL,'user',60.00);
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

-- Dump completed on 2026-04-23 23:00:35
