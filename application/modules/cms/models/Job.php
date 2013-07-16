<?php
class Cms_Model_Job
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Job
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get job by given id
	 * @param integer $id
	 * @return array
	 */
	public function getJob($id) {
		$result = array();
		if(!$id) {
			return $result;
		}
		$options = array('id' => $id);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->job_select . '_' . $id;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		 
		$jobDao = Cms_Model_DAO_Job::factory();
		try {
			$result = $jobDao->getJob($options);
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
	 * Get list of hot job
	 * 
	 * */
	public function getHotJobs($offset = 0, $limit = 7) {
		$result = array();
		$options = array('hot' => 1, 'offset' => $offset, 'limit' => $limit);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->job_select . '_hot' . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
			
		$jobDao = Cms_Model_DAO_Job::factory();
		try {
			$result = $jobDao->getJob($options);
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
		
		return $result;
	}
}