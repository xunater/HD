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
	 * @return [type] [description]
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
	 * @return [type] [description]
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
	 * @return [type]      [description]
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
	 * @return [type] [description]
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
	 * @param  [type] $id [description]
	 * @return [type]     [description]
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
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function one($id=null){
		return $this->find($id);
	}
	/**
	 * [getAll all方法别名]
	 * @return [type] [description]
	 */
	public function getAll(){
		return $this->all();
	}
	/**
	 * [all 取出所有符合条件的数据]
	 * @return [type] [description]
	 */
	public function all(){
		$sql = "SELECT ".$this->_opts['field']." FROM `".$this->table."`".$this->_opts['where'].$this->_opts['group'].$this->_opts['having'].$this->_opts['order'].$this->_opts['limit'];
		return $this->query($sql);
	}
	/**
	 * [exe 发送不带结果集的sql]
	 * @return [type] [description]
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
}
?>