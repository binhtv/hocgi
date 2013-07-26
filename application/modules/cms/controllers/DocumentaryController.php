<?php
class Cms_DocumentaryController extends Zend_Controller_Action {
    public function init() {
        $this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
        		$this->view->serverUrl() . $canonical);
    }
    
	public function indexAction() {
		$documentary = array();
		$page = intval($this->_request->getParam('page'));
		$limit = intval($this->_request->getParam('limit'));
		$type = intval($this->_request->getParam('type'));//Type 1: latest document, Type 2: most download
		if($page < 1) {
			$page = 1;
		}
		if($limit <= 0){
			$limit = Utils_Global::getConfig('cms', 'site', 'documentaryPerPage');
		}
		
		$documentaryModel = Cms_Model_Documentary::factory();
		if($type == 1) {
			$documentary = $documentaryModel->getLatestDocumentary(($page - 1) * $limit, $limit);
		} else if($type == 2) {
			$documentary = $documentaryModel->getMostDownloadDocumentary(($page - 1) * $limit, $limit);
		}
		$this->view->documentary = $documentary;
		$this->view->page = $page;
		$this->view->totalItem = $documentaryModel->getDocumentaryCount();
		$this->view->numRowPerPage = $limit;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/documentary/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		$this->view->type = $type;
		if(Utils_Global::get('isAjax')) {
			$this->_helper->layout()->disableLayout();
			$this->render('content');
		} else {
			$this->view->content = $this->view->render('documentary/content.phtml');
		}
	}
	
	public function detailAction() {
	    $id = intval(Utils_Global::$params['document_id']);
	    $documentModel = Cms_Model_Documentary::factory();
	    $document = $documentModel->getDocumentById($id);
	    $this->view->document = $document;
	    $this->view->documentId = $id;
	    $this->view->downloadUrl = Utils_Global::getConfig('cms', 'site', 'downloadDocumentaryUrl');
	}
	
	public function addViewAction() {
		$documentId = Utils_Global::$params['document_id'];
		$documentModel = Cms_Model_Documentary::factory();
		$result = $documentModel->addViewCount($documentId);
		$this->_helper->json($result);
	}
	
	public function addDownloadAction() {
		$documentId = Utils_Global::$params['document_id'];
		$documentModel = Cms_Model_Documentary::factory();
		$result = $documentModel->addDownloadCount($documentId);
		$this->_helper->json($result);
	}
}
