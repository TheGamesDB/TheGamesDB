-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-6
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 24, 2008 at 10:37 PM
-- Server version: 5.0.32
-- PHP Version: 5.2.0-8+etch10
-- 
-- Database: `thetvdb`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `apiusers`
-- 

DROP TABLE IF EXISTS `apiusers`;
CREATE TABLE `apiusers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `apikey` varchar(16) default NULL,
  `projectname` varchar(255) NOT NULL,
  `projectwebsite` varchar(255) default NULL,
  `userid` int(10) unsigned NOT NULL,
  `lastupdated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `banners`
-- 

DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `keytype` varchar(16) NOT NULL,
  `keyvalue` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL default '1',
  `subkey` varchar(16) default NULL,
  `filename` varchar(255) NOT NULL,
  `username` varchar(45) default NULL,
  `dateadded` int(10) unsigned default NULL,
  `languageid` int(10) NOT NULL default '7',
  `resolution` varchar(9) default NULL,
  `colors` varchar(255) default NULL,
  `artistcolors` varchar(255) default NULL,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `FK_banners_1` (`userid`),
  KEY `Index_3` (`keyvalue`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=25174 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `deletions`
-- 

DROP TABLE IF EXISTS `deletions`;
CREATE TABLE `deletions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `path` varchar(255) character set latin1 NOT NULL default 'junkinfo',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2912 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `genres`
-- 

DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `genre` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `imgstatus`
-- 

DROP TABLE IF EXISTS `imgstatus`;
CREATE TABLE `imgstatus` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `languages`
-- 

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `abbreviation` varchar(10) NOT NULL,
  `name` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL default '0',
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mirrors`
-- 

DROP TABLE IF EXISTS `mirrors`;
CREATE TABLE `mirrors` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `mirrorpath` varchar(255) NOT NULL,
  `contactemail` varchar(255) default NULL,
  `projectname` varchar(255) default NULL,
  `lastupdated` timestamp NOT NULL default '2006-12-31 23:00:00',
  `mirrorpass` varchar(45) NOT NULL,
  `typemask` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `ratings`
-- 

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `itemtype` varchar(16) character set latin1 NOT NULL,
  `itemid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `itemid` (`itemid`,`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8039 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `runtimes`
-- 

DROP TABLE IF EXISTS `runtimes`;
CREATE TABLE `runtimes` (
  `Runtime` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Runtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `seriesupdates`
-- 

DROP TABLE IF EXISTS `seriesupdates`;
CREATE TABLE `seriesupdates` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `seriesid` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `seriesid` (`seriesid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=92405 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_episodename`
-- 

DROP TABLE IF EXISTS `translation_episodename`;
CREATE TABLE `translation_episodename` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `episodeid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` varchar(255) NOT NULL,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `EpisodeId` (`episodeid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=514206 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_episodeoverview`
-- 

DROP TABLE IF EXISTS `translation_episodeoverview`;
CREATE TABLE `translation_episodeoverview` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `episodeid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` text,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `EpisodeId` (`episodeid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=324950 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_labels`
-- 

DROP TABLE IF EXISTS `translation_labels`;
CREATE TABLE `translation_labels` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `languageid` int(10) unsigned NOT NULL,
  `english` varchar(255) NOT NULL,
  `translation` varchar(255) NOT NULL,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `LanguageKey` (`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_seriesname`
-- 

DROP TABLE IF EXISTS `translation_seriesname`;
CREATE TABLE `translation_seriesname` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `seriesid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` varchar(255) NOT NULL,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `SeriesId` (`seriesid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=35468 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_seriesoverview`
-- 

DROP TABLE IF EXISTS `translation_seriesoverview`;
CREATE TABLE `translation_seriesoverview` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `seriesid` int(10) unsigned NOT NULL,
  `languageid` int(10) unsigned NOT NULL,
  `translation` text,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `SeriesId` (`seriesid`,`languageid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25946 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tvepisodes`
-- 

DROP TABLE IF EXISTS `tvepisodes`;
CREATE TABLE `tvepisodes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `seasonid` int(10) unsigned NOT NULL default '0',
  `EpisodeNumber` int(10) unsigned NOT NULL default '0',
  `EpisodeName` varchar(255) default 'Unknown',
  `FirstAired` varchar(45) default NULL,
  `GuestStars` text,
  `Director` text,
  `Writer` text,
  `Overview` text,
  `ProductionCode` varchar(45) default NULL,
  `ShowURL` varchar(255) default NULL,
  `lastupdated` int(10) unsigned default NULL,
  `flagged` int(10) unsigned default '0',
  `DVD_discid` varchar(45) default NULL,
  `DVD_season` int(10) unsigned default NULL,
  `DVD_episodenumber` decimal(10,1) unsigned default NULL,
  `DVD_chapter` int(10) unsigned default NULL,
  `locked` varchar(3) NOT NULL default 'no',
  `absolute_number` int(3) default NULL,
  `filename` varchar(255) default NULL,
  `seriesid` int(10) unsigned NOT NULL,
  `lastupdatedby` int(10) unsigned NOT NULL default '0',
  `airsafter_season` varchar(10) default NULL,
  `airsbefore_season` int(10) default NULL,
  `airsbefore_episode` int(10) default NULL,
  `thumb_author` int(10) NOT NULL default '1',
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lockedby` int(11) NOT NULL,
  `IMDB_ID` varchar(25) default NULL,
  `EpImgFlag` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IMDB_ID` (`IMDB_ID`),
  KEY `mirrorupdate` (`mirrorupdate`),
  KEY `seasonid` (`seasonid`),
  KEY `seriesid` (`seriesid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 5120 kB; (`seasonid`) REFER `tvshows/tvseasons`' AUTO_INCREMENT=372817 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tvseasons`
-- 

DROP TABLE IF EXISTS `tvseasons`;
CREATE TABLE `tvseasons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `seriesid` int(10) unsigned NOT NULL,
  `season` int(10) unsigned NOT NULL,
  `bannerrequest` int(10) unsigned default '0',
  `locked` varchar(3) NOT NULL default 'no',
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lockedby` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueseason` (`seriesid`,`season`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=33326 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tvseries`
-- 

DROP TABLE IF EXISTS `tvseries`;
CREATE TABLE `tvseries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `SeriesName` varchar(255) NOT NULL,
  `SeriesID` varchar(45) default NULL,
  `Status` varchar(100) default NULL,
  `FirstAired` varchar(100) default NULL,
  `Network` varchar(100) default NULL,
  `Runtime` varchar(100) default NULL,
  `Genre` varchar(100) default NULL,
  `Actors` text,
  `Overview` text,
  `bannerrequest` int(10) unsigned default '0',
  `lastupdated` int(10) unsigned default NULL,
  `Airs_DayOfWeek` varchar(45) default NULL,
  `Airs_Time` varchar(45) default NULL,
  `Rating` varchar(45) default NULL,
  `flagged` int(10) unsigned default '0',
  `forceupdate` int(10) unsigned default '0',
  `hits` int(10) unsigned default '0',
  `updateID` int(10) NOT NULL default '0',
  `requestcomment` varchar(255) NOT NULL default '',
  `locked` varchar(3) NOT NULL default 'no',
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lockedby` int(11) NOT NULL,
  `autoimport` varchar(16) default NULL,
  `disabled` varchar(3) NOT NULL default 'No',
  `IMDB_ID` varchar(25) default NULL,
  `zap2it_id` varchar(12) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IMDB_ID` (`IMDB_ID`),
  UNIQUE KEY `zap2it_id` (`zap2it_id`),
  UNIQUE KEY `SeriesID` (`SeriesID`),
  KEY `mirrorupdate` (`mirrorupdate`),
  KEY `disabled` (`disabled`),
  FULLTEXT KEY `SeriesName` (`SeriesName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=82354 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `user_episodes`
-- 

DROP TABLE IF EXISTS `user_episodes`;
CREATE TABLE `user_episodes` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `episodeid` int(10) unsigned NOT NULL,
  `status` varchar(16) NOT NULL,
  `lastupdated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`episodeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(45) NOT NULL,
  `userpass` varchar(255) NOT NULL,
  `emailaddress` varchar(45) default NULL,
  `ipaddress` varchar(45) default NULL,
  `userlevel` varchar(45) default 'USER',
  `languageid` int(10) unsigned NOT NULL default '7',
  `favorites` text,
  `favorites_displaymode` varchar(8) NOT NULL default 'banners',
  `bannerlimit` int(11) default '3',
  `banneragreement` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1',
  `uniqueid` varchar(16) default NULL,
  `lastupdatedby_admin` int(10) unsigned default NULL,
  `mirrorupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `mirrorupdate` (`mirrorupdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3187 ;
