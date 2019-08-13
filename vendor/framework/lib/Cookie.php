<?php namespace lib;

/*
示例：取回一个cookie值：
	方法一：
		$ck = new \Lib\Cookie(['ckey'=>'uid']);
		$ck->cGet();
	方法二：
		Lib\Cookie::mine(['ckey'=>'uid'])->cGet();
*/
class Cookie
{
	use \Kernel\traits\Baselib;

	protected $ckey;//cookie名称

	//注意：HHK建议修改，任意值15位以下的字串
	public const HHk='vtpsesskey';

	// 生成会话ck的套嵌key 20190802
	public static function CKKEY()
	{
		$ckey = self::GET(self::HHk);
		if(!$ckey)
		{
			$ckey = self::SET(self::HHk, substr(base64_encode(time()), 0, rand(7,9)));
		}

		return $ckey;
	}

	//通过索引取cookie值 20180725
	public static function GET($ckey)
	{
		return isset($_COOKIE[$ckey]) ? $_COOKIE[$ckey] : false;
	}

	/**
	 * 设置cookie值 20180725
	 * $ckey [必] string cookie名 
	 * $value [选] string cookie值，默认空表删除ck, 其它为值
	 * $time [选] int 有效期，默认：time=0表示关闭浏览器失败, 其它为有效秒数
	 * 
	 * 示例：
	 * 设置关闭浏览器失效：self::SET('uid', '1005');
	 * 设置1小时有效：self::SET('uid', '1005', 3600);
	 * 删除：self::SET('uid');
	 * 
	 */
	public static function SET($ckey, string $value='', int $time=0)
	{
		$time = $time==0 ? null : time()+$time;
		setcookie($ckey, $value, $time, '/');
		return $value;
	}

	//删除cookie 20190522
	public static function DEL($ckey)
	{
		setcookie($ckey, null, -100, '/');
	}
	

	//cookie取值
	public function cGet()
	{
		return self::GET($this->ckey);
	}

	/*
	 * cookie的设置/更新/删除
	 * @value string [选] cookie的值 
	 * @time int [选] 有效的时间(秒数)
	 * @return bool true||false
	 * 
	 * 示例：
		设置：cSet(5); 或 cSet(5, 3600);
		更新：cSet(6); 或 cSet(6, 3600);
		删除：cSet();
	 * 20180606
	 * LM:20180615 $time由时间戳更改为有效的秒数
	*/
	public function cSet($value='', $time=0)
	{
		return self::SET($this->ckey, $value, $time);
	}

	//删除cookie
	public function cDel()
	{
		return self::DEL($this->ckey);
	}



	//标记会话
	//$val 没有设置，值为time()； 有设置，则使传入的值
	//return 返回会话的值
	/*
	 * 标记会话
	 * $val mix [选] 会话值
	 * $time int [选] 会话时间,默认10分钟有效
	 * $hhk mix [选] 会话名称，为空
	 * return 会话的值
	 */
	public static function MK($val=null, $hhk=null, $time=600)
	{
		if(!$hhk) $hhk=self::CKKEY();
		if($val===null) $val=time();
		return self::SET($hhk, $val, $time);
	}


	//
	/*
	 * 验证会话
	 * 说明：验证会话是通过传入的会话值与标记的会话值进行比较
	 * $val [必] string 会话值
	 * $hhk [选] string 会话名
	 * 
	 */
	public static function YZ($val, $hhk='')
	{
		if($hhk=='') $hhk=self::CKKEY();
		$cVal = self::GET($hhk);

		//验证会话
		//会话值$cVal  与 传入值$val 的比较
		if($cVal===false){
			$b=false;
		}
		else{
			$b = $cVal==$val;
		}

		//清除
		self::DEL($hhk);
		//返回验证结果
		return $b;
	}


	

}


