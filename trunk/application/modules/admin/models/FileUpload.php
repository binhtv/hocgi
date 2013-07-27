<?php
class Admin_Model_FileUpload
{    
    private static $_instance;
    private static $_cacheTimeout = 300;
    private static $_table = "file_upload";
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_FileUpload
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert new upload
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insertNewUpload($data) {
        if(!$data['username'] || !$data['name']) {
            return 0;
        }
        $modelFileUpload = Admin_Model_DAO_FileUpload::factory();
        try {
            $result = $modelFileUpload->insertNewUpload($data);
            if($result) {//Log
            	$data['id'] = $result;
            	Utils_Global::storeBackendLog('insert', self::$_table, $result, $data);
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    /**
     * Get list of upload by options
     * @param array $options
     * @return array
     * */
    public function getUploads($options = array()) {
        $result = array();
        $modelFileUpload = Admin_Model_DAO_FileUpload::factory();
        try {
        	$result = $modelFileUpload->getUploads($options);
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
}