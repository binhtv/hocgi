<?php

class Admin_AuthController extends Zend_Controller_Action 
{
    public function init()
	{
		// do something
	}
	
	public function indexAction() {
	    $hasIdentity = Zend_Auth::getInstance()->hasIdentity();
	    if($hasIdentity) {
	        $this->_redirect('/admin');
	    }
	}
	
    public function loginAction()
	{		
        $request 	= $this->getRequest();
        $registry 	= Zend_Registry::getInstance();
    	$auth		= Zend_Auth::getInstance(); 
		
    	$db = Utils_Global::getDbInstance('admin');    		
    	$authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('cms_users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');    
    	// Set the input credential values
    	$uname = $request->getParam('username');
    	$paswd = $request->getParam('password');		
    	if(!$uname || !$paswd) {
    	    $this->_redirect('/admin/auth');
    	}
        $authAdapter->setIdentity($uname);
        $authAdapter->setCredential(md5($paswd));		
        // Perform the authentication query, saving the result
        $result = $auth->authenticate($authAdapter);		
        if($result->isValid()) {			
    	  $data = $authAdapter->getResultRowObject(null, 'password');
    	  $auth->getStorage()->write($data);		  
    	  $this->_redirect('/admin');
    	}
		else {
    	  $this->_redirect('/admin/auth');
    	}
    }
     
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
	    $auth->clearIdentity();
	    $this->_redirect('/admin/auth');
    }
}
?>