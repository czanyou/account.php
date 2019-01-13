<?php
require_once "common.php";

import('core.util.Email');

VisionParseRequestPath();

function OnSecureGetPageName() {
	global $global_paths;

	$page = "login";
	if (!empty($global_paths[2])) {
		$_GET['q'] = $global_paths[2];
		$page = $global_paths[2];
	}
	
	return $page;
}

function OnSecureForget() {
	include('account/secure_forgot.php');
}

function OnSecureForgetFinish() {
	include('account/secure_forgot_finish.php');
}

/** Login */
function OnSecureLogin() {
	global $globalAuth;

	$username	= safe_get($_REQUEST, "username");
	$password	= safe_get($_REQUEST, 'password');
	$callback 	= safe_get($_REQUEST, 'callback');
	$client    	= safe_get($_REQUEST, 'client');

	if (empty($callback)) {
  		$callback = "index.php";
	}

	if (VisionIsLogin()) {
		header('Location: '.$callback);
		exit(0);
	}

	if (!empty($username)) {
		$error	= $globalAuth->login($username, $password);
		if (empty($error)) {
			header('Location: '.$callback);
			exit(0);
		}
	}

	// Auto login parameters
	if (empty($username)) {
		$username = cookie('pa');
	}

	// parameters
	$params = array(
		'callback'	=> $callback, 
		'error'		=> $error, 
		'username'	=> $username, 
		'password' 	=> $password, 
		'client' 	=> $client);

	include('account/secure_login.php');
}

/** OnSecureAddUser. */
function OnSecureAddUser() {
	$name		= safe_get($_GET,  'name');
	$email		= safe_get($_GET,  'email');
	$userDao	= BaseService::getService("UserDao");
	$params  	= $userDao->getParams($paths, $_GET);
	 
	$params['userId'] = "@me";
	$params['groupId'] = "@search";
	$params['action'] = "@add";  

	$exception = "InvalidException";
	if (OnCheckName(1)) {
		$title = ('该用户名已经被注册了');
		echo json_encode((object)$userDao->getErrorResponse($exception, $title));

	} else if (OnCheckEmail(1)) {
		$title = ('该邮箱地址已经被注册了');
		echo json_encode((object)$userDao->getErrorResponse($exception, $title));

	} else {
		$result = $userDao->addUser($params, $params);
		echo json_encode((object)$result);
	} 
}

/** OnCheckName. */
function OnCheckName($isCheck = 0) {
	$name		= safe_get($_GET,  'name');
	$userDao	= BaseService::getService("UserDao");
	$user 		= $userDao->getUserBy("name", $name);
	
	if ($isCheck) {
		return $user != NULL;
	}

	$ret = ($user) ? 1 : 0;
	$result = array('ret'=>$ret);
	echo json_encode((object)$result);
}

/** OnCheckEmail. */
function OnCheckEmail($isCheck = 0) {
	$email		= safe_get($_GET,  'email');
	$userDao	= BaseService::getService("UserDao");
	$user 		= $userDao->getUserBy("email", $email);

	if ($isCheck) {
		return $user != NULL;
	}

	$ret = ($user) ? 1 : 0;
	$result = array('ret'=>$ret);
	echo json_encode((object)$result);
}

/** Logout. */
function OnSecureLogout() {
	global $globalAuth;
	$callback = "index.php";
	$globalAuth->logout($callback);
	exit(0);
}

/** Print Register Page. */
function OnSecureRegister() {
	include('account/secure_register.php');
}

function OnSecureRegisterFinish() {
	include('account/secure_register_finish.php');
}

/** Print Password Reset Page. */
function OnSecureReset() {
	global $globalAuth;

	$email = safe_get($_POST, "email");
	$password = safe_get($_POST, "password");
	$code = safe_get($_POST, "code");

	if (!empty($email)) {
		$userDao  = BaseService::getService("UserDao");
		if (!$userDao->isEmail($email)) {
			echo "无效的邮箱地址";
			return;
		}

		if (empty($password)) {
			echo "无效的密码";
			return;
		}

		if (empty($code)) {
			echo "无效的重置码";
			return;
		}

		$user = $userDao->getUserBy('email', $email);
		if ($user == null) {
			echo "不存在邮箱地址";
			return;
		}

		if ($user->reset_code != $code) {
			echo "无效的重置码";
			return;
		}

		$password = md5($password);

		$newUser = array();
		$newUser['password'] = $password;
		$userDao->updateUser($user->id, $newUser);
		echo "密码重置成功!";
		return;
	}

	include('account/secure_reset.php');
}

/** Send Password Reset Email. */
function OnSecureSendResetEmail($userid, $email) {
	// Email Content
	$text = '<a href=\'http://'. $_SERVER["SERVER_NAME"].'?path=/account/reset&userId='.$userid. '&chId='.$enc_text.'\'>'.L('_FORGET_MAIL_TEXT').'</a>';
	 
	// Send
	$sender	= new EmailUtils();  
	$result = $sender->Send($email, $email, L('忘记密码'), $text);
	return $result;
}

function OnPageDefault() {
	$router = array(
		'add' 				=> OnSecureAddUser,
		'checkEmail' 		=> OnCheckEmail,
		'checkName' 		=> OnCheckName,
		'forget' 			=> OnSecureForget,
		'forget_finish' 	=> OnSecureForgetFinish,
		'login' 			=> OnSecureLogin,
		'logout' 			=> OnSecureLogout,
		'register' 			=> OnSecureRegister,
		'register_finish' 	=> OnSecureRegisterFinish,
		'reset' 			=> OnSecureReset
		);

	$page = OnSecureGetPageName();
	$function = safe_get($router, $page, OnSecureLogin);
	call_user_func($function);
}

OnPageDefault();

