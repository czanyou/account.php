<?php
require_once "common.php";

import('core.vision.SettingsDao');

VisionCheckAdminStatus();

function onSettingsAdd() {
    $name = safe_get($_GET, "name");
    $value = safe_get($_GET, "value");

    if (empty($name)) {
        return "没有填写名称";
    }

    if (empty($value)) {
        return "没有填写值";
    } 

    $settingsDao = BaseService::getService("SettingsDao");

    $params = array();
    $params['name'] = $name;
    $params['value'] = $value;

    $result = $settingsDao->addSetting($params);
    return 0; 
}

function onSettingsRemove() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $settingsDao = BaseService::getService("SettingsDao");
    $result = $settingsDao->removeSetting($id);
    return 0; 
}

function onSettingsEdit() {
    $id = safe_get($_GET, "id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $value = safe_get($_GET, "value");
    if (empty($value)) {
        return "没有填写值";
    }

    $entry = array();
    $entry['value'] = $value;

    $settingsDao = BaseService::getService("SettingsDao");
    $result = $settingsDao->updateSetting($id, $entry);
 
    return 0;
}

function onSettingsList() {
    $settingsDao = BaseService::getService("SettingsDao");

    $params = array();
    $result = $settingsDao->findSettings($params);
    
    return $result;
}

$action = safe_get($_GET, "action");

if ($action == 'add') {
    $result = onSettingsAdd();

    if (empty($result)) {
        $url = '?path=/manager/settings&action=success';
        header("location: ".$url);
        return;

    } else {
        $action = "failed";
    }

} else if ($action == 'remove') {
    onSettingsRemove();

    $url = '?path=/manager/settings&action=success';
    header("location: ".$url);
    return;

} else if ($action == 'edit') {
    onSettingsEdit();
    return;
    
} else if ($action == 'list') {    

$result = onSettingsList();
?>
<div class="frame-main">
    <div class="wrapper" onclick="onPopCancel();">
    <table class="grid-table">
        <tr>
        <th class="align_center" style="min-width:40px;">编号</th>
        <th style="min-width:80px;">类型</th>
        <th style="min-width:160px;">值</th>
        <th class="align_center" style="min-width:80px;">操作</th>
        </tr>
    <?php if (!$result) { ?>
        <tr><td colspan="4">没有找到记录</td></tr>
    <?php } ?>
    <?php foreach ($result as $entry) { ?>
        <tr>
        <td class="align_center"><?=$entry->id?></td>
        <td><?=$entry->name?></td>
        <td contenteditable="true" id="v<?=$entry->id?>" onblur="onValueBlur(this)"><?=$entry->value?></td>
        <td class="align_center"><a href="javascript:onRemove(<?=$entry->id?>)">删除</a> 
            <input type="hidden" id="default_v<?=$entry->id?>" value="<?=$entry->value?>"/></td>
        </tr>
    <?php } ?>
    </table>
    </div>
</div>
<?php


} else {

?><!doctype html>
<html>
<head>
    <title>系统参数</title>
    <?php require_once("manager/head.php"); ?>
    <script type="text/javascript">
        $currentPage = <?=safe_get($_GET, "page", 1)?>;
        $pageName = "?path=/manager/settings";
        
        function onActionCallback() {
            onRefresh($currentPage);

            $('#message-box').showInfo("操作完成")
        }

        function onAdd() {
            $("#addPopBox").show("fast");
        }

        function onRemove(id) {
            if (!window.confirm("将删除这个参数?")) {
                return;
            }

            location.href = "?path=/manager/settings&action=remove&id=" + id;
        }

        function onValueBlur(obj) {
            //alert(obj.id + ":" + obj.innerText);
            var id = obj.id || "";
            var defaultId = "default_" + id;
            var defaultValue = $("#" + defaultId).val();
            var value = obj.innerText || "";
            if (value == defaultValue) {
                return;
            }

            id = id.substring(1);
            var url = "?path=/manager/settings&action=edit&id=" + id;
            url += "&value=" + value;
            $.get(url, function(data, status) {
                $("#" + defaultId).val(value);
            });
        }
        
        function onFormSubmit(id) {
            onFormSubmitDefault($pageName, id, onActionCallback);
        }

        function onPopCancel() {
            $("#addPopBox").hide("fast");
        }

        function onRelayout() {
            var viewHeight = $(window).height();
            $(".wrapper").height(viewHeight - 101);
        }
        
        function onRefresh(page) {
            $currentPage = page;
            onRefreshDefault($pageName, "", page)
        }

        $(document).ready(function() {
            onRelayout();
            
            var html = '';
            html += '<a onclick="javascript:onAdd(); return false;">添加参数</a>';
            $("#header-menu").html(html);
            
            lsideSelect('settings');
            
            onRefresh($currentPage);
        });

        $(window).resize(onRelayout);

    </script>
    <style>
        .frame-table { background: #f5f5f6; }
        .frame-main { min-width: 600px; overflow: hidden; float: left; background: #fff; }
        .rside { margin-left: 1px; overflow: auto; }
        .rside h2 { padding: 16px 16px 4px 16px; border-bottom: 1px dotted #ddd; }
        .rside form { max-width: 2600px; }
        .align_center { text-align:center; }  
    </style>
</head>
<body>
<?php require_once("manager/header.php"); ?>

<div class="frame-table">
  <div id="frame-body" class="frame-body"></div>
  <div id="message-box" class="message-box" style="display:none;">...</div>

  <div class="rside">
    <div id="addPopBox" class="side-right" style="display:none;">
    <form id="addForm" name="addForm">
      <input type="hidden" name="action" value="add"/>
      <div class="form-header">
        <h2>添加参数:</h2> 
        <a href="javascript:rsideHideAll();">关闭</a></div>
      <dl class="form-content">
        <dt>名称和值:</dt>
        
        <dd><input type="text" class="text" name="name" required="required" 
          placeholder="名称" value="<?=safe_get($_GET, 'name')?>"/></dd>
          
        <dd><input type="text" class="text" name="value" required="required" 
          placeholder="值" value="<?=safe_get($_GET, 'value')?>"/></dd>
          
        <dd><input type="button" class="button" value="添加" 
          onclick="onFormSubmit('addForm');"/></dd>
      </dl>
    </form>
    </div>
  </div>
</div>

</body>
</html>

<?php
}
