<?php
class Cms_Model_Book
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Book
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get top book
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function getTopBooks($offset = 0, $limit = 9) {
		$options = array('order' => 'order', 'by' => 'DESC', 'offset' => $offset, 'limit' => $limit);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->book_select . '_top_' . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		 
		$bookDao = Cms_Model_DAO_Book::factory();
		try {
			$result = $bookDao->getBooks($options);
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
	 * Get hot book (best sale)
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getHotBooks($offset = 0, $limit = 9) {
		$options = array('hot' => 1, 'order' => 'order', 'by' => 'DESC', 
					'offset' => $offset, 'limit' => $limit);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->book_select . '_hot_' . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
			
		$bookDao = Cms_Model_DAO_Book::factory();
		try {
			$result = $bookDao->getBooks($options);
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
	 * Get book detail for given $id
	 * @param integer $id
	 * @return array
	 * */
	public function getBookDetail($id) {
	    if(!$id) {
	        return array();
	    }
	    
	    $options = array('id' => $id);
	    //Get from cache first
	    $cache = Utils_Global::getCacheInstance('cms');
	    $keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
	    $key = $keyConfig->book_select . '_bookdetail_' . $id;
	    if(is_object($cache)) {
	    	$result = $cache->getCache($key);
	    	if($result) {
	    		return $result;
	    	}
	    }
	    	
	    $bookDao = Cms_Model_DAO_Book::factory();
	    try {
	    	$result = $bookDao->getBooks($options);
	    	if(is_object($cache)) {
	    		$cache->setCache($key, $result);
	    	}
	    } catch (Exception $exc) {
	    	prBinh($exc);
	    	Utils_Global::storeLog($exc, __FILE__, __LINE__ );
	    }
	    
	    return $result;
	}
}