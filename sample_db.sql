/*
Navicat MySQL Data Transfer

Source Server         : WAMP Server (localhost)
Source Server Version : 50508
Source Host           : localhost:3306
Source Database       : gdb-blank

Target Server Type    : MYSQL
Target Server Version : 50508
File Encoding         : 65001

Date: 2012-06-21 02:34:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `apiusers`
-- ----------------------------
DROP TABLE IF EXISTS `apiusers`;
CREATE TABLE `apiusers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apikey` varchar(16) DEFAULT NULL,
  `projectname` varchar(255) NOT NULL DEFAULT '',
  `projectwebsite` varchar(255) DEFAULT NULL,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apiusers
-- ----------------------------

-- ----------------------------
-- Table structure for `audits`
-- ----------------------------
DROP TABLE IF EXISTS `audits`;
CREATE TABLE `audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `action` text,
  `game` int(11) DEFAULT NULL,
  `time` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of audits
-- ----------------------------

-- ----------------------------
-- Table structure for `banners`
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keytype` varchar(16) NOT NULL DEFAULT '',
  `keyvalue` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '1',
  `subkey` varchar(16) DEFAULT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM AUTO_INCREMENT=6886 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of banners
-- ----------------------------

-- ----------------------------
-- Table structure for `comments`
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `comment` text,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of comments
-- ----------------------------

-- ----------------------------
-- Table structure for `deletions`
-- ----------------------------
DROP TABLE IF EXISTS `deletions`;
CREATE TABLE `deletions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'junkinfo',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1009 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of deletions
-- ----------------------------

-- ----------------------------
-- Table structure for `games`
-- ----------------------------
DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `GameTitle` varchar(255) NOT NULL DEFAULT '',
  `GameID` varchar(45) DEFAULT NULL,
  `Players` tinyint(4) DEFAULT NULL,
  `ReleaseDate` varchar(100) DEFAULT NULL,
  `Developer` varchar(100) DEFAULT NULL,
  `Publisher` varchar(100) DEFAULT NULL,
  `Runtime` varchar(100) DEFAULT NULL,
  `Genre` varchar(100) DEFAULT NULL,
  `Actors` text,
  `Overview` text,
  `bannerrequest` int(10) unsigned DEFAULT '0',
  `created` int(10) DEFAULT NULL,
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
  `lockedby` int(11) NOT NULL DEFAULT '0',
  `autoimport` varchar(16) DEFAULT NULL,
  `disabled` varchar(3) NOT NULL DEFAULT 'No',
  `IMDB_ID` varchar(25) DEFAULT NULL,
  `zap2it_id` varchar(12) DEFAULT NULL,
  `Platform` varchar(100) DEFAULT NULL,
  `coop` varchar(10) DEFAULT NULL,
  `os` varchar(255) NOT NULL,
  `processor` varchar(255) NOT NULL,
  `ram` varchar(255) NOT NULL,
  `hdd` varchar(255) NOT NULL,
  `video` varchar(255) NOT NULL,
  `sound` varchar(255) NOT NULL,
  `Youtube` text,
  `Alternates` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IMDB_ID` (`IMDB_ID`),
  UNIQUE KEY `zap2it_id` (`zap2it_id`),
  KEY `mirrorupdate` (`mirrorupdate`),
  KEY `disabled` (`disabled`),
  FULLTEXT KEY `GameTitle` (`GameTitle`)
) ENGINE=MyISAM AUTO_INCREMENT=3171 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of games
-- ----------------------------

-- ----------------------------
-- Table structure for `genres`
-- ----------------------------
DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `genre` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of genres
-- ----------------------------

-- ----------------------------
-- Table structure for `imgstatus`
-- ----------------------------
DROP TABLE IF EXISTS `imgstatus`;
CREATE TABLE `imgstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of imgstatus
-- ----------------------------

-- ----------------------------
-- Table structure for `languages`
-- ----------------------------
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `abbreviation` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of languages
-- ----------------------------
INSERT INTO `languages` VALUES ('1', 'en', 'English', '1', '2010-04-23 04:25:40');

-- ----------------------------
-- Table structure for `messages`
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `status` varchar(50) DEFAULT 'new',
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of messages
-- ----------------------------

-- ----------------------------
-- Table structure for `mirrors`
-- ----------------------------
DROP TABLE IF EXISTS `mirrors`;
CREATE TABLE `mirrors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mirrorpath` varchar(255) NOT NULL DEFAULT '',
  `contactemail` varchar(255) DEFAULT NULL,
  `projectname` varchar(255) DEFAULT NULL,
  `lastupdated` timestamp NOT NULL DEFAULT '2007-01-01 15:00:00',
  `mirrorpass` varchar(45) NOT NULL DEFAULT '',
  `typemask` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mirrors
-- ----------------------------

-- ----------------------------
-- Table structure for `platforms`
-- ----------------------------
DROP TABLE IF EXISTS `platforms`;
CREATE TABLE `platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) DEFAULT NULL,
  `icon` varchar(100) NOT NULL,
  `console` varchar(100) DEFAULT NULL,
  `controller` varchar(100) DEFAULT NULL,
  `developer` text,
  `manufacturer` text,
  `media` text,
  `cpu` text,
  `memory` text,
  `graphics` text,
  `sound` text,
  `maxcontrollers` text,
  `display` text,
  `overview` text,
  `youtube` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4915 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of platforms
-- ----------------------------

-- ----------------------------
-- Table structure for `pubdev`
-- ----------------------------
DROP TABLE IF EXISTS `pubdev`;
CREATE TABLE `pubdev` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of pubdev
-- ----------------------------

-- ----------------------------
-- Table structure for `publishers`
-- ----------------------------
DROP TABLE IF EXISTS `publishers`;
CREATE TABLE `publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `logo` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of publishers
-- ----------------------------

-- ----------------------------
-- Table structure for `ratings`
-- ----------------------------
DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `itemtype` varchar(16) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `itemid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `itemid` (`itemid`,`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=318 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ratings
-- ----------------------------

-- ----------------------------
-- Table structure for `runtimes`
-- ----------------------------
DROP TABLE IF EXISTS `runtimes`;
CREATE TABLE `runtimes` (
  `Runtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Runtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of runtimes
-- ----------------------------

-- ----------------------------
-- Table structure for `translation_labels`
-- ----------------------------
DROP TABLE IF EXISTS `translation_labels`;
CREATE TABLE `translation_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageid` int(10) unsigned NOT NULL DEFAULT '0',
  `english` varchar(255) NOT NULL DEFAULT '',
  `translation` varchar(255) NOT NULL DEFAULT '',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `LanguageKey` (`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of translation_labels
-- ----------------------------

-- ----------------------------
-- Table structure for `translation_seriesname`
-- ----------------------------
DROP TABLE IF EXISTS `translation_seriesname`;
CREATE TABLE `translation_seriesname` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seriesid` int(10) unsigned NOT NULL DEFAULT '0',
  `languageid` int(10) unsigned NOT NULL DEFAULT '0',
  `translation` varchar(255) NOT NULL DEFAULT '',
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `SeriesId` (`seriesid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of translation_seriesname
-- ----------------------------

-- ----------------------------
-- Table structure for `translation_seriesoverview`
-- ----------------------------
DROP TABLE IF EXISTS `translation_seriesoverview`;
CREATE TABLE `translation_seriesoverview` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seriesid` int(10) unsigned NOT NULL DEFAULT '0',
  `languageid` int(10) unsigned NOT NULL DEFAULT '0',
  `translation` text,
  `mirrorupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `SeriesId` (`seriesid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of translation_seriesoverview
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL DEFAULT '',
  `userpass` varchar(255) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM AUTO_INCREMENT=247 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('246', 'admin', '*4ACFE3202A5FF5CF467898FC58AAB1D615029441', 'nonexist@thegamesdb.net', null, 'SUPERADMIN', '1', null, 'banners', '3', '0', '1', '01B4EBCDFCA577ED', null, '2012-06-21 02:30:11');
