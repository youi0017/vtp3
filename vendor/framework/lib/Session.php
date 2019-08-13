<?php namespace lib;

/* 
 * 会话session
 * 20180606
 */
class Session
{
	use \Kernel\traits\Baselib;
	
	protected $skey;
	
	//取得session_id 20180725
	public static function SID()
	{
		if(!isset($_SESSION)) session_start();
		return session_id();
	}

	//摧毁,清空session,慎用 20180725
	public static function DES()
	{
		self::SID();
		session_destroy();
	}

	//取值 20180725
	//session::GET('uid');
	public static function GET($key)
	{
		self::SID();
		return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
	}

	//设置 20180725
	//session::SET('uid', 'chy2019');
	public static function SET($key, $val)
	{
		self::SID();
		$_SESSION[$key]=$val;
		return $val;
	}

	//删除 20180725
	//session::DEL('uid');
	public static function DEL($key)
	{
		self::SID();
		//if(isset($_SESSION[$key]))
		unset($_SESSION[$key]);
	}
	

	//设置（创建+更新）
	//\lib\session::mine(['skey'=>'uid'])->sset('abc')->sget();
	public function sset($value)
	{
		$_SESSION[$this->skey]=$value;
		return $this;
	}

	//取值
	//\lib\session::mine(['skey'=>'uid'])->sset('abc')->sget();
	public function sget()
	{
		return isset($_SESSION[ $this->skey ]) ? $_SESSION[ $this->skey ] : false;
	}

	//删除
	//\lib\session::mine(['skey'=>'uid'])->sdel();
	public function sdel()
	{
		unset($_SESSION[ $this->skey ]);
		return $this;
	}

	
	
}
