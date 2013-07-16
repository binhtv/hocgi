<?php
class Admin_Model_DAO_Banner
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_Banner
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
     * Insert banner
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insert($data) {
       $this->_db->insert('banner', $data);
       $result = $this->_db->lastInsertId('banner', 'id');
       return $result;
   }
   
   public function insertPositionBanner($position, $banner) {
       $this->_db->insert('position_banner', array('pos_id' => $position, 'banner_id' => $banner));
       $result = $this->_db->lastInsertId('position_banner', 'id');
       return $result;
   }
   
   /**
    * Update banner by given id and data
    * @param integer $id
    * @param array $data
    * @return true on success, false on failure
    * */
   public function update($id, $data) {
       return $this->_db->update('banner', $data, "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
    * Delete banner by given id
    * @param integer $id
    * @return true on success, false on failure
    * */
   public function delete($id) {
//        return $this->_db->delete('banner', "id = {$this->_db->quote($id, 'INTEGER')}");
   }
   
   /**
     * Get list of banners by given options
     * @param array $options
     * @return array
     * */
	public function getBanners($options = array()) {
		$sql = "SELECT `id`, `name`, `image`, `link`, `last_update`, `dateline`, `active`, `width`, `height`, `video`,`note`,`hash_folder` ";
		$from = " FROM `banner` ";
		$where = " WHERE 1 = 1 ";
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
	 * Get list of banner for specific position
	 * @param array $options
	 * @return array
	 * */
	public function getBannerByPosition($options = array()) {
	    $sql = "SELECT `banner`.`id`, `name`, `image`, `link`, `last_update`, `dateline`, `active`, `width`, `height`, `video`,`note`,`hash_folder` ";
	    $from = " FROM `banner` INNER JOIN `position_banner` ON `banner`.`id` = `position_banner`.`banner_id` ";
	    $where = " WHERE 1 = 1 ";
	    if($options['positionId']) {
	    	$where .= " AND `position_banner`.`pos_id` = {$this->_db->quote($options['positionId'], 'INTEGER')} ";
	    }
	    
	    $order = " ORDER BY `banner`.`id` DESC ";
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
	 * Get article count by given options
	 * @param array $options
	 * @return integer
	 * */
	public function getArticleCount($options = array()) {
		$sql = "SELECT count(*) as `count` ";
		$from = " FROM `article` ";
		$where = " WHERE 1 = 1 ";
		$result = $this->_db->fetchOne($sql . $from . $where);
		return $result;
	}
}