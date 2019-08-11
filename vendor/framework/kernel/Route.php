<?php namespace kernel;
/**
 * 系统类：url相关
 * 修改：v1.0
 *
 * 20190228
 */
 
class Route
{

	//取得 url查询字询
	public static function get_queryString()
	{
		return urldecode($_SERVER['QUERY_STRING']);
	}

	
	/*
	 * 禁止危险函数的默认方法
	 * 20190228
	 * 
	 */
	public static function disDfun(array $disFuns=['eval'])
	{
		if(self::hasDFun($disFuns)) Rtn::ALERT('You Are Forbidden!');
	}

	/**
	 * 判断是否有危险函数
	 * 20190228 chy
	 * 说明: 判断get传入的字串中是否含有危险函数
	 * @param disFuns array 要去除的函数名称 如：['eval', 'unlink']
	 * @return bool true:有危险; false:无危险
	 * 示例：
	 	$b = Route::hasDFun(['eval', 'file']);
		var_dump($b);
	 */
	public static function hasDFun(array $disFuns=['eval'], $str='')
	{
		//处理
		foreach($disFuns as &$f){$f="($f\s*\()";}

		//匹配
		$b = preg_match('/'.implode('|', $disFuns).'/i', urldecode($_SERVER['REQUEST_URI']), $matchs);

		//var_dump($b, $matchs);
		return $b;
	}

	/*
	 * 请求类型
	 * return string 可能的值为： "GET", "HEAD"，"POST"，"PUT"
	 * 示例：
		 return \lib\route::get_requestType();
	 * 20190228
	*/
	public static function get_requestType($isLower=false)
	{
		return $isLower ? strtolower($_SERVER['REQUEST_METHOD']) : $_SERVER['REQUEST_METHOD'];
	}



	
}
