<?php
class Cms_LibraryController extends Zend_Controller_Action {
    public function init() {
        $this->view->thuVien = 'current';
        $this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
        		$this->view->serverUrl() . $canonical);
    }
    
	public function indexAction() {
	}
	
	public function topBookAction() {
		$limit = intval(Utils_Global::$params['limit']);
		if($limit <= 0) {
			$limit = 9;
		}
		
		$books = array();
		$bookModel = Cms_Model_Book::factory();
		$books = $bookModel->getTopBooks(0, $limit);
		$this->view->books = $books;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/book/';
	}
	
	public function bestSaleAction() {
		$limit = intval(Utils_Global::$params['limit']);
		if($limit <= 0) {
			$limit = 9;
		}
		
		$books = array();
		$bookModel = Cms_Model_Book::factory();
		$books = $bookModel->getHotBooks(0, $limit);
		$this->view->books = $books;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/book/';
	}
	
	public function detailAction() {
	    $id = Utils_Global::$params['book_id'];
	    $bookModel = Cms_Model_Book::factory();
	    $book = $bookModel->getBookDetail($id);
	    $this->view->book = $book;
	    $this->view->staticUrl = Utils_Global::get('staticUrl') . '/book/';
	}
}
