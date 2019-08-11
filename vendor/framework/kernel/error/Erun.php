<?php namespace kernel\error;

/**
* 文件名称：baseExpt.class.php
* 摘 要：自定义错误异常类, 该类继承至PHP内置的错误异常类
*/
class Erun extends \ErrorException
{
	// 错误类型：对应localName
	protected $etype='?';
	// 错误文件：隐藏目录，只剩文件名称
	protected $filename='?';

	public static $localCode = [
		E_COMPILE_ERROR => 4001,
		E_COMPILE_WARNING => 4002,
		E_CORE_ERROR => 4003,
		E_CORE_WARNING => 4004,
		E_DEPRECATED => 4005,
		E_ERROR => 4006,
		E_NOTICE => 4007,
		E_PARSE => 4008,
		E_RECOVERABLE_ERROR => 4009,
		E_STRICT => 4010,
		E_USER_DEPRECATED => 4011,
		E_USER_ERROR => 4012,
		E_USER_NOTICE => 4013,
		E_USER_WARNING => 4014,
		E_WARNING => 4015,
		4016 => 4016,
	 ];
	 
	public static $localName = [
		E_COMPILE_ERROR => '编译错误',
		E_COMPILE_WARNING => '编译警告',//Compile Warning
		E_CORE_ERROR => 'PHP错误',
		E_CORE_WARNING => 'PHP警告',
		E_DEPRECATED => '警告，此内容未来可能被废止',
		E_ERROR => '致命运行错误，必需纠正/Fatal Error',
		E_NOTICE => '运行通知，建议修改/Notic Error',
		E_PARSE => '语法错误，必需纠正/Parse Error',
		E_RECOVERABLE_ERROR => '未被捕捉的致命错误/Recoverable Error',
		E_STRICT => '严格讲是不对的，建议修改/Strict Warning',
		E_USER_DEPRECATED => '建议修改，可能有问题/User Deprecated Warning',
		E_USER_ERROR => '开发者的错误/User Error',
		E_USER_NOTICE => '提示开发者注意修改/User Notice',
		E_USER_WARNING => '警告开发者要修改/User Warning',
		E_WARNING => '运行警告，请纠正/Warning',
		4016 => '开发者不认真导致的错误',
	];


	// 取不可访问的属性
	public function __call($name, $args=[])
	{
		$name=lcfirst(substr($name, 3));
		return isset($this->$name) ? $this->$name : '???';
	}

	
	/**
	* 方  法：构造函数
	* 摘  要：相关知识请查看 http://php.net/manual/en/errorexception.construct.php
	* 
	* 参  数：string  $message  异常信息(可选)
	*    int   $code   异常代码(可选)
	*    int   $severity
	*    string  $filename  异常文件(可选)
	*    int   $line   异常的行数(可选)
	*   Exception $previous 上一个异常(可选)
	*
	* 返  回：void
	*/
	public function __construct($message = '', $code = 0, $severity = 1, $filename = __FILE__, $line = __LINE__, Exception $previous = null)
	{
		parent::__construct($message, self::getLocalCode($code), $severity, $filename, $line, $previous);
		
		$this->etype=self::getLocalName($code);
		$this->filename=self::getFilename($filename);

	}



	//一般错误
	public static function dealErr0($errno, $errstr, $errfile, $errline )
	{
		//echo '<h1 color="red">dealErr0 错误入口</h1>';//此为调试信息
		//抛出异常：
		//注：第二个参数用于区分错误等级.
		//var_dump($errno);
		if( \ERR_ON>1 ) return;
		
		throw new self($errstr, 0, $errno, $errfile, $errline);
		
		// var_dump($errno, $errstr, $errfile, $errline ,self::isFatalError($errno));//exit;
	 	// $expt =  new self($errstr, 0, $errno, $errfile, $errline);
	 	// 总入口接管错误
		// VtpErr::dealExpt($expt);
	}

	//严重错误
	//注意：因为使用 shut 捕获，所以exit也会激发此函数
	public static function dealErr1()
	{
		// exit也会
		$error = error_get_last();
	   	if($error) 
	    {
			//使用异常，接管错误：
			echo '<h1 color="red">dealErr1 错误入口</h1>';
			// var_dump($error);
			//注：第二个参数用于区分错误等级
		    $expt =  new self($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
		    //总入口接管错误
		    VtpErr::dealExpt($expt);
	    }

	}

	
	/**
	* 方  法：是否是致命性错误
	* 参  数：array $error
	* 返  回：boolean
	*/
	public static function isFatalError($errType)
	{
		return !in_array($errType, [
			E_NOTICE,
			// E_WARNING,
			E_USER_NOTICE,
			// E_USER_WARNING,
			// E_DEPRECATED
		]);
	}

	
	/**
	* 方  法：根据原始的错误代码得到本地的错误代码
	* 参  数：int $code
	* 返  回：int $localCode
	*/
	public static function getLocalCode($code)
	{
		return isset(self::$localCode[$code]) ? self::$localCode[$code] : self::$localCode[4016];
	}
	/**
	* 方  法：根据原始的错误代码获取用户友好型名称
	* 参  数：int 
	* 返  回：string $name
	*/
	public static function getLocalName($code)
	{
		return isset(self::$localName[$code]) ? self::$localName[$code] : self::$localName[4016];
	}

	// 取得文件名称
	public static function getFilename($filesite)
	{
		return pathinfo($filesite)['basename'];
		// return substr(strrchr($filesite, '/'), 1 );
		return strrchr($filesite, '\\');
	}

}
