<?php
class Admin_Model_DAO_Center
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_Center
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
     * Insert center
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert('center', $data);
       $result = $this->_db->lastInsertId('center', 'id');
       return $result;
   }
   
   /**
    * Update center by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update('center', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete center by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
       return $this->_db->delete('center', "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of centers by given options
     * @param array $options
     * @return array
     * */
	public function getCenters($options = array()) {
		$sql = "SELECT `id`, `name`, `image`, `contact_info`, `address`,
		               `city`, `city_code`, `hash_folder`, `last_update`, `dateline` ";
		$from = " FROM `center` ";
		$where = " WHERE 1 = 1 ";
		
		if($options['id']) {
			$where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		
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
	 * Get center count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getCenterCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `center` ";
		$where = " WHERE 1 = 1 ";
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}