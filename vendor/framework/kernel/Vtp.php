<?php namespace kernel;
/**
 * LM: 20160923
 * VIT核心类
 * 实现 URL分发, CTL控制器定向
 *
 * 更新记录
 * V1.0 2015-04-27 修复当uri最后为"/"时返回参数为空字串的bug，后续直接用isset对参数进行判断
 * V1.0 2015-06-05 增加对CTL控制器的判断，防止将核心库读成控制器以报错
 * 	
 * V2.0 2015-08-10
 *  增加调试模式，优化URL处理方式，并引入新的静态变量
 * 	2015-09-11 重新对"调试"和"定向"模式逻辑进行处理，优化处理方式。并加入对url末端的"?"后内容进行去除，防止出现"控制器或方法"不存在的错误
 * 	2015-09-30 更改APP的APP_CTL(控制器目录)后，控制器名称也会跟着变化，因此"引入APP控制器文件中间名 APP_CTL_MID"
 * 	2015-10-15 当控制方法不存在时，调用默认控制器index
 * 	2015-12-04 取得REQUEST_URI时，直接进行urldecode解码，防止中文参数被编码
 * 	2015-12-07 $this->params由初始设置为true，修改为array()。即参数没有时为空数组，用empty对其进行判断
 * 	
 * V3.0 2016-01-28 
 * 	3.1 为更好的优化url，减短url长度，并对uri的参数优化设置，重新构架对url的解析
 		url为三段：/ctl-act/prm1-prm2-prm3/pg_nmb.html
 * 	3.2 对控制器引入基类ctl.lib.php 20160322
 		默认影响器原先为index，但新的url中不用再有响应器地址段，且在index控制器中,响应index替代了构造函数，导致解析时直接执行了响应，解决此bug，将默认act名称更改为dft 20160322
 		增加url的健壮性，对址进行小写转化 20160323
 * 	3.3 对url解析逻辑进行梳理，参数段 和 页码段 明确区别开来 20160401
 * 
 * V4.0 20160923
 	4.1 exc改为静态方法；
 	4.2 更改uri的解析方式，由Apache服务器重写对参数进行分派，由htaccess设定（不再exc中进行解析）；
 	4.3 不再控制重写模式，由url自行控制；
 	4.4 页码不再单独设定，而由GET参数控制。
 	4.5 将不存在的响应统一定位到\inc\ctl::act()中，可以像钩子一样重置act。20161001
 	4.6 $prm由[]变更为null。20161002
 	4.7 加入权限控制。 20170729
 * 	
 * V5.0 20181227
 	1. url使用PATH_INFO取得并解析，不再用get
 	2. GET参数默认传入执行器（注：不对应型参）
 	3. 控制器使用大驼峰命名，默认控制器Index，默认执行器index
 * 	
 * 说明
 * 1. "定向模式"为url优化模式，为SEO设计，需了解apache的重定向
 * 2. "中继模式"为调试开发模式，使用简单
 * 3. 两种模式都可：既不影响URI参数，又可使用传统的get传参法
 * 
 * 	20190813145303 过滤uri中后缀名
 */
//header("Content-type: text/html; charset=utf-8");  
class Vtp
{

    /**
     * URL分发与定位：处理并取得url参数，并初始化 控制器
     * 
     	使用重写的URL,如：
			/adm-index/aa-bb-cc.html?sn=5&pn=3
	    不使用重写的URL,如：
			/index.php?__dir=adm-index&__prm=aa-bb-cc&sn=5&pn=3
		
		说明:
		1. 常规uri由2段'/'组成： 第一段("控制-影响")，第二段("参数1-参数2-参数x")
		2. 其它参数以 GET参数 传递，如：页码pn，显示条数sn
		3. URL后缀名为".html" 或 留空。
     *
     * 20160923
     */
    public static function exc()
    {
		//默认定义
		$ctl='Index';//默认控制器，用于主页
		$act='index';//默认响应
		$prm=null;//参数为空 20161002
		$pn=(isset($_GET['pn'])&&$_GET['pn']>1) ? floor($_GET['pn']) : 1;//页码

		// var_dump($_SERVER['PATH_INFO']);exit;
		if(isset($_SERVER['PATH_INFO']))
		{
			$all = $_SERVER['PATH_INFO'];
			//去除后缀名
			$all = str_replace(strrchr($all, '.'),'',$all); 
			//过滤空段
			$all = array_values(array_filter(explode('/', $all)));
			// var_dump($all);exit;
			
			//控制器与影响器
			if(isset($all[0]))
			{
				$dir = array_filter(explode('-', $all[0]));
				if(!empty($dir[0])) $ctl=$dir[0];
				if(!empty($dir[1])) $act=$dir[1];
				unset($dir);
			}

			//参数
			if(isset($all[1]))
			{
				//说明：过滤参数中的无效内容，如"--aa-"打断后为['','','aa','']，但参数的空值将被过滤，而索引不变($[2]=aa, $[0]是不存在的)，而后面对参数的判断直接使用isset($[0])即可(如不过滤，则还必需判断值是否为空)
				$prm = array_filter(explode('-', $all[1]), function($v)
				{
					return $v!='';
				});

			}
		}

		//定义全局参数
		define('CTL', $ctl);//
		define('ACT', $act);//		

		// var_dump($_GET, $ctl, $act, $prm, $pn);exit;
		// \cls\auth::exc();//用在所用的控制器内20180127


		//执行器执行响应(动作)
		$ctl = '\\ctl\\'.str_replace(',', '\\', $ctl);
		
		if(cls_file_exists($ctl)==false)
		{
			Rtn::E404('“404 无效控制器 - '.CTL.'”');
		}

		//将执行结果返回
		echo call_user_func_array([new $ctl($prm), $act], $_GET);		
	}

}
