<?php
abstract class Utils_Db_Connection_Abstract
{
    const KEY = 'Utils_Db_Connection_Abstract_Key';
    const PREFIX_KEY = 'Utils_Db_Connection_Abstract_TablePrefix';
    /**
     * Default table prefix
     */
    const DEFAULT_PREFIX = 't_';
    
    protected $_adapter;
    
    public function __construct ($adapter)
    {
        $this->_adapter = $adapter;
    }
    
    /**
     * @return string
     */
    
    public function getAdapter()
    {
    	return $this->_adapter;
    }
    
    /**
     * Support master connection type
     * @return mixed
     */
    public function getMasterConnection()
    {
    	return $this->_getConnection('master');
    }
    
    /**
	 * Support slave connection type
	 * @return mixed
	 */
    public function getSlaveConnection()
    {
    	return $this->_getConnection('slave');
    }
    
    /**
     * Get database table prefix
     * @return string
     */
    public static function getDbPrefix()
    {
    	if(!Zend_Registry::isRegistered(self::PREFIX_KEY)){
    		$config = Utils_Config::getConfig();
    		
    	$prefix = (null == $config->db->prefix)	? self::DEFAULT_PREFIX : $config->db->prefix;
    	Zend_Registry::set(self::PREFIX_KEY, $prefix);
    	}
    	return Zend_Registry::get(self::PREFIX_KEY);
    }
    
    /**
     * @param string $type Type of connection. Must be slave or master
     * @param mixed
     */
    protected function _getConnection($type)
    {
    	$key = self::KEY. '_' . $type;
    	if(!Zend_Registry::isRegistered($key)){
    		$config = Utils_Config::getConfig();
    		$servers = $config->db->$type;
    		
    		/**
    		 * Connect to server
    		 */
    		$servers = $servers->toArray();
    		$server = array_rand($servers);
    		
    		/**
    		 * Get database prefix
    		 */
    		$prefix = (null==$config->db->prefix) ? self::DEFAULT_PREFIX : $config->db->prefix;
    		$servers[$server]['prefix'] = $prefix;
    		$db = $this->_connect($servers[$server]);
    		
    		Zend_Registry::set($key, $db);
    	}
    	
    	return Zend_Registry::get($key);
    }
    
    /**
     * Abstract connection
     * @param array $config Database connection setting, includes:
     * - host
     * - port
     * - dbname
     * - username
     * - password
     * - charset
     * @param mixed Database connection
     */
    protected abstract function _connect($config);
    
    /**
	 * Get database server version
	 */
	public abstract function getVersion();
	
    /**
     * Execute SQL query
     * @param string $sql
     */
    public abstract function query($sql);
}
?>