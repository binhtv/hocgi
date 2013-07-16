<?php
class Admin_Model_DAO_Category
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_Category
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
     * Insert cateogyr
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert('category', $data);
       $result = $this->_db->lastInsertId('category', 'id');
       return $result;
   }
   
   /**
    * Update category by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update('category', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete category by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
       return $this->_db->delete('category', "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of categories by given options
     * @param array $options
     * @return array
     * */
	public function getCategories($options = array()) {
		$sql = "SELECT `cat`.`id` `parent_id`, `cat`.`name` `parent_name`, `cat1`.`id` `child_id`, `cat1`.`name` `child_name`";
		$from = " FROM `category` `cat` LEFT JOIN `category` `cat1` ON `cat`.`id` = `cat1`.`parent` ";
		$where = " WHERE `cat`.`active` = 1 AND (`cat`.`parent` IS NULL OR `cat`.`parent` = 0) ";

		if($options['for_course']) {
		    $where .= " AND `cat`.`for_course` = {$this->_db->quote($options['for_course'], 'INTEGER')} ";
		}
		if($options['id']) {
			$where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		
		$order = " ORDER BY `cat`.`order` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `cat`.`{$options['order']}` {$options['by']} ";
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
	 * Get article count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getArticleCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `article` ";
		$where = " WHERE active = 1 ";
		if($options['top']) {
			$where .= " AND `top` = {$this->_db->quote($options['top'], 'INTEGER')} ";
		}
		if($options['category']) {
			$where .= " AND `category` = {$this->_db->quote($options['category'], 'INTEGER')} ";
		}
		
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}