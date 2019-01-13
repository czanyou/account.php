<?php

require_once '../../common.php';
require_once 'common/api_user.php';
require_once 'common/api_group.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_user_login() {
	testCase('test_user_login');

	$clientId = 'test_client';
	$EMAIL_ADDRESS = 'test1234@qq.com';
	$OLD_PASSWORD = '66668888';
	$NEW_PASSWORD = '77778888';
	$MOBILE = '13899998888';

	// login
	$params = array('username'=>$EMAIL_ADDRESS, 'password'=>$OLD_PASSWORD, 'client_id'=>$clientId);
	$ret = onUserLogin($params);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);
	
	$user = (object)$ret['data'];
	assertNotNull($user);

	//print_r($user);

	assertEqual($EMAIL_ADDRESS, $user->email);
	assertEqual('test1234', $user->name);
	assertEqual($MOBILE, $user->mobile);


	$userId = $user->id;
	$openkey = $user->openkey;

	assertNotNull($userId);
	assertNotNull($openkey);

	return $user;
}

function test_user_edit_password($userId) {
	$clientId = 'test_client';
	$EMAIL_ADDRESS = 'test1234@qq.com';
	$OLD_PASSWORD = '66668888';
	$NEW_PASSWORD1 = '77779999';
	$NEW_PASSWORD2 = '77778888';
	$MOBILE = '13899998888';

	testCase('test_user_edit_password');

	$params = array();
	$params['sent_code']	= $OLD_PASSWORD;
	$params['mobile'] 		= $MOBILE;	
	$params['client_ip'] 	= 'test';	
	$params['sent_time'] 	= time();	
	userSaveValidatorCode($params, 0, 'test');

	// reset password
	$params = array('openid'=>$userId, 'code'=>$OLD_PASSWORD, 'new_password'=>$NEW_PASSWORD1);
	$ret = onUserEditPassword($params);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	// reset password
	$params = array('openid'=>$userId, 'old_password'=>$NEW_PASSWORD1, 'new_password'=>$NEW_PASSWORD2);
	$ret = onUserEditPassword($params);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);
	//print_r($ret);
}


function test_user_edit($userId) {
	$clientId = 'test_client';
	$EMAIL_ADDRESS = 'test1234@qq.com';
	$OLD_PASSWORD = '66668888';
	$NEW_PASSWORD = '77778888';

	testCase('test_user_edit');

	// edit
	$params = array('openid'=>$userId, 'about_me'=>'test_12345');
	$ret = onUserEdit($params);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);
	//print_r($ret);

	$params = array('user_id'=>$userId);
	$ret = onUserGetInfo($params);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);

	//print_r($ret);
	$user = (object)$ret['data'];
	assertNotNull($user);
	//print_r($user);
	assertEqual('test1234', $user->name);
	assertEqual('test_12345', $user->about_me);

}

function test_user_logout($userId, $openkey) {
	testCase('test_user_logout');

	$clientId = 'test_client';
	$EMAIL_ADDRESS = 'test1234@qq.com';
	$OLD_PASSWORD = '66668888';
	$NEW_PASSWORD = '77778888';

	// 
	$params = array('openid'=>$userId, 'client_id'=>$clientId, 'openkey'=>$openkey);
	$ret = onUserLogout($params);
	//print_r($ret);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);

	// relogin
	$params = array('username'=>$EMAIL_ADDRESS, 'password'=>$NEW_PASSWORD);
	$ret = onUserLogin($params);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);

	//print_r($ret);
	$user = (object)$ret['data'];
	assertNotNull($user);

	$userId = $user->id;
	assertNotNull($userId);
}

function test_user() {
	$clientId = 'test_client';
	$EMAIL_ADDRESS = 'test1234@qq.com';
	$OLD_PASSWORD = '66668888';
	$NEW_PASSWORD = '77778888';	
	$MOBILE = '13899998888';

	// register
	testCase('用户');
	$params = array('name'=>'test1234', 'email'=>$EMAIL_ADDRESS, 'mobile'=>$MOBILE, 'password'=>$OLD_PASSWORD);
	$ret = onUserRegisterWithEmail($params);
	//print_r($ret);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);

	$ret = onUserRegisterWithEmail($params);
	assertNotNull($ret);
	assertEqual(-4001, $ret['ret']);

	$user = test_user_login();
	$userId = $user->id;
	$openkey = $user->openkey;

	test_user_edit_password($userId);
	test_user_edit($userId);
	test_user_logout($userId, $openkey);

	testCase('test_user_remove');

	// remove
	$userDao  = userDaoGet();
	$ret = $userDao->removeUser($userId);
	assertNotNull($ret);
	assertEqual(0, $ret['ret']);

	// relogin
	$params = array('username'=>$EMAIL_ADDRESS, 'password'=>$NEW_PASSWORD);
	$ret = onUserLogin($params);
	assertNotNull($ret);
	assertEqual(-401, $ret['ret']);
}

echo $_SCONFIG['site_name'];
echo C('site_name');

//test_user();
//testReport();
