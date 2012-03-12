/*
Navicat MySQL Data Transfer

Source Server         : WAMP (localhost)
Source Server Version : 50520
Source Host           : localhost:3306
Source Database       : gdb

Target Server Type    : MYSQL
Target Server Version : 50520
File Encoding         : 65001

Date: 2012-03-12 18:56:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `pubdev`
-- ----------------------------
DROP TABLE IF EXISTS `pubdev`;
CREATE TABLE `pubdev` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;