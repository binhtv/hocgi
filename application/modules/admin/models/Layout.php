<?php

class Layout
{
	/**
	 * get all layout from db
	 */
	public function getLayouts()
	{
		// get db object
		$layouts_business = BlockManagement_Layouts::getInstance();
		$result = $layouts_business->getList();
				
		return $result;
	}
	
	/**
	 * get layout by layout_name
	 * @return layout object
	 */
	public function getLayout($layoutName = '')
	{
		$layouts_business = BlockManagement_Layouts::getInstance();
		$result = $layouts_business->getLayout($layoutName);
		
		if(isset($result))		
		{
			$result = $this->parseArrayToObject($result);
			return $result;
		}
		else
		{
			$objLayout = new stdClass();
			$objLayout->layout_name = '';
			$objLayout->folder_name = '';
			$objLayout->title = '';
			$objLayout->sections = '';
			$objLayout->thumb = '';
			return $objLayout;
		}		
	}
	
	/**
	 * update layout information
	 */
	public function updateLayout($arrData, $id)
	{
		if($id == '')
		{
			// insert to db
			$layouts_business = BlockManagement_Layouts::getInstance();
			$layouts_business->insertLayout($arrData);
		}
		else
		{
			// update to db
			$layouts_business = BlockManagement_Layouts::getInstance();
			$layouts_business->updateLayout($id, $arrData);
		}		
	}
	
	/**
	 * delete a layout
	 */
	public function deleteLayout($layoutName)
	{ 
		$layouts_business = BlockManagement_Layouts::getInstance();
		$layouts_business->deleteLayout($layoutName);		
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