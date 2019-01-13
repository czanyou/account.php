<?php
require_once "common.php";

?><!doctype html>
<html>
<head>
    <title>我的群组</title>
    <?php require_once("common/head.php"); ?>
    <script type="text/javascript">

    </script>
</head>
<body>

<header><h1><a href="?path=/manager/profile">我的群组</a></h1></header> 

<div class="list_header">我创建的群组:</div>
<div class="group_top_menu" onclick="onEditCancel();">
<ul>
<?php 
    $userId = $globalViewer->id;
    $groupDao = BaseService::getService("GroupDao");
    $result = $groupDao->getGroupsByOwner($userId);
    foreach ($result as $key) { ?>
	<li><a href="?path=/manager/group&id=<?=$key->id?>"><?=$key->name?></a></li>	
<?php } ?>
</ul>
</div>

<div class="list_header">我加入的群组:</div>
<div class="group_top_menu" onclick="onEditCancel();">
<ul>
<?php 
    $result = $groupDao->getGroupsOfJoined($userId);
    foreach ($result as $key) { ?>
    <li><a href="?path=/manager/group&id=<?=$key->id?>"><?=$key->name?></a></li>   
<?php } ?>
</ul>
</div>

</body>
</html>