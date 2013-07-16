<?php
/**
 *  box model class
 *  @author Huytq2
 *
 */
class Box
{
	/**
	 * get all box from db
	 */
	public function getBoxes()
	{
		$_box = BlockManagement_Boxes::getInstance();
		return $_box->getList();		
	}
	public function getBoxsSearch(array $options = null)
	{
		$_box = BlockManagement_Boxes::getInstance();
		$result = $_box->getListSearch($options);
				
		return $result;
	}
	/**
	 * get box by boxid
	 * @return box object
	 */
	public function getBox($boxId = '')
	{
		$_box = BlockManagement_Boxes::getInstance();
		$result = $_box->getBox($boxId);
		if(isset($result))
		{
			$result = $this->parseArrayToObject($result);
			return $result;
		}
		else
		{
			$objBox 		= new stdClass();
			$objBox->boxid 	= '';
			$objBox->boxname = '';
			$objBox->content = '';			
			return $objBox;
		}		
	}
	
	/**
	 * update box information
	 */
	public function updateBox($arrData, $id)
	{
		$_box = BlockManagement_Boxes::getInstance();		
		
		if($id == '')
		{
			$_box->insertBox($arrData);
		}
		else
		{
			$_box->updateBox($id,$arrData);											  
		}
	}
	
	/**
	 * delete a box
	 */
	public function deleteBox($boxid)
	{
		$_box = BlockManagement_Boxes::getInstance();
		$_box->deleteBox($boxid);	
	}
//////////////////////// private functions //////////////////////////
			
	private function parseArrayToObject($array) {
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