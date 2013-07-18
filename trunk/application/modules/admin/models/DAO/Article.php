<?php
class Admin_Model_DAO_Article
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_Article
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
     * Insert article
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert('article', $data);
       $result = $this->_db->lastInsertId('article', 'id');
       return $result;
   }
   
   /**
    * Update article by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update('article', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete article by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
       return $this->_db->delete('article', "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of articles by given options
     * @param array $options
     * @return array
     * */
	public function getArticles($options = array()) {
		$sql = "SELECT `article`.*, `category`.`name` `category_name` ";
		$from = " FROM `article` INNER JOIN `category` ON `article`.`category` = `category`.`id` ";
		$where = " WHERE 1 = 1 ";
		if($options['top']) {
			$where .= " AND `top` = {$this->_db->quote($options['top'], 'INTEGER')} ";
		}
		if($options['category']) {
			$where .= " AND `category` = {$this->_db->quote($options['category'], 'INTEGER')} ";	
		}
		if($options['id']) {
			$where .= " AND `article`.`id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		if($options['title']) {
		    $where .= " AND `article`.`title` LIKE '%" . $options['title'] . "%'";
		}
		if($options['active'] != -1) {
		    $where .= " AND `article`.`active` = {$this->_db->quote($options['active'], 'INTEGER')} ";
		}
		if($options['datelineF'] && $options['datelineT']) {
		    $where .= " AND `article`.`dateline` >= {$this->_db->quote($options['datelineF'], 'INTEGER')} 
		                AND `article`.`dateline` <= {$this->_db->quote($options['datelineT'], 'INTEGER')} ";
		}
		
		$order = " ORDER BY `article`.`id` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `article`.`{$options['order']}` {$options['by']} ";
		}
		
		$limit = "";
		if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
			$limit = " limit {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} "; 
		}
		$result = $this->_db->fetchAll ( $sql . $from . $where . $order . $limit );
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
		$where = " WHERE 1 = 1 ";
		if($options['top']) {
			$where .= " AND `top` = {$this->_db->quote($options['top'], 'INTEGER')} ";
		}
		if($options['category']) {
			$where .= " AND `category` = {$this->_db->quote($options['category'], 'INTEGER')} ";
		}
		if($options['id']) {
		    $where .= " AND `article`.`id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		if($options['title']) {
			$where .= " AND `article`.`title` LIKE '%" . $options['title'] . "%'";
		}
		if($options['active'] != -1) {
		    $where .= " AND `article`.`active` = {$this->_db->quote($options['active'], 'INTEGER')} ";
		}
		if($options['datelineF'] && $options['datelineT']) {
		    $where .= " AND `article`.`dateline` >= {$this->_db->quote($options['datelineF'], 'INTEGER')}
				AND `article`.`dateline` <= {$this->_db->quote($options['datelineT'], 'INTEGER')} ";
		}
		
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}