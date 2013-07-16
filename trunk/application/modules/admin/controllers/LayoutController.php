<?php
/**  
* Admin_LayoutController
* @author: tunm
*/ 
class Admin_LayoutController extends Zend_Controller_Action 
{
	protected $_layoutModel;
	public function init() {
	    $this->view->layoutclzz = 'active';
	}
	/**
	 * listAction
	 * Description: show layout list
	 */
	 public function listAction()
	 {
		$model = $this->_getModel();
		$result = $model->getLayouts();	
		$page = (int)($this->_request->getParam('page'));   
		if(count($result) > 0)
		{ 
			Utils_Global::doPaging($result, $page, $this->view);
		}
				
		$this->view->page = $page;
	 }
	
	/**
	 * method: new/edit layout
	 * Description: create new or edit a layout
	 */
	 public function editAction()
	 {
		$model = $this->_getModel();
		$layoutName = $this->_request->getParam('layout');   
		$this->view->objLayout = $model->getLayout($layoutName);
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
			$arrData['layout_name'] = $this->_request->getPost('layout_name');
			$arrData['folder_name'] = $this->_request->getPost('folder_name');
			$arrData['title'] 		= $this->_request->getPost('title');
			$arrData['sections'] 	= $this->_request->getPost('sections');
			$arrData['thumb']	 	= $this->_request->getPost('thumb');
			$id = $this->_request->getPost('id');
			$model->updateLayout($arrData, $id);
			$this->_redirect('/admin/layout/list');
		}
		else
			$this->_redirect('/admin/layout/list');
	 }
	 
	 /**
	 * method: deleteAction
	 * Description: delete a layout
	 */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteLayout($this->_request->getParam('layout'));
		$this->_redirect('/admin/layout/list');
	 }
	 /**
	 * Get model layout object	
	 */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Layout.php';
		if ($this->_layoutModel == null) 
		{                        
            $this->_layoutModel = new Layout();
        }
        return $this->_layoutModel;                
    }
}