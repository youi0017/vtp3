<?php namespace Kernel;

/*
 * 控制器基类
 * CM: 20160321
 * LM: 20161007 在构造函数中加入unset，删除构架的参数__dir 和 __prm
 * 20160925 加入act()，用于处理不存在的影响器
 * 20170731 更改命名空间为clib
 * 20190519 多向继承模版类
 *
*/

class BaseLib
{
	protected static $_mine;

	public function __construct(array $keyVals=[])
	{
		foreach($keyVals as $k => $v)
		{
			$this->$k=$v;
		}
	}


	//自实例化 20190521
	public static function mine(array $keyVals=[])
	{
		//var_dump($keyVals);exit;
		if(!isset(static::$_mine)) static::$_mine=new static($keyVals);
			
		return static::$_mine;
	}

}