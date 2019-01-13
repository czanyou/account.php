
function InitLoginForm() {
	$('#login-form').submit(function() {
		if (!$(this.username).val()) {
			$('#error_block').html('<p class="error">请输入账号名. </p>');
			return false;

		} else if (!$(this.password).val()) {
			$('#error_block').html('<p class="error">请输入密码.</p>');
			return false;
		}

		var username = $(this.username).val() || ""; 

		if ($(this.password).val() != "" && $(this.password).val().length != 32) {
			$(this.password).val($(this.password).val());
		}
		
		return true;
	});
}

var $users = {
	create : function(url) {
		$.json.put(url, null, function(result, e) { 
			//alert(JSON.stringify(result));return;
			if (result && result.error) {
				//alert(result.error.message);
				$("#warning-box").html(result.error.message).show();
				return;
			}
			if (!result.entry) {
				return;
			}
			
			var entry = result.entry;
			location.href = "?path=/account/login&q=register_finish&email=" + 
				entry.email + "&password=" + entry.password;
		}); 
	},

	update : function(url) {  
		$.json.put(url, null, function(result, e) {  
			if (!result.entry) {
				return;
			}

			alert($lang["_FORGET_RESET_DONE_TIP"]);
			location.href = '?path=/account/login';
		}); 
	}
};

