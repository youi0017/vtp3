<?php
header("Content-type: text/html; charset=utf-8");

//载入 全局配置
require '../config/conf.php';

//载入 项目配置
require PJ.'conf.php';

//引入助理
require FW.'kernel/Assistant.php';

//引入用户级错误
use \Kernel\Error\Eusr;

\Kernel\Vtp::exc();
