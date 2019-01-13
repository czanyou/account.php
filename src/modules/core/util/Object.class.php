<?php
!defined('IN_VISION') && exit('Access Denied');

/** Vision 系统基类. */
class Object {
	public $className = "Object";
	private static $_instance = array();

	/**
	 * 自动变量设置
	 * @param string $name 属性名称
	 * @param mixed $value 属性值
	 */
    public function __set($name, $value) {
        //if (property_exists($this, $name)) {
            
        //}

    	if ($name) {
       		$this->$name = $value;    
    	}
    }

	/**
	 * 自动变量获取
	 * @param string $name 属性名称
	 * @return mixed
	 */
    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
		return null;
    }

	/**
	 * 系统自动加载类库
	 * @param string $classname 对象类名
	 */
	public static function autoload($classname) {
		require_once S_ROOT.'/classes/vision/'.$classname.'.class.php';
        return;
    }

	/**
	 * 取得对象实例 支持调用类的静态方法
	 * @param string $class 对象类名
     * @param string $method 类的静态方法名
	 */
	static public function instance($class, $method='') {
        $identify = $class.$method;
        if (!isset(self::$_instance[$identify])) {
            if (class_exists($class)){
                $o = new $class();
                if (!empty($method) && method_exists($o,$method)) {
                    self::$_instance[$identify] = call_user_func_array(array(&$o, $method));

                } else {
                    self::$_instance[$identify] = $o;
				}

            } else {
                halt(L('_CLASS_NOT_EXIST_'));
			}
        }

        return self::$_instance[$identify];
    }
}

?>