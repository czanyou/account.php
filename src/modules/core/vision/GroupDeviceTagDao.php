<?php
!defined('IN_VISION') && exit('Access Denied');

/**
 * 组成员标签管理, 标签用来标注一个群组内某类型的多个成员, 可相当于成员分组. 
 * 主要是为了方便权限管理.
 */
class GroupDeviceTagDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id", 
			"name"			=> "name",
			"group_id"		=> "group_id",
			"device_count"	=> "device_count"
		);

		parent::init("vision_group_device_tags", "id", $fields, $do);
	}
	
	public function addDeviceTag($params) {
		return parent::addEntity($params); 
	}

	public function findDeviceTags($params) {
		return parent::findEntities($params);
	}

	public function findDeviceTagsOfDevice($params) {
		$pdoTemplate = $this->pdoTemplate;

        $names = $pdoTemplate->getFieldNames("a.");

		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statLeftJoin('vision_group_device_tag_device', 'b', 'a.id', 'b.tag_id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statOrderBy($params);
		$sql .= $pdoTemplate->statLimit($params);
		
		return $pdoTemplate->fetchAll($sql);
	}

	public function getDeviceTagCount($params = array()) {
		return parent::getEntityCount($params);
	}

	public function getDeviceTagCountOfGroup($groupId) {
		$params = array();
		$this->addFilter($params, "group_id", '=', $groupId);
		return parent::getEntityCount($params);
	}

	public function getDeviceTag($memberId, $params = array()) {
		return parent::getEntityById($memberId);
	}

	public function getDeviceTagBy($key, $value) {
		return parent::getEntityByProperty($key, $value);
	}

	public function getDeviceTagsOfDevice($groupDeviceId) {
		$params = array();
		$this->addFilter($params, "device_id", "=", $groupDeviceId);
		return $this->findDeviceTagsOfDevice($params); 
	}

	public function getDeviceTagsOfGroup($groupId) {
		$params = array();
		$this->addFilter($params, "group_id", "=", $groupId);
		return $this->findDevices($params); 
	}

	public function removeDeviceTagsOfGroup($groupId) {
		$pdoTemplate = $this->pdoTemplate;
		$table = $pdoTemplate->tableName;
		$sql = "DELETE * FROM $table WHERE group_id=?";
		$values = array();
		$values[] = $groupId;
		return $pdoTemplate->executeUpdate($sql, $values);
	}

	public function removeDeviceTag($id, $params = array()) {
		return parent::removeEntity($id, $params);
	}

	public function updateDeviceTag($id, $form, $params = array()) {
		return parent::updateEntity($id, $form, $params);
	}
	
}
