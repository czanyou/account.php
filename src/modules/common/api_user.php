<?php

require_once "common/api_common.php";

import('core.vision.UserDao');
import('core.vision.SessionDao');

//////////////////////////////////////////////////////////////////////////////////
// User 内部方法
// 

function userDaoGet() {
    return BaseService::getService("UserDao");
}

/** 
 * 返回指定用户的信息
 * @param userId
 * @param hideMobile 是否隐藏手机号码部分数字
 */
function userGet($userId, $hideMobile = true) {
	$userDao  = userDaoGet();
	$user = $userDao->getUser($userId);
	if ($user == null) {
		return null;
	}

	return userGetData($user, $hideMobile);
}

/** 
 * 返回指定用户的信息
 * @param user
 * @param hideMobile 是否隐藏手机号码部分数字
 */
function userGetData($user, $hideMobile = true) {
	$data = array();
	$data['id'] 		= (int)$user->id;
	$data['last_time'] 	= (int)$user->last_time;
	$data['reg_time'] 	= (int)$user->reg_time;
	$data['group_id'] 	= (int)$user->default_group;	
	$data['about_me'] 	= $user->about_me ? $user->about_me : '';
	$data['email'] 		= $user->email 	  ? $user->email : '';
	$data['name'] 		= $user->name 	  ? $user->name : '';
	$data['avatar_url'] = $user->avatar_url ? $user->avatar_url : '';

	if (!empty($user->mobile)) {
		if ($hideMobile) {
        	$data['mobile'] = substr_replace($user->mobile, "****", 3, 4);
	    } else {
	    	$data['mobile'] = $user->mobile;
	    }
    }

    $group = groupGetDefault($data);
    if ($group) {
    	$data['group_id'] 	= (int)$group->id;
    }

	return $data;
}

function userIsAdmin($request = null) {
	if ($request == null) {
		$request = $_GET;
	}

	$openid = safe_get($request, "openid");
	return $openid == 739;
}

/** 指出当前用户是否已经登录. */
function userIsLogin($request = null) {
	if ($request == null) {
		$request = $_GET;
	}

	if (VisionIsLogin()) {
		return true;
	}

	$openid   = safe_get($request, "openid");
	$openkey  = safe_get($request, "openkey");
	$clientId = safe_get($request, "client_id");


	// check parameters
	if (empty($openid)) {
		return false;

	} else if (empty($openkey)) {
		return false;

	} else if (empty($clientId)) {
		return false;
	}

	// check session
	$session = userGetLoginSession($clientId);
	if ($session == null) {
		return false;

	} else if ($openid != $session->user_id) {
		return false;

	} else if ($openkey != $session->openkey) {
		return false;		
	}

	return true;
}

//////////////////////////////////////////////////////////////////////////////////
// User Validator Code
// 

function userMessageDaoGet() {
	$fields = array(
		"id"			=> "id", 
		"sent_time"		=> "sent_time",
		"sent_code"		=> "sent_code",
		"mobile"		=> "mobile",
		"msg"			=> "msg",
		"result"		=> "result",
		"client_ip"		=> "client_ip"
	);

	$pdoUtils = PdoUtils::getInstance();
	$baseDao = new BaseService();
	$baseDao->init("vision_sms_logs", "id", $fields, $pdoUtils);

	return $baseDao;
}

function userGetLastValidatorCode($mobile) {
	// 提取发给用户的验证码
	$now 	= time();
	$params = array();
	$params['orderBy'] = 'sent_time DESC';

	$messageDao = userMessageDaoGet();
	$messageDao->addFilter($params, 'sent_time', '>', $now - 60 * 60 * 24);
	$messageDao->addFilter($params, 'mobile', '=', $mobile);

	$list = $messageDao->findEntities($params);
	if (count($list) <= 0) {
		return null;
	}

	return $list[0];
}

function userSaveValidatorCode($params, $result, $msg) {
	$messageDao = userMessageDaoGet();

	$params['msg'] 		= $msg;
	$params['result'] 	= $result;
	$messageDao->addEntity($params);
}

/** 发送 HTTP POST 请求. */
function userSendPostMessage($curlPost, $url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);

	$return_str = curl_exec($curl);
	curl_close($curl);
	return $return_str;
}

/** 
 * 发送验证短信到用户手机. 
 * @param mobile 目标手机号码
 * @param sendCode 要发送的验证码  
 */
function userSendMobileMessage($mobile, $sendCode) {
	$target = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit';

	$url  = 'account=';
	$url .= C('sms_account');
	$url .= '&password=';
	$url .= C('sms_password');
	$url .= '&mobile=';
	$url .= $mobile;
	$url .= '&content=';
	$url .= rawurlencode('您的验证码是：');
	$url .= $sendCode;
	$url .= rawurlencode('。请不要把验证码泄露给其他人。如非本人操作，可不用理会！');
	//print_r($url);

	$postData = $url;
	$ret = userXmlToArray(userSendPostMessage($postData, $target));
	return safe_get($ret, 'SubmitResult');
}

/** 解析 XML 文本并转换成数组. */
function userXmlToArray($xml) {
	$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
	if (preg_match_all($reg, $xml, $matches)) {
		$count = count($matches[0]);
		for ($i = 0; $i < $count; $i++) {
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];

			if (preg_match( $reg, $subxml )) {
				$arr[$key] = userXmlToArray($subxml);

			} else {
				$arr[$key] = $subxml;
			}
		}
	}

	return $arr;
}

//////////////////////////////////////////////////////////////////////////////////
// User Login Session
// 

function userSessionDaoGet() {
    return BaseService::getService("SessionDao");
}

function userGetLoginSession($clientId) {
	$sessionDao = userSessionDaoGet();
	return $sessionDao->getSessionBy('client_id', $clientId);
}

function userRemoveLoginSession($sessionId) {
	$sessionDao = userSessionDaoGet();
	$sessionDao->removeSession($ssessionId);
}

/** 更新用户登录会话表. */
function userUpdateLoginSession($request, $userId) {
	$token = null;
	$clientId = safe_get($request, "client_id");

	if (empty($clientId)) {
		return $token;

	} else if (empty($userId)) {
		return $token;
	}

	$sessionDao = userSessionDaoGet();
	$session = userGetLoginSession($clientId);

	// print_r($session);

	if ($session == null) { // 创建一个新的会话
		$token = $sessionDao->getRandChar(32);

		$newSession = array();
		$newSession['client_id'] 		= $clientId;
		$newSession['openkey'] 			= $token;
		$newSession['token'] 			= $token;
		$newSession['user_id'] 			= $userId;

		safeSetParams($newSession, $request, 'client_name');
		safeSetParams($newSession, $request, 'client_version');	
		$sessionDao->addSession($newSession);

	} else if ($session->user_id != $userId) { // 登录了不同账号
		$token = $sessionDao->getRandChar(32);

		$newSession = array();
		$newSession['client_id'] 		= $clientId;
		$newSession['id']				= $session->id;
		$newSession['openkey'] 			= $token;
		$newSession['token'] 			= $token;
		$newSession['user_id'] 			= $userId;

		safeSetParams($newSession, $request, 'client_name');
		safeSetParams($newSession, $request, 'client_version');	

		$sessionId = $session->id;
		$sessionDao->updateSession($sessionId, $newSession);

	} else { // 仅仅更新一下会话
		$token = $session->openkey;

		$newSession = array();
		safeSetParams($newSession, $request, 'client_name');
		safeSetParams($newSession, $request, 'client_version');		

		$sessionId = $session->id;
		$sessionDao->updateSession($sessionId, $newSession);
	}
	
	return $token;
}

//////////////////////////////////////////////////////////////////////////////////
// User Actions
// 

/**
 * 修改用户信息
 * @param openid
 * @param about_me 
 */
function onUserEdit($request) {
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // check user
	$userId = safe_get($request, "openid");

	$userDao  = userDaoGet();
	$user = $userDao->getUser($userId);
	if ($user == null) {
		return errorNotExists('user');
	}

	// update user
	$entry = array();
	safeSetParams($entry, $request, 'about_me');

	$result = $userDao->updateUser($userId, $entry);
	$result['ret']  = 0;
	return $result;
}


/**
 * 修改用户登录密码
 * @param openid
 * @param old_password 当前用户的当前密码
 * @param new_password 要设置的新密码
 */
function onUserEditPassword($request) {
	// check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

	// check parameters
	$userId   	 = safe_get($request, "openid");
	$oldPassword = safe_get($request, "old_password");
	$newPassword = safe_get($request, "new_password");
	$code 		 = safe_get($request, "code");

	if (empty($oldPassword)) {
		if (empty($code)) {
			return errorMissParamter('old_password/code');
		}

	} else if (empty($newPassword)) {
		return errorMissParamter('new_password');
	}	

	// check user
	$userDao  = userDaoGet();
	$user = $userDao->getUser($userId);
	if ($user == null) {
		return errorNotExists('user');
	}

	if ($oldPassword) {
		// 通过旧密码来验证用户
		// check old password
		if (!empty($oldPassword) && strlen($oldPassword) != 32){
			$oldPassword = md5($oldPassword);
		}

		if ($oldPassword != $user->password) {
			return array('ret'=>-1, 'error'=>'invalid old password');
		}

	} else {
		// 通过短信验证码来验证用户
		$mobile = $user->mobile;
		$item = userGetLastValidatorCode($mobile);
		if (!$item) {
			return array('ret'=>-4008, 'error'=>'Invalid code (无效的验证码)');
		}

		$sendCode = safe_get($item, 'sent_code');
		if ($sendCode != $code) {
			return array('ret'=>-4008, 'error'=>'Invalid code (无效的验证码)');
		}
	}

	// set new password
	if (!empty($newPassword) && strlen($newPassword) != 32){
		$newPassword = md5($newPassword);
	}

	$userDao->updateUserPassword($userId, $newPassword);
	return array('ret'=>0);
}

/**
 * 找回密码
 *
 */
function onUserForgot($request) {
	$result = array('ret'=>-1);

	// parameters
	$email = safe_get($request, "email");
	if (empty($email)) {
		return errorMissParamter('email');
	}

	// check email address
	$userDao  = userDaoGet();
	if (!$userDao->isEmail($email)) {
		$result['error'] = 'invalid email address';
		return $result;
	}

	// check user
	$user = $userDao->getUserBy('email', $email);
	if ($user == null) {
		return errorNotExists($email);
	}

	// 生成重置码
	$resetCode = rand(12345678, 87654321);
	$newUser = array();
	$newUser['reset_code'] = $resetCode;
	$userDao->updateUser($user->id, $newUser);

	// User ID
	$userid	= urlencode($email);
	 
	// Email Content
	$url = C('site_name') . S_PATH . '/?path=/account/reset&email='.$userid. '&code='.$resetCode;

	$text = 'Hi:<br/>';
	$text .= 'Password Reset code (密码重置代码): '.$resetCode.'<br/>';
	$text .= '您可以通过下面的链接重置您的密码:'.'<br/>';
	$text .= $url;
	//$text .= '<a href="'.$url.'">Reset password</a>';
	//echo $text;

	// Send email
	$from 		= C('reset_mail');
	$password 	= C('reset_mail');
	$sender	= new EmailUtils($from, $password);  

	$ret = $sender->Send($email, $from, '重置密码/Reset Password', $text);

	$result = array();
	$result['ret'] = $ret;
	return $result;
}

/**
 * 返回指定的 ID 的用户的基本信息
 * @param user_id 要查询的用户的 ID
 */
function onUserGetInfo($request) {
	$userId = safe_get($request, "user_id");

	// 只有用户自己可以看到自己注册的手机号码
	$hideMobile = true;
	if (empty($userId)) {
		if (userIsLogin()) {
       		$userId = safe_get($request, "openid");
       		$hideMobile = false;
    	}

    	if (empty($userId)) {
			return errorMissParamter('user_id');
		}
	}

	$data = userGet($userId, $hideMobile);
	if ($data) {
		unset($data['email']);
		unset($data['reg_time']);
	}

	$result = array('ret'=>-1);
	$result['data'] = $data;
	$result['ret']  = 0;
	return $result;
}

/**
 * 检查指定用户是否登录
 * 客户端可以每次 APP 启动时访问这个接口检查用户登录状态, 并更新用户登录会话
 * (因为用户登录会话超过多少天没有更新会被判定离线并需重新登录, 为了让客户端一直保持登录状态
 * 就需要定期访问这个接口以便重新开始计划登录超时时间)
 */
function onUserIsLogin($request) {
	if (!userIsLogin()) {
        return errorNotLogin();
    }

    // 更新登录会话以便延时登录超时时间
	$userId = safe_get($request, "openid");
	$openkey = userUpdateLoginSession($request, $userId);

	$now = time();

	// result
	$data = userGet($userId, false);
	$data['openkey'] = $openkey;
	return array('ret'=>0, 'now'=>$now, 'data'=>$data);
}

/**
 * 用户登录
 * 因为同一个账号可以在多个设备上同时登录, 所以除了用户名和密码之外, 
 * 客户端还需提供客户端的 ID. 登录成功后返回和这个客户端 ID 绑定的 openkey.
 * 服务端会保存这个客户端 ID 和 openkey, 以及用户名做为登录会话, 注意长时间不更
 * 新登录会话会被服务器判定登录过期并需要重新登录, 可以简单地通过 is_login 接口更新登录会话.
 * @param username
 * @param password
 * @param client_id
 */
function onUserLogin($request) {
	// check parameters
	$username 	= safe_get($request, "username");
	$password 	= safe_get($request, "password");

	if (empty($username)) {
		return errorMissParamter('username');

	} else if (empty($password)) {
		return errorMissParamter('password');
	}

	// check user (用户名和密码登录)
	$userDao  = userDaoGet();
	$user = $userDao->checkUserLogin($username, $password);

	//print_r($user);
	if ($user != null) {
		// 生成或更新登录会话
		$openkey = userUpdateLoginSession($request, $user->id);

		// result
		$data = userGetData($user, false);
		$data['openkey'] 	= $openkey;
		return array('ret'=>0, 'data'=>$data);
	}

	// 如果通过用户名和密码方式登录不成功则尝试用手机号码和短信验证码的方式验证登录
	$mobile = $username;
	$code   = $password;

	$item = userGetLastValidatorCode($mobile); // 提取发给用户的验证码
	if (!$item) {
		return errorLoginFailed();
	}

	$sendCode = safe_get($item, 'sent_code');
	if ($sendCode != $code) {
		$result['ret'] = -4008;
		$result['error'] = 'Invalid code or password (错误的验证码或密码)';
		return $result;
	}

	if (!empty($password) && strlen($password) != 32){
		$password = md5($password);
	}

	// check mobile number
	$userDao  = userDaoGet();
	$user = $userDao->getUserBy('mobile', $mobile);

	// 如果通过短信验证码登录的这个用户还没有注册则自动为其生成一个账号
	if ($user == null) { // 注册一个新的账号
		$params = array();
		$params['name'] 		= substr_replace($mobile, "****", 3, 4);;
		$params['email'] 		= '';
		$params['mobile'] 		= $mobile;
		$params['password'] 	= $password;
		$userDao->addUser($params);

		$user = $userDao->getUserBy('mobile', $mobile);
		if ($user == null) {
			return errorNotExists('user');
		}		
	}

	// 生成或更新登录会话
	$openkey = userUpdateLoginSession($request, $user->id);

	// result
	$data = userGetData($user, false);
	$data['openkey'] = $openkey;
	return array('ret'=>0, 'data'=>$data);
}

/**
 * 注销当前登录
 *
 */
function onUserLogout($request) {
	$result 	= array('ret'=>-1);
	$openkey  	= safe_get($request, "openkey");
	$clientId 	= safe_get($request, "client_id");

	$session = userGetLoginSession($clientId);
	if ($session == null) {
		return errorNotExists('session');
	}

	$token = $session->openkey;
	if ($openkey != $token) {
		return $result;
	}

	userRemoveLoginSession($session->id);
	$result['ret']  = 0;
	return $result;
}

/** 
 * 新用户注册
 *
 */
function onUserRegister($request) {
	$mobile = safe_get($request, "mobile");

	if (!empty($mobile)) {
		return onUserRegisterWithMobile($request);

	} else {
		return onUserRegisterWithEmail($request);
	}
}

/**
 * 通过邮箱地址快速注册账号
 */
function onUserRegisterWithEmail($request) {
	$result = array('ret'=>-1);

	$name 		= safe_get($request, "name");
	$email 		= safe_get($request, "email");
	$password 	= safe_get($request, "password");
	$mobile 	= safe_get($request, "mobile");

	// check paramters
	if (empty($name)) {
		$result['ret'] = -4002;
		$result['error'] = 'Miss name (没有提供用户名)';
		return $result;

	} else if (empty($email)) {
		$result['ret'] = -4001;
		$result['error'] = 'Miss email (没有提供邮箱址)';
		return $result;

	} else if (empty($password)) {
		$result['ret'] = -4003;
		$result['error'] = 'Miss password (没有提供密码)';
		return $result;
	}

	// check email address
	$userDao  = userDaoGet();
	if (!$userDao->isEmail($email)) {
		$result['ret'] = -4001;
		$result['error'] = 'Invalid email address (无效的邮箱地址)';
		return $result;
	}

	$user = $userDao->getUserBy('email', $email);
	if ($user != null) {
		$result['ret'] = -4001;
		$result['error'] = 'Email address already used (邮箱地址已被注册)';
		return $result;
	}

	$user = $userDao->getUserBy('name', $name);
	if ($user != null) {
		$result['ret'] = -4002;
		$result['error'] = 'Name already used (用户名已被占用)';
		return $result;
	}

	if (!empty($password) && strlen($password) != 32) {
		$password = md5($password);
	}

	// add
	$params = array();
	$params['name'] 	= $name;
	$params['email'] 	= $email;
	$params['password'] = $password;
	$params['mobile'] 	= $mobile;
	$user = $userDao->addUser($params);
	if ($user && $user['error']) {
		$error = $user['error'];
		return array('ret'=>$error['code'], 'data'=>$error['message']);
	}
	//print_r($user);

	// 
	$user = $userDao->getUserBy('email', $email);
	if ($user == null) {
		return errorNotExists('user');
	}
	$token = userUpdateLoginSession($request, $user->id);

	// result
	$data = userGetData($user, false);
	$data['openkey'] = $token;
	return array('ret'=>0, 'data'=>$data);
}

/**
 * 用户通过手机号码快速登录和注册
 */
function onUserRegisterWithMobile($request) {
	$password 	= safe_get($request, "password");
	$mobile 	= safe_get($request, "mobile");
	$code 		= safe_get($request, "code");
	$password   = '';

	$result = array('ret'=>-1);

	// check paramters
	if (empty($mobile)) {
		$result['ret'] = -4001;
		$result['error'] = 'Miss mobile (没有提供手机号码).';
		return $result;

	} else if (empty($code)) {
		$result['ret'] = -4008;
		$result['error'] = 'Miss code (没有提供验证码).';
		return $result;
	}

	// 提取发给用户的验证码
	$item = userGetLastValidatorCode($mobile);
	if (!$item) {
		$result['ret'] = -4008;
		$result['error'] = 'Invalid code (无效的验证码)';
		return $result;
	}

	$sendCode 	  = safe_get($item, 'sent_code');
	if ($sendCode != $code) {
		$result['ret'] = -4008;
		$result['error'] = 'Invalid code (错误的验证码)';
		return $result;
	}

	if (!empty($password) && strlen($password) != 32){
		$password = md5($password);
	}

	// check mobile number
	$userDao  = userDaoGet();
	$user = $userDao->getUserBy('mobile', $mobile);
	if ($user != null) { // 这个手机号码已经注册过
		$params = array();
		$params['mobile'] 		= $mobile;
		$params['mobile_valid'] = 1;
		$params['password'] 	= $password;
		$user = $userDao->updateUser($user->id, $params);

	} else { // 注册一个新的账号
		$params = array();
		$params['name'] 		= substr_replace($mobile, "****", 3, 4);;
		$params['email'] 		= '';
		$params['mobile'] 		= $mobile;
		$params['mobile_valid'] = 1;
		$params['password'] 	= $password;
		$user = $userDao->addUser($params);
	}

	// 
	$user = $userDao->getUserBy('mobile', $mobile);
	if ($user == null) {
		return errorNotExists('user');
	}
	$token = userUpdateLoginSession($request, $user->id);

	// result
	$data = userGetData($user, false);
	$data['openkey'] = $token;
	return array('ret'=>0, 'data'=>$data);
}


/** 获取短信验证码. */
function onUserSendValidator($request) {
	$mobile = safe_get($request, "mobile");

	if (empty($mobile)) {
		return errorMissParamter('mobile');
	}

	// TODO: 验证手机号码

	$code 			= rand(100000, 999999); // 验证码为 6 位随机数字
	$now 			= time();
	$clientIp 		= $_SERVER["REMOTE_ADDR"];
	$lastSendTime 	= 0;

	// 2 小时内最后发送过的一条验证码
	$item = userGetLastValidatorCode($mobile);
	if ($item) {
		$code 		  = safe_get($item, 'sent_code');
		$lastSendTime = safe_get($item, 'sent_time');
	}

	// TODO: 限制每个手机/每个 IP 地址每天的短信发送量
	
	// 发送短信到用户手机
	$span = $now - $lastSendTime;
	if ($span > 60 * 2) {
		$params = array();
		$params['sent_code']	= $code;
		$params['mobile'] 		= $mobile;	
		$params['client_ip'] 	= $clientIp;	
		$params['sent_time'] 	= $now;	

		$ret = userSendMobileMessage($mobile, $code);
		$msg 	= safe_get($ret, 'msg');
		$result = safe_get($ret, 'code');

		userSaveValidatorCode($params, $result, $msg);	
	}

	// result
	$result = array();
	$result['ret'] = 0;
	$result['mobile'] = $mobile;
	return $result;
}

