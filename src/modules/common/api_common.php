<?php

import('core.util.ImageUtils');
import('util.ImageUtils');

///////////////////////////////////////////////////////////////////////////////
// 

define('PERSON_CAMERA_MAX_COUNT',		50); 	// 个人可添加的摄像机的上限
define('OPEN_ID',						'openid');
define('VISION_BASE_URL', 				'http://' . C('site_name') . S_PATH . '/');


///////////////////////////////////////////////////////////////////////////////

define('E_ALREADY_EXISTS',				-434);  // 指定的对象已经存在了
define('E_MISS_REQUIRED_PARAM',			-431); 	// 缺少指定名称的参数
define('E_NAME_DUPLICATE',				-432);  // 指定的名称冲突了, 不能使用
define('E_NOT_EXISTS',					-433);  // 指定的对象不存在
define('E_NOT_YOUR_OWN',				-435);  // 指定的对象不属于当前用户, 没有权限查看或操作

define('E_USER_INVALID_OPEN_ID',		-441);  // 没有指定用户 ID
define('E_USER_NOT_LOGIN',				-401);  // 当前用户没有登录

///////////////////////////////////////////////////////////////////////////////
// 公共错误返回函数

function errorNotLogin() {
	return array('ret'=>E_USER_NOT_LOGIN, 'error'=>'Unauthorized (用户没用登录)');
}

function errorLoginFailed() {
	return array('ret'=>E_USER_NOT_LOGIN, 'error'=>'Invalid username or password (无效的用户名或密码).');
}

function errorMissParamter($name) {
	return array('ret'=>E_MISS_REQUIRED_PARAM, 'error'=>'Miss required parameter ('.$name.'), see doc for more info');
}

function errorNameDuplicate($name) {
	return array('ret'=>E_NAME_DUPLICATE, 'error'=>'name duplicate: ['.$name.'] (名称冲突)');
}

function errorNotExists($name) {
	return array('ret'=>E_NOT_EXISTS, 'error'=>'object ['.$name.'] not exists (对象不存在)');
}

function errorAlreadyExists($name) {
	return array('ret'=>E_ALREADY_EXISTS, 'error'=>'object ['.$name.'] already exists (对象已经存在)');
}

function errorNotYourOwn($name) {
	return array('ret'=>E_NOT_YOUR_OWN, 'error'=>'object ['.$name.'] not your own (无法操作不属于你的对象)');
}

///////////////////////////////////////////////////////////////////////////////
//

/** 只有当请求参数有指定名称的值才复制到目标对象. */
function safeSetParams(&$params, &$request, $name) {
    if (isset($request[$name])) {
        $params[$name] = safe_get($request, $name);
    }
}


function safeGetString($array, $name) {
	$value = safe_get($array, $name);
	if ($value == 'null') {
		return;
	}

	return $value;
}

function errorResultCheck($result) {
    if (empty($result)) {
        return null;
        //return array('ret'=>-1, 'message'=>'internal error, empty result.');

    } else if (safe_get($result, 'error')) {
        $code = safe_get($result, 'code');
        $message = safe_get($result, 'message');
        return array('ret'=>-$code, 'message'=>$message);
    }

    return null;
}
