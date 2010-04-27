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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `Developer` varchar(100) DEFAULT NULL,
  `Publisher` varchar(100) DEFAULT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-04-17 13:39:39
