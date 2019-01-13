<!DOCTYPE html>
<html>
<head>
  <?php include('account/secure_common.php') ?>
	<script type="text/javascript">
	<!--
	$(document).ready(function() {
		$("#forgetForm").validate( {
			errorElement : "em",
			rules : { email : { required: true, email: true } },
			messages : { email: "请输入正确的邮箱地址" }
		}); 
	});

	function OnFormSubmit() {
		var email = $("#email").val();
		if (email.indexOf("@") != -1 && email.indexOf(".") != -1) {
			$.get("<?=S_PATH?>/?api=u/forgot&email=" + email, null, function(result) {
				location.href = "?path=/account/forget_finish&email=" + email;
			});
		}
	}

	//-->
	</script>
</head>
<body class="login_body">
<?php include('account/secure_header.php') ?>
<section id="body">
  <div class="body-wrapper">
	<div id="content">
	  <form id="forgetForm" name="forgetForm" method="post" action="javascript:OnFormSubmit();"> 
    <h1 class="login-title"><label>找回密码</label></h1>
		<div>请输入您注册时使用的邮箱地址</div>
		<div class="name" style="padding-bottom: 10px;">
		  <input class="text" type="text" id="email" name="email" placeholder="邮箱地址" /></div>
		<div><input type="submit" name="submit" class="rbutton" value="确定"/></div>
	  </form>
	</div>
  </div>
</section>
<?php include('common/footer.php') ?>
</body>
</html>
