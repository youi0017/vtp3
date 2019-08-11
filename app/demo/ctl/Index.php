<?php namespace ctl;

use \kernel\Rtn;

class Index extends \kernel\BaseCtl
{
	//视图解析
	public function index()
	{
		//直接返回内容
		return '<h1>欢迎来到Index主页</h1>';
		
		//解析数据视图
		$this->assign(['author'=>'Yuhang Chu', 'creatAt'=>'2013年9月']);
		$this->display('index.tpl');
	}

	//输出与显示
	public function shErr()
	{
		//错误页面输出
		// Rtn::e404();
		// Rtn::e403();
		// Rtn::e500();
		
		//轻型文字·输出
		// Rtn::alert(new \Exception('我的自定义错误输出'));

		// json格式[成功]时的输出
		// Rtn::okk();
		// json格式[失败]时的输出
		Rtn::err('用户名或密码错误', '-1', 401);
	}
	
}




