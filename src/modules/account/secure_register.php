<!DOCTYPE html>
<html>
<head>
<?php include('account/secure_common.php') ?>
  <script type="text/javascript">
	var seed = 100;
	var checkType = "email";

	function OnFormSubmit() { 
		var em = $("#register_form").find("em").remove(); 
		var email = $('#email').val();
		var password = $('#password').val();
		var name = $('#name').val();

        password = hex_md5(password);
		
		var form_data = "email=" + email + "&name=" + name + "&password=" + password;
		$users.create("?path=/account/add&action=@add&groupId=@self&" + form_data);
	}

	$users.check = function(type, value) {
		var url = "?path=/account/";
		if (type == "email") {
			url += "checkEmail&action=@get&groupId=@search";
			$users.get(type, url + "&email=" + value);

		} else {
			url += "checkName&action=@get&groupId=@search";
			$users.get(type, url + "&name=" + value);
		}
	}

	$users.get = function(checkType, params) { 
		$.json.get(params, function(result, e) {
			var td = $("#" + checkType).parent();
			var em = td.find("em"); 
			em.show();
			em.empty();
			//alert(JSON.stringify(result));

			if (!result) {
				em.attr("class", "error");
				em.text("发生内部错误").show(); 
			}

			var ret = result.ret;
			if (ret) {
				em.attr("class", "error");
				if (checkType == "name"){
					em.text("这个用户名已经被注册了"); 
				} else {
					em.text("这个邮箱地址已经被注册了");
				}

			} else {
				em.attr("class", "success");

				if (checkType == "name") {
					//em.text("{#_REGISTER_NAME_OK_TIP}");

				} else {
					var tips = "";
					if (result.error) {
						tips = "ERROR:" + result.error.message;
					}
					em.text(tips);
				}
			}
		});
	}

	$(document).ready(function() {

		jQuery.validator.addMethod("token", function(value, element) { 
		  return this.optional(element) || /^[0-9a-zA-Z\_]+$/.test(value); 
		}, "请输入一个有效的用户名");

		var validator = $("#register_form").validate( {	
			errorElement: "em",
			rules: {
				name			: { required: true, minlength: 3, token: true },
				email			: { required: true, email: true },
				password		: { required: true, minlength: 6},
				checkpassword	: { required: true, minlength: 6, equalTo: "#password" },
				code			: { required: true, equalTo: "#tempcode" }
			},

			messages: {
				checkpassword: { equalTo: "两次输入的密码不一样" },
				code: { equalTo: "错误的验证码" }
			}
		});

		$("#register_checkcode").click(function (){
			var url = "modules/account/checkcode.gif.php?seed=" + Math.random();
			$("#register_checkcode_img").attr("src", url);
			return false;
		});

		$("#check_agree").click(function (){ 
			 $('#submit').attr("disabled", !$("#check_agree").attr("checked")); 
		});
		
		$("#code").focus(function(){
			$("#tempcode").attr("value", $.getCookie("randcode"));
		});

		$("#name").blur(function () { 
			if (validator.element("#name")) {
				var td = $("#name").parent();
				var em = td.find("em");
				em.remove();
				if (!em.html()) {
					var oNewp = $("<em></em>"); 
					oNewp.insertAfter("#name"); 
				}
				$users.check("name", $(this).val());
			}
		})

		$("#email").keyup(function () {
			if (validator.element("#email")) {
				$users.check("email", $(this).val());
			}
		});
	
		$("#email").blur(function () {  
			if (validator.element("#email")) {
				var td = $("#email").parent();
				var em = td.find("em"); 
				if (!em.html()) {
					var oNewp = $("<em></em>"); 
					oNewp.insertAfter("#email"); 
				}
				$users.check("email", $(this).val());
			}
		})
		//$("#warning-box").html("sfdasfds").show();
		OnLoginPageRelayout();
	});

	function OnLoginPageRelayout() {
		var height = $(window).height();
		height = Math.max(450, height - 180);
		//$("#register-block").css("min-height", height);
	}

	$(window).resize(OnLoginPageRelayout);

  </script>
</head>
<body class="register_body">
<?php include('account/secure_header.php') ?>
<section id="body" class="register-body">
  <div class="body-wrapper"> 
	
	<div id="register-block" class="clearfix"> 
	  <form id="register_form" name="register_form" method="post" action="javascript:OnFormSubmit();">
		<input type="hidden" id="tempmail" name="tempmail"/>
		<input type="hidden" id="tempname" name="tempname"/>
		<input type="hidden" id="tempcode" name="tempcode"/>
		<dl>
	    <dt><h1 class="login-title"><label>免费注册</label></h1></dt>
		
			<dd height="25px"><td colspan="2" style="padding-left:100px">
				<div id="warning-box" class="warning-box"></div>
			</dd>
		
			<dt>邮箱地址 :</dt>
			  <dd><input class="text" type="email" name="email" id="email" placeholder="邮箱地址"/></dd>

	        <dt>用户名 :</dt>
			  <dd><input class="text" type="text" name="name" id="name" placeholder="用户名"/></dd>
		  
			<dt>密码 :</dt> 
			  <dd><input class="text" type="password" name="password" id="password" placeholder="密码"/></dd>

			<dt>确认密码 :</dt> 
			  <dd><input class="text" type="password" name="checkpassword" id="checkpassword" placeholder="再次确认密码"/></dd>

			<dt>验证码 :</dt>
			  <dd><input class="text" type="text" id="code" name="code" maxlength="5" size="6" placeholder="验证码"/></dd>

			  <dd><img id="register_checkcode_img" class="absmiddle" src="modules/account/checkcode.gif.php" style="width:150px; height:30px; border: 1px solid #ddd;" alt="checkcode"/> &nbsp; <a href="javascript:void(0)" id="register_checkcode"> 看不清, 换一个</a> </dd>

			  <dd><input type="checkbox" id="check_agree" checked="checked" name="agree"><label for="check_agree"> 同意服务条款</label> </dd>

			  <dd><input type="submit" name="submit" id="submit" class="rbutton submit-button" value="立即注册"/></dd>
			  <dt>&nbsp;</dt>
		</dl>
	  </form>
	</div>

	<div class="block" id="register_tip_block" style="display:none;">
	  <div class="profile_content"><div class="content"></div></div>
	</div>
	
  </div>
</section>
<?php include('common/footer.php') ?>
</body>
</html>
