<?php
!defined('IN_VISION') && exit('Access Denied');

/**
 * 组成员管理
 *
 */
class MemberDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id", 
			"user_id"		=> "user_id",
			"group_id"		=> "group_id",
			"user_email"	=> "user_email",
			"user_mobile"	=> "user_mobile",
			"user_name"		=> "user_name",
			"role"			=> "role"
			);

		$this->init("vision_members", "id", $fields, $do);
	}
	
	public function addMember($params) {
		return parent::addEntity($params); 
	}

	public function findMembers($params) {
		$pdoTemplate = $this->pdoTemplate;

        $names = $pdoTemplate->getFieldNames("a.");
		$names .= ", b.name as name, b.email as email, b.mobile as mobile";
		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statJoin('vision_users', 'id', 'user_id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statOrderBy($params);
		$sql .= $pdoTemplate->statLimit($params);
		
		//print_r($sql);
		return $pdoTemplate->fetchAll($sql);
	}

	public function findMembersOfTag($params) {
		$pdoTemplate = $this->pdoTemplate;

        $names = $pdoTemplate->getFieldNames("a.");
		$names .= ", b.name as name, b.email as email, b.mobile as mobile";

		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statLeftJoin('vision_users', 'b', 'b.id', 'a.user_id');
		$sql .= $pdoTemplate->statLeftJoin('vision_member_tag_member', 'c', 'c.member_id', 'a.id');
		$sql .= $pdoTemplate->statWhere($params);
		$sql .= $pdoTemplate->statOrderBy($params);
		$sql .= $pdoTemplate->statLimit($params);
		
		return $pdoTemplate->fetchAll($sql);
	}

	public function getMembersOfInvited($groupId, $email, $mobile) {
		$pdoTemplate = $this->pdoTemplate;

		$sql = 'SELECT *';
		$sql .= $pdoTemplate->statTable($params); 
		$sql .= ' WHERE group_id =? AND role=5';
		$sql .= ' AND (user_email=? OR user_mobile=?)';

		$values = array();
		$values[] = $groupId;
		$values[] = $email;
		$values[] = $mobile;

		return $pdoTemplate->fetchAll($sql, $values);
	}

	public function getMember($memberId, $params = array()) {
		$pdoTemplate = $this->pdoTemplate;
        $names = $pdoTemplate->getFieldNames("a.");
		$names .= ", b.name as name, b.email as email";
		$sql = "SELECT $names";
		$sql .= $pdoTemplate->statTable($params);
		$sql .= $pdoTemplate->statJoin('vision_users', 'id', 'user_id');
		$sql .= ' WHERE a.id='.$memberId;
		$sql .= $pdoTemplate->statLimit($params); 
		$result = $pdoTemplate->fetchAll($sql);
		return $result[0];
	}

	public function getMemberBy($key, $value) {
		return parent::getEntityByProperty($key, $value);
	}

	public function getMemberOfGroup($groupId, $userId) {
		$params = array();
		$this->addFilter($params, "user_id",  "=", $userId);
		$this->addFilter($params, "group_id", "=", $groupId);
		$this->addFilter($params, "role", "<", 2);

		$members = $this->findMembers($params); 
		return (count($members) > 0) ? $members[0] : null;
	}

	public function getMemberOfGroupBy($groupId, $key, $value) {
		$params = array();
		$this->addFilter($params, $key,  "=", $value);
		$this->addFilter($params, "group_id", "=", $groupId);

		$members = $this->findMembers($params); 
		return (count($members) > 0) ? $members[0] : null;
	}
	
	public function getMembersOfTag($tagId) {
		$params = array();
		$this->addFilter($params, "c.tag_id",  "=", $tagId);
		return $this->findMembersOfTag($params); 
	}

	public function getMemberCount($params = array()) {
		return parent::getEntityCount($params);
	}

	public function getMemberCountOfGroup($groupId) {
		$params = array();
		$this->addFilter($params, "group_id", '=', $groupId);
		return parent::getEntityCount($params);
	}

	public function removeMember($id, $params = array()) {
		return parent::removeEntity($id, $params);
	}

	public function removeMembersOfGroup($groupId) {
		$pdoTemplate = $this->pdoTemplate;
		$sql = "DELETE * from $pdoTemplate->tableName WHERE group_id=?";
		$values = array();
		$values[] = $groupId;
		return $pdoTemplate->executeUpdate($sql, $values);
	}

	public function removeMembersOfInvited($groupId, $email, $mobile) {
		$pdoTemplate = $this->pdoTemplate;

		$sql = 'DELETE FROM ';
		$sql .= $pdoTemplate->tableName;
		$sql .= ' WHERE group_id =? AND role=5';
		$sql .= ' AND (user_email=? OR user_mobile=?)';

		$values = array();
		$values[] = $groupId;
		$values[] = $email;
		$values[] = $mobile;

		return $pdoTemplate->executeUpdate($sql, $values);
	}

	public function updateMember($id, $form, $params = array()) {
		return parent::updateEntity($id, $form, $params);
	}
	
}
