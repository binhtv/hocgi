<?php

class Admin_Model_Privilege
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
	 * get all Privilege from db
	 */
	public function getPrivilege(array $options = null)
	{
		try{		
			$sql = "SELECT * ";
			$from = " FROM `cms_privileges` ";
			$where = " WHERE 1 = 1 ";
			$order = "";
			
			if(!empty($options)){
				if(!empty($options['id'])){
					$where .= " AND id = " . $options['id'];
				}
			}

			$result = $this->_db->fetchAll($sql.$from.$where.$order);
			return $result;
		}
		catch(Exception $e){
			pr($e->getMessage());
		}
	}	
	
	public function insertPrivilege($data)
	{
		try{
			$this->_tablename = 'cms_privileges';		
			$result = $this->_db->insert($this->_tablename,$data);	
			return $result;	
		}
		catch(Exception $e)
		{
			return 0;
		}		
	}
	
	public function updatePrivilege($data, $id)
	{		
		$this->_tablename = 'cms_privileges';
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
	public function deletePrivilege($id)
	{ 
		$this->_tablename = 'cms_privileges';
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