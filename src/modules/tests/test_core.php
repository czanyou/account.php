<?php

require_once '../../common.php';
require_once 'common/api_dispatch.php';
require_once 'test_unit.php';

echo php_version_code(), "\r\n";


function test_object() {
	echo "test_object\r\n";

	$object = new Object();

	assertTrue($object);
	assertTrue($object->name == null);

	$object->name = "mike";
	assertEqual("mike", $object->name);

	$object->name = null;
	assertEqual("", $object->name);

}

function test_base_service() {
	echo "test_base_service\r\n";

	$service = new BaseService();

	assertEqual("@all", $service::ALL);
	

}

function test_pdo_template() {
	echo "test_pdo_template\r\n";

	$template = new PdoTemplate();
	$template->pdo = PdoUtils::getInstance();

	// init
	$fields = array(
		"id"	=> "id", 
		"name"	=> "name"
	);
	$template->fields 		= $fields;
	$template->primaryKey 	= "id";
	$template->tableName 	= "test_table";

	// add
	$params = array('name' => 'lucy');
	$ret = $template->addEntity(null, $params);
	print_r($ret);

	// 
	$ret = $template->getEntity('name', 'lucy');
	assertEqual('lucy', $ret->name);
	$id = $ret->id;

	$ret = $template->getEntityCount();
	assertEqual(1, $ret);

	$ret = $template->updateEntity($id, array('name'=>'Lucy J.'));
	print_r($ret);

	$ret = $template->getEntityById($id);
	assertEqual('Lucy J.', $ret->name);

	$ret = $template->removeEntity($id);
	print_r($ret);

	$ret = $template->getEntityCount();
	assertEqual(0, $ret);
}

function test_pdo_utils() {
	echo "test_pdo_utils\r\n";

	$pdo = PdoUtils::getInstance();

	$tableName = "test_table";

	// insert
	$sql = "INSERT INTO $tableName (name) VALUES ('baby');";
	//echo($sql);
	$ret = $pdo->addItem($sql);
	print_r($ret);

	// get
	$sql = "SELECT * FROM $tableName WHERE name='baby';";
	$ret = $pdo->queryItem($sql);
	print_r($ret);	

	// list
	$sql = "SELECT * FROM $tableName;";
	$ret = $pdo->listItems2OBJ($sql);
	print_r($ret);

	// remove
	$sql = "DELETE FROM $tableName;";
	$ret = $pdo->deleteItem($sql);
	print_r($ret);
}

function test_php_template() {
	echo "test_php_template\r\n";

	$template = new Template();
	$template->assign('a1', 'a1111');
	$template->assign('b1', 'b11111');

	$module = 'tests';
	$view = 'test';
	$ret = $template->output($view.'.view.htm', $module);
	print_r($ret);
}

function dump_talbe($pdo, $dbName, $tableName) {
	$sql = "SELECT * FROM COLUMNS WHERE TABLE_SCHEMA='$dbName' AND TABLE_NAME='$tableName'";
	$tables = $pdo->listItems2OBJ($sql);
	print_r($tables);

}

function test_export() {
	global $_SCONFIG;

	$pdo = new PdoUtils(
				$_SCONFIG['dbhost'], 
				'information_schema', 
				$_SCONFIG['dbuser'], 
				$_SCONFIG['dbpw'], 
				$_SCONFIG['dbport']);

	$dbName = 'test';

	$sql = "SELECT * FROM TABLES WHERE TABLE_SCHEMA='$dbName'";
	$tables = $pdo->listItems2OBJ($sql);
	print_r($tables);

	dump_talbe($pdo, $dbName, 'test_table');
}

function test_all() {
	test_object();
	//test_base_service();
	//test_pdo_utils();
	//test_pdo_template();
	test_php_template();

}




test_export();
