<?php
class Cms_BannerController extends Zend_Controller_Action {
	public function indexAction() {
		$position = $this->_request->getParam('position');
		
		$bannerModel = Cms_Model_Banner::factory();
		$banner = $bannerModel->getBanner($position);
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
		if($position == 1) {
			$this->view->banner = $banner;
			$this->render('top-new-banner');
		} else if($position == 3) {
			$this->view->banner = $banner;
			$this->render('mid-banner');
		} else if($position == 4) {
			$this->view->banner = $banner;
			$this->render('mid-right');
		}
	}
	
	public function clipHayAction() {
		$position = $this->_request->getParam('position');
		
		$bannerModel = Cms_Model_Banner::factory();
		$banner = $bannerModel->getBanner($position);
		if(is_array($banner)) {
			$this->view->banner = $banner[0];
		}
	}
	
	public function group3Action() {
	    $position = $this->_request->getParam('position');
	    $bannerModel = Cms_Model_Banner::factory();
	    $banner = $bannerModel->getBanner($position);
	    $this->view->banner = $banner;
	    $this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
	}
	
	public function simpleAction() {
		$position = $this->_request->getParam('position', 0);
		
		$bannerModel = Cms_Model_Banner::factory();
		$banner = $bannerModel->getBanner($position);
		if(is_array($banner)) {
			$banner = $banner[0];
			$this->view->banner = $banner;
		}
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
	}
	
	public function extendedAction() {
	    $position = $this->_request->getParam('position');
	    $bannerModel = Cms_Model_Banner::factory();
	    $banner = $bannerModel->getBanner($position);
	    $this->view->banner = $banner;
	    $this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
	}
	
	public function sponsorBoxAction() {
		$position = $this->_request->getParam('position', 0);
		
		$bannerModel = Cms_Model_Banner::factory();
		$banners = $bannerModel->getBanner($position);
		$this->view->banners = $banners;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
	}
	
	public function doiTacLienKetAction() {
		$position = $this->_request->getParam('position', 0);
		
		$bannerModel = Cms_Model_Banner::factory();
		$banners = $bannerModel->getBanner($position);
		$this->view->banners = $banners;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
	}
	
	public function bannerRightHomePageAction() {
		$position = $this->_request->getParam('position', 12);
		
		$bannerModel = Cms_Model_Banner::factory();
		$banners = $bannerModel->getBanner($position);
		$this->view->banners = $banners;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
	}
}
