<?php
class Admin_Model_Position
{    
    private static $_instance;
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Position
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert position
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
        $daoPosition = Admin_Model_DAO_Position::factory();
        try {
            $result = $daoPosition->insert($data);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    /**
     * Update position
     * @param integer $id
     * @param array $data
     * @return true on success, false on failure
    */
    public function update($id, $data) {
        $result = 0;
        $daoPosition = Admin_Model_DAO_Position::factory();
        try {
        	$result = $daoPosition->update($id, $data);
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        return $result;
    }
    
    /**
     * Delete article by given $id
     * @param integer $id
     * @return true on success, false on failure
     * */
    public function delete($id) {
        $result = 0;
        if(!$id) {
            return 0;
        }
        
        $daoPosition = Admin_Model_DAO_Position::factory();
        try {
            $result = $daoPosition->delete($id);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);  
        }
        
        return $result;
    }
    
    /**
     * Get positions by given condition
     * @param array $options
     * @return array
     * */
    public function getPositions($options = array()) {
        $positions = array();
        $daoPosition = Admin_Model_DAO_Position::factory();
        try {
            $positions = $daoPosition->getPositions($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $positions;
    }
    
    /**
     * Get positions count by given Id
     * @param array $options
     * @return integer
     * */
    public function getPositionsCount($options = array()) {
        $count = 0;
        $daoPosition = Admin_Model_DAO_Position::factory();
        try {
            $count = $daoPosition->getPositionsCount($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $count;
    }
}