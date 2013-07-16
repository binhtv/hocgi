<?php
class Admin_Model_DAO_Course
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_Course
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
     * Insert course
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert('course', $data);
       $result = $this->_db->lastInsertId('course', 'id');
       return $result;
   }
   
   /**
    * Update course by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update('course', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete course by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
       return $this->_db->delete('course', "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of courses by given options
     * @param array $options
     * @return array
     * */
	public function getCourses($options = array()) {
		$sql = "SELECT `id`, `name`, `name_seo`, `image`, `active`, `order`,
		                `content`, `best`, `promotion`, `hash_folder`, `opening_date`, `tuition`, 
		                `course_link`, `hot`, `category`, `center_id` ";
		$from = " FROM `course` ";
		$where = " WHERE 1 = 1 ";
		if($options['top']) {
			$where .= " AND `top` = {$this->_db->quote($options['top'], 'INTEGER')} ";
		}
		if($options['category']) {
			$where .= " AND `category` = {$this->_db->quote($options['category'], 'INTEGER')} ";	
		}
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
	 * Get course count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getCourseCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `course` ";
		$where = " WHERE 1 = 1 ";
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}