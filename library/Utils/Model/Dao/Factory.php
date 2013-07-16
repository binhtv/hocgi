<?php
class Utils_Model_Dao_Factory
{
	/**
	 * @var Utils_Model_Dao_Factory
	 */
	private static $_instance;
	
	protected $_dbAdapter = 'Pdo_Mysql';
	
	/**
	 * @var string
	 */
	private $_module;
	
	private function __construct()
	{
		$config = Utils_Config::getConfig();
		$this->_dbAdapter = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($config->db->adapter))));
	}
	
	public static function getInstance()
	{
		if(null == self::$_instance){
			self::$_instance = new self();
		}
		
		self::$_instance->reset();
		return self::$_instance;
	}
	
	/**
	 * @param string $module Name of module
	 * @return Utils_Model_Dao_Factory
	 */
	public function setModule($module)
	{
		$this->_module = $module;		
		return $this;
	}
	
	/**
	 * Reset
	 */
	public function reset()
	{
		$this->_module = null;
	}
	
	public function __call($name, $arguments)
	{
		if (strlen($name) <= 6 || substr($name, 0, 3) != 'get' || substr($name, -3) != 'Dao') {
			return;
		}
		$name = substr($name, 3);
		$name = substr($name, 0, -3);
		$name = ucfirst($name);
		
		if (null == $this->_module) {
			throw new Exception('Module is not set');
		}
		
		$class = ucfirst($this->_module) . '_Models_Dao_' . $name;
		$dao = new $class();
		
		return $dao;
	}
}
?>