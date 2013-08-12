<?php
class Cms_Model_DAO_User
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_User
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
     * Get list of user by given options
     * @param array $options
     * @return array
     * */
	public function getUser($options = array()) {
		$sql = "SELECT `id`, `username`, `password`, `email`, `fullname`, `active` ";
		$from = " FROM `user` ";
		$where = " WHERE 1 = 1 ";
		if($options['active']) {
    		$where .= " WHERE active = 1 ";
		}
		if($options['username'] && $options['email']) {
    		$where .= " AND (`username` = {$this->_db->quote($options['username'])} OR 
    		            `email` = {$this->_db->quote($options['email'])}) ";
		} else {
		    if($options['username']) {
		    	$where .= " AND `username` = {$this->_db->quote($options['username'])} ";
		    }
		    if($options['email']) {
		    	$where .= " AND `email` = {$this->_db->quote($options['email'])} ";
		    }
		}
		$result = $this->_db->fetchRow( $sql . $from . $where );
		return $result;
	}
	
	/**
	 * Insert new user
	 * @param array $data user information
	 * @return array
	 * */
	public function insert($data) {
	    $this->_db->insert('user', $data);
	    $result = $this->_db->lastInsertId();
	    return $result;
	}
}