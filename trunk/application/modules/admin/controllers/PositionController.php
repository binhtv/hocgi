<?php

class Admin_PositionController extends Zend_Controller_Action
{
    private $_config;
    private $_categories;
	protected $_models = array();
	
    public function init()
    {
        $this->view->bannerclzz = 'active';
    }

    public function deleteAction()
    {
		$id = Utils_Global::$params['id'];
		$text = "Chưa hỗ trợ chức năng delete";
		$this->_redirect('/admin/position/list/errMessage/' . $text);
    }
    
    public function listAction() {   
        $page = intval(Utils_Global::$params['page']);
        $limit = intval(Utils_Global::$params['limit']);
        $errMessage = Utils_Global::$params['errMessage'];
        $id = Utils_Global::$params['id'];
        $name = Utils_Global::$params['position_name'];
        if($page <= 0) {
            $page = 1;
        }
        if($limit <= 0) {
            $limit = 10;
        }
        
        $positionModel = Admin_Model_Position::factory();
        $options = array('offset' => ($page - 1) * $limit, 'limit' => $limit);
        $options['id'] = $id;
        $options['position_name'] = $name;
		$result = $positionModel->getPositions($options);
		$this->view->positions = $result;
		$this->view->page = $page;
		$countOptions = array('id' => $id, 'position_name' => $name);
		$this->view->totalItem = $positionModel->getPositionsCount($options);
		$this->view->numRowPerPage = $limit;
		$this->view->currentUrl = $this->view->serverUrl() . $this->view->url(array()) . '?fake=1';
		$this->view->title = "Position";
		$this->view->errMessage = $errMessage;
		$this->view->params = $options;
    }
    
    public function editAction()
    {
        $id = Utils_Global::$params['id'];
        if($id) {
            $positionModel = Admin_Model_Position::factory();
            $positions = $positionModel->getPositions(array('id' => $id));
            $this->view->position = $positions[0];
            $this->view->id = $id;
        }
    }
	
	/**
	 * method: update layout information
	 * Description: update layout
	 */
	 public function saveAction()
	 {
	     $id = Utils_Global::$params['id'];
	     $name = Utils_Global::$params['name'];
	     if(!$name) {
	         Utils_Global::$params['errMessage'] = "Vui lòng nhập tên";
	         $this->_forward('edit', 'position', 'admin');
	     }
	     
	     $positionModel = Admin_Model_Position::factory();
	     $data = array('position_name' => $name,
        	         'last_update' => time(),);
	     if($id) {
	         $result = $positionModel->update($id, $data);
	     } else {
	         $data['dateline'] = time();
	         $result = $positionModel->insert($data);
	     }
	     
	     if($result > 0) {
	         $this->_redirect('/admin/position/list');
	     } else {
	         $this->view->errMessage = "Có lỗi xảy ra, vui lòng thử lại";
	         $this->_forward('edit', 'position', 'admin');
	     }
	 }


    /**
     * Get model box object	
     */
    protected function &_getModel($className)
    {
		if(isset($this->_models[$className]))
		{
			if(is_object($this->_models[$className]))
			{
				return $this->_models[$className];
			}
		}
		
        require APPLICATION_PATH . "/modules/admin/models/{$className}.php";
        $this->_models[$className] = new $className();        
        return $this->_models[$className];
    }
}