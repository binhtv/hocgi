<?php

class Business_Common_Variables extends Business_Abstract
{
	private $_tablename = 'variables';
					
	private static $_instance = null;

	const KEY_VARIABLE = 'var.detail.%s';
	const KEY_LIST = 'var.list';
	
	function __construct()
	{			
	}	
	
	private function getKeyVar($name)
	{
		return sprintf(Business_Common_Variables::KEY_VARIABLE, $name);
	}
	
	private function getKeyList()
	{
		return sprintf(Business_Common_Variables::KEY_LIST);
	}
		
	
	/**
	 * get instance of Business_Common_Variables
	 *
	 * @return Business_Common_Variables
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_Variables();
		}
		return self::$_instance;
	}
	
	public static function variable_get($name, $default_value)
	{
		$_variable = Business_Common_Variables::getInstance();
		return $_variable->getVariable($name, $default_value);
	}
	
	public static function variable_set($name, $value)
	{
		$_variable = Business_Common_Variables::getInstance();
		return $_variable->setVariable($name, $value);
	}
		
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	private function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');		
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();
		return $cache;
	}
	
	public function setVariable($name, $value)
	{
		$result = $this->_getVariable($name);		
		if($result == null)
		{
			//chua co variable -> insert
			
			$this->_insertVariable($name, $value);
		}
		else
		{
			//co variable -> update
			$this->_updateVariable($name, $value);
		}
		return true;
	}
	
	public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY name";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}

	public function getVariable($name, $default_value = NULL)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyVar($name);
		$result = $cache->getCache($key);
		
		if($result === FALSE)
		{			
			$value = $this->_getVariable($name);
			if($value != null)
			{
				if($value['serialized'] == '1')
				{
					$result = $this->_deserialize($value['value']);
				}
				else 
				{
					$result = $value['value'];
				}					 
			}
			else
			{
				$result = $default_value;
			}
			$cache->setCache($key, $result);		
		}
		return $result;
				
	}
	
	//private functions	
	
	private function _getVariable($name)
	{
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename . " WHERE name=?";
		$data = array($name);
		$result = $db->fetchAll($query,$data);				
		if(!is_null($result) && is_array($result) && count($result) > 0)
		{
			return $result[0];
		}
		else
		{
			return null;
		}
	}
	
	private function _updateVariable($name, $value)
	{
		$db = $this->getDbConnection();
			
		$data = array();
		if(is_array($value))
		{
			$value = $this->_serialize($value);
			$data['value'] = $value;
			$data['serialized'] = '1';
		}
		else
		{
			$data['value'] = $value;
			$data['serialized'] = '0';
		}
		
		$where = array();
		$where[] = "name='" . parent::adaptSQL($name) . "'";
		
		$result = $db->update($this->_tablename, $data, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();			
			$key = $this->getKeyVar($name);			
			$cache->deleteCache($key);
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $result;
	}
	
	private function _insertVariable($name, $value)
	{
		$db = $this->getDbConnection();
				
		$data = array();
		if(is_array($value))
		{
			$value = $this->_serialize($value);
			$data['value'] = $value;
			$data['serialized'] = '1';
		}
		else
		{
			$data['value'] = $value;
			$data['serialized'] = '0';
		}
		$data['name'] = $name;
		$result = $db->insert($this->_tablename, $data);
		if($result)
		{
			$cache = $this->getCacheInstance();			
			$key = $this->getKeyVar($name);
			$cache->deleteCache($key);
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $result;
	}
	
	private function _serialize($data = array())
	{
		return json_encode($data);
	}
	
	private function _deserialize($input = '')
	{
		if($input = '') return array();
		try
		{		
			$return = json_decode($input, true);
			return $return;
		}
		catch(Exception $e)
		{
			return array();
		}
	}
	

}

?>