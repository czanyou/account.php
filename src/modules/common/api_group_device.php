<?php


//////////////////////////////////////////////////////////////////////////////////
// Group Devices Action
// 

/**
 * 添加群组摄像机
 * @param group_id 所属的群组 ID
 * @param device_id 要添加的私有摄像机 ID
 */
function onGroupDeviceAdd($request = array()) {
    // check login
	if (!userIsLogin($request)) {
		return errorNotLogin();
	}

	// check parameters
	$groupId   = safe_get($request, "group_id");
    $uid       = safe_get($request, "uid");

    if (empty($groupId)) {
		return errorMissParamter('group_id');

    } else if (empty($uid)) {
		return errorMissParamter('uid');
    }

    // 是否存在该群组
    $groupDao = GroupManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if (empty($group)) {
    	return errorNotExists('group');
    }

    // 是否存在该摄像机
    $groupDeviceDao = GroupManager::getGroupDeviceDao();

    $params = array();
    safeSetParams($params, $request, 'description');
    safeSetParams($params, $request, 'name');
    safeSetParams($params, $request, 'privacy');
    safeSetParams($params, $request, 'uid');
    safeSetParams($params, $request, 'username');
    safeSetParams($params, $request, 'password');
    safeSetParams($params, $request, 'uri');
    $device = (object)$params;

    // 检查是否已是该群组摄像机
    $groupDevice = $groupDeviceDao->getDeviceOfGroupByUID($groupId, $uid);
    if (!empty($groupDevice)) {
    	return errorAlreadyExists('device');
    }

    // add
    $params = array();
    $params['group_id']     = $groupId;
    $params['name']         = $device->name;
    $params['description']  = $device->description;
    $params['uid']          = $device->uid;
    $params['password']     = $device->password;
    $params['uri']          = $device->uri;
    $params['privacy']      = $device->privacy;
    $params['username']     = 'public';
    $groupDevice = $groupDeviceDao->addGroupDevice($params);

    // update group (device count)
    $groupDao->updateGroupDeviceCount($groupId, true);

    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $groupDevice;
    return $result;
}

function onGroupDeviceGetInfo($request = array()) {
    // check login
    if (!userIsLogin($request)) {
        return errorNotLogin();
    }

    $groupDeviceId = safe_get($request, "id");
    if (empty($groupDeviceId)) {
        return errorMissParamter('id');
    }

    $groupDeviceDao = GroupManager::getGroupDeviceDao();
    $data = $groupDeviceDao->getGroupDevice($groupDeviceId);
    if (empty($data)) {
        return errorNotExists('groupDevice');
    }

    $memberTagDao = GroupManager::getMemberTagDao();
    $tags = $memberTagDao->getMemberTagsOfDevice($groupDeviceId);
    $data->tags = $tags;

    $result = array();
    $result['ret']  = 0;
    $result['data'] = $data;
    return $result;
}

/**
 * 返回指定群组下所有摄像机
 * 同时返回群主信息, 和已加入的子组
 * @param group_id 所属的群组 ID
 */
function onGroupDeviceList($request = array()) {

    // check parameters
    $groupId = safe_get($request, "group_id");
    if (empty($groupId)) {
        return errorMissParamter('group_id');

    } else if ($groupId == "@me") {
        return onGroupDeviceListMe($request);

    } else if ($groupId == "@all") {
        return onGroupDeviceListAll($request);        
    }

    // check group
    $groupDao = GroupManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if (empty($group)) {
        return errorNotExists('group');
    }

    $devices = array();
    $total   = 0;
    $userId  = safe_get($request, "openid");

    if (VisionIsAdmin()) {
        $userId = $group->owner_id;
    }

    $data = array();

    if ($group->owner_id == $userId) {
        // find
        $params = array();
        $groupDeviceDao = GroupManager::getGroupDeviceDao();
        $groupDeviceDao->addFilter($params, 'group_id', '=', $groupId);
        $devices = $groupDeviceDao->findGroupDevices($params);
        $total   = $groupDeviceDao->getGroupDeviceCount($params);

        foreach ($devices as $device) {
            $device->username = 'admin';
            $device->password = '888888';
            $data[] = GroupManager::groupDevice2Array($device);
        }

    } else {
        // 检查是否已是该群组成员
        $memberDao = GroupManager::getMemberDao();
        $member = $memberDao->getMemberOfGroup($groupId, $userId);
        if (!empty($member) && $member->role < 2) {
            $group->is_join = 1;
        }

        if ($group->is_join) {
            $memerId = $member->id;
            $groupDeviceDao = GroupManager::getGroupDeviceDao();
            $devices = $groupDeviceDao->findGroupDevicesByMember($groupId, $memerId);
            $total = count($devices);
        }

        foreach ($devices as $device) {
            $device->username = 'public';
            $device->password = 'public';
            $data[] = GroupManager::groupDevice2Array($device);
        }
    }

    // owner info
    $owner = userGet($group->owner_id);
    if ($owner) {
        $owner['user_id'] = $owner['id'];
    }

    // result
    $result = array();
    $result['ret']       = 0;
    $result['owner']     = $owner;
    $result['group']     = GroupManager::group2Array($group);
    $result['data']      = $data;
    $result['total']     = (int)$total;
    return $result;
}

function onGroupDeviceListMe($request = array()) {
    $openid  = safe_get($request, "openid");

    $data = array();
    $groupIds = array();

    $groupDeviceDao = GroupManager::getGroupDeviceDao();
    $group = groupGetDefault($openid);
    if ($group) {
        $devices = GroupManager::getGroupDeviceList($group, $openid, 100);

        $list = array();
        foreach ($devices as $device) {
            $device->username = 'public';
            $device->password = 'public';
            $list[] = GroupManager::groupDevice2Array($device);
        }

        $item = GroupManager::group2Array($group);
        $item['devices'] = $list;
        $data[] = $item;  
    }

    $result = array();
    $result['ret']      = 0;
    $result['data']     = $data;
    return $result;
}

function onGroupDeviceListAll($request = array()) {
    $openid  = safe_get($request, "openid");

    $data = array();
    $groupIds = array();

    $groupDeviceDao = GroupManager::getGroupDeviceDao();

    // created groups
    $groupDao = GroupManager::getGroupDao();
    $groups = $groupDao->getGroupsByOwner($openid);
    foreach ($groups as $group) {
        if ($group->privacy != 2) {
            continue;
        }

        $devices = GroupManager::getGroupDeviceList($group, $openid, 6);
        $list = array();
        foreach ($devices as $device) {
            $device->username = 'public';
            $device->password = 'public';
            $list[] = GroupManager::groupDevice2Array($device);
        }

        $item = GroupManager::group2Array($group);
        $item['devices'] = $list;
        $data[] = $item;
    }

    // joined groups
    $groups = $groupDao->getGroupsOfJoined($openid);
    foreach ($groups as $group) {
        if ($group->privacy != 2) {
            continue;
        }

        $devices = GroupManager::getGroupDeviceList($group, $openid, 6);
        $list = array();
        foreach ($devices as $device) {
            $device->username = 'public';
            $device->password = 'public';
            $list[] = GroupManager::groupDevice2Array($device);
        }

        $item = GroupManager::group2Array($group);
        $item['devices'] = $list;
        $data[] = $item;
    }

    //print_r($groupIds);

    // 
    //$devices = $groupDeviceDao->getGroupDevicesOfGroups($groupIds);

    $result = array();
    $result['ret']      = 0;
    $result['data']     = $data;
    return $result;
}

/**
 * 删除群组下面指定的摄像机
 * @param group_id 要删除的摄像机所属的群组
 * @param device_id 要删除的私有摄像机的 ID
 * @param id 要删除的摄像机的 ID
 */
function onGroupDeviceRemove($request = array()) {
    // check login
	if (!userIsLogin($request)) {
		return errorNotLogin();
	}

	$groupDeviceDao = GroupManager::getGroupDeviceDao();

    // remove by ID
	$groupDeviceId = safe_get($request, "id");
	if (empty($groupDeviceId)) {
        // check parameters
        $groupId  = safe_get($request, "group_id");
        $uid      = safe_get($request, "uid");

        if (empty($groupId)) {
            return errorMissParamter('group_id');

        } else if (empty($uid)) {
            return errorMissParamter('uid');
        }

        // find
        $groupDevice = $groupDeviceDao->getDeviceOfGroupByUID($groupId, $uid);
        if (empty($groupDevice)) {;
            return array('ret'=>0);
        }

        // remove by ID
        $groupDeviceId = $groupDevice->id;

    } else {
        $groupDevice = $groupDeviceDao->getGroupDevice($groupDeviceId);
        if (empty($groupDevice)) {
            return errorNotExists('groupDevice');
        }

        $groupId = $groupDevice->group_id;
    }

    $result = $groupDeviceDao->removeGroupDevice($groupDeviceId);

    // update group (device count)
    $groupDao = GroupManager::getGroupDao();
    $groupDao->updateGroupDeviceCount($groupId, true);

    // TODO: remove tag device
    GroupManager::removeDeviceTagsOfGroup($groupDeviceId);

    // result
    $result['ret']  = 0;
    return $result;
}

/**
 * 修改群组下面的摄像机信息
 *
 */
function onGroupDeviceUpdate($request = array()) {
    // check login
    if (!userIsLogin($request)) {
        return errorNotLogin();
    }

    $groupDeviceId = safe_get($request, "id");
    if (empty($groupDeviceId)) {
        return errorMissParamter('id');
    }

    $groupDeviceDao = GroupManager::getGroupDeviceDao();
    $groupDevice = $groupDeviceDao->getGroupDevice($groupDeviceId);
    if (empty($groupDevice)) {
        return errorNotExists('groupDevice');
    }


    // update
    $params = array();
    safeSetParams($params, $request, 'description');
    safeSetParams($params, $request, 'name');
    safeSetParams($params, $request, 'privacy');

    safeSetParams($params, $request, 'username');
    safeSetParams($params, $request, 'password');
    safeSetParams($params, $request, 'uri');

    if (!empty($params)) {
        $groupDevice = $groupDeviceDao->updateGroupDevice($groupDeviceId, $params);
    }

    $result = array();
    $result['ret']  = 0;
    $result['data'] = $groupDevice;
    return $result;
}

/**
 * 修改群组下面的摄像机信息
 *
 */
function onGroupDeviceUpdateTags($request = array()) {
    // check login
    if (!userIsLogin($request)) {
       //  return errorNotLogin();
    }

    // parametes
    $groupDeviceId = safe_get($request, "id");
    if (empty($groupDeviceId)) {
        return errorMissParamter('id');
    }

    if (!isset($request['tags'])) {
        return errorMissParamter('tags');
    }

    // check group device
    $groupDeviceDao = GroupManager::getGroupDeviceDao();
    $data = $groupDeviceDao->getGroupDevice($groupDeviceId);
    if (empty($data)) {
        return errorNotExists('groupDevice');
    }

    // query last tags
    $tagDeviceManager = GroupManager::getGroupTagDeviceDao();

    $params = array();
    $tagDeviceManager->addFilter($params, "device_id",  "=", $groupDeviceId);
    $lastTags = $tagDeviceManager->findEntities($params);

    $lastTagSet = array();
    foreach ($lastTags as $tag) {
        $lastTagSet[$tag->tag_id] = $tag->id;
    }

    //echo('last:');
    //print_r($lastTagSet);

    // add/remove set
    $addTags = array();

    $tags = safe_get($request, "tags");
    $tokens = explode(",", $tags);
    $addCount = 0;
    foreach($tokens as $token) {
        if (!($token > 0)) {
            continue;
        }

        if ($lastTagSet[$token]) {
            unset($lastTagSet[$token]);

        } else {
            $addTags[] = $token;
        }
    }

    //echo('remove:');
    //print_r($lastTagSet);

    //echo('add:');
    //print_r($addTags);

    // add
    $addCount = 0;
    foreach($addTags as $tag) {
        $params = array();
        $tagDeviceManager->addFilter($params, "tag_id",  "=", $tag);
        $tagDeviceManager->addFilter($params, "device_id",  "=", $groupDeviceId);
        $count = $tagDeviceManager->getEntityCount($params);
        if ($count > 0) {
            continue;
        }

        $data = array('tag_id'=>$tag, 'device_id'=>$groupDeviceId);
        $tagDeviceManager->addEntity($data);
        $addCount++;
    }

    // remove
    $removeCount = 0;
    foreach($lastTagSet as $tag) {
        $params = array();
        $tagDeviceManager->addFilter($params, "id",  "=", $tag);     
        $tagDeviceManager->removeEntities($params);

        $removeCount++;
    }

    // result
    $result = array();
    $result['ret']  = 0;
    $result['addCount']  = $addCount;
    $result['removeCount']  = $removeCount;
    $result['data'] = $tags;
    return $result;
}

