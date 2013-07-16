<?php
abstract class Utils_Model_Dao
{
    /**
     * @var Utils_Db_Connection
     */
    protected $_conn;
    
    /**
     * Database table prefix
     * @var string
     */
    protected $_prefix = '';

    public function __construct($conn = null)
    {
    	$this->_prefix = Utils_Db_Connection_Abstract::getDbPrefix();
    	if($conn != null){
    		$this->setDbConnection($conn);
    	}
    }
    
    /**
     * @param Utils_Db_Connection $conn
     * @return Utils_Model_Dao
     */
    public function setDbConnection($conn)
    {
    	$this->_conn = $conn;
    	return $this;
    }
    
    /**
     * @return Utils_Db_Connection
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