<?php namespace lib;
/**
 * 系统类：工具集合
 * 修改：v1.0
 * LM: 20170711 加note方法
 *
 * 20170207
 */
 
class Tools
{
	use \Kernel\traits\Baselib;

	private $timeZero;
	private $memoryZero;

	/**
	 * 判断是否是ajax请求
	 * @block bool true:阻止继续
	 * 说明：
	 	1. jQuery和vtp-ajx 发出 ajax 请求时，会在请求头部添加一个名为 X-Requested-With 的信息，信息内容为：XMLHttpRequest,在后端可以使用 $_SERVER['HTTP_X_REQUESTED_WITH'] 来获取。（注意：中划线换成了下划线，不区分大小写）20170207
	 * 
	 * 20170207
	 */
	public static function ajxOn($block=true)
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
			return true;
		}
		else{
			if($block) rtn::err('ERR: 非法请求！');			
			return false;
		}
	}

	/*
	 * 以sig方式验证GET参数
	 * 示例：
		$r=\lib\tools::yzSig();//生成sig
		$r=\lib\tools::yzSig($_GET['sig']);
	 * 20180115
	 */
	public static function yzSig($sig='')
	{
		if($sig=='')
		{
			if(isset($_GET['sig'])) unset($_GET['sig']);
			if(count($_GET)<1) return false;
		}
		else
		{
			//验证时参数:必需有sig，且有其它参数
			if(!isset($_GET['sig']) || count($_GET)<2) return false;
			//取出sig，并删除GET中的sig
			$sig=$_GET['sig'];
			unset($_GET['sig']);
		}

		//对GET升序排序
		ksort($_GET);//var_dump($_GET);

		$r='';//拼接GET字串
		foreach( $_GET as $k => $v ){$r.=urlencode($k).urlencode($v);}
		//var_dump($r);
		
		$r=md5($r);//加密字串
		return $sig ? ($sig==$r) : $r;
	}

	/**
	 * 判断是否是'手机访问'请求
	 * return [真]则为手机访问，[假]则为电脑访问
	 * 20170223
	 */
	public static function isMobile()
	{
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
		$mobile_browser = '0';
		if( preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])) ) $mobile_browser++;
		if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)) $mobile_browser++;
		if(isset($_SERVER['HTTP_X_WAP_PROFILE'])) $mobile_browser++;
		if(isset($_SERVER['HTTP_PROFILE'])) $mobile_browser++;
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array(  
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
			'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
			'wapr','webc','winw','winw','xda','xda-');
	 	if(in_array($mobile_ua, $mobile_agents)) $mobile_browser++;
		  
		if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) $mobile_browser++;
		
		// Pre-final check to reset everything if the user is on Windows  
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) $mobile_browser=0;
		
		// But WP7 is also Windows, with a slightly different characteristic  
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) $mobile_browser++;
		 
		//if($mobile_browser>0) return true;else return false;
		return $mobile_browser>0;
	}

	//取回当前Url 20170927
	public static function getUrl() 
	{
		$protocol = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']=='on') ? 'https://' : 'http://';
		$port=$_SERVER['SERVER_PORT']!= '80'?':'.$_SERVER['SERVER_PORT'] : '';
		return $protocol.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
	}

	
	/**
	 * 记录错误
	 * 示例：
	 	完整输出：\lib\err::note('err undefined xxx', __FUNCTION__, __FILE__, __LINE__);
	 	简单输出：\lib\err::note('echo test string');
	 * 20170711
	 */
	public static function note($fileSite, $txt, $funName='', $fileName='', $line='')
	{
		//生成字串
		$str = date('Y-m-d H:i:s');
		if($funName!='') $str .= ' Func::'.$funName;
		$str .= PHP_EOL.$txt.PHP_EOL;
		if($fileName!='') $str .= 'In File '.$fileName;
		if($line!='') $str .= ' At Line '.$line;
		$str .= PHP_EOL.PHP_EOL;

		$fp = fopen($fileSite, 'a');
		fwrite($fp, $str);
		fclose($fp);
	}

	


	

	//取得当前时间戳的毫秒数 20170716
	public static function getMillisecond()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}


	/**
	 * 定时起点
	 * @return float 毫秒时间戳
	 * 20170716
	 * LM: 20170716
	 */
	public function timeZero()
	{
		return $this->timeZero=self::getMillisecond();
	}


	/**
	 * 定时终点（与timeZero的差值）
	 * @return float 毫秒时间戳
	 * 20170716
	 * 示例：
		\lib\tools::meer()->timeZero();
		$r=file_get_contents($url);
		var_dump($r);
		\lib\tools::meer()->timeBreak(1);
	 * 
	 * LM: 20170716
	 */
	public function timeBreak($echo)
	{
		if(!$this->timeZero)
		{
			$r='<br/><br/>ERR: 请先调用 timeZero() 设置起始点！';
			if($echo) var_dump($r);
		} 
		else
		{
			$now = self::getMillisecond();
			$r=$now-$this->timeZero;
			
			if($echo)
			{
				//echo '<br/><br/>----------------<br/>';
				//var_dump('起始：'.$this->timeZero.' | '.'终止：'.$now);
				var_dump('动行时间(ms)：'.$r);
			}
		}

		$this->timeZero=null;
		return $r;
	}


	/**
	 * 内存统计起点
	 * @return float 内存用量(字节B)
	 * 20170716
	 * LM: 20170716
	 */
	public function mmZero()
	{
		return $this->memoryZero=memory_get_usage();
	}


	/**
	 * 内存统计终点（与memoryZero的差值）
	 * @return float 毫秒时间戳
	 * 20170716
	 * 示例：
		\lib\tools::meer()->mmZero();
		$r=file_get_contents($url);
		var_dump($r);
		\lib\tools::meer()->mmBreak(1);
	 * 
	 * LM: 20170716
	 */
	public function mmBreak($echo)
	{
		if(!$this->memoryZero)
		{
			$r='ERR: 请先调用 mmZero() 设置起始点！';
			if($echo) var_dump($r);
		} 
		else
		{
			$now = memory_get_usage();
			$r=$now-$this->memoryZero;
			
			if($echo)
			{
				//echo '<br/><br/>----------------<br/>';
				//var_dump('超始：'.$this->memoryZero.' | '.'终止：'.$now);
				var_dump('内存占用(字节)：'.$r);
			}
		}

		$this->memoryZero=null;
		return $r;
	}


	
}
