<?php
/**
 * Description Admin_SectionController
 * @author huytq2
 */
class Admin_SectionController extends Zend_Controller_Action
{
	public function headerAction()
	{
		$auth = Zend_Auth::getInstance();  
		$identity = $auth->getIdentity();		
		if($auth->hasIdentity())
		{ 			
			$this->view->msgLogin = "Hello <strong>{$identity->username}</strong> <a href=\"/admin/product/clearcache/1\"><font color='red'>[Clear Cache]</font></a> <a href=\"/admin/users/change-pass/id/".$identity->userid."\">[Change password]</a> <a href=\"/admin/auth/logout\">[Logout]</a>";			
		}
		else
		{ 		
			$this->view->msgLogin = "&nbsp;";		
		}		
	}
	
	public function loginAction()
	{
		//show login form from view->login.phtml
	}	
	
	public function menuAction()
	{
		//show menu for admin		
	}
	
	public function testAction()
	{		
		//$this->view->abc = $this->_request->getParam("111");
		
		
	}
	
}