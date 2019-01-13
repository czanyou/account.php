<?php

# markdown to html
# ======
# 

define('S_ROOT', dirname(__FILE__));    ///< System Root Path

# Query seed
# 通过修改这个值来实现更新浏览器缓存的功能
$Q = date("Ymd") . 100014;

# 
require_once('assets/Parsedown.class.php');


/**
 * 一个安全的取得一个数组或者对象的指定的名称的属性的值的方法.
 * @param object 一个对象或者数组
 * @param name 要查询的属性的名称
 * @param default 如果指定的属性不存在, 返回的默认值.
 * @author chengzhen (anyou@msn.com)
 */
function safe_get($object, $name, $default = null) {
    if (is_array($object)) {
        return (isset($object[$name])) ? $object[$name] : $default;

    } else if (is_object($object)) {
        return (isset($object->$name)) ? $object->$name : $default;

    } else {
        return $default;
    }
}

// ------------------------------------------------------------
// context menu

function md_add_toc($parser, $text) {

    $headers = array();
    $lastLevel = 1;

    $headerCount = 0;

    $headers[] = '<div class="toc">';
    $headers[] = '<ul class="l1">';

    foreach ($parser->headers as $header) {
        $element = $header["element"];
        $level   = $element['level'];
        $name    = $element['text'];
        $index   = $element['index'];

        $indent = '';
        for ($i = 1; $i < $level; $i++) {
            $indent .= '  ';
        }

        //echo $level, " - ", $name, "\r\n";
        if ($level > $lastLevel) {
            for ($i = $lastLevel; $i < $level; $i++) {
                $headers[] = $indent . '<ul class="l' . ($i + 1) .'"> ';
            }

        } else if ($level < $lastLevel) {
            for ($i = $level; $i < $lastLevel; $i++) {
                $headers[] = $indent . '</ul>';
            }
        }

        $lastLevel = $level;

        $headerCount++;
        $headers[] = $indent . '<li>' . '<a href="#h' . $index . '">' . $name . '</a></li>';
    }

    for ($i = 1; $i < $lastLevel; $i++) {
        $headers[] = '</ul>';
    }

    $headers[] = '</ul>';
    $headers[] = '</div>';

    if ($headerCount > 0) {
        $menuText =  join("\r\n", $headers);
        $text = str_replace("[TOC]", $menuText, $text);
    }

    return $text;
}


function md_load($page, $file) {
    $filename = S_ROOT . "/" . $page . "/" . $file;
    if (!file_exists($filename)) {
        return "File not found: " . $filename . "\r\n";
    }

    $fileData       = implode('', file($filename));
    $parser         = Parsedown::instance();
    $articleHtml    = $parser->text($fileData);
    $articleHtml    = md_add_toc($parser, $articleHtml);

    return $articleHtml;
}

