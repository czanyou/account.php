<?php

require_once "common/api_common.php";
require_once "common/api_user.php";

import('core.vision.GroupDao');
import('core.vision.MemberDao');
import('core.vision.MemberTagDao');
import('core.vision.UserDao');

define('ROLE_NORMAL',   0);

class MemberManager {

    function getGroupDao() {
        return BaseService::getService("GroupDao");
    }

    function getDeviceTagDao() {
        return BaseService::getService("GroupDeviceTagDao");
    }

    function getMemberDao() {
        return BaseService::getService("MemberDao");
    }

    function getMemberTagDao() {
        return BaseService::getService("MemberTagDao");
    }

    function getUserDao() {
        return BaseService::getService("UserDao");
    }

    function isGroupManager() {
        return true;
    }

    function getGroupTagMemberDao() {
        global $groupTagMemberManager;

        if ($groupTagMemberManager) {
            return $groupTagMemberManager;
        }

        $fields = array(
            "id"            => "id", 
            "member_id"     => "member_id",
            "tag_id"        => "tag_id"
        );

        $pdoUtils = PdoUtils::getInstance();
        $baseDao = new BaseService();
        $baseDao->init("vision_member_tag_member", "id", $fields, $pdoUtils);

        $groupTagMemberManager = $baseDao;
        return $baseDao;
    }

    function getGroupTagDeviceDao() {
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
        $baseDao->init("vision_group_device_tag_device", "id", $fields, $pdoUtils);

        $groupTagDeviceManager = $baseDao;
        return $baseDao;
    }  

    /** 输入摄像机的信息. 类型会合理转换, 有些信息会适当地隐藏. */
    function member2Array(&$member) {
        $data = array();
        $data['role']           = (int)$member->role;
        $data['id']             = (int)$member->id;
        $data['likes']          = (int)$member->likes;
        $data['user_id']        = (int)$member->user_id;
        $data['group_id']       = (int)$member->group_id;

        // print_r($member);

        // mobile
        $mobile = '';
        if (!empty($member->mobile)) {
            $mobile = substr_replace($member->mobile, "****", 3, 4);

        } else if (!empty($member->user_mobile)) {
            $mobile = substr_replace($member->user_mobile, "****", 3, 4);
        }

        // display name
        $name = $member->name;
        if (empty($name)) {
            if (!empty($member->user_name)) {
                $name = $member->user_name;

            } else if (!empty($mobile)) {
                $name = $mobile;

            } else {
                $name = $member->id;
            }
        }

        if ($member->user_email ) { $data['user_email']   = $member->user_email; }
        if ($member->user_name  ) { $data['user_name']    = $member->user_name; }
        if ($member->email      ) { $data['email']        = $member->email; }

        if ($name  ) { $data['name']         = $name; }
        if ($mobile) { $data['mobile']       = $mobile; }

        return $data;
    }

    function addGroupMember($groupId, $name, $key, $value, $userKey = null) {
        //print_r($tokens);
        //echo $key,'/',$value,'/',$userKey,';   ';

        // 是否存在该用户
        $userDao  = MemberManager::getUserDao();
        $user = null;
        if (!empty($value)) {
            $user = $userDao->getUserBy($key, $value);
        }

        if (!empty($user)) {
            $userId = $user->id;
            if ($userId == $group->owner_id) {
                return array('error'=>'要添加的成员已经存在了');
            }

            // 检查是否已是该群组成员
            $memberDao = MemberManager::getMemberDao();
            $member = $memberDao->getMemberOfGroup($groupId, $userId);
            if (!empty($member)) {
                return array('error'=>'要添加的成员已经存在了');
            }

            // add
            $params = array();
            $params['group_id']     = $groupId;
            $params['user_id']      = $userId;
            $params['role']         = 0;

            if (!empty($name)) {
                $params['user_name'] = $name;
            }

            $member = $memberDao->addMember($params);
            return $member['data'];

        } else if (!empty($userKey)) {
            $memberDao = MemberManager::getMemberDao();
            $member = $memberDao->getMemberOfGroupBy($groupId, $userKey, $value);
            if ($member != null) {
                return array('error'=>'要添加的成员已经存在了');
            }

            // add
            $params = array();
            $params['group_id']     = $groupId;
            $params['user_id']      = 0;
            $params['role']         = 5;

            if (!empty($name)) {
                $params['user_name'] = $name;
            }

            $params[$userKey]       = $value;

            // 这个用户不存在, 添加这个成员等待用户加入
            $member = $memberDao->addMember($params);
            return $member['data'];

        } else {
            return null;
        }
    }

    /**
     * 返回指定的成员信息
     * @param memberId 要查询的成员的 ID
     */
    function getGroupMember($memberId) {
        $memberDao = MemberManager::getMemberDao();
        $member = $memberDao->getMember($memberId);

        if ($member == null) {
            return null;
        }    

        $userId = $member->user_id;
        $userDao  = MemberManager::getUserDao();
        $user = $userDao->getUser($userId);
        if ($user == null) {
            return null;
        }

        $member->user = $user;
        return $member;
    }

    function getGroupMemberByUser($groupId, $userId) {
        $memberDao = MemberManager::getMemberDao();
        return $memberDao->getMemberOfGroup($groupId, $userId);
    }

    function getGroupMemberListByTag($tagId) {
        $memberDao = MemberManager::getMemberDao();
        return $memberDao->getMembersOfTag($tagId);
    }

    /** 删除指定成员相关的所有标签. */
    function removeGroupMemberTags($memberId) {
        $params = array();

        $tagMemberManager = MemberManager::getGroupTagMemberDao();
        $tagMemberManager->addFilter($params, "member_id",  "=", $memberId);
        $tagMemberManager->removeEntities($params);
    }

    function getGroupMemberTag($memberTagId) {
        $memberTagDao = MemberManager::getMemberTagDao();
        return $memberTagDao->getMemberTag($memberTagId);
    }

    /** 删除指定标签相关的所有成员. */
    function removeGroupMemberTagMembers($tagId) {
        $params = array();

        $tagMemberManager = MemberManager::getGroupTagMemberDao();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $tagMemberManager->removeEntities($params);
    }

    function updateGroupMemberTagCount($tagId) {
        // query member count
        $params = array();
        $tagMemberManager = MemberManager::getGroupTagMemberDao();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $count = $tagMemberManager->getEntityCount($params);

        // update member count
        $params = array('member_count'=>$count);
        $memberTagDao = MemberManager::getMemberTagDao();
        $memberTagDao->updateMemberTag($tagId, $params);
    }

    function updateGroupDeviceTagCount($tagId) {
        // query member count
        $params = array();
        $tagMemberManager = MemberManager::getGroupTagDeviceDao();
        $tagMemberManager->addFilter($params, "tag_id",  "=", $tagId);
        $count = $tagMemberManager->getEntityCount($params);

        // update member count
        $params = array('device_count'=>$count);
        $memberTagDao = MemberManager::getDeviceTagDao();
        $memberTagDao->updateDeviceTag($tagId, $params);
    }    

}

//////////////////////////////////////////////////////////////////////////////////
// Members Action
// 

/** 
 * 添加一个群组成员
 * @param group_id 群组 ID
 * @param user_id 要添加的成员的用户 ID
 * @param mobile 要添加的成员的手机号码
 * @param email 要添加的成员的邮箱地址
 * @param name 要添加的成员的邮箱地址
 * @param id 要添加的成员的 ID
 */
function onGroupMemberAdd($request = array()) {
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $groupId   = safe_get($request, "group_id");
    $userId    = safe_get($request, "user_id");
    $mobile    = safe_get($request, "mobile");
    $email     = safe_get($request, "email");
    $name      = safe_get($request, "name");
    $memberId  = safe_get($request, "id");

    if (empty($groupId)) {
        return errorMissParamter('group_id');
    }

    // 是否存在该群组
    $groupDao = MemberManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if (empty($group)) {
        return errorNotExists('group');
    }

    $data = array();
    $error = null;

    if (empty($memberId)) {
        if (empty($userId) && empty($mobile) && empty($email)) {
            return errorMissParamter('user_id/mobile/email');
        }

        // 是否存在该用户
        $userDao  = MemberManager::getUserDao();
        $user = null;
        if (!empty($userId)) {
            $member = MemberManager::addGroupMember($groupId, $name, 'id', $userId);
            if ($member) {
                $data[] = $member;
            }

        } else if (!empty($email)) {
            $tokens = explode(",", $email);
            foreach($tokens as $token) {
                $member = MemberManager::addGroupMember($groupId, $name, 'email', $token, 'user_email');
                if ($member) {
                    if ($member['error']) {
                        $error = $member['error'];
                    }
                    $data[] = $member;
                }
            }

        } else if (!empty($mobile)) {
            $tokens = explode(",", $mobile);
            foreach($tokens as $token) {
                $member = MemberManager::addGroupMember($groupId, $name, 'mobile', $token, 'user_mobile');
                if ($member) {
                    if ($member['error']) {
                        $error = $member['error'];
                    }
                    $data[] = $member;
                }
            }
        }

    } else { 
        // 把指定的非正式成员更新为正式成员
        $memberDao = MemberManager::getMemberDao();
        $member = $memberDao->getMember($memberId);
        if ($member == nil) {
            return errorNotExists('member');
        }

        if ($member->role == 2) {
            $member->role = 0;
            $memberDao->updateMember($member->id, (array)$member);
        }

        if ($member) {
            $data[] = $member;
        }
    }
    
    // 
    $groupDao->updateGroupMemberCount($groupId, true);

    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $data;

    if ($error) {
        $result['ret']  = -1;
        $result['error'] = $error;
    }
    return $result;
}

/** 
 * 退出指定的群组. 
 * @param id 要退出的群组 
 */
function onGroupMemberExit($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    $memberDao = MemberManager::getMemberDao();

    // check parameters
    $groupId = safe_get($request, "id");
    $userId  = safe_get($request, "openid");
    if (empty($groupId)) {
        $groupId = safe_get($request, "group_id");
        if (empty($groupId)) {
            return errorMissParamter('id');
        }

    } else if (empty($userId)) {
        return errorMissParamter('openid');
    }

    // find
    $member = $memberDao->getMemberOfGroup($groupId, $userId);
    if (empty($member)) {

        // 是否存在该用户
        $userDao = MemberManager::getUserDao();
        $user = $userDao->getUser($userId);

        if ($user) {
            // 是否受到邀请
            $members = $memberDao->getMembersOfInvited($groupId, $user->email, $user->mobile);
            if (count($members) > 0) {
                $memberDao->removeMembersOfInvited($groupId, $user->email, $user->mobile);
            }
        }

        $result = array();
        $result['ret']  = 0;
        return $result;
    }

    // remove
    $memberId = $member->id;
    $result = $memberDao->removeMember($memberId);
    $result['ret']  = 0;

    // update
    $groupDao = MemberManager::getGroupDao();
    $groupDao->updateGroupMemberCount($groupId, true);

    // TODO: remove tag_member

    return $result;
}

/**
 * 申请加入指定的群组
 * @param id 要加入的群组
 * @param code 要加入的群组的邀请码
 */
function onGroupMemberJoin($request = array()) {
    // check login
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // parameters
    $groupId = safe_get($request, "id");
    if (empty($groupId)) {
        $groupId = safe_get($request, "group_id");
    }

    $code    = safe_get($request, "code");
    if (empty($groupId)) {
        if (empty($code)) {
            return errorMissParamter('id');

        } else {
            $groupId = $code;
        }
    }

    $userId  = safe_get($request, "openid");
    if (empty($userId)) {
        return errorMissParamter('openid');
    }

    // 是否存在该群组
    $groupDao = MemberManager::getGroupDao();
    $group = $groupDao->getGroup($groupId);
    if (empty($group)) {
        return errorNotExists('group');
    }

    // 是否存在该用户
    $user = null;
    if (!empty($userId)) {
        $userDao = MemberManager::getUserDao();
        $user = $userDao->getUser($userId);
    }

    if (empty($user)) {
        return errorNotExists('user');

    } else if ($userId == $group->owner_id) {
        return errorAlreadyExists('owner');
    }

    // 检查是否已是该群组成员
    $memberDao = MemberManager::getMemberDao();
    $member = $memberDao->getMemberOfGroup($groupId, $userId);
    if (!empty($member) && $member->role < 2) {
        return errorAlreadyExists('member');
    }

    $role = 2; // 申请加入

    // 是否受到邀请
    $members = $memberDao->getMembersOfInvited($groupId, $user->email, $user->mobile);
    if (count($members) > 0) {
        $memberDao->removeMembersOfInvited($groupId, $user->email, $user->mobile);
        $role = 0;
    }

    // add
    $params = array();
    $params['group_id'] = $groupId;
    $params['user_id']  = $userId;
    $params['role']     = $role;
    $member = $memberDao->addMember($params);
    
    // update
    $groupDao->updateGroupMemberCount($groupId, true);

    $result = array();
    $result['ret']  = 0;
    $result['data'] = $member;
    return $result;
}


/**
 * 查询群组成员
 * @param group_id 所属的群组
 */
function onGroupMemberList($request = array()) {
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

    // group members
    $params = array();
    $params['orderBy'] = 'role DESC,name ASC';

    $memberDao = MemberManager::getMemberDao();
    $memberDao->addFilter($params, 'group_id', '=', $groupId);
    $members = $memberDao->findMembers($params);
    $total   = $memberDao->getMemberCount($params);

    $data = array();
    if (is_array($members)) {
        foreach ($members as $item) {
            if (!is_object($item)) {
                continue;
            }

            $data[] = MemberManager::member2Array($item);        
        }
    }

    $result = array();
    $result['data'] = $data;
    $result['total'] = (int)$total;

    // group owner
    $owner = userGet($group->owner_id);
    $owner['user_id'] = $owner['id'];
    $result['owner'] = $owner;

    // ret
    $result['ret']  = 0;
    return $result;
}

/**
 * 删除群组成员
 * @param id 要删除的成员 ID
 * @param group_id 要删除的成员所属的群组
 * @param user_id 要删除的成员相关的用户 ID
 */
function onGroupMemberRemove($request = array()) {
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    $memberDao = MemberManager::getMemberDao();
    $memberId = safe_get($request, "id");
    if (empty($memberId)) {
        // check parameters
        $groupId = safe_get($request, "group_id");
        if (empty($groupId)) {
            return errorMissParamter('group_id');
        }

        $userId  = safe_get($request, "user_id");
        if (empty($userId)) {
            return errorMissParamter('user_id');
        }

        if (!MemberManager::isGroupManager($groupId)) {
            return errorNotYourOwn('group');
        }

        // find by user ID
        $member = $memberDao->getMemberOfGroup($groupId, $userId);
        if (empty($member)) {
            $result = array();
            $result['ret']  = 0;
            return $result;
        }

        $memberId = $member->id;

    } else {
        // find by member ID
        $member = $memberDao->getMember($memberId);
        if (empty($member)) {
            return errorNotExists('member');
        } 

        $groupId = $member->group_id;
        if (!MemberManager::isGroupManager($groupId)) {
            return errorNotYourOwn('group');
        }
    }

    // remove the member
    $result = $memberDao->removeMember($memberId);
    $result['ret']  = 0;

    // remove the tag member
    MemberManager::removeGroupMemberTags($memberId);

    // update group member count
    $groupDao = MemberManager::getGroupDao();
    $groupDao->updateGroupMemberCount($groupId, true);

    return $result;
}

/**
 * 修改指定的群组成员
 * @param id
 * @param user_name 
 */
function onGroupMemberUpdate($request = array()) {
    if (!userIsLogin()) {
        return errorNotLogin();
    }

    // find by member ID
    $memberId  = safe_get($request, "id");
    if (empty($memberId)) {
        return errorMissParamter('id');
    }

    $memberDao = MemberManager::getMemberDao();
    $member = $memberDao->getMember($memberId);
    if ($member == nil) {
        return errorNotExists('member');
    }

    // Check group manager
    $groupId = $member->group_id;
    if (!MemberManager::isGroupManager($groupId)) {
         return errorNotYourOwn('group');
    }
    //print_r($member);

    // update
    if ($member->role != ROLE_NORMAL) {
        $newMember = array();
        $newMember['role'] = ROLE_NORMAL;
        $memberDao->updateMember($member->id, $newMember);

    } else {
        $newMember = array();

        $userName = safe_get($request, "user_name");
        if (!empty($userName)) {
            $newMember['user_name'] = $userName;
            $memberDao->updateMember($member->id, $newMember);
        }
    }

    // result
    $result = array();
    $result['ret']  = 0;
    $result['data'] = $member;
    return $result;
}


