<?php
class Admin_Model_Article
{    
    private static $_instance;
    private static $_table = "article";
    
    private function __construct() {
    }
    
    /**
     * @return Admin_Model_Article
     * */
    public static function factory() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    /**
     * Insert article
     * @param array $data
     * @return 1 on success, 0 on failure
     * */
    public function insert($data) {
        if(!$this->isValidArticle($data)) {
            return 0;
        }
        $daoArticle = Admin_Model_DAO_Article::factory();
        try {
            $result = $daoArticle->insert($data);
            if($result) {//Log
                $data['id'] = $result;
                Utils_Global::storeBackendLog('insert', self::$_table, $result, $data);
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $result;
    }
    
    /**
     * Update article
     * @param integer $id
     * @param array $data
     * @return true on success, false on failure
    */
    public function update($id, $data) {
        $result = 0;
        if(!$this->isValidArticle($data, true)) {
            return 0;
        }
        
        $daoArticle = Admin_Model_DAO_Article::factory();
        try {
        	$result = $daoArticle->update($id, $data);
        	if($result) {//Log
        		$data['id'] = $id;
        		Utils_Global::storeBackendLog('update', self::$_table, $id, $data);
        	}
        } catch (Exception $exc) {
        	prBinh($exc);
        	Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        return $result;
    }
    
    /**
     * Delete article by given $id
     * @param integer $id
     * @return true on success, false on failure
     * */
    public function delete($id) {
        $result = 0;
        if(!$id) {
            return 0;
        }
        
        $daoArticle = Admin_Model_Article::factory();
        try {
            $result = $daoArticle->delete($id);
            if($result) {//Log
            	Utils_Global::storeBackendLog('delete', self::$_table, $id, array('id' => $id));
            }
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);  
        }
        
        return $result;
    }
    
    /**
     * Get articles by given condition
     * @param array $options
     * @return array
     * */
    public function getArticles($options = array()) {
        $articles = array();
        $daoArticle = Admin_Model_DAO_Article::factory();
        try {
            $articles = $daoArticle->getArticles($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $articles;
    }
    
    /**
     * Get article count by given Id
     * @param array $options
     * @return integer
     * */
    public function getArticlesCount($options = array()) {
        $count = 0;
        $daoArticle = Admin_Model_DAO_Article::factory();
        try {
            $count = $daoArticle->getArticleCount($options);
        } catch (Exception $exc) {
            prBinh($exc);
            Utils_Global::storeLog($exc, __FILE__, __LINE__);
        }
        
        return $count;
    }
    
    private function isValidArticle($data, $isUpdate=false) {
        if(!$data['title'] ||
            !$data['title_seo'] ||
             !$data['short_description']||
            !$data['content'] ||
            !$data['category'] ||
            ($isUpdate?false:!$data['hash_folder'])||
            ($isUpdate?false:!$data['image'])) {
            return false;
        }
        
        return true;
    }
}