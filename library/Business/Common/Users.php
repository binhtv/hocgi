<?php

class Business_Common_Users extends Business_Abstract
{
	private $_tablename = 'cms_users';
	
	const KEY_LIST = 'cms_users.list';
	const KEY_DETAIL = 'cms_users.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_Users
	 *
	 * @return Business_Common_Users
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function checkRight($right = '')
	{
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		if(!$auth->hasIdentity()) return false;
		
		if(is_null(self::$_current_rights))
		{			
			if($auth->hasIdentity())
			{
				$_user = self::getInstance();			
				$userid = $identity->userid;
				self::$_current_rights = $_user->getRolesForUser($userid);	  
			}
			else
			{
				self::$_current_rights = array();
			}			
		}

		$username = $identity->username;
		if($username == "admin") return true;
		
		if(in_array($right, self::$_current_rights)) return true;
		else return false;
	}
	
	private function getKeyList()
	{
		return sprintf(Business_Common_Users::KEY_LIST);
	}
	
	private function getKeyDetail($uid)
	{
		return sprintf(Business_Common_Users::KEY_DETAIL, $uid);
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
	
	public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
						
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY username";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
	
	public function getUserByUid($uid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($uid);
		$result = $cache->getCache($key);
		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ?";
			$data = array($uid);
			$result = $db->fetchAll($query, $data);
			
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				$result = $result[0];
				$cache->setCache($key, $result);
			}			
		}		
		return $result;
	}
	
	public function getUser($username)
	{
		$list = $this->getList();
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['username'] == $username)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
	
	//return userid last inserted
	public function addUser($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		
		$lastid = 0;
		if($result)
		{
			$lastid = $db->lastInsertId();
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $lastid;
	}
	
	public function updateUser($userid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "userid='" . parent::adaptSQL($userid) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			
			$key = $this->getKeyDetail($userid);
			$cache->deleteCache($key);
		}
		return $result;
	}
	
//return userid last inserted
	public function deleteUser($id)
	{		
		$db = $this->getDbConnection();

		$where = array();	
		$where[] = "userid = " . $id;
		try
		{			
			$result = $db->delete($this->_tablename, $where);	
			return $result; 
		}catch(Exception $e){
			pr($e->getMessage());
		}
	}
	
	public function getRolesForUser($userid)
	{
		$_roles = Business_Common_Roles::getInstance();
		
		$user_roles = $_roles->getRolesByUser($userid);		
				
		$user_perm = array();
		
		$_permission = Business_Common_Permissions::getInstance();
		
		if($user_roles != null && is_array($user_roles) && count($user_roles) > 0)
		{
			for($i=0;$i<count($user_roles);$i++)
			{
				$pid = $user_roles[$i]['pid'];				
				$perm = $_permission->getPermision($pid);				
				$perm = explode(',',$perm['permission']);												
				$user_perm = array_merge($user_perm,$perm);				
			}
		}		
		return $user_perm;
	}
	
	
		
}
?>