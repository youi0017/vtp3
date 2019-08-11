<?php namespace kernel\traits;
/**
 * trait Templete : 模版解析与加载
 * 说明本类只用于控制器基类的多向继承
 * 
 * 
 */
trait Templete
{
	protected $dArr=[];
	
	/*
	 * 模版赋值方法
	 * 
	 * 说明：
	 	$vals数组：要解析的所有变量和值，以索引和值进行映射
	 	$vals字串：此时$vals将作为变量名，$v作为值
	 * 
	 * 20190519
	 */
	public function assign($vals, $v=null)
	{
		if(is_array($vals)){
			$this->dArr = array_merge($this->dArr, $vals);
		}
		elseif(isset($v) && is_string($vals))
		{
			$this->dArr[$vals]=$v;
		}
		
		return $this;
	}

	/*
	 * 解析视图
	 * 完成内容：
	 	1. 导出数组变量
	 	2. 加载视图
	 	
	 	注意：
	 	多次调用 display 会由于 多次爆开数据导致内存升高
	 * 20190519
	 */
	public function display($tpl, $isSysView=false)
	{
		//取回视图路径
		$fsite = self::fsite($tpl, $isSysView);
		if(is_file($fsite)==false)
		{
			throw new \Exception("编译文件 {$tpl} 不存在！");
			exit;
		}

		//导出数组变量，并解析视图
		extract($this->dArr, EXTR_OVERWRITE);
		include $fsite;
	}


	/**
	 * 返回模版文件路径
	 * 20190519
	 */
    public static function fsite($file, $isSysView=false)
    {
		//判断加载：模版文件地址
	    return $isSysView ? FW.'view/'.$file : PJ.APP_TPL.'/'.$file;
    }

}