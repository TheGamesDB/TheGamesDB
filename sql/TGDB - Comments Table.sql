/*
Navicat MySQL Data Transfer

Source Server         : Wamp Server (Localhost)
Source Server Version : 50508
Source Host           : localhost:3306
Source Database       : gdb

Target Server Type    : MYSQL
Target Server Version : 50508
File Encoding         : 65001

Date: 2011-11-27 22:56:20
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
