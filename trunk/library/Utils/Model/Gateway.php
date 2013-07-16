<?php
abstract class Utils_Model_Gateway
{
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_conn;
	
	/**
	 * Database table prefix
	 * @var string
	 */
	protected $_prefix;
	
	/**
	 * @return void
	 */
	public function __construct($conn = null)
	{
		$this->_prefix = Utils_Db_Connection::getDbPrefix();
		if($conn != null){
			$this->setDbConnection($conn);
		}
	}
	
	/**
	 * @param Zend_Db_Adapter_Abstract $conn
	 */
	public function setDbConnection($conn)
	{
		$this->_conn = $conn;
	}
	
	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDbConnection()
	{
		return $this->_conn;
	}
	
	/**
	 * Convert an object or array to entity instance
	 * @param mixed $entity
	 * @return Utils_Model_Entity
	 */
	abstract function convert($entity);
}
?>