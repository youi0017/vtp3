<?php
/*
 * vit 框架助理Assistant/Asst 
 * 注册函数集合 
 * 20161101 LM:20170703
*/



/**
 * 1. 捕获错误
 * set_error_handler 错误捕捉用户自定义错误 handler
 * 注一：只能够捕获 E_WARNING E_NOTICE E_DEPRECATED E_USER_* E_STRICT 级的错误
 * 注二：无法捕获 E_ERROR E_PARSE E_CORE_* E_COMPILE_* [DivisionByZeroError TypeError] 级的错误
 */
// set_error_handler('_get_err');

/**
 * 2. 捕获异常
 * set_exception_handler 用户自定义捕获异常 handler
 	注：由于vtp是一个大的try 和 catch 所以，待验证是否需要引模块
 	
 	说明：
 * 异常没有被 try ... catch ... 捕获处理的话会被抛出
 * 此时系统会检查上下文是否注册了 set_exception_handler
 * 如果未注册 则进入 PHP 标准异常处理 致命错误退出执行
 * 如果已注册 则进入 set_exception_handler 处理 程序依然会退出执行
 * 而 try ... catch ... 捕获异常后仍不会退出执行
 * 故强烈建议将有异常的执行逻辑放入 try ... catch 中
 * 
set_exception_handler(function ($exception) {
    echo '<h1 style="color:red">异常捕获输出'.$exception.'</h1>';
    // 此处程序会退出执行 异常到此结束 并不会交给 PHP 标准异常处理
});
 */

//2. 注册autoload
spl_autoload_register('myloader');


// 错误处理: 注册错误处理函数
\Kernel\Error\VtpErr::exc();


/*
//3. 错误(显示)控制 {开发:1 用户:0}
if(ERR_ON>0){
	//开启错误显示
	ini_set('display_errors', 1);
	//php启动错误信息：开(默认关)
	ini_set('display_startup_errors', 1);
	
	//以HTML形式显示到页面：开(默认开)
	ini_set('html_errors', 1);
}
else{
	//关闭错误显示
	ini_set('display_errors', 0);
}


//4. 错误日志(严重&编译等日志)控制 {关闭:0 开启:1} ，写入错误日志：ERROR_LOG_FILE
if(LOG_ON>0){
	//开启错误日志
	ini_set('log_errors', 'On');
	//严重&编译等 错误写入 日志
	ini_set('error_log', ERROR_LOG_FILE);
}
else{
	//不开启日志，注意：没有此行 错误日志将写入到 php.ini的错误路径
	ini_set('log_errors', 'Off');
}
*/

//== 支持方法 ===================================
/**
 * vendor第三方项目载入方法
 * 说明：所有项目规则： /pub/vendor/包名packageName/autoload.php
 * @$packageName packagist包的名称
 * 20190518
 * 
 	源代码示例:
  	require_once (FR_PUB."vendor/phpoffice/autoload.php");
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setCellValue('A1', 'Hello World !');
	 
	$writer = new Xlsx($spreadsheet);
	$writer->save(FR_PUB.'cs_2018.xlsx');
 */
function vendor($packageName)
{
	require_once WR.'vendor/'.$packageName.'/vendor/autoload.php';
}


/**
 * 自动加载相关库
 * 说明：已载入的模块（含php内置或扩展的模块）不会执行本方法 20190518
 * 
 * 更新
 * 20150930 将控制器类文件名的中间名由'ctl'更改为APP_CTL_MID
 * 20160612 使用空间加载方式
 * 20161101 去除加类的中间名
 * 20190518 更改取类地址方式
 */ 
function myloader($clsPath)
{
	//判断资源是否存在，并取得地址 20190519
	$p2='';
	$b = cls_file_exists($clsPath, $p2);
	//var_dump($p2);//exit;

	if(!$b)
	{
		//throw new \Exception('访问资源['.$clsPath.']不存在！');
		//\lib\tools::note_err('loader class not exited! file path is'.$p2);
	}	
}

// 
/*
 * 判断要加载的类文件是否存在
 * 如果存在，则载入，并返回true；不存在则返回false
 *  注：存在后并载入后，不会再执行myloader 20190519
 * 
 *  20190519 v0
 *  20190730 v1 空间段：最后一段强制首字母大写，前面段强制小写
 */
function cls_file_exists($clsPath, &$p2='')
{
	//$clsPath = strtolower($clsPath);
	//注意：从相对空间传入如：Kernel\Vtp， 与绝对空间传入如 \ctl\index
	if($clsPath{0}=='\\') $clsPath=substr($clsPath,1); 
	//var_dump($clsPath);
	//空间段：最后一段强制首字母大写，前面段强制小写
	$cArr = explode('\\', $clsPath);
	$t = count($cArr)-1;
	foreach ($cArr as $k => &$v) {
		$v = $k==$t ? ucwords($v) : strtolower($v);
	}

	$p2=implode('/', $cArr);
	// var_dump($cArr, $p2);exit;

	//$space=strtolower($cArr[0]);
	switch ( $cArr[0] )
	{
		//系统
		case $cArr[0]>='kernel':
		case $cArr[0]>='lib':
		{
			$p2 = FW.$p2.'.php';
			//var_dump($p2);
			break;
		}
		//项目
		case 'ctl':
		case 'mdl':
		case 'cls':
		{
			$p2 = PJ.$p2.'.php';
			break;
		}
		//其它:此处为预留，暂无用20190518
		default:
		{
			//var_dump($clsPath);exit;
			break;
		}
	}

	// var_dump($p2);
	$b = is_file($p2);
	if($b) require_once($p2);
	else
	{
		// exit('不存在：'.$p2);
	}
	return $b;
}



/**
 * 错误控制(一般性错误)
 * 20161101
 * 
 * 	配置说明：
	本配置由APP配置决定，需配置以下内容
	运行控制码： ERR_ON
		1		: 开发调试	(提示详细错误信息)
		0,-1	: 用户模式	(不显示任何错误)
	日志控制码：LOG_ON
		1		: 开发调试	(生成"错误信息"的日志)
		0,-1	: 用户模式	(不生成日志)
	
	一般性错误 和 编译性错误 均使用日志文件：ERROR_LOG_FILE
 */
function _get_err($errno, $errstr, $errfile, $errline)
{
	//一般错误(显示)
    if(ERR_ON>0)
    {
		echo '<p><b>Meet Error</b>!<br/>',$errstr,'<br/>Error on line ',$errline,'<p><br/>';
    }

	//一般错误(记录)
    if(LOG_ON>0)
    {
    	$log_msg = PHP_EOL."Meet Error[$errno]: ".date('Y-m-d G:i:s').PHP_EOL.$errstr.PHP_EOL."Error on line $errline in $errfile".PHP_EOL.'Uri is '.$_SERVER['REQUEST_URI'].PHP_EOL;
		error_log($log_msg, 3, ERROR_LOG_FILE);
    }
}


