<?php

class Admin_BackendLogController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->backendlogclzz = 'active';
    }
    
    public function listAction() {
        $table = Utils_Global::$params['table'];
        $dateF = strtotime(Utils_Global::$params['dateF']);
        $dateT = strtotime(Utils_Global::$params['dateT']);
        $active = Utils_Global::$params['active'];
        $userName = Utils_Global::$params['username'];
        
        $modelBackendLog = Admin_Model_BackendLog::factory();
        $statistics = array();
        if($table) {
            $options = array('table' => $table, 'dateF' => $dateF, 'dateT' => $dateT, 'active' => $active, 'username' => $userName);
            $statistics = $modelBackendLog->getEditorStatistics($options);
            $this->view->params = $options;
        } else {
            $options = array('dateF' => $dateF, 'dateT' => $dateT, 'active' => $active,'username' => $userName);
            $this->view->params = $options;
            $options['table'] = 'article';
            $stArticles = $modelBackendLog->getEditorStatistics($options);
            $options['table'] = 'course';
            $stCourses = $modelBackendLog->getEditorStatistics($options);
                       
            $statistics = $stArticles + $stCourses;
        }
        $this->view->statistics = $statistics;
        $this->view->title = "Thống kê";
    }
}

