<?php
!defined('IN_VISION') && exit('Access Denied');

/**
 * 基本的 RESTful 服务提供者.
 * <p>
 * 这个类实现了一些基本的 RESTful WEB 服务功能.
 * 
 * @author ChengZhen (anyou@msn.com)
 */
class BaseService extends Object {

	///////////////////////////////////////////////////////////////////////////
	// URL 常量

	const ALL		= "@all";
	const ADMIN		= "@admin";
	const ME		= "@me";
	const OWNER		= "@owner";
	const VIEWER	= "@viewer";
	const SEARCH	= "@search";

	///////////////////////////////////////////////////////////////////////////
	// 数据成员

	protected $urlTemplate;		///< URL 模板
	protected $defaultNames;	///< 
	protected $pdoTemplate;		///< 

	private static $pdoUtil;	///< 全局 PDO 对象
	private static $_instance = array();

	///////////////////////////////////////////////////////////////////////////
	// 静态方法

	/**
	 * 返回指定的名称的 RESTful 服务对象.
	 *
	 * @param string $name 要返回的服务的类名, 如 'DeviceDao'.
	 * @return BaseService 返回相应的服务的实例, 如果不存在则返回 NULL.
	 */
	public static function getService($class) {
		if (!isset(self::$_instance[$class])) {
			if (!is_object(self::$pdoUtil)) {
				self::$pdoUtil = PdoUtils::getInstance();
			}

			if (class_exists($class)) { 
				self::$_instance[$class] = new $class(self::$pdoUtil);
			}
		}
		
        return self::$_instance[$class];
	}

	/**
	 * 创建并返回一个错误应答消息.
	 *
	 * @param int $code 错误代码或者类型
	 * @param string $message 错误消息
	 * @return array 应答
	 *
	 */
	public static function getErrorResponse($code, $message) {
		$response = array("error" => array("code"=>$code, "message"=>$message));
		return $response;
	}

	public static function isEmail($email) {
		return preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/i", $email);
	}

	public static function isMobile($mobile) {
		return preg_match("/^1[3458]{1}\d{9}$/i", $mobile);
	}

	/** 
	 * 添加一个新的实体对象. 
	 *
	 * @param $params array 要添加的实体的属性和新的值.
	 */
	public function addEntity($params) {
		$generateId = $this->generateId();
		$ret = $this->pdoTemplate->addEntity($generateId, $params);
		return $this->createResponse($ret);
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

		$params['@filters'][] = array('by'=>$field, 'op'=>$op, 'value'=>$value);
	}

	/** 
	 * 创建一个应答消息对象. 
	 *
	 * @param mixed $entry 实体
	 * @param int $totalResults 总共的实体数
	 * @param array $params 请求参数
	 * @return array 返回应答消息对象. 
	 */
	public function createResponse($entry, $totalResults = null, $params = null) {
		if (is_array($entry) && isset($entry['error'])) {
			return $this->errorResponse(200, $entry['message']);
		}

		$response = array("data" => $entry);

		$code = $this->pdoTemplate->pdo->errorCode();
		if ($code && $code != '00000') {
			$response["error"] = $this->pdoTemplate->pdo->errorInfo();
		}

		if (!is_null($totalResults)) {
			$response["total"] = $totalResults;
		}

		if (!is_null($startIndex)) {
			$response["start"] = $startIndex;
		}

		if (!is_null($itemsPerPage)) {
			$response["count"] = $itemsPerPage;
		}

		return $response;
	}

	public function errorResponse($code, $message = '') {
		return array('error'=>array('code'=>$code, 'message'=>$message));
	}

	/** 
	 * 查询指定条件的实体. 
	 *
	 * @param array $params 查询参数
	 */
	public function findEntities($params) {
		return $this->pdoTemplate->findEntities($params);
	}

	/** 产生主键值的方法. */
	public function generateId() {
		return null;
	}

	/** 
	 * 执行查询操作. 
	 *
	 * @param array $params 查询参数
	 * @return array 返回查询结果
	 */
	public function getEntities($params) {
		return $this->createResponse("");
	}

	/** 
	 * 查询指定条件的实体的总数. 
	 *
	 * @param array $params 查询参数
	 */
	public function getEntityCount($params) {
		return $this->pdoTemplate->getEntityCount($params);
	}

	/**
	 * 返回指定的 ID 的实体对象.
	 *
	 * @param string $id 实体的 ID
	 * @return 返回指定的 ID 的实体对象, 如果不存在则返回 NULL.
	 */
	public function getEntityById($id, $fieldNames = NULL) {
		if (!$id) {
			return null;
		}
		return $this->pdoTemplate->getEntityById($id, $fieldNames);
	}

	/**
	 * 查询指定属性的实体对象.
	 *
	 * @param string $field 字段的名称.
	 * @param string $value 字段的值.
	 * @param string $fieldNames 返回的字段列表, 如果没有指定则返回默认的字段.
	 * @return 返回符合条件的实体对象, 如果不存在则返回 NULL.
	 */
	public function getEntityByProperty($field, $value, $fieldNames = NULL) {
		return $this->pdoTemplate->getEntity($field, $value, $fieldNames);
	}

	/** 返回当前登录的用户的 ID. */
	public function getMe() {
		global $auth;
		return $auth->getMe();
	}

	/** 
	 * 初始化所有的查询参数. 
	 *
	 * @param array $paths 请求的路径
	 * @param array $query 请求的查询参数
	 * @return array 返回合并后的参数集
	 */
	public function getParams($paths, $query) {
		$params = array_merge($query);

		// URL 查询参数
		$params["startIndex"]   = $query['startIndex'];
		$params["count"]		= $query['count'];

		if (isset($query['filterBy'])) {
			$this->addFilter($params, $query['filterBy'], $query['filterOp'], $query['filterValue']);
		}

		if (isset($query['fields'])) {
			$params["fields"]	= $query['fields'];
		}

		if (!is_numeric($params["startIndex"])) {
			$params["startIndex"] = 0;
		}

		if (!is_numeric($params["count"])) {
			$params["count"] = 20;
		}

		// URL 路径参数
		$urlParts = explode('/', $this->urlTemplate);
		$index = 1;
		$lastTag = null;
		foreach ($urlParts as $part) {
			if (substr($part, 0, 1) == '{' && substr($part, strlen($part) - 1, 1) == '}') {
				$tag = substr($part, 1, strlen($part) - 2);
				if (!isset($params[$tag])) {
					$params[$tag] = $paths[$index];
				}
				$lastTag = $tag;
			}

			$index++;
		}

		// 实体 ID 变量名
		if (!is_null($lastTag)) {
			$params["@entity_id_name"] = $lastTag;
		}

		return $params;
	}

	/** 
	 * 返回相关的对象的字段列表. 
	 * 
	 * @param string $prefix 名称前缀
	 */
	public function getFieldNames($prefix = null, $fields = null) {
		return $this->pdoTemplate->getFieldNames($prefix, $fields);
	}

	/** 
	 * 初始化这个服务. 
	 *
	 * @param string $table 数据库表格名
	 * @param string $pk 主键名称
	 * @param array $fields 字段列表
	 * @param PdoUtils $do PDO 对象
	 */
	public function init($tableName = null, $primaryKey = null, $fields = null, $pdo = null) {

		$this->pdoTemplate = new PdoTemplate();

		$pdoTemplate = $this->pdoTemplate;
		if (is_object($pdo)) {
			$pdoTemplate->pdo = $pdo;
		}

		if (is_string($tableName)) {
			$pdoTemplate->tableName = $tableName;
		}

		if (is_string($primaryKey)) {
			$pdoTemplate->primaryKey = $primaryKey;
		}

		if (is_array($fields)) {
			$pdoTemplate->fields = $fields;
		}
	}

	public function removeEntities($params = array()) {
		return $this->pdoTemplate->removeEntities($params);
	}

	/** 
	 * 删除指定的 ID 实体对象. 
	 *
	 * @param string $id 要删除的实体的 ID.
	 * @param array $params 查询参数
	 */
	public function removeEntity($id, $params) {
		$ret = $this->pdoTemplate->removeEntity($id, $params);
		return $this->createResponse($ret);
	}
		
	/** 清除所有值为 null 的属性. */
	public function trimResult(&$entry) {
		if (is_object($entry)) {
			
			foreach ($entry as $key => $value) {
				if (is_null($value)) {
					unset($entry->$key);
				}
			}
			return $entry;

		} else if (!is_array($entry)) {
			return $entry;
		}

		foreach ($entry as $item) {
			if (!is_object($item)) {
				break;
			}

			foreach ($item as $key => $value) {
				if (is_null($value)) {
					unset($item->$key);
				}
			}
		}

		return $entry;
	}

	/** 
	 * 更新指定的实体对象. 
	 *
	 * @param string $id 要更新的实体的 ID.
	 * @param array $entry 要更新的实体的属性和新的值.
	 * @param array $params 查询参数
	 */
	public function updateEntity($id, $entry, $params = array()) {
		$ret = (object)$this->pdoTemplate->updateEntity($id, $entry, $params);
		if ($ret) {
			$ret = $this->trimResult($ret);
		}
		return $this->createResponse($ret);
	}
}

