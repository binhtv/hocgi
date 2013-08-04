<?php
class Admin_Model_ImageUpload
{    
    private static $_instance;
    private static $_cacheTimeout = 300;
    private static $_table = "user_upload";
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_ImageUpload
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
        if(!$data['username'] || !$data['title']) {
            return 0;
        }
        $modelImageUpload = Admin_Model_DAO_ImageUpload::factory();
        try {
            $result = $modelImageUpload->insertNewUpload($data);
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
     * Get image upload by given optoions
     * @param array $options
     * @return array
     * */
    public function getImageUploads($options = array()) {
        $modelImageUpload = Admin_Model_DAO_ImageUpload::factory();
        try {
        	$result = $modelImageUpload->getImageUploads($options);
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    /**
     * Get uploads count by given options
     * @param array $options
     * @return array
     * */
    public function getImageUploadsCount($options = array()) {
    	$count = 0;
    	$modelImageUpload = Admin_Model_DAO_ImageUpload::factory();
    	try {
    		$count = $modelImageUpload->getImageUploadsCount($options);
    	} catch (Exception $exc) {
    		prBinh($exc);
    		Utils_Global::storeLog($exc, __FILE__, __LINE__);
    	}
    
    	return $count;
    }
}