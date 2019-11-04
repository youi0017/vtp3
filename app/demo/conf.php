<?php
/**
* 项目配置
* 20170726
*/

//APP控制器目录
const APP_CTL = 'ctl';
//APP模版目录
const APP_TPL = 'tpl';


//错误(显示)控制 {1:所有错误或异常(严开发模式－推荐), 0：不报告任意的错误或异常(用户模式), 2:报告非一般错误或异常(松开发模式)}
const ERR_ON = 1;
//错误(日志)控制 {关闭:0 开启:1}
const LOG_ON = 1;
//定义存储日志(错误)文件的位置
define('ERROR_LOG_FILE', WR.'run/logs/'.PJ_NAME.'_err_'.date('Ymd').'.txt');


//数据库配置信息-local
const DB_CNF='{"dsn":"mysql:host=localhost;port=3306;dbname=chy_cs;","usr":"chy","pwd":"123456"}';

// 邮箱服务器信息
const EMAIL_CNF='{"host":"smtp.qq.com","usr":"xxxx@xx.com","pwd":"sqaxcgyaslsbvgwdsdfac","nick":"xxx", "debug":false}';


