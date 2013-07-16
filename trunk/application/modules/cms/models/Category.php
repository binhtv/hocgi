<?php
class Cms_Model_Category
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_Category
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get list of category by given ids
	 * @param array $ids
	 * @return array
	 */
	//@FIXME: save cache if cache is enabled
	public function getCategories($ids = array()) {
		if(!$ids) {
			return array();
		}
		
		$ids = implode(',', $ids);
		$options = array('ids' => $ids);
		$categoryDao = Cms_Model_DAO_Category::factory();
		$categories = $categoryDao->getCategories($options);
		return $categories;
	}
	
	public function getCategoriesByOptions($options = array()) {
	    $categoryDao = Cms_Model_DAO_Category::factory();
	    $categories = $categoryDao->getCategories($options);
	    return $categories;
	}
	/**
	 * Get a category by given id
	 * @param integer $id
	 * @return array
	 * */
	public function getCategory($id) {
		if(!$id) {
			return array();
		}
		
		$options = array('id' => $id);
		$categoryDao = Cms_Model_DAO_Category::factory();
		$category = $categoryDao->getCategories($options);
		
		return $category;
	}
}