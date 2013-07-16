<?php

class BlockManagement_Views extends BlockManagement_Abstract 
{
	private $_tablename = 'cms_views';
	private $_module;
	
	const KEY = 'views.viewid.%s'; //views.viewid.[id]
	const KEY_LIST = 'views.list';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		$front = Zend_Controller_Front::getInstance();
		$this->_module = strtolower($front->getRequest()->getParam('module'));		
	}
	
	/**
	 * get instance of BlockManagement_Views
	 *
	 * @return BlockManagement_Views
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	
	public function getKey($viewid)
	{
		return sprintf(self::KEY, $viewid);
	}	
	
	public function getKeyList()
	{
		return self::KEY_LIST;
	}

	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Utils_Global::getDbInstance($this->_module);
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = Utils_Global::getCacheInstance($this->_module);
		return $cache;
	}

	public function getList()
	{
		$key = $this->getKeyList();
		$cache = $this->getCacheInstance();
		$result = false;
		if(is_object($cache)) {
			$result = $cache->getCache($key);;
		}		
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = "select * from " . $this->_tablename . " ORDER BY module, controller, action, viewname";			
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result) && is_object($cache))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}
		
	public function getView($viewid)
	{		
		$key = $this->getKey($viewid);
		$cache = $this->getCacheInstance();
		$result = false;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
		}
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE viewid = ?";
			$data = array($viewid);		
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
	
	public function updateView($viewid, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "viewid='" . parent::adaptSQL($viewid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			if(is_object($cache)) {
				$key = $this->getKey($viewid);
				$cache->deleteCache($key);
				$key = $this->getKeyList();
				$cache->deleteCache($key);
			}
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insertView($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
		$layoutname = $data["layout_name"];
		//xoa cache list
		$cache = $this->getCacheInstance();
		if(is_object($cache)) {
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $result;		
	}
	
	public function deleteView($viewid)
	{
		$block = $this->getView($viewid);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "viewid='" . parent::adaptSQL($viewid) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		if(is_object($cache)) {
			$key = $this->getKey($viewid);
			$cache->deleteCache($key);
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
	}	
	
	public function getListSearch(array $options = null)
	{
		$result = false;
		$key = $this->getKeyList();
		if(!empty($options)){
			if(!empty($options['name'])){
				$key .= md5($options['name']);
			}
		}
		$cache = $this->getCacheInstance();
		if(is_object($cache)) {
			$result = $cache->getCache($key);
		}
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$select = "select * from " . $this->_tablename;
			$where = " where 1 = 1 ";
			if(!empty($options)){
				if(!empty($options['name'])){
					$oName = $options['name'];
					$where .= " AND viewname LIKE '%{$oName}%'";
				}
			}
			$order = " ORDER BY module, controller, action, viewname";
			$query = $select . $where . $order;
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result) && is_object($cache))
			{
				$cache->setCache($key,$result);
			}
		}
		return $result;
	}
}

?>