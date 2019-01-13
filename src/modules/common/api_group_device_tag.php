<?php

//////////////////////////////////////////////////////////////////////////////////
// Member Tags Action
// 

function onGroupDeviceTagAdd($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $groupId   = safe_get($request, "group_id");
    $name    = safe_get($request, "name");

    if (empty($groupId)) {
        return errorMissParamter('group_id');

    } else if (empty($name)) {
        return errorMissParamter('name');
    }

    // 是否存在该群组
    $groupDao = MemberManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if (empty($group)) {
        return errorNotExists('group');
    }

    $data = array();
    $data['name'] = $name;
    $data['group_id'] = $groupId;

    $memberTagDao = MemberManager::getDeviceTagDao();
    $memberTagDao->addMemberTag($data);
    
    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $data;
    return $result;
}

function onGroupDeviceTagAddDevice($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberId   = safe_get($request, "uid");
    if (empty($memberId)) {
        return errorMissParamter('uid');
    }


    $tagMemberManager = MemberManager::getGroupTagDeviceDao();

    $tokens = explode(",", $memberId);
    $addCount = 0;
    foreach($tokens as $token) {
        $params = array();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $tagMemberManager->addFilter($params, "device_id",  "=", $token);        
        $count = $tagMemberManager->getEntityCount($params);
        if ($count > 0) {
            continue;
        }

        $data = array('tag_id'=>$tagId, 'device_id'=>$token);
        $tagMemberManager->addEntity($data);
        $addCount++;
    }

    MemberManager::updateGroupDeviceTagCount($tagId);

    return array('ret'=>0, 'count'=>$addCount);
}

function onGroupDeviceTagGetInfo($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberTagDao = MemberManager::getDeviceTagDao();
    $tag = $memberTagDao->getMemberTag($tagId);
    if ($tag == nil) {
        return errorNotExists('tag');
    }

    $memberDao = MemberManager::getMemberDao();
    $members = $memberDao->getMembersOfTag($tag->id);

    $data = array();
    if (is_array($members)) {
        foreach ($members as $item) {
            if (!is_object($item)) {
                continue;
            }

            $data[] = MemberManager::member2Array($item);        
        }
    }

    $tag->members = $data;

    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $tag;
    return $result;
}

function onGroupDeviceTagList($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $groupId = safe_get($request, "group_id");
    if (empty($groupId)) {
        return errorMissParamter('group_id');
    }

    // group info
    $groupDao = MemberManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if (empty($group)) {
        return errorNotExists('group');
    } 

    // group tags
    $params = array();
    $params['orderBy'] = 'name ASC';

    $memberTagDao = MemberManager::getDeviceTagDao();
    $memberTagDao->addFilter($params, 'group_id', '=', $groupId);
    $tags = $memberTagDao->findMemberTags($params);
    $total   = $memberTagDao->getMemberTagCount($params);

    $result = array();
    $result['data'] = $tags;
    $result['total'] = (int)$total;

    // ret
    $result['ret']  = 0;
    return $result;
}



function onGroupDeviceTagRemove($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberTagDao = MemberManager::getDeviceTagDao();
    $memberTagDao->removeMemberTag($tagId);

    // TODO: remove members
    // TODO: remove tag_device
    groupMemberTagRemoveDevices($tagId);
    MemberManager::removeGroupMemberTagMembers($tagId);

    // result
    $result = array();
    $result['ret']  = 0;
    return $result;
}

/**
 * 删除指定标签相关的多个群组成员
 */
function onGroupDeviceTagRemoveDevice($request = array()) {
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberId   = safe_get($request, "uid");
    if (empty($memberId)) {
        return errorMissParamter('uid');
    }

    $tagMemberManager = MemberManager::getGroupTagDeviceDao();

    $removeCount = 0;
    $tokens = explode(",", $memberId);
    foreach($tokens as $token) {
        $params = array();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $tagMemberManager->addFilter($params, "device_id",  "=", $token);        
        $tagMemberManager->removeEntities($params);

        $removeCount++;
    }

    MemberManager::updateGroupDeviceTagCount($tagId);

    return array('ret'=>0, 'count'=>$removeCount);
}

function onGroupDeviceTagUpdate($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    $name    = safe_get($request, "name");

    if (empty($tagId)) {
        return errorMissParamter('tag_id');

    } else if (empty($name)) {
        return errorMissParamter('name');
    }

    $memberTagDao = MemberManager::getDeviceTagDao();
    $tag = $memberTagDao->getMemberTag($tagId);
    if ($tag == nil) {
        return errorNotExists('tag');
    }

    $data = array();
    $data['name'] = $name;

    $memberTagDao->updateMemberTag($tagId, $data);
    
    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $tag;
    return $result;
}

