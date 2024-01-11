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

 Date: 23/07/2021 09:58:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for el_exams_question_type
-- ----------------------------
DROP TABLE IF EXISTS `el_exams_question_type`;
CREATE TABLE `el_exams_question_type` (
  `exams_question_type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '试题类型表主键ID',
  `question_type_title` varchar(255) NOT NULL DEFAULT '' COMMENT '类型名称',
  `question_type_key` varchar(50) NOT NULL DEFAULT '' COMMENT '试题类型关键字',
  `mhm_id` int(11) DEFAULT '0' COMMENT '机构ID',
  `web_id` tinyint(3) DEFAULT '0' COMMENT '站点ID',
  PRIMARY KEY (`exams_question_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='试题类型表';

-- ----------------------------
-- Records of el_exams_question_type
-- ----------------------------
BEGIN;
INSERT INTO `el_exams_question_type` VALUES (1, '单选题', 'radio', 0, 0);
INSERT INTO `el_exams_question_type` VALUES (2, '多选题', 'multiselect', 0, 0);
INSERT INTO `el_exams_question_type` VALUES (3, '判断题', 'judge', 0, 0);
INSERT INTO `el_exams_question_type` VALUES (4, '填空题', 'completion', 0, 0);
INSERT INTO `el_exams_question_type` VALUES (5, '论述题', 'essays', 0, 0);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
