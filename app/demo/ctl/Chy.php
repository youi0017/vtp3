<?php namespace ctl;
use \kernel\Rtn;
use \kernel\Request;
use \lib\DB4 as DB;
use Intervention\Image\ImageManagerStatic as Image;

/*
 * chy是所有模块的综合展示入口
 * 注：如模块有更多应用时，再独立创建控制器展示
 * 20190926160813
 */
class Chy extends \kernel\Control
{
	public function index()
	{
		return '<h1>welcome to chy funs！</h1>';
	}

	//错误的使用 20190619 lm:20190811114918
	public function error()
	{
		/*
		使用错误与异常
		注意：vtp中将错误与异常分为：用户错误 与 系统运行错误
		1. 用户错误：由于用户的输入不符全要求导致的错误，如：404页面，调试输出错误页
		2. 系统运行错误：由于在运行中程序跑偏导致的错误
		*/

		// 手动调用404页
		// \kernel\Rtn::e404();	
		
		//输出一个轻型错误页
		Rtn::alert( new \exception('发出错误') );

		//运行中的错误与异常
		//echo xxx();//调用不存在的函数
		//echo $abc12456;//返回不存在的变量
		//echo 'nihao err!'//语法错误:少分号
		
	}	

	
	/*
	 * 数据传入
	 * 说明：request 与 verify的使用
	 * 示例url: /chy-request?phone=15237154040&uid=255&usr=abc456&pwd=asdf456789
	*/
	public function request()
	{
		// 自实例化调用Request (推荐)
		// Request::mine()->get('mailxxx', 'string')->isEmail()->val();
		// exit;

		// 手动实例化调用request（也可）
		$rq = new Request();
		$mail = $rq->get('mail', 'string')->isEmail()->val();
		var_dump($mail);exit;
		$phone = $rq->get('phone', 'string')->isMobile()->val();
		$uid = $rq->get('uid', 'int')->min(100)->max(1000)->val();
		$usr = $rq->get('usr', 'string')->isUsr()->val();
		$pwd = $rq->get('pwd', 'string')->isPwd()->val();
		var_dump($phone, $uid, $usr, $pwd);exit;			
	}

	//\lib\Curl方法接口
	//20190525
	public function curl()
	{
		//取回神马页面
		echo \lib\curl::mine()->exc('https://m.sm.cn');
	}

	/**
	 * 分页库使用案例 stefangabos/zebra_pagination
	 * chy 20190525
	 */
	public function paging()
	{
		//1.从数据库中取数据
		$pg=['key'=>'page', 'show'=>15];
		$rows = DB::mine()->P('select * from t_myh_lishi where id>? and id<? order by id desc', [1000, 2000], $pg);
		// var_dump($rows, $pg); exit;

		//2.载入分页模块
		vendor('stefangabos-zebra_pagination');
		//页码对象
		$paging = new \Zebra_Pagination();
		//设置总条数
		$paging->records($pg['tt']);
		//设置每页显示条数
		$paging->records_per_page($pg['show']);
		//设置上/下页符号
		$paging->labels('上一页', '下一页');

		//3.赋值数据并加载视图
		$this->assign('rows', $rows);
		$this->assign('paging', $paging);
		$this->display('paging.tpl');
	}


	/**
	 * 图像库使用案例 intervention_image
	 * chy 20190811115307
	 */
	public function img()
	{
		//https://imagine.readthedocs.io/en/stable/usage/effects.html
		vendor('intervention_image');
		
		// 创建画布
		Image::configure(array('driver' => 'imagick'));

		// 画布
		$image = Image::canvas(120, 120, '#0F0');

		// 画圆
		$image->circle(100, 60, 60, function ($draw) {
		    $draw->background('FFF');
		    $draw->border(1, '#000');
		});

		// 模糊
		// $image->blur(3);

		// 旋转
		// $image->rotate(45);

		// 输出图片
		echo $image->response('jpg', 70);
	}


	/**
	 * 邮箱库使用案例（vtp-mail已封装）
	 * chy 20190525
	 */
	public function mail()
	{
		$cmail = new \lib\Mail();
		//var_dump($cmail);exit;
		$b = $cmail->sendMail(['2829281863@qq.com'=>'航一'], '今日天气', '15到27度，4到5级风');
		
		var_dump($b);
	}

	/**
	 * DB库使用案例
	 * chy 20190811152823
	 */
	public function db()
	{
		$r = \lib\db4::mine()->R('select * from t_usrs');
		var_dump($r, \lib\db4::mine()->getErr());
	}

}




