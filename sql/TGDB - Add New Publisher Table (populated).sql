/*
Navicat MySQL Data Transfer

Source Server         : Wamp Server (Localhost)
Source Server Version : 50508
Source Host           : localhost:3306
Source Database       : gdb

Target Server Type    : MYSQL
Target Server Version : 50508
File Encoding         : 65001

Date: 2011-11-27 09:24:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `publishers`
-- ----------------------------
DROP TABLE IF EXISTS `publishers`;
CREATE TABLE `publishers` (
  `id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `logo` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of publishers
-- ----------------------------
INSERT INTO `publishers` VALUES ('1', '2k, 2K', '2k.png');
INSERT INTO `publishers` VALUES ('2', '3D Realms', '3drealms.png');
INSERT INTO `publishers` VALUES ('3', '3do', '3do.png');
INSERT INTO `publishers` VALUES ('4', 'Accolade', 'Accolade.png');
INSERT INTO `publishers` VALUES ('5', 'Acclaim', 'Acclaim.png');
INSERT INTO `publishers` VALUES ('6', 'Apogee', 'Apogee.png');
INSERT INTO `publishers` VALUES ('7', 'Activision', 'Activision.png');
INSERT INTO `publishers` VALUES ('8', 'Neversoft', 'Neversoft.png');
INSERT INTO `publishers` VALUES ('9', 'Infinity', 'Infinity.png');
INSERT INTO `publishers` VALUES ('10', 'Atari', 'Atari.png');
INSERT INTO `publishers` VALUES ('11', 'Atlus', 'Atlus.png');
INSERT INTO `publishers` VALUES ('12', 'Namco, Bandai', 'Namco.png');
INSERT INTO `publishers` VALUES ('13', 'Bethesda', 'Bethesda.png');
INSERT INTO `publishers` VALUES ('14', 'Zenimax', 'Zenimax.png');
INSERT INTO `publishers` VALUES ('15', 'Broderbund', 'Broderbund.png');
INSERT INTO `publishers` VALUES ('16', 'Blizzard', 'Blizzard.png');
INSERT INTO `publishers` VALUES ('17', 'Bungie', 'Bungie.png');
INSERT INTO `publishers` VALUES ('18', 'Capcom', 'Capcom.png');
INSERT INTO `publishers` VALUES ('19', 'Codemasters', 'Codemasters.png');
INSERT INTO `publishers` VALUES ('20', 'Coleco', 'Coleco.png');
INSERT INTO `publishers` VALUES ('21', 'Data', 'Dataeast.png');
INSERT INTO `publishers` VALUES ('22', 'Silver', 'Silver.png');
INSERT INTO `publishers` VALUES ('23', 'Disney', 'Disney.png');
INSERT INTO `publishers` VALUES ('24', 'Eidos', 'Eidos.png');
INSERT INTO `publishers` VALUES ('25', 'EA, Electronic', 'Ea.png');
INSERT INTO `publishers` VALUES ('26', 'Epic', 'Epic.png');
INSERT INTO `publishers` VALUES ('27', 'Hasbro', 'Hasbro.png');
INSERT INTO `publishers` VALUES ('28', 'Hudson', 'Hudson.png');
INSERT INTO `publishers` VALUES ('29', 'Infogrames', 'Infogrames.png');
INSERT INTO `publishers` VALUES ('30', 'Tecmo, Koei', 'Tecmo.png');
INSERT INTO `publishers` VALUES ('31', 'Konami', 'Konami.png');
INSERT INTO `publishers` VALUES ('32', 'Lucas, LucasArts', 'Lucas.png');
INSERT INTO `publishers` VALUES ('33', 'Maxis', 'Maxis.png');
INSERT INTO `publishers` VALUES ('34', 'MicroProse', 'MicroProse.png');
INSERT INTO `publishers` VALUES ('35', 'Microsoft', 'Microsoft.png');
INSERT INTO `publishers` VALUES ('36', 'Midway', 'Midway.png');
INSERT INTO `publishers` VALUES ('37', 'MTV', 'MTV.png');
INSERT INTO `publishers` VALUES ('38', 'NCsoft', 'NCsoft.png');
INSERT INTO `publishers` VALUES ('39', 'Nintendo', 'Nintendo.png');
INSERT INTO `publishers` VALUES ('40', 'Ocean', 'Ocean.png');
INSERT INTO `publishers` VALUES ('41', 'Origin', 'Origin.png');
INSERT INTO `publishers` VALUES ('42', 'Popcap', 'Popcap.png');
INSERT INTO `publishers` VALUES ('43', 'Storm', 'Storm.png');
INSERT INTO `publishers` VALUES ('44', 'Rockstar', 'Rockstar.png');
INSERT INTO `publishers` VALUES ('45', 'Sega, SEGA', 'Sega.png');
INSERT INTO `publishers` VALUES ('46', 'Sierra', 'Sierra.png');
INSERT INTO `publishers` VALUES ('47', 'SNK', 'SNK.png');
INSERT INTO `publishers` VALUES ('48', 'Sony', 'Sony.png');
INSERT INTO `publishers` VALUES ('49', 'Enix, Square', 'Enix.png');
INSERT INTO `publishers` VALUES ('50', 'Stardock', 'Stardock.png');
INSERT INTO `publishers` VALUES ('51', 'Sunsoft', 'Sunsoft.png');
INSERT INTO `publishers` VALUES ('52', 'Take, T2', 'T2.png');
INSERT INTO `publishers` VALUES ('53', 'THQ', 'THQ.png');
INSERT INTO `publishers` VALUES ('54', 'Ubisoft', 'Ubisoft.png');
INSERT INTO `publishers` VALUES ('55', 'Rare, Rareware', 'Rare.png');
INSERT INTO `publishers` VALUES ('56', 'Valve', 'Valve.png');
INSERT INTO `publishers` VALUES ('57', 'Virgin', 'Virgin.png');
