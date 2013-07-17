<?php

class Admin_Model_Resource
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
	 * get all Resource from db
	 */
	public function getResource(array $options = null)
	{
		try{		
			$sql = "SELECT * ";
			$from = " FROM `cms_resources` ";
			$where = " WHERE 1 = 1  AND active = 1 ";
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
	
	public function insertResource($data)
	{
		try{
			$this->_tablename = 'cms_resources';		
			$result = $this->_db->insert($this->_tablename,$data);	
			return $result;	
		}
		catch(Exception $e)
		{
			return 0;
		}		
	}
	
	public function updateResource($data, $id)
	{		
		$this->_tablename = 'cms_resources';
		$where = array();	
		$where[] = "id = " . $id;
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
	public function deleteResource($id)
	{ 
		$this->_tablename = 'cms_resources';
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
	
	public function hasResource($resource)
	{
		$sql = "SELECT id FROM `cms_resources` WHERE name = {$this->_db->quote($resource)} ";
		try
		{			
			$result = $this->_db->fetchAll($sql);	
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
	
	public function inactiveResource($option)
	{
		try
		{			
			$this->_tablename = 'cms_resources';
			$where = array();	
			if(!empty($option['id'])){
				$where[] = "id = " . $option['id'];
			}
			$data = array("active" => 0);
			$result = $this->_db->update($this->_tablename, $data, $where);	
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
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