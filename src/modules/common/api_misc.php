<?php

require_once "common/api_common.php";

import('core.vision.SettingsDao');

//////////////////////////////////////////////////////////////////////////////////
// System Information Actions
// 

function systemSettingsDaoCreate() {
    return BaseService::getService("SettingsDao");
}

function systemSettingsList() {
    $settingsDao = systemSettingsDaoCreate();
    //print_r($settingsDao);

    $params = array();
    $list = $settingsDao->findSettings($params);
    //print_r($list);
    
    $settings = array();
    foreach ($list as $item) {
        $key = $item->name;
        $value = $item->value;
        $settings[$key] = $value;
    }

    return $settings;
}
