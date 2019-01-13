<!DOCTYPE html>
<html>
<head>
<?php include('account/secure_common.php') ?>
<?php
	$code	  = safe_get($_GET, "code"); 
	$email	= safe_get($_GET, "email"); 
	$userId = safe_get($_GET, "userId");

	$timenow = strtotime(date("Y-m-d H:i:s",strtotime("now"))); 
	 
?>
  <script type="text/javascript">	

	$(document).ready(function() {
		$("#resetForm").validate( {
			errorElement : "em",
			rules : {
				password : { required: true, minlength: 6 },
				confirm_password : { required: true, minlength: 6, equalTo: "#password"}
			}
		});
	});

  </script>
</head>
<body class="login_body">
<?php include('account/secure_header.php') ?>
<section id="body" class="clearfix">
  <div class="body-wrapper">
	<div id="content">
  	  <h1 class="login-title"><label>重置密码</label></h1>
	  <form id="resetForm" method="post" action="?path=/account/reset">
	  	<div class="name">邮箱地址:</div>
		<div><input class="text" type="text" id="email" name="email" value="<?=$email?>"/></div>
		<div class="name">重置代码:</div>
		<div><input class="text" type="text" id="code" name="code" value="<?=$code?>"/></div>
		<div class="name">新的密码:</div>
		<div><input class="text" type="password" id="password" name="password" value="123456"/></div>
		<div class="name">再次确认密码:</div> 
		<div><input class="text" type="password" id="confirm_password" name="confirm_password" value="123456"/></div>
		<div class="name"><input type="submit" name="submit" class="rbutton" value="确定"/></div>
		<div>&nbsp;</div>
	   </form>
	</div>
  </div>
</section>
<?php include('common/footer.php') ?>
</body>
</html>
