<?php
/**
 * 模型类
 */
class Model{
	public static $link = null; //mysqli对象
	protected $table; //表名
	protected $alias = array(); //字段别名
	private $_opts = array(); //sql语句子条件语句
	private static $sqls = array(); //存储发送过的sql语句
	private $_pk; //主键
	private $_fields;
	

	public function __construct($table = null){
		$this->table = is_null($table) ? C('DB_PREFIX') . $this->table : C('DB_PREFIX') . $table;
		$this->connect();
		$this->_init_opts();
		$this->_get_fields();
	}
	/**
	 * [connect 连接数据库]
	 * @return null
	 */
	public function connect(){
		if(is_null(self::$link)){
			C('DB_DATABASE') OR halt('请先设置数据库');
			$mysqli = new mysqli(C('DB_HOST'), C('DB_USER'), C('DB_PWD'), C('DB_DATABASE'), C('DB_PORT'));
			if($mysqli->connect_error){
				halt('数据库连接错误' . $mysqli->connect_error);
			}
			$mysqli->set_charset(C('DB_CHARSET'));//设置字符集
			self::$link = $mysqli;
		}
	}
	/**
	 * [_opts 初始化或重置sql语句]
	 * @return null
	 */
	private function _init_opts(){
		$this->_opts = array(
			'field'=>'*',
			'where'=>'',
			'group'=>'',
			'having'=>'',
			'order'=>'',
			'limit'=>''
		);
	}
	/**
	 * [query 发送带结果集的查询sql]
	 * @param  [type] $sql [description]
	 * @return array
	 */
	public function query($sql){
		self::$sqls[] = $sql;
		$result = self::$link->query($sql);
		$result OR halt('MYSQL错误：'.self::$link->error."<br/>[SQL: {$sql}]");
		$rows = array();
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		$result->free();
		$this->_init_opts();
		return $rows;
	}
	/**
	 * [_get_fields 获取表的字段名和主键字段名]
	 * @return null
	 */
	private function _get_fields(){
		$result = self::$link->query("DESC ".$this->table);
		$result OR halt('MYSQL错误：'.self::$link->error."<br/>[SQL: {$sql}]");
		$fields = array();
		while ($row = $result->fetch_assoc()) {
			if($row['Key']=='PRI'){
				$this->_pk = $row['Field'];
			}
			$fields[] = $row['Field'];
		}
		$result->free();
		$this->_fields = $fields;
	}
	public function get_sqls(){
		return self::$sqls;
	}
	/**
	 * [find 取一条数据]
	 * @param  [int] $id [主键id]
	 * @return array
	 */
	public function find($id=null){
		if(is_int($id)){
			$this->where("`".$this->_pk."`=".$id);
		}
		$result = $this->limit(1)->all();
		return current($result);
	}
	/**
	 * [one find方法别名]
	 * @param  [int] $id [主键id]
	 * @return array
	 */
	public function one($id=null){
		return $this->find($id);
	}
	/**
	 * [getAll all方法别名]
	 * @return array
	 */
	public function getAll(){
		return $this->all();
	}
	/**
	 * [all 取出所有符合条件的数据]
	 * @return array
	 */
	public function all(){
		$sql = "SELECT ".$this->_opts['field']." FROM `".$this->table."`".$this->_opts['where'].$this->_opts['group'].$this->_opts['having'].$this->_opts['order'].$this->_opts['limit'];
		return $this->query($sql);
	}
	/**
	 * [exe 发送不带结果集的sql]
     * @param [string] $sql []
	 * @return int
	 */
	public function exe($sql){
		//INSERT INTO sw_user (`username`,`password`) VALUES ('waly2','2235235235')
		$link = self::$link;
		$bool = $link->query($sql);
		is_object($bool) && halt('请使用query方法发送查询sql');
		if($bool){
			return $link->insert_id ? $link->insert_id : $link->affected_rows;
		}else{
			halt('MYSQL错误：'.$link->error."<br/>[SQL: ".$sql."]");
		}
	}
	public function field($field){
		if(is_array($field)){
			$field = implode(',', $field);
		}
		$this->_opts['field'] = $field;
		return $this;
	}
	public function where($where){
		if($this->_opts['where']){
			$this->_opts['where'] .= " AND ".$where;
		}else{
			$this->_opts['where'] = " WHERE ".$where;
		}
		return $this;
	}
	public function group($group){
		$this->_opts['group'] = " GROUP BY ".$group;
		return $this;
	}
	public function having($having){
		$this->_opts['having'] = " HAVING ".$having;
		return $this;
	}
	public function order($order){
		$this->_opts['order'] = " ORDER BY ".$order;
		return $this;
	}
	public function limit($limit){
		if(is_int($limit)){
			$limit = " LIMIT 0,".$limit;
		}else{
			$limit = " LIMIT ".$limit;
		}
		$this->_opts['limit'] = $limit;
		return $this;
	}

    /**
     * 字符串安全处理（如果系统开启了转义,反转义之后使用mysqli的字符串安全处理函数）
     * @param $str
     * @return mixed
     */
    private function _safe_str($str){
        if(get_magic_quotes_gpc()){
            $str = stripslashes($str);
        }
        return self::$link->real_escape_string($str);
    }

    /**
     * 添加方法
     * @param array $data
     * @return int|null
     */
    public function add($data = array()){
        //INSERT INTO sw_user (`username`,`password`,`email`) VALUES ('valys','123','123@qq.cc')
        $data = is_null($data) ? $_POST : $data;
        if(!$data){
            return null;
        }
        $field = $value = '';
        foreach($data as $k=>$v){
            if(in_array($k,$this->alias)){
                $k = $this->alias[$k];
            }
            if(in_array($k,$this->_fields)){
                $field .= "`".$this->_safe_str($k)."`,";
                $v = is_int($v) ? $v : "'".$this->_safe_str($v)."'";
                $value .= $v.',';
            }
        }
        $field = trim($field,',');
        $value = trim($value,',');
        $sql = 'INSERT INTO ' . $this->table . ' (' . $field . ') VALUES (' . $value . ')';
        return $this->exe($sql);
    }

    public function update($data = array()){
        //UPDATE sw_user SET username='valys333',password='123',email='123@qq.cc' WHERE id=17
        $data = is_null($data) ? $_POST : $data;
        if(!$data){
            return null;
        }
        $key = $this->_pk;
        if(isset($data[$key])){
            $where = "`{$key}`=".$data[$key];
            $this->where($where);
        }
        if(!$this->_opts['where']){
            halt('update语句必须有where条件');
        }
        $upd_str = '';
        foreach($data as $k=>$v){
            if(in_array($k,$this->alias)){
                $k = $this->alias[$k];
            }
            if(in_array($k,$this->_fields)){
               $upd_str .= "`".$this->_safe_str($k)."`='".$this->_safe_str($v)."',";
            }
        }
        $upd_str = trim($upd_str,',');
        $sql = 'UPDATE '.$this->table.' SET '.$upd_str." ".$this->_opts['where'];
        return $this->exe($sql);
    }
}
?>