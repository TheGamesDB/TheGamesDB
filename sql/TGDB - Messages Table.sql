/*
Navicat MySQL Data Transfer

Source Server         : Wamp Server (Localhost)
Source Server Version : 50508
Source Host           : localhost:3306
Source Database       : gdb

Target Server Type    : MYSQL
Target Server Version : 50508
File Encoding         : 65001

Date: 2011-11-28 06:47:41
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of messages
-- ----------------------------
