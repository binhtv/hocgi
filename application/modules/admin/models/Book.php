<?php
class Admin_Model_Book
{    
    private static $_instance;
    private static $_table = "book";
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Book
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert new book
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
        if(!$this->isValidBook($data)) {
            return 0;
        }
        
        $daoBook = Admin_Model_Book::factory();;
        try {
            $result = $daoDocumentary->insert($data);
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
     * Update documentary
     * @param integer $id
     * @param array $data
     * @return true on success, false on failure
    */
    public function update($id, $data) {
        $result = 0;
        if(!$this->isValidDocumentary($data, true)) {
            return 0;
        }
        
        $daoDocumentary = Admin_Model_DAO_Documentary::factory();
        try {
        	$result = $daoDocumentary->update($id, $data);
            if($result) {//Log
            	$data['id'] = $id;
            	Utils_Global::storeBackendLog('update', self::$_table, $id,  $data);
            }
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        return $result;
    }
    
    /**
     * Delete documentary by given $id
     * @param integer $id
     * @return true on success, false on failure
     * */
    public function delete($id) {
        $result = 0;
        if(!$id) {
            return 0;
        }
        
        $daoDocumentary = Admin_Model_DAO_Documentary::factory();
        try {
            $result = $daoDocumentary->delete($id);
            if($result) {//Log
            	Utils_Global::storeBackendLog('delete', self::$_table, $id, array('id' => $id));
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);  
        }
        
        return $result;
    }
    
    /**
     * Get documentary by given condition
     * @param array $options
     * @return array
     * */
    public function getDocumentaries($options = array()) {
        $documentaries = array();
        $daoDocumentary = Admin_Model_DAO_Documentary::factory();
        try {
            $documentaries = $daoDocumentary->getDocumentaries($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $documentaries;
    }
    
    /**
     * Get documentary count by given Id
     * @param array $options
     * @return integer
     * */
    public function getDocumentaryCount($options = array()) {
        $count = 0;
        $daoDocumentary = Admin_Model_DAO_Documentary::factory();
        try {
            $count = $daoDocumentary->getDocumentaryCount($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $count;
    }
    
    private function isValidBook($data, $isUpdate=false) {
        if(!$data['name'] ||
            !$data['name_seo'] ||
             !$data['short_description']||
            !$data['price'] ||
            !$data['author'] ||
            !$data['publisher']||
            !$data['publish_date']||
            ($isUpdate?false:!$data['hash_folder'])||
            ($isUpdate?false:!$data['image'])) {
            return false;
        }
        
        return true;
    }
}