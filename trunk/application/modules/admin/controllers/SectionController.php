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
			$this->view->userName = $identity->username;			
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