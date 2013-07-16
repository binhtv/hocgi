<?php
class Cms_SearchController extends Zend_Controller_Action {
    public function init() {
        $this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
        		$this->view->serverUrl() . $canonical);
    }
    
	public function indexAction() {
		$keySearch = Utils_Global::$params['keySearch'];
		$page = intval(Utils_Global::$params['page']);
		$limit = intval(Utils_Global::$params['limi']);
		if($page < 1) {
			$page = 1;
		}
		if($limit <= 0){
			$limit = Utils_Global::getConfig('cms', 'site', 'coursePerPage');
		}

		$courseModel = Cms_Model_Course::factory();
		$courses = $courseModel->searchFullText($keySearch, ($page - 1) * $limit, $limit);
		$this->view->courses = $courses;
		$this->view->page = $page;
		$this->view->totalItem = $courseModel->getCountFullText($keySearch);
		$this->view->numRowPerPage = $limit;;
		$this->view->keySearch = $keySearch;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		if(Utils_Global::get('isAjax')) {
		    $this->_helper->layout()->disableLayout();
		}
	}
}
