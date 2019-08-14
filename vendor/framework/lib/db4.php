<?php namespace lib;

/**
 * 数据库的操作类db
 * 说明：完成数据库相关的基础操作
 * ver1.0 初版
 * ver1.1 增加 P()函数 20180713
 * ver1.2 增加 meer\D\D2\I\U\UI函数 20180720
 * ver4 增加 继承于baselib基类 20190523
 * ver5 整体设计修改：统一使用?点位与绑定，但数据依然是二维关联数组
 * 		增加 I1 实现单数据写入
 * 		增加 I2 实现多条数据写入
 *   	加入 getStmtNo 不再单个绑定数据，而是以?数字值的形式在执行语句时绑定
 *   	修改 get_stmt 为 getStmtMix 
 *   	更改 get_err 为 getErr
 * 
 * 在使用dbX时,必需要用 use 引入，如
 	use \Lib\Db4 as DB;
 * 
 */
class Db4
{
	use \Kernel\traits\Baselib;

	private $err='ok';
	private $pdo;
	private $lastId;
	
	//pdo基础设置
	public function __construct()
	{
		try
		{
			$db = json_decode(\DB_CNF, true);
			//1. 连接数据库
			$this->pdo=new \PDO($db['dsn'], $db['usr'], $db['pwd']);
			$this->pdo->exec('set names utf8');
			
			//开启错误，抛出错误
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		catch( \PDOException $err )
		{
			$this->err=$err->getMessage();
		}		
	}

	// 取得最后生成的数据ID
	// 注：insert语句使用
	// 20190712160347
	public function getLastid()
	{
		return $this->lastId;
	}

	/*
	 * 执行操作SQL语句，返回影响条数
	 * @sql [必] string 操作型SQL语句(必需是：update/delete/insert)
	 * @row [选] array 要绑定的数据 
	 * @return int 受操作语句影响的行数: 整数 或 false(sql错误)
	 *
	 * lm: 加入错误判断 20180614
	 */
	public function exec($sql, array $row=[])
	{
		//执行sql语句
		$stmt=$this->getStmtMix($sql, $row);
		//返回影响条数
		return $stmt ? $stmt->rowCount() : false;
	}

	/**
	 * D方法一
	 * $tblName [必] string 表名
	 * $whr [必] string 删除条件,如 "id between 5 and 10" 或 "tit like '%手机%'" 或 "id=?" 或 "id=:id and stat=:stat"
	 * $row [选] 要绑定的一维数组，没有要绑定的数据则留空
	 * 
		DB::MINE()->D('t_qy_dgtbl', 'id>1000 and stat=0');
		DB::MINE()->D('t_qy_dgtbl', 'id=:id and grp=:grp', ['id'=>9, 'grp'=>14]);
		DB::MINE()->D('t_qy_dgtbl', 'id in (?,?,?)', [101,109,138]);
	 * 
	 * 20180720
	 */
	public function D($tblName, string $whr, array $row=[])
	{		
		$sql="delete from {$tblName} where {$whr};";

		//执行sql语句
		$stmt=$this->getStmtMix($sql, $row);
		//返回影响条数
		return $stmt ? $stmt->rowCount() : false;
	}


	/**
	 * I方法：插入数据方法
	 * $tblName [必] string 表名
	 * $array [必] array 待插入的数据（一/二维关联数组）
	 * return false/int flase有错误，int受操作影响行数 
	 * 
	 	示例：
		$instance->I('t_qy_dgtbl', [
			'tit'=>'I insert tit',
			'cnt'=>'I insert cnt',
			'grp'=>4
		]);

	 */
	public function I($tblName, array $arr)
	{
		//1. 执行sql返回结果集
		$ifun=is_array(reset($arr)) ? 'I2' : 'I1';
		$stmt = $this->$ifun($tblName, $arr);

		//4. 返回结果
		if($stmt)
		{
			//如果ID不是自增，将不能取得lastInsertId 20190425
			$this->lastId = $this->pdo->lastInsertId();
			return $stmt->rowCount();	
		}
			
		return false;	 
	}


	/**
	 * U方法
	 * $param tblName string 表名
	 * $param row array 待更新的一维数组数据
	 * $pk tblName string 主键名称
	 *
	 * 注： U方法只适用于一条数据的更新
	 * 示例：
	 * 	$row = ['id'=>2, 'city'=>'开封', 'age'=>22];
	 *  $c = \lib\db4::mine()->U('t_user_0', $row, 'id');
	 *  var_dump($c, \lib\db4::mine()->getErr());
	 *  
	 */
	public function U($tblName, array $row, $pk='id')
	{
		if(!isset($row[$pk]))
		{
			$this->err='更新数据中未定义主键，请检查数据！';
			return false;
		}

		// 取出索引
		$pk="$pk='{$row[$pk]}'";
		unset($row[$pk]);

		// 取出set字段和值
		$d=['sets'=>[], 'v'=>[]];
		foreach ($row as $k => $v) {
			$d['sets'][]="`$k`=?";
			$d['v'][]=$v;
		}

		// 生成sql语句
		$sql='update '.$tblName.' set '.implode(', ', $d['sets']).' where '.$pk;

		//执行sql语句
		$stmt=$this->getStmtNo($sql, $d['v']);
		//返回影响条数
		return $stmt ? $stmt->rowCount() : false;
	}

	/**
	 * IU方法
	 * @param tblName string 表名
	 * @param dArr array 待更新/插入的一维数组数据
	 * @pk tblName string 主键名称
	 * 说明: 如果 pkName在数据中则更新，不在则插入
	 * 
	 	示例：
		$arr=['id'=>'2', 'tit'=>'第2行标题', 'grp'=>1];
		$c=db::U('t_qy_dgtbl', $arr, 'id');
		$c>0 ? rtn::okk() : rtn::err();
		exit;
	 */

	public function IU($tblName, array $row, $pk='id')
	{
		if(empty($row[$pk]))
		{
			return $this->I($tblName, $row);
		}
		else
		{
			return $this->U($tblName, $row, $pk);
		}
		
	}



	/**
	 * 取得sql的查询结果
	 * $sql string [必] sql语句
	 * $row array [选] 作为sql语句的数据
	 * retrurn array|false 如果是false，则sql语句错误，如是空数组则代表未查到任何数据
	 * 
	 * 20180601 chy
	 * lm: 加入 错误判断 20180614
	 * lm: 加入 单值的输出 20180715
	 * 注：二维时未查到返回空数组，其它返回false，所以对结果的判断用empty
	 	20181213 一、二维时未查到返回空数组，单值没找到返回空字串，语句错误返回false
	 */
	public function R($sql, array $row=[], $fetchType=2, $rType='array')
	{
		//执行sql语句
		$stmt=$this->getStmtMix($sql, $row);
		if(!$stmt) return false;
		//var_dump($stmt, $stmt->fetchAll(\PDO::FETCH_OBJ));
		
		switch($fetchType)
		{
			//返回 二维数组
			case 2:
				return $stmt->fetchAll($this->get_rtnType($rType))?:[];
			//返回 一维数组
			case 1:
				return $stmt->fetch($this->get_rtnType($rType))?:[];
			//返回 单值
			default:
				return $stmt->fetchColumn()?:'';
		}

	}

	
	/*
	 * 取得分页数据
	 * $sql [必] 查询语句 
	 * $row [选] 要绑定的数据
	 * $pgInf [选] 页码相关的数据，以引用方式存储
	 * 
	 * 
		使用示例一：
		$page=[];//存储页码数据
		$rows=DB::MINE()->P($sql, [], $page);//取出分页数据
		var_dump($page, $rows);//测试输出 分页数据 和 页码
		使用示例二：
		//控制每页显示10条
		$arr=['rows'=>[],'page'=>['show'=>10]];
		$arr['rows'] = DB::MINE()->P($sql, [], $arr['page']);
		var_dump($arr);
	*/
	public function P($sql, $row=[], &$pgInf=[], $rType='array')
	{
		//显示条数(传入优先，配置其次)
		if(isset($_GET['sn']) && $_GET['sn']>0)
			$pgInf['show']=$_GET['sn'];
		else
			$pgInf['show'] = isset($pgInf['show']) ? $pgInf['show'] : 5;
		//强制显示条数为整数
		$pgInf['show']=(int)$pgInf['show'];

		//url中控制页码的参数名称，默认为pn
		$key = isset($pgInf['key']) ? $pgInf['key'] : 'pn';
		
		//当前页码
		$pgInf[$key] = isset($_GET[$key]) && $_GET[$key]>1 ? $_GET[$key] : 1;

		//总页数
		$sql_t=preg_replace('/^select .* from/i', 'select count(*) as t from', $sql);
		$pgInf['total']=(int)$this->R($sql_t, $row, 0);//总条数
		//总页数= 向上取整(总条数/显示条数) 
		$pgInf['tp'] = ceil($pgInf['total']/$pgInf['show']);//总页数
		
		//启始条数 = (页数-1)*显示条数
		$limit=($pgInf[$key]-1)*$pgInf['show'];
		$limit=" limit {$limit}, {$pgInf['show']}";

		return $this->R($sql.$limit, $row, 2, $rType);
	}

	//返回错误信息 20180614
	public function getErr()
	{
		return $this->err;
	}


	// 生成多行插入sql语句 20190712110510
	private function I2($tbl, array $arr)
	{
		//1.取出基础数据
		// k:索引, v:值, seats:?占位符
		$d = ['v'=>[], 'seats'=>[]];
		// 1.1 生成索引
		$d['k'] = array_keys(reset($arr));
		// 1.2 取出二维值生成占位符与对应值（一维值数组）
		foreach($arr as $row) {
			$_seats=[];
			foreach ($row as $v) {
				$_seats[]='?'; 
				$d['v'][]=$v;
			}

			$d['seats'][]='('.implode(',', $_seats).')';
		}


		//2. 生成预处理语句
		$sql = 'insert into '.$tbl.' ('.implode(',', $d['k']).") values ".implode(',', $d['seats']).';';

		//3. 执行并返回结果集
		return $this->getStmtNo($sql, $d['v']);
	}

	// 生成一行插入sql语句 20190712110510
	private function I1($tbl, array $row)
	{
		//1.取出基础数据
		// k:索引, v:值, seats:?占位符
		$d = ['k'=>[], 'v'=>[], 'seats'=>[]];
		foreach($row as $k =>$v)
		{
			$d['k'][]=$k;
			$d['v'][]=$v;
			$d['seats'][]='?';
		}
	
		//2. 生成预处理语句
		$sql = "insert into {$tbl} (".implode(',', $d['k']).") values (".implode(',', $d['seats']).");";

		//3. 执行并返回结果集
		return $this->getStmtNo($sql, $d['v']);
	}


	/*
	 * 执行sql语句，返回pdo::sth对象
	 * $row [必] array 要绑定的数据，一维数组: 索引 与 关联均可
	 * return PDOStatment结果集对象
	 * 20190712
	 */
	private function getStmtMix($sql, array $row)
	{
		if(!$this->pdo) return false;
		
		try
		{
			//预执行sql语句
			$stmt=$this->pdo->prepare($sql);

			//绑定数据[]
			if(!empty($row))
			{
				//数值型数组
				if( isset($row[0]) && $row[0]===reset($row) )
				{
					$i=0;
					foreach( $row as $val)
					{
						$stmt->bindValue(++$i, $val);
					}
				}
				//关联型数组
				else
				{
					foreach( $row as $key => $val)
					{
						$stmt->bindValue(':'.$key, $val);
					}
				}
			}

			//执行
			$stmt->execute();

			//返回sth对象
			return $stmt;
			
		}
		catch( \PDOException $err )
		{
			$this->err=$err->getMessage();
			return false;
		}
		
	}


	/*
	 * 执行sql语句，返回pdo::sth对象
	 * $row [必] array 要绑定的数据，必需为一维数值索引数组
	 * return PDOStatment结果集对象
	 * 20190712141809
	 */
	private function getStmtNo($sql, array $row)
	{
		if(!$this->pdo) return false;
		
		try
		{
			//预执行sql语句
			$stmt=$this->pdo->prepare($sql);

			//执行
			$stmt->execute($row);

			//返回sth对象
			return $stmt;
			
		}
		catch( \PDOException $err )
		{
			$this->err=$err->getMessage();
			return false;
		}
		
	}

	/**
	 * 设置结果类型
	 * 20170715
	 */
	private function get_rtnType($rType='object')
	{
		switch($rType)
		{
			case 'object': return \PDO::FETCH_OBJ;
			case 'array': return \PDO::FETCH_ASSOC;
			case 'both': return \PDO::FETCH_BOTH;
			default: return \PDO::FETCH_OBJ;
		}
	}


	/*
	 * 取得转换后的字串 20180720
	 	示例：
		$arr=['id'=>13, 'tit'=>'第13行的标题', 'grp'=>2];
		$r=DB::get_cvt_string($arr);
		var_dump($r);
	 */
	public static function get_cvt_string(array $arr, $mk=',')
	{
		$r=[];
		
		foreach( $arr as $k => $v )
		{
			$r[]=" `$k`=:$k ";
		}

		return implode($mk, $r);		
	}

}


