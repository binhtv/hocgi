<?php
class Plugin_BlockManagement_BlockManagement extends Zend_Controller_Plugin_Abstract
{

    protected $_modules_exclude = array();

    public function __construct($module_exclude = array())
    {
        $this->_modules_exclude = $module_exclude;
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $module = strtolower($request->getParam('module'));
        $controller = strtolower($request->getParam('controller'));
        $action = strtolower($request->getParam('action'));
        Zend_Layout::startMvc(APPLICATION_PATH . '/modules/' . $module . '/layouts/');
        if ($module == 'admin') {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                Plugin_BlockManagement_BlockManager::setLayout('admin_layout');
            } else {
                Plugin_BlockManagement_BlockManager::setLayout('admin_login');
            }
        }

        $layoutConfig = Utils_Global::getConfig($module, 'layout', 'layout');
        $layout_override = Utils_Global::getConfig($module, 'layout', 'layout')->toArray();
        $layout = '';
        if (isset($layout_override[$controller])) {
            // specify layout for controller
            if (is_array($layout_override[$controller]) && isset($layout_override[$controller][$action])) {
                // specify layout for controller:action
                $layout = $layout_override[$controller][$action];
            } elseif (isset($layout_override[$controller]['default'])) {
                //having layout for action, but does not have default for controller
                $layout = $layout_override[$controller]['default']; //default controller layout
            }
        }
        if ($layout == '' && isset($layout_override['default'])) {
            $layout = $layout_override['default']; //default module layout
        }
        if ($layout) {
            Plugin_BlockManagement_BlockManager::setLayout($layout); //default module layout
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getParam('module');
        if (in_array($module, $this->_modules_exclude)) {
            return;
        }
        $_layout = Zend_Layout::getMvcInstance();
        if ($_layout->isEnabled() === FALSE)
            return;
        $this->executeBlocks();
    }

    public function executeBlocks()
    {
        try {
        $layout = Plugin_BlockManagement_BlockManager::getLayout();
        $sections = $this->getSections($layout);
        $folder_name = $this->getFoldername($layout);
        if ($folder_name != '') {
            Plugin_BlockManagement_BlockManager::setLayout($folder_name . '/' . $layout);
        }
        if (is_array($sections) && count($sections) > 0) {
            for ($i = 0; $i < count($sections); $i++) {
                Plugin_BlockManagement_BlockManager::setPostfix($sections[$i], '');
            }
        }
        $blocks = $this->getBlocks($layout);
        $this->processBlocks($blocks, $sections);
    } catch (Exception $e) {
    	prBinh($e);
    }
    }

    private function checkBlockShow($module, $controller, $action, $block)
    {
        if (!isset($block['except']) || empty($block['except']))
            return true; //true la show, false la off
        $listexcept = explode(',', $block['except']);
        for ($i = 0; $i < count($listexcept); $i++) {
            $excepts = explode('|', $listexcept[$i]);
            if (count($excepts) == 1) {
                $excepts[1] = '*';
                $excepts[2] = '*';
                $excepts[3] = '*';
            } elseif (count($excepts) == 2) {
                $excepts[2] = '*';
                $excepts[3] = '*';
            } elseif (count($excepts) == 3) {
                $excepts[3] = '*';
            }
            //check module first
            if ($excepts[0] != '*' && $excepts[0] == $module) {
                if ($excepts[1] == '*')
                    return true;
                elseif ($excepts[1] == '-')
                    return false;
                elseif ($excepts[1] == $controller) {
                    if ($excepts[2] == '*')
                        return true;
                    elseif ($excepts[2] == '-')
                        return false;
                    elseif ($excepts[2] == $action) {
                        if ($excepts[3] == '*')
                            return true;
                        else
                            return false;
                    }
                }
            }
        }
        return false;
    }

    public function processBlocks($blocks, $sections)
    {
        if ($blocks == null || !is_array($blocks) || count($blocks) == 0)
            return;
        $content = '';
        $module = '';
        $frontController = Zend_Controller_Front::getInstance();
        $request = $frontController->getRequest();
        $_module = $request->getParam('module');
        $_controller = $request->getParam('controller');
        $_action = $request->getParam('action');
        for ($i = 0; $i < count($blocks); $i++) {
            if (!in_array($blocks[$i]['section'], $sections)) {
                continue;
            }

            $showup = $this->checkBlockShow($_module, $_controller, $_action, $blocks[$i]);
            if ($showup == false)
                continue;
            $module = $blocks[$i]['module'];
            if ($module == 'extview') {
                $content = $this->processExtView($blocks[$i]);
            } else
            if ($module == 'view') {
                $content = $this->processView($blocks[$i]);
            } else
            if ($module == 'box') {
                $content = $this->processBox($blocks[$i]); 
				$content = $this->renderSiteVariableTemplate($content);
            } else
            if ($module == 'appbox') {
                $content = $this->processAppBox($blocks[$i]);
            }
            if ($content != null && $content != '') {
                Plugin_BlockManagement_BlockManager::setPostfix($blocks[$i]['section'], $content);
            }
        }
    }

    public function processBox($block)
    {
        $content = '';
        if ($block == null || !is_array($block)) {
            return $content;
        }
        $delta = $block["delta"];
        $_box = BlockManagement_Boxes::getInstance();
        $result = $_box->getBox($delta);
        if ($result != NULL && is_array($result)) {
            $content = $result["content"];
        }
        return $content;
    }

    public function processExtView($block)
    {
        $content = '';
        if ($block == null || !is_array($block)) {
            return $content;
        }
        $delta = $block["delta"];
        $view_business = BlockManagement_ExtViews::getInstance();
        $result = $view_business->getView($delta);
        if ($result != null) {
            $callback = $result['callback'];
            $require = $result['require_option'];
            $params_list = $result['params'];
            if ($params_list != null && $params_list != '') {
                $params = unserialize($params_list);
            } else {
                $params = array();
            }
            ////ProfilerLog::startLog("process extview callback='$callback'");
            $content = $this->renderExtView($callback, $params, $require);
            ////ProfilerLog::endLog("process extview callback='$callback'");
        }
        return $content;
    }

    public function processView($block)
    {
        $content = '';
        if ($block == null || !is_array($block)) {
            return $content;
        }
        $delta = $block["delta"];
        $view_business = BlockManagement_Views::getInstance();
        $result = $view_business->getView($delta);
        if ($result != null) {
            $action = $result['action'];
            $controller = $result['controller'];
            $module = $result['module'];
            $params_list = $result['params'];
            if ($params_list != null && $params_list != '') {
                $params = unserialize($params_list);
				$front = Zend_Controller_Front::getInstance();
				$request = $front->getRequest();
				foreach($params as $key => $value){
					$paramValue = $request->getParam($key);
					if(!empty($paramValue))
						$params[$key]= $paramValue;
				}
            } else {
                $params = array();
            }
            
            
            ////ProfilerLog::startLog("process view module='$module' - controller='$controller' - action='$action'");;
            $content = $this->renderView($action, $controller, $module, $params);
            //	//ProfilerLog::endLog("process view module='$module' - controller='$controller' - action='$action'");
        }
        return $content;
    }

    public function processAppBox($block)
    {
        $module = "profile";
        $controller = "profile";
        $action = "appboxblock";
        $params = array();
        $params["privacy_type"] = $block["privacy_type"];
        $params["module"] = $block["module"];
        $params["delta"] = $block["delta"];
        $params["section"] = $block["section"];
        $params["weight"] = $block["weight"];
        //ProfilerLog::startLog("process appbox appid='" . $block['delta'] . "'");
        $content = '';
        try {
            $content = $this->renderView($action, $controller, $module, $params);
        } catch (Exception $e) {
            $content = '';
        }
        //ProfilerLog::endLog("process appbox appid='" . $block['delta'] . "'");
        return $content;
    }

    public function getBlocks($layout)
    {
        $block_business = BlockManagement_Blocks::getInstance(); 
		$result = $block_business->getListByLayout($layout);
        return $result;
    }

    public function getFoldername($layout)
    {
        $layouts_business = BlockManagement_Layouts::getInstance();
        $result = $layouts_business->getLayout($layout);
        if ($result == null)
            return '';
        else {
            return $result['folder_name'];
        }
    }

    public function getSections($layout)
    {
        $layouts_business = BlockManagement_Layouts::getInstance();
        $result = $layouts_business->getLayout($layout);
        $_return = array();
        if ($result != null && is_array($result)) {
            $_return = explode(',', $result['sections']);
        }
        return $_return;
    }

    public function renderExtView($callback, $params, $require = '')
    {
        $content = '';
        try {
            if ($require != '') {
                $require = str_ireplace('{MODULE_PATH}',
                            APPLICATION_PATH . '/' . SGN_Application::get('module'), $require);
                require_once $require;
            }
            $content = call_user_func_array($callback, $params);
        } catch (Exception $e) {
            $content = '';
        }
        return $content;
    }

    public function renderView($action, $controller, $module, $params)
    {
        $content = "";
        try {
            $view = new Zend_View();
            //action, controller, module, params
            $content = $view->action($action, $controller, $module, $params);
        } catch (Exception $e) {

        }
        return $content;
    }
	
	public function renderNavigationTemplate($content)
	{
		$module =  SGN_Application::get('module');
		$search = preg_match_all('/{os_link_id_[0-9]}/', $content, $matches);
		foreach($matches[0] as $value){
			$osId = substr($value,12,1);
			$content = str_replace('{os_link_id_0}',"/$module",$content);
			$content = str_replace('{os_link_id_'.$osId .'}',"/$module/detail/cat/os/{$osId}",$content);
			
			$front = Zend_Controller_Front::getInstance();
			$paramOsId = $front->getRequest()->getParam('os',0);//		 pr($paramOsId );
			$controllerName = $front->getRequest()->getControllerName();
			if($paramOsId == $osId){
				$content = str_replace('{os_active_id_'.$osId .'}',"Active",$content);
			}else{
				$content = str_replace('{os_active_id_'.$osId .'}',"",$content);
			}
		
		}
		
		$search = preg_match_all('/{page_link_id_[0-9]}/', $content, $matches2);
		foreach($matches2[0] as $value){
			$pageid = substr($value,14,1);
			$content = str_replace('{page_link_id_'.$pageid .'}',"/$module/page/view/pageid/{$pageid}",$content);
			
			$front = Zend_Controller_Front::getInstance();
			$paramPageId= $front->getRequest()->getParam('pageid',0);//		 pr($paramOsId );
			if($paramPageId == $pageid){
				$content = str_replace('{page_active_id_'.$pageid .'}',"Active",$content);
			}else{
				$content = str_replace('{page_active_id_'.$pageid .'}',"",$content);
			}
		
		}
		if ($controllerName == 'information') {
			$content = str_replace('{information}',"Active",$content);
		}
		
		if(empty($paramOsId) && empty($paramPageId))
			$content = str_replace('{os_active_id_0}',"Active",$content);
		return $content;
	}
	public function renderSiteVariableTemplate($content)
	{
	
		$search = preg_match_all('/{[a-zA-Z0-9_]+}/', $content, $matches);
		$templateVariables = Utils_Global::get('templateVariables');
		if ($templateVariables) {
			foreach($matches[0] as $value) {
				$varName = substr($value,1, strlen($value) -2);
				if ( $templateVariables->$varName->value) {
					$content = str_replace($value, $templateVariables->$varName->value, $content);
				} else { // for menu
					$front = Zend_Controller_Front::getInstance();
					$controllerName = $front->getRequest()->getControllerName();
					$actionName = $front->getRequest()->getActionName();
					$active = 0;
					if ($templateVariables->$varName->controller == $controllerName){
						$active = 1;
						if ($templateVariables->$varName->action) {
							if ($templateVariables->$varName->action == $actionName) {
								$paramsName = $front->getRequest()->getParams();
								if ($templateVariables->$varName->params) {
									$active = 0;
									foreach($paramsName as $key1 => $value1) {
										foreach ($templateVariables->$varName->params as $key2 => $value2) {
											if (($key1 == $key2) && ($value1 == $value2)) {
												$active = 1;
												break;
											}
										}
									}
								}
		
							} else {
								$active = 0;
							}
						}
					}
					if ($active) {
						$content = str_replace( $value , ' class="' . $templateVariables->$varName->classNameActive . '"', $content);
					} else {
						$content = str_replace($value, "", $content);
					}
				}
			}
		}
		return $content;
	}
}

?>