<?php
/**  
* Admin_BoxController
* Description: define boxcontroller class for list, insert, update, delete box for the layout
* @author: tunm
*/ 
class Admin_BoxController extends Zend_Controller_Action 
{
	protected $_boxModel;
	
	public function init() {
	    $this->view->boxclzz = 'active';
	}
	/**
	 * listAction
	 * Description: show boxies list of box
	 */
	 public function listAction()
	 {
		$model = $this->_getModel();
			
		$page = (int)($this->_request->getParam('page'));  
	 	$arrData = array();
		if($this->_request->isPost()){
			$arrData['name'] 		= $this->_request->getPost('name');
			$arrData['content'] 	= $this->_request->getPost('content');
		} else
		{
			$cookie = $this->_request->getCookie('edit');
			if($cookie == "true")
			{
				$name = $this->_request->getCookie('name');
				$content = $this->_request->getCookie('content');
				
				$page = (int)$this->_request->getCookie('page');
				
				$arrData['name']	= $name;
				$arrData['content']	= $content;	

				setcookie('name', '', time() -1000, '/admin/box');
				setcookie('content', '', time() -1000, '/admin/box');	
				setcookie('edit', '', time() -1000, '/admin/box');	
				setcookie('page', '', time() -1000, '/admin/box');
			}
		}
		$result = $model->getBoxsSearch($arrData);
		if(count($result) > 0)
		{ 
			Utils_Global::doPaging($result, $page, $this->view);
		}
				
		$this->view->page = $page;
		$this->view->postData = $arrData;
		//var_dump($result);
		//var_dump($arrData);
	 }
	
	/**
	 * method: new/edit layout
	 * Description: create new or edit a box
	 */
	 public function editAction()
	 {
		$model = $this->_getModel();
		$boxId = $this->_request->getParam('boxid');
		$this->view->objBox	= $model->getBox($boxId);
	 }
	 
	 /**
	 * method: update layout information
	 * Description: update layout
	 */
	 public function saveAction()
	 {
		$model = $this->_getModel();
		// check if form is posted
		if($this->_request->isPost())
		{
			$arrData = array();			
			$arrData['boxname'] 	= $this->_request->getPost('boxname');
			$arrData['content'] 	= $this->_request->getPost('content');			
			$id = $this->_request->getPost('id');
			$model->updateBox($arrData, $id);
			$this->_redirect('/admin/box/list');
		}
		else
			$this->_redirect('/admin/box/list');
	 }
	 
	 /**
	 * method: deleteAction
	 * Description: delete a box
	 */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteBox($this->_request->getParam('boxid'));
		$this->_redirect('/admin/box/list');
	 }
	 /**
	 * Get model box object	
	 */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Box.php';
		if ($this->_boxModel == null) 
		{                        
            $this->_boxModel = new Box();
        }
        return $this->_boxModel;                
    }
}