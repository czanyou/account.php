<?php
require_once "common.php";

VisionCheckLoginStatus();

?><!doctype html>
<html>
<head>
    <title>控制面板</title>
    <?php require_once("manager/head.php"); ?>
    <script type="text/javascript">

    </script>
    <style>
    @media screen and (max-width: 480px) { 
    	.frame-left a { display: block; border-bottom: 1px solid #e5e5e5; line-height: 48px; font-size: 17px; }

    }
    </style>
</head>
<body>
  <?php require_once("manager/header.php"); ?>
  <div class="frame-header">
	  <div class="frame-left"></div>
  </div>

  <div class="list_divider">&nbsp;</div>
</body>
</html>