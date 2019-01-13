<?php

require_once "manager_common.php";

import('core.vision.UserDao');

VisionCheckAdminStatus();

function userAdd() {
    $account = safe_get($_GET, "account");
    $password = safe_get($_GET, "password");

    if (empty($account)) {
        return "没有填写名称";
    }

    if (empty($password)) {
        return "没有填写值";
    } 

    $userDao = BaseService::getService("UserDao");

    $params = array();
    $params['account'] = $account;
    $params['password'] = $password;

    $result = $userDao->addUser($params);
    return 0; 
}

function userEdit() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $name = safe_get($_GET, "name");

    $entry = array();
    $entry['about_me'] = safe_get($_GET, "about_me");
    $entry['default_group'] = safe_get($_GET, "default_group");


    $userDao = BaseService::getService("UserDao");
    $result = $userDao->updateUser($id, $entry);
 
    return 0;
}

function userRemove() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $userDao = BaseService::getService("UserDao");
    $result = $userDao->removeUser($id);
    return 0; 
}

function userListCheckParams(&$that, &$params, $name) {
    $keyword = safe_get($_GET, $name);
    if ($keyword) {
        $that->addFilter($params, $name, 'contains', $keyword);
    }  
}

function userList() {
    $userDao = BaseService::getService("UserDao");
    $page    = safe_get($_GET, "page");
    $keyword = safe_get($_GET, "keyword");
    $mobile  = safe_get($_GET, "mobile");
    $email   = safe_get($_GET, "email");

    $params = array();
    if (empty($page)) {
        $page = 1;

    } else {
        $start = max(0, ($page - 1) * 20);
        $params['startIndex'] = $start;
    }

    userListCheckParams($userDao, $params, 'name');
    userListCheckParams($userDao, $params, 'mobile');
    userListCheckParams($userDao, $params, 'email');

    $result = $userDao->findUsers($params);
    $result['total'] = $userDao->getUserCount($params);
    return $result;
}

$action = safe_get($_GET, "action");

if ($action == 'add') {
    $result = userAdd();

    if (empty($result)) {
        

    } else {
        $action = "failed";
    }

    return;

} else if ($action == "edit") { 
    echo(json_encode(userEdit()));
	return;

} else if ($action == 'remove') {
    echo(json_encode(userRemove()));
    return;	

} else  if ($action == "edit_form") { 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

    $id = safe_get($_GET, "id");
    $userDao = BaseService::getService("UserDao");
    $entry = $userDao->getUser($id);

?>
<form id="editForm" name="editForm" class="">
    <div class="form-header"><h2>修改用户:</h2></div>
    <dl class="form-content">
      <dt>名称 ( 1 到 16 个字符):</dt>
      <dd><input type="text" class="text" name="name" required="required" placeholder="名称" value="<?=$entry->name?>"/></dd>
      
      <dt>手机 (只支持中国大陆地区手机号码):</dt>
      <dd><input type="text" class="text" name="mobile" placeholder="手机" value="<?=$entry->mobile?>"/></dd>
      
      <dt>邮箱:</dt>
      <dd><input type="text" class="text" name="email" placeholder="邮箱" value="<?=$entry->email?>"/></dd>

      <dt>描述 (1 到 96 个字符):</dt>
      <dd><input type="text" class="text" name="about_me" placeholder="描述" value="<?=$entry->about_me?>"/></dd>
      
      <dt>默认群组:</dt>
      <dd><input type="text" class="text" name="default_group" placeholder="默认群组" value="<?=$entry->default_group?>"/></dd>

      <dd><input type="button" class="button" onclick="onFormSubmit('editForm')" value="确定"/></dd>
    </dl>
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="id" value="<?=$id?>"/>
</form>

<?php 
} else  if ($action == "list") { 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

    $page   = safe_get($_GET, "page");
    $result = userList();
    $total = $result['total'];
    unset($result['total']);

?>

    <div class="frame-main">
      <div id="wrapper" class="wrapper">
        <table class="grid-table">
          <tr>
            <th>名称 (共 <?=$total?> 条记录)</th>
            <th class="align_center">编号</th>
            <th>手机</th>
            <th>邮箱</th>
            <th class="align_center">操作</th>
          </tr>

        <?php if (!$result) { ?>
          <tr><td colspan="5">没有找到记录</td></tr>
        <?php } ?>

        <?php foreach ($result as $key) { ?>
          <tr>
            <td><a href="javascript:onEdit(<?=$key->id?>);"><?=$key->name ? $key->name : '无名'?></a></td>
            <td class="align_center" nowrap="nowrap"><?=$key->id?></td>  
            <td nowrap="nowrap"><?=$key->mobile ? $key->mobile : '-' ?></td>
            <td nowrap="nowrap"><?=$key->email?></td>    
            <td class="align_center" nowrap="nowrap"><a href="javascript:onRemove(<?=$key->id?>)">删除</a></td>
          </tr>
        <?php } ?>
        </table>
      </div>

      <div class="grid-footer">
        <?php managerShowPager($total, $page, '?path=/manager/user'); ?>
      </div>
    </div>


<?php } else {
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

?><!doctype html>
<html>
<head>
    <title>用户管理</title>
    <?php require_once("manager/head.php"); ?>

    <script type="text/javascript">
        $currentPage = <?=safe_get($_GET, "page", 1)?>;
        $pageName = "?path=/manager/user";

        function onActionCallback() {
            onRefresh($currentPage);

            $('#message-box').showInfo("操作完成")
        }

        function onRemove(id) {
            onRemoveDefault($pageName, id, onActionCallback)
        }

        function onEdit(id) {
        	onEditDefault($pageName, id);
        }

        function onFormSubmit(id) {
            onFormSubmitDefault($pageName, id, onActionCallback);
        }

        function onSearchSubmit() {
            onSearchSubmitDefault($pageName)
        }

        function onRefresh(page) {
            $currentPage = page;
            onRefreshDefault($pageName, "", page)
        }

        function onRelayout() {

        }

        $(document).ready(function() {
            onRelayout();

            var html = '';
            html += '<a onclick="onSearch(); return false;">查找用户</a>';
            $("#header-menu").html(html);
            lsideSelect('user');

            onRefresh($currentPage);
            window.onpopstate = onHistoryPopState;
        });

        $(window).resize(onRelayout);

    </script>
    <style>
        .frame-table { background: #f5f5f6; }
        .frame-main { max-width: 960px; overflow: hidden; background: #fff; }
        .grid-table { max-width: 800px; }
        .rside {  }
        .rside h2 { padding: 16px 16px 4px 16px; border-bottom: 1px dotted #ddd; }
        .rside form { max-width: 600px; }
        .align_center { text-align:center; }    
    </style>
</head>
<body>

<?php require_once("manager/header.php"); ?>

<div class="frame-table">
  <div id="frame-body" class="frame-body"></div>
  <div id="message-box" class="message-box" style="display:none;">...</div>

  <div id="rside" class="rside">
    <div class="close-button-wrapper"><a href="javascript:rsideHideAll();">关闭</a></div>
    <div id="editPopBox" class="side-right" style="display:none;"></div>
    <div id="searchPopBox" class="side-right" style="display:none;">
      <form id="searchForm" name="searchForm">
        <div class="form-header"><h2>查找用户:</h2>
        <dl class="form-content">
          <dt>请输入关键词:</dt>
          <dd><input type="text" class="text" name="name" placeholder="关键词" value="<?=safe_get($_GET, 'name')?>"/></dd>
          
          <dt>手机号码:</dt>
          <dd><input type="text" class="text" name="mobile" placeholder="手机号码" value="<?=safe_get($_GET, 'mobile')?>"/></dd>

          <dt>邮箱地址:</dt>
          <dd><input type="text" class="text" name="email" placeholder="邮箱地址" value="<?=safe_get($_GET, 'email')?>"/></dd>

          <dd><input type="button" class="button" value="查找" onclick="onSearchSubmit();"/></dd>
        </dl>
      </form>
    </div>
  </div>

</div>

<?php } ?>

</body>
</html>