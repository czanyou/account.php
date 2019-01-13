<?php

require_once '../../common.php';
require_once 'common/api_device.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');


function test_device() {
	$uid = 'test1234';
	$openid = 100;
	$params = array('openid'=>$openid, 'name'=>'test', 'uri'=>'pppp://1234', 'uid'=>$uid);
	$ret = onPrivateDeviceAdd($params);
	print_r($ret);
	assertTrue(safe_get($ret, 'ret') == 0);

	$ret = onPrivateDeviceAdd($params);
	print_r($ret);
	assertTrue(safe_get($ret, 'ret') < 0);

	$params = array('openid'=>$openid);
	$ret = onPrivateDeviceList($params);
	print_r($ret);
	assertTrue(safe_get($ret, 'total') > 0);

	$params = array('openid'=>$openid, 'name'=>'test5', 'uri'=>'pppp://12345', 'uid'=>$uid);
	$ret = onPrivateDeviceUpdate($params);
	print_r($ret);

	$ret = onPrivateDeviceGet($uid, $openid);
	print_r($ret);
	assertEqual('test5', safe_get($ret, 'name'));

	$params = array('openid'=>$openid, 'uid'=>$uid);
	$ret = onPrivateDeviceRemove($params);
	print_r($ret);

	$ret = onPrivateDeviceGet($uid, $openid);
	print_r($ret);

	assertTrue($ret == null);
}

function test_public_device() {
	$uid = 'test1234';
	$openid = 100;
	$params = array('openid'=>$openid, 'name'=>'test', 'uri'=>'pppp://1234', 'uid'=>$uid);
	$ret = onPublicDeviceAdd($params);
	print_r($ret);
	assertTrue(safe_get($ret, 'ret') == 0);

	$ret = onPublicDeviceAdd($params);
	print_r($ret);
	//assertTrue(safe_get($ret, 'ret') < 0);

	$params = array('openid'=>$openid);
	$ret = onPublicDeviceList($params);
	//print_r($ret);
	assertTrue(safe_get($ret, 'total') > 0);

	// update
	$params = array('openid'=>$openid, 'name'=>'test5', 'uri'=>'pppp://12345', 'uid'=>$uid);
	$ret = onPublicDeviceUpdate($params);
	print_r($ret);

	$params = array('openid'=>$openid, 'uid'=>$uid);
	$ret = onPublicDeviceGet($params);
	print_r($ret);

	$data = safe_get($ret, 'data');
	assertEqual('test5', safe_get($data, 'name'));
	assertTrue($data->is_public == 1);

	// like/unlike
	$params = array('openid'=>$openid, 'uid'=>$uid);
	$ret = onPublicDeviceLike($params);
	print_r($ret);

	$params = array('openid'=>$openid, 'uid'=>$uid);
	$ret = onPublicDeviceUnlike($params);
	print_r($ret);

	// remove
	$params = array('openid'=>$openid, 'uid'=>$uid);
	$ret = onPublicDeviceRemove($params);
	print_r($ret);

	$params = array('openid'=>$openid, 'uid'=>$uid);
	$ret = onPublicDeviceGet($params);
	print_r($ret);

	$data = safe_get($ret, 'data');
	assertTrue($data->is_public == 0);
}

function test_all() {
	test_device();
	test_public_device();
}


test_public_device();
