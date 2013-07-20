<?php
class Admin_Model_BackendLog
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_BackendLog
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    private function __construct()
    {
    	$module = Utils_Global::get('module');
    	$this->_db = Utils_Global::getDbInstance($module);
    }
    
    /**
     * Get statistics by given options
     * @param array $options
     * @return array
     * */
    public function getEditorStatistics($options) {
        $statistics = array();
        $daoBakendLog = Admin_Model_DAO_BackendLog::factory();
        try {
        	$rStatistics = $daoBakendLog->getEditorStatistics($options);
    	    $statistics[$options['table']] = $rStatistics;
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $statistics;
    }
}