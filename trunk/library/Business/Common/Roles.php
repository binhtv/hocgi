<?php

class Business_Common_Roles extends Business_Abstract
{
	private $_tablename = 'cms_users_roles';
		
	const KEY_LIST = 'user_roles.list';
	const KEY_LIST_BY_USER = 'user_roles.listbyuser.%s';	//list by user
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_Roles
	 *
	 * @return Business_Common_Roles
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_Roles();
		}
		return self::$_instance;
	}
	
	
	
	public function getKeyByUser($userid)
	{
		return sprintf(Business_Common_Roles::KEY_LIST_BY_USER,$userid);
	}	
	
	public function getKeyList()
	{
		return Business_Common_Roles::KEY_LIST;
	}

	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb', false);
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

	public function getRolesByUser($userid)
	{
		$cache = $this->getCacheInstance();		
		$key = $this->getKeyByUser($userid);
		$result = $cache->getCache($key);
		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT pid FROM " . $this->_tablename . " WHERE userid = ?";
			$data = array($userid);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;			
	}
	
	public function checkRole($userid, $pid)
	{
		$roles = $this->getRolesByUser($userid);
		if($roles != null)
		{
			if(in_array($pid, $roles))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else return false;
	}

	public function addRole($userid, $pid)
	{		
		if($this->checkRole($userid, $pid))
		{
			return;
		}
		
		$db = $this->getDbConnection();
		$data = array(
			'userid' => $userid,
			'pid' => $pid
		);
		
		$result = $db->insert($this->_tablename, $data);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyByUser($userid);
			$cache->deleteCache($key);
		}
	}
	
	public function deleteAllRoleForUser($userid)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "userid='" . parent::adaptSQL($userid) . "'";
		$result = $db->delete($this->_tablename, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyByUser($userid);
			$cache->deleteCache($key);
		}
	}
	
	
}

?>