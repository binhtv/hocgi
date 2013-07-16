<?php

/**
 * Global class
 * */
class Utils_Global extends Zend_Registry
{
	/**
	 *
	 * Singleton database instances
	 *
	 * @var array $_dbInstances
	 *
	 */
	private static $_dbInstances;
	
	/**
	 *
	 * Singleton cache instances
	 *
	 * @var array $_dbInstances
	 *
	 */
	private static $_cacheInstances;
	
	/**
	 *
	 * Configuration instances
	 *
	 * @var array
	 */
	private static $_configs;
	
	/**
	 *
	 * Static value for $_GET, $_POST
	 * cleaned by ZF
	 *
	 * @var array
	 */
	public static $params;
	
	/**
	 *
	 * Get configuration
	 * Guarantee singelton
	 *
	 *
	 * @param string $namespace
	 * @param string $name
	 * @param $configKey
	 * @return Zend_Config_Ini object
	 */
	public static function getConfig($namespace, $name, $configKey = '') {
		if (is_object(self::$_configs[$namespace][$name])) {
			if ($configKey) {
				return self::$_configs[$namespace][$name]->get($configKey);
			}
			return self::$_configs[$namespace][$name];
		}
		$configFilePath = APPLICATION_PATH . "/configs/$namespace/$name.ini";
		if (!Zend_Loader::isReadable($configFilePath)) {
			throw new Exception("Configuration file not found: \"$configFilePath\"");
		}
		if (!$namespace) {
			throw new Exception("Configuration namespace \"$namespace\" not found in \"$configFilePath\"");
		}
		if (!$name) {
			throw new Exception("Configuration name \"$name\" not found in namespace \"$namespace\" in \"$configFilePath\"");
		}
		
		//Save to cache
// 		$cache = GlobalCache::getCacheInstance('config');
// 		$configKeyName = "config.$namespace.$name"; // config.namespace.name
// 		$config = $cache->getCache($configKeyName);
// 		if ($config === false) {
// 			$config = new Zend_Config_Ini($configFilePath, APPLICATION_ENV, true);
// 			$cache->setCache($configKeyName, $config);
// 		}

		$config = new Zend_Config_Ini($configFilePath, APPLICATION_ENV, true);
		self::$_configs[$namespace][$name] = $config;
		if ($configKey) {
			return self::$_configs[$namespace][$name]->get($configKey);
		}
		return self::$_configs[$namespace][$name];
	}
	
	/**
	 *
	 * Get cache instance
	 * Guarantee singelton
	 *
	 *
	 * @param string $product
	 * @param string $dbInstance
	 *
	 * @return Zend_Cache object
	 */
	public static function getCacheInstance($product, $cacheInstance='default') {
		if (is_object(self::$_cacheInstances[$product][$cacheInstance])) {
			return self::$_cacheInstances[$product][$cacheInstance];
		}
		if (!$product) {
			return false;
		}
		$cacheConfig = self::getConfig('cache', $product, $cacheInstance);
		if (!$cacheConfig instanceof Zend_Config) {
			throw new Exception("Cache configuration of \"$cacheInstance\" in \"$product\" does not exist");
		}
		
		if ($cacheConfig->get('cache_enabled')) {
			$frontendOptions = array(
					'lifetime' => $cacheConfig->get('cache_lifetime'),
					'write_control' => $cacheConfig->get('cache_writecontrol'),
					'ignore_user_abort' => $cacheConfig->get('ignore_user_abort'),
					'automatic_serialization' => $cacheConfig->get('cache_automatic_serialization'),
					'caching' => $cacheConfig->get('cache_enabled'),
					'cache_id_prefix' => $cacheConfig->get('cache_id_prefix')
			);
			$backendType = $cacheConfig->cache_backend->type;
			$option = $cacheConfig->cache_backend->options->Memcached->toArray();
			$config['frontend'] = 'Core';
			$config['backend'] = $backendType;
			$config['frontendOptions'] = $frontendOptions;
			$config['backendOptions'] = $option;
			$backendOptions = isset($config['backendOptions']) ? $config['backendOptions'] : array();
			$frontend = $config['frontend'];
			$backend = $config['backend'];
			$frontendOptions = isset($config['frontendOptions']) ? $config['frontendOptions'] : array();
			$customNameFrontend = isset($config['customNameFrondend']) ? $config['customNameFrondend'] : false;
			$customNameBackend = isset($config['customNameBackend']) ? $config['customNameBackend'] : false;
			$instance = Zend_Cache::factory($frontend, $backend, $frontendOptions, $backendOptions, $customNameFrontend, $customNameBackend);
			$cacheConfig = $cacheConfig->toArray();
			$cacheConfig['product'] = $product;
			$cacheConfig['instance'] = $cacheInstance;
			self::$_cacheInstances[$product][$cacheInstance] = $instance;
			return self::$_cacheInstances[$product][$cacheInstance];
		} else {
			return null;
		}
	}
	
	/**
	 *
	 * Get database instances that's registered here
	 *
	 * @return array
	 *
	 */
	public function getDbInstances() {
		return self::$_dbInstances;
	}
	
	/**
	 *
	 * Get database instance
	 * Guarantee singelton
	 *
	 *
	 * @param string $product
	 * @param string $dbInstance
	 *
	 * @return Zend_Db object
	 */
	public static function getDbInstance($product, $dbInstance="default") {
		if (is_object(self::$_dbInstances[$product][$dbInstance])) {
			return self::$_dbInstances[$product][$dbInstance];
		}
		if (!$product) {
			return false;
		}
		$config = self::getConfig('databases', $product, $dbInstance);
		if ($config instanceof Zend_Config) {
			self::$_dbInstances[$product][$dbInstance] = Zend_Db::factory($config);
			if (self::isDebugging()) {
				self::$_dbInstances[$product][$dbInstance]->getProfiler()->setEnabled(true);
			}
			return self::$_dbInstances[$product][$dbInstance];
		}
	}
	
	/**
	 * Get hash into 512 filder
	 */
	public static function hashName($fileName, $totalTb = 512) {
		if (!empty($fileName)) {
			$total = 0;
			$array = str_split($fileName);
			foreach($array as $char) {
				$total = $total% $totalTb + ord($char);
			}
			$result = sprintf('%04d', $total % $totalTb);
			return $result;
		} else {
			return '';
		}
	}
	
	public static function doPaging($arrData, $page, $view) {
		// get paging configuration
		$config = Utils_Global::getConfig('admin', 'site');
		$arrPaging = $config->pagination->toArray();
		$paginator  = Zend_Paginator::factory($arrData);
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage($config->pagination->itemPerPage)->setPageRange($arrPaging['pageRange']);
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		// setup view helpers
		//$view->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/');
		$viewRenderer->setView($view);
		$viewRenderer->view->paginator      = $paginator;
		$viewRenderer->view->currentPage    = $page;
		$viewRenderer->view->scrollingStyle = $arrPaging['scrollingStyle'];
		Zend_View_Helper_PaginationControl::setDefaultViewPartial($arrPaging['paginationControl'] . '_pagination_control.phtml');
	}
	
	/**
	 *
	 * Debug Mode Switcher
	 *
	 * @return boolean
	 *
	 */
	public static function isDebugging() {
		// MDT = Master Debug Turn (on/off)
		$debug = false;
		if (self::$params['MDT'] == 'on') {
			$debug = true;
		}
		if (self::$params['MDT'] == 'off') {
			$debug = false;
		}
		return $debug;
	}
	
	/**
	 * Store log: exception, info or debug
	 * @param string $message
	 * @return true on success, otherwise false
	 * */
	public static function storeLog($e, $filePath, $line, $logFile=null) {
		try {
			if($logFile == null) {
				$logFile = Utils_Global::getConfig('cms', 'site', 'log_file');
			}
			$logFile = $logFile.date('Ymd', time());
			$data = array('message' => ($e instanceof Exception)? $e->getMessage() : $e,
							'file' => $filePath,
							'line' => $line,
							'time' => date('Y-m-d H:i:s', time()),
			);
			$data = json_encode($data);
			return error_log($data .  "\n", 3, $logFile);
		}catch (Exception $e) {
			return 0;
		}
	}
}
