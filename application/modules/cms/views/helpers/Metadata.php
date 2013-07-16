<?php
/**
 * 
 * @author nhudt
 *
 */
class cms_View_Helper_Metadata {
	public $view;
	
	public function metadata($configName, $data = array(), $canonical = '')
	{
	    $metadata = array();
	    $config = Utils_Global::getConfig('cms', 'site', 'meta');
	    if(!isset($config->$configName)){
	        throw new Exception('Metadata config not found: ' . $configName);
	    }
	    foreach ($config->$configName as $key => $value) {
	        foreach ($data as $search => $replace) {
	            $value = str_replace('[' . $search . ']', $replace, $value);
	        }
	        $metadata[$key] = $value; 
	    }
	    return array('metadata' => $metadata, 'canonical' => $canonical);
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;		
	}

}