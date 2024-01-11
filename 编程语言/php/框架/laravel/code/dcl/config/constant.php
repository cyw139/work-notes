<?php
const NOT_EXISTS = -11;
const EXISTS = -10;
const SQL_ERROR = -2;
const OP_FAILURE = -1;
const OP_SUCCESS = 0;

/**
 * 一、培训系统
 */
# 课件状态
const COURSEWARE_STATUS_FORBIDDEN = 0; // 禁用
const COURSEWARE_STATUS_NORMAL = 1; // 正常
# 附件资源类型
const ATTACH_RESOURCE_TYPE_UNKNOWN = 0; // 未知
const ATTACH_RESOURCE_TYPE_VIDEO = 1; // 视频
const ATTACH_RESOURCE_TYPE_SOUND = 2; // 音频
const ATTACH_RESOURCE_TYPE_PICTURE = 3; // 图片
const ATTACH_RESOURCE_TYPE_DOCUMENT = 4; // 文档
const ATTACH_RESOURCE_TYPE_VIDEO_URL = 5; // 视频链接
# 附件存储类型
const ATTACH_STORAGE_UNKNOWN = -1; // 未知
const ATTACH_STORAGE_LOCAL = 0; // 本地
const ATTACH_STORAGE_QINIU = 1; // 七牛
const ATTACH_STORAGE_ALIYUN = 2; // 阿里云存储
const ATTACH_STORAGE_CC = 3; // cc存储
const ATTACH_STORAGE_HUAWEIYUN = 4; // 【华为云存储】 => EduLine存储
const ATTACH_STORAGE_BAIDU_DOC = 5; // 百度文档
const ATTACH_STORAGE_UPYUN = 7; // 又拍云
const ATTACH_STORAGE_THIRD = 8; // 第三方连接
# 附件转码状态（应用于视频）
const ATTACH_TRANSCODING_STATUS_NOOP = -1; // 未转码
const ATTACH_TRANSCODING_STATUS_FAILURE = 0; // 转码失败
const ATTACH_TRANSCODING_STATUS_FINISH = 1; // 已经转码
const ATTACH_TRANSCODING_STATUS_WAITING = 2; // 等待转码

const RS_NORMAL = 10; // 正常记录
const RS_REMOVE = 11; // 回收记录
const RS_ALL = 12; // 所有记录
const RS_STATUS = [ RS_NORMAL, RS_REMOVE, RS_ALL ]; // 记录状态

const SEX_UNKNOWN = 0; // 未知
const SEX_MALE = 1; // 男
const SEX_FEMALE = 2; // 女

/**
 * 二、模拟考试
 */
// 教育程度
const EDUCATION_LEVEL_1 = 1; // 小学
const EDUCATION_LEVEL_2 = 2; // 初中
const EDUCATION_LEVEL_3 = 3; // 高中
const EDUCATION_LEVEL_4 = 4; // 本科
const EDUCATION_LEVEL_5 = 5; // 硕士研究生
const EDUCATION_LEVEL_6 = 6; // 博仕研究生
// 学制
const SCHOOL_SYSTEM_1 = 1; // 一年制
const SCHOOL_SYSTEM_2 = 2; // 两年制
const SCHOOL_SYSTEM_3 = 3; // 三年制
const SCHOOL_SYSTEM_4 = 4; // 四年制
const SCHOOL_SYSTEM_5 = 5; // 五年制
// 在职情况
const JOB_STATUS_DECIDING = 0; // 待定
const JOB_STATUS_HOLD = 1; // 在职
const JOB_STATUS_RESIGN = 2; // 离职
// 企业经营状态
const ENTERPRISE_STATUS_UNKNOWN = 0; // 未知
const ENTERPRISE_STATUS_RESERVE = 1; // 存续
const ENTERPRISE_STATUS_DESTROY = 2; // 注销



