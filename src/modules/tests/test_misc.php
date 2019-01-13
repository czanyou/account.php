<?php

require_once '../../common.php';
require_once 'common/api_misc.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_misc() {
	$params = array();
	$ret = onVersionCheckApplication($params);
	print_r($ret);

	$params = array();
	$ret = onVersionCheckFrmware($params);
	print_r($ret);	
}


test_misc();
