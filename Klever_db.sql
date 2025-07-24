CREATE DATABASE  IF NOT EXISTS `klever_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `klever_db`;
-- MySQL dump 10.13  Distrib 8.0.42, for macos15 (arm64)
--
-- Host: localhost    Database: klever_db
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `daily_summary`
--

DROP TABLE IF EXISTS `daily_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_summary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `summary_date` date NOT NULL,
  `total_sales` decimal(10,2) DEFAULT '0.00',
  `orders_count` int DEFAULT '0',
  `top_item_name` varchar(255) DEFAULT NULL,
  `top_item_quantity` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_date` (`summary_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_summary`
--

LOCK TABLES `daily_summary` WRITE;
/*!40000 ALTER TABLE `daily_summary` DISABLE KEYS */;
INSERT INTO `daily_summary` VALUES (1,'2025-07-23',1030.00,7,'Gobi Manchuri',6);
/*!40000 ALTER TABLE `daily_summary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `item_id` int NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,'Tea',3,10.00),(2,2,4,'Poha',3,25.00),(3,3,4,'Poha',3,25.00),(4,3,27,'Gobi Manchuri',3,110.00),(5,4,2,'Idli',3,30.00),(6,5,5,'Coffee',3,30.00),(7,6,5,'Coffee',3,30.00),(8,6,29,'Girmit',4,45.00),(9,7,16,'Fried Rice',1,70.00),(10,8,2,'Idli',2,30.00),(11,9,7,'Pasta',2,90.00),(12,10,27,'Gobi Manchuri',6,110.00),(13,11,29,'Girmit',1,45.00),(14,12,29,'Girmit',1,45.00),(15,13,1,'Tea',2,10.00),(16,14,1,'Tea',2,10.00),(17,15,7,'Pasta',2,90.00),(18,16,1,'Tea',2,10.00),(19,17,1,'Tea',2,10.00),(20,18,3,'Dosa',3,60.00),(21,19,5,'Coffee',2,30.00),(22,20,1,'Tea',2,10.00),(23,21,1,'Tea',2,10.00),(24,22,1,'Tea',2,10.00),(25,23,1,'Tea',2,10.00),(26,24,6,'Pav Bhaji',2,50.00),(27,25,6,'Pav Bhaji',2,50.00),(28,26,1,'Tea',2,10.00),(29,27,1,'Tea',2,10.00),(30,28,1,'Tea',2,10.00),(31,29,1,'Tea',2,10.00),(32,30,1,'Tea',2,10.00),(33,31,1,'Tea',2,10.00),(34,32,1,'Tea',2,10.00),(35,33,1,'Tea',2,10.00),(36,34,1,'Tea',2,10.00),(37,35,1,'Tea',2,10.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,NULL,'KLE-9523','Zoe','01fe24mca050@kletech.ac.in','UPI',30.00,'Pending','2025-07-15 19:16:32'),(2,NULL,'KLE-6676','iqra','01fe24mca050@kletech.ac.in','UPI',75.00,'Completed','2025-07-15 19:28:19'),(3,NULL,'KLE-6231','iqra','01fe24mca050@kletech.ac.in','UPI',405.00,'Preparing','2025-07-15 19:39:30'),(4,NULL,'KLE-4994','iqra1','01fe24mca050@kletech.ac.in','UPI',90.00,'Completed','2025-07-16 07:07:49'),(5,NULL,'KLE-7870','Ambi','01fe24mca050@kletech.ac.in','UPI',90.00,'Preparing','2025-07-16 07:12:08'),(6,NULL,'KLE-8727','iqra2','01fe24mca050@kletech.ac.in','UPI',270.00,'Pending','2025-07-16 07:12:52'),(7,NULL,'KLE-4155','iqra','kulsummkhan05@gmail.com','COD',70.00,'Preparing','2025-07-16 17:50:31'),(8,NULL,'KLE-4726','TestUser','01fe24mca050@kletech.ac.in','UPI',60.00,'Completed','2025-07-23 07:14:44'),(9,NULL,'KLE-3561','I','01fe24mca050@kletech.ac.in','UPI',180.00,'Pending','2025-07-23 09:25:21'),(10,NULL,'KLE-8567','i2','01fe24mca050@kletech.ac.in','UPI',660.00,'Pending','2025-07-23 09:26:16'),(11,NULL,'KLE-6500','iqra','01fe24mca050@kletech.ac.in','UPI',45.00,'Preparing','2025-07-23 14:18:51'),(12,NULL,'KLE-5855','iqra','01fe24mca050@kletech.ac.in','UPI',45.00,'Preparing','2025-07-23 14:19:28'),(13,2,'KLE-5720','TestUser','01fe24mca050@kletech.ac.in','UPI',20.00,'Completed','2025-07-23 16:38:09'),(14,2,'KLE-8870','TestUser','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-23 17:05:57'),(15,2,'KLE-7874','iqra','01fe24mca050@kletech.ac.in','UPI',180.00,'Pending','2025-07-23 20:24:39'),(16,2,'KLE-8659','iqra','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-23 20:25:39'),(17,2,'KLE-9149','iqra','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-23 20:27:29'),(18,2,'KLE-7168','iqra3','01fe24mca050@kletech.ac.in','UPI',180.00,'Pending','2025-07-23 20:28:09'),(19,2,'KLE-4486','iqra','01fe24mca050@kletech.ac.in','UPI',60.00,'Pending','2025-07-24 05:12:04'),(20,2,'KLE-3899','iqranew','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 05:19:07'),(21,2,'KLE-1563','iqranew1','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 05:22:57'),(22,2,'KLE-8847','iqranew2','01fe24mca050@kletech.ac.in','UPI',20.00,'Completed','2025-07-24 05:24:00'),(23,2,'KLE-4363','iqranew2','01fe24mca050@kletech.ac.in','UPI',20.00,'Preparing','2025-07-24 05:24:14'),(24,2,'KLE-4083','Iq2','01fe24mca050@kletech.ac.in','UPI',100.00,'Pending','2025-07-24 15:44:58'),(25,2,'KLE-5830','Iq2','01fe24mca050@kletech.ac.in','UPI',100.00,'Pending','2025-07-24 15:45:29'),(26,2,'KLE-1684','i3','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:14:11'),(27,2,'KLE-2081','i3','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:15:15'),(28,2,'KLE-1799','i4','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:23:01'),(29,2,'KLE-1516','i48','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:23:15'),(30,2,'KLE-2500','i49','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:29:32'),(31,2,'KLE-9426','iqra 2','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:48:09'),(32,2,'KLE-8154','hii','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 16:51:06'),(33,2,'KLE-4370','hiii','01fe24mca050@kletech.ac.in','UPI',20.00,'Preparing','2025-07-24 17:36:29'),(34,2,'KLE-1512','hiiie','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 17:37:43'),(35,2,'KLE-5286','hiiiek','01fe24mca050@kletech.ac.in','UPI',20.00,'Pending','2025-07-24 17:37:55');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` text COLLATE utf8mb4_general_ci,
  `is_available` tinyint(1) DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Tea',10.00,'https://thespicerootindiancuisine.com/wp-content/uploads/2023/03/Indian-Masala-Tea.jpg',1,0,NULL,1),(2,'Idli',30.00,'https://static.toiimg.com/photo/68631114.cms',1,0,NULL,1),(3,'Dosa',60.00,'https://media.cntraveller.in/wp-content/uploads/2020/05/dosa-recipes-1366x768.jpg',1,0,NULL,1),(4,'Poha',25.00,'https://st2.depositphotos.com/5653638/11810/i/450/depositphotos_118105520-stock-photo-poha-or-aalu-poha-or.jpg',1,0,NULL,1),(5,'Coffee',30.00,'http://i.huffpost.com/gen/1693731/images/o-COFFEE-facebook.jpg',1,0,NULL,1),(6,'Pav Bhaji',50.00,'https://www.thestatesman.com/wp-content/uploads/2019/07/pav-bhaji.jpg',1,0,NULL,1),(7,'Pasta',90.00,'https://images.pexels.com/photos/1438672/pexels-photo-1438672.jpeg?cs=srgb&dl=food-photography-of-pasta-1438672.jpg&fm=jpg',1,0,NULL,1),(8,'Samosa',15.00,'https://cdn.pixabay.com/photo/2024/02/04/20/02/ai-generated-8553025_1280.jpg',1,0,NULL,1),(9,'Vada',25.00,'https://images.slurrp.com/prod/recipe_images/transcribe/breakfast/Medu-Vada.webp',1,0,NULL,1),(10,'Upma',40.00,'https://simmertoslimmer.com/wp-content/uploads/2014/02/Rava-Upma.jpg',1,0,NULL,1),(11,'Noodles',80.00,'https://www.recipetineats.com/wp-content/uploads/2022/05/Supreme-Soy-Noodles_1-SQ.jpg',1,0,NULL,1),(12,'Juice',35.00,'https://images.ctfassets.net/yixw23k2v6vo/2oidT8ZVeVYwE7L7ksr0hE/614f7592570de77b4a0260e5c80c113a/large-GettyImages-825882916-3000x2000.jpg',1,0,NULL,1),(13,'Chapati',15.00,'https://recipes.timesofindia.com/thumb/61203720.cms?imgsize=670417&width=800&height=800',1,0,NULL,1),(14,'Pizza',120.00,'https://media.istockphoto.com/photos/cheesy-pepperoni-pizza-picture-id938742222?b=1&k=20&m=938742222&s=170667a&w=0&h=HyfY78AeiQM8vZbIea-iiGmNxHHuHD-PVVuHRvrCIj4=',1,0,NULL,1),(15,'Spring Rolls',60.00,'https://wallpaperaccess.com/full/6905828.jpg',1,0,NULL,1),(16,'Fried Rice',70.00,'https://wallpaperaccess.com/full/2175404.jpg',1,0,NULL,1),(17,'Burger',50.00,'https://wallpapercave.com/wp/wp1987065.jpg',1,0,NULL,1),(18,'Momos',75.00,'https://static.vecteezy.com/system/resources/thumbnails/039/002/703/small_2x/ai-generated-delicious-momos-with-chutney-on-the-leaf-captured-with-selective-focus-photo.jpg',1,0,NULL,1),(19,'Cutlet',30.00,'https://1.bp.blogspot.com/-8MPWfPnCZTc/VB1Y40TRuqI/AAAAAAAAAPY/XluQZxXKKng/s1600/Vegetable-cutlet.jpg',1,0,NULL,1),(20,'Paratha',35.00,'https://www.whiskaffair.com/wp-content/uploads/2020/06/Lachha-Paratha-2-1.jpg',1,0,NULL,1),(21,'Puri Bhaji',65.00,'images/puri-bhaji.jpg',1,1,NULL,1),(22,'Spicy Paneer Wrap',99.00,'images/paneer-wrap.jpg',1,1,NULL,1),(23,'Classic Veg Biryani',129.00,'images/veg-biryani.jpg',1,1,NULL,1),(24,'Fruit Smoothie',79.00,'images/smoothie.jpg',1,1,NULL,1),(25,'Choco Lava Cake',80.00,'images/choco-lava.jpg',0,0,NULL,1),(26,'Masala Dosa',70.00,'images/masala-dosa.jpg',1,1,NULL,1),(27,'Gobi Manchuri',110.00,'images/gobi-manchuri.jpg',1,1,NULL,1),(29,'Girmit',45.00,'https://i.ytimg.com/vi/fdx4ke3WWFs/hq720.jpg?sqp=-oaymwE7CK4FEIIDSFryq4qpAy0IARUAAAAAGAElAADIQj0AgKJD8AEB-AH-CYAC0AWKAgwIABABGH8gWigjMA8=&rs=AOn4CLAdp2aJh02dijEOx6zN9VlMMU7xJw',1,0,NULL,1);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','$2y$12$9v6.ogm0IXa6MqOIdKh0EefKNY1GVvFTyTDbSFph7H1uvauP/Ue.e','iqra58577@gmail.com','9980406168',1),(2,'TestUser','$2y$12$dfWozkQZbC83jtr6v5H5KOQYvGrKAznJj0vJBM89/dm5eUj0vcbVO','01fe24mca050@kletech.ac.in','9980406100',0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-24 23:11:47
