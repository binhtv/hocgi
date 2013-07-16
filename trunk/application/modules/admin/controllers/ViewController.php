<?php
/**  
* Admin_ViewController
* Description: define viewcontroller class for list, insert, update, delete view for the layout
* @author: tunm
*/ 
class Admin_ViewController extends Zend_Controller_Action 
{
	protected $_viewModel;
	
	public function init()
	{
		$this->view->viewclzz = 'active';
	}
	
	public function indexAction()
	{
		
	}
	
	/**
	 * listAction
	 * Description: show views list of layout
	 */
	 public function listAction()
	 {
		$model = $this->_getModel();
		
		$page = (int)($this->_request->getParam('page'));
		$arrData = array();
		if($this->_request->isPost()){
			$arrData['name'] 	= $this->_request->getPost('name');
		}
		else
		{
			$cookie = $this->_request->getCookie('edit');
			if($cookie == "true")
			{
				$name = $this->_request->getCookie('name');
				$page = (int)$this->_request->getCookie('page');
				$arrData['name']	= $name;	
				setcookie('edit', '', time() -1000, '/admin/view');
				setcookie('name', '', time() -1000, '/admin/view');		
				setcookie('page', '', time() -1000, '/admin/view');			
			}
		}
		$result = $model->getViewsSearch($arrData);	
		//var_dump($result);
		//exit();
		if(count($result) > 0)
		{ 
			Utils_Global::doPaging($result, $page, $this->view);
		}
		
		$this->view->page = $page;
		$this->view->postData = $arrData;
	 }
	
	/**
	 * method: new/edit view
	 * Description: create new or edit a view
	 */
	 public function editAction()
	 {
		$model = $this->_getModel();
		$viewId = $this->_request->getParam('viewid');   
		$this->view->objView = $model->getView($viewId);
	 }
	 
	 /**
	 * method: update view information
	 * Description: update view
	 */
	 public function saveAction()
	 {
		$model = $this->_getModel();
		//$url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
		//echo $url;
		//exit();
		// check if form is posted
		if($this->_request->isPost())
		{
			$arrData = array();
			$arrData['viewname'] 		= $this->_request->getPost('viewname');
			$arrData['module'] 			= $this->_request->getPost('module');
			$arrData['controller'] 		= $this->_request->getPost('controller');
			$arrData['action'] 			= $this->_request->getPost('action');
			$arrData['key']	 			= $this->_request->getPost('key');
			$arrData['value']	 		= $this->_request->getPost('value');
			// get array params
			$arrTmp = array();
			$n = count($arrData['key']);
			for($i = 0; $i < $n; $i++){
				$arrTmp[$arrData['key'][$i]] = $arrData['value'][$i];
			}
			unset($arrData['key']);
			unset($arrData['value']);
			$arrData['params'] = serialize($arrTmp);
			$id = $this->_request->getPost('id');
			$model->updateView($arrData, $id);
			$this->_redirect('/admin/view/list');
		}
		else
			$this->_redirect('/admin/view/list');
	 }
	 
	 /**
	 * method: deleteAction
	 * Description: delete a view
	 */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteView($this->_request->getParam('viewid'));
		$this->_redirect('/admin/view/list');
	 }
	 /**
	 * Get model layout object	
	 */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/View.php';
		if ($this->_viewModel == null) 
		{                        
            $this->_viewModel = new View();
        }
        return $this->_viewModel;                
    }
}