<?php

require_once 'common.php';
require_once 'common/api_dispatch.php';

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

function onApiRequest($api) {
	$result = onApiDispatch($api);

	$phpVersion = php_version_code();
	if ($phpVersion >= 5004) {
		// PHP 5.4 以上支持以原文输入中文字符
		echo json_encode($result, JSON_UNESCAPED_UNICODE);

	} else {
		echo json_encode($result);
	}
}

// check api 
$api = safe_get($_GET, "api");
if (!empty($api)) {
	onApiRequest($api);
	return;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

function onPublicRequest($path) {
	$router = array(
		'/group' 				=> 'group/group_activites.php',
		'/group/activites' 		=> 'group/group_activites.php',
		'/group/group' 			=> 'group/group.php',
		'/manager/profile'		=> 'manager/manager.php',
		'/manager/user'			=> 'manager/user_manager.php',
		'/manager/user_session'	=> 'manager/user_session_manager.php',
		'/manager/groups'		=> 'manager/groups_manager.php',
		'/manager/group_detail'	=> 'manager/group_detail.php',
		'/manager/group_devices'=> 'manager/group_devices.php',
		'/manager/group_members'=> 'manager/group_members.php',
		'/manager/group_tags'	=> 'manager/group_tags.php',
		'/manager/settings'		=> 'manager/settings_manager.php',
		'/me'					=> 'manager/manager.php'
	);

	$page = safe_get($router, $path, 'account/index.php');
	require_once($page);
}

$path = safe_get($_GET, "path");
if (!empty($path)) {
	onPublicRequest($path);
	return;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

// default home page
if (VisionIsLogin()) {
	require_once 'manager/home.php';

} else {
	require_once 'account/index.php';
}

