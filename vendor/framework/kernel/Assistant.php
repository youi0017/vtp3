<?php
/*
 * vit 框架助理Assistant/Asst 
 * 注册函数集合 
 * 20161101 LM:20170703
*/


//1. 注册autoload
spl_autoload_register('myloader');


//2. 注册错误处理
\Kernel\Error\VtpErr::exc();



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
	// var_dump($cArr, $p2);
	switch ( $cArr[0] )
	{
		//项目
		case 'ctl':
		case 'mdl':
		case 'cls':
		{
			$p2 = PJ.$p2.'.php';
			//var_dump($p2);//exit;
			break;
		}

		//系统
		case $cArr[0]>='kernel':
		case $cArr[0]>='lib':
		{
			$p2 = FW.$p2.'.php';
			//var_dump($p2);
			break;
		}

		//其它:此处为预留，暂无用20190518
		default:
		{
			//var_dump($p2);//exit;
			break;
		}
	}

	//var_dump($p2);
	$b = is_file($p2);
	if($b) require_once($p2);
	else
	{
		// exit('不存在：'.$p2);
	}
	return $b;
}


