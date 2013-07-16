<?php
class Cms_Model_Poll
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Poll
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get poll by given id
	 * @param integer $id
	 * @return array
	 */
	public function getPoll($id) {
		$result = array();
		if(!$id) {
			return $result;
		}
		$options = array('id' => $id);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->poll_select . '_' . $id;
		if(is_object($cache)) {
			$result = $cache->get;Cache($key);
			if($result) {
				return $result;
			}
		}
		 
		$pollDao = Cms_Model_DAO_Poll::factory();
		try {
			$result = $pollDao->getPoll($options);
			if(is_array($result)) {
			    $total = 0;
			    $tempResult = array();
			    foreach ($result as $r) {
			        $tempResult['choices'][] = $r;
			        $total += $r['vote'];
			    }
		        $tempResult['question'] = $r['question'];
			    $tempResult['total'] = $total;
			    $tempResult['id'] = $r['id'];
			    
			    $result = $tempResult;
			}
			
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
	 * Vote +1 for choice(s) by give choice ids
	 * @param integer|array $choiceIds
	 * @return true on success, false on failure
	 * */
	public function voteChoice($choiceIds) {
		$pollDao = Cms_Model_DAO_Poll::factory();
		return $pollDao->voteChoice($choiceIds);
	}
}