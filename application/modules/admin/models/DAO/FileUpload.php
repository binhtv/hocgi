<?php
class Admin_Model_DAO_FileUpload
{    
    private $_db;
    private static $_instance = null;
    private $_tablename = "file_upload";
    
    /**
     * @return Admin_Model_DAO_FileUpload
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
     * Insert new upload
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insertNewUpload($data) {
       $this->_db->insert($this->_tablename, $data);
       $result = $this->_db->lastInsertId($this->_tablename, 'id');
       return $result;
   }
   
   public function getUploads($options = array()) {
       $sql = " SELECT * ";
       $from = " FROM file_upload ";
       $where = " WHERE 1 = 1 ";
       if($options['id']) {
           $where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
       }
       
       $orderBy = " ORDER BY id DESC ";
       $limit = "";
       $result = $this->_db->fetchAll($sql . $from . $where . $orderBy . $limit);
       return $result;
   }
}