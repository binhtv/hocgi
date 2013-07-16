<?php
class Cms_Model_DAO_Book
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Book
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
     * Get list of Books by given options
     * @param array $options
     * @return array
     * */
	public function getBooks($options = array()) {
		$sql = "SELECT `id`, `name`, `name_seo`, `author`, `image`, `content`, `hash_folder`, `price`, `short_description` ";
		$from = " FROM `book` ";
		$where = " WHERE active = 1 ";
		
		if($options['hot']) {
			$where .= " AND `hot` = {$this->_db->quote($options['hot'], 'INTEGER')} ";
		}
		if($options['id']) {
		    $where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		
		$order = " ORDER BY `order` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `{$options['order']}` {$options['by']} ";
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
}