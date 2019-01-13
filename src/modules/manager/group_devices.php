<?php
require_once "common/api_dispatch.php";
require_once "group_common.php";

$groupId  = safe_get($_GET, "group_id");
$action   = safe_get($_GET, "action");
$name     = safe_get($_GET, "name");
$itemId   = safe_get($_GET, "group_device_id");
$deviceId = safe_get($_GET, "device_id");

$openid   = safe_get($_GET, "openid");
$openkey  = safe_get($_GET, "openkey");
$clientId = safe_get($_GET, "client_id");

$userId  = $globalViewer->id;
$groupDao = BaseService::getService("GroupDao");
$error   = null;
//print_r($groupInfo);

if ($action == 'create') { 
  	if (!empty($name)) {
  		$request = array();
  		$request['name'] 		= $name;
  		$request['openid'] 		= $openid;
  		$request['openkey'] 	= $openkey;
  		$request['client_id'] 	= $clientId;
  		$request['description'] = '';
  		$request['parent_id'] 	= '0';
  		$result = onGroupCreate($request);
  		$error  = $result['error'];
  		$ret    = $result['ret'];

  		if ($ret == 0) {
  			$url = '?path=/manager/group&action=success';
        	header("location: ".$url);
        	return;
  		}
  	}
}

if (!empty($itemId)) {

    $device = GroupManager::getGroupDevice($itemId);
    //print_r($device);

    ?><div class="member-info">
    <dl class="base_form_list">
      <dt>编号:</dt>
      <dd><?=$device->id?></dd>

      <dt>名称:</dt>
      <dd><?=$device->name?></dd>

      <dt>描述:</dt>
      <dd><?=$device->description?></dd>

      <dt>UID:</dt>
      <dd><?=$device->uid?></dd>

      <dt>用户名:</dt>
      <dd><?=$device->username?></dd>

      <dt>密码:</dt>
      <dd><?=$device->password?></dd>

      <dt>谁可以看:</dt>
      <dd><?=$device->privacy?></dd>

      <dt>更新时间:</dt>
      <dd><?=date('Y-m-d H:i:s', $device->updated);?></dd>
    </dl>

    </div>
    <?php

    return;

} else if (!empty($deviceId)) {
    $device = groupPublicDeviceGet($deviceId);
    //print_r($device);

    ?><div class="member-info">
    <dl class="base_form_list">
      <dt>编号:</dt>
      <dd><?=$device->id?></dd>

      <dt>名称:</dt>
      <dd><?=$device->name?></dd>

      <dt>UID:</dt>
      <dd><?=$device->uid?></dd>

      <dt>所有者:</dt>
      <dd><?=$device->owner_id?></dd>

      <dt>城市:</dt>
      <dd><?=$device->city?></dd>

      <dt>地址:</dt>
      <dd><?=$device->address?></dd>

      <dt>网址:</dt>
      <dd><?=$device->webpage?></dd>

      <dt>电话:</dt>
      <dd><?=$device->phone?></dd>

      <dt>描述:</dt>
      <dd><?=$device->description?></dd>

    </dl>

    </div>
    <?php

    return;
}

?><!doctype html>
<html>
<head>
    <title>群组详情</title>
    <?php require_once("manager/head.php"); ?>
    <style>
        .frame-table { background: #f8f8f9; }
        .frame-main { background: #fff; width: 240px; float: left; border-right: 1px solid #eee; }
        .frame-side { background: #fff; width: 240px; position: absolute; left: 0;
        top: 0; bottom: 0; border-right: 1px solid #ddd; z-index: 100; }
        .frame-side2 { background: #fff; width: 240px; position: absolute; left: 240px; z-index: 99;  top: 0; bottom: 0; border-right: 1px solid #ddd; }
        .frame-side3 { background: #fff; position: absolute; 
            left: 481px; right: 0; z-index: 101; top: 0; bottom: 0; }
        .group_top_menu li a label { padding-left: 16px; }
        .member-info { padding: 16px 16px; }
    	.error-wrapper { margin: 16px 16px; padding: 8px 8px; border: 1px solid #ddd; background: #fee; color: #f99; }

        .group_top_menu li.selected { background: #ffe; }
    </style>
    <script>
        var gLastDeviceId = '';

        function onRelayout() {
        }

        $(document).ready(function() {
            onRelayout();
        });

        $(window).resize(onRelayout);

        function onSelectChange(id) {
            $('#device_wrapper_' + gLastDeviceId).removeClass('selected');
            $('#device_wrapper_' + id).addClass('selected');

            gLastDeviceId = id;
        }

        function onPublicClick(id) {
            var url = "?path=/manager/group_devices&device_id=" + id;
            $("#block-right").html(getLoadingHtml()).load(url);

            onSelectChange('p' + id);
        }

        function onDeviceClick(id) {
            var url = "?path=/manager/group_devices&group_device_id=" + id;
            $("#block-right").html(getLoadingHtml()).load(url);

            onSelectChange('g' + id);
        }

        <?=groupPrintList()?>
    </script>
</head>
<body class="group_body">
  <?php require_once("manager/header.php"); ?>

  <?php if ($action == 'create') { ?>
  <header><div><h1>创建群组</h1></div></header>
  <div class="wrapper"><form method="GET">
  	<input type="hidden" name="action" value="create"/>
  	<input type="hidden" name="openid" value="<?=$openid?>"/>
  	<input type="hidden" name="openkey" value="<?=$openkey?>"/>
  	<input type="hidden" name="client_id" value="<?=$clientId?>"/>

  	<?php if ($error) { ?><div class="error-wrapper">
  		<?=$error?>
  	</div><?php } ?>
  	<dl>
  	  <dt><label>名称:</label></dt>
  	  <dd><input type="text" class="text" name="name" value="<?=$name?>"/></dd>
  	  <dt class="button-wrapper"><button class="button rbutton">提交</button></dt>
  	</dl></form>
  </div>

  <?php } else if ($action == 'success') { ?>
  <header><div><h1>创建群组</h1></div></header>
  <div class="wrapper"><dl>
  	<dt>新的群组已经创建成功!</dt>
  	</dl>
  </div>

<?php } else { 
  	$groupInfo = $groupDao->getGroup($groupId);

    $request = array('group_id'=>$groupId);
    $result = onGroupDeviceList($request);
    //print_r($result);

    $groupDevices = $result['data'];

    //print_r($groupDevices);
?>
<div class="frame-table clearfix">

  <!-- Group Info   -->
  <div class="frame-main frame-side"><div class="wrapper">
    <div class="group_info">
      <h2><?=$groupInfo->name?></h2>
      <p><?=$groupInfo->description?></p>
    </div> 
    <div class="group_top_menu">
      <ul>
        <li><a href="?path=/manager/group_detail&group_id=<?=$groupId?>">基本信息 </a></li>    

        <li><a href="?path=/manager/group_members&group_id=<?=$groupId?>">
            成员信息 (<?=$groupInfo->member_count?>个成员)</a></li>
        <li class="selected"><a href="?path=/manager/group_devices&group_id=<?=$groupId?>">
            设备信息 (<?=$groupInfo->device_count?>个视频)</a></li>
        <li><a href="?path=/manager/group_tags&group_id=<?=$groupId?>">
            标签</a></li>

      </ul>
    </div> 
  </div></div>

  <!-- Group Members   -->
  <div class="frame-main frame-side2"><div class="wrapper">
    <div class="group_info">
      <h2>分享的摄像机:</h2>
    </div> 
    <div class="group_top_menu">
      <ul>
      <?php if (empty($groupDevices)) { ?> <li><a href="#">暂无</a></li> <?php } ?>
      <?php foreach ($groupDevices as $device) { ?>
        <?php $device = (object)$device; ?>
        <li id="device_wrapper_g<?=$device->id?>"><a href="javascript:onDeviceClick(<?=$device->id?>);">
          <input type="checkbox"/><label><?=$device->name?></a></label></li>
      <?php } ?>
      </ul>
    </div>    
  </div></div>

  <!-- Right Side -->
  <div class="frame-side3" id="block-right">
    
  </div>
</div>

<?php } ?>

</body>
</html>