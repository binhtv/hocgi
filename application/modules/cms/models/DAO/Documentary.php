<?php
class Cms_Model_DAO_Documentary
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Documentary
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
     * Get list of documentary by given options
     * @param array $options
     * @return array
     * */
	public function getDocumentary($options = array()) {
		$sql = "SELECT `id`, `name`, `name_seo`, `author`, `image`, `content`, `hash_folder`, 
						`short_description`, `content`, `view_count`, `download_count`, `dateline`, 
		                `file_type`,`file_name`,`hash_download_folder`,`file_size` ";
		$from = " FROM `documentary` ";
		$where = " WHERE active = 1 ";
		
		if($options['new']) {
			$where .= " AND `new` = {$this->_db->quote($options['hot'], 'INTEGER')} ";
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
	 * Get documentary count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getDocumentaryCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `documentary` ";
		$where = " WHERE active = 1 ";
		if($options['new']) {
			$where .= " AND `new` = {$this->_db->quote($options['new'], 'INTEGER')} ";
		}

		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
	
	/**
	 * Update view count by given document id
	 * @param integer $id
	 * @return true on success, false on failure
	 * */
	public function addViewCount($id) {
	    $sql = "UPDATE `documentary` SET `view_count` = `view_count` + 1 ";
	    $where = " WHERE `id` = {$this->_db->quote($id, 'INTEGER')} ";
	    $result = $this->_db->query($sql . $where);
	    return $result->rowCount();;
	}
	/**
	 * Update download count by given document id
	 * @param integer $id
	 * @return true on success, false on failure
	 * */
	public function addDownloadCount($id) {
	    $sql = "UPDATE `documentary` SET `download_count` = `download_count` + 1 ";
	    $where = " WHERE `id` = {$this->_db->quote($id, 'INTEGER')} ";
	    $result = $this->_db->query($sql . $where);
	    return $result->rowCount();
	}
}