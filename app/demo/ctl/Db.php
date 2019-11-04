<?php namespace ctl;
use \kernel\Rtn;
use \lib\Db4 as dbt;
use \mdl\Lishi as ls;
use \mdl\User as usr;

/*
 * 数据库模块 - 综合使用案例展示
 * chy 20190926161151
 */
class Db extends \kernel\Control
{
	public function index()
	{
		rtn::alert(new \exception(
			'此模块需数据库支持，未配置数据库则本模块所有方法不能用'
		));
	}


	// 使用模型操作数据：模型基本应用
	// chy 20190927111251 
	public function mdlOp()
	{
		// 取得用户模块(usr表)
		$usr = new usr();

		/*
		//取值一： id取值 
		$r = $usr->getRow(9, 'usr,pwd');//getRow 取usr表中usr，pwd字段	

		//取值二： 链式取值
		$r=$usr
			->where('id=? or id=?', [8, 9])
			->order('id desc')
			->limit(1)
			->select('usr, headerimg, creat_at');

		var_dump($r);
		*/
	

		//  
		
		/*
		// 写入 ，并取回生成的数据id
		$id=null;
		$b = $usr->insert(['usr'=>'abc456', 'pwd'=>sha1('123456')], $id);
		var_dump($b, $id, $usr->getErr());
		 */
		
		/*
		// 更新：
		$b = $usr
			->where('id=?', [18])
			->update(['pwd'=>sha1('789456')]);
		var_dump($b, $usr->getErr());
		*/
	

		/*
		// 更新：
		$b = $usr
			->where('id in ()', [8])
			->delete();
		var_dump($b, $usr->getErr());
		*/
	}




	// 读取数据
	public function read()
	{
		$r = dbt::mine()->R('select * from t_myh_lishi where m=? and d=? and stat>0 order by id desc', [date('n'), date('j')]);

		$r===false ? Rtn::err('系统错误：'.dbt::mine()->getErr(), 0, 500) : Rtn::okk('ok', $r);
	}

	public function insert()
	{
		$c = dbt::mine()->I('t_myh_lishi', [
			'tit'=>'vtp3.1.3发布'.date('Y-m-d'),
			'y'=>date('Y'),
			'm'=>date('n'),
			'd'=>date('j'),
			'click'=>rand(10, 100),
		]);


		if($c===false)
		{
			Rtn::err('系统错误：'.dbt::mine()->getErr(), 0, 500);
		}
		else
		{
			// 取最后生成的ID
			$lastid=dbt::mine()->getLastid();
			// 标记lastid
			\lib\Cookie::set('lastid', $lastid, 3600);
			// 显示结果
			// $this->read();
			rtn::okk('数据被[写入]成功！', $lastid);
		}

	}

	// 更新
	public function update()
	{
		// 取回标记的ID
		$lastid = \lib\Cookie::get('lastid');

		if($lastid)
		{
			// 可以执行更新操作
			$c = dbt::mine()->U('t_myh_lishi', [
					'tit'=>'vtp3.1正式发布'.date('Y-m-d'),
					'id'=>$lastid,
				],
				'id'
			);

			if($c===false)
			{
				rtn::err('系统错误：'.dbt::mine()->getErr(), 0, 500);
			}
			elseif($c==0)
			{
				rtn::err('没有数据被更新');
			}
			else
			{
				rtn::okk('数据被[更新]成功！', $lastid);
				//$this->read();
			}

		}
		else
		{
			rtn::err('请先写入一条数据后，再执行更新');
		}

	}


	public function delete()
	{
		// 取回标记的ID
		$lastid = \lib\Cookie::get('lastid');

		if($lastid)
		{
			// 清除lastid
			\lib\Cookie::set('lastid');

			//删除数据
			$c=dbt::mine()->exec('delete from t_myh_lishi where id=?', [$lastid]);
			if($c===false)
			{
				rtn::err('系统错误：'.dbt::mine()->getErr(), 0, 500);
			}
			elseif($c==false)
			{
				rtn::err('没有数据被删除');
			}
			else
			{
				rtn::okk('数据被[删除]成功！', $lastid);
				// $this->read();
			}	
		}
		else
		{
			rtn::err('请先写入一条数据后，再执行删除');
		}
		
	}


	public function cs()
	{
		var_dump($_COOKIE);
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


}





