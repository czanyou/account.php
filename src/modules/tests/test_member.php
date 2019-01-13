<?php

require_once '../../common.php';
require_once 'common/api_group.php';
require_once 'common/api_member.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_create_user() {
	$clientId = 'test_client';

	// register
	$params = array('name'=>'test1234', 'email'=>'test1234@qq.com', 'password'=>'66668888');
	$ret = onUserRegisterWithEmail($params);
	//print_r($ret);

	// login
	$params = array('username'=>'test1234@qq.com', 'password'=>'66668888', 'client_id'=>$clientId);
	$ret = onUserLogin($params);
	//print_r($ret);

	$user = (object)$ret['data'];
	return $user;
}

function test_remove_user($user) {
	$clientId = 'test_client';

	$userId = $user->id;
	$openkey = $user->openkey;

	// remove
	$params = array('openid'=>$userId, 'client_id'=>$clientId, 'openkey'=>$openkey);
	$ret = onUserLogout($params);
	//print_r($ret);

	$userDao  = userDaoGet();
	$userDao->removeUser($userId);
}

function test_create_group($userId) {
	// add
	$params = array('openid'=>$userId, 'name'=>'test_group', 'description'=>'group desc');
	$ret = onGroupCreate($params);
	//print_r($ret);

	// list
	$params = array('openid'=>$userId);
	$ret = onGroupList($params);
	//print_r($ret);

	$list = safe_get($ret, 'data');
	$group = $list[0];

	$groupId = $group->id;

	return $group;
}

function test_remove_group($userId, $groupId) {
	// remove
	$params = array('openid'=>$userId, 'id'=>$groupId);
	$ret = onGroupRemove($params);
	//print_r($ret);
}

function test_member() {
	$user = test_create_user();
	$userId = $user->id;

	$group = test_create_group($userId);
	$groupId = $group->id;

	$params = array('group_id'=>$groupId, 'email'=>'test1234@qq.com');
	$ret = onGroupMemberAdd($params);
	print_r($ret);

	$params = array('group_id'=>$groupId);
	$ret = onGroupMemberList($params);
	//print_r($ret);

	$list = safe_get($ret, 'data');
	$member = (object)$list[0];
	$memberId = $member->id;
	print_r($member);

	$params = array('group_id'=>$groupId, 'id'=>$memberId, 'user_name'=>'test1234', 'openid'=>$userId);
	$ret = onGroupMemberUpdate($params);
	print_r($ret);

	$params = array('group_id'=>$groupId);
	$ret = onGroupMemberList($params);
	print_r($ret);

	$list = safe_get($ret, 'data');
	foreach ($list as $member) {
		$memberId = safe_get($member, 'id');	

		$params = array('group_id'=>$groupId, 'id'=>$memberId, 'openid'=>$userId);
		$ret = onGroupMemberRemove($params);
		print_r($ret);
	}

	test_remove_group($userId, $groupId);
	test_remove_user($user);
}

test_member();
