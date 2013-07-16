<?php
class Cms_Model_DAO_Article
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Article
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
     * Get list of articles by given options
     * @param array $options
     * @return array
     * */
	public function getArticles($options = array()) {
		$sql = "SELECT `id`, `title`, `title_seo`, `category`, `short_description`,
						`content`, `image`, `hash_folder`, `hot` ";
		$from = " FROM `article` ";
		$where = " WHERE active = 1 ";
		if($options['top']) {
			$where .= " AND `top` = {$this->_db->quote($options['top'], 'INTEGER')} ";
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
		
		$order = " ORDER BY `order` DESC ";
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
		if($options['categories']) {
			$where .= " AND category IN ({$options['categories']}) ";
		}
		
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
	
	/**
	 * Update article with data by given id
	 * @param integer $id
	 * @param array $data
	 * @return 1 on success, 0 on failure
	 * */
	public function update($id, $data) {
	    return $this->_db->update('article', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
	}
	
	/**
	 * +1 view count for given id
	 * @param integer $id
	 * @return 1 on success, 0 on failure
	 * */
	public function addViewCount($id) {
	    $sql = "UPDATE `article` SET `view_count` = `view_count` + 1 ";
	    $where = " WHERE `id` = {$this->_db->quote($id, 'INTEGER')} ";
	    $result = $this->_db->query($sql . $where);
	    return $result;
	}
}