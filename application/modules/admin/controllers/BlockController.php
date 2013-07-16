<?php
/**  
* Admin_BlockController
* Description: define blockcontroller class for list, insert, update, delete block for the layout
* @author: tunm
*/ 
class Admin_BlockController extends Zend_Controller_Action 
{
	protected $_blockModel;
	protected $_appModel;
	protected $_viewModel;
	protected $_extviewModel;
	protected $_appboxModel;
	protected $_boxModel;
	protected $_layoutModel;
	
	public function init()
	{
		$this->view->blockclzz = 'active';
	}
	
	public function indexAction()
	{
		
	}
	
	/**
	 * listAction
	 * Description: show block list of layout
	 */
	 public function listAction()
	 {
		$this->view->layout = '0';	
		$page = (int)($this->_request->getParam('page'));
		$arrData = array();
			
		if($this->_request->getParam('layout') != ''){
			$this->view->layout 		= $this->_request->getParam('layout');
			$arrData['layout'] 			= $this->_request->getParam('layout');
			$arrData['layout_name'] 	= $this->_request->getParam('layout_name');
			$arrData['description'] 	= $this->_request->getParam('description');
		}
		if($this->_request->isPost()){
			$this->view->layout 		= $this->_request->getPost('layout');
			$arrData['layout'] 			= $this->_request->getPost('layout');
			$arrData['layout_name'] 	= $this->_request->getPost('layout_name');
			$arrData['description'] 	= $this->_request->getPost('description');
		}
		else
		{
			$cookie = $this->_request->getCookie('edit');
			if($cookie == "true")
			{
				$clayout_name = $this->_request->getCookie('layout_name');
				$clayout = $this->_request->getCookie('layout');
				$cdescription = $this->_request->getCookie('description');
				$page = (int)$this->_request->getCookie('page');
				
				$arrData['layout'] 			= $clayout;
				$arrData['layout_name'] 	= $clayout_name;
				$arrData['description'] 	= $cdescription;
									
				setcookie('layout', '', time() -1000, '/admin/block');
				setcookie('layout_name', '', time() -1000, '/admin/block');
				setcookie('description', '', time() -1000, '/admin/block');	
				setcookie('edit', '', time() -1000, '/admin/block');	
				setcookie('page', '', time() -1000, '/admin/block');					
			}
		}
		$model = $this->_getModel();
		//$result = $model->getBlocks($this->view->layout);
		$result = $model->getBlocksSearch($arrData);
		if(count($result) > 0)
		{ 
			Utils_Global::doPaging($result, $page, $this->view);
		}
				
		$this->view->page = $page;
		// get layouts
		$layoutModel = $this->_getLayoutModel();
		$this->view->layouts = $layoutModel->getLayouts();
		$this->view->postData = $arrData;	

		//echo "AAA" . count($result);
		//var_dump($arrData);
		//var_dump($result);
	 }
	
	/**
	 * method: new/edit layout
	 * Description: create new or edit a layout
	 */
	 public function editAction()
	 {
		$model 		= $this->_getModel();
		$blockId 	= $this->_request->getParam('blockid');   
		$layout 	= $this->_request->getParam('layout');   
		$viewModel 	= $this->_getViewModel();
		$viewResult = $viewModel->getViews();
		$layoutModel = $this->_getLayoutModel();
		$layoutResult = $layoutModel->getLayouts(); 
		// array module
		$config = Utils_Global::getConfig('admin', 'site');
		$this->view->arrStatus  = $config->status->toArray();		
		$this->view->arrModule 	= $config->module->toArray();
		$this->view->arrLayout  = $this->resultToArray($layoutResult, 'layout_name', 'layout_name');
		// get block object for edit
		$this->view->objBlock 	= $model->getBlock($blockId);
		
		if($layout != '' && $layout != '0' && $this->view->objBlock->layout == ''){
			$this->view->objBlock->layout = $layout;
		}
		// check module
		if($this->view->objBlock->module == 'view' || $this->view->objBlock->module == ''){
			$this->view->arrView   	= $this->resultToArray($viewResult, 'viewid', 'viewname');
		}elseif($this->view->objBlock->module == 'extview'){
			$extviewModel = $this->_getExtViewModel();
			$viewResult = $extviewModel->getViews();
			$this->view->arrView   	= $this->resultToArray($viewResult, 'extviewid', 'extviewname');
		}
		elseif($this->view->objBlock->module == 'box'){
			$boxModel 	= $this->_getBoxModel();
			$boxResult = $boxModel->getBoxes();
			$this->view->arrView = $this->resultToArray($boxResult, 'boxid', 'boxname');	
		}elseif($this->view->objBlock->module == 'app'){
			$appModel = $this->_getAppModel();
			$appResult = $appModel->getApps();
			$this->view->arrView = $this->resultToArray($appResult, 'app_id', 'app_name');	
		}elseif($this->view->objBlock->module == 'appbox'){
			$appboxModel = $this->_getAppBoxModel();
			$appboxresult = $appboxModel->getList();						
			$this->view->arrView = $this->resultToArray($appboxresult, 'appboxid', 'app_name');	
		}
	
		$arrTmp = array();
		$sections = '';
		if($this->view->objBlock->layout != ''){
			// in edit mode
			// get sections from layout
			foreach($layoutResult as $key => $value){
				if($value['layout_name'] == $this->view->objBlock->layout){
					$sections = $value['sections'];
				}
			}		
			$arrTmp = explode(',', $sections);
		}else
			$arrTmp = explode(',', $layoutResult[0]['sections']);
		
		$arrSection = array();
		if(count($arrTmp) > 0){
			foreach($arrTmp as $key => $value){
				$arrSection[$value] = $value;
			}
		}
		
		$this->view->arrSection = $arrSection;		
	 }
	 
	/**
	 * convert array result resource to array 
	 */
	public function resultToArray($result, $idName, $valueName)
	{
		$arrTmp = array();
		foreach($result as $key => $value)
		{
			$arrTmp[$value[$idName]] = $value[$valueName];
		}
		return $arrTmp;
	}
	
	/**
	 * method: ajaxChangeModuleAction
	 */
	public function getModuleByTypeAction()
	{
		$this->_helper->layout->disableLayout();
		
		// get param from geturl
		$type 	= $this->_request->getParam('type');		
		$this->_helper->viewRenderer->setRender('seldelta');	
		if($type == 'box'){
			$boxModel 	= $this->_getBoxModel();
			$viewResult = $boxModel->getBoxes();
			$this->view->arrView = $this->resultToArray($viewResult, 'boxid', 'boxname');		
		}
		elseif($type == 'extview'){
			$viewModel 	= $this->_getExtViewModel();
			$viewResult = $viewModel->getViews();
			$this->view->arrView = $this->resultToArray($viewResult, 'extviewid', 'extviewname');		
		}
		elseif($type == 'view'){
			$viewModel 	= $this->_getViewModel();
			$viewResult = $viewModel->getViews();
			$this->view->arrView = $this->resultToArray($viewResult, 'viewid', 'viewname');		
		}
		elseif($type == 'appbox')
		{
			$appboxModel = $this->_getAppBoxModel();
			$appboxresult = $appboxModel->getList();						
			$this->view->arrView = $this->resultToArray($appboxresult, 'appboxid', 'app_name');
		}
		else{
			// type = app
			$appModel = $this->_getAppModel();
			$appResult = $appModel->getApps();
			$this->view->arrView = $this->resultToArray($appResult, 'app_id', 'app_name');		
		}
		
	}
	
	/**
	 * method: getSectionByLayout
	 */
	public function getSectionByLayoutAction()
	{				
		$this->_helper->layout->disableLayout();		
		// get param from geturl
		$layout = $this->_request->getParam('layout');		
		$this->_helper->viewRenderer->setRender('selsection');	
		$layoutModel = $this->_getLayoutModel();
		$layoutResult = $layoutModel->getLayout($layout);
		$arrTmp = explode(',', $layoutResult->sections);
		$arrSection = array();
		foreach($arrTmp as $key => $value){
			$arrSection[$value] = $value;
		}
		$this->view->arrSection = $arrSection;
	}
	
	 /**
	 * method: saveAction
	 * Description: update block information
	 */
	 public function saveAction()
	 {
		$model = $this->_getModel();
		// check if form is posted
		if($this->_request->isPost())
		{
			$arrData = array();
			$arrData['module'] 		= $this->_request->getPost('module');
			$arrData['description'] = $this->_request->getPost('description');
			$arrData['delta'] 		= $this->_request->getPost('delta');
			$arrData['weight'] 		= $this->_request->getPost('weight');
			$arrData['section'] 	= $this->_request->getPost('section');
			$arrData['layout'] 		= $this->_request->getPost('layout');
			$arrData['status']	 	= $this->_request->getPost('status');
			$layout = $this->_request->getPost('layout');
			$id = $this->_request->getPost('id');
			$model->updateBlock($arrData, $id);
			$this->_redirect('/admin/block/list/layout/' . $layout);
		}
		else
			$this->_redirect('/admin/block/list');
	 }
	 
	 /**
	  * method: deleteAction
	  * Description: delete a layout
	  */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteBlock($this->_request->getParam('blockid'));
		$this->_redirect('/admin/block/list');
	 }
	 
	/**
	 * Get model block object	
	 */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Block.php';
		if ($this->_blockModel == null) 
		{                        
            $this->_blockModel = new Block();
        }
        return $this->_blockModel;                
    }
    
	/**
	 * Get extview model object	
	 */
	protected function _getExtViewModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/ExtView.php';
		if ($this->_extviewModel == null) 
		{                        
            $this->_extviewModel = new ExtView();
        }
        return $this->_extviewModel;                
    }
	
	/**
	 * Get view model object	
	 */
	protected function _getViewModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/View.php';
		if ($this->_viewModel == null) 
		{                        
            $this->_viewModel = new View();
        }
        return $this->_viewModel;                
    }
	
	/**
	 * Get box model object	
	 */
	protected function _getBoxModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Box.php';
		if ($this->_boxModel == null) 
		{                        
            $this->_boxModel = new Box();
        }
        return $this->_boxModel;                
    }
    
	protected function _getAppBoxModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/AppBox.php';
		if ($this->_appboxModel == null) 
		{                        
            $this->_appboxModel = new AppBox();
        }
        return $this->_appboxModel;                
    }
	
	/**
	 * Get app model object	
	 */
	protected function _getAppModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/App.php';
		if ($this->_appModel == null) 
		{                        
            $this->_appModel = new App();
        }
        return $this->_appModel;                
    }
	
	/**
	 * Get layout model object	
	 */
	protected function _getLayoutModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Layout.php';
		if ($this->_layoutModel == null) 
		{                        
            $this->_layoutModel = new Layout();
        }
        return $this->_layoutModel;                
    }
}