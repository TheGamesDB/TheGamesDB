/*
Navicat MySQL Data Transfer

Source Server         : WAMP Server (localhost)
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : tgdb-dev

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-01-01 17:50:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `moderation_reported`
-- ----------------------------
DROP TABLE IF EXISTS `moderation_reported`;
CREATE TABLE `moderation_reported` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bannerid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `reason` varchar(512) NOT NULL,
  `additional` text NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of moderation_reported
-- ----------------------------

-- ----------------------------
-- Table structure for `moderation_uploads`
-- ----------------------------
DROP TABLE IF EXISTS `moderation_uploads`;
CREATE TABLE `moderation_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `imagekey` varchar(512) NOT NULL,
  `filename` varchar(512) NOT NULL,
  `resolution` varchar(32) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of moderation_uploads
-- ----------------------------
