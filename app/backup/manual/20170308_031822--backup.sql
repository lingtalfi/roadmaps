-- MySQL dump 10.13  Distrib 5.6.35, for osx10.9 (x86_64)
--
-- Host: localhost    Database: roadmaps
-- ------------------------------------------------------
-- Server version	5.6.35

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
-- Current Database: `roadmaps`
--

/*!40000 DROP DATABASE IF EXISTS `roadmaps`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `roadmaps` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `roadmaps`;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (1,'leaderfit-ecommerce');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(128) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` text NOT NULL,
  `parent_task_id` int(11) DEFAULT NULL,
  `done` tinyint(4) NOT NULL,
  `project_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `color` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_task_idx` (`parent_task_id`),
  KEY `fk_task_project1_idx` (`project_id`),
  CONSTRAINT `fk_task_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_task_task` FOREIGN KEY (`parent_task_id`) REFERENCES `task` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES (1,'kamille','2017-03-13 00:00:00','2017-03-27 00:00:00','',21,0,1,1,'rgb(79, 16, 62)'),(2,'Architecture','2017-03-13 00:00:00','2017-03-17 00:00:00','',1,0,1,0,'rgb(79, 16, 62)'),(3,'Services','2017-03-17 00:00:00','2017-03-20 00:00:00','',1,0,1,1,'rgb(79, 16, 62)'),(4,'Modules and hooks','2017-03-20 00:00:00','2017-03-23 00:00:00','',1,0,1,2,'rgb(79, 16, 62)'),(5,'MVC','2017-03-23 00:00:00','2017-03-27 00:00:00','',1,0,1,3,'green'),(6,'Admin tools','2017-03-27 00:00:00','2017-04-17 00:00:00','',21,0,1,2,'#907611'),(7,'Theme implementation','2017-04-17 00:00:00','2017-05-01 00:00:00','',21,0,1,3,'rgb(79, 16, 62)'),(8,'Nullos MVC','2017-05-01 00:00:00','2017-05-14 00:00:00','',21,0,1,4,'#34851d'),(9,'Module e-commerce','2017-05-14 00:00:00','2017-06-15 00:00:00','',21,0,1,5,'#c57fc1'),(10,'create datatables tools','2017-03-27 00:00:00','2017-04-03 00:00:00','',6,0,1,0,'#907611'),(11,'create form tools','2017-04-03 00:00:00','2017-04-10 00:00:00','',6,0,1,0,'#907611'),(12,'create generator tools (autoadmin)','2017-04-10 00:00:00','2017-04-17 00:00:00','',6,0,1,0,'#907611'),(13,'test MVC - no css framework (basic html) - redo basic zilu interface','2017-04-24 00:00:00','2017-05-01 00:00:00','',7,0,1,1,'rgb(79, 16, 62)'),(14,'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html','2017-04-17 00:00:00','2017-04-24 00:00:00','',7,0,1,0,'rgb(79, 16, 62)'),(15,'création système import module','2017-05-01 00:00:00','2017-05-07 00:00:00','',8,0,1,0,'#34851d'),(16,'création modules basiques de test','2017-05-07 00:00:00','2017-05-14 00:00:00','',8,0,1,0,'#34851d'),(17,'conception module e-commerce','2017-05-14 00:00:00','2017-05-24 00:00:00','',9,0,1,0,'#c57fc1'),(18,'implémentation maquette front - https://www.boulanger.com/','2017-05-24 00:00:00','2017-05-29 00:00:00','',9,0,1,1,'#c57fc1'),(19,'implémentation pages backoffice','2017-05-29 00:00:00','2017-06-08 00:00:00','',9,0,1,2,'#c57fc1'),(21,'all','2017-03-13 00:00:00','2017-06-15 00:00:00','',NULL,0,1,0,'#733a4a'),(22,'modules paiement','2017-06-08 00:00:00','2017-06-15 00:00:00','',9,0,1,3,'#c57fc1');
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-08  4:18:25
