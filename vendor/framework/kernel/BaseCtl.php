<?php namespace Kernel;
/**
 * 控制器基类
 * CM: 20160321
 * LM: 20161007 在构造函数中加入unset，删除构架的参数__dir 和 __prm
 * 20160925 加入act()，用于处理不存在的影响器
 * 20170731 更改命名空间为clib
 * 20190519 多向继承模版类
 * 20190815145428 删除_index方法，加入_call方法
 *
 */

abstract class BaseCtl
{
	//引入模版类
	use Traits\Templete;
	
	protected $prms;

	public function __construct($prms=null)
	{
		$this->prms = $prms;

		//注：$prms不确定是否会被 dFun函数 注入 20190228
		if(!empty($_GET) || isset($prms)) Route::disDfun();
	}

	//影响重置器: 默认影响器
	public function __call($fname, $prms)
	{
		// var_dump($fname, $prms);
		Rtn::e404('无效执行器 - '.ACT);
	}

}