<?php 
/**
 * VIT核心配置文件 conf.php
 *
 * 更新
 * 2015年9月30日 加入APP_NAME，由url的host判断得到项目名称，APP_NAME不能是数字，IP地址访问时将自动加载APP_0的项目
 * 2015年10月10日 将APP_NAME的定义分成两种形式，WEB环境下手工定义，LOCAL环境下自动加载
 * 20161101 去除资源定位器：HT, HT_APP, HT_PUB
 * 20170920去除WINOS静态值，其值由\clib\lists::IS_WINOS();
 */

//1. 需修改的定义
const PJ_NANE='demo';//当前生效APP的名字

//2. 根目录 & 系统分区目录
//外部公共目录
const W3='public';
// 系统根目录(文件系统)WebRoot, 注意不是app根目录, -8：\sys\inc
define('WR', substr(str_replace( "\\", '/', dirname(__FILE__) ),0,-6) );
//框架核心目录
define('FW', WR.'vendor/framework/');//framework
//当前app目录
define('PJ', WR.'app/'.PJ_NANE.'/');
//公共静态资源目录
define('ST',  WR.W3.'/static/');

//3. 项目基本配置
//配置时区
date_default_timezone_set('PRC');


//4. 开启所有错误报告
error_reporting(E_ALL);
