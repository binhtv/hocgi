<?php
class Admin_Model_DAO_Position
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_Position
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
     * Insert position
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert('position', $data);
       $result = $this->_db->lastInsertId('position', 'id');
       return $result;
   }
   
   /**
    * Update position by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update('position', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete position by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
       return $this->_db->delete('position', "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of positions by given options
     * @param array $options
     * @return array
     * */
	public function getPositions($options = array()) {
		$sql = "SELECT `id`, `position_name`, `last_update`, `dateline` ";
		$from = " FROM `position` ";
		$where = " WHERE 1 = 1 ";
		
		$order = " ORDER BY `id` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `{$options['order']}` {$options['by']} ";
		}
		
		$limit = "";
		if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
			$limit = " limit {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} "; 
		}
		if($options['id']) {
			$result = $this->_db->fetchRow( $sql . $from . $where . $order . $limit );
		} else {
			$result = $this->_db->fetchAll ( $sql . $from . $where . $order . $limit );
		}
		return $result;
	}
	
	/**
	 * Get Position count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getPositionsCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `position` ";
		$where = " WHERE 1 = 1 ";
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}