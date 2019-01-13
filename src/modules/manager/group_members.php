<?php
require_once "common/api_dispatch.php";
require_once "group_common.php";

$groupId  = safe_get($_GET, "group_id");
$action   = safe_get($_GET, "action");
$name     = safe_get($_GET, "name");
$memberId = safe_get($_GET, "member_id");
$ownerId  = safe_get($_GET, "owner_id");

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

if (!empty($memberId)) {

    $member = MemberManager::getGroupMember($memberId);

    //print_r($member);
    ?><div class="member-info"><dl class="base_form_list">
      <dt>编号:</dt>
      <dd><?=$member->user_id?></dd>

      <dt>名称:</dt>
      <dd><?=$member->user_name ? $member->user_name : $member->name?></dd>

      <dt>邮箱:</dt>
      <dd><?=$member->user_email ? $member->user_email : $member->email?></dd>

      <dt>电话:</dt>
      <dd><?=$member->user_mobile ? $member->user_mobile : $member->mobile ?></dd>

    </dl></div>

    <?php
    return;

} else if (!empty($ownerId)) {
    // owner info
    $owner = (object)userGet($ownerId);
    //print_r($owner);

    ?><div class="member-info"><dl class="base_form_list">
      <dt>编号:</dt>
      <dd><?=$owner->id?></dd>

      <dt>名称:</dt>
      <dd><?=$owner->name?></dd>

      <dt>邮箱:</dt>
      <dd><?=$owner->email?></dd>

      <dt>电话:</dt>
      <dd><?=$owner->mobile?></dd>

      <dt>介绍:</dt>
      <dd><?=$owner->about_me?></dd>

    </dl></div>
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
        .frame-main { background: #fff; width: 240px; float: left; border-right: 1px solid #ddd; }
        .frame-side { background: #fff; width: 240px; position: absolute; left: 0;
        top: 0; bottom: 0; border-right: 1px solid #ddd; z-index: 100; }
        .frame-side2 { background: #fff; width: 240px; position: absolute; left: 240px; z-index: 99;  top: 0; bottom: 0; }
        .frame-side3 { background: #fff; position: absolute; 
            left: 480px; right: 0; z-index: 101; top: 0; bottom: 0; }

        .group_top_menu li a label { padding-left: 16px; }
        .member-info { padding: 16px 16px; }
    	.error-wrapper { margin: 16px 16px; padding: 8px 8px; border: 1px solid #ddd; background: #fee; color: #f99; }

        .group_top_menu li.selected { background: #ffe; }
    </style>
    <script>
        var gLastMemberId = '';

        function onRelayout() {

        }

        $(document).ready(function() {
            onRelayout();
        });

        $(window).resize(onRelayout);

        function onMemberClick(id) {
            $('#member_wrapper_' + gLastMemberId).removeClass('selected');

            if (id) {
                 var url = "?path=/manager/group_members&member_id=" + id;
                $("#block-right").html(getLoadingHtml()).load(url);

                $('#member_wrapper_' + id).addClass('selected');
            }

            gLastMemberId = id;
        }

        function onOwnerClick(id) {
            onMemberClick(0);
            
            var url = "?path=/manager/group_members&owner_id=" + id;
            $("#block-right").load(url);
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
    $result = onGroupMemberList($request);
    $members = $result['data'];

    // owner info
    $owner = userGet($groupInfo->owner_id);
    if ($owner) {
        $owner['user_id'] = $owner['id'];
    }

    $owner = (object)$owner;
?>
<div class="frame-table clearfix">
  <div class="frame-main frame-side"><div id="main-wrapper" class="wrapper">
    <div class="group_info">
      <h2><?=$groupInfo->name?></h2>
      <p><?=$groupInfo->description?></p>
    </div>
    <div class="group_top_menu">
      <ul>
        <li><a href="?path=/manager/group_detail&group_id=<?=$groupId?>">基本信息 </a></li>    

        <li class="selected"><a href="?path=/manager/group_members&group_id=<?=$groupId?>">
            成员信息 (<?=$groupInfo->member_count?>个成员)</a></li>
        <li><a href="?path=/manager/group_devices&group_id=<?=$groupId?>">
            设备信息 (<?=$groupInfo->device_count?>个视频)</a></li>

        <li><a href="?path=/manager/group_tags&group_id=<?=$groupId?>">
            标签</a></li>
           
      </ul>
    </div> 
  </div></div>

  <div class="frame-main frame-side2"><div id="wrapper" class="wrapper">
    <div class="group_info">
      <h2>群主:</h2>
    </div> 

    <div class="group_top_menu">
      <ul>
        <li><a href="javascript:onOwnerClick(<?=$owner->id?>);">
          <input type="checkbox"/><label><?=$owner->name?></a></label></li>
      </ul>
    </div> 

  	<div class="group_info">
  	  <h2>成员:</h2>
  	</div> 
  	<div class="group_top_menu">
      <ul>
      <?php if (empty($members)) { ?> <li><a href="#">暂无</a></li> <?php } else { ?>
      <?php foreach ($members as $member) { ?>
  	    <li id="member_wrapper_<?=safe_get($member, 'id')?>"><a href="javascript:onMemberClick(<?=safe_get($member, 'id')?>);">
          <input type="checkbox"/><label><?=safe_get($member, 'name')?></a></label></li>
      <?php } } ?>
      </ul>
  	</div> 
  </div></div>

  <div class="frame-side3" id="block-right">
    
  </div>
</div>

<?php } ?>

</body>
</html>