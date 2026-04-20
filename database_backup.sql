-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: parking_system
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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'LOGIN','User logged in','::1','2026-04-20 21:29:22');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slots`
--

DROP TABLE IF EXISTS `slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slots` (
  `slot_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `category` varchar(20) DEFAULT 'normal',
  PRIMARY KEY (`slot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slots`
--

LOCK TABLES `slots` WRITE;
/*!40000 ALTER TABLE `slots` DISABLE KEYS */;
INSERT INTO `slots` VALUES (1,'occupied','vip'),(2,'occupied','vip'),(3,'occupied','vip'),(4,'occupied','vip'),(5,'occupied','vip'),(6,'occupied','vip'),(7,'occupied','vip'),(8,'occupied','vip'),(9,'occupied','vip'),(10,'occupied','vip'),(11,'available','vip'),(12,'available','vip'),(13,'available','vip'),(14,'occupied','vip'),(15,'available','vip'),(16,'available','vip'),(17,'available','vip'),(18,'occupied','vip'),(19,'occupied','vip'),(20,'available','vip'),(21,'available','vip'),(22,'occupied','vip'),(23,'available','vip'),(24,'available','vip'),(25,'occupied','vip'),(26,'available','vip'),(27,'available','vip'),(28,'available','vip'),(29,'available','vip'),(30,'available','vip'),(31,'available','ev'),(32,'occupied','ev'),(33,'available','ev'),(34,'available','ev'),(35,'occupied','ev'),(36,'available','ev'),(37,'occupied','ev'),(38,'occupied','ev'),(39,'occupied','ev'),(40,'available','ev'),(41,'available','ev'),(42,'occupied','ev'),(43,'available','ev'),(44,'available','ev'),(45,'occupied','ev'),(46,'available','ev'),(47,'available','ev'),(48,'occupied','ev'),(49,'available','ev'),(50,'available','ev'),(51,'available','ev'),(52,'available','ev'),(53,'occupied','ev'),(54,'occupied','ev'),(55,'available','ev'),(56,'available','ev'),(57,'occupied','ev'),(58,'occupied','ev'),(59,'available','ev'),(60,'available','ev'),(61,'occupied','suv'),(62,'occupied','suv'),(63,'available','suv'),(64,'available','suv'),(65,'occupied','suv'),(66,'occupied','suv'),(67,'available','suv'),(68,'available','suv'),(69,'available','suv'),(70,'available','suv'),(71,'available','suv'),(72,'available','suv'),(73,'occupied','suv'),(74,'available','suv'),(75,'available','suv'),(76,'available','suv'),(77,'available','suv'),(78,'available','suv'),(79,'occupied','suv'),(80,'available','suv'),(81,'occupied','suv'),(82,'available','suv'),(83,'occupied','suv'),(84,'available','suv'),(85,'available','suv'),(86,'available','suv'),(87,'available','suv'),(88,'available','suv'),(89,'available','suv'),(90,'occupied','suv'),(91,'available','normal'),(92,'occupied','normal'),(93,'occupied','normal'),(94,'occupied','normal'),(95,'occupied','normal'),(96,'available','normal'),(97,'occupied','normal'),(98,'occupied','normal'),(99,'available','normal'),(100,'occupied','normal'),(101,'available','normal'),(102,'available','normal'),(103,'available','normal'),(104,'available','normal'),(105,'occupied','normal'),(106,'available','normal'),(107,'available','normal'),(108,'available','normal'),(109,'available','normal'),(110,'available','normal'),(111,'occupied','normal'),(112,'available','normal'),(113,'available','normal'),(114,'occupied','normal'),(115,'available','normal'),(116,'available','normal'),(117,'available','normal'),(118,'available','normal'),(119,'available','normal'),(120,'available','normal'),(121,'available','normal'),(122,'available','normal'),(123,'available','normal'),(124,'available','normal'),(125,'available','normal'),(126,'available','normal'),(127,'available','normal'),(128,'occupied','normal'),(129,'available','normal'),(130,'available','normal'),(131,'available','normal'),(132,'available','normal'),(133,'available','normal'),(134,'available','normal'),(135,'occupied','normal'),(136,'available','normal'),(137,'available','normal'),(138,'available','normal'),(139,'available','normal'),(140,'available','normal'),(141,'available','normal'),(142,'occupied','normal'),(143,'occupied','normal'),(144,'available','normal'),(145,'available','normal'),(146,'occupied','normal'),(147,'available','normal'),(148,'occupied','normal'),(149,'available','normal'),(150,'available','normal');
/*!40000 ALTER TABLE `slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `plan` varchar(20) DEFAULT 'free',
  `vehicle_no` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `contact_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `car_model` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `car_image` varchar(255) DEFAULT NULL,
  `wallet_balance` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@gmail.com','1234','free',NULL,'admin',NULL,NULL,NULL,NULL,NULL,NULL,0.00),(2,'Shriyash','test1@gmail.com','1234','free',NULL,'user',NULL,NULL,NULL,NULL,NULL,NULL,0.00),(3,'user','user@gmail.com','1234','ultimate','MH12YF6837','user','','','0000-00-00','RE meteor  350',NULL,NULL,59.97);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_no` varchar(20) DEFAULT NULL,
  `slot_id` int(11) DEFAULT NULL,
  `entry_time` datetime DEFAULT current_timestamp(),
  `exit_time` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'412307',1,'2026-04-18 18:51:50','2026-04-18 19:24:30',NULL),(2,'554466',3,'2026-04-18 18:51:56','2026-04-18 19:32:15',NULL),(3,'985898',5,'2026-04-18 18:52:05','2026-04-18 19:32:32',NULL),(4,'554466',6,'2026-04-18 18:52:09',NULL,NULL),(5,'123456',8,'2026-04-18 18:55:13',NULL,NULL),(6,'412307',3,'2026-04-18 19:32:24',NULL,NULL),(7,'985898',1,'2026-04-18 19:32:41',NULL,NULL),(8,'MH12YF6837',37,'2026-04-20 19:06:36',NULL,NULL),(9,'MH12YF6837',5,'2026-04-20 19:22:30','2026-04-20 20:13:53',3),(10,'MH12YF6837',9,'2026-04-20 21:23:09',NULL,3),(11,'MH12YF6837',5,'2026-04-20 21:23:09',NULL,3);
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-20 21:36:11
