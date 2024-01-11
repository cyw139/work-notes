/*
 Navicat Premium Data Transfer

 Source Server         : 酷爽科技-旧版
 Source Server Type    : MySQL
 Source Server Version : 50727
 Source Host           : localhost:3306
 Source Schema         : sm_fjkspx

 Target Server Type    : MySQL
 Target Server Version : 50727
 File Encoding         : 65001

 Date: 13/08/2021 15:08:32
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for el_zy_currency_category
-- ----------------------------
DROP TABLE IF EXISTS `el_zy_currency_category`;
CREATE TABLE `el_zy_currency_category` (
  `zy_currency_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程分类表主键ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `mhm_id` int(11) DEFAULT '0' COMMENT '机构ID',
  `middle_ids` int(11) DEFAULT '0' COMMENT '分类封面 一二级有',
  `is_choice_pc` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否推荐到pc 0否1是 一级主分类',
  `is_choice_app` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否推荐到app 0否1是 一级主分类',
  `is_choice_ranking` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否显示右侧排行榜 0否1是 pc首页',
  `is_h5_and_app` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否推荐到h5/app首页 二级菜单有',
  `is_nav_left` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否推荐到主分类导航左侧主显示',
  `re_sort` int(3) DEFAULT '999' COMMENT '推荐排序，值越大越靠前',
  `web_id` tinyint(3) DEFAULT '0' COMMENT '站点ID',
  PRIMARY KEY (`zy_currency_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=368 DEFAULT CHARSET=utf8 COMMENT='课程分类表';

-- ----------------------------
-- Records of el_zy_currency_category
-- ----------------------------
BEGIN;
INSERT INTO `el_zy_currency_category` VALUES (357, 282, '通用课程', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (358, 283, '工贸行业', 14, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (359, 282, '煤矿行业', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (366, 282, '防疫及宣传', 8, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (361, 282, '危化行业', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (362, 282, '烟花爆竹行业', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (363, 282, '交通运输行业', 5, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (364, 282, '班组长', 6, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (365, 282, '金属非金属矿山行业', 7, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (367, 282, '金属冶炼行业', 9, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (356, 332, '知识竞赛', 7, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (354, 332, '危险设备', 6, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (353, 332, '日常培训', 5, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (352, 332, '从业人员', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (274, 0, '法律法规', 13, 0, 0, 0, 0, 0, 1, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (278, 274, '通用法律法规', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (279, 274, '专业法律法规', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (282, 0, '安全管理知识', 14, 0, 0, 0, 0, 0, 0, 0, 1, 0);
INSERT INTO `el_zy_currency_category` VALUES (283, 0, '安全技术知识', 15, 0, 0, 0, 0, 0, 0, 0, 2, 0);
INSERT INTO `el_zy_currency_category` VALUES (284, 0, '应急管理', 16, 0, 0, 0, 0, 0, 0, 0, 3, 0);
INSERT INTO `el_zy_currency_category` VALUES (285, 279, '矿山行业', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (286, 279, '危险化学品行业', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (287, 279, '烟花爆竹行业', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (288, 279, '金属冶炼行业', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (289, 279, '煤矿行业', 5, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (290, 279, '工贸行业', 6, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (291, 279, '水电行业', 7, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (292, 279, '建筑行业', 8, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (293, 279, '交通运输行业', 9, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (294, 278, '国家法律法规', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (295, 278, '地方法律法规', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (296, 295, '福建省法律法规', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (297, 283, '金属非金属矿山行业', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (298, 283, '煤矿行业', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (299, 283, '危险化学品行业', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (300, 283, '烟花爆竹行业', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (301, 283, '金属冶炼行业', 5, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (302, 283, '一般工贸行业', 6, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (303, 283, '建筑行业', 7, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (304, 283, '水电行业', 8, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (305, 283, '交通运输行业', 9, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (306, 284, '应急救援预案', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (307, 284, '应急演练', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (308, 284, '应急响应', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (310, 283, '特种作业', 10, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (311, 274, '政策文件', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (312, 311, '国家政策文件', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (313, 311, '地方政策文件', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (314, 313, '福建省政策文件', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (315, 0, '企业课程', 18, 0, 0, 0, 0, 0, 0, 0, 5, 0);
INSERT INTO `el_zy_currency_category` VALUES (316, 0, '事故案例', 19, 0, 0, 0, 0, 0, 0, 0, 6, 0);
INSERT INTO `el_zy_currency_category` VALUES (317, 316, '矿山行业', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (318, 316, '危险化学品行业', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (319, 316, '烟花爆竹行业', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (320, 316, '金属冶炼行业', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (321, 316, '煤矿行业', 5, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (322, 316, '水电行业', 6, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (323, 316, '建筑行业', 7, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (324, 316, '交通运输行业', 8, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (351, 332, '特种作业人员', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (350, 332, '一般行业安管人员', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (349, 332, '高危行业安管人员人员', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (328, 283, '通用课程-职业健康和防护', 11, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (329, 283, '通用课程-劳动防护用品', 12, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (330, 0, '考卷管理', 20, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (331, 330, '章节考卷', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (332, 330, '结业考卷', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (333, 310, '高压电工', 1, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (334, 310, '低压电工', 2, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (335, 310, '熔化焊接与热切割', 3, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (336, 310, '高处安装和维修作业', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (337, 310, '制冷空调维修作业', 5, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (338, 310, '通风作业', 6, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (339, 310, '提升作业', 7, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (340, 310, '井下电气作业', 8, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (341, 310, '尾矿作业', 9, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (342, 310, '支柱作业', 10, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (343, 310, '排水作业', 11, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (344, 284, '应急救援', 4, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (345, 310, '氟化工艺', 12, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (346, 283, '危险设备', 13, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (347, 0, '专家论坛', 21, 0, 0, 0, 0, 0, 0, 0, 999, 0);
INSERT INTO `el_zy_currency_category` VALUES (348, 279, '特种作业', 10, 0, 0, 0, 0, 0, 0, 0, 999, 0);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
