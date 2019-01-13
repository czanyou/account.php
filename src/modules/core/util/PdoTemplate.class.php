<?php
!defined('IN_VISION') && exit('Access Denied');

class PdoTemplate extends Object {

	public $fields;			///< 这个对象相关的数据库表字段列表
	public $tableName;		///< 这个对象相关的数据库表名
	public $primaryKey;		///< 这个对象相关的数据库表的主键名
	public $pdo;			///< 相关的 PDO 对象

	/** 
	 * 添加一个新的实体对象. 
	 *
	 * @param object generateId 主键的值
	 * @param array $entity 要添加的实体的属性和新的值.
	 * @param array $params 查询参数
	 */
	public function addEntity($generateId, $entity) {
		$names	= '';
		$list   = '';
		$sep	= '';
		$values = array();

		if (!empty($generateId)) {
			$names	= $this->primaryKey.',';
			$list   = $generateId.',';
		}

		// fields
		foreach ($this->fields as $fieldName => $columnName) {
			if ($columnName == $this->primaryKey) {
				continue; // 不可以修改主键

			} else if (!isset($entity[$fieldName])) {
				continue;
			}

			$names .= $sep;
			$names .= $columnName;
			$list  .= $sep;
			$list  .= '?';

			$values[] = $entity[$fieldName];
			$sep = ',';
		}

		// execute
		$sql = "INSERT INTO $this->tableName ($names) VALUES ($list);";

		//echo $sql;

		$statement = $this->pdo->prepare($sql);
		if (is_object($statement)) {
			$statement->execute($values);
			$info = $statement->errorInfo();
			if (isset($info[1])) {
				return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
			}
			
			return $entity;
		}

		return array('sql'=>$sql, 'values'=>$values);
	}

	/** 
	 * 添加一个过滤条件.
	 *
	 * @param array $params 查询参数
	 * @param string $field 字段名
	 * @param string $op 操作符
	 * @param string $value 操作值
	 */
	public function addFilter(&$params, $field, $op, $value) {
		if (!isset($params['@filters'])) {
			$params['@filters'] = array();
		}

		//VisionPrintStackTrace();
		$params['@filters'][] = array('by'=>$field, 'op'=>$op, 'value'=>$value);
	}

	/** 执行 Update 语句. */
	public function executeUpdate($sql, $values = NULL) {
		$statement = $this->pdo->prepare($sql);
		if (is_object($statement)) {
			$statement->execute($values);
			$info = $statement->errorInfo();
			if (isset($info[1])) {
				return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
			}
			
			return array('id'=>$id);
		}

		return 0;
	}

	/** 执行查询并返回对象列表. */
	public function fetchAll($sql, $values = null) {
		$statement = $this->pdo->prepare($sql);
		if (is_object($statement)) {
			$statement->execute($values);
			$info = $statement->errorInfo();
			if (isset($info[1])) {
				return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
			}

			return $statement->fetchAll(PDO::FETCH_OBJ);

		} else {
			$info = $this->pdo->errorInfo();
			if (!empty($info)) {
				return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
			}
		}

		return array();
	}

	/** 执行查询并返回整数结果. */
	public function fetchCount($sql) {
		$statement = $this->pdo->prepare($sql);
		if (is_object($statement)) {
			$statement->execute(array());
			return $statement->fetchColumn(0);
		}

		return -1;
	}

	/** 
	 * 查询指定条件的实体. 
	 *
	 * @param array $params 查询参数
	 */
	public function findEntities($params) {
		$sql = "SELECT ";
		$sql .= $this->getDefaultNames();
		$sql .= $this->statTable($params);
		$sql .= $this->statWhere($params);
		$sql .= $this->statOrderBy($params);
		$sql .= $this->statLimit($params);

		// echo $sql;

		return $this->fetchAll($sql);
	}

	/**
	 * 返回默认的字段名列表
	 *
	 * @return
	 */
	public function getDefaultNames() {
		if (!empty($this->defaultNames)) {
			return $this->defaultNames;
		}

		$names = $this->getFieldNames();
		$this->defaultNames = $names;
		return $names;
	}

	/**
	 * 查询指定属性的实体对象.
	 *
	 * @param string $field 字段的名称.
	 * @param string $value 字段的值.
	 * @param string $fieldNames 返回的字段列表, 如果没有指定则返回默认的字段.
	 * @return 返回符合条件的实体对象, 如果不存在则返回 NULL.
	 */
	public function getEntity($field, $value, $fieldNames = NULL) {
		$names = is_null($fieldNames) ? $this->getDefaultNames() : $fieldNames;
		if (isset($fields[$field])) {
			$field = $fields[$field];
		}

		$sql = "SELECT $names FROM $this->tableName WHERE $field=? LIMIT 0,1";
		//echo 'sql:',$sql;

		$statement = $this->pdo->prepare($sql);
		if (is_object($statement)) {
			$statement->execute(array($value));
			$ret = $statement->fetchObject();
			return $ret;
		}

		return NULL;
	}

	/**
	 * 返回指定的 ID 的实体对象.
	 *
	 * @param string $id 实体的 ID
	 * @return 返回指定的 ID 的实体对象, 如果不存在则返回 NULL.
	 */
	public function getEntityById($id, $fieldNames = NULL) {
		return $this->getEntity($this->primaryKey, $id, $fieldNames);
	}

		/** 
	 * 查询指定条件的实体的总数. 
	 *
	 * @param array $params 查询参数
	 */
	public function getEntityCount($params = array()) {
		$sql = 'SELECT COUNT(*) AS count';
		$sql .= $this->statTable($params);
		$sql .= $this->statWhere($params);
		
		//echo $sql;
		$statement = $this->pdo->prepare($sql);
		if (is_object($statement)) {
			$statement->execute(array());
			return $statement->fetchColumn(0);
		}
		return -1;
	}

	/** 
	 * 返回相关的对象的字段列表. 
	 * 
	 * @param string $prefix 名称前缀
	 */
	public function getFieldNames($prefix = null, $fields = null) {

		$names = "";
		$sep = "";

		if (is_array($fields)) {
			foreach ($fields as $key) {
				$value = $this->fields[$key];
				if (is_null($value)) {
					continue;
				}

				$names .= $sep;

				if (!is_null($prefix)) {
					$names .= $prefix;
					$names .= $value;
					$names .= " AS ";

				} else if ($key != $value) {
					$names .= $value;
					$names .= " AS ";
				}
				$names .= $key;
				$sep = ", ";
			}

		} else {

			foreach ($this->fields as $key => $value) {
				if ($key == "password") {
					// continue;
				}

				$names .= $sep;

				if (!is_null($prefix)) {
					$names .= $prefix;
					$names .= $value;
					$names .= " AS ";

				} else if ($key != $value) {
					$names .= $value;
					$names .= " AS ";
				}
				$names .= $key;
				$sep = ", ";
			}
		}

		return $names;
	}

	/** 返回这个服务的所有字段名. */
	public function getFields()		{ 
		return $this->fields; 
	}

	/** 
	 * 返回 Filter WHERE 子句. 
	 * 
	 * @param array $params 查询参数
	 */
	public function getFilterExpression($params) {

		if (!is_array($params) || !isset($params['@filters'])) {
			return '';
		}

		$filters = $params['@filters'];
		if (!is_array($filters)) {
			return '';
		}

		$where = '';
		$sep = '';

		foreach ($filters as $filter) {
			$by = $filter['by'];
			$op = $filter['op'];
			$value = $filter['value'];

			if (isset($this->fields[$by])) {
				$by = $this->fields[$by];
			}

			if ($op == "contains") {
				$op = 'LIKE';
				$value = "%".$value."%";

			} else if ($op == "equals") {
				$op = '=';

			} else if ($op == "startsWith") {
				$op = 'LIKE';
				$op = $value."%";

			} else if ($op == "present") {

			}
			
			$where .= $sep;

			if ($op == 'is' && is_null($value)) {
				$where .= ' (';
				$where .= $by;
				$where .= ' IS NULL OR ';
				$where .= $by;
				$where .= ' = \'\')';

			} else {
				$where .= $by;
				$where .= ' ';
				$where .= $op;

				if (is_null($value)) {
					$where .= " NULL";

				} else {
					if ($op == "IN" || $op == "NOT IN") $where .= " ("; 

					if (is_array($value)) {
						$where .= join(",", $value);
						
					} else {
						$where .= " '";
						$where .= $value;
						$where .= "'";
					}

					if ($op == "IN" || $op == "NOT IN") $where .= ")";
				}
			}

			$sep = ' AND ';
		}

		return $where;
	}
	
	/** 返回这个服务相关的数据库表主键名. */
	public function getPrimaryKey() { 
		return $this->primaryKey; 
	}

	/** 返回这个服务相关的数据库表名. */
	public function getTableName()  { 
		return $this->tableName; 
	}

	public function removeEntities($params = array()) {
		$where = $this->statWhereInfo($params, $values);
		if (empty($where)) {
			return -1; // 防止删除所有记录
		}

		$sql = 'DELETE FROM ';
		$sql .= $this->tableName;
		$sql .= $where;

		$statement = $this->pdo->prepare($sql);
		if (!is_object($statement)) {
			return -1;
		}

		// execute update
		$statement->execute($values);

		$info = $statement->errorInfo();
		if (isset($info[1])) {
			return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
		}
		
		return 0;
	}
	
	/** 
	 * 删除指定的 ID 实体对象. 
	 *
	 * @param string $id 要删除的实体的 ID.
	 * @param array $params 查询参数
	 */
	public function removeEntity($id, $params = array()) {
		// Entity ID
		if ($id == "@entity_id") {
			$key = $params["@entity_id_name"];
			$id  = $params[$key];
		}
		
		if (empty($id)) {
			return array('error'=>-100, 'message'=>'invalid id');
		}
		
		$this->addFilter($params, $this->primaryKey, '=', $id);
		
		// Build SQL string
		$values = array();
		
		$sql = 'DELETE FROM ';
		$sql .= $this->tableName;
		$pos = strpos($id, ",");
		if ($pos === false) {
			$sql .= $this->statWhereInfo($params, $values);
			
		} else { 
			$pos = strpos($id, "'");
			if ($pos === false) {
				$id = str_replace(",", "','",$id);
				$id = "'".$id."'";
			}

			$sql .= " WHERE ".$this->primaryKey;
			$sql .= " IN (".$id.")";
		}

		//echo $sql;
		
		// prepare SQL statement
		$statement = $this->pdo->prepare($sql);
		if (!is_object($statement)) {
			return -1;
		}		
			
		// execute update
		$statement->execute($values);

		$info = $statement->errorInfo();
		if (isset($info[1])) {
			return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
		}
		
		return array('id'=>$id);
	}

	/**
	 * Left Join 子句
	 * @param table 要连接的表名
	 * @param pk 要连接的表的主键名
	 * @param key 当前表要连接的字段名
	 */
	public function statJoin($table, $pk, $key) {
		if (is_object($table) && is_subclass_of($table, 'BaseService')) {
			$table = $table->pdoTemplate->getTableName();
		}
		return ' LEFT JOIN '.$table.' b ON (a.'.$key.' = b.'.$pk.')';
	}
	
	public function statLeftJoin($table, $alies, $pk, $key) {
		if (is_object($table) && is_subclass_of($table, 'BaseService')) {
			$table = $table->pdoTemplate->getTableName();
		}
		return ' LEFT JOIN '.$table.' '.$alies.' ON ('.$key.' = '.$pk.')';
	}
	
	/** Limit 子句. */
	public function statLimit(&$params) {
		$startIndex = $params['startIndex'];
		$count = $params['count'];
		
		if (!is_numeric($startIndex)) {
			$startIndex = 0;
		}
		
		if (!is_numeric($count)) {
			$count = 20;
		}
		
		return ' LIMIT '.$startIndex.', '.$count;
	}

	/**
	 * Order 子句
	 */
	public function statOrder($field, $asc = true) {
		return ' ORDER BY '.$field.($asc ? ' ASC' : ' DESC');
	}

	/** Order By 子句. */
	public function statOrderBy(&$params, $table = "") {
		$orderBy = $params['orderBy'];
		if (!empty($orderBy)) {
			return ' ORDER BY '.$table.$orderBy;
		}
		
		return '';
	}

	/** Table Name 子句. */
	public function statTable(&$params = null) {
		return ' FROM '.$this->tableName.' a';
	}

	/** Where 子句. */
	public function statWhere(&$params) {
		$where = $this->getFilterExpression($params);
		if (!empty($where)) {
			return ' WHERE '.$where;
		}

		return '';
	}

	/** Where 子句. */
	public function statWhereInfo(&$params, &$values) {
		if (!is_array($params) || !isset($params['@filters'])) {
			return '';
		}

		$filters = $params['@filters'];
		if (!is_array($filters)) {
			return '';
		}

		$where = '';
		$sep = '';

		foreach ($filters as $filter) {
			$by = $filter['by'];
			$op = $filter['op'];

			if (isset($this->fields[$by])) {
				$by = $this->fields[$by];
			}
			
			$where .= $sep;
			$where .= $by;
			$where .= $op;
			$where .= '?';

			$sep = ' AND ';
			
			$values[] = $filter['value'];
		}

		if (!empty($where)) {
			return ' WHERE '.$where;
		}

		return '';
	}

	/** 
	 * 更新指定的实体对象. 
	 *
	 * @param string $id 要更新的实体的 ID.
	 * @param array $entity 要更新的实体的属性和值.
	 * @param array $params 查询参数
	 */
	public function updateEntity($id, $entity, $params = array()) {
		// Entity ID
		if ($id == "@entity_id") {
			$key = $params["@entity_id_name"];
			$id  = $params[$key];
		}

		// 字段名和字段值列表
		$names  = "";
		$sep    = "";
		$values = array();
		foreach ($this->fields as $fieldName => $columnName) {
			if ($columnName == $this->primaryKey) {
				continue; // 不可以修改主键

			} else if (!isset($entity[$fieldName])) {
				continue; // 不存在
			}

			$names .= "$sep$columnName=?";
			$values[] = $entity[$fieldName];
			$sep = ",";
		}
		
		// Filter
		$this->addFilter($params, $this->primaryKey, '=', $id);
		
		// Build SQL
		$sql = "UPDATE $this->tableName SET $names";
		$sql .= $this->statWhereInfo($params, $values);

		//print_r($sql);
		//print_r($values);
		
		// Prepare query
		$statement = $this->pdo->prepare($sql);
		if (!is_object($statement)) {
			return array('sql'=>$sql, 'values'=>$values);
		}

		// Execute query
		$statement->execute($values);
		$info = $statement->errorInfo();
		if (isset($info[1])) {
			return array('error'=>$info[0], 'code'=>$info[1], 'message'=>$info[2]);
		}
		
		return $entity;
	}
}
