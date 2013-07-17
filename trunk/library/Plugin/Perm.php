<?php
class Plugin_Perm extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
// 	   $module = $request->getParam('module');
// 	   if($module == 'admin') {
// 	       $auth = Zend_Auth::getInstance();
// 	       $hasIdentity = $auth->hasIdentity();
// 	       if(!$hasIdentity) {
// 	           $request->setControllerName ( 'Auth' );
// 	           $request->setActionName ( 'index' );
// 	       }
// 	   }
	}
}