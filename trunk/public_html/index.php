<?php
//$ips = array('58.186.195.166', '123.21.112.231', '113.162.179.215', '123.20.72.79', '123.21.97.111');
//if(!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
//    header('Location: /Maintenance.htm');
//}
ob_start();
session_start();
define('DS', DIRECTORY_SEPARATOR);
// Define path to application directory
defined('APPLICATION_BASE')
	|| define('APPLICATION_BASE',
			realpath(dirname(__FILE__)));
			
defined('APPLICATION_PATH') 
	|| define('APPLICATION_PATH', 
				realpath(dirname(__FILE__)) . '/../application');

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV',
		(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') 
									: 'development'));

define('CONFIG_PATH', realpath(APPLICATION_PATH . '/configs'));
	
//Typically, you will also want to add your library/ directory
// to the include_path, particularly if it contains your ZF installed
set_include_path(APPLICATION_BASE.'/../library');

/** Zend_Application */
require_once 'Zend/Application.php';
$environment = APPLICATION_ENV;
$option = array(APPLICATION_PATH . '/configs/cms/application.ini');
$application = new Zend_Application($environment, array('config' => $option));
$options = array(
		'bootstrap' => array(
				'path' 	=> APPLICATION_PATH . DS . 'Bootstrap.php',
				'class' => 'Bootstrap',
		),
		'autoloadernamespaces' => array(
				"Plugin" => "Plugin_",
				"Utils" => "Utils_",
		        'Skoch' => "Skoch_",
				"BlockManagement" => "BlockManagement_",
		        "Business" => "Business_",
		        "Business_Common" => "Business_Common_",
		),
);

$options = $application->mergeOptions($application->getOptions(), $options);
$application->setOptions($options)
			->bootstrap();
ob_end_flush();