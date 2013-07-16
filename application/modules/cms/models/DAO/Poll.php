<?php
class Cms_Model_DAO_Poll
{    
	private $_db;
	private static $_instance = null;
 
	/**
	 * @return Cms_Model_DAO_Poll
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
     * Get list of poll choice by given options
     * @param array $options
     * @return array
     * */
	public function getPoll($options=array()) {
		$sql = "SELECT `poll`.`id`, `poll_choice`.`id` as `choice_id`, `question`, `content`, `vote`, `poll`.`dateline` ";
		$from = " FROM `poll` INNER JOIN `poll_choice` ON `poll`.`id` = `poll_choice`.`poll_id` ";
		$where = " WHERE `active` = 1 ";
		if($options['id']) {
			$where .= " AND `poll`.`id` = {$this->_db->quote($options['id'], 'INTEGER')} ";
		}
		$order = " ORDER BY `order` ";
		
		$result = $this->_db->fetchAll( $sql . $from . $where . $order);
		return $result;
	}
	
	/**
	 * Add vote for give choice(s)
	 * @param integer|array $choiceIds
	 * @return true on success, false on failure
	 * */
	public function voteChoice($choiceIds) {
		$result = false;
		if(is_array($choiceIds) && $choiceIds) {
			$choiceIds = implode(',', $choiceIds);
		} else {
			$choiceIds = intval($choiceIds);
		}
		
		$sql = "UPDATE `poll_choice` SET `vote` = `vote` + 1 ";
		$where = " WHERE `id` IN ({$choiceIds}) ";
		try {
			$this->_db->beginTransaction();
			$result = $this->_db->query($sql.$where);
			$this->_db->commit();
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
			$this->_db->rollBack();
		}
		
		return $result;
	}
}