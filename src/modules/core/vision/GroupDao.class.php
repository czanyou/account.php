<?php
!defined('IN_VISION') && exit('Access Denied');

/**
 * 组织/群组管理
 *
 */
class GroupDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id",		// 
			"member_count"	=> "member_count",
			"device_count"	=> "device_count",			
			"created"		=> "created",
			"description"	=> "description",
			"disabled"		=> "disabled",
			"name"			=> "name",			// 
			"owner_id"		=> "owner_id",
			"privacy"		=> "privacy",
			"thumbnail"		=> "thumbnail",
			"parent_id"		=> "parent_id");

		$this->init("vision_groups", "id", $fields, $do);
	}
 
	/** 创建一个新的群组. */
	public function addGroup($entry) {
		$now = time();

		$entry['created'] = $now;                        // 20010310
		return parent::addEntity($entry);
	}

	public function findGroups($params) { 
		if (!isset($params['orderBy'])) {
			$params['orderBy'] = 'privacy DESC,name ASC';
		}
		
		return parent::findEntities($params);
	}

	public function getGroup($groupId) {
		return parent::getEntityById($groupId);
	}

	public function getGroupCount($params) { 
		return parent::getEntityCount($params);
	}
	
	public function getGroups($params = array()) {
		$this->addFilter($params, "parent_id", "=", 0);
		return parent::findEntities($params);
	}

	public function getGroupsByInvite($email, $mobile) {
		$params = array();
		$pdoTemplate = $this->pdoTemplate;
		$names = $this->getFieldNames("a.");

		$sql = "SELECT DISTINCT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statJoin('vision_members', 'group_id', 'id');
		$sql .= " WHERE b.role=5 AND (b.user_mobile=? OR b.user_email=?)";

		$values = array();
		$values[] = $mobile;
		$values[] = $email;
		return $pdoTemplate->fetchAll($sql, $values); 
	}

	public function getGroupsByDevice($deviceId) {
		//
		$params = array();
		$this->addFilter($params, "b.device_id", "=", $deviceId); 

		$pdoTemplate = $this->pdoTemplate;
		$names = $this->getFieldNames("a.");
		$names .= "";
		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statJoin('vision_group_devices', 'group_id', 'id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statLimit($params);

		//echo $sql;

		return $pdoTemplate->fetchAll($sql); 
	}

	public function getGroupsByDeviceUID($uid) {
		//
		$params = array();
		$this->addFilter($params, "b.uid", "=", $uid); 

		$pdoTemplate = $this->pdoTemplate;
		$names = $this->getFieldNames("a.");
		$names .= "";
		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statJoin('vision_group_devices', 'group_id', 'id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statLimit($params);

		//echo $sql;

		return $pdoTemplate->fetchAll($sql); 
	}

	public function getGroupsByName($name) { 
		$params = array();
		$this->addFilter($params, "name", "=", $name); 
		return $this->findEntities($params);
	}

	public function getGroupsByOwner($userId) { 
		$params = array();
		$this->addFilter($params, "owner_id", "=", $userId); 
		return parent::findEntities($params);
	}

	public function getGroupsByParent($parentId) { 
		$params = array();
		$this->addFilter($params, "parent_id", "=", $parentId); 
		return parent::findEntities($params);
	}

	public function getGroupsByUID($uid) {
		$params = array();
		$this->addFilter($params, "b.uid", "=", $uid); 

		$pdoTemplate = $this->pdoTemplate;
		$names = $this->getFieldNames("a.");
		$names .= "";
		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statJoin('vision_group_devices', 'group_id', 'id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statLimit($params);

		//echo $sql;

		return $pdoTemplate->fetchAll($sql); 
	}	

	public function getGroupsOfJoined($userId) {
		$params = array();
 		$names = $this->getFieldNames("a.");
		$sql = "SELECT $names FROM vision_groups a";
		$sql.= " LEFT JOIN (vision_members b) ON (a.id = b.group_id)";
		$sql.= " WHERE b.user_id ='$userId' AND b.role<=1";

		$pdoTemplate = $this->pdoTemplate;
	    $entry = $pdoTemplate->fetchAll($sql);
		return $entry;
	}

	public function getGroupsOfPublic($params = array()) {
		$this->addFilter($params, "privacy", "=", 2);
		$this->addFilter($params, "parent_id", "=", 0);
		return parent::findEntities($params);
	}

	public function removeGroup($groupId) { 
		$params = array();
		return parent::removeEntity($groupId, $params);
	}
	
	public function updateGroup($id, $form, $query = array()) {
		return parent::updateEntity($id, $form, $query);			
	}

	public function updateGroupDeviceCount($groupId, $flag = true) {
		$groupDeviceDao = BaseService::getService("GroupDeviceDao");		
		$count = $groupDeviceDao->getGroupDeviceCountOfGroup($groupId);

		$form = array();
		$form["device_count"] = $count;
		return $this->updateGroup($groupId, $form);
	}
	
	public function updateGroupMemberCount($groupId, $flag = true) {
		$memberDao = BaseService::getService("MemberDao");		
		$count = $memberDao->getMemberCountOfGroup($groupId);

		$form = array();
		$form["member_count"] = $count;
		return $this->updateGroup($groupId, $form);
	}

	public function updateGroupThumbnail($groupId, $url) { 
		return $this->updateGroup($groupId, array('thumbnailUrl'=>$url), array());
	}
}
