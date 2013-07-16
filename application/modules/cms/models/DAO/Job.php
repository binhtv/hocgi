<?php
class Cms_Model_DAO_Job
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Job
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
     * Get list of job choice by given options
     * @param array $options
     * @return array
     * */
	public function getJob($options=array()) {
		$sql = "SELECT `id`, `name`, `name_seo`, `company`, `short_description`, `content` ";
		$from = " FROM `job` ";
		$where = " WHERE `active` = 1 ";
		if($options['id']) {
			$where .= " AND `id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		
		if($options['hot']) {
			$where .= " AND `hot` = {$this->_db->quote($options['hot'], 'INTEGER')} ";
		}
		
		$order = " ORDER BY `order` DESC ";
		if($options['order'] && $options['by']) {
			$order = " ORDER BY `{$options['order']}` {$options['by']} ";
		}
		
		$limit = "";
		if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
			$limit = " LIMIT {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} ";
		}
		if($options['id']) {//Select by id
			$result = $this->_db->fetchRow( $sql . $from . $where . $order);
		} else {
			$result = $this->_db->fetchAll( $sql . $from . $where . $order . $limit);
		}
		
		return $result;
	}
}