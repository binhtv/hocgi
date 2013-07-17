<?php

class Admin_RolesController extends Zend_Controller_Action 
{
	private $_permission_business = null;

	public function init()
	{
		// do something
		Plugin_BlockManagement_BlockManager::setLayout('admin_layout');
	}
	
	public function indexAction()
	{
		
	}
	
	public function deletecfmAction()
	{
		$this->_helper->viewRenderer->setRender('confirm');
		$id = $this->_request->getParam("id");

		$model = new Admin_Model_Role();	
		$role = $model->getRole(array('id'=>$id));
		
		$this->view->message = "Are you want to delete role name '" . $role[0]["name"] . "'";
		$this->view->yes_link = $this->view->serverUrl() . '/' . "admin/roles/delete/id/" . $id;
		$this->view->no_link = $this->view->serverUrl() . '/' . "admin/roles/list";
	}
	
	public function deleteAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_request->getParam("id");

		$model = new Admin_Model_Role();	
		$role = $model->deleteRole($id);
		
		$mPermission = new Admin_Model_Permission();	
		$role = $mPermission->deletePermissionByRoleId($id);

		$url = $this->view->serverUrl() . '/' . "admin/roles/list";
		$this->_redirect($url);

	}
	
	public function editAction()
	{
		$model = new Admin_Model_Role();	
		$id = $this->_request->getParam('id');		
		
		if($id != null && $id != "")
		{
			$role = $model->getRole(array('id'=>$id));	
			$this->view->data = $role[0];
		}
		
		$this->view->form_action = $this->view->serverUrl() . '/' . "admin/roles/save/";
		$this->view->cancel_btn = $this->view->serverUrl() . '/' . "admin/roles/list";
		
		$this->view->edited = true;
		
	}
	
	public  function saveAction()
	{				
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_request->getParam('id');
		$rolename = $this->_request->getParam('rolename');
		
		//check role name
		$model = new Admin_Model_Role();		
			
		
		$data = array();
		$data['name'] = $rolename;		
		
		if($id == null || $id == '')
		{
			$model->insertRole($data);
		}
		else
		{
			$model->updateRole($data,$id);
		}
		
		$url = $this->view->serverUrl() . '/'. "admin/roles/list";
		$this->_redirect($url);
	}
	
	public function listAction()
	{
		$model = new Admin_Model_Role();		
		$list = $model->getRole();
		
		$title = array("Name", "Operation", "Edit Permission");
		$fields = array(	
				array("type" => "title", "data" => "name"),
				array("type" => "link", "data" => array(
															array(	"title" => "edit role", 
																	"field" => "id", 
																	"link" => $this->view->serverUrl() . '/' . "admin/roles/edit/id/%s"
																),
															array(	"title" => "delete role", 
																	"field" => "id", 
																	"link" => $this->view->serverUrl() . '/' . "admin/roles/deletecfm/id/%s"
																)
														),
					),							
				array("type" => "link", "data" => array(
															array(	"title" => "edit permission", 
																	"field" => "id", 
																	"link" => $this->view->serverUrl() . '/' . "admin/permissions/list/id/%s"
																)															
														),
					)
				
		);
		
		$listing = new Utils_Listing($title, $fields, $list,true);	
		
		$content = $listing->renderList();
		
		$this->view->content = $content;
		$this->view->user = $user;
		$this->view->action_url = $this->view->serverUrl() . '/' . "admin/roles/save";
		
		
	}
	
	////////// private functions ////////////
	
	/**
	 * Get bussiness instance of Business_Common_Permissions
	 *
	 * @return Business_Common_Permissions
	 */
	private function getPermissionBusiness()
	{
		if($this->_permission_business == null)
		{
			$this->_permission_business = new Business_Common_Permissions();			
		}
		return $this->_permission_business;		
	}
	
    /**
     * Get model box object	
     */
    protected function &_getModel($className)
    {
		if(isset($this->_models[$className]))
		{
			if(is_object($this->_models[$className]))
			{
				return $this->_models[$className];
			}
		}
		
        require APPLICATION_PATH . "/modules/admin/models/{$className}.php";
        $this->_models[$className] = new $className();        
        return $this->_models[$className];
    }
}