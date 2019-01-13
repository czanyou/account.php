<?php
!defined('IN_VISION') && exit('Access Denied');

class AuthUtils {

	/** 检测用户是否登录. */
	public function checkLogin($callback = '/', $role = '') {
		$callback = $_SERVER['HTTP_REFERER'];    
		if (empty($callback)) {
			$callback = "manager.php";
		}

		$url = "?path=/account/login&callback=".urlencode($callback);
		
		if (!$this->isLogin()) {
			$username	= safe_get($_GET,  'username');
			$password	= safe_get($_GET,  'password'); 
			if (!empty($username) && !empty($password)) {
				$this->login($username, $password); 
			}

			$this->reLocation($url);
			exit(0);
		}

		if (!empty($role) && !$this->isRole($role)){
			$this->reLocation($url);
			exit(0);
		}
	}

	public function getLoginUser() {
		return safe_get($_SESSION, 'vision_login_user', new Object());
	}

	public function getMe() {
		global $globalViewer;
		return $globalViewer->id;
	}

	public function isEmail($email) {
		return eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$", $email);
	}

	public function isLogin() {
		global $globalViewer;
		return is_object($globalViewer);// && !is_null($globalViewer->email);
	}

	public function isRole($role) {
		global $globalViewer;
		 
		if ($role == 'admin') {
			return safe_get($globalViewer, 'email') == C('site_admin');
		}

		return false;
	}

	/** 登录. */
	public function login($username, $password, $callback = null) {
		global $globalViewer;

		if (empty($username) || empty($password)) {  
			return 'Empty account and password';
		}

		//如果密码没有经过MD5加密则自动加密
		if (!empty($password) && strlen($password) != 32){
			$password = md5($password);
		}
	
		//echo $username, $password;

		//echo $type;
		$userDao  = BaseService::getService('UserDao');
		$user = $userDao->checkUserLogin($username, $password);
		//print_r($user);
		if (is_null($user)) {
			return L('无效的用户名或密码');
		}

		$globalViewer = $user;
		$_SESSION["vision_login_user"] = $user;
		$_SESSION["vision_user_id"] = $user->id;

		// 保存用户名
		cookie('pa', $username, 360000);
		
		return '';
	}

	/** 注销当前登录的用户. */
	public function logout($callback) {
		unset($_SESSION['vision_login_user']);
		unset($_SESSION['vision_user_id']);
		
		if ($callback) {
			header('Location: '.$callback);
		}

		cookie('pt', null);
		cookie('pm', null);
	}
	
	public function reLocation($url){
		echo "<script>";
		echo  "top.location.href = '".$url."';";
		echo "</script>";
	}

}

?>