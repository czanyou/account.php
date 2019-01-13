<?php


//////////////////////////////////////////////////////////////////////////////////
// Member Tags Action
// 

function onGroupMemberTagAdd($request = array()) {
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

    $memberTagDao = MemberManager::getMemberTagDao();
    $memberTagDao->addMemberTag($data);
    
    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $data;
    return $result;
}

function onGroupMemberTagAddMember($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberId   = safe_get($request, "member_id");
    if (empty($memberId)) {
        return errorMissParamter('member_id');
    }


    $tagMemberManager = MemberManager::getGroupTagMemberDao();

    $tokens = explode(",", $memberId);
    $addCount = 0;
    foreach($tokens as $token) {
        $params = array();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $tagMemberManager->addFilter($params, "member_id",  "=", $token);        
        $count = $tagMemberManager->getEntityCount($params);
        if ($count > 0) {
            continue;
        }

        $data = array('tag_id'=>$tagId, 'member_id'=>$token);
        $tagMemberManager->addEntity($data);
        $addCount++;
    }

    MemberManager::updateGroupMemberTagCount($tagId);

    return array('ret'=>0, 'count'=>$addCount);
}

function onGroupMemberTagGetInfo($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberTagDao = MemberManager::getMemberTagDao();
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

function onGroupMemberTagList($request = array()) {
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

    $memberTagDao = MemberManager::getMemberTagDao();
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



function onGroupMemberTagRemove($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberTagDao = MemberManager::getMemberTagDao();
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
function onGroupMemberTagRemoveMember($request = array()) {
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $tagId   = safe_get($request, "tag_id");
    if (empty($tagId)) {
        return errorMissParamter('tag_id');
    }

    $memberId   = safe_get($request, "member_id");
    if (empty($memberId)) {
        return errorMissParamter('member_id');
    }

    $tagMemberManager = MemberManager::getGroupTagMemberDao();

    $removeCount = 0;
    $tokens = explode(",", $memberId);
    foreach($tokens as $token) {
        $params = array();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $tagMemberManager->addFilter($params, "member_id",  "=", $token);        
        $tagMemberManager->removeEntities($params);

        $removeCount++;
    }

    MemberManager::updateGroupMemberTagCount($tagId);

    return array('ret'=>0, 'count'=>$removeCount);
}

function onGroupMemberTagUpdate($request = array()) {
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

    $memberTagDao = MemberManager::getMemberTagDao();
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
