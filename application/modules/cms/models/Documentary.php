<?php
use Zend\Crypt\PublicKey\Rsa\PublicKey;
class Cms_Model_Documentary
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Documentary
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get new documentary
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function getLatestDocumentary($offset = 0, $limit = 6) {
		$options = array('order' => 'dateline', 'by' => 'DESC', 'offset' => $offset, 'limit' => $limit);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->documentary_select . '_latest_' . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		$documentaryDao = Cms_Model_DAO_Documentary::factory();
		try {
			$result = $documentaryDao->getDocumentary($options);
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
	 * Get 
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getMostDownloadDocumentary($offset = 0, $limit = 6) {
		$options = array('order' => 'download_count', 'by' => 'DESC', 
					'offset' => $offset, 'limit' => $limit);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->documentary_select . '_most_download_' . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
			
		$documentaryDao = Cms_Model_DAO_Documentary::factory();
		try {
			$result = $documentaryDao->getDocumentary($options);
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
	 * Get documentary count
	 * @param array $options
	 * @return array
	 * */
	public function getDocumentaryCount($options) {
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->documentary_count;
		if($options['new']) {
			$key .= '_new_' . $options['new'];
		}
		
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$documentaryDao = Cms_Model_DAO_Documentary::factory();
		try {
			$result = $documentaryDao->getDocumentaryCount($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			prBinh($exc);	
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
}