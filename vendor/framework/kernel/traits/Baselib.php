<?php namespace Kernel\traits;

/*
 * 控制器基础类－复用代码
 * CM: 20160321
 * LM: 20161007 在构造函数中加入unset，删除构架的参数__dir 和 __prm
 * 20160925 加入act()，用于处理不存在的影响器
 * 20170731 更改命名空间为clib
 * 20190519 多向继承模版类
 * 20190811170709 改为trait，否则"tatic $_mine"在子类中仍然是父类的
 *
*/

trait Baselib
{
	protected static $_mine;

	// 构造函数：注意在lib类中尽量不要传函数
	public function __construct(array $keyVals=[])
	{
		foreach($keyVals as $k => $v)
		{
			$this->$k=$v;
		}
	}


	//单态实例 20190811170839
	public static function mine(array $keyVals=[])
	{
		//var_dump($keyVals);exit;
		if(!static::$_mine) static::$_mine=new static($keyVals);
			
		return static::$_mine;
	}

}