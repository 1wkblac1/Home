<?php

/*
 * 异常码
 */
const SUCCESS = 10000;
const ERROR_PARAMS = 10001; // 参数验证错误
const ERROR_HANDLE = 10002; // 处理逻辑错误
const ERROR_NO_TARGET = 10003; // 目标不存在

/*
 * token异常码
 */
const TOKEN_EXPIRE = 10100;
const TOKEN_NULL = 10101;

/*
 * 用户类型
 */
const USER_TYPE_ADMIN = 1; //后台用户

/*
 * 登录过期时间
 */
const LOGIN_EXPIRE_TIME = 60 * 60 * 6;

/*
 * 登录者来源
 */
const LOGIN_USER_TYPE_HOME = 1; // 后台用户

// 上传文件类型
const TEMP_FILE_TYPE_IMAGE = 1 ; // 临时文件类型 图片
const TEMP_FILE_TYPE_VIDEO = 2 ; // 临时文件类型 视频
const TEMP_FILE_TYPE_RADIO = 3 ; // 临时文件类型 音频
const TEMP_FILE_TYPE_FILE = 4 ; // 临时文件类型 文件

const TEMP_FILE_TYPE_OPTION = [
    1 =>"图片",
    2 =>"视频",
    3 =>"音频",
    4 =>"文件",
];

// 图片缩放比例
const IMAGE_SCALE = 5;
