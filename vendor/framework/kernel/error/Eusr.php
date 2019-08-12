<?php namespace kernel\error;
use \kernel\Rtn;
/*
 * 开发者 错误类
 * 注：为减轻系统压力，此类暂留，但不使用 20190812163109
 */

class Eusr extends \Exception
{
	//ecode为类型不同用ErrorException::code，emsg为信息
	protected $code,$message;
	public function __construct($ecode=404, $emsg='Not Found!')
	{

		//父方法
		parent::__construct($emsg, $ecode);
	}

	//抛出异常
	public static function CAST($ecode=404, $emsg='Not Found!')
	{
		throw new self($ecode, $emsg);
		// VtpErr::displayEusr(new Eusr($ecode, $emsg));
		
		// $expt = new self($ecode, $emsg);
		// var_dump($expt);exit;
	}
	





}
	