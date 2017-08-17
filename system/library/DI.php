<?php
namespace di;

use ArrayAccess;

class di implements ArrayAccess {
	protected static $instance = NULL;

	//注册的服务池
	protected $data = array();
	/**
	 * @var array $hitTimes 服务命中的次数
	 */
	protected $hitTimes = array();

	public function __construct() {

	}

	public static function one() {
		if(self::$instance == NULL) {
			static::$instance = new di();
			static::$instance->onConstruct();
		}
		return static::$instance;
	}
    
    public function onConstruct() {
    	$this->request = '\\PhalApi\\Request';
    }
    public function set($key, $value) {
        $this->hitTimes[$key] = 0;

        $this->data[$key] = $value;

        return $this;
    }
    public function get($key, $default = NULL) {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

        // 内联操作，减少函数调用，提升性能
        if (!isset($this->hitTimes[$key])) {
            $this->hitTimes[$key] = 0;
        }
        $this->hitTimes[$key] ++;

        if ($this->hitTimes[$key] == 1) {
            $this->data[$key] = $this->initService($this->data[$key]);
        }

        return $this->data[$key];
    }
    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __get($name) {
        return $this->get($name, NULL);
    }
	/** ------------------ ArrayAccess（数组式访问）接口 ------------------ **/

	public function offsetSet($offset, $value) {
	    $this->set($offset, $value);
	}

	public function offsetGet($offset) {
	    return $this->get($offset, NULL);
	}

	public function offsetUnset($offset) {
	    unset($this->data[$offset]);
	}

	public function offsetExists($offset) {
	    return isset($this->data[$offset]);
	}
	public function __call($name, $arguments) {
	    // if (substr($name, 0, 3) == 'set') {
	    //     $key = lcfirst(substr($name, 3));
	    //     return $this->set($key, isset($arguments[0]) ? $arguments[0] : NULL);
	    // } else if (substr($name, 0, 3) == 'get') {
	    //     $key = lcfirst(substr($name, 3));
	    //     return $this->get($key, isset($arguments[0]) ? $arguments[0] : NULL);
	    // }

	    throw new Exception(
	        ('Call to undefined method DependenceInjection::{name}() .')
	    );
	}
	protected function initService($config) {
	    $rs = NULL;

	    if ($config instanceOf Closure) {
	        $rs = $config();
	    } elseif (is_string($config) && class_exists($config)) {
	        $rs = new $config();
	        // if(is_callable(array($rs, 'onInitialize'))) {
	        //     call_user_func(array($rs, 'onInitialize'));
	        // }
	    } else {
	        $rs = $config;
	    }

	    return $rs;
	}
}