<?php
class Admin_Model_Banner
{    
    private static $_instance;
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Banner
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert banner
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
        $daoBanner = Admin_Model_DAO_Banner::factory();
        try {
            $position = $data['position'];
            unset($data['position']);
            $result = $daoBanner->insert($data);
            $result2 = $daoBanner->insertPositionBanner($position, $result);
            if($result) {//Log
                $data['id'] = $result;
            	Utils_Global::storeBackendLog('insert', $data);
            	$position['id'] = $result2;
            	Utils_Global::storeBackendLog('insert', $position);
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    /**
     * Update banner
     * @param integer $id
     * @param array $data
     * @return true on success, false on failure
    */
    public function update($id, $data) {
        $result = 0;
        $daoBanner = Admin_Model_DAO_Banner::factory();
        try {
            $position = $data['position'];
            unset($data['position']);
        	$result = $daoBanner->update($id, $data);
    	    if($result) {//Log
    	    	$data['id'] = $result;
    	    	Utils_Global::storeBackendLog('update', $data);
    	    }
        	try {
        	    $result2 = $daoBanner->insertPositionBanner($position, $id);
        	    if($result2) {//Log
        	    	$position['id'] = $result2;
        	    	Utils_Global::storeBackendLog('update', $position);
        	    }
        	} catch (Exception $exc) {
        	    Utils_Global::storeLog($exc, __FILE__, __LINE__);
        	}
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        return $result;
    }
    
    /**
     * Delete banner by given $id
     * @param integer $id
     * @return true on success, false on failure
     * */
    public function delete($id) {
        $result = 0;
        if(!$id) {
            return 0;
        }
        
        $daoArticle = Admin_Model_Article::factory();
        try {
            $result = $daoArticle->delete($id);
            if($result) {//Log
    	    	$data['id'] = $result;
    	    	Utils_Global::storeBackendLog('delete', $data);
    	    }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);  
        }
        
        return $result;
    }
    
    /**
     * Get banner by given condition
     * @param array $options
     * @return array
     * */
    public function getBanners($options = array()) {
        $banner = array();
        $daoBanner = Admin_Model_DAO_Banner::factory();
        try {
            $banner = $daoBanner->getBanners($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $banner;
    }
    
    /**
     * Get banners by given position
     * @param array $options
     * @return array
     * */
    public function getBannerByPosition($options = array()) {
        $banners = array();
        if(!$options['positionId']) {
            return $banners;
        }
        
        $daoBanner = Admin_Model_DAO_Banner::factory();
        try {
            $banners = $daoBanner->getBannerByPosition($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $banners;
    }
    
    /**
     * Get banner count by given Id
     * @param array $options
     * @return integer
     * */
    public function getBannersCount($options = array()) {
        $count = 0;
        $daoBanner = Admin_Model_DAO_Banner::factory();
        try {
            $count = $daoBanner->getBannerCount($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $count;
    }
}