<?php

class Utils_Listing
{
	private $_title = array();
	private $_fields = array();
	private $_data = array();
	private $_autoid = false;
	private $_listing = "commonlayout/listing.phtml";
		
	public function __construct($title = array(), $fields = array(), $data = array(), $autoid = false, $start = 0,$view = "")
	{
		$this->_title = $title;
		$this->_fields = $fields;
		$this->_data = $data;
		$this->_autoid = $autoid;		
		$this->_start = $start;
		
		if($view != "") {
			$this->_listing = $view;
		}
		
	}
	
	public function renderList()
	{
		$view = new Zend_View();		
		$view->title = $this->_title;
		$view->fields = $this->_fields;
		$view->data = $this->_data;
		$view->autoid = $this->_autoid;
		$view->start = $this->_start;
		$view->setBasePath(APPLICATION_PATH . "/modules/admin/views", "phtml");		
		//$content = $view->render('commonlayout/listing.phtml');				
		$content = $view->render($this->_listing);
		return $content;
	}
}

?>