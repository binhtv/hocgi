<?php
class Admin_Model_DAO_ImageUpload
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_ImageUpload
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
       $this->_db->insert('user_upload', $data);
       $result = $this->_db->lastInsertId('user_upload', 'id');
       return $result;
   }
}