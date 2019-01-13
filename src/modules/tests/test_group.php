<?php

require_once '../../common.php';
require_once 'common/api_group.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_create_user() {
	$clientId = 'test_client';

	// register
	$params = array('name'=>'test1234', 'email'=>'test1234@qq.com', 'password'=>'66668888');
	$ret = onUserRegisterWithEmail($params);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	// login
	$params = array('username'=>'test1234@qq.com', 'password'=>'66668888', 'client_id'=>$clientId);
	$ret = onUserLogin($params);
	assertEqual(0, $ret['ret']);
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
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	$userDao  = userDaoGet();
	$userDao->removeUser($userId);
}

function test_group() {
	testCase('群组');

	$user = test_create_user();
	$userId = $user->id;

	// add
	$params = array('openid'=>$userId, 'name'=>'test_group', 'description'=>'group desc');
	$ret = onGroupCreate($params);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	// list
	$params = array('openid'=>$userId);
	$ret = onGroupList($params);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	$list = safe_get($ret, 'data');
	$group = $list[0];

	$groupId = $group->id;

	// update
	$params = array('openid'=>$userId, 'id'=>$groupId, 'name'=>'test group new');
	$ret = onGroupUpdate($params);
	assertEqual(0, $ret['ret']);
	//print_r($ret);
	
	$params = array('openid'=>$userId, 'id'=>$groupId);
	$ret = onGroupGetInfo($params);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	$group = (array)$ret['data'];
	assertEqual('test group new', $group['name']);
	
	// remove
	$params = array('openid'=>$userId, 'id'=>$groupId);
	$ret = onGroupRemove($params);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	$params = array('openid'=>$userId, 'id'=>$groupId);
	$ret = onGroupGetInfo($params);
	assertEqual(-433, $ret['ret']);
	
	test_remove_user($user);
}

test_group();
testReport();

