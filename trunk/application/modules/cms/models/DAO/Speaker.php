<?php
class Cms_Model_DAO_Speaker
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Speaker
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
     * Get list of speaker by given options
     * @param array $options
     * @return array
     * */
	public function getSpeaker($options = array()) {
		$sql = "SELECT `id`, `name`, `name_seo`, `image`, `hash_folder` ";
		$from = " FROM `speaker` ";
		$where = " WHERE active = 1 ";
		
		$order = " ORDER BY `order` DESC ";
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
}