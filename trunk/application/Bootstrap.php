<?php
use Zend\Form\Element\DateSelect;
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	private static $_moduleNames;
	/**
	 * Init route
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
	
		$router = new Zend_Controller_Router_Rewrite();
		$moduleNames = self::loadModuleNames();
		foreach($moduleNames as $name){
			$configFiles = $this->loadRouteConfigs($name);
			foreach ($configFiles as $file){
				$config = new Zend_Config_Ini($file, 'routes');
				$router->addConfig($config, 'routes');
			}
		}
		
		$front->setRouter($router);
	
		/**
		 * Don't use default route
		 */
//		$front->getRouter()->removeDefaultRoutes();
	}
	
// 	protected function _initPlugins() {
// 		$this->bootstrap('FrontController');
// 		$front = $this->getResource('FrontController');
// 		$front->registerPlugin(new Plugin_StartUp());
		
// 		// Error handler
// 		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
// 				'controller' => 'error',
// 				'action' => 'error',
// 		)));
		
// 		Zend_Controller_Front::getInstance()->throwExceptions(false);//Not to throw exception when error
// 	}
	
	protected function _initAutoLoad() {
		$autoloader = new Zend_Loader_Autoloader_Resource (
				array(
						'basePath'      => APPLICATION_PATH . DS . 'modules',
						'namespace'     => '',
						'resourceTypes' => array (
								'cms_model' => array (
										'namespace' => 'Cms_Model',
										'path'      => 'cms/models',
								    ),
								'cms_dao' => array('namespace' => 'Cms_Model_DAO',
													'path'      => 'cms/models/DAO',
								 ),
						        'admin_model' => array('namespace' => 'Admin_Model',
						                                'path' => 'admin/models'
						        ),
								'admin_dao' => array('namespace' => 'Admin_Model_DAO',
								                        'path' => 'admin/models/DAO'
								),
						)
				)
		);
// 		$autoloader = Zend_Loader_Autoloader::getInstance();
// 		$modules = self::loadModuleNames();
// 		foreach($modules as $module){
// 			new Utils_Autoloader(array(
// 					'basePath' => APPLICATION_PATH . DS . 'modules' . DS . $module,
// 					'namespace' => ucfirst($module) . '_',
// 			));
// 		}
		
// 		return $autoloader;
	}
	
	/**
	 *
	 * Setup controllers
	 *
	 * @return void
	 */
	protected function _initControllers()
	{
		$config = Utils_Global::getConfig('global', 'product');
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Plugin_StartUp(array()))
								->registerPlugin(new Plugin_BlockManagement_BlockManagement(array()))
								 ->registerPlugin(new Plugin_Perm());
		$front->setControllerDirectory($config->controllers->toArray());
		try {
			$front->throwExceptions(true);
			$front->dispatch();
		} catch(Exception $ex) {
			$logData = array('code' => $ex->getCode(),
								'message' => $ex->getMessage(),
								'more' => $ex->getTrace());
			Utils_Global::storeLog(json_encode($logData), 
								$ex->getFile(), $ex->getLine(), Utils_Global::getConfig('cms', 'site', 'log_exceptionfile'));
			prBinh($ex->getMessage(), 0);
			prBinh("<hr>", 0);
			prBinh($ex->getTrace());
// 			header("Location: http://{$_SERVER['SERVER_NAME']}/404.htm", false, 301);
		}
		unset($front);
	}
	
	private static function loadModuleNames() {
		if(!isset(self::$_moduleNames)) {
			self::$_moduleNames = self::getSubDir(APPLICATION_PATH . DS . 'modules');
		}
		
		return self::$_moduleNames;
	}
	
	private static function getSubDir($dir)
	{
		if(!file_exists($dir)){
			return array();
		}
	
		$subDirs = array();
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $dir){
			if($dir->isDot() || !$dir->isDir()){
				continue;
			}
			$dir = $dir->getFilename();
			if($dir == '.svn'){
				continue;
			}
			$subDirs[] = $dir;
		}
		return $subDirs;
	}
	
	/**
	 * @return array
	 */
	private function loadRouteConfigs($moduleName)
	{
		$dir = APPLICATION_PATH . DS . 'modules' . DS . $moduleName . DS . 'config' . DS . 'routes';
		if(!is_dir($dir)){
			return array();
		}
	
		$configFile = array();
	
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file){
			if($file->isDot() || $file->isDir()){
				continue;
			}
			$name = $file->getFilename();
			if (preg_match('/^[^a-z]/i', $name) || ('CVS' == $name)
					|| ('.svn' == strtolower($name))) {
				continue;
			}
			$configFiles[] = $dir . DS . $name;
		}
	
		return $configFiles;
	}
}

function prBinh($data, $die = true)
{
	if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '123.21.112.231' || $_SERVER['REMOTE_ADDR'] == '123.21.97.111') {
		$trace = debug_backtrace();
		$caller = array_shift($trace);
		echo '<pre>';
		echo "called by [" . $caller['file'] . "] line: " . $caller['line'] . "\n";
		print_r($data);
		if ($die) {
			exit();
		}
	}
}