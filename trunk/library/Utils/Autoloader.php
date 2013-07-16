<?php
class Utils_Autoloader extends Zend_Loader_Autoloader_Resource
{
	public function __construct($options)
	{
		parent::__construct($options);
	}
	
	public function autoload($class)
	{
		$prefix = APPLICATION_PATH . DS;
		$paths = explode('_', $class);
		$prefix .= 'modules' . DS;
		
		$className = $paths[count($paths) - 1];
		$classFile = substr($class, 0, -strlen($className));
		$classFile = $prefix . strtolower(str_replace('_', DS, $classFile)) . $className . '.php';
		
		if(file_exists($classFile)){
			return require_once $classFile;
		}
		
		return false;
	}
}
?>