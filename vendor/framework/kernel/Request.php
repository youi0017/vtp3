<?php namespace kernel;
/*
 * 取外部数据工具库
 * ver2 修改错误数据返回内容，加入debug
 * 20190524 加入使用verify库对数据的验证
 * 20190601 因修改了Rtn对返回数据的处理，不再区分返回错误的数据类型，并取消了构造函数
 * 使用示例：
 	$request = new request();
 	$uid = request->get('uid', 'int')->min(1)->val();
 	
 */

class Request
{
	use \Kernel\traits\Baselib;
	const debug=true;//调试控制：true开启，false关闭
	
	protected $val;//值
	protected $key;//取值的索引
	protected $eMsg='ok';//错误信息

	protected static $phpinput=false;//php:input传入的数据

/*
	protected $eType;//错误返回类型
	*
	 * 构造函数
	 * $eType [选] string 错误的返回类型：{text:文字型；json:json型；}
	 * 20190524
	 *
	public function __construct($eType='text')
	{
		$this->eType = strtoupper($eType);
	}
*/


	/*
	 * 调用验证库 20190525
	 * 说明：调用\Lib\Verify库进行验证
	 * 示例：
	 	$request = new Request('json');
		$request = $request->get('phone', 'int')->isMoble()->val();
		$request = $request->get('email', 'string')->isEmail()->val();
	 */
	public function __call($validFun, $prms)
	{
		//如果取值错误，则跳过此验证20190530
		if($this->eMsg!='ok')
		{
			return $this;
		}
		
		//判断验证方法是否存在
		$b = method_exists('\\lib\\verify', $validFun);
		if(!$b) Err::ALERT(new \Exception("\\Verify::{$validFun} not exit!"));

		//取值到验证函数
		array_unshift($prms, $this->val);

		//调用验证库
		$b = call_user_func_array(['\\lib\\verify', $validFun], $prms);
		//$emsg = $this->emsg!='ok'

		
		$funZh = \lib\verify::NAMES()[$validFun];
		
		if(!$b) $this->setMsg("“{$funZh}”验证不通过，传入的值为：{$this->val}");
		
		return $this;
	}


	/*
	 * POST方式取数据
	 * $key [必] string 数据索引
	 * $vTyp [选] string 数据约束类型：string,int,float
	 * return $this
	 * 20190726 兼容input
	 */
	public function post($key, $vType='string')
	{
		if(!$_POST) $_POST=json_decode(file_get_contents('php://input'), true);

		$this->query($_POST, $key, $vType, 'POST');
		return $this;
	}



	/*
	 * GET方式取数据
	 * $key [必] string 数据索引
	 * $vTyp [选] string 数据约束类型：string,int,float
	 * return $this
	 */
	public function get($key, $vType='string')
	{
		$this->query($_GET, $key, $vType, 'GET');
		
		return $this;
	}

	//数据返回
	public function val()
	{
		//如未取得值，为前段出错
		if($this->eMsg==='ok') return $this->val;
		//出错时返回错误信息
		Rtn::err($this->eMsg,'',403);

		//$this->eType=='JSON' ? Rtn::err(['msg'=>$this->eMsg]) : Rtn::ALERT(new \Exception($this->eMsg));
	}

	//$request->get('uid',int)->isName()->getErr()->val();
	public function setErr($emsg)
	{
		$this->eMsg=$emsg;
		return $this;
		
	}
	

	/*
	 * 验证最大值
	 * $max [必] numberic 控制的最大值
	 * return $this
	 * 
	 * 示例
	 	//以int型取uid,要求不能大于100,否则返回错误
	   $request = new Request('json');
	 * $request->get('uid', 'int')->max(100)->val();
	 * 
	 */
	public function max($max)
	{
		//如未取得值，为前段出错
		if($this->eMsg==='ok')
		{
			switch($this->vType)
			{
				case 'string':
				{
					if(strlen($this->val)>$max) $this->setMsg("{{$this->val}}不应该大于{$max}位");
					break;
				}
				default:
				{
					if($this->val>$max) $this->setMsg("{{$this->val}}不应该大于{$max}");
				}
			}	
		}

		return $this;
	}

	/*
	 * 验证最大值
	 * $max [必] numberic 控制的最大值
	 * return $this
	 * 
	 * 示例
	 	//以int型取uid,要求不能小于10,否则返回错误
	   $request = new Request('json');
	 * $request->get('uid', 'int')->min(10)->val();
	 * 
	 */
	public function min($min)
	{
		//如未取得值，为前段出错
		if($this->eMsg==='ok')
		{
			switch($this->vType)
			{
				case 'string':
				{
					if(strlen($this->val)<$min) $this->setMsg("{{$this->val}}不应该小于{$min}位");
					break;
				}
				default:
				{
					if($this->val<$min) $this->setMsg("{{$this->val}}不应该小于{$min}");
				}
			}	
		}

		return $this;
	}


	//通用取值方法 20190524
	public function query($data, $key, $vType, $method)
	{
		//还原为null值（必须）
		$this->val=null;
		
		//数据规范化处理
		$data = array_change_key_case($data, CASE_LOWER);
		$this->key = strtolower($key);
		$this->vType = strtolower($vType);

		//设置错误信息
		$setEmsg=function($val) use($method){
			if(self::debug)
				return  "{$method}::{$this->key}=>({$this->vType}){$val} !";
			else
				return '服务器未能取到有效数据';

		};

		//判断是否存在
		if(isset($data[$this->key])==false)
		{
			//var_dump($setEmsg('undefined'));
			$this->setMsg($setEmsg('undefined'));
			return false;
		}

		$val=$data[$this->key];
		//判断类型
		switch ($this->vType)
		{
			case 'string': 
			{
				$this->val = (string)$val;
				break;
			}
			case 'int':
			case 'float':
			{
				if(is_numeric($val)){
					$this->val = $val-0;
					break;
				}

				

				//$this->setMsg($setEmsg($val));
				
			}
			default:
			{
				//var_dump($val);
				$this->setMsg($setEmsg($val));
				return false;
			}
		}

		return true;
	}


	//设置错误 20190524
	protected function setMsg($emsg)
	{
		$this->eMsg='取值错误: '. $emsg;
	}


	/**
     * 取得 或生成 urlPath
     * $path [选] 生成urlPath时的控制器与执行器
     * $params [选] 生成urlPath时的参数信息
     * return string urlPath
     * 
     * 示例一：取当前uri
			echo \kernel\Request::URI();
     	示例二：生成uri
     		echo request::URI(['usr', 'login']);
     	示例二：生成带参数的uri
     		echo request::URI(['usr', 'login'], ['a'=>1, 'x'=>78]);
     * 
     * 20190523 
	 */
	public static function URI(array $path=[], array $params=[])
	{
		if($path==false)
		{
			$path=$_SERVER['PATH_INFO'];
			return $path{-1}=='/' ? substr($path, 0, -1) : $path;
		}
		else
		{
			//取控制与执行器
			$uri='/'.implode('-', array_slice($path, 0, 2));
			//取路径参数
			$pathVal=array_slice($path, 2);
			//var_dump($uri, $pathVal==false);exit;
			if(!empty($pathVal)) $uri.='/'.implode('-', $pathVal);
			//加入后缀
			$uri.='.html';

			$prms='';
			foreach($params as $k=>$v)
			{
				$prms .= '&'.$k.'='.$v;
			}

			return $prms=='' ? $uri : $uri.'?'.substr($prms,1);
		}
	}

}


















