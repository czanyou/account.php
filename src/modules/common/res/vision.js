	
;(function($) {
	
	$.setElementValue = function (frm, name, value) {	
		try {
			var item = frm[name];
			if (!item) {
				item = document.getElementById(name);
				if (item) {
					item.innerHTML = value;
				}
				
				return;
			}
			
			if (item.tagName) {
				if (item.type == "checkbox") {
					item.checked = (value == item.value);

				} else if (item.type == "radio") {
					item.checked = (value == item.value);

				} else {
					item.value = value;
				}

			} else if (item.length > 0) {
				for (var i = 0; i < item.length; i++) {
					var box = item[i];
					
					if (typeof value == 'object') {
						box.checked = value[box.value];
						
					} else {
						box.checked = (value == box.value);
					}
				}
			}

		} catch (e) {
		}
	}

	$.fn.dialog = function (params) {
		var dialog = this.get(0);
		
		$window = $(window);
		var height = $window.height();
		var width  = $window.width();
		
		var left = Math.floor((width  - this.width())  / 2);
		var top  = Math.floor((height - this.height()) / 2);

		this.children(".ui-dialog-content").height(this.height() - 84);
		this.show().css('left', left + "px").css('top', top + "px");
		return this;
	}

	$.fn.resetForm = function() {
		return this;
	}

	$.fn.clearForm = function() {
		return this;
	}

	$.fn.initForm = function (params) {
		var form = this.get(0);
		if (typeof params != "undefined" && params) {
			for (var name in params) { 
				var value = (params[name] == "null" || params[name] == null) ? "" : params[name];
				$.setElementValue(form, name, value);
			}		
		}
		return this;
	}

	$.fn.showInfo = function(info) {
		var buttonHtml = '<a class="close-box-button" href="#" onclick="$.hideInfo();" title="Close">X</a>';
		$(this).html(info + buttonHtml).fadeIn("fast");
	}

	$.hideInfo = function() {
		$('#message-box').hide(); 
	}
  
	function JSONRequest(base_url) {
		if (base_url) {
			this.base_url = base_url;
		}
		return this;
	}

	/** 
	 * @param url string
	 * @param done function()
	 * @param timeout number 
	 */
	JSONRequest.prototype.get = function(url, done, timeout) {
		return this.send("GET", url, done, timeout);
	}

	JSONRequest.prototype.del = function(url, done, timeout) {
		return this.send("DELETE", url, done, timeout);
	}

	JSONRequest.prototype.put = function(url, data, done, timeout) {
		return this.send("PUT", url, done, timeout, data);
	}

	JSONRequest.prototype.post = function(url, data, done, timeout) {
		return this.send("POST", url, done, timeout, data);
	}

	JSONRequest.prototype.send = function(method, url, done, timeout, data, type) {
		return $.ajax({
			type	: method,
			url		: (this.base_url || "") + url,
			data	: data,
			timeout : timeout || 5000,
			dataType : type || "json",
			success  : done,
			contentType : "application/x-www-form-urlencoded;charset=UTF-8",
			complete : function(request, status) {
				if ($.json.onComplete) {
					$.json.onComplete(request, status);
				}
			},
			beforeSend : function(request) {				
				request.setRequestHeader("Accept", "application/json");
				if ($.json.onStart) {
					$.json.onStart(request);
				}
			}
		});
	}

	$.json = new JSONRequest(null);

	///////////////////////////////////////////////////////////////////////////////
	// Cookie

	function GetCookieVal(offset) {
		var endstr = document.cookie.indexOf(";", offset);
		if (endstr == -1) {
			endstr = document.cookie.length;
		}
		return unescape(document.cookie.substring(offset, endstr));
	}

	$.delCookie = function (name) {
		var exp = new Date();
		exp.setTime (exp.getTime() - 1);
		var cval = this.getCookie (name);
		document.cookie = name + "=" + cval + "; expires="+ exp.toGMTString();
	}

	$.setCookie = function (name, value, expires, path, domain, secure) {
		var expires_date = new Date();
		if (expires) {
			expires_date.setTime(expires_date.getTime() + ( expires * 1000 ));
		}

		var cookie = name + "=" + escape (value)
			+ ((expires) ? ';expires=' + expires_date.toGMTString() : '')
			+ ((path) ? ";path=" + path : '')
			+ ((domain) ? ";domain=" + domain : '')
			+ ((secure) ? ";secure" : '');

		document.cookie = cookie;
	};

	$.getCookie = function (name) {
		var arg = name + "=";
		var alen = arg.length;
		var clen = document.cookie.length;
		var i = 0;

		while (i < clen) {
			var j = i + alen;
			if (document.cookie.substring(i, j) == arg)
				return GetCookieVal (j);

			i = document.cookie.indexOf(" ", i) + 1;
			if (i == 0) 
				break;
		}
		return null;
	};

})(jQuery);

if (!window.localStorage) {
	window.localStorage  = [];
}

function padTime(value) {
	return value < 10 ? '0' + value : value;
}

Date.prototype.setISO8601 = function(dString) {
	var regexp = /(\d\d\d\d)(-)?(\d\d)(-)?(\d\d)(T)?(\d\d)(:)?(\d\d)(:)?(\d\d)(\.\d+)?(Z|([+-])(\d\d)(:)?(\d\d))/;

	if (dString.toString().match(new RegExp(regexp))) {
		var d = dString.match(new RegExp(regexp));
		var offset = 0;

		this.setUTCDate(1);
		this.setUTCFullYear(parseInt(d[1],10));
		this.setUTCMonth(parseInt(d[3],10) - 1);
		this.setUTCDate(parseInt(d[5],10));
		this.setUTCHours(parseInt(d[7],10));
		this.setUTCMinutes(parseInt(d[9],10));
		this.setUTCSeconds(parseInt(d[11],10));

		/*if (d[12])
			etUTCMilliseconds(parseFloat(d[12]) * 1000);
		else
			this.setUTCMilliseconds(0);*/

		if (d[13] != 'Z') {
			offset = (d[15] * 60) + parseInt(d[17],10);
			offset *= ((d[14] == '-') ? -1 : 1);
			this.setTime(this.getTime() - offset * 60 * 1000);
		}
	} else {
		this.setTime(Date.parse(dString));
	}
	return this;
};

Date.prototype.toLocaleISOString = function() {
	var text = "";
	text += this.getFullYear();
	text += "-";
	text += padTime(this.getMonth() + 1);
	text += "-";
	text += padTime(this.getDate());
	text += " ";
	text += padTime(this.getHours());
	text += ":";
	text += padTime(this.getMinutes());
	text += ":";
	text += padTime(this.getSeconds());
	return text;
}

/** Calendar */
var $calendar 	= {};

$calendar.init = function () {
	var date   = new Date();

	this.date  = date;
	this.year  = date.getFullYear();
	this.month = date.getMonth() + 1;
	this.day   = date.getDate();

	OnCalendarUpdateMonthAndDays(this.month);
}

function OnCalendarDayChange(day) {
	$calendar.day = day;

	var days = $('.calendar-day-block a'); 
	days.each(function() {
		if (this.id == "day-" + day) { 
			$(this).addClass("checked");

		} else {
			$(this).removeClass("checked");
		}
	});

	if ($calendar.OnCalendarChange) {
		$calendar.OnCalendarChange();
	}
}

function OnCalendarMonthChange(month) {
	$calendar.day = null;
	$calendar.month = month;

	OnCalendarUpdateMonthAndDays(month);

	if ($calendar.OnCalendarChange) {
		$calendar.OnCalendarChange();
	}
}

function OnCalendarYearChange(type) {
	$calendar.year +=  ('next' == type) ?  1 : -1;

	$("#this-year-box").text($calendar.year); 
	OnCalendarUpdateMonthAndDays($calendar.month);

	if ($calendar.OnCalendarChange) {
		$calendar.OnCalendarChange();
	}
}

function OnCalendarUpdateDays(year, month) {
	var dayCountOfFebruary = 28;
	if (year % 4 == 0) {
		dayCountOfFebruary = 29;
	}

	if ((year % 100 == 0) & (year % 400 != 0)) {
		dayCountOfFebruary = 28;
	}

	var dayCountOfMonth = [31, dayCountOfFebruary, 31,  30, 31, 30,  31, 31, 30,  31, 30, 31];
	var html = '';

	// Week block
	var weekdays = $lang['_WEEKS'] || "";
	weekdays = weekdays.split(',');
	html += '<div class="calendar-week-block">';
	for (var i = 0; i < 7; i++) {
		html += '<label>';
		html += weekdays[i] || "";
		html += '</label>';
	}
	html += "</div>";

	// Black days
	var date = new Date();
	date.setFullYear(year, month - 1, 1);
	var weekday = date.getDay();
	for (var i = 0; i < weekday; i++) {
		html += '<a>';
		html += "&nbsp";
		html += '</a>';
	}

	var isThisMonth = false;
	var today = new Date();
	if (today.getFullYear() == year && (today.getMonth() + 1) == month) {
		isThisMonth = true;
	}
	
	// Days
	var count = dayCountOfMonth[month - 1];
	for (var i = 1; i <= count; i++) { 
		html += '<a'; 

		var className = "";
		if ($calendar.day == i) {
			className += ' checked';
		} 

		if (isThisMonth && i == today.getDate()) {
			className += ' day-today';
		}

		html += ' class="' + className + '"';
		html += ' onclick="OnCalendarDayChange(' + i + ');" id="day-' + i + '">' + i + '</a>';
	}
	 
	$('#calendar-day-block').html(html);
}

function OnCalendarUpdateMonthAndDays(month) {
	var month = month || $calendar.month;

	var months = $('#calendar-month-block a'); 
	months.each(function() {
		if (this.id == "rcm-" + month) {
			$(this).addClass("checked");

		} else {
			$(this).removeClass("checked");
		}
	});

	OnCalendarUpdateDays($calendar.year, month);
}


///////////////////////////////////////////////////////////////////////////////

function getLoadingHtml(height) { 
	var html = '<div';
	if (height) {
		html += ' style="height:' + height + 'px;"';
	}
	html += '><div class="loading-box" style="padding: 16px 16px;">';
	html += '<img src="images/blue_loading.gif" class="absmiddle"/> &nbsp;';
	html += '<label>';
	html += window.LOADING || "Loading, please wait...";	
	html += '</label></div></div>';
	return html;
}

var now = new Date();
var gmtseconds = now.getTimezoneOffset() * 60;
$.setCookie("time_zone", gmtseconds, 36000 * 24 * 360);


