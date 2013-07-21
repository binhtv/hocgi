<?php
class Admin_Model_Center
{    
    private static $_instance;
    private static $_table = "center";
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Center
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert center
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
        if(!$this->isValidCenter($data)) {
            return 0;
        }
        
        $daoCenter = Admin_Model_DAO_Center::factory();
        try {
            $result = $daoCenter->insert($data);
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
     * Update center
     * @param integer $id
     * @param array $data
     * @return true on success, false on failure
    */
    public function update($id, $data) {
        $result = 0;
        if(!$this->isValidCenter($data, true)) {
            return 0;
        }
        
        $daoCenter = Admin_Model_DAO_Center::factory();
        try {
        	$result = $daoCenter->update($id, $data);
        	if($result) {//Log
        		$data['id'] = $id;
        		Utils_Global::storeBackendLog('update', self::$_table, $id, $data);
        	}
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        return $result;
    }
    
    /**
     * Delete course by given $id
     * @param integer $id
     * @return true on success, false on failure
     * */
    public function delete($id) {
        $result = 0;
        if(!$id) {
            return 0;
        }
        
        $daoCourse = Admin_Model_DAO_Course::factory();
        try {
            $result = $daoCourse->delete($id);
            if($result) {//Log
            	Utils_Global::storeBackendLog('update', self::$_table, $id, array('id' => $id));
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);  
        }
        
        return $result;
    }
    
    /**
     * Get Centers by given condition
     * @param array $options
     * @return array
     * */
    public function getCenters($options = array()) {
        $centers = array();
        $daoCenter = Admin_Model_DAO_Center::factory();
        try {
            $centers = $daoCenter->getCenters($options);
        } catch (Exception $exc) {
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $centers;
    }
    
    /**
     * Get centers count by given Id
     * @param array $options
     * @return integer
     * */
    public function getCentersCount($options = array()) {
        $count = 0;
        $daoCenter = Admin_Model_DAO_Center::factory();
        try {
            $count = $daoCenter->getCenterCount($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $count;
    }
    
    private function isValidCenter($data, $isUpdate=false) {
        if(!$data['name'] ||
            !$data['contact_info'] ||
             !$data['address']||
            !$data['city'] ||
            !$data['city_code'] ||
            ($isUpdate?false:!$data['hash_folder'])||
            ($isUpdate?false:!$data['image'])) {
            return false;
        }
        
        return true;
    }
}