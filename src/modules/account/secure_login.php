<!DOCTYPE html>
<html>
<head>
  <?php include('account/secure_common.php') ?>
	<title>登录</title>
  <script type="text/javascript">
	var $lang = {};

	function OnPageResize() {
		var viewHeight = $(window).height();
		var bodyHeight = viewHeight - 170;
		$("#body").height(bodyHeight < 400 ? 400 : bodyHeight);
	}

	$(document).ready(function() { 
		InitLoginForm(); 
		OnPageResize();
	});

	$(window).resize(OnPageResize);

  </script>
</head>
<body class="login-body login-body3">
<?php include('account/secure_header.php') ?>
<section class="body-wrap" id="body" ng-app="loginApp" ng-controller="loginCtrl">
	<script>
	var app = angular.module('loginApp', []);
	app.controller('loginCtrl', function($scope, $location) {
		var $search = $location.search();
			$scope.error 	  = "<?=$error?>";
			$scope.callback = "<?=safe_get($_GET, 'callback')?>";
			$scope.username = "<?=safe_get($_GET, 'username')?>";
	});
	</script>

	<div id="login-block" class="body-wrapper">
	  <form id="login-form" name="login-form" method="post" action="?path=/account/login">
		<input type="hidden" name="path" value="/account/login"/>
		<input type="hidden" name="callback" value="{{callback}}"/>
		<ul>
		  <li><h1 class="login-title">登录</h1></li>
		  <li><label>账户和密码:</label></li>
		  <li>&nbsp;</li>
		  <li><input class="text" type="text" name="username" value="{{username}}" placeholder="邮箱地址或手机号码"/></li>
		  <li><input class="text" type="password" name="password" placeholder="密码"/></li>		
		  <li id="error_block" ng-if="error">
			  <p class="error">{{error}}</p>
		  </li>
		  <li>&nbsp;</li>
		  <li><button type="submit" name="submit" class="rbutton submit-button">立即登录</button></li>
		  <li>&nbsp;</li>
		  <li><a href="?path=/account/forget" class="link-forget absmiddle">忘记密码了?</a></li>
		</ul>
	  </form>
	</div>
</section>
<?php include('common/footer.php')?>

</body>
</html>
