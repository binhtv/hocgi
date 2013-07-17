<?php

class Admin_UsersController extends Zend_Controller_Action 
{
	private $_user_business = null;

	public function init()
	{
		// do something
		Plugin_BlockManagement_BlockManager::setLayout('admin_layout');
	}
	
	public function indexAction()
	{
	}
	
	public function changePassSaveAction()
	{
		$old_password = $this->_request->getParam('old_password');
		$new_password = $this->_request->getParam('new_password');
		$cfm_new_password = $this->_request->getParam('cfm_new_password');
		$userid = $this->_request->getParam('id');
		
		$auth = Zend_Auth::getInstance();  
		$identity = $auth->getIdentity();
		$username = $identity->username;

				
		$_user = Business_Common_Users::getInstance();
		if($username == "admin" && $userid != null)
		{			
			if($new_password != $cfm_new_password)
			{
				$this->view->message = "New password and Confirm New password not the same";
				$this->changePassAction();
				$this->_helper->viewRenderer->setRender('change-pass');
				return;
			}			
			$data = array();
			$data['password'] = md5($new_password);
			$_user->updateUser($userid,$data);
			$this->view->ok_message = "Password changed.";
			
			$this->listAction();
			$this->_helper->viewRenderer->setRender('list');
			return;
		}
		else
		{
			$old_password = md5($old_password);
			
			$user = $_user->getUser($username);
						
			if($user != null && $user['password'] == $old_password)
			{
				$id = $user['userid'];
				if($new_password != $cfm_new_password)
				{
					$this->view->message = "New password and Confirm New password not the same";
					$this->changePassAction();
					$this->_helper->viewRenderer->setRender('change-pass');
					return;
				}			
				$data = array();
				$data['password'] = md5($new_password);
				$_user->updateUser($id,$data);
				$this->view->ok_message = "Password changed.";
				$this->_forward("list", null, null,array("mes" => "Password changed."));
				return;
			}
			else
			{
				$this->view->message = "Old password not correct";
				$this->changePassAction();
				$this->_helper->viewRenderer->setRender('change-pass');
				return;
			}
		}
		
		
	}
	
	public function changePassAction()
	{
		
		$auth = Zend_Auth::getInstance();  
		$identity = $auth->getIdentity();
		$username = $identity->username;
		$userid = $this->_request->getParam('id');
		$_user = Business_Common_Users::getInstance();
		
		if($username == "admin" && $userid != null)
		{
			$user = $_user->getUserByUid($userid);			
			$this->view->is_admin = true;	
			$this->view->data = $user;
		}
		else
		{
			$user = $_user->getUser($username);
			
			$this->view->is_admin = false;			
			$this->view->data = $user;
		}
		
		$this->view->form_action = $this->view->serverUrl() . '/' . "admin/users/change-pass-save?id=" . $userid;
		$this->view->cancel_btn = $this->view->serverUrl() . '/' . "admin/users/list";
		
	}
	
	public function addnewAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$username = $this->_request->getParam('username');
		$password = $this->_request->getParam('password');
		$status = $this->_request->getParam('status');
		$roles = $this->_request->getParam('roles');
		
		$_user = Business_Common_Users::getInstance();
		
		//check username first
		$user = $_user->getUser($username);
		$data = array();
		$data['username'] = $username;
		$data['password'] = md5($password);
		$data['status'] = $status;
		$data['role_id'] = $roles;
		
		if($user != null)
		{
			$this->view->message = "This username '$username' existed. Choose another one.";
			$this->view->data = $data;
			return;
		}	
	
		$userid = $_user->addUser($data);
		
		$this->_redirect($this->view->serverUrl() . '/' . "admin/users/list");
		
	}
	
	public function addAction()
	{
		try{
			$model = new Admin_Model_Role();		
			$this->view->roles = $model->getRole();
			$this->view->form_action = $this->view->serverUrl() . '/' . "admin/users/addnew";
			$this->view->cancel_btn = $this->view->serverUrl() . '/' . "admin/users/list";
		}
		catch(Exception $e){
			pr($e->getMessage());
		}
	}
	
	public function editAction()
	{
		try{
			$model = new Admin_Model_Role();	
			
			$id = $this->_request->getParam('id');	
			$_user = Business_Common_Users::getInstance();			
			$user = $_user->getUserByUid($id);
			$this->view->roles = $model->getRole();
			$this->view->data = $user[0];
			$this->view->form_action = $this->view->serverUrl() . '/' . "admin/users/save";
			$this->view->cancel_btn = $this->view->serverUrl() . '/' . "admin/users/list";
		}
		catch(Exception $e){
			pr($e->getMessage());
		}
	}
	
	public function saveAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_request->getParam('id');
		
		$password = $this->_request->getParam('password');
		$status = $this->_request->getParam('status');
		$roles = $this->_request->getParam('roles');
		
		$data = array();
		$data['status'] = $status;
		$data['role_id'] = $roles;
		
		if($password != null && $password != '')
		{					
			$data['password'] = md5($password);		
		}
		
		$_user = Business_Common_Users::getInstance();
		$_user->updateUser($id, $data);
		
		//get roles
		$this->_redirect($this->view->serverUrl() . '/' . "admin/users/list");
		
	}
	
	public function listAction()
	{
				
		$_users = $this->getUserBusiness();		
		
		$list = $_users->getList();		
				
		$title = array("Username", "Status", "Action");
		$fields = array(	
				array("type" => "title", "data" => "username"),
				array("type" => "title", "data" => "status"),							
				array("type" => "link", "data" => array(
															array(	"title" => "Edit", 
																	"field" => "userid", 
																	"link" => $this->view->serverUrl() . '/' . "admin/users/edit/id/%s"
																),
															array(	"title" => "Change password", 
																	"field" => "userid", 
																	"link" => $this->view->serverUrl() . '/' . "admin/users/change-pass/id/%s"
																),
															array(
																	"title" => "Delete", 
																	"field" => "userid", 
																	"link" => $this->view->serverUrl() . '/' . "admin/users/delete/id/%s"
																)															
														)
					)
		);
		
		$listing = new Utils_Listing($title, $fields, $list,true);	
		
		$content = $listing->renderList();
		
		$this->view->mes = $this->_request->getParam("mes","");
		$this->view->content = $content;
		$this->view->user = $user;
		$this->view->create_url = $this->view->serverUrl() . '/' . "admin/users/add";
			
		
	}
	
    public function deleteAction()
    {
		try{
			$id = (int)$this->getRequest()->getParam('id',0);				
			$_user = Business_Common_Users::getInstance();
			if(!empty($id)){
				$_user->deleteUser($id);
			}
			$this->_redirect('admin/users/list');	
				
		}catch(Exception $e){
			pr($e->getMessage());
		}
    }
	
	
	
	////////// private functions ////////////
	
	/**
	 * Get bussiness instance of Business_Common_Users
	 *
	 * @return Business_Common_Users
	 */
	private function getUserBusiness()
	{
		if($this->_user_business == null)
		{
			$this->_user_business = new Business_Common_Users();			
		}
		return $this->_user_business;		
	}
    
}