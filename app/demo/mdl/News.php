<?php namespace mdl;

use \lib\Db4 as dbt;

/**
 * t_qy_news表模型
 * 20190816100134
 * chy 
 *
 * 
 */

class News
{
	// 读取所有信息
	public static function readAll()
	{
		$uid=101;
		$r = dbt::mine()->R('select id,ctime,cusr,tit,src,grp,click from chy_qyweb.t_qy_news where cusr=? and stat>0 order by id desc', [$uid]);

		return $r===false ? [0, '系统错误：'.dbt::mine()->getErr()] : [1, $r];
	}


	// 读取一条
	public static function readOne($nid)
	{
		$r = dbt::mine()->R('select id,ctime,cusr,tit,src,grp,click,cnt from chy_qyweb.t_qy_news where id=? and stat>0 order by id desc', [$nid], 1);

		return $r===false ? [0, '系统错误：'.dbt::mine()->getErr()] : [1, $r];
	}

	// 写入
	public static function insert($data)
	{
		$c = dbt::mine()->I('chy_qyweb.t_qy_news', $data);

		return $c===false ? [0, '系统错误：'.dbt::mine()->getErr()] : [1, dbt::mine()->getLastid()];
	}

	// 更新
	public static function update($data, $key='id')
	{
		$c = dbt::mine()->U('chy_qyweb.t_qy_news', $data, $key);

		return $c===false ? [0, '系统错误：'.dbt::mine()->getErr()] : [1, ''];
	}


	// 删除
	public static function delete(array $ids)
	{
		$c = dbt::mine()->exec('delete from chy_qyweb.t_qy_news where id in('.str_repeat('?',count($ids)).')', $ids);

		return $c===false ? [0, '系统错误：'.dbt::mine()->getErr()] : [1, ''];
	}








}


