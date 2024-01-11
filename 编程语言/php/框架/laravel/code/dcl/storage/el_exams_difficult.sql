/*
 Navicat Premium Data Transfer

 Source Server         : kskj_local
 Source Server Type    : MySQL
 Source Server Version : 50727
 Source Host           : localhost:33060
 Source Schema         : sm_fjkspx

 Target Server Type    : MySQL
 Target Server Version : 50727
 File Encoding         : 65001

 Date: 23/07/2021 09:59:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for el_exams_difficult
-- ----------------------------
DROP TABLE IF EXISTS `el_exams_difficult`;
CREATE TABLE `el_exams_difficult` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '试题难度表主键ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '难度名称',
  `mhm_id` int(11) DEFAULT '0' COMMENT '机构ID',
  `web_id` tinyint(3) DEFAULT '0' COMMENT '站点ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='试题难度表';

-- ----------------------------
-- Records of el_exams_difficult
-- ----------------------------
BEGIN;
INSERT INTO `el_exams_difficult` VALUES (1, '简单', 0, 0);
INSERT INTO `el_exams_difficult` VALUES (2, '容易', 0, 0);
INSERT INTO `el_exams_difficult` VALUES (3, '困难', 0, 0);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
