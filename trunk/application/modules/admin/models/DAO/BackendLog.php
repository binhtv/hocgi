<?php
class Admin_Model_DAO_BackendLog
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_BackendLog
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
        $table = $options['table'];
        $sql = " SELECT `username`, count(1) `count` ";
        $from = " FROM `cms_backend_log` tb1 INNER JOIN `{$table}` tb2 ON `tb1`.`username` = `tb2`.`editor` ";
        $where = " WHERE 1 = 1 ";
        $groupBy = "GROUP BY `tb1`.`username` ";
        
        if($options['active'] == 2) {
            $active = 0;
        } else {
            $active = $options['active'];
        }
        
        if($options['active']) {
            $where .= " AND `active` = {$this->_db->quote($active, 'INTEGER')} ";
        }
        if($options['dateF']) {
            $where .= " AND `tb1`.`dateline` >= {$this->_db->quote($options['dateF'], 'INTEGER')} ";
        }
        if($options['dateT']) {
            $where .= " AND `tb1`.`dateline` <= {$this->_db->quote($options['dateT'], 'INTEGER')} ";
        }
        if($options['username']) {
            $where .= " AND `tb1`.`username` = {$this->_db->quote($options['username'])} ";
        }
        $result = $this->_db->fetchAll($sql . $from . $where . $groupBy);
        
        return $result;
    }
}