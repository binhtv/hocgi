<?php
class Cms_CategoryController extends Zend_Controller_Action {
	
	public function indexAction() {
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
									$this->view->serverUrl() . $canonical);
		$category = Utils_Global::$params['id'];
		$this->view->category = $category;
		if($category == 2) {
			$this->view->huongNghiep = 'current';
		} elseif($category == 15) {
		    $this->view->tuyenSinh = 'current';
		} elseif($category == 17) {
		    $this->view->viecLam = 'current';
		}
	}
	
	public function listArticleAction() {
		$category = Utils_Global::$params['id'];
		$page = intval(Utils_Global::$params['page']);
		$limit = intval(Utils_Global::$params['limit']);
		if($page < 1) {
			$page = 1;
		}
		if($limit <= 0){
			$limit = Utils_Global::getConfig('cms', 'site', 'articlePerPage');
		}
		
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getArticleByCategory($category, ($page - 1) * $limit, $limit);
		
		$this->view->articles = $articles;
		$this->view->page = $page;
		$this->view->totalItem = $articleModel->getArticleCount($category);
		$this->view->numRowPerPage = $limit;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		$this->view->category = $category;
		if(Utils_Global::get('isAjax')) {
			$this->_helper->layout()->disableLayout();
		}
	}
	
	public function funnyArticleAction() {
		$category = Utils_Global::$params['id'];
		$page = intval(Utils_Global::$params['page']);
		$limit = intval(Utils_Global::$params['limit']);
		if($page < 1) {
			$page = 1;
		}
		if($limit <= 0){
			$limit = Utils_Global::getConfig('cms', 'site', 'articlePerPage');
		}
		
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getArticleByCategory($category, ($page - 1) * $limit, $limit);
		$this->view->articles = $articles;
		$this->view->page = $page;
		$this->view->totalItem = $articleModel->getArticleCount($category);
		$this->view->numRowPerPage = $limit;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/article/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		$this->view->category = $category;
		$this->view->cuoi = 'current';
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
				$this->view->serverUrl() . $canonical);
		if(Utils_Global::get('isAjax')) {
			$this->_helper->layout()->disableLayout();
		}
		
		$this->render("list-article");
	}
	
	public function mostViewFunnyArticleAction() {
		$category = intval(Utils_Global::$params['id']);
		$limit = intval(Utils_Global::$params['limit']);
		
		if($limit <= 0) {
			$limit = 15;
		}
		
		$articleModel = Cms_Model_Article::factory();
		$articles = $articleModel->getMostViewArticle($category, 0, $limit);
		$this->view->articles = $articles;
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		$this->view->category = $category;
	}
}
