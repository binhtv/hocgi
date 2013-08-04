<?php
class Admin_Model_DAO_ImageUpload
{    
    private $_db;
    private static $_instance = null;
    
    /**
     * @return Admin_Model_DAO_ImageUpload
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
     * Insert new upload
     * @param array $data
     * @return integer last insert id on success or 0 on failure
     * */
   public function insertNewUpload($data) {
       $this->_db->insert('user_upload', $data);
       $result = $this->_db->lastInsertId('user_upload', 'id');
       return $result;
   }
   
   /**
    * Get image upload by given options
    * @param array $options
    * @return array
    * */
   public function getImageUploads($options) {
       $sql = "SELECT *  ";
       $from = " FROM `user_upload` ";
       $where = " WHERE 1 = 1 ";
       if($options['title']) {
           $where .= " AND `title` LIKE '%{$options['title']}%' ";    
       }
       if($options['category']) {
           $where .= " AND `category` = {$this->_db->quote($options['category'])} ";
       }
       if($options['username']) {
           $where .= " AND `username` = {$this->_db->quote($options['username'])} ";    
       }
       if($options['usernames']) {
           $where .= " AND `username` IN ({$options['usernames']}) ";
       }
       
       $order = " ORDER BY `user_upload`.`id` DESC ";
       if($options['order'] && $options['by']) {
       	    $order = " ORDER BY `user_upload`.`{$options['order']}` {$options['by']} ";
       }
       
       $limit = "";
       if(isset($options['offset']) && $options['offset'] >=0 && $options['limit'] >0) {
       	$limit = " limit {$this->_db->quote($options['offset'], 'INTEGER')}, {$this->_db->quote($options['limit'], 'INTEGER')} ";
       }
       $result = $this->_db->fetchAll ( $sql . $from . $where . $order . $limit );
       return $result;
   }
   
   /**
    * Get image upload by given options
    * @param array $options
    * @return integer
    * */
   public function getImageUploadsCount($options) {
       $sql = "SELECT count(*) as `count`  ";
       $from = " FROM `user_upload` ";
       $where = " WHERE 1 = 1 ";
       if($options['title']) {
       	$where .= " AND `title` = '%{$options['title']}%' ";
       }
       if($options['category']) {
       	$where .= " AND `category` = {$this->_db->quote($options['category'])} ";
       }
       if($options['username']) {
       	$where .= " AND `username` = {$this->_db->quote($options['username'])} ";
       }
       if($options['usernames']) {
       	$where .= " AND `username` IN ({$options['usernames']}) ";
       }
        
   	    $result = $this->_db->fetchOne ( $sql . $from . $where);
       return $result;
   }
}