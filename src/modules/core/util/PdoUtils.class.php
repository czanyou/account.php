<?php
!defined('IN_VISION') && exit('Access Denied');

class PdoUtils extends PDO {
	static function getInstance() {
		global $_SCONFIG;
		if (self::$instance == NULL) {
			self::$instance = new PdoUtils(
				$_SCONFIG['dbhost'], 
				$_SCONFIG['dbname'], 
				$_SCONFIG['dbuser'], 
				$_SCONFIG['dbpw'], 
				$_SCONFIG['dbport']);
		}
		return self::$instance;
	}
	
	static private $instance = NULL;
	
	function __construct($host, $database, $user, $pass, $port) {
        try { 
			$url = 'mysql:host='.$host.';port='.$port.';dbname='.$database;
			$flags = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
            parent::__construct($url, $user, $pass, $flags);

        } catch (PDOException $e) {
            die('error!' . $e->getMessage() ); 
        }
    }

    function __destruct() {}

	function addItem($sql) {
		$this->handler($sql);
	}

	function addItems($sqls) {
		$this->handler($sqls);
	}

	function checkOperate($sql) {
		$sql = trim($sql);
		$arr = explode(" ", $sql);
		if (count($arr) > 0) {
			$operate = strtoupper($arr[0]);
			if ($operate == "INSERT" || $operate == "DELETE" 
				|| $operate == "UPDATE" || $operate == "SELECT") {
				return TRUE;
			}
		}

		return FALSE;
	}

	function deleteItem($sql) {
		$this->handler($sql);
	}

	function deleteItems($sqls) {
		$this->handler($sqls);
	}

	function getItem($sql) {
		parent::setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
		return parent::query($sql)->fetch();
	}

	function handler($sqls) {
		try {
			parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			parent::beginTransaction();
			
			if (is_array($sqls)) {
				foreach($sqls as $sql) {
					if ($this->checkOperate($sql)) {
						parent::exec($sql);
					}
				}
			} else if (is_string($sqls)) {
				if ($this->checkOperate($sqls)) {
					parent::exec($sqls);
				}
			}

			parent::commit();
		} catch (Exception $e) { 
			parent::rollBack(); 
			echo "Failed: ".$e->getMessage(); 
		}
	}

	function listItems($sql) {
		parent::setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
		return parent::query($sql);
	}

	function listItems2ASSOC($sql) {
		$rs = $this->listItems($sql);
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		$result_arr = $rs->fetchAll();
		return $result_arr;
	}

	function listItems2NUM($sql) {
		$rs = $this->listItems($sql);
		$rs->setFetchMode(PDO::FETCH_NUM);
		$result_arr = $rs->fetchAll();
		return $result_arr;
	}

	function listItems2BOTH($sql) {
		$rs = $this->listItems($sql);
		$rs->setFetchMode(PDO::FETCH_BOTH);
		$result_arr = $rs->fetchAll();
		return $result_arr;
	}

	function listItems2OBJ($sql) {
		$rs = $this->listItems($sql);
		$rs->setFetchMode(PDO::FETCH_OBJ);
		$result_arr = $rs->fetchAll();
		return $result_arr;
	}

    function queryItem($sql) { 
        return parent::query($sql)->fetch();
    } 

    function queryList($sql) {
        return parent::query($sql)->fetchAll();
    }

	function updateItem($sql) {
		$this->handler($sql);
	}

	function updateItems($sqls) {
		$this->handler($sqls);
	}

}


?>
