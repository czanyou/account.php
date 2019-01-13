<?php

require_once "manager_common.php";

import('core.vision.GroupDao');


VisionCheckAdminStatus();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 

function groupAdd() {
    $name = safe_get($_GET, "name");
    $description = safe_get($_GET, "description");

    if (empty($name)) {
        return "没有填写名称";
    }

    if (empty($description)) {
        return "没有填写值";
    } 

    $groupDao = BaseService::getService("GroupDao");

    $params = array();
    $params['name'] = $name;
    $params['description'] = $description;

    $result = $groupDao->addGroup($params);
    return 0; 
}

function groupEdit() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $name = safe_get($_GET, "name");
    $description = safe_get($_GET, "description");

    $entry = array();
    $entry['name']          = $name;
    $entry['description']   = $description;
    $entry['owner_id']      = safe_get($_GET, "owner_id");
    $entry['privacy']       = safe_get($_GET, "privacy");   

    $groupDao = BaseService::getService("GroupDao");
    $result = $groupDao->updateGroup($id, $entry);
 
    return 0;
}

function groupRemove() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $groupDao = BaseService::getService("GroupDao");
    $result = $groupDao->removeGroup($id);
    return 0; 
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 

$action = safe_get($_GET, "action");

if ($action == 'add') {
    $result = groupAdd();

    if (empty($result)) {
        $url = '?path=/manager/groups&action=success';
        header("location: ".$url);
        return;

    } else {
        $action = "failed";
    }

} else if ($action == "edit") { 
	groupEdit();

    $page   = safe_get($_GET, "page");
    $url = '?path=/manager/groups&page='.$page;
    header("location: ".$url);
	return;

} else if ($action == 'remove') {
    groupRemove();

    $url = '?path=/manager/groups&';
    header("location: ".$url);
    return;	

} else if ($action == "edit_form") { 
    require_once("manager/groups_manager_edit_form.php");

} else if ($action == "list") { 
    require_once("manager/groups_manager_list.php");

} else { 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 

?><!doctype html>
<html>
<head>
    <title>群组管理</title>
    <?php require_once("manager/head.php"); ?>
    <script type="text/javascript">
        $currentPage = <?=safe_get($_GET, "page", 1)?>;
        $pageName = "?path=/manager/groups";

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
            onSearchSubmitDefault($pageName);
        }        

        function onRefresh(page) {
            $currentPage = page;
            onRefreshDefault($pageName, "", page)
        }

        function onRelayout() {
        }

        $(document).ready(function() {
            onRelayout();

            var html = '<a onclick="onAdd(); return false;">添加群组</a>';
            html += '<a onclick="onSearch(); return false;">查找群组</a>';
            $("#header-menu").html(html);
            lsideSelect('group');

            onRefresh($currentPage);
            window.onpopstate = onHistoryPopState;
        });

        $(window).resize(onRelayout);

    </script>
    <style type="text/css">
        .frame-table { background: #f5f5f6; }
        .frame-main { overflow: hidden; background: #fff; }
        tr.business td { background: #ffe; }
        .grid-table { max-width: 800px; }
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
      <div id="addPopBox" class="side-right" style="display:none;">
        <form id="addForm" name="addForm" class="">
        <div class="form-header"><h2>添加群组:</h2></div>
        <dl class="form-content">
          <dt>群组名称和描述:</dt>
          <dd><input type="text" class="text" name="name" required="required" placeholder="名称" value="<?=safe_get($_GET, 'name')?>"/></dd>
          <dd><input type="text" class="text" name="description" required="required" placeholder="描述" value="<?=safe_get($_GET, 'description')?>"/></dd>
          <dd><input type="button" class="button" value="添加" onClick="onFormSubmit('addForm')"/></dd>
          </dl>
          <input type="hidden" name="action" value="add"/>
        </form>
      </div>

      <div id="editPopBox" class="side-right" style="display:none;"></div>
    <div id="searchPopBox" class="side-right" style="display:none;">
      <form id="searchForm" name="searchForm">
        <div class="form-header"><h2>查找群组:</h2></div>
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