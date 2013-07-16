<?php
class Cms_Model_Banner
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Banner
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get banner by given position
	 * @param integer|array $position
	 * @return array
	 */
	public function getBanner($position) {
		if(!$position) {
			return array();
		}
		
		$options = array('position' => $position);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->banner_select . '_' . $position;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		 
		$bannerDao = Cms_Model_DAO_Banner::factory();
		try {
			$result = $bannerDao->getBanner($options);
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