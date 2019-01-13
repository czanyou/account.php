<?php

require_once "common/api_common.php";
require_once "common/api_group.php";
require_once "common/api_group_device.php";
require_once "common/api_group_device_tag.php";
require_once "common/api_member.php";
require_once "common/api_member_tag.php";
require_once "common/api_user.php";
require_once "common/api_misc.php";

//////////////////////////////////////////////////////////////////////////////////
// Dispatch Action
// 

/** 
 * 分发客户端 API 访问请求. 
 * @param api 客户端要访问的 API 的路径, 如 p/list_pulibc
 */
function onApiDispatch($api) {
	global $router;
 	if (!$router) {
 		// group
 		$router['g/add'] 			= onGroupCreate;
 		$router['g/del'] 			= onGroupRemove;
 		$router['g/edit'] 			= onGroupUpdate;
 		$router['g/exit'] 			= onGroupMemberExit; 		
 		$router['g/get_info'] 		= onGroupGetInfo;
 		$router['g/join'] 			= onGroupMemberJoin;
 		$router['g/list'] 			= onGroupList;
 		$router['g/list_public'] 	= onGroupListPublic;
 		$router['g/set_default'] 	= onGroupSetDefault;

 		// group device
 		$router['gd/add'] 			= onGroupDeviceAdd;
  		$router['gd/del'] 			= onGroupDeviceRemove;
  		$router['gd/edit'] 			= onGroupDeviceUpdate;
  		$router['gd/edit_tags'] 	= onGroupDeviceUpdateTags;
  		$router['gd/get_info'] 		= onGroupDeviceGetInfo;
  		$router['gd/list'] 			= onGroupDeviceList;

  		// member
		$router['m/add'] 			= onGroupMemberAdd;
		$router['m/del'] 			= onGroupMemberRemove;
		$router['m/edit'] 			= onGroupMemberUpdate;
		$router['m/list'] 			= onGroupMemberList;

		// member tag
		$router['mtag/add'] 		= onGroupMemberTagAdd;
		$router['mtag/add_member'] 	= onGroupMemberTagAddMember;
		$router['mtag/del'] 		= onGroupMemberTagRemove;
		$router['mtag/del_member'] 	= onGroupMemberTagRemoveMember;
		$router['mtag/edit'] 		= onGroupMemberTagUpdate;
		$router['mtag/get_info'] 	= onGroupMemberTagGetInfo;
		$router['mtag/list'] 		= onGroupMemberTagList;

		// device tag
		$router['dtag/add'] 		= onGroupDeviceTagAdd;
		$router['dtag/add_device'] 	= onGroupDeviceTagAddDevice;
		$router['dtag/del'] 		= onGroupDeviceTagRemove;
		$router['dtag/del_device'] 	= onGroupDeviceTagRemoveDevice;
		$router['dtag/edit'] 		= onGroupDeviceTagUpdate;
		$router['dtag/get_info'] 	= onGroupDeviceTagGetInfo;
		$router['dtag/list'] 		= onGroupDeviceTagList;		

 		// user
 		$router['u/edit'] 			= onUserEdit;
 		$router['u/edit_password'] 	= onUserEditPassword;
 		$router['u/forgot'] 		= onUserForgot;
 		$router['u/get_info'] 		= onUserGetInfo;
 		$router['u/is_login'] 		= onUserIsLogin;
 		$router['u/login'] 			= onUserLogin;
 		$router['u/logout'] 		= onUserLogout;
 		$router['u/register'] 		= onUserRegister;
 		$router['u/validator'] 		= onUserSendValidator;

 		// misc
 		$router['php/info'] 		= onPhpVersion;
 		
 	}

	$function = $router[$api];
	if (!$function) {
		return array('ret'=>-1, 'error'=>"method not found");
	}

 	$request = $_GET;
	return call_user_func($function, $request);
}

function onPhpVersion($request) {
	phpinfo();
}
