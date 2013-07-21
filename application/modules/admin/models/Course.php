<?php
class Admin_Model_Course
{    
    private static $_instance;
    private static $_table = "course";
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Course
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert course
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
        if(!$this->isValidCourse($data)) {
            return 0;
        }
        
        $daoCourse = Admin_Model_DAO_Course::factory();
        try {
            $result = $daoCourse->insert($data);
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
     * Update course
     * @param integer $id
     * @param array $data
     * @return true on success, false on failure
    */
    public function update($id, $data) {
        $result = 0;
        if(!$this->isValidCourse($data, true)) {
            return 0;
        }
        
        $daoCourse = Admin_Model_DAO_Course::factory();
        try {
        	$result = $daoCourse->update($id, $data);
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
            	Utils_Global::storeBackendLog('delete', self::$_table, $id, array('id' => $id));
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);  
        }
        
        return $result;
    }
    
    /**
     * Get courses by given condition
     * @param array $options
     * @return array
     * */
    public function getCourses($options = array()) {
        $courses = array();
        $daoCourse = Admin_Model_DAO_Course::factory();
        try {
            $courses = $daoCourse->getCourses($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $courses;
    }
    
    /**
     * Get courses count by given Id
     * @param array $options
     * @return integer
     * */
    public function getCoursesCount($options = array()) {
        $count = 0;
        $daoCourse = Admin_Model_DAO_Course::factory();
        try {
            $count = $daoCourse->getCourseCount($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $count;
    }
    
    private function isValidCourse($data, $isUpdate=false) {
        if(!$data['name'] ||
            !$data['name_seo'] ||
             !$data['tuition']||
            !$data['content'] ||
            !$data['category'] ||
            !$data['course_link'] ||
            !$data['center_id']||
            ($isUpdate?false:!$data['hash_folder'])||
            ($isUpdate?false:!$data['image'])) {
            return false;
        }
        
        return true;
    }
}