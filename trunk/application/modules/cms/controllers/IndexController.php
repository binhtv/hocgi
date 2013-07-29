<?php
class Cms_IndexController extends Zend_Controller_Action {
	private $_id;
	public function indexAction() {
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
									$this->view->serverUrl() . $canonical);
	}
	
	public function detailAction() {
		$articleId = $this->_request->getParam('id');
		if(!$articleId) {
			header("Location: " . $this->view->serverUrl() . '/cms/404.htm', true, 301);
            exit();
		}		
		$articleModel = Cms_Model_Article::factory();
		$article = $articleModel->getArticleById($articleId);
		Utils_Global::set('article', $article);
		if(!$article) {
			header("Location: " . $this->view->serverUrl() . '/cms/404.htm', true, 301);
		}
		
		$categoryModel = Cms_Model_Category::factory();
		$category = $categoryModel->getCategory($article['category']);
		
		$this->view->article = $article;
		$this->view->category = $category;
		$this->view->articleId = $article['id'];
		
		$metaInfo = array('article-title' => $article['title'],
		                    'article-description' => $article['short_description'],
		);
		$this->view->metadata = $this->view->metadata('detail-article', $metaInfo,
				$this->view->serverUrl() . $this->view->url(array()));
	}
	
	public function relatedArticleAction() {
		$article = array();
		$articles = array();
		//Get for zend registry first
		try {
			$article = Utils_Global::get('article');
		} catch (Exception $e) {
			Utils_Global::storeLog($e, __FILE__, __LINE__);	
		}
		$articleModel = Cms_Model_Article::factory();
		if(!$article) {
			$id = Utils_Global::$params[id];
			$article = $articleModel->getArticleById($id);
		}
		
		if($article) {
			$category = $article['category'];
			$articles = $articleModel->getRelatedArticle($category, 0, 3);
		}
		
		$this->view->articles = $articles;
		$this->view->staticUrl =  Utils_Global::get('staticUrl') . '/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
	}
	
	public function otherArticleAction() {
		$articles = array();
		
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getNewestArticle(0, 10);
		
		$this->view->articles = $articles;
		$this->view->staticUrl =  Utils_Global::get('staticUrl') . '/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
	}
	
	public function topNewBoxAction() {
		$top = $this->_request->getParam('top', 1);
		$category = $this->_request->getParam('category');
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 4);
		
		$articleModel = Cms_Model_Article::factory();
		if(!$category) {
			$articles = $articleModel->getArticlesTopNew($top, $offset, $limit);
		} else {
		    $categoryModel = Cms_Model_Category::factory();
		    $childCategories = $categoryModel->getCategoriesByOptions(array('parent' => $category));
		    if(is_array($childCategories) && count($childCategories) > 0) {
		    	$cids = array();
		    	foreach ($childCategories as $cat) {
		    		$cids[] = $cat['id'];
		    	}
		    	$cids[] = $category;
		    	$articles = $articleModel->getArticleTopByCategories($top, implode(',', $cids), $offset, $limit);
		    } else {
		    	$articles = $articleModel->getArticleTopByCategory($top, $category, $offset, $limit);
		    }
		}
		
		$this->view->articles = $articles;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		
		if($top == 2) {//Box left top
			$this->render('top-new-left');			
		}
	}
	
	public function boxNewWithCategoryAction() {
		$mainCategory = $this->_request->getParam('category');
		$limit = intval($this->_request->getParam('limit'));
		if($limit <= 0) {
		    $limit = 6;
		}

		$categoryModel = Cms_Model_Category::factory();
		$categories = $categoryModel->getCategoriesByOptions(array('parent' => $mainCategory));
		$mainCategoryInfo = $categoryModel->getCategory($mainCategory);
		//Get article by category
		$articleModel = Cms_Model_Article::factory();
		if(is_array($categories) && count($categories) > 0) {
			$cids = array();
			foreach ($categories as $cat) {
				$cids[] = $cat['id'];
			}
			$cids[] = $mainCategory;
			$news = $articleModel->getArticleByCategories(implode(',', $cids), 0, $limit);
		} else {
    		$news = $articleModel->getArticleByCategory($mainCategory, 0, $limit);
		}
		
		$this->view->categories = $categories;
		$this->view->mainCategory = $mainCategoryInfo;
		$this->view->articles = $news;
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/article/';
	}
	
	public function boxChuyenVienTuVanAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 6);
		$category = $this->_request->getParam('category', 21);
		
		$articleModel = Cms_Model_Article::factory();
		$speakers = $articleModel->getArticleByCategory($category, $offset, $limit);
		$this->view->speakers = $speakers;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/article/';
	}
	
	public function boxNewestCourseAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 6);
		
		$courseModel = Cms_Model_Course::factory();
		$courses = $courseModel->getCourses($offset, $limit);
		$this->view->courses = $courses;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
	}
	
	public function mostViewAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 5);
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getArticlesMostView($offset, $limit);
		
		$this->view->articles = $articles;
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		
	}
	
	public function promotionCourseAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 5);
		$courseModel = Cms_Model_Course::factory();
		$courses = $courseModel->getCourses($offset, $limit, array('promotion' => 1));
		$bestCourses = $courseModel->getCourses($offset, $limit, array('best' => 1));
		$this->view->courses = $courses;
		$This->view->bestCourse = $bestCourses;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
	}
	
	public function hotJobBoxAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 7);
		$category = 17;
		
		$articleModel = Cms_Model_Article::factory();
		$jobs = $articleModel->getArticleByCategory($category, $offset, $limit);
		$this->view->jobs = $jobs;
	}
	
	public function boxFunArticleAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 4);
		$category = $this->_request->getParam('category');
		
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getArticleByCategory($category, $offset, $limit);
		
		$this->view->articles = $articles;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
	}
	
	public function boxMidBottomAction() {
		$category = $this->_request->getParam('category', 8);
		$this->view->category = $category;
	}
	
	public function questionAnswerBoxAction() {
		$offset = $this->_request->getParam('offset', 0);
		$limit = $this->_request->getParam('limit', 1);
		$category = 9;//Hỏi đáp
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getArticleByCategory($category, $offset, $limit);
		
		$this->view->articles = $articles;
		$this->view->staticUrl = Utils_Global::get('staticUrl').'/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
	}
	
	public function addViewAction() {
	    $articleId = Utils_Global::$params['article_id'];
	    $articleModel = Cms_Model_Article::factory();
	    $result = $articleModel->addViewCount($articleId);
	    $this->_helper->json($result);
	}
	
	public function testAction() {
		// 		prBinh(serialize(array('categories' => '3,4,5,6','category' => 7)));
		// 		$cateogryModel = Cms_Model_Category::factory();
		// 		prBinh($cateogryModel->getCategories(array(2,1)));
		// 		$articleModel = Cms_Model_Article::factory();
		// 		prBinh($articleModel->getArticleByCategory(2));
		// 		prBinh(serialize(array('position' => json_encode(array(1,2)))));
		// 		prBinh(serialize(array('position' =>12)));
		prBinh(Utils_Global::hashName('323232424242424242432dfwer32r23ed2332'));
	}
}
