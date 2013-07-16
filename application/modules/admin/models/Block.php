<?php

class Block
{
	protected  static $_instance = null;
	
	/**
	 * get all block from db
	 */
	public function getBlocks($layout = '')
	{		
		$block = $this->getBlockBuiness();			
		if($layout != '' && $layout != '0')
			$result = $block->getList($layout);
		else 
			$result = $block->getList();	
		return $result;		
	}
	
	public function getBlocksSearch(array $options = null)
	{		
		$block = $this->getBlockBuiness();	
		$result = $block->getListSearch($options);	
		//var_dump($block->getList());	
		/*
		if(!empty($options)){
			if(!empty($options['layout'])){
				$result = $block->getList($options['layout']);
			}
			else
			{
				$result = $block->getList();
			}
			if(!empty($options['layout_name'])){
				foreach($result as $key => $item){ 
					if(strpos(strtolower($item['layout']),strtolower($options['layout_name'])) === false)
					{
						unset($result[$key]);
					}
				}
			}if(!empty($options['description'])){
				foreach($result as $key => $item){ 
					if(strpos(strtolower($item['description']),strtolower($options['description'])) === false)
					{
						unset($result[$key]);
					}
				}
			}			
		}	
		else
		{
			$result = $block->getList();
		}*/

		
		return $result;		
	}
	
	/**
	 * get block by blockid
	 * @return block object
	 */
	public function getBlock($blockId = '')
	{
		$block = $this->getBlockBuiness();		
		$result = $block->getBlock($blockId);
		if($result != null)
		{
			$result = $this->parseArrayToObject($result);
			return $result;
		}		
		else
		{
			$objBlock = new stdClass();
			$objBlock->description = '';
			$objBlock->blockid 	= '';
			$objBlock->module 	= '';
			$objBlock->delta 	= '';
			$objBlock->weight 	= '0';
			$objBlock->section 	= '';
			$objBlock->layout 	= '';
			$objBlock->status 	= '';
			return $objBlock;
		}		
	}
	
	/**
	 * update layout information
	 */
	public function updateBlock($arrData, $id)
	{ 		
		if($id == '')
		{			
			$data = array(
				"module" => $arrData['module'],
				"description" => $arrData['description'],
				"delta" => $arrData['delta'],
				"weight" => $arrData['weight'],
				"section" => $arrData['section'],
				"layout" => $arrData['layout'],
				"status" => $arrData['status']											 
			);		
			
			$block = $this->getBlockBuiness();
			$block->insertBlock($data);			
		}
		else
		{
			$block = $this->getBlockBuiness();
			$data = array(
				"module" => $arrData['module'],
				"description" => $arrData['description'],
				"delta" => $arrData['delta'],
				"weight" => $arrData['weight'],
				"section" => $arrData['section'],
				"layout" => $arrData['layout'],
				"status" => $arrData['status']											 
			);		
			$block->updateBlock($id,$data);			
		}
	}
	
	/**
	 * delete a layout
	 */
	public function deleteBlock($blockId)
	{ 
		$block = $this->getBlockBuiness();
		$block->deleteBlock($blockId);		
	}
	
	//////////////////////// private functions //////////////////////////
	
	private function getBlockBuiness()
	{
		if(self::$_instance == null)
		{
			self::$_instance = BlockManagement_Blocks::getInstance();
		}
		return self::$_instance;
	}
	
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