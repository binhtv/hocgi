<?php
class Admin_Model_DAO_Documentary
{    
    private $_db;
    private static $_instance = null;
    private $_tablename = "documentary";
    
    /**
     * @return Admin_Model_DAO_Documentary
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    private function __construct()
    {
    	$module = Utils_Global::get('module');
    	$this->_db = Utils_Global::getDbInstance($module);
    }
    
    /**
     * Insert documentary
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert($this->_tablename, $data);
       $result = $this->_db->lastInsertId('course', 'id');
       return $result;
   }
   
   /**
    * Update documentary by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update($this->_tablename, $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete documentary by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
       return $this->_db->delete($this->_tablename, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of documentary by given options
     * @param array $options
     * @return array
     * */
	public function getDocumentaries($options = array()) {
		$sql = "SELECT * ";
		$from = " FROM `{$this->_tablename}` ";
		$where = " WHERE 1 = 1 ";
		if($options['editor']) {
			$where .= " AND `editor` = {$this->_db->quote($options['editor'])} ";
		}
		if($options['new']) {
		    if($options['new'] == 2) {
		        $new = 0;
		    } else {
		        $new = $options['new'];
		    }
			$where .= " AND `new` = {$this->_db->quote($new, 'INTEGER')} ";
		}
		
		if($options['id']) {
			$where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		if($options['name']) {
		    $where .= " AND `name` LIKE '%" . $options['name'] . "%' ";
		}
		if($options['active']) {
		    if($options['active'] == 2) {
		    	$active = 0;
		    } else {
		    	$active = 1;
		    }
		    $where .= " AND `active` = {$this->_db->quote($active, 'INTEGER')} ";
		}
		
		$order = " ORDER BY `id` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `{$options['order']}` {$options['by']} ";
		}
		
		$limit = "";
		
		if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
			$limit = " limit {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} "; 
		}
		$result = $this->_db->fetchAll ( $sql . $from . $where . $order . $limit );
		return $result;
	}
	
	/**
	 * Get documentary count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getDocumentaryCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `course` ";
		$where = " WHERE 1 = 1 ";
		if($options['editor']) {
			$where .= " AND `editor` = {$this->_db->quote($options['editor'])} ";
		}
	    if($options['new']) {
		    if($options['new'] == 2) {
		        $new = 0;
		    } else {
		        $new = $options['new'];
		    }
			$where .= " AND `hot` = {$this->_db->quote($new, 'INTEGER')} ";
		}
		if($options['id']) {
		    $where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		if($options['name']) {
		    $where .= " AND `name` = {$this->_db->quote($options['name'])} ";
		}
	    if($options['active']) {
		    if($options['active'] == 2) {
		    	$active = 0;
		    } else {
		    	$active = 1;
		    }
		    $where .= " AND `active` = {$this->_db->quote($active, 'INTEGER')} ";
		}
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}