<?php

class Admin_PermissionsController extends Zend_Controller_Action 
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
	
	public function saveAction()
	{
		try{
			$this->_helper->viewRenderer->setNoRender();
			$role_id = (int)$this->getRequest()->getParam('id',0);
			$privileges = $this->getRequest()->getParam('privileges');
			$mResouce = new Admin_Model_Resource();
			$mPermission = new Admin_Model_Permission();		
			$resouces = $mResouce->getResource();	
		
			if(!empty($resouces)){
				foreach ($resouces as $resouce){
					$privilegeStr = "";
					$i = 0;
					foreach ($privileges as $privilege){
						$privilegeData = explode("_",$privilege);
						if($resouce['name'] == $privilegeData[0]){				
							$privilegeStr .= $privilegeData[1]."|";						
						}					
					}		
					$privilegeStr = substr($privilegeStr,0,-1);
					
					$data= array(								
								'permission' => $privilegeStr,
					);
					$where = array(
								'role_id' => $role_id,
								'resource_id' => $resouce['id'],
					);
					
					if($mPermission->hasPermission($where)){
						$result = $mPermission->updatePermission($data,$where);
					}else{
						$data = array_merge($data,$where);
						$mPermission->insertPermission($data);						
					}					
				}
			}
			$mess = base64_encode("Update successfully.");
			$this->_redirect($this->view->serverUrl() . '/' . "admin/permissions/list/id/{$role_id}/mess/{$mess}");
		}
		catch(Exception $e){
			prBinh($e->getMessage());
		}
		
	}
	
	public function listAction()
	{
		try{			
			$mPermission = new Admin_Model_Permission();			
			$mResouce = new Admin_Model_Resource();		
			$mPrivilege = new Admin_Model_Privilege();	
			$this->view->privileges	= $mPrivilege->getPrivilege();			

			$role_id = (int)$this->getRequest()->getParam('id',0);	
			
			$this->view->permission = $mPermission->getPermission(array('role_id'=>$role_id));
			$this->view->resources = $mResouce->getResource();	
			$this->view->role_id = $role_id;	
			$this->view->url_action = $this->view->serverUrl() . '/'. "admin/permissions/save/id/" . $role_id;	
			$this->view->mess = base64_decode(trim($this->_request->getParam("mess")));		
			$this->view->btn_cancel =  $this->view->serverUrl() . '/' . "admin/roles/list";		
			$this->view->btn_refresh =  $this->view->serverUrl() . '/' . "admin/permissions/refreshresource/id/{$role_id}";	
		}
		catch(Exception $e){
			prBinh($e->getMessage());
		}
	}
	
	public function refreshresourceAction()
	{
		try{	
			$this->_helper->viewRenderer->setNoRender();
			$mResouce = new Admin_Model_Resource();			
			
			$files = array_diff( scandir(APPLICATION_PATH . '/modules/admin/controllers'), array(".", "..") ); 
			foreach($files as $fileName)
			{
				$pos = strpos($fileName, "Controller.php");
				if($pos  > 0){
					$resource = strtolower(substr($fileName,0,$pos));
					if(!$mResouce->hasResource($resource)){
						$mResouce->insertResource(array("name" => $resource));
					}
				}		
			}	

			$role_id = (int) trim($this->_request->getParam("id",0));
			$mess = base64_encode("Refresh resource successfully.");		
			$uri = $this->view->serverUrl() . '/' . "admin/permissions/list/id/{$role_id}/mess/{$mess}";
			$this->_redirect($uri);		
		}
		catch(Exception $e){
			pr($e->getMessage());
		}
	}
	
	public function deleteresourceAction()
	{
		try{	
			$this->_helper->viewRenderer->setNoRender();
			$mResouce = new Admin_Model_Resource();			
			$resource_id = (int) trim($this->_request->getParam("id",0));
							
			$mResouce->inactiveResource(array("id" => $resource_id));

			$mess = base64_encode("Delete resource successfully.");		
			$uri = $this->view->serverUrl() . '/' . "admin/permissions/list/id/{$role_id}/mess/{$mess}";
			$this->_redirect($uri);		
		}
		catch(Exception $e){
			pr($e->getMessage());
		}
	}
	
	private function openTable($roles, &$content)
	{
		$content .= "<table class=\"table table-bordered table-striped\"><thead><tr><th valign=\"middle\">Permission</th>";
		
		for($i=0;$i<count($roles);$i++)
		{
			$content .= "<th>" . $roles[$i]['name'] . "</th>";
		}
		
		$content .= "</tr></thead>";
	}
	
	private function closeTable(&$content)
	{
		$content .= "</table>";
	}
	
	private function addTrModule($modulename, &$content, $colspan = 2)
	{
		$content .= "<tr>" . "<td class=\"module\" colspan=\"$colspan\">" .$modulename . "</td></tr>";		
	}
	
	private function addTd($list, &$content)
	{
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list['permission_name']);$i++)
			{
				$permission_name = $list['permission_name'][$i];
				$content .= "<tr class='" . ($i % 2 == 0 ? "odd" : "even") . "'>" . "<td>" . $permission_name . "</td>";
				for($j=0;$j<count($list['roles']);$j++)
				{
					$role = $list['roles'][$j];
					$permissions = explode(',', $role['permission']);
					if(in_array($permission_name,$permissions))
					{
						$content .= "<td><input type='checkbox' name='" . $role['pid'] . "[]' id='" .$role['name'] . "-" 
								. $permission_name . "' checked=checked value='" . $permission_name . "'></td>";
					}
					else
					{
						$content .= "<td><input type='checkbox' name='" . $role['pid'] . "[]' id='" .$role['name'] . "-" 
								. $permission_name . "' value='" . $permission_name . "'></td>";
					}
				}
				$content .= "</tr>";
			}
		}
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
}