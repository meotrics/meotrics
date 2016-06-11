-- MySQL dump 10.13  Distrib 5.5.42, for osx10.6 (i386)
--
-- Host: localhost    Database: form_register
-- ------------------------------------------------------
-- Server version	5.5.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `apply`
--

DROP TABLE IF EXISTS `apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `apply` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apply`
--

LOCK TABLES `apply` WRITE;
/*!40000 ALTER TABLE `apply` DISABLE KEYS */;
INSERT INTO `apply` VALUES (1,'viet','ldviet92@gmail.com','123123','2015-01-01','Icon21465647109.png','j;lkjd',NULL),(4,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(5,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(6,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(7,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(8,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(9,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(10,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(11,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(12,'viet','lduwepori2','sdkjf;w','0000-00-00','13344623_1176140829071548_4367547327306242918_n.jpg','',NULL),(13,'fycj','skldfj','sdlkfj','0000-00-00','Icon3.png','',NULL),(14,'asdfas','dfsdf','sdfs','0000-00-00','1465640935Email-tuyen-dung.docx','',NULL),(15,'sdfkjsd','sdkjfl','dkjf','0000-00-00','Icon2-1465647750.png','',NULL),(16,'sdfkj','kjk','jkj','0000-00-00','Email-tuyen-dung-1465647869.docx','sdfsdf',NULL),(17,'sdfkj','kjk','jk','0000-00-00','Email-tuyen-dung-1465647894.docx','dfsdfsdf',NULL),(18,'jhksjdf','kjsldkfj','kjsdl;fkjsd','2016-06-30','Email-tuyen-dung-1465659467.docx','sfjw;lkdjfw;klef',NULL),(19,'sdf','jslkdfj','slkdjflksd','2016-06-11',NULL,'sdfsd',NULL),(20,'sdfasd','wẻ','sdf',NULL,NULL,'',NULL),(21,'sdfs','jklsdjfl','sdfksjdlk','2016-06-11',NULL,'',NULL),(22,'sdfs','sdf','sdf',NULL,NULL,'',NULL),(23,'sdf','sdf','sdf',NULL,NULL,'',NULL),(24,'dfsdf','sdfsdf','sdfsd',NULL,NULL,'',NULL),(25,'sdf','sdfasdf','sadfasd',NULL,NULL,'','juniorfontend'),(26,'nhung','email@gmail.com','01645005533','2016-06-15','Meotrics-Thông-tin-trên-trang-tuyển-dụng-1465666491.docx','','juniorfontend');
/*!40000 ALTER TABLE `apply` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-12  1:11:04
