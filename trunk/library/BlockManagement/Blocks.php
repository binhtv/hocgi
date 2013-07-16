<?php

class BlockManagement_Blocks extends BlockManagement_Abstract
{
	private $_tablename = 'cms_blocks';
	private $_module;
	
	
	const KEY_LIST_BY_LAYOUT = 'blocks.layout.%s'; //blocks.layout.layout-name
	const KEY_LIST_BY_LAYOUT_ACTIVE = 'blocks.layout.%s.active';	//blocks.layout.layout-name.active
	const KEY_LIST = 'blocks.layout';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		$front = Zend_Controller_Front::getInstance();
		$this->_module = strtolower($front->getRequest()->getParam('module'));
	}
	
	/**
	 * get instance of BlockManagement_Blocks
	 *
	 * @return BlockManagement_Blocks
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getKeyList()
	{
		return self::KEY_LIST;
	}
	
	public function getKeyListByLayout($layout)
	{
		return sprintf(self::KEY_LIST_BY_LAYOUT,$layout);
	}
	
	public function getKeyListByLayoutActive($layout)
	{
		return sprintf(self::KEY_LIST_BY_LAYOUT_ACTIVE,$layout);
	}
	
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db = Utils_Global::getDbInstance($this->_module);
		return $db;	
	}
	
	function getCacheInstance()
	{
		$cache = Utils_Global::getCacheInstance($this->_module);		
		return $cache;
	}

	public function getListByLayout($layout)
	{
		$key = $this->getKeyListByLayoutActive($layout);
		$cache = $this->getCacheInstance();
		$result = false;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
		}
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "select * from " . $this->_tablename . " where layout = ? and status=1 order by section,weight asc";			
			$data = array($layout);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && is_object($cache))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}
		
	public function getList($layout = '')
	{		
		$key = $this->getKeyListByLayout($layout);
		$cache = $this->getCacheInstance();
		$result = false;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
		}		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();			
			if($layout == '')
			{
				$query = "select * from " . $this->_tablename . " order by blockid asc";
				$data = array();	
			}
			else
			{			
				$query = "select * from " . $this->_tablename . " where layout = ? order by blockid asc";
				$data = array($layout);
			}						
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && is_object($cache))
			{
				$cache->setCache($key, $result);
			}					
		}
		return $result;
	}
	
	public function getBlock($blockid)
	{
		$db = $this->getDbConnection();
		$query = " SELECT * FROM {$this->_tablename}"
						." WHERE blockid = ?";
		$data = array($blockid);		
		$result = $db->fetchAll($query, $data);
		if($result != null && is_array($result))
		{
			return $result[0];				
		}
		else return null;
	}
	
	public function updateBlock($blockid, $data)
	{
		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "blockid='" . parent::adaptSQL($blockid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$layout = $data['layout'];
			
			$cache = $this->getCacheInstance();
			if(is_object($cache)) {
    			$key = $this->getKeyList();
    			$cache->deleteCache($key);
    			$key = $this->getKeyListByLayout($layout);
    			$cache->deleteCache($key);			
    			$key = $this->getKeyListByLayoutActive($layout);
    			$cache->deleteCache($key);
    			
    			$key = $this->getKeyListByLayout('');
    			$cache->deleteCache($key);
    			$key = $this->getKeyListByLayoutActive('');
    			$cache->deleteCache($key);
			}
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insertBlock($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
		$layout = $data["layout"];
		//xoa cache list
		$cache = $this->getCacheInstance();
		if(is_object($cache)) {
			$key = $this->getKeyList();		
			$cache->deleteCache($key);
			$key = $this->getKeyListByLayout($layout);
			$cache->deleteCache($key);
			$key = $this->getKeyListByLayoutActive($layout);
			$cache->deleteCache($key);
			
			$key = $this->getKeyListByLayout('');
			$cache->deleteCache($key);
			$key = $this->getKeyListByLayoutActive('');
			$cache->deleteCache($key);
		}
		
		return $result;		
	}
	
	public function deleteBlock($blockid)
	{
		$block = $this->getBlock($blockid);
		if($block == null) return;
		
		$layout = $block["layout"];
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "blockid='" . parent::adaptSQL($blockid) . "'";
		$result = $db->delete($this->_tablename,$where);
	}
	
	public function getListSearch(array $options = null)
	{
	    $result = false;
		$key = $this->getKeyList();
		if(!empty($options)){
			if(!empty($options['layout_name']) || !empty($options['layout']) || !empty($options['description'])){
				$key .= md5($options['layout_name'] . '_' . $options['layout'] . '_' . $options['description']);
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
				if(!empty($options['layout_name'])){
					$oName = $options['layout_name'];
					$where .= " AND layout LIKE '%{$oName}%'";
				}if(!empty($options['layout'])){
					$oName = $options['layout'];
					$where .= " AND layout = '{$oName}'";
				}if(!empty($options['description'])){
					$oDescription = $options['description'];
					$where .= " AND description LIKE '%{$oDescription}%'";
				}
			}
			$order = " order by blockid asc";
			$query = $select . $where . $order;
			//var_dump($query);
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