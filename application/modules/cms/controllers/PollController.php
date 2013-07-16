<?php
class Cms_PollController extends Zend_Controller_Action {
	public function indexAction() {
		$pollId = $this->_request->getParam('poll_id', 0);
		$pollBannerPosition = $this->_request->getParam('position', 0);
		$pollModel = Cms_Model_Poll::factory();
		$poll = $pollModel->getPoll($pollId);
		
		$this->view->poll = $poll;
		$this->view->position = $pollBannerPosition;
	}
	
	public function voteAction() {
		$ids = trim(Utils_Global::$params['choices']);
		if(!$ids) {
			$this->_helper->json(-1);	
		}
		$ids = json_decode($ids, true);
		$pollModel = Cms_Model_Poll::factory();
		$result = $pollModel->voteChoice($ids);
		if($result) {
			$this->_helper->json(1);
		} else {
			$this->_helper->json(0);
		}
	}
	
	public function pollResultAction() {
		$pollId = Utils_Global::$params['id'];
		$pollModel = Cms_Model_Poll::factory();
		$poll = $pollModel->getPoll($pollId);
		$this->view->poll = $poll;
		$this->_helper->layout()->disableLayout();
	}
}
