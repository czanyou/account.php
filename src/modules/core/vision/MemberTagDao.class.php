<?php
!defined('IN_VISION') && exit('Access Denied');

/**
 * 组成员标签管理, 标签用来标注一个群组内某类型的多个成员, 可相当于成员分组. 
 * 主要是为了方便权限管理.
 */
class MemberTagDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id", 
			"name"			=> "name",
			"group_id"		=> "group_id",
			"member_count"	=> "member_count"
		);

		parent::init("vision_member_tags", "id", $fields, $do);
	}
	
	public function addMemberTag($params) {
		return parent::addEntity($params); 
	}

	public function findDeviceMemberTags($params) {
		$pdoTemplate = $this->pdoTemplate;

        $names = $pdoTemplate->getFieldNames("a.");

		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statLeftJoin('vision_member_tag_device', 'b', 'a.id', 'b.tag_id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statOrderBy($params);
		$sql .= $pdoTemplate->statLimit($params);
		
		return $pdoTemplate->fetchAll($sql);
	}

	/** 查询指定的成员标签. */
	public function findMemberTags($params) {
		return parent::findEntities($params);
	}

	public function getMemberTagCount($params = array()) {
		return parent::getEntityCount($params);
	}

	public function getMemberTagCountOfGroup($groupId) {
		$params = array();
		$this->addFilter($params, "group_id", '=', $groupId);
		return parent::getEntityCount($params);
	}

	public function getMemberTag($memberId, $params = array()) {
		return parent::getEntityById($memberId);
	}

	public function getMemberTagBy($key, $value) {
		return parent::getEntityByProperty($key, $value);
	}

	public function getMemberTagsOfDevice($groupDeviceId) {
		$params = array();
		$this->addFilter($params, "device_id", "=", $groupDeviceId);
		return $this->findDeviceMemberTags($params); 
	}

	public function getMemberTagsOfGroup($groupId) {
		$params = array();
		$this->addFilter($params, "group_id", "=", $groupId);
		return parent::findEntities($params); 
	}

	public function removeMemberTagsOfGroup($groupId) {
		$pdoTemplate = $this->pdoTemplate;
		$table = $pdoTemplate->tableName;
		$sql = "DELETE * FROM $table WHERE group_id=?";
		$values = array();
		$values[] = $groupId;
		return $pdoTemplate->executeUpdate($sql, $values);
	}

	public function removeMemberTag($id, $params = array()) {
		return parent::removeEntity($id, $params);
	}

	public function updateMemberTag($id, $form, $params = array()) {
		return parent::updateEntity($id, $form, $params);
	}
	
}
