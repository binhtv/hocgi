<?php
class Plugin_Perm2 extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		//$uri = $request->getRequestUri();
		$moduleName = $request->getModuleName();
		$controllerName = $request->getControllerName();
		$actionName = $request->getActionName();
		
		echo "Module name: ".$moduleName."<br/>";
		echo "Controller name: ".$controllerName."<br/>";
		echo "Action name: ".$actionName."<br/>";
		
	}
}