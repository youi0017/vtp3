<?php namespace ctl;
use \kernel\Rtn;
use \kernel\Request;
use \lib\DB4 as DB;
use Intervention\Image\ImageManagerStatic as Image;


class Cs extends \kernel\Control
{
	public function index()
	{
		phpinfo();exit;
		echo $r;
		exit('okkk');
		$r = \kernel\Logger::err('这是一个测试');
		var_dump($r);
	}

	public function cs1()
	{
		$ls = new \mdl\Lishi();
		$r = $ls->where(['id'=>10975])->delete();
		var_dump($r, $ls->getErr());


	}



	public function cs0()
	{
		$ls = new \mdl\Lishi();
		$r = $ls->getRow(10975);
		var_dump($r, $ls->getErr());

		
		exit;
		$md = new \lib\Model();
		// $r = $md->where(['zuohao'=>4, 'banji'=>'web1703'], 'or')->select();
		//$r = $md->where('id>1010 and id<1200')->order('id asc')->limit('20,10')->select('id,tit as title,y,m,d,creat_at');

		$md = new \lib\Model();
		$r = $md
		//->where('id>1010 and id<1200')
		//->where(['stat'=>2])
		->order('m desc')
		//->limit('5')
		->group('m')
		->select('id,m');
		var_dump($r, $md::getErr());
	}	

}




