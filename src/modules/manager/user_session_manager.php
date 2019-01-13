<?php

require_once "manager_common.php";

import('core.vision.SessionDao');

VisionCheckAdminStatus();

function userAdd() {

    return 0; 
}

function userEdit() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    return 0;
}

function userRemove() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $settingsDao = BaseService::getService("SessionDao");
    $result = $settingsDao->removeSession($id);
    return 0; 
}

function userListCheckParams(&$that, &$params, $name) {
    $keyword = safe_get($_GET, $name);
    if ($keyword) {
        $that->addFilter($params, $name, 'contains', $keyword);
    }  
}

function userList() {
    $settingsDao = BaseService::getService("SessionDao");
    $page    = safe_get($_GET, "page");
    $params = array();
    if (empty($page)) {
        $page = 1;

    } else {
        $start = max(0, ($page - 1) * 20);
        $params['startIndex'] = $start;
    }

    userListCheckParams($settingsDao, $params, 'user_id');
    userListCheckParams($settingsDao, $params, 'client_id');
    userListCheckParams($settingsDao, $params, 'client_version');

    $result = $settingsDao->findSessions($params);
    //$result['total'] = $settingsDao->getUserCount($params);

    $result['total'] = count($result);
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
    $settingsDao = BaseService::getService("SessionDao");
    $entry = $settingsDao->getSession($id);

?>
<form id="editForm" name="editForm" class="">
    <h2>会话信息:</h2>
    <dl class="form-content">
      <dt>编号:</dt>
      <dd><?=$entry->id?></dd>
      
      <dt>用户:</dt>
      <dd><?=$entry->user_id?></dd>

      <dt>KEY:</dt>
      <dd><?=$entry->openkey?></dd>

      <dt>客户端 IP:</dt>
      <dd><?=$entry->client_ip?></dd>

      <dt>客户端 ID:</dt>
      <dd><?=$entry->client_id?></dd>

      <dt>客户端名称:</dt>
      <dd><?=$entry->client_name?></dd>

      <dt>客户端版本:</dt>
      <dd><?=$entry->client_version?></dd>            
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
            <th>会话 (共 <?=$total?> 条记录)</th>
            <th class="align_center">编号</th>
            <th>KEY</th>
            <th>客户端</th>
            <th class="align_center">操作</th>
          </tr>

        <?php if (!$result) { ?>
          <tr><td colspan="5">没有找到记录</td></tr>
        <?php } ?>

        <?php foreach ($result as $key) { ?>
          <tr>
            <td><a href="javascript:onEdit(<?=$key->id?>);"><?=$key->user_id?></a></td>
            <td class="align_center" nowrap="nowrap"><?=$key->id?></td>  
            <td nowrap="nowrap"><?=$key->openkey ? $key->openkey : '-' ?></td>
            <td nowrap="nowrap"><?=$key->client_id?></td>    
            <td class="align_center" nowrap="nowrap"><a href="javascript:onRemove(<?=$key->id?>)">删除</a></td>
          </tr>
        <?php } ?>
        </table>
      </div>

      <div class="grid-footer">
        <?php managerShowPager($total, $page, '?path=/manager/user_session'); ?>
      </div>
    </div>


<?php } else {
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --

?><!doctype html>
<html>
<head>
    <title>会话管理</title>
    <?php require_once("manager/head.php"); ?>

    <script type="text/javascript">
        $currentPage = <?=safe_get($_GET, "page", 1)?>;
        $pageName = "?path=/manager/user_session";

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
            var viewHeight = $(window).height();
            $("#wrapper").height(viewHeight - 101);
            $("#rside").height(viewHeight - 60);
        }

        $(document).ready(function() {
            onRelayout();

            var html = '';
            html += '<a onclick="onSearch(); return false;">查找会话</a>';
            $("#header-menu").html(html);
            lsideSelect('user_session');

            onRefresh($currentPage);
            window.onpopstate = onHistoryPopState;
        });

        $(window).resize(onRelayout);

    </script>
    <style>
        .frame-table { background: #f5f5f6; }
        .frame-main { width: 600px; overflow: hidden; float: left; background: #fff; }
        .rside { margin-left: 601px; overflow: auto; }
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
    <div id="editPopBox" class="side-right" style="display:none;"></div>
    <div id="searchPopBox" class="side-right" style="display:none;">
      <form id="searchForm" name="searchForm">
        <div class="form-header"><h2>查找会话:</h2> <a href="javascript:rsideHideAll();">关闭</a></div>
        <dl class="form-content">
          <dt>用户ID:</dt>
          <dd><input type="text" class="text" name="user_id" placeholder="用户ID" value="<?=safe_get($_GET, 'user_id')?>"/></dd>
          
          <dt>客户端ID:</dt>
          <dd><input type="text" class="text" name="client_id" placeholder="客户端ID" value="<?=safe_get($_GET, 'client_id')?>"/></dd>

          <dt>客户端版本:</dt>
          <dd><input type="text" class="text" name="client_version" placeholder="客户端版本" value="<?=safe_get($_GET, 'client_version')?>"/></dd>

          <dd><input type="button" class="button" value="查找" onclick="onSearchSubmit();"/></dd>
        </dl>
      </form>
    </div>
  </div>

</div>

<?php } ?>

</body>
</html>