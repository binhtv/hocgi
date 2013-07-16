<?php
use Zend\Crypt\PublicKey\Rsa\PublicKey;
class Cms_Model_Course
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Course
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get list of Courses show on box news
	 * @param integer $offset
	 * @param integer $limit
	 * @param array $options 
	 * @return array
	 */
	public function getCourses($offset = 0, $limit = 4, $options=array()) {
		$temp = array('offset' => $offset, 'limit' => $limit);
		$options = array_merge($temp, $options); 
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->course_select . '_';
		if($options['promotion']) {
			$key .= '_promotion_';	
		}
		if($options['best']) {
			$key .= '_best_';
		}
		if($options['city']) {
			$key .= '_city_';
		}
		if($options['hot']) {
			$key .= '_hot_';
		}
		//@FIXME cache key
		$key .= $options['offset'] . '_' . $options['limit'];
		
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		 
		$courseDao = Cms_Model_DAO_Course::factory();
		try {
			$result = $courseDao->getCourses($options);;
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
		    prBinh($exc);
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
		return $result;
	}
	
	/**
	 * Get course detail by given id
	 * @param integer $id
	 * @return array
	 * */
	public function getCourseById($id) {
		$course = array();
		if(!$id) {
			return $course;
		}
		
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->course_select . '_id_' . $id;
		if(is_object($cache)) {
			$course = $cache->getCache($key);
			if($course) {
				return $course;
			}
		}
		
		$courseDao = Cms_Model_DAO_Course::factory();
		$options = array('id' => $id);
		try {
			$course = $courseDao->getCourses($options);
			if($course && is_object($cache)) {
				$cache->setCache($key, $course);
			}
		} catch (Exception $exc) {
		    prBinh($exc);
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $course;
	}
	
	/**
	 * Get course count by given condition
	 * @package array $options
	 * @return integer
	 * */
	public function getCourseCount($options=array()) {
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->course_count;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$courseDao = Cms_Model_DAO_Course::factory();
		try {
			$result = $courseDao->getCourseCount($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
		    prBinh($exc);
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * Get course by given keyword
	 * @param string $keySearch
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function searchFullText($keySearch, $offset=0, $limit=10) {
	    $result = array();
	    try {
	        $courseDao = Cms_Model_DAO_Course::factory();
	        $result = $courseDao->searchFullText($keySearch, $offset, $limit);
	    } catch (Exception $exc) {
	        prBinh($exc);
	        Utils_Global::storeLog($exc, __FILE__, __LINE__);
	    }
	    
	    return $result;
	}
	
	/**
	 * Get count by keyword
	 * @param string $keySearch
	 * @return integer
	 * */
	public function getCountFullText($keySearch) {
	    $count = 0;
	    try {
	    	$courseDao = Cms_Model_DAO_Course::factory();
	    	$result = $courseDao->countFullText($keySearch);
	    } catch (Exception $exc) {
	    	prBinh($exc);
	    	Utils_Global::storeLog($exc, __FILE__, __LINE__);
	    }
	     
	    return $result;
	}
}