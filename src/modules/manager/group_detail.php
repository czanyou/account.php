<?php
require_once "group_common.php";

require_once "common/api_dispatch.php";

$groupId  = safe_get($_GET, "group_id");
$action   = safe_get($_GET, "action");
$name     = safe_get($_GET, "name");

$openid   = safe_get($_GET, "openid");
$openkey  = safe_get($_GET, "openkey");
$clientId = safe_get($_GET, "client_id");

$userId  = $globalViewer->id;
$groupDao = BaseService::getService("GroupDao");
$error   = null;
//print_r($groupInfo);

function groupEdit() {
    $id = safe_get($_GET, "group_id");
    if (empty($id)) {
        return "没有提供 ID";
    }

    $name = safe_get($_GET, "name");
    $description = safe_get($_GET, "description");

    $entry = array();
    $entry['name']          = $name;
    $entry['description']   = $description;

    $groupDao = BaseService::getService("GroupDao");
    $result = $groupDao->updateGroup($id, $entry);
 
    return 0;
}

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

} else if ($action == 'update') {
    groupEdit();

}

?><!doctype html>
<html>
<head>
    <title>群组详情</title>
    <?php require_once("head.php"); ?>
    <style>
        .frame-table { background: #f8f8f9; }
        .frame-main { width: 320px; background: #fff; float: left; }
        .rside { margin-left: 321px; overflow: auto; }
        .group_info_wrapper { padding: 16px 16px; }
    	.error-wrapper { margin: 16px 16px; padding: 8px 8px; border: 1px solid #ddd; background: #fee; color: #f99; }
    </style>
    <script>
        function onRelayout() {
            var viewHeight = $(window).height();
            $("#wrapper").height(viewHeight - 60);
            $("#rside").height(viewHeight - 60);
        }

        $(document).ready(function() {
            onRelayout();
        });

        $(window).resize(onRelayout);

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
  ?>

<div class="frame-table"> 
  <div class="frame-main"><div id="wrapper" class="wrapper">
	<div class="group_info">
	  <h2><?=$groupInfo->name?></h2>
	  <p><?=$groupInfo->description?></p>
	</div> 

	<div class="group_top_menu"><ul>
      <li class="selected"><a href="?path=/manager/group_detail&group_id=<?=$groupId?>">
        基本信息 </a></li>    
	  <li><a href="?path=/manager/group_members&group_id=<?=$groupId?>">
        成员信息 (<?=$groupInfo->member_count?>个成员)</a></li>
      <li><a href="?path=/manager/group_devices&group_id=<?=$groupId?>">
        设备信息 (<?=$groupInfo->device_count?>个视频)</a></li>
      <li><a href="?path=/manager/group_tags&group_id=<?=$groupId?>">
        标签</a></li>

	</ul></div> 
  </div></div>

  <div id="rside" class="rside">
    <div class="group_info_wrapper">
      <?php if ($action != 'edit') { ?><form method="get">
      <input type="hidden" name="group_id" value="<?=$groupInfo->id?>"/>
      <input type="hidden" name="action" value="edit"/>
      <dl class="base_form_list">
        <dt>ID</dt>
        <dd><?=$groupInfo->id?></dd>        

        <dt>名称</dt>
        <dd><?=$groupInfo->name?></dd>

        <dt>简介</dt>
        <dd><?=$groupInfo->description?></dd>

        <dt>类型</dt>
        <dd><?=$groupInfo->privacy == 2 ? '[企业群组]' : '[私人群组]'?></dd>

        <dt>创建时间</dt>
        <dd><?=date('Y-m-d H:i:s', $groupInfo->created) ?></dd>

        <dt>图片</dt>
        <dd><img src="<?=$groupInfo->thumbnail?>" style="width:320px; height:180px;"/></dd>

        <dd><input type="submit" class="rbutton" name="submit" value="修改信息"/></dd>
      </dl></form>

      <?php } else { ?><form>
      <input type="hidden" name="group_id" value="<?=$groupInfo->id?>"/>
      <input type="hidden" name="action" value="update"/>

      <dl class="base_form_list">
        <dt>ID</dt>
        <dd><?=$groupInfo->id?></dd>        

        <dt>名称</dt>
        <dd><input type="text" class="text" name="name" value="<?=$groupInfo->name?>"/></dd>

        <dt>简介</dt>
        <dd><input type="text" class="text" name="description" value="<?=$groupInfo->description?>"/></dd>

        <dt>类型</dt>
        <dd><?=$groupInfo->privacy == 2 ? '[企业群组]' : '[私人群组]'?></dd>

        <dt>创建时间</dt>
        <dd><?=date('Y-m-d H:i:s', $groupInfo->created) ?></dd>
        <dd><input type="submit" class="button" name="submit" value="提交"/></dd>
      </dl></form>

      <form action="?path=/manager/group_detail&action=upload&group_id=<?=$groupInfo->id?>" method="post" enctype="multipart/form-data">
      <dl class="base_form_list">
        <dt>图片</dt>
        <dd><?=$groupInfo->thumbnail?></dd>
        <dd><img src="<?=$groupInfo->thumbnail?>" style="width:320px; height:180px;"/></dd>

        <dt>上传新图片</dt>
        <dd><input type="file" name="pic"/></dd>
        <input type="hidden" name="group_id" value="<?=$groupInfo->id?>"/>
        <input type="hidden" name="action" value="upload"/>

        <dd><input type="submit" class="button" name="submit" value="上传"/></dd>
      </dl></form>
      <?php } ?>
    </div>
  </div>
</div> 

<?php } ?>

</body>
</html>