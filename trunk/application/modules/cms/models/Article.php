<?php
class Cms_Model_Article
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Vmg_Model_Manga
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get list of Article show on top new box
	 * @param integer $top 1, 2
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function getArticlesTopNew($top=1, $offset = 0, $limit = 4) {
		$options = array('offset' => $offset, 'limit' => $limit,'top' => $top);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_' . $options['offset'] . '_' . $options['limit'];
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		 
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
		
		return $result;
	}
	
	/**
	 * Get list of article by category
	 * @param integer $top 1,2
	 * @param integer $category
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getArticleTopByCategory($top, $category, $offset=0, $limit=4) {
		$options = array('offset' => $offset, 'limit' => $limit,'top' => $top, 'category' => $category);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_' . $top . '_' . $category . '_' . $options['offset'] . '_' . $options['limit'];
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
			
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
		
		return $result;
	}
	
	/**
	 * Get list of article by category
	 * @param integer $top 1,2
	 * @param integer $categories
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getArticleTopByCategories($top, $categories, $offset=0, $limit=4) {
		$options = array('offset' => $offset, 'limit' => $limit,'top' => $top, 'categories' => $categories);
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_' . $top . '_' . $categories . '_' . $options['offset'] . '_' . $options['limit'];
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
			
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
	
		return $result;
	}
	
	/**
	 * Get list of most view article
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function getArticlesMostView($offset = 0, $limit = 5) {
		$options = array('offset' => $offset, 'limit' => $limit,'order' => 'view_count', 'by' => 'DESC');
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select_most_view . '_' . $options['offset'] . '_' . $options['limit'];
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
			
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if(is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			prBinh($exc);
			Utils_Global::storeLog($exc, __FILE__, __LINE__ );
		}
	
		return $result;
	}
	
	/**
	 * Get article by given category
	 * @param integer $category
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	//@FIXME: add cache if any
	public function getArticleByCategory($category, $offset = 0, $limit = 6) {
		$result = array();
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_category_' . $category . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$options = array('category' => $category, 'offset' => $offset, 'limit' => $limit);
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * Get article by given category
	 * @param integer $categories
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	//@FIXME: add cache if any
	public function getArticleByCategories($categories, $offset = 0, $limit = 6) {
		$result = array();
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_categories_' . $categories . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
	
		$options = array('categories' => $categories, 'offset' => $offset, 'limit' => $limit);
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
	
		return $result;
	}
	
	/**
	 * Get most view article by given category
	 * @param integer $category
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getMostViewArticle($category, $offset, $limit) {
		$result = array();
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_category_' . $category . '_most_view_' . $offset.$limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$options = array('category' => $category, 'offset' => $offset, 'limit' => $limit, 'order' => 'view_count', 'by' => 'DESC');
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * Get article count for given category
	 * @param integer $category
	 * @return integer
	 * */
	public function getArticleCount($category) {
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_count . '_category_' . $category;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$options = array('category' => $category);
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticleCount($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * Get article by given category and order by id
	 * @param integer $category
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getRelatedArticle($category, $offset = 0, $limit = 3) {
		$result = array();
		if(!$category) {
			return $result;
		}
		
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_' . $id;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$options = array('category' => $category, 'offset' => $offset, 'limit' => $limit, 'order' => 'id', 'by' => 'DESC');
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * Get newest article
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 * */
	public function getNewestArticle($offset = 0, $limit = 10) {
		$result = array();
	
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_newest_' . $offset . $limit;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		$options = array('offset' => $offset, 'limit' => $limit, 'order' => 'id', 'by' => 'DESC');
		$articleDao = Cms_Model_DAO_Article::factory();
		try {
			$result = $articleDao->getArticles($options);
			if($result && is_object($cache)) {
				$cache->setCache($key, $result);
			}
		} catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * Get article by given id
	 * @param integer $id
	 * @return array
	 * */
	public function getArticleById($id) {
		$result = array();
		if(!$id) {
			return $result;
		}
		
		//Get from cache first
		$cache = Utils_Global::getCacheInstance('cms');
		$keyConfig = Utils_Global::getConfig('cache', 'keys', 'keys');
		$key = $keyConfig->article_select . '_' . $id;
		if(is_object($cache)) {
			$result = $cache->getCache($key);
			if($result) {
				return $result;
			}
		}
		
		$articleDao = Cms_Model_DAO_Article::factory();
		$options = array('id' => $id);
		try {
			$result = $articleDao->getArticles($options);
			if($result && is_object($cache)) {
				$cache->$cache->setCache($key, $result);
			}
		}catch (Exception $exc) {
			Utils_Global::storeLog($exc, __FILE__, __LINE__);
		}
		return $result;
	}
	
	/**
	 * Update article by given data
	 * @param integer $id
	 * @param array $data
	 * @return 1 on success, 0 on failure
	 * */
	public function update($id, $data) {
	    if(!$id || !$data) {
	        return 0;;
	    }
	    $articleDao = Cms_Model_DAO_Article::factory();
	    try {
	        $reuslt = $articleDao->update($id, $data);
	    } catch (Exception $exc) {
	        prBinh($exc);
	        Utils_Global::storeLog($exc, __FILE__, __LINE__);
	    }
	    
	    return $result;
	}
	
	/**
	 * +1 view count by given id
	 * @param integer $id
	 * @return 0 on success, 1 on failure
	 * */
	public function addViewCount($id) {
	    if(!$id) {
	        return 0;
	    }
	    $articleDao = Cms_Model_DAO_Article::factory();
	    try {
	        $result = $articleDao->addViewCount($id);
	    } catch (Exception $e) {
	        prBinh($e);
	        Utils_Global::storeLog($e, __FILE__, __LINE__);
	    }
	    
	    return $result;
	}
}