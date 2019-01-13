<?php
!defined('IN_VISION') && exit('Access Denied');

///////////////////////////////////////////////////////////////////////////////
// PHP 系统级工具方法 

/**
 * Cookie 设置、获取、清除 (支持数组或对象直接设置) 2009-07-9
 * 1 获取cookie: cookie('name')
 * 2 清空当前设置前缀的所有cookie: cookie(null)
 * 3 删除指定前缀所有cookie: cookie(null,'think_') | 注：前缀将不区分大小写
 * 4 设置cookie: cookie('name','value') | 指定保存时间: cookie('name','value',3600)
 * 5 删除cookie: cookie('name',null)
 * $option 可用设置prefix,expire,path,domain
 * 支持数组形式:cookie('name','value',array('expire'=>1,'prefix'=>'think_'))
 * 支持query形式字符串:cookie('name','value','prefix=tp_&expire=10000')
 */
function cookie($name, $value='', $option = null) {
	global $_SCONFIG;

	// 默认设置
	$config = array(
		'prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
		'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
		'path'   => C('COOKIE_PATH'),   // cookie 保存路径
		'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
	);

	// 参数设置(会覆盖黙认设置)
	if (!empty($option)) {
		if (is_numeric($option)) {
			$option = array('expire'=>$option);

		} elseif ( is_string($option) ) {
			parse_str($option, $option);
		}

		$config = array_merge($config, array_change_key_case($option));
	}

	// 清除指定前缀的所有cookie
	if (is_null($name)) {
	   if (empty($_COOKIE)) {
		   return;
	   }

	   // 要删除的cookie前缀，不指定则删除config设置的指定前缀
	   $prefix = empty($value) ? $config['prefix'] : $value;
	   if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
		   foreach($_COOKIE as $key => $val) {
			   if (0 === stripos($key,$prefix)){
					setcookie($_COOKIE[$key],'',time() - 3600, $config['path'], $config['domain']);
					unset($_COOKIE[$key]);
			   }
		   }
	   }
	   return;
	}

	$name = $config['prefix'].$name;
	if ('' === $value){
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;// 获取指定Cookie

	} else {
		if (is_null($value)) {
			// 删除指定cookie
			setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
			unset($_COOKIE[$name]);

		} else {
			// 设置cookie
			$expire = !empty($config['expire'])? time() + intval($config['expire']) : 0;
			setcookie($name, $value, $expire, $config['path'], $config['domain']);
			$_COOKIE[$name] = $value;
		}
	}
}

/**
 * 区分大小写的文件存在判断
 * @filename 文件名
 * @author chengzhen (anyou@msn.com)
 */
function file_exists_case($filename) {
	if (is_file($filename)) {
		return true;
	}
	return false;
}

function php_version_code() {
	$phpVersions = explode('.', phpversion());
	return $phpVersions[0] * 1000 + $phpVersions[1];
}


/**
 * require_cache 的方法的变体, 实现 java 风格的 import 语法.
 * 如 import('util.Test') == require_cache('classes/util/Test.classs.php');
 * @param 要导入的类的名称, 类 文件必须是 "(类名).class.php", 可以包含名称空间(即目录), 如 util.Test.
 * @author chengzhen (anyou@msn.com)
 */
function import($class) {
	$class = str_replace(array('.', '#'), array('/', '.'), $class);
	require_cache(S_ROOT.'/modules/'.$class.'.class.php');
}

/**
 * 优化的 require_once, 通过缓存减少实际的 require 方法的定义.
 * @param $filename 要包含的 PHP 脚本文件的名称.
 * @author chengzhen (anyou@msn.com)
 */
function require_cache($filename) {
	if (empty($filename)) {
		return;
	}

	// echo $filename, "\r\n";

	static $_importFiles = array(); // 已经导入的文件列表, 防止多次加载同一个文件.

	$filename = realpath($filename);
	if (!isset($_importFiles[$filename])) {
		if (file_exists_case($filename)) {
			require_once $filename;
			$_importFiles[$filename] = true;

		} else {
			$_importFiles[$filename] = false;
		}
	}
	return $_importFiles[$filename];
}

/** 显示当前调用堆栈. */
function print_stack_trace() {
	$array = debug_backtrace();
	//print_r($array);//信息很齐全

	$html = '';
	unset($array[0]);
	foreach($array as $row) {
		$html .= $row['file'].':'.$row['line'].', '.$row['function']."()\r\n";
	}

	echo $html;
}

///////////////////////////////////////////////////////////////////////////////
//  

/**
 * 使用指定的视图模板来显示数据. 这个方法可以用指定的名称模块下的模板来显示数据, 便于模块化设计.
 * 如 print_by_view(array("name"=>"Alice", "user"=>$user), "test", "card");
 * 表示用 /modules/test/card.view.htm 模板来显示指定的数据.
 * 具体使用方式, 请参考相关用例.
 *
 * @param data 要显示的数据, 表格, 在模板中通过这个表格的 key 来访问每一个元素.
 * @param module 模块名称
 * @param page 模板页面名称, 不包括 'view.htm' 部分.
 * @author chengzhen (anyou@msn.com)
 */
function print_by_view($data, $module, $view) {
	global $user, $globalViewer, $lang;

	$template = new Template();

	// global data
	//$template->assign('lang', $lang);
	//$template->assign('viewer', $globalViewer);

	if (is_array($data) || is_object($data)) {
		foreach ($data as $key => $value) {
			$template->assign($key, $value);
		}
	}
	echo $template->output($view.'.view.htm', $module);
}


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

function safe_select($object, $name, $default = null) {
	$result = safe_get($object, $name, null);
	return empty($result) ? $default : $result;
}


///////////////////////////////////////////////////////////////////////////////
// 应用系统级工具方法 

function L($name=null, $value=null)
{
	return $name;
}

// 获取配置值
function C($name=null, $value=null)
{
	global $_SCONFIG;

	// 无参数时获取所有
	if (empty($name)) {
		return $_SCONFIG;
	}

	// 优先执行设置获取或赋值
	if (is_string($name)) {
		if (is_null($value)) {
			return isset($_SCONFIG[$name]) ? $_SCONFIG[$name] : null;

		} else {
			$_SCONFIG[$name] = $value;
			return;
		}

	} else if (is_array($name)) {
		// 批量设置
		return $_SCONFIG = array_merge($_SCONFIG, array_change_key_case($name));
	}
	return null; // 避免非法参数
}
