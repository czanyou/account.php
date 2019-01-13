<?php

function groupPrintList() {
	echo "var html = '<h2>我的群组</h2>';\r\n";
    echo "html += '<ul>';\r\n";

    global $globalViewer;
    $groupId  = safe_get($_GET, "group_id");

    $userId = $globalViewer->id;
    $groupDao = BaseService::getService("GroupDao");
    $result = $groupDao->getGroupsByOwner($userId);
    foreach ($result as $key) { 
        $clazz = ($groupId == $key->id) ? "selected" : "";
        echo "html += '<li class=\"", $clazz, "\"><a href=\"?path=/manager/group_detail&group_id=", $key->id, "\">", $key->name, "</a></li>';\r\n";
    }
    
    echo "html += '</ul>';\r\n";
    echo "$(document).ready(function() {\r\n";
    echo "    $('#side_menu').html(html);\r\n";
    echo "});\r\n";
}


