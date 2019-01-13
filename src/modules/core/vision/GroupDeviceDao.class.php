<?php
!defined('IN_VISION') && exit('Access Denied');

/**
 * 组设备管理
 *
 */
class GroupDeviceDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id", 
			"source"		=> "source", 
			"device_id"		=> "device_id",
			"uid"			=> "uid", 
			"uri"			=> "uri", 
			"name"			=> "name", 
			"description"	=> "description", 
			"privacy"		=> "privacy", 
			"username"		=> "username", 
			"password"		=> "password", 
			"group_id"		=> "group_id"
			);

		$this->init("vision_group_devices", "id", $fields, $do);
	}
	
	public function addGroupDevice($params) {
		return parent::addEntity($params); 
	}

	public function findGroupDevices($params) {
		return parent::findEntities($params);
	}

	public function findGroupDevicesByMember($groupId, $memberId, $params = array()) {
		$pdoTemplate = $this->pdoTemplate;

        $names = $pdoTemplate->getFieldNames("a.");
		$sql = "SELECT DISTINCT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statLeftJoin('vision_member_tag_device', 'b', 'a.id', 'b.device_id');
		$sql .= $pdoTemplate->statLeftJoin('vision_member_tag_member', 'c', 'b.tag_id', 'c.tag_id');
		$sql .= ' WHERE (a.privacy=2 AND c.member_id=?) OR (a.privacy=0 AND a.group_id=?)';

		$sql .= $pdoTemplate->statOrderBy($params);
		$sql .= $pdoTemplate->statLimit($params);

		// echo $sql;
		
		$args = array();
		$args[] = $memberId;
		$args[] = $groupId;

		//echo $sql;
		//print_r($args);

		return $pdoTemplate->fetchAll($sql, $args);
	}

	public function getGroupDevice($deviceId) {
		return parent::getEntityById($deviceId);
	}
	
	public function getGroupDeviceCount($params = array()) {
		return parent::getEntityCount($params);
	}

	public function getGroupDeviceCountOfGroup($groupId) {
		$params = array();
		$this->addFilter($params, "group_id", '=', $groupId);
		return parent::getEntityCount($params);
	}

	public function getDeviceOfGroup($groupId, $deviceId) {
		$params = array();
		$this->addFilter($params, "device_id",  "=", $deviceId);
		$this->addFilter($params, "group_id", "=", $groupId);

		$devices = $this->findGroupDevices($params); 
		return (count($devices) > 0) ? $devices[0] : null;
	}

	public function getDeviceOfGroupByUID($groupId, $uid) {
		$params = array();
		$this->addFilter($params, "uid",  "=", $uid);
		$this->addFilter($params, "group_id", "=", $groupId);

		$devices = $this->findGroupDevices($params); 
		return (count($devices) > 0) ? $devices[0] : null;
	}

	public function getGroupDevicesOfGroups($groups) {
		if (!$groups || count($groups) <= 0) {
			return array();
		}

		$params = array();
		$params['count'] = 100;
		$this->addFilter($params, "group_id", "IN", $groups);
		$list = $this->findGroupDevices($params);
		return $list;
	}

	public function removeGroupDevice($id, $params = array()) {
		return parent::removeEntity($id, $params);
	}

	public function removeGroupDevices($groupId) {
		$pdoTemplate = $this->pdoTemplate;
		$table = $pdoTemplate->tableName;
		$sql = "DELETE FROM $table WHERE group_id=?";
		$values = array();
		$values[] = $groupId;
		return $pdoTemplate->executeUpdate($sql, $values);
	}

	public function updateGroupDevice($id, $form, $query = array()) {
		return parent::updateEntity($id, $form, $query);			
	}
}
