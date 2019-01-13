<?php

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --
// System Config

define('IN_VISION',			TRUE);			///< System Flags
define('S_ROOT',			dirname(__FILE__));	///< System Root Path

global $_SCONFIG;
$_SCONFIG = array();
$_SCONFIG['COOKIE_PATH']   = '/';
$_SCONFIG['COOKIE_EXPIRE'] = 3600 * 24 * 30;
$_SCONFIG['COOKIE_PREFIX'] = '';
$_SCONFIG['COOKIE_DOMAIN'] = '';
require_once(S_ROOT.DIRECTORY_SEPARATOR.'config.php');

session_start();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --
// Vision System 

header("Content-Type:text/html; charset=UTF-8");

//print_r($_SCONFIG);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --
// System Settings

//define('S_PATH',			"/v3");			///< 
define('X_RELEASE',			'20151117.6'); 	///< System Release Version
function GETX_RELEASE() { return X_RELEASE; }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --
// System Common Include Paths

define('MODULE_PATH',		S_PATH.'/modules');		///< 

$path  = PATH_SEPARATOR.S_ROOT.DIRECTORY_SEPARATOR."modules".PATH_SEPARATOR;
set_include_path(get_include_path().$path);

// System Error Report Level
if (D_BUG) {
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | 
		E_COMPILE_ERROR | E_USER_ERROR | E_USER_WARNING );
	
} else {
	error_reporting(E_ERROR);
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --
// 

// System Common Functions
require_once('core/functions.php');

// System Common Classes
import('core.util.Object');
import('core.util.PageUtils');
import('core.util.PdoUtils');
import('core.util.Template');
import('core.util.EmailUtils');
import('core.util.PdoTemplate');
import('core.util.BaseService');
import('core.util.HttpClient');
import('account.AuthUtils');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

// System Classe Autoload Function
//spl_autoload_register(array('Object', 'autoload'));

// System Global Vars
global $lang, $globalAuth, $globalViewer;

// Current View User
$globalAuth 	= new AuthUtils();
$globalViewer 	= $globalAuth->getLoginUser();

date_default_timezone_set('prc');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

global $global_paths;

/** 解析请求路径. */
function VisionParseRequestPath() {
	global $global_paths;
	
	// URL Rewrite
	$path = safe_get($_GET, "path", '/');
	if (empty($path)) {
		$path = safe_get($_POST, "path", '/');
	}

	$pathInfo = $_SERVER['PATH_INFO'];
	if (!empty($pathInfo)) {
		$path = $pathInfo;
	}
	
	$global_paths = explode('/', $path);
	return $path;
}

///////////////////////////////////////////////////////////////////////////////
// 用户和权限相关工具方法 

function VisionCheckLoginStatus() {
	if (!isset($_SESSION["vision_user_id"])) {
		header('Location: ?path=/account/login');
		exit(0);
	}
}

function VisionCheckAdminStatus() {
	if (!VisionIsAdmin()) {
		header('Location: ?path=/account/login');
		exit(0);
	}
}

function VisionGetLoginUser() {
	return safe_get($_SESSION, "vision_login_user", new Object());
}

/**
 * 指出当前登录的用户是否是超级管理员.
 * @return 如果是超级管理员则返回 true. 否则返回 false.
 * @author chengzhen (anyou@msn.com)
 */
function VisionIsAdmin() {
	global $globalViewer;
	if (safe_get($globalViewer, "name") == 'admin') {
		return true;
	}

	return false;
}

/**
 * 指出是否已经登录.
 * @return 如果已经登录则返回 true. 否则返回 false.
 * @author chengzhen (anyou@msn.com)
 */
function VisionIsLogin() {
	global $globalViewer;
	if (is_object($globalViewer) && !empty($globalViewer->email)) {
		return true;
	}

	return false;
}


