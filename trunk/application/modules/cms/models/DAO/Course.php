<?php
class Cms_Model_DAO_Course
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Course
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
     * Get list of Courses by given options
     * @param array $options
     * @return array
     * */
	public function getCourses($options = array()) {
		$sql = "SELECT `course`.`id`, `course`.`name`, `name_seo`, `course`.`image`, `content`, `course`.`hash_folder`,`tuition`, `course_link`, `category`,
		                    `opening_date`, `city`, `center`.`name` `center_name`, `center`.`image` `center_logo`, `center`.`hash_folder` `chash_folder`, `contact_info` ";
		$from = " FROM `course` INNER JOIN `center` ON `course`.`center_id` = `center`.`id` ";
		$where = " WHERE active = 1 ";
		if($options['best']) {
			$where .= " AND best = 1 ";
		}
		if($options['promotion']) {
			$where .= " AND promotion = 1 ";
		}
		if($options['city']) {
			$where .= " AND `center`.`city_code` LIKE '%{$options['city']}%' ";
		}
		if($options['tuition_from']) {
			$where .= " AND tuition >= {$this->_db->quote($options['tuition_from'], 'INTEGER')} ";
		}
		if($options['tuition_to']) {
			$where .= " AND tuition <= {$this->_db->quote($options['tuition_to'], 'INTEGER')} ";
		}
		if($options['name']) {
			$where .= " AND `course`.`name` LIKE '%{$options['name']}%' ";
		}
		if($options['id']) {
			$where .= " AND `course`.`id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		if($options['hot']) {
			$where .= " AND hot = {$this->_db->quote($options['hot'], "INTEGER")} ";
		}
	    if($options['category']) {
	        $where .= " AND category = {$this->_db->quote($options['category'], 'INTEGER')} ";
	    }
	    if($options['categories']) {
	        $where .= " AND category IN ({$options['categories']}) ";
	    }
		
		$order = " ORDER BY `order` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `course`.`{$options['order']}` {$options['by']} ";
		}
		
		$limit = "";
		if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
			$limit = " LIMIT {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} "; 
		}
		if($options['id']) {
			$result = $this->_db->fetchRow( $sql . $from . $where . $order . $limit );
		} else {
			$result = $this->_db->fetchAll ( $sql . $from . $where . $order . $limit );
		}
		return $result;
	}
	
	/**
	 * Get course count by given condition
	 * @param array $options
	 * @return integer
	 * */
	public function getCourseCount($options=array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `course` INNER JOIN `center` ON `course`.`center_id` = `center`.`id` ";
		$where = " WHERE active = 1 ";
		if($options['category']) {
			$where .= " AND category = {$this->_db->quote($options['category'], 'INTEGER')} ";
		}
		if($options['categories']) {
			$where .= " AND category IN ({$options['categories']}) ";
		}
		if($options['best']) {
			$where .= " AND best = 1 ";
		}
		if($options['promotion']) {
			$where .= " AND promotion = 1 ";
		}
		if($options['city']) {
			$where .= " AND `center`.`city_code` = '%{$options['city']}%' ";
		}
		if($options['tuition_from']) {
			$where .= " AND tuition >= {$this->_db->quote($options['tuition_from'], 'INTEGER')} ";
		}
		if($options['tuition_to']) {
			$where .= " AND tuition <= {$this->_db->quote($options['tuition_to'], 'INTEGER')} ";
		}
		if($options['name']) {
			$where .= " AND `course`.`name` LIKE '%{$options['name']}%' ";
		}
		if($options['id']) {
			$where .= " AND `course`.`id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		if($options['hot']) {
			$where .= " AND hot = {$this->_db->quote($options['hot'], "INTEGER")} ";
		}
		
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
	
	/**
	 * Get course by give keyword
	 * @param string $k
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function searchFullText($k, $offset, $limit) {
	    $searchSql = "SELECT `course`.`id`, `course`.`name`, `name_seo`, `course`.`image`, `content`, `course`.`hash_folder`,`tuition`, `course_link`, `category`,
		                    `opening_date`, `city`, `center`.`name` `center_name`, `center`.`image` `center_logo`, `center`.`hash_folder` `chash_folder`, `contact_info` 
	                    FROM `course` INNER JOIN `center` ON `course`.`center_id` = `center`.`id` WHERE active = 1
                	    and ( MATCH (`course`.`name`) AGAINST ({$this->_db->quote("*$k*")}) OR `course`.`name` LIKE {$this->_db->quote("%$k%")})
                	    or ( MATCH (content) AGAINST ({$this->_db->quote("*$k*")}) OR content LIKE {$this->_db->quote("%$k%")})
                	    ORDER BY `course`.`dateline` DESC 
	                    LIMIT $offset , $limit ";
	    $result = $this->_db->fetchAll ( $searchSql );
	    return $result;
	}
	
	/**
	 * Get count full text
	 * @param string $k
	 * @return integer
	 * */
	public function countFullText($k) {
	    $searchSql = "SELECT count(1) as `count`  FROM `course` INNER JOIN `center` ON `course`.`center_id` = `center`.`id` WHERE active = 1
                	    and ( MATCH (`course`.`name`) AGAINST ({$this->_db->quote("*$k*")}) OR `course`.`name` LIKE {$this->_db->quote("%$k%")});
                	    or ( MATCH (content) AGAINST ({$this->_db->quote("*$k*")}) OR content LIKE {$this->_db->quote("%$k%")})";
	    $result = $this->_db->fetchOne ( $searchSql );
	    return $result;
	}
}