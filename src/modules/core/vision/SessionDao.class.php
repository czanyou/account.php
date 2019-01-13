<?php
!defined('IN_VISION') && exit('Access Denied');

/** 
 * 评论. 
 * 
 * @author ChengZhen (anyou@msn.com)
 */
class SessionDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"			=> "id", 
			"token"			=> "token",
			"user_id"		=> "user_id",
			"openkey"		=> "openkey",
			"client_id"		=> "client_id",
			"client_ip"		=> "client_ip",
			"client_name"	=> "client_name",
			"client_version"=> "client_version",
			"updated"		=> "updated"
		);

		$this->init("vision_user_sessions", "id", $fields, $do);
	}

	public function addSession($entry) {
		$now = time();
		$ip=$_SERVER["REMOTE_ADDR"];

		$entry['updated'] = $now;
		$entry['client_ip'] = $ip;
		
		return parent::addEntity($entry);
	}

	public function findSessions($params) {
		if (!isset($params['orderBy'])) {
			$params['orderBy'] = 'updated DESC';
		}
		
		return parent::findEntities($params);
	}
	
	public function getRandChar($length) {
		$str = null;
		$strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol) - 1;

		for ($i = 0; $i < $length; $i++) {
			$str .= $strPol[rand(0,$max)]; // rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	}

	public function getSession($entryId) {
		return parent::getEntityById($entryId);
	}

	public function getSessionBy($key, $value) {
		return parent::getEntityByProperty($key, $value);
	}

	public function getSessionCount($params = array()) {
		return parent::getEntityCount($params);
	}

	public function removeSession($id) {
        return parent::removeEntity($id, $params);
	}

	public function updateSession($id, $entry, $query = array()) {
		$entry['updated']   = time();
		$entry['client_ip'] = $_SERVER["REMOTE_ADDR"];

		return parent::updateEntity($id, $entry, $query);			
	}
}

?>