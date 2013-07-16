<?php
class Utils_Db_Connection_Pdo_Mysql extends Utils_Db_Connection_Abstract
{
	protected function _connect($config) {
		$db = Zend_Db::factory($this->_adapter, $config);
		$db->setFetchMode(Zend_Db::FETCH_OBJ);
		$db->query("SET CHARACTER SET 'utf8'");
		return $db;
	}

	public function getVersion()
	{
		$conn = $this->getSlaveConnection();
		$row  = $conn->query('SELECT VERSION() AS ver')->fetch();
		return 'MySQL v' . $row->ver;
	}
	
	public function query($sql)
	{
		$conn = $this->getMasterConnection();
		$conn->query($sql);
	}
}
?>