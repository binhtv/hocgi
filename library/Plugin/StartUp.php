<?php
class Plugin_StartUp extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
	}
	
	public function dispatchLoopStartup($request)
	{
		$module = $this->_request->getParam('module');
		Utils_Global::set('module', $module);
		Utils_Global::set('controller', $this->_request->getControllerName());
		Utils_Global::set('action', $this->_request->getActionName());
		Utils_Global::set('isAjax', $this->_request->isXmlHttpRequest());
		Utils_Global::set('baseUrl', $this->_request->getScheme() . '://' . $this->_request->getHttpHost());
		Utils_Global::set('isAjax', $this->_request->isXmlHttpRequest());
		Utils_Global::set('siteUrl', Utils_Global::get('baseUrl'));
		$siteConfig = Utils_Global::getConfig('cms', 'site');
		Utils_Global::set('imgUrl', $siteConfig->imgUrl);
		Utils_Global::set('staticUrl', $siteConfig->staticUrl);
		Utils_Global::set('templateVariables', array());
		Utils_Global::set('username', $_SESSION['username']);
		foreach ($this->_request->getParams() as $key => $value) {
			Utils_Global::$params[$key] = $value;
		}
	}
}