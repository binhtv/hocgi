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
		                `course_link`, `hot`, `category`, `center_id`, `schedule` ";
		$from = " FROM `course` ";
		$where = " WHERE 1 = 1 ";
		if($options['editor']) {
			$where .= " AND `editor` = {$this->_db->quote($options['editor'])} ";
		}
		if($options['hot']) {
		    if($options['hot'] == 2) {
		        $hot = 0;
		    } else {
		        $hot = $options['hot'];
		    }
			$where .= " AND `hot` = {$this->_db->quote($hot, 'INTEGER')} ";
		}
		if($options['promotion']) {
		    if($options['promotion'] == 2) {
		        $promotion = 0;
		    } else {
		        $promotion = $options['promotion'];
		    }
			$where .= " AND `promotion` = {$this->_db->quote($promotion, 'INTEGER')} ";
		}
		if($options['category']) {
			$where .= " AND `category` = {$this->_db->quote($options['category'], 'INTEGER')} ";	
		}
		if($options['categories']) {
			$where .= " AND `category` IN ({$options['categories']}) ";
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
		if($options['beginingF']) {
		    $where .= " AND `opening_date` >= {$options['beginingF']} ";
		}
		if($options['beginingT']) {
		    $where .= " AND `opening_date` <= {$options['beginingT']} ";
		}
		if($options['center_id']) {
		    $where .= " AND `center_id` = {$this->_db->quote($options['center_id'], 'INTEGER')} ";
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
	 * Get course count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getCourseCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `course` ";
		$where = " WHERE 1 = 1 ";
		if($options['editor']) {
			$where .= " AND `editor` = {$this->_db->quote($options['editor'])} ";
		}
	    if($options['hot']) {
		    if($options['hot'] == 2) {
		        $hot = 0;
		    } else {
		        $hot = $options['hot'];
		    }
			$where .= " AND `hot` = {$this->_db->quote($hot, 'INTEGER')} ";
		}
		if($options['promotion']) {
		    if($options['promotion'] == 2) {
		        $promotion = 0;
		    } else {
		        $promotion = $options['promotion'];
		    }
			$where .= " AND `promotion` = {$this->_db->quote($promotion, 'INTEGER')} ";
		}
		if($options['category']) {
			$where .= " AND `category` = {$this->_db->quote($options['category'], 'INTEGER')} ";
		}
		if($options['categories']) {
		    $where .= " AND category IN ({$options['categories']}) ";
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
		if($options['beginingF']) {
		    $where .= " AND `opening_date` >= {$this->_db->quote($options['beginingF'], 'INTEGER')} ";
		}
		if($options['beginingT']) {
		    $where .= " AND `opening_date` <= {$this->_db->quote($options['beginingT'], 'INTEGER')} ";
	    }
	    if($options['center_id']) {
	    	$where .= " AND `center_id` = {$this->_db->quote($options['center_id'], 'INTEGER')} ";
		}
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}