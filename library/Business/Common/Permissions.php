<?php

class Business_Common_Permissions extends Business_Abstract
{
	private $_tablename = 'cms_permission';
	
	const KEY = 'perm.pid.%s'; //perm.pid.[id]
	const KEY_LIST = 'perm.list';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_Permissions
	 *
	 * @return Business_Common_Permissions
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_Permissions();
		}
		return self::$_instance;
	}
	
	
	
	public function getKey($id)
	{
		return sprintf(Business_Common_Permissions::KEY,$id);
	}	
	
	public function getKeyList()
	{
		return Business_Common_Permissions::KEY_LIST;
	}

	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db = Utils_Global::getDbInstance('admin');
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}

	public function getList()
	{
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename . " ORDER BY name";
		$result = $db->fetchAll($query);
		return $result;
	}
	
	public function checkName($name)
	{
		$list = $this->getList();
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['name'] == $name)
				{
					unset($list);
					return true;
				}
			}			
		}
		
		return false;
	}
	
	public function getPermision($pid)
	{
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename . " WHERE pid = ?";
		$data = array($pid);
		$result = $db->fetchAll($query, $data);						
		return $result;		
	}
	
	public function addPermision($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		return $result;		
	}
	
	public function updatePermission($pid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "pid='" . $this->adaptSQL($pid) . "'";		
		$result = $db->update($this->_tablename, $data, $where);
		return $result;
	}
	
	public function deletePermision($pid)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "pid='" . $this->adaptSQL($pid) . "'";
		$result = $db->delete($this->_tablename, $where);
		return $result;
	}
	
}

?>