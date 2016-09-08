<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class COCO_Loder{

	//指定加载的目录
	protected $dirs = array();

	//根目录
	protected $basePath = '';

	public function __construct($basePath,$dirs = array())
	{
		$this->setBasePath($basePath);
		if(! empty($dirs))
		{
			$this->addDirs($dirs);
		}
		spl_autoload_register(array($this, 'load'));
	}

	/**
	 * 设置根目录
	 * @param string $basePath 根目录
	 */
	public function setBasePath($basePath)
	{
		$this->basePath = $basePath;
	}

    public function addDirs($dirs)
    {
    	if(! is_array($dirs))
    	{
    		$dirs = array($dirs);
    	}
    	$this->dirs = array_merge($this->dirs,$dirs);
    }

    public function load($className)
    {
    	if(class_exists($className,FALSE) || interface_exists($className,FALSE))
    	{
    		// return;
    		trigger_error("Unable to load class: $className", E_USER_WARNING);
    	}
    }
}

