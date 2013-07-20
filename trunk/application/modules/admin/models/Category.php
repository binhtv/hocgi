<?php
class Admin_Model_Category
{    
    private static $_instance;
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Category
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert article
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
    }
    
    public function update($id, $data) {
        
    }
    
    public function delete($id) {
        
    }
    
    /**
     * Get list categories by given options
     * @param array $options
     * @return array
     * */
    public function getCategories($options = array()) {
        $result = array();
        $categoryDao = Admin_Model_DAO_Category::factory();
        try {
            $categories = $categoryDao->getCategories($options);
            foreach ($categories as $category) {
                if(!$result[$category['parent_id']]) {
                    $tempArr = array('id' => $category['parent_id'],
                                                            'name' => $category['parent_name'],
                                                            'childs' => array(),);
                    if($category['child_id']) {
                        $tempArr['childs'][] = array('id' => $category['child_id'], 'name' => $category['child_name']);
                    }
                    $result[$category['parent_id']] = $tempArr;
                } else {
                    $result[$category['parent_id']]['childs'][] = array('id' => $category['child_id'], 'name' => $category['child_name']);
                }
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    /**
     * Get child category by given parent
     * @param integer $id
     * @param integer $for_course
     * @return array
     * */
    public function getChildCategoriesOf($id, $for_course) {
        $result = array();
        $categoryDao = Admin_Model_DAO_Category::factory();
        try {
            $categories = $categoryDao->getChildCategoriesOf($id);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
    
        return $categories;
    }
    
}