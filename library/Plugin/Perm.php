<?php
class Plugin_Perm extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
 	   $module = $request->getParam('module');
	   $controller = $request->getParam('controller');
 	   if($module == 'admin' && $controller != 'auth') {
 	       $auth = Zend_Auth::getInstance();
 	       $hasIdentity = $auth->hasIdentity();
 	       if(!$hasIdentity) {
 	           $this->_response->setRedirect('/admin/auth');
 	       } else {
 	           $namespace = new Zend_Session_Namespace('Zend_Auth');
               $namespace->setExpirationSeconds(60*60);
 	       }
 	   }
	}
}