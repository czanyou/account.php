<?php
require_once "common.php";

$groupId = safe_get($_GET, "id");

?><!doctype html>
<html>
<head>
    <title>我的群组</title>
    <?php require_once("head.php"); ?>
    <script type="text/javascript">
        function onRelayout() {
            var viewHeight = $(window).height();
            $(".wrapper").height(viewHeight - 60);
        }

        $(document).ready(function() {
            onRelayout();
        });

        $(window).resize(onRelayout);

    </script>
    <style>
        .frame-table { background: #f8f8f9; }
        .frame-main { background: #fff; width: 300px; }
    </style>
</head>
<body>

<?php require_once("manager/header.php"); ?>
<div class="frame-table">

  <div class="frame-main"><div class="wrapper">
    <div class="group_info">
      <h2>我创建群组:</h2>
    </div>
    <div class="group_top_menu" onclick="onEditCancel();">
    <ul>
    <?php 
        $userId = $globalViewer->id;
        $groupDao = BaseService::getService("GroupDao");
        $result = $groupDao->getGroupsByOwner($userId);
        foreach ($result as $key) { ?>
    	<li><a href="?path=/manager/group_detail&group_id=<?=$key->id?>"><?=$key->name?></a></li>	
    <?php } ?>
    </ul>
    </div>
  </div></div>
</div>
</body>
</html>