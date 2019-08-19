<?php namespace kernel;

/**
 * 数据库链式调用模型
 * 说明：此类是模型基类，所有模型必需继承此类
 * 方法：
    class childModel extends \kernel\Model
    {
        protected $table='tableName';
        
    }  
 * 
 * 示例：
 * 
 * $r = $md->where('id>1010 and id<1200')
        ->where(['stat'=>2])
        ->order('id asc')
        ->limit('30')
        ->select('id,tit as title,y,m,d,creat_at,stat');
        var_dump($r, $md::getErr());
 *
 * 20190816172903
 */
class Model
{
	protected $table='undefined';//当前表，继承时必需被覆盖 undefined
    protected $where = 'WHERE 1';//where条件
    protected $order = '';//order排序
    protected $limit = 'LIMIT 500';//limit限定查询,默认500条
    protected $group = '';//group分组

    protected $dArr=[];//与占位符对应的数据
    protected $err='ok';

    public function __construct()
    {
        if($this->table=='undefined') \lib\Rtn::err('模型名称未定义！');
    }


    //DB的引入
    public static function db()
    {
        return \lib\Db4::mine();
    }

    // 设置为当前表 20190816165218
    // 注意此项非必需，默认使用 $this->$table
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }


    //设置数据类型：默认数组 20190816164923
    //注意：此项非必需,默认由R或P方法控制
    public function dtype($type='array')
    {  
        $this->type=$type;
        return $this;
    }


    /*
     * 通过ID取得行数据 
     * 
     * 示例 
     * $md = new \lib\Model();
     * $r = $md->getRow(3);
     * var_dump($r, $md::getErr());
     * 
     * 20190816165444
     */
    public function getRow($id, $fields='*')
    {
    	$this->where(['id'=>$id]);
    	return self::db()->R("SELECT {$fields} from {$this->table} {$this->where}", $this->dArr, 1);
    }


    /**
     * 实现查询操作
     * 在已有子查询的基础上返回结果
     *
     * $md = new \lib\Model();
        $r = $md->where('id>1010 and id<1200')
            ->where(['stat'=>2])//复加条件
            ->order('id asc')
            ->limit('5')
            ->select('id,tit as title,y,m,d,creat_at,stat');
        var_dump($r, $md::getErr());
     * 
     * 20190816165620
     */
    public function select($fields="*")
    {
        // var_dump("SELECT {$fields} FROM {$this->table} {$this->where} {$this->group} {$this->order} {$this->limit}", $this->dArr);
        return self::db()->R("SELECT {$fields} FROM {$this->table} {$this->where} {$this->group} {$this->order} {$this->limit}", $this->dArr);
    }
 

    /**
     * order排序
     * @order string 排序字串
     * 
     * 示例：
     * self::order('id desc,ord asc');
     * 
     * 20190816164737
     */
    public function order(string $order='')
    {
        $this->order = $order ?  ' ORDER BY '.$order : '';
        return $this;
    }


    /**
     * 设置条件子句：字串条件
     * 注： 不直接使用，仅供where调用的方法
     * $where [选] string 以？占位的条件字串,如： '(grade=? and city=?) or stat>?'
     * $data [选] array 索引数组,如对应上面的数据 [1801, 'zhengzhou', 2]
     * 20190816170339
     */
    protected function whereStr(string $where='', $data=[])
    {
		$this->dArr = array_merge($data, $this->dArr);
		$this->where .= ' AND '.$where; 
    }


    /*
     * 设置条件子句：数组条件
     * 注意： 不直接使用，仅供where调用的方法
     * 注意：$mk推荐用and，可能导致错误
     * 
     * $where array 条件数组,如：[id=>15, stat=>1, grp=2]
     * $mk string 条件的连接符, 如 and，则上面各条件以and相连
     * 
     * 20190816170336
     */
    protected function whereArr(array $where=[], $mk='and')
    {
    	//将条件数据合并到数据: 注意顺序不可错
        $this->dArr = array_merge($this->dArr, array_values($where));

        $whr=[];
        foreach ($where as $key => $value) {
            $whr[] = "{$key}=?";
        }

        $where = implode(" {$mk} ", $whr);

        $this->where .= ($this->where=='WHERE 1' ? ' AND ':" {$mk} ")."({$where})";
    }
 

    /**
     * where条件
     * 默认传入的字串条件
     * 20190816153504
     * 
     	示例一：字串型
     	$md = new \lib\Model();
		$r = $md->where('id<? and zuohao<=?', [10,2])->select();
		var_dump($r, $md::getErr());
		
		示例二：数组型
		$md = new \lib\Model();
		$r = $md->where(['zuohao'=>4, 'banji'=>'web1703'], 'or')->select();
		//或 $r = $md->where(['zuohao'=>4], 'or')->where(['banji'=>'web1703'], 'or')->select();
		var_dump($r, $md::getErr());
     */
    public function where($where='', $mkOrData=[])
    {
    	if($where)
    	{
    		if(is_array($where))
    		{
    			$mkOrData = empty($mkOrData) ? 'and' : $mkOrData;
				$this->whereArr($where, $mkOrData);
    		}
    		else
    		{   			
    			$this->whereStr($where, $mkOrData);
    		}

        	return $this;
    	}
        
    }

 
    /**
     * 设置group分组
     * 20190816170708
     */
    public function group($group='')
    {
        if($group)
        {
            if(is_array($group)) $group = implode(',',$group);
            $this->group = ' GROUP BY '.$group;
        }
        
        return $this;
    }
 

    /**
     * 设置limit限定
     * 20190816170733
     */
    public function limit($limit='')
    {
        if($limit) $this->limit = " LIMIT ".$limit;
        return $this;
    }
 

    /**
     * 插入数据
     * $data [必] array 要插入的数据(必需是一维)
     * $lastid [选] int 引用返回插入数据的ID
     *
     * 示例
        $ls = new \mdl\Lishi();
        $id=0;
        $r = $ls->insert([
                'tit'=>'new mdl creat',
                'y'=>2019,
                'm'=>8,
                'd'=>16,
            ], $id);

        var_dump($r, $id, $ls->getErr());
     * 
     * 20190816171736
     */
    public function insert(array $dArr, &$lastid=0)
    {
    	$c = self::db()->I($this->table, $dArr);
        $lastid = self::db()->getLastid();
    	return $c;
    }

 
    /**
     * 更新数据
     * $dArr [必] array 要更新的数据
     * 注意：必需指定where条件
     *
        $ls = new \mdl\Lishi();
        $r = $ls->where(['id'=>10975])
                ->update([
                    'tit'=>'new mdl creat5555',
                    'stat'=>0,
                ]);

        var_dump($r, $ls->getErr());
     *
     * 
     * 20190816172208
     */
    public function update(array $dArr)
    {
        if($this->where=='WHERE 1'){
            $this->err='必需先设置where后再执行update';
            return false;
        }

        $arr=['sets'=>[], 'data'=>[]];

    	foreach ($dArr as $k => $v) {
    		$arr['sets'][]="$k=?";
    		$arr['data'][]=$v;    		
    	}

    	$sets = implode(',', $arr['sets']);
        //注意：此处$data必需在前
    	$this->dArr=array_merge($arr['data'], $this->dArr);

    	$c = self::db()->exec("update {$this->table} set {$sets} {$this->where}", $this->dArr);
    	return $c;
    }
 
    /**
     * 删除数据
     * 注意：必需指定where条件
     *
        $ls = new \mdl\Lishi();
        $r = $ls->where(['id'=>10975])->delete();
        var_dump($r, $ls->getErr());
     * 
     * 20190816172215
     */
    public function delete()
    {
        if($this->where=='WHERE 1'){
            $this->err='必需先设置where后再执行delete';
            return false;
        }
        $c = self::db()->exec("DELETE FROM {$this->table} {$this->where}", $this->dArr);
        return $c;
    }

    // 取得sql错误信息
    // 20190816172602
    public function getErr()
    {
        return $this->err!='ok' ? $this->err : self::db()->getErr();
    }

}
