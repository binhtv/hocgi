<?php
class Cms_Model_Speaker
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Speaker
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get list of Speaker show on box news
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function getSpeaker($offset = 0, $limit = 4) {
		$options = array('offset' => $offset, 'limit' => $limit);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->speaker_select . '_' . $options['offset'] . '_' . $options['limit'];
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		 
		$speakerDao = Cms_Model_DAO_Speaker::factory();
		try {
			$result = $speakerDao->getSpeaker($options);
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
		
		return $result;
	}
}