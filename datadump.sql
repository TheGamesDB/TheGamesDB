-- MySQL dump 10.13  Distrib 5.1.37, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: thegamedb
-- ------------------------------------------------------
-- Server version	5.1.37-1ubuntu5.1

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
-- Table structure for table `apiusers`
--

DROP TABLE IF EXISTS `apiusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apiusers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apikey` varchar(16) DEFAULT NULL,
  `projectname` varchar(255) NOT NULL,
  `projectwebsite` varchar(255) DEFAULT NULL,
  `userid` int(10) unsigned NOT NULL,
  `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apiusers`
--

LOCK TABLES `apiusers` WRITE;
/*!40000 ALTER TABLE `apiusers` DISABLE KEYS */;
/*!40000 ALTER TABLE `apiusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keytype` varchar(16) NOT NULL,
  `keyvalue` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL DEFAULT '1',
  `subkey` varchar(16) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `username` varchar(45) DEFAULT NULL,
  `dateadded` int(10) unsigned DEFAULT NULL,
  `languageid` int(10) NOT NULL DEFAULT '7',
  `resolution` varchar(9) DEFAULT NULL,
  `colors` varchar(255) DEFAULT NULL,
  `artistcolors` varchar(255) DEFAULT NULL,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_banners_1` (`userid`),
  KEY `Index_3` (`keyvalue`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=25174 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deletions`
--

DROP TABLE IF EXISTS `deletions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deletions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'junkinfo',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2912 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deletions`
--

LOCK TABLES `deletions` WRITE;
/*!40000 ALTER TABLE `deletions` DISABLE KEYS */;
/*!40000 ALTER TABLE `deletions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `GameTitle` varchar(255) NOT NULL,
  `GameID` varchar(45) DEFAULT NULL,
  `Status` varchar(100) DEFAULT NULL,
  `FirstAired` varchar(100) DEFAULT NULL,
  `Network` varchar(100) DEFAULT NULL,
  `Runtime` varchar(100) DEFAULT NULL,
  `Genre` varchar(100) DEFAULT NULL,
  `Actors` text,
  `Overview` text,
  `bannerrequest` int(10) unsigned DEFAULT '0',
  `lastupdated` int(10) unsigned DEFAULT NULL,
  `Airs_DayOfWeek` varchar(45) DEFAULT NULL,
  `Airs_Time` varchar(45) DEFAULT NULL,
  `Rating` varchar(45) DEFAULT NULL,
  `flagged` int(10) unsigned DEFAULT '0',
  `forceupdate` int(10) unsigned DEFAULT '0',
  `hits` int(10) unsigned DEFAULT '0',
  `updateID` int(10) NOT NULL DEFAULT '0',
  `requestcomment` varchar(255) NOT NULL DEFAULT '',
  `locked` varchar(3) NOT NULL DEFAULT 'no',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lockedby` int(11) NOT NULL,
  `autoimport` varchar(16) DEFAULT NULL,
  `disabled` varchar(3) NOT NULL DEFAULT 'No',
  `IMDB_ID` varchar(25) DEFAULT NULL,
  `zap2it_id` varchar(12) DEFAULT NULL,
  `Platform` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IMDB_ID` (`IMDB_ID`),
  UNIQUE KEY `zap2it_id` (`zap2it_id`),
  KEY `mirrorupdate` (`mirrorupdate`),
  KEY `disabled` (`disabled`),
  FULLTEXT KEY `GameTitle` (`GameTitle`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (1,'Halo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1271467092,NULL,NULL,NULL,0,0,0,0,'','no','2010-04-17 01:18:12',0,NULL,'No',NULL,NULL,NULL),(2,'Fable',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1271521696,NULL,NULL,NULL,0,0,0,0,'','no','2010-04-17 16:28:16',0,NULL,'No',NULL,NULL,NULL),(3,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1271534337,NULL,NULL,NULL,0,0,0,0,'','no','2010-04-17 19:58:57',0,'tv.com','No',NULL,NULL,'|Sega Dreamcast|Sega Game Gear|'),(4,'Halo 3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Here is some over view text.',NULL,1271536383,NULL,NULL,NULL,0,0,0,0,'','no','2010-04-17 20:33:03',0,'tv.com','No',NULL,NULL,NULL);
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genres` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `genre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genres`
--

LOCK TABLES `genres` WRITE;
/*!40000 ALTER TABLE `genres` DISABLE KEYS */;
/*!40000 ALTER TABLE `genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imgstatus`
--

DROP TABLE IF EXISTS `imgstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imgstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imgstatus`
--

LOCK TABLES `imgstatus` WRITE;
/*!40000 ALTER TABLE `imgstatus` DISABLE KEYS */;
/*!40000 ALTER TABLE `imgstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `abbreviation` varchar(10) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'EN','English',1,'2010-04-16 23:26:15');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mirrors`
--

DROP TABLE IF EXISTS `mirrors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mirrors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mirrorpath` varchar(255) NOT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `projectname` varchar(255) DEFAULT NULL,
  `lastupdated` timestamp NOT NULL DEFAULT '2007-01-01 07:00:00',
  `mirrorpass` varchar(45) NOT NULL,
  `typemask` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mirrors`
--

LOCK TABLES `mirrors` WRITE;
/*!40000 ALTER TABLE `mirrors` DISABLE KEYS */;
/*!40000 ALTER TABLE `mirrors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `platforms`
--

DROP TABLE IF EXISTS `platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platforms`
--

LOCK TABLES `platforms` WRITE;
/*!40000 ALTER TABLE `platforms` DISABLE KEYS */;
INSERT INTO `platforms` VALUES (1,'PC'),(2,'GameCube'),(3,'Nintendo 64'),(4,'Gameboy'),(5,'Gameboy Advance'),(6,'Super Nintendo'),(7,'NES'),(8,'Nintendo DS'),(9,'Nintendo Wii'),(10,'Sony Playstation'),(11,'Sony Playstation 2'),(12,'Sony Playstation 3'),(13,'Sony PSP'),(14,'Microsoft XBox'),(15,'Microsoft XBox 360'),(16,'Sega Dreamcast'),(17,'Sega Saturn'),(18,'Sega Genesis'),(19,'Sega Master System'),(20,'Sega Game Gear'),(21,'Sega CD'),(22,'Atari 2600'),(23,'Arcade'),(24,'NeoGeo');
/*!40000 ALTER TABLE `platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `itemtype` varchar(16) CHARACTER SET latin1 NOT NULL,
  `itemid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `itemid` (`itemid`,`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=8039 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `runtimes`
--

DROP TABLE IF EXISTS `runtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `runtimes` (
  `Runtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Runtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `runtimes`
--

LOCK TABLES `runtimes` WRITE;
/*!40000 ALTER TABLE `runtimes` DISABLE KEYS */;
/*!40000 ALTER TABLE `runtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seriesupdates`
--

DROP TABLE IF EXISTS `seriesupdates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seriesupdates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seriesid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seriesid` (`seriesid`)
) ENGINE=MyISAM AUTO_INCREMENT=92412 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seriesupdates`
--

LOCK TABLES `seriesupdates` WRITE;
/*!40000 ALTER TABLE `seriesupdates` DISABLE KEYS */;
INSERT INTO `seriesupdates` VALUES (92405,1),(92406,2),(92408,3),(92411,4);
/*!40000 ALTER TABLE `seriesupdates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation_episodename`
--

DROP TABLE IF EXISTS `translation_episodename`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_episodename` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `episodeid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` varchar(255) NOT NULL,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `EpisodeId` (`episodeid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=514206 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation_episodename`
--

LOCK TABLES `translation_episodename` WRITE;
/*!40000 ALTER TABLE `translation_episodename` DISABLE KEYS */;
/*!40000 ALTER TABLE `translation_episodename` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation_episodeoverview`
--

DROP TABLE IF EXISTS `translation_episodeoverview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_episodeoverview` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `episodeid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` text,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `EpisodeId` (`episodeid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=InnoDB AUTO_INCREMENT=324950 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation_episodeoverview`
--

LOCK TABLES `translation_episodeoverview` WRITE;
/*!40000 ALTER TABLE `translation_episodeoverview` DISABLE KEYS */;
/*!40000 ALTER TABLE `translation_episodeoverview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation_labels`
--

DROP TABLE IF EXISTS `translation_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageid` int(10) unsigned NOT NULL,
  `english` varchar(255) NOT NULL,
  `translation` varchar(255) NOT NULL,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `LanguageKey` (`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation_labels`
--

LOCK TABLES `translation_labels` WRITE;
/*!40000 ALTER TABLE `translation_labels` DISABLE KEYS */;
/*!40000 ALTER TABLE `translation_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation_seriesname`
--

DROP TABLE IF EXISTS `translation_seriesname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_seriesname` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seriesid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` varchar(255) NOT NULL,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `SeriesId` (`seriesid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=35476 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation_seriesname`
--

LOCK TABLES `translation_seriesname` WRITE;
/*!40000 ALTER TABLE `translation_seriesname` DISABLE KEYS */;
INSERT INTO `translation_seriesname` VALUES (35468,1,1,'Halo','2010-04-17 01:18:12'),(35469,2,1,'Fable','2010-04-17 16:28:16'),(35471,3,1,'Fable 2','2010-04-17 19:58:57'),(35475,4,1,'Halo 3','2010-04-17 20:33:03');
/*!40000 ALTER TABLE `translation_seriesname` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation_seriesoverview`
--

DROP TABLE IF EXISTS `translation_seriesoverview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_seriesoverview` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seriesid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` text,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `SeriesId` (`seriesid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=25946 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation_seriesoverview`
--

LOCK TABLES `translation_seriesoverview` WRITE;
/*!40000 ALTER TABLE `translation_seriesoverview` DISABLE KEYS */;
/*!40000 ALTER TABLE `translation_seriesoverview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tvepisodes`
--

DROP TABLE IF EXISTS `tvepisodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tvepisodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seasonid` int(10) unsigned NOT NULL DEFAULT '0',
  `EpisodeNumber` int(10) unsigned NOT NULL DEFAULT '0',
  `EpisodeName` varchar(255) DEFAULT 'Unknown',
  `FirstAired` varchar(45) DEFAULT NULL,
  `GuestStars` text,
  `Director` text,
  `Writer` text,
  `Overview` text,
  `ProductionCode` varchar(45) DEFAULT NULL,
  `ShowURL` varchar(255) DEFAULT NULL,
  `lastupdated` int(10) unsigned DEFAULT NULL,
  `flagged` int(10) unsigned DEFAULT '0',
  `DVD_discid` varchar(45) DEFAULT NULL,
  `DVD_season` int(10) unsigned DEFAULT NULL,
  `DVD_episodenumber` decimal(10,1) unsigned DEFAULT NULL,
  `DVD_chapter` int(10) unsigned DEFAULT NULL,
  `locked` varchar(3) NOT NULL DEFAULT 'no',
  `absolute_number` int(3) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `seriesid` int(10) unsigned NOT NULL,
  `lastupdatedby` int(10) unsigned NOT NULL DEFAULT '0',
  `airsafter_season` varchar(10) DEFAULT NULL,
  `airsbefore_season` int(10) DEFAULT NULL,
  `airsbefore_episode` int(10) DEFAULT NULL,
  `thumb_author` int(10) NOT NULL DEFAULT '1',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lockedby` int(11) NOT NULL,
  `IMDB_ID` varchar(25) DEFAULT NULL,
  `EpImgFlag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IMDB_ID` (`IMDB_ID`),
  KEY `mirrorupdate` (`mirrorupdate`),
  KEY `seasonid` (`seasonid`),
  KEY `seriesid` (`seriesid`)
) ENGINE=MyISAM AUTO_INCREMENT=372817 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 5120 kB; (`seasonid`) REFER `tvshows/tvseasons`';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tvepisodes`
--

LOCK TABLES `tvepisodes` WRITE;
/*!40000 ALTER TABLE `tvepisodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `tvepisodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tvseasons`
--

DROP TABLE IF EXISTS `tvseasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tvseasons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seriesid` int(10) unsigned NOT NULL,
  `season` int(10) unsigned NOT NULL,
  `bannerrequest` int(10) unsigned DEFAULT '0',
  `locked` varchar(3) NOT NULL DEFAULT 'no',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lockedby` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueseason` (`seriesid`,`season`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=33326 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tvseasons`
--

LOCK TABLES `tvseasons` WRITE;
/*!40000 ALTER TABLE `tvseasons` DISABLE KEYS */;
/*!40000 ALTER TABLE `tvseasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_episodes`
--

DROP TABLE IF EXISTS `user_episodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_episodes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `episodeid` int(10) unsigned NOT NULL,
  `status` varchar(16) NOT NULL,
  `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`episodeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_episodes`
--

LOCK TABLES `user_episodes` WRITE;
/*!40000 ALTER TABLE `user_episodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_episodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `userpass` varchar(255) NOT NULL,
  `emailaddress` varchar(45) DEFAULT NULL,
  `ipaddress` varchar(45) DEFAULT NULL,
  `userlevel` varchar(45) DEFAULT 'USER',
  `languageid` int(10) unsigned NOT NULL DEFAULT '7',
  `favorites` text,
  `favorites_displaymode` varchar(8) NOT NULL DEFAULT 'banners',
  `bannerlimit` int(11) DEFAULT '3',
  `banneragreement` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `uniqueid` varchar(16) DEFAULT NULL,
  `lastupdatedby_admin` int(10) unsigned DEFAULT NULL,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'mattmcla','*9C54AF0E0CAFA65284B400EA017E5EA64B477D56','mclaughlin.matt@gmail.com',NULL,'SUPERADMIN',1,NULL,'banners',3,1,1,'58536D31278176DA',NULL,'2010-04-16 23:28:13');
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

-- Dump completed on 2010-04-17 13:40:30
