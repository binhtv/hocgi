<?php

class BlockManagement_Layouts extends BlockManagement_Abstract
{
	private $_tablename = 'cms_layouts';
	private $_module;
	
	const KEY_BY_LAYOUT = 'layouts.layoutname.%s'; //blocks.layout.layout-name	
	const KEY_LIST = 'layouts.layout';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		$front = Zend_Controller_Front::getInstance();
		$this->_module = strtolower($front->getRequest()->getParam('module'));
	}
	
	/**
	 * Get instance of BlockManagement_Layouts
	 *
	 * @return BlockManagement_Layouts
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Get cache Instance
	 */
	function getCacheInstance()
	{
		$cache = Utils_Global::getCacheInstance('cms');		
		return $cache;
	}
	
	public function getKeyList()
	{
		return self::KEY_LIST;
	}
	
	public function getKeyByLayout($layout)
	{
		return sprintf(self::KEY_BY_LAYOUT,$layout);
	}	
	
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db = Utils_Global::getDbInstance('cms');
		return $db;	
	}

	public function getList()
	{		
		$key = $this->getKeyList();
		$cache = $this->getCacheInstance();
		$result = false;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
		}
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "select * from " . $this->_tablename . " ORDER BY layout_name ASC";			
			$result = $db->fetchAll($query);			
			if(!is_null($result) && is_array($result) && is_object($cache))
			{
				$cache->setCache($key,$result);
			}			
		}
		
		return $result;
	}		
		
	public function getLayout($layoutname)
	{
		if($layoutname == '')
			 return null;
		$key = $this->getKeyByLayout($layoutname);
		$cache = $this->getCacheInstance();
		$result = false;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
		}
		
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE layout_name = ?";
			$data = array($layoutname);		
			$result = $db->fetchAll($query,$data);
	
			if(!is_null($result) && is_array($result) && is_object($cache))
			{
				$cache->setCache($key,$result);
			}
		}
		
		if($result != null && is_array($result) && count($result) > 0)
		{
			return $result[0];				
		}
		else return null;
	}
	
	public function updateLayout($layoutname, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "layout_name='" . parent::adaptSQL($layoutname) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			if(is_object($cache)) {
    			$key = $this->getKeyList();
    			$cache->deleteCache($key);
    			$key = $this->getKeyByLayout($layoutname);
    			$cache->deleteCache($key);
			}			
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insertLayout($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
		$layoutname = $data["layout_name"];
		//xoa cache list
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		if(is_object($cache)) {
		    $cache->deleteCache($key);
    		$key = $this->getKeyByLayout($layoutname);
    		$cache->deleteCache($key);		
		}
		return $result;		
	}
	
	public function deleteLayout($layoutname)
	{
		$block = $this->getLayout($layoutname);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "layout_name='" . parent::adaptSQL($layoutname) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		if(is_object($cache)) {
    		$key = $this->getKeyList();
    		$cache->deleteCache($key);
    		$key = $this->getKeyByLayout($layoutname);
    		$cache->deleteCache($key);
		}		
	}
}

?>