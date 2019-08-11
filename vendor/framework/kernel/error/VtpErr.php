<?php namespace kernel\error;
/*
 * 开发者
 *
 */

class VtpErr 
{
	// 注册错误/异常语柄 到 Erun
	public static function register()
	{
		// 捕获异常
		set_exception_handler('\kernel\error\VtpErr::dealExpt');
		// 捕获错误
		set_error_handler('\kernel\error\Erun::dealErr0');
		// 捕获其它未被捕捉或停止的
		register_shutdown_function('\kernel\error\Erun::dealErr1');
	}

	// vtp错误启动入口
	public static function exc()
	{
		//显示控制
		//1. 关闭php系统错误/异常显示
		error_reporting(1);

		//2. 错误显示控制
		if(\ERR_ON>0){
			//开启错误显示
			// ini_set('display_errors', 1);
			//php启动错误信息：开(默认关)
			ini_set('display_startup_errors', 1);
			
			//以HTML形式显示到页面：开(默认开)
			ini_set('html_errors', 1);

			//注册错误捕获
			self::register();
		}

		//3. 错误log
		if(\LOG_ON>0){
			//开启错误日志
			ini_set('log_errors', 'On');
			//严重&编译等 错误写入 日志
			ini_set('error_log', \ERROR_LOG_FILE);
			return true;
		}
		else{
			//不开启日志，注意：没有此行 错误日志将写入到 php.ini的错误路径
			ini_set('log_errors', 'Off');
			return false;
		}

	}


	//所有的异常被自动捕获后，均在此处理 20190616
	/*
	 * 对在运行中的所有错误和异常的处理接口
	 * 注1：此方法不用显式调用，self::register已绑定过，有错误时被自动调用
	 * 注2：a.有错误时，此方法作为错误的处理方法，接管错误
	 * 		b.有异常，且未被捕捉时，自动执行本方法
	 * 
	 **/
	public static function dealExpt($Expt)
	{
		// echo '<h1 color="red">dealExpt 错误总入口</h1>'; 
		//取得异常的名称 
		$exptName = get_class($Expt);
		// var_dump($exptName, $Expt);exit;

		switch ($exptName) {
			/*
			//注意：用户级错误不由Eusr维护，而由Rtn维护与处理
			case 'kernel\Error\Eusr':{
				self::displayEusr($Expt);
				break;
			}
			*/
			case 'kernel\error\Erun':{
				self::display($Expt);
				break;
			}

			default:{
				//非运行异常，均转为运算异常进行处理
				$Expt=new Erun($Expt->getMessage(), $Expt->getCode(), $Expt->getCode(), $Expt->getFile(), $Expt->getLine());
				//调用vtpErr显示错误
				self::display($Expt);
			}

		}
		
	}


	//erun输出视图，必需由exc调用
	public static function display($expt)
	{
		$data=[
			'etype'=>$expt->getEtype(),
			'message'=>$expt->getMessage(),
			'line'=>$expt->getLine(),
			'file'=>$expt->getFile(),
			'stack'=>(string)$expt,
			'line0'=>$expt->getLine()-10,
		];
		// var_dump($data);exit;

		//起始行必需不小于1
		if($data['line0']<1) $data['line0']=1;
		//1. 定位错误文件，并读取上下各10行
		$data['lines'] = self::_getFileLines($data['file'],$data['line0'],$data['line']+10 );

		// var_dump($data);exit;
		//2. 解析数据并载入视图
		unset($expt);//清除释放内存
		$tpl = new \kernel\Templete;
		$tpl->assign($data)->display('err/erun.tpl', true);
		exit;
	}


	/** 返回文件从X行到Y行的内容(支持php5、php4)  
	 * @param string $filename 文件名
	 * @param int $startLine 开始的行数
	 * @param int $endLine 结束的行数
	 * @return string
	 */
	public static function _getFileLines($filename, $startLine = 1, $endLine=50, $method='rb')
	{
	    $r = array();
	    $count = $endLine - $startLine;  

	    $fp = new \SplFileObject($filename, $method);
	    $fp->seek($startLine-1);// 转到第N行, seek方法参数从0开始计数
	    for($i = 0; $i <= $count; ++$i) {
	        $r[]=$fp->current();// current()获取当前行内容
	        $fp->next();// 下一行
	    }
	    

	    return array_filter($r); // array_filter过滤：false,null,''
	}


	
	// eusr 的错误页面
	public static function displayEusr($expt)
	{
		//判断构造函数传入的状态码
		switch ( $expt->getCode() )
		{
			case 404:
				\kernel\Rtn::E404($expt->getMessage());
				break;		
			case 403:
				//# code...
				//echo '显示403页';
				\kernel\Rtn::E403();
				break;
			//500	
			default:
				// echo '未知错误页';
				\kernel\Rtn::E500('653');
				break;
		}
		
	}

}
	