<?php

class Admin_Model_Permission
{
	private $_db;
	private $_tablename;
	
	public function __construct(array $options = null)
    {
		$this->_db = Utils_Global::getDbInstance('admin');
		
		if (is_array($options)) {
            $this->setOptions($options);
        }
    }
	
	public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
	
	/**
	 * get all Permission from db
	 */
	public function getPermission(array $options = null)
	{
		try{		
			$sql = "SELECT * , r.name resource_name, role.name role_name ";
			$from = " FROM `cms_permission` p INNER JOIN `cms_resources` r ON p.resource_id = r.id
						INNER JOIN `cms_roles` role ON role.id = p.role_id ";
			$where = " WHERE 1 = 1 AND r.active = 1";
			$order = "";
			
			if(!empty($options)){
				if(!empty($options['id'])){
					$where .= " AND id = " . $options['id'];
				}
				if(isset($options['role_id'])){
					$where .= " AND role_id = " . $options['role_id'];
				}
				
			}

			$result = $this->_db->fetchAll($sql.$from.$where.$order);
			return $result;
		}
		catch(Exception $e){
			pr($e->getMessage());
		}
	}	
	
	public function insertPermission($data)
	{
		try{
			$this->_tablename = 'cms_permission';		
			$result = $this->_db->insert($this->_tablename,$data);	
			return $result;	
		}
		catch(Exception $e)
		{
			return 0;
		}		
	}
	
	public function updatePermission($data, $option)
	{		
		$this->_tablename = 'cms_permission';
		$where = array();
		if(!empty($option['role_id'])){
			$where[] = "role_id = " . $option['role_id'];			
		}	
		if(!empty($option['resource_id'])){
			$where[] = "resource_id = " . $option['resource_id'];			
		}
		try
		{			
			$result = $this->_db->update($this->_tablename, $data, $where);	
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	/**
	 * delete a OS
	 */
	public function deletePermission($id)
	{ 
		$this->_tablename = 'cms_permission';
		$where = array();	
		$where[] = "id = " . $id;
		try
		{			
			$result = $this->_db->delete($this->_tablename, $where);	
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	/**
	 * delete a Permission
	 */
	public function deletePermissionByRoleId($id)
	{ 
		$this->_tablename = 'cms_permission';
		$where = array();	
		$where[] = "role_id = " . $id;
		try
		{			
			$result = $this->_db->delete($this->_tablename, $where);	
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	
	public function hasPermission($options)
	{ 
		try{
			$sql = "SELECT pid ";
			$from = " FROM `cms_permission` ";
			$where = " WHERE 1 = 1 ";
						
			if(!empty($options)){
				if(!empty($options['role_id'])){
					$where .= " AND role_id = " . $options['role_id'];
				}
				if(isset($options['resource_id'])){
					$where .= " AND resource_id = " . $options['resource_id'];
				}				
			}

			$result = $this->_db->fetchAll($sql.$from.$where); 
			if(count($result) > 0)
				return true;
			else
				return false;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	
	
	//////////////////////// private functions //////////////////////////
			
	private function parseArrayToObject($array) {
		$object = new stdClass();
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $name=>$value) {
				$name = strtolower(trim($name));
				if (!empty($name)) {
					$object->$name = $value;
				}
			}
		}
		return $object;
	}
}