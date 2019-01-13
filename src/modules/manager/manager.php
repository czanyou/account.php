<?php
require_once "common.php";

import('core.vision.UserDao');
VisionCheckLoginStatus();

global $globalViewer;

$user = $globalViewer;

?><!doctype html>
<html>
<head>
    <title>个人信息</title>
    <?php require_once("manager/head.php"); ?>
    <script type="text/javascript">

        function onRelayout() {
            
        }

        $(document).ready(function() {
            onRelayout();

            $("#mitem_home").addClass("selected");
        });

        $(window).resize(onRelayout);

    </script>
    <style>
      .profile-block { margin: 16px 24px; }
      .profile-block h2 { line-height: 300%; border-bottom: 1px solid #ddd; margin-bottom: 16px; }
      .profile-block dl { padding: 4px 8px; }
      .profile-block dt { font-size: 100%; color: #ff6538; padding: 4px 0; }
      .profile-block dd { font-size: 110%; margin-bottom: 16px; }

    @media screen and (max-width: 480px) { 
    	.frame-left a { display: block; border-bottom: 1px solid #e5e5e5; line-height: 48px; font-size: 17px; }
    }
    </style>
</head>
<body>
  <?php require_once("manager/header.php"); ?>
  <div class="frame-table">
    <div class="frame-main">
      <div id="wrapper" class="wrapper">
        <div class="profile-block">
          <h2>个人信息</h2>
          <dl>
            <dt>用户ID:</dt>
            <dd><?=$user->id?></dd>
            <dt>名称:</dt>
            <dd><?=$user->name?></dd>
            <dt>邮箱地址:</dt>
            <dd><?=$user->email ? $user->email : '-' ?></dd>
            <dt>手机:</dt>
            <dd><?=$user->mobile ? $user->mobile : '-' ?></dd>
            <dt>简介:</dt>
            <dd><?=$user->about_me ? $user->about_me : '-' ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</body>
</html>