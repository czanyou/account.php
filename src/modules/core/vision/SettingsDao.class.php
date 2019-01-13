<?php
!defined('IN_VISION') && exit('Access Denied');

/** 
 * 系统参数. 
 * 
 * @author ChengZhen (anyou@msn.com)
 */
class SettingsDao extends BaseService {

	function __construct($do) {
		$fields = array(
			"id"		=> "id", 
			"name"		=> "name",
			"value"		=> "value"
		);

		$this->init("vision_settings", "id", $fields, $do);
	}

	public function addSetting($entry) {
		return parent::addEntity($entry);
	}

	public function findSettings($params = array()) {
		$params['orderBy'] = 'name';
		return parent::findEntities($params);
	}
	
	public function getSetting($id) {
		return parent::getEntityById($id);
	}

	public function removeSetting($id, $params = array()) {
        return parent::removeEntity($id, $params);
	}

	public function updateSetting($id, $entry, $params = array()) {
        return parent::updateEntity($id, $entry, $params);
	}
}

?>