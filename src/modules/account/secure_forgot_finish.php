<!DOCTYPE html>
<html>
<head>
  <?php include('account/secure_common.php') ?>
	<script type="text/javascript">
	<!--
	$(document).ready(function() {

	});

	//-->
	</script>
</head>
<body class="login_body">
<?php include('account/secure_header.php') ?>
<section id="body">

  <div class="body-wrapper">
 
	<div id="content">
      <h1 class="login-title"><label>找回密码</label></h1>

	  <?php
		$userid	= safe_get($_GET, "userid");
		$email	= safe_get($_GET, "email");
		if (!empty($userid)) { 
			OnSecureSendResetEmail($userid, $email);
		}
	  ?>

	  <div id="registerTipDiv">
		<?=L('验证码已发送到您的邮箱,')?> &nbsp; 
		  请查收邮件并 <a href="?path=/account/reset"><?=L('重置密码')?></a>
	  </div>
	</div>
  </div>
</section>
<?php include('common/footer.php') ?>
</body>
</html>
