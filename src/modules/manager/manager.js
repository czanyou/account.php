
function lsideSelect(item) {
	$("#mitem_" + item).addClass("selected");
}

function rsideHideAll() {
	$("#rside").children(".side-right").hide()
    $("#rside").hide()
}

function onLogout() {
	if (!window.confirm('将退出登录')) {
		return;
	}

	location.href = '?path=/account/logout';
}

function onAdd() {
    var box = $('#addPopBox')
	if (box.is(':hidden')) {
		rsideHideAll()
    	box.show();
        $("#rside").show("fast");

	} else {
        $("#rside").hide("fast");
    }
}

function onEditDefault(path, id) {
    rsideHideAll();

    var url = path + "&action=edit_form&id=" + id;
    var html = "<div style='padding: 16px 16px;'>Now loadding...</div>"
	$('#editPopBox').show().empty();
	$('#editPopBox').html(html).load(url);

    $("#rside").show("fast");
}

function onFormSubmitDefault(path, id, callback) {
    var data = $("#" + id).serialize();

    //$.hideInfo();
    $('#message-box').showInfo(getLoadingHtml());

    var url = path + "&" + data;
    $.get(url, callback);
}

function onRemoveDefault(path, id, callback) {
    if (!window.confirm("将删除这个记录?")) {
        return;
    }

    $('#message-box').showInfo(getLoadingHtml());

    var url = path + "&action=remove&id=" + id;
    $.get(url, callback)
}

function onRefreshDefault(path, params, page) {
    params = params || "";
    if (page) {
        params += "&page=" + page
    }

    var url = path + "&action=list" + params;
    $("#frame-body").load(url, onRelayout)

    var href = path + "&action=" + params;
    onHistorySaveState(url, href);
}

function onSearch() {
	var box = $('#searchPopBox')
	if (box.is(':hidden')) {
		rsideHideAll()

        $("#rside").show("fast");
    	box.show();

	} else {
        $("#rside").hide("fast");
    }
}

function onSearchSubmitDefault(path) {
    var params = "&" + $("#searchForm").serialize();
    onRefreshDefault(path, params)
} 

function onPage(page) {
	onRefresh(page)
}

function onHistoryParams(names) {
	var params = ""
	var count = names.length;
	for (var i = 0; i < count; i++) {
		var name = names[i];

		if (page) {
            params += "&page=" + page
        }
	}

	return params;
}

function onHistorySaveState(url, href) {
	try {
		var state = {type:'list', url:url}
   		window.history.pushState(state, ",", href); 
   	} catch (e) {
        console.log(e)
    }	
}

function onHistoryPopState(e) {
    //console.log(e.state);

    var state = e.state;
    if (state && state.url) {
        $("#frame-body").load(state.url, onRelayout)
    }
}
