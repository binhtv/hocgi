<?php

abstract class Business_Abstract
{
			 
	private function getDbConnection()
	{
		$db = Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	public function adaptSQL($input)
	{
		$input = str_replace("'","''",$input);
		return $input;
	}
	
	public function parseArrayToObject($array) {
		$object = new stdClass();
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $name=>$value) {
				$name = strtolower(trim($name));
				if (!empty($name)) {
					$object->$name = $value;
				}
			}
		}
		return $object;
	}
}

?>