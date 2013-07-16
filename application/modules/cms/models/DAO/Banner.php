<?php
class Cms_Model_DAO_Banner
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Banner
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
	public function getBanner($options=array()) {
		$sql = "SELECT `image`, `link`, `name`, `note`,
						`width`, `height`, `hash_folder`,`video` ";
		$from = " FROM `banner` INNER JOIN `position_banner` ON `banner`.`id` = `position_banner`.`banner_id` ";
		$where = " WHERE active = 1 ";
		
		if($options['position']) {
			$where .= " AND `position_banner`.`pos_id` = {$this->_db->quote($options['position'], 'INTEGER')} ";
			$result = $this->_db->fetchAll( $sql . $from . $where);
		}
		return $result;
	}
}