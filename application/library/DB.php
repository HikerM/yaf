<?php

class DB{
	protected $host = 'localhost';
	protected $username = 'root';
	protected $pwd = 'root';
	protected $base = 'dd_test';
	protected $link = null;
	//表前缀
	protected $pre = 'dt_';


	/**
	 * @param  数据库初始化
	 */
	public function __construct(){

		@$this->link = mysqli_connect($this->host,$this->username,$this->pwd);
		if(mysqli_connect_errno()){
			die('连接数据库失败：'.mysqli_connect_error());
		}

		$this->link->select_db($this->base);
		$this->link->set_charset('utf8');


	}

	/**
	 * 查询单条数据
	 * @param  table   表名
	 * @param  where   where条件(不包括where关键字)
	 * @param  field   显示的字段
	 * @return data    处理好的数组数据
	 */
	public function sel_once($table,$where = null,$field = '*'){
		$table = $this->pre.$table;
		$sql = "SELECT {$field} FROM {$table} {$where}";
		$res = $this->query($sql);
		$data = $this->assoc($res,true);
		return $data;
	}

	/**
	 * 查询多条数据
	 * @param  table   表名
	 * @param  where   where条件(不包括where关键字)
	 * @param  field   显示的字段
	 * @return data    处理好的数组数据
	 */
	public function sel_all($table,$where = null,$field = '*'){
		$table = $this->pre.$table;
		$sql = "SELECT {$field} FROM {$table} {$where}";

		// 执行sql语句
		$res = $this->query($sql);
		$data = $this->assoc($res);
		return $data;
	}
	/**
	 * 跨库查询
	 * @param  [type] $table [description]
	 * @param  [type] $where [description]
	 * @param  string $field [description]
	 * @return [type]        [description]
	 */
	public function db_sel($table,$where = null,$field = '*'){
		$sql = $sql = "SELECT {$field} FROM {$table} {$where}";

		// 执行sql语句
		$res = $this->query($sql);
		$data = $this->assoc($res);
		return $data;
	}

	/**
	 * 跨库添加单条数据
	 * @param  table   表名
	 * @param  data    需要添加的数据array
	 * @return res     返回布尔值
	 */
	public function db_add($table,$data){
		// INSERT INTO table_name ( field1, field2,...fieldN ) VALUES ( value1, value2,...valueN );
		$data = $this->arrdata($data);
		$k 	 = array_keys($data);
		$v 	 = array_values($data);
		$key = implode(',',$k);
		$val = implode(',',$v);
		$sql = "INSERT INTO {$table} ({$key}) VALUES ({$val})";

		$res = $this->query($sql);
		return $res;
	}


	/**
	 * 添加单条数据
	 * @param  table   表名
	 * @param  data    需要添加的数据array
	 * @return res     返回布尔值
	 */
	public function addresult($table,$data){
		// INSERT INTO table_name ( field1, field2,...fieldN ) VALUES ( value1, value2,...valueN );
		$data = $this->arrdata($data);
		$k 	 = array_keys($data);
		$v 	 = array_values($data);
		$key = implode(',',$k);
		$val = implode(',',$v);
		$table = $this->pre.$table;

		$sql = "INSERT INTO {$table} ({$key}) VALUES ({$val})";

		$res = $this->query($sql);
		return $res;
	}

	/**
	 * 添加多条数据
	 * @param  table   表名
	 * @param  data    需要添加的数据array
	 * @return res     返回布尔值
	 */
	public function addsresult($table,$data){
		$table = $this->pre.$table;
		$key = null;
		$kval = null;
		$sql = '';

		foreach($data as $k => $v){
			$v = $this->arrdata($v);
			$key = implode(',',array_keys($v));
			$val = implode(',',array_values($v));
			$sql .= "INSERT INTO {$table} ({$key}) VALUE ({$val});";

		}
		$sql = rtrim($sql,';');

		if ($this->link->multi_query($sql) === TRUE) {
		    return true;
		} else {
		    return "Error: " . $sql . "<br>" . $this->error;
		}
	}


	/**
	 * @param  table    表名
	 * @param  data     修改的数据
	 * @param  where    条件
	 * @return result   返回布尔值
	 */
	public function update($table,$data,$where=null){
		$table = $this->pre.$table;
		$sql = "UPDATE {$table} SET {$data}  {$where}";
		$result = $this->query($sql);
		return $result;
	}

	/**
	 * @param  table
	 * @param  where
	 * @return return
	 */
	public function del($table,$where){
		$table = $this->pre.$table;
		$sql = "DELETE FROM {$table} WHERE {$where}";
		$result =$this->query($sql);
		return $result;
	}

	/**
	 * @param  data    需要处理的一维数组
	 * @return data    处理返回array
	 */
	public function arrdata($data){
		foreach($data as $k=>$v){
			if(is_string($v)){
				$data[$k] = "'".$v."'";
			}
		}
		return $data;
	}



	// 处理查询的结果集
	/**
	 * @param  res     结果集
	 * @param  boolean 判断返回一维还是二维数组
	 * @return data    array
	 */
	public function assoc($res,$m = false){
		$data = [];
		while($res && $list = mysqli_fetch_assoc($res)){
			$data[] = $list;
		}
		if($m == true){
			$data = $data[0];
		}
		return $data;
	}

	/**
	 * @param  sql   sql语句
	 * @return 结果集
	 */
	public function query($sql){
		return $this->link->query($sql);
	}

	/**
	 * 分页
	 * @param  [type] $p     [当前页数]
	 * @param  [type] $pnum  [页数据]
	 * @param  [type] $table [表名]
	 * @param  [type] $href  [分页链接]
	 * @param  string $where [条件]
	 * @param  [type] $field [字段]
	 * @return [type]        [description]
	 */
	function page($p,$pnum,$table,$where='',$field='*',$href=''){

		$count = $this->countNum($table,'count(*) as c');

		$page['count'] = $count['c'];
		$pageN = ceil($count['c'] /$pnum);
		if($p <= 0){
			$p = 1;
		}else if($p > $pageN){
			$p = $pageN;
			if($p == 0){
				$p = 1;
			}
		}

		$s = ($p-1) * $pnum;

		$page['pre'] = $p -1;
		$page['next'] = $p +1;

        $result = $this->query("select {$field} from {$table} inner join ( select id from {$table} order by post_at desc limit {$s},{$pnum}) b using (id)");
        $list = $this->assoc($result);

// 		$list = $this->sel_all($table,"{$where} limit {$s},{$pnum}",$field);

		$page['num'] = count($list);


		$page['span'] = $this->pageM($pageN,$p,$href);
		$page['list'] = $list;
		return $page;
	}
	/**
	 * [page description]
	 * @param  [type] $sum     [总页数]
	 * @param  [type] $pagenum [页数]
	 * @return [type]          [description]
	 */
	function pageM($sum,$pagenum,$href){
		$span = "";
		$pspan = [];
		if($sum > 0){
  			if($pagenum <=0){$pagenum = 1;}
	  		if($pagenum >= $sum){$pagenum = $sum;}

	  		$k = $pagenum-2 <= 0 ? 1:$pagenum-2;
	  		$m = $sum - 6 <= 0 ?1:$sum-6;
	  		$pageM = $pagenum == 1?$pagenum+4:$pagenum + 2;
            if($pagenum == 2)$pageM = 5;
	  		if($sum - $pagenum >= 6){
	  			for($i = $k; $i <= $pageM; $i++){
	  	// 			$color = $i == $pagenum?'style="background:#2d8cf0;color:white"':'';
		  //			$span .= "<navigator url='{$href}{$i}' {$color}>第{$i}页</navigator>";
		  			array_push($pspan,$i);

		  		}

		  		$span .= '....';
		  		array_push($pspan,false);
		  		for($i = $sum - 3; $i <= $sum; $i++){
		  		    array_push($pspan,$i);
		  //			$span .= "<navigator url='{$href}{$i}' {$color}>第{$i}页</navigator>";
		  		}
	  		}else{
	  			for($i = $m; $i <= $sum; $i++){
	  			    array_push($pspan,$i);
		  //			$color = $i == $pagenum?'style="background:#2d8cf0;color:white"':'';
		  //			$span .= "<navigator url='{$href}{$i}' {$color}>第{$i}页</navigator>";
		  		}
	  		}
  		}
  		return $pspan;
	}

	function countNum($table,$where){
	    return $this->sel_once($table,'','count(*) as c');
	}

	/**
	 * @param  关闭连接
	 */
	public function __destruct(){
		mysqli_close($this->link);
	}

}

?>
