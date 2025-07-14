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
-- Table structure for table `full_menu`
--

DROP TABLE IF EXISTS `full_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `full_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` text COLLATE utf8mb4_general_ci,
  `is_available` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `full_menu`
--

LOCK TABLES `full_menu` WRITE;
/*!40000 ALTER TABLE `full_menu` DISABLE KEYS */;
INSERT INTO `full_menu` VALUES (1,'Tea',10.00,'https://thespicerootindiancuisine.com/wp-content/uploads/2023/03/Indian-Masala-Tea.jpg',1),(2,'Idli',30.00,'https://static.toiimg.com/photo/68631114.cms',1),(3,'Dosa',60.00,'https://media.cntraveller.in/wp-content/uploads/2020/05/dosa-recipes-1366x768.jpg',1),(4,'Poha',25.00,'https://st2.depositphotos.com/5653638/11810/i/450/depositphotos_118105520-stock-photo-poha-or-aalu-poha-or.jpg',1),(5,'Coffee',30.00,'http://i.huffpost.com/gen/1693731/images/o-COFFEE-facebook.jpg',1),(6,'Pav Bhaji',50.00,'https://www.thestatesman.com/wp-content/uploads/2019/07/pav-bhaji.jpg',1),(7,'Pasta',90.00,'https://images.pexels.com/photos/1438672/pexels-photo-1438672.jpeg?cs=srgb&dl=food-photography-of-pasta-1438672.jpg&fm=jpg',1),(8,'Samosa',15.00,'https://cdn.pixabay.com/photo/2024/02/04/20/02/ai-generated-8553025_1280.jpg',1),(9,'Vada',25.00,'https://images.slurrp.com/prod/recipe_images/transcribe/breakfast/Medu-Vada.webp',1),(10,'Upma',40.00,'https://simmertoslimmer.com/wp-content/uploads/2014/02/Rava-Upma.jpg',1),(11,'Noodles',80.00,'https://www.recipetineats.com/wp-content/uploads/2022/05/Supreme-Soy-Noodles_1-SQ.jpg',1),(12,'Juice',35.00,'https://images.ctfassets.net/yixw23k2v6vo/2oidT8ZVeVYwE7L7ksr0hE/614f7592570de77b4a0260e5c80c113a/large-GettyImages-825882916-3000x2000.jpg',1),(13,'Chapati',15.00,'https://recipes.timesofindia.com/thumb/61203720.cms?imgsize=670417&width=800&height=800',1),(14,'Pizza',120.00,'https://media.istockphoto.com/photos/cheesy-pepperoni-pizza-picture-id938742222?b=1&k=20&m=938742222&s=170667a&w=0&h=HyfY78AeiQM8vZbIea-iiGmNxHHuHD-PVVuHRvrCIj4=',1),(15,'Spring Rolls',60.00,'https://wallpaperaccess.com/full/6905828.jpg',1),(16,'Fried Rice',70.00,'https://wallpaperaccess.com/full/2175404.jpg',1),(17,'Burger',50.00,'https://wallpapercave.com/wp/wp1987065.jpg',1),(18,'Momos',45.00,'https://static.vecteezy.com/system/resources/thumbnails/039/002/703/small_2x/ai-generated-delicious-momos-with-chutney-on-the-leaf-captured-with-selective-focus-photo.jpg',1),(19,'Cutlet',30.00,'https://1.bp.blogspot.com/-8MPWfPnCZTc/VB1Y40TRuqI/AAAAAAAAAPY/XluQZxXKKng/s1600/Vegetable-cutlet.jpg',1),(20,'Paratha',35.00,'https://www.whiskaffair.com/wp-content/uploads/2020/06/Lachha-Paratha-2-1.jpg',1),(21,'Puri Bhaji',40.00,'https://as2.ftcdn.net/v2/jpg/04/32/10/33/1000_F_432103366_8Ip4tLE7vEiE2JuYr1AoCN44UR5kyA5w.jpg',1);
/*!40000 ALTER TABLE `full_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT 'default.jpg',
  `category` varchar(100) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_special` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,'Spicy Paneer Wrap',NULL,99.00,'images/paneer-wrap.jpg','Specials',1,1),(2,'Classic Veg Biryani',NULL,129.00,'images/veg-biryani.jpg','Main Course',1,1),(3,'Fruit Smoothie',NULL,79.00,'images/smoothie.jpg','Beverages',1,1),(4,'Choco Lava Cake',NULL,85.00,'images/choco-lava.jpg','Desserts',1,1),(5,'Masala Dosa',NULL,70.00,'images/masala-dosa.jpg','Breakfast',1,1),(6,'Puri Bhaji',NULL,65.00,'images/puri-bhaji.jpg','Breakfast',1,1),(7,'Gobi Manchuri',NULL,110.00,'images/gobi-manchuri.jpg','Starters',1,1);
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_details` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'KLE-2104','iqra','01fe24mca050@kletech.ac.in','COD','[{\"id\":\"item_4\",\"name\":\"Upma\",\"quantity\":1,\"price\":\"40.00\",\"cost\":40},{\"id\":\"item_16\",\"name\":\"Paratha\",\"quantity\":3,\"price\":\"35.00\",\"cost\":105}]',145.00,'Pending','2025-07-04 20:39:55'),(2,'KLE-7066','iqra','01fe24mca050@kletech.ac.in','COD','[{\"id\":\"item_3\",\"name\":\"Coffee\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-04 21:08:30'),(3,'KLE-8038','Ambika','01fe24mca001@kletech.ac.in','COD','[{\"id\":\"item_3\",\"name\":\"Coffee\",\"quantity\":2,\"price\":\"30.00\",\"cost\":60}]',60.00,'Pending','2025-07-05 07:14:10'),(4,'KLE-6941','IK','zoekhan05@gmail.com','COD','[{\"id\":\"item_2\",\"name\":\"Pizza\",\"quantity\":1,\"price\":\"120.00\",\"cost\":120}]',120.00,'Pending','2025-07-05 07:15:09'),(5,'KLE-3010','Kulsum','kulsummkhan05@gmail.com','COD','[{\"id\":\"item_3\",\"name\":\"Coffee\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-05 14:49:16'),(6,'KLE-8433','xyz','01fe24mca050@kletech.ac.in','UPI','[{\"id\":\"item_19\",\"name\":\"Cutlet\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-08 18:15:51'),(7,'KLE-5517','iqra','01fe24mca050@kletech.ac.in','UPI','[{\"id\":\"item_1\",\"name\":\"Tea\",\"quantity\":2,\"price\":\"10.00\",\"cost\":20}]',20.00,'Pending','2025-07-08 18:28:40'),(8,'KLE-2871','iqra','01fe24mca050@kletech.ac.in','Card','[{\"id\":\"item_19\",\"name\":\"Cutlet\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-08 18:40:39'),(9,'KLE-5730','iqra','01fe24mca050@kletech.ac.in','Card','[{\"id\":\"item_19\",\"name\":\"Cutlet\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-08 18:44:50'),(10,'KLE-7075','iqra','01fe24mca050@kletech.ac.in','UPI','[{\"id\":\"item_2\",\"name\":\"Idli\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-08 18:45:27'),(11,'KLE-8350','iqra','01fe24mca050@kletech.ac.in','COD','[{\"id\":\"item_2\",\"name\":\"Idli\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-08 18:51:26'),(12,'KLE-3537','iiiiiii','zoekhan05@gmail.com','UPI','[{\"id\":\"item_1\",\"name\":\"Tea\",\"quantity\":1,\"price\":\"10.00\",\"cost\":10}]',10.00,'Pending','2025-07-09 09:32:41'),(13,'KLE-3492','iqra','01fe24mca050@kletech.ac.in','Card','[{\"id\":\"item_19\",\"name\":\"Cutlet\",\"quantity\":2,\"price\":\"30.00\",\"cost\":60}]',60.00,'Pending','2025-07-09 10:04:22'),(14,'KLE-1686','i3','01fe24mca050@kletech.ac.in','COD','[{\"id\":\"item_1\",\"name\":\"Tea\",\"quantity\":1,\"price\":\"10.00\",\"cost\":10},{\"id\":\"item_2\",\"name\":\"Idli\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',40.00,'Pending','2025-07-09 10:24:15'),(15,'KLE-3249','iqra','01fe24mca050@kletech.ac.in','Card','[{\"id\":\"item_2\",\"name\":\"Idli\",\"quantity\":1,\"price\":\"30.00\",\"cost\":30}]',30.00,'Pending','2025-07-09 11:37:31');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
  `type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','password123','admin@klever.com',1),(2,'student','student123','student@kletech.ac.in',0),(3,'ambi','$2y$12$EUmJV.YAL9/nvZhVjAF91uVO3Ib45RsEyWGjPmO736mWJc0bBmpv6','ambikakallanagoudar0@gmail.com',0),(4,'srishti','$2y$12$eFzA8GUS4NcMcBlzIGwgfuyPLgco7fwJSh7hQjQzATDb2gx4QN5zC','srishtihiremath8@gmail.com',0);
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

-- Dump completed on 2025-07-14 16:02:45
