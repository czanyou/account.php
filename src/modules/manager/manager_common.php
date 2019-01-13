<?php

require_once "common.php";

function managerShowPager($total, $page, $path) {
	$pageUtils = new PageUtils($total, $page);
    $pageUtils->show2($path);
}

function managerSetPagerParams($request, &$params) {
    $page   = safe_get($request, "page");
    
    if (empty($page)) {
        $page = 1;

    } else {
        $start = max(0, ($page - 1) * 20);
        $params['startIndex'] = $start;
    }

    return $page;
}
