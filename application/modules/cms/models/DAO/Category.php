<?php
class Cms_Model_DAO_Category
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Category
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
     * Get category by given options
     * @param array $options
     * @return array
     * */
    public function getCategories($options = array()) {
    	$sql = "SELECT `id`, `name`, `name_seo`, `for_course` ";
    	$from = " FROM `category` ";
    	$where = " WHERE active = 1 ";
    	
    	if($options['parent']) {
    	    $where .= " AND `parent` = {$this->_db->quote($options['parent'], 'INTEGER')} ";
    	}
    	if($options['ids']) {
    		$where .= " AND `id` IN ({$options['ids']}) ";
    	} elseif($options['id']) {
    		$where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
    	}
    	
    	$order = " ORDER BY `order` DESC ";
		if($options['order'] && $options['by']) {
    			$order = " ORDER BY `{$options['order']}` {$options['by']} ";
    	}
    	
    	$limit = "";
    	
    	if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
			$limit = " limit {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} ";
		}
		
		if($options['id']) {
			$result = $this->_db->fetchRow ( $sql . $from . $where . $order . $limit );
		} else {
    		$result = $this->_db->fetchAll ( $sql . $from . $where . $order . $limit );
		}
    	return $result;
    }
}