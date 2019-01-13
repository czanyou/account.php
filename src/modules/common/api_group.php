<?php

require_once "common/api_common.php";
require_once "common/api_user.php";

import('core.vision.GroupDao');
import('core.vision.GroupDeviceDao');
import('core.vision.MemberDao');
import('core.vision.MemberTagDao');
import('core.vision.UserDao');

class GroupManager {
    
    public static function encodeDeviceURI($uid, $username, $password) {
		$uri = 'pppp:' . base64_encode('//' . C('site_name') . '/?uid='.$uid.'&u='.$username.'&p='.$password);
		return $uri;
	}

    public static function getGroupDao() {
        return BaseService::getService("GroupDao");
    }

    public static function getGroupDeviceDao() {
        return BaseService::getService("GroupDeviceDao");
    }

    public static function getMemberDao() {
        return BaseService::getService("MemberDao");
    }

    public static function getMemberTagDao() {
        return BaseService::getService("MemberTagDao");
    }

    public static function getGroupTagDeviceDao() {
        global $groupTagDeviceManager;

        if ($groupTagDeviceManager) {
            return $groupTagDeviceManager;
        }

        $fields = array(
            "id"            => "id", 
            "device_id"     => "device_id",
            "tag_id"        => "tag_id"
        );

        $pdoUtils = PdoUtils::getInstance();
        $baseDao = new BaseService();
        $baseDao->init("vision_member_tag_device", "id", $fields, $pdoUtils);

        $groupTagDeviceManager = $baseDao;
        return $baseDao;
    }

    public static function getGroupDeviceList($group, $userId, $count = 4) {
        $devices = array();
        $total   = 0;
        $groupId = $group->id;

        $params = array();
        $params['count'] = $count;

        //print_r($groupId);
        //print_r($userId);
        //print_r($group->owner_id);
        //print_r($count);

        $groupDeviceDao = GroupManager::getGroupDeviceDao();   
        if ($group->owner_id == $userId) {
            $groupDeviceDao->addFilter($params, 'group_id', '=', $groupId);
            $devices = $groupDeviceDao->findGroupDevices($params);
            $total   = $groupDeviceDao->getGroupDeviceCount($params);

        } else {
            // 检查是否已是该群组成员
            $memberDao = GroupManager::getMemberDao();
            $member = $memberDao->getMemberOfGroup($groupId, $userId);
            //echo $member, "/", $groupId, "/", $userId ;

            if (!empty($member) && $member->role < 2) {
                $group->is_join = 1;
            }
            if ($group->is_join) {
                $memerId = $member->id;
                $devices = $groupDeviceDao->findGroupDevicesByMember($groupId, $memerId, $params);
                $total = count($devices);
            }
        }

        return $devices;
    }

    /**
     * 返回指定的摄像机信息
     * @param itemId 要查询的摄像机的 ID
     */
    public static function getGroupDevice($itemId) {
        $groupDeviceDao = GroupManager::getGroupDeviceDao();
        return $groupDeviceDao->getGroupDevice($itemId);
    }

    /**
     *
     */
    public static function group2Array($group) {
        $data = array();
        $data['id']             = (int)$group->id;
        $data['member_count']   = (int)$group->member_count;
        $data['device_count']   = (int)$group->device_count;
        $data['created']        = (int)$group->created;
        $data['owner_id']       = (int)$group->owner_id;
        $data['privacy']        = (int)$group->privacy;
        $data['is_public']      = (int)($group->privacy == 2) ? 1 : 0;

        if ($group->is_join) {
            $data['is_join']    = (int)$group->is_join;
        }

        $data['description']    = $group->description ? $group->description : '';
        $data['name']           = $group->name        ? $group->name : '';
        $data['thumbnail']      = $group->thumbnail   ? $group->thumbnail : '';
        return $data;
    }

    public static function groupDevice2Array(&$device) {
        $uid        = $device->uid;
        $password   = $device->password;
        $username   = $device->username;

        $data = array();
        $data['id']             = (int)$device->id;
        $data['privacy']        = (int)$device->privacy;
        $data['group_id']       = (int)$device->group_id;
        $data['views']          = (int)$device->views;    

        $data['name']           = $device->name;
        $data['uid']            = $device->uid;
        $data['uri']            = GroupManager::encodeDeviceURI($uid, $username, $password);

        $now = floor(time() / 3600);
        $url = $device->uid . ".jpg?q=" . $now;

        $host = C('site_name');
        $data['cover']          = 'http://'.$host.'/chinav/cover/' . $url;

        return $data;
    }

    public static function isGroupOwner($groupId, $userId) {
        // check group
        $groupDao = GroupManager::getGroupDao();
        $group = $groupDao->getGroup($groupId);
        if (empty($group)) {
            return false;
        }

        return $group->owner_id == $userId;
    }

    /** 删除指定摄像机相关的所有标签. */
    public static function removeDeviceTagsOfGroup($deviceId) {
        $params = array();

        $tagDeviceManager = GroupManager::getGroupTagDeviceDao();
        $tagDeviceManager->addFilter($params, "device_id",  "=", $deviceId);
        $tagDeviceManager->removeEntities($params);
    }

    /** 删除指定标签相关的所有摄像机. */
    public static function removeGroupMemberTagDevices($tagId) {
        $params = array();

        $tagDeviceManager = GroupManager::getGroupTagDeviceDao();
        $tagDeviceManager->addFilter($params, "tag_id",  "=", $tagId);
        $tagDeviceManager->removeEntities($params);
    }
}

function groupGetDefault($user) {
    $defaultGroupId = null;
    $openid = null;
    
    if (is_array($user)) {
        $openid = safe_get($user, 'id');

    } else {
        $openid = $user;
        $user = userGet($openid);
        if (!$username) {
            return null;
        }

        $defaultGroupId = safe_get($user, 'group_id');
    }

    // created groups
    $groupDao = GroupManager::getGroupDao();
    if ($defaultGroupId) {
        $group = $groupDao->getGroup($defaultGroupId);
        if ($group) {
            return $group;
        }
    }

    // created groups
    $groups = $groupDao->getGroupsByOwner($openid);
    foreach ($groups as $group) {
        if ($group->privacy != 2) {
            continue;
        }

        return $group;
    }

    // joined groups
    $groups = $groupDao->getGroupsOfJoined($openid);
    foreach ($groups as $group) {
        if ($group->privacy != 2) {
            continue;
        }

        return $group;
    }

    return null;
}

//////////////////////////////////////////////////////////////////////////////////
// Groups Action
// 

/** 
 * 创建一个新群组. 
 * @param name 要创建的分组的名称，不超过16个汉字，32个半角字符。
 * @param description 要创建的分组的描述，不超过70个汉字，140个半角字符。
 * @param parent_id 要创建的子组所属的分组
 * 
 * 每个用户最多能够创建20个分组；
 * 重复创建同名分组将给出错误；
 */
function onGroupCreate($request = array()) {
    if (!userIsLogin($request)) {
        return errorNotLogin();
    }

    // check parameters
    $openid      = safe_get($request, "openid");
    $name        = safe_get($request, "name");
    $description = safe_get($request, "description");

    if (empty($openid)) {
        return errorMissParamter('openid');

    } else if (empty($name)) {
        return errorMissParamter('name');
    }

    // check name duplicate
    $groupDao = GroupManager::getGroupDao();
    $groups = $groupDao->getGroupsByName($name);
    if (count($groups) > 0) {
        return errorNameDuplicate('name');
    }

    // check created group count
    // TODO: ...

    // add
    $params = array();
    $params['owner_id']     = $openid;
    $params['name']         = $name;
    $params['description']  = $description;
    $group = $groupDao->addGroup($params);
    
    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $group;
    return $result;
}

/**
 * 
 * @param id 
 */
function onGroupGetInfo($request = array()) {
    // parameters
    $groupId = safe_get($request, "id");
    $uid     = safe_get($request, "uid");
    $code    = safe_get($request, "code");    
    if (empty($groupId)) {
        if (!empty($code)) {
            $groupId = $code;

        } else if (empty($uid)) {
            return errorMissParamter('id');
        }
    }

    // 是否存在该群组
    $groupDao = GroupManager::getGroupDao();
    if (empty($groupId)) {
        $groups = $groupDao->getGroupsByUID($uid);
        // print_r($groups);
        $group = count($groups) > 0 ? $groups[0] : null;

    } else {
        $group = $groupDao->getGroup($groupId);
    }


    if (empty($group)) {
        return errorNotExists('group');
    }

    $userId = safe_get($request, "openid");
    if (!empty($userId)) {
        // 检查是否已是该群组成员
        $memberDao = GroupManager::getMemberDao();
        $member = $memberDao->getMemberOfGroup($groupId, $userId);
        if (!empty($member) && $member->role < 2) {
            $group->is_join = 1;
        }
    }

    $result = array();
    $result['ret']  = 0;
    $result['data'] = GroupManager::group2Array($group);
    return $result;
}

/** 
 * 返回当前用户创建和加入的群组. 
 * @param device_id 如果指定了设备 ID, 则返回所有这个设备分享到的群组
 */
function onGroupList($request = array()) {
    $groupDao = GroupManager::getGroupDao();

    $openid   = safe_get($request, "openid");
    $deviceId = safe_get($request, "device_id");
    $uid      = safe_get($request, "uid");

    $params = array();

    $host = C('site_name');
    $default_url = 'http://'.$host.'/chinav/cover/cover3.jpg';

    $data = array();
    if ($uid) {
        // shared groups
        $groups = $groupDao->getGroupsByDeviceUID($uid);
        foreach ($groups as $group) {
            if (empty($group->thumbnail)) {
                $group->thumbnail = $default_url;
            }
            $data[] = $group;
        }

    } else if ($deviceId) {
        // shared groups
        $groups = $groupDao->getGroupsByDevice($deviceId);
        foreach ($groups as $group) {
            if (empty($group->thumbnail)) {
                $group->thumbnail = $default_url;
            }
            $data[] = $group;
        }      

    } else if (userIsAdmin($request)) {
        // created groups
        $params = array();
        $groups = $groupDao->getGroups($params);
        foreach ($groups as $group) {
            if (empty($group->thumbnail)) {
                $group->thumbnail = $default_url;
            }
            $data[] = $group;
        }

    } else if ($openid) {
        // 是否存在该用户
        $userDao = BaseService::getService("UserDao");
        $user = $userDao->getUser($openid);
        if (empty($user)) {
            return errorNotExists('user');
        }

        //print_r($user);
        $email  = $user->email;
        $mobile = $user->mobile;

        $inviteGroups = $groupDao->getGroupsByInvite($email, $mobile);

        // created groups
        $groups = $groupDao->getGroupsByOwner($openid);
        foreach ($groups as $group) {
            if (empty($group->thumbnail)) {
                $group->thumbnail = $default_url;
            }
            $data[] = $group;
        }

        // joined groups
        $groups = $groupDao->getGroupsOfJoined($openid);
        foreach ($groups as $group) {
            if (empty($group->thumbnail)) {
                $group->thumbnail = $default_url;
            }
            $data[] = GroupManager::group2Array($group);
        }

    } else {
        return errorMissParamter('openid');
    }
  
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $data;
    $result['invites'] = $inviteGroups;
    return $result;
}

/** 
 * 返回所有的公开的群组. 
 */
function onGroupListPublic($request = array()) {
    $groupDao = GroupManager::getGroupDao();

    $host = C('site_name');
    $default_url = 'http://'.$host.'/chinav/cover/cover3.jpg?q=1';

    $params = array();
    $data = array();

    $groups = $groupDao->getGroupsOfPublic($params);
    
    foreach ($groups as $group) {
        if (empty($group->thumbnail)) {
             $group->thumbnail = $default_url;
        }
        $data[] = GroupManager::group2Array($group);
    }


    $result = array();
    $result['ret']  = 0;
    $result['data'] = $data;
    return $result;
}

/** 
 * 删除指定的群组. 只能删除自己创建的分组
 * @param id 要删除的分组 ID
 */
function onGroupRemove($request = array()) {
    if (!userIsLogin($request)) {
        return errorNotLogin();
    }

    // check parameters
    $groupId = safe_get($request, "id");
    if (empty($groupId)) {
        return errorMissParamter('id');
    }

    $openid = safe_get($request, "openid");

    // check group
    $groupDao = GroupManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if ($group == null) {
        return errorNotExists('group');

    } else if ($openid != $group->owner_id) {
        if (!userIsAdmin($request)) {
            return errorNotYourOwn('group');
        }

    } else if ($group->privacy == 2) {
         return errorNotYourOwn('group');
    }

    // groups
    $groups = $groupDao->getGroupsByParent($groupId);
    if (count($groups)) {
        return array('ret'=>-1, 'error'=>'group ('.$name.') have subgroups');
    }

    // remove members
    $memberDao = GroupManager::getMemberDao();
    $memberDao->removeMembersOfGroup($groupId);

    // remove devices
    $groupDeviceDao = GroupManager::getGroupDeviceDao();
    $groupDeviceDao->removeGroupDevices($groupId);

    $memberTagDao = GroupManager::getMemberTagDao();
    $memberTagDao->addFilter($params, 'group_id', '=', $groupId);
    $tags = $memberTagDao->findMemberTags($params);

    foreach ($tags as $tag) {
        GroupManager::removeGroupMemberTagDevices($tag->id);
        groupMemberTagRemoveMembers($tag->id);
    }

    // remove tags
    $memberTagDao->removeMemberTagsOfGroup($groupId);

    // remove tag_device
    // remove tag_member

    // remove group
    $result = $groupDao->removeGroup($groupId);
    $result['ret']  = 0;
    return $result;
}

function onGroupSetDefault($request = array()) {
    if (!userIsLogin($request)) {
        return errorNotLogin();
    }

    // check parameters
    $openid      = safe_get($request, "openid");
    $groupId     = safe_get($request, "id");
    if (empty($groupId)) {
       return errorMissParamter('id');
    }

    // check group
    $groupDao = GroupManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if ($group == null) {
        return errorNotExists('group');
    }

    // check user
    $userDao  = getUserDao();
    $user = $userDao->getUser($openid);
    if ($user == null) {
        return errorNotExists('user');
    }

    $userDao->updateUser($openid, array('default_group' => $groupId));

    $result = array();
    $result['ret']  = 0;
    $result['default_group']  = $groupId;
    $result['data']  = $group;
    return $result;        
}

/** 
 * 更新指定的群组的信息. 只能更新自己创建的群组
 * @param id 要更新的群组的 ID, 
 */
function onGroupUpdate($request = array()) {
	if (!userIsLogin($request)) {
		return errorNotLogin();
	}

	// check parameters
	$openid 	 = safe_get($request, "openid");
	$groupId     = safe_get($request, "id");
    if (empty($groupId)) {
	   return errorMissParamter('id');
    }
    
    // check group
    $groupDao = GroupManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if ($group == null) {
    	return errorNotExists('group');

    } else if ($openid != $group->owner_id) {
        if (!userIsAdmin($request)) {
    	   return errorNotYourOwn('group');
        }
    }

    // update
    $params = array();
    safeSetParams($params, $request, 'description');
    safeSetParams($params, $request, 'name');

    // 只有管理员可以修改认证类型
    if (userIsAdmin($request)) {
        safeSetParams($params, $request, 'privacy');
    }

    $group = $groupDao->updateGroup($groupId, $params);
    
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $group;
    return $result;
}
