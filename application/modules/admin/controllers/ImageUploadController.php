<?php

class Admin_ImageUploadController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->imageuploadlzz = 'active';
    }
    
    public function indexAction()
    {
    }
    
    
    private function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    
    public function uploadAction() {
        $erroMessage = Utils_Global::$params['errMessage'];
        $succMessage = base64_decode(Utils_Global::$params['succMessage']);
        $name = Utils_Global::$params['name'];
        $source = Utils_Global::$params['source'];
        $category = Utils_Global::$params['category'];
        
        $categoryModel = Cms_Model_Category::factory();
        $this->view->errMessage = $erroMessage;
        $this->view->succMessage = $succMessage;
        $this->view->name = $name;
        $this->view->source = $source;
        
    }
    
    /**
     * List of images
     * */
    public function listAction() {
        $page = intval(Utils_Global::$params['page']);
        $limit = intval(Utils_Global::$params['limit']);
        $title = Utils_Global::$params['title'];
        $category = Utils_Global::$params['category'];
        $username = Utils_Global::$params['username'];
        $filterUsernames = array();
        if(!$username) {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $userName = $identity->username;
            $filterUsernames = array("'binhtv'", "'" . $userName . "'");
        }
        
        if($limit <= 0) {
        	$limit = 10;
        }
        if($page <= 0) {
        	$page = 1;
        }
        
        $imageUploadModel = Admin_Model_ImageUpload::factory();
        $options = array('title' => $title, 'category' => $category, 'offset' => ($page - 1) * $limit, 'limit' => $limit);
        if($username) {
            $options['username'] = $username;
        }
        if($filterUsernames) {
            $options['usernames'] = implode(',', $filterUsernames);
        }
        $uploads = $imageUploadModel->getImageUploads($options);
        $this->view->uploads = $uploads;
        $this->view->page = $page;
        $this->view->numRowPerPage = $limit;
        $this->view->totalItem = $imageUploadModel->getImageUploadsCount($options);
        $this->view->currentUrl = $this->view->serverUrl() . $this->view->url(array()) . '?' . http_build_query($options);
        $this->view->params = $options;
    }
    
    /**
     * Save truyen che action
     * */
    public function saveAction() {
        $succFullImageUrl = "";
        $name = strip_tags(trim(Utils_Global::$params['Caption']));
        $source = strip_tags(Utils_Global::$params['Source']);
        $category = (Utils_Global::$params['category']);
        $nameSeo = Utils_CommonFunction::getNameSeo($name);
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $userName = $identity->username;
        if(!$userName) {
            $params = array('errMessage' => 'Vui lòng đăng nhập!', 'name' => $name, 'category' => $category);
            $this->_helper->redirector('upload', 'truyen-che', 'vmg', $params);
        }
        
        if(!Utils_CommonFunction::getNameSeo($name)) {
            $params = array('errMessage' => 'Vui lòng nhập tên truyện hợp lệ.', 'source' => $source, 'category' => $category);
            $this->_helper->redirector('upload', 'truyen-che', 'vmg', $params);
        }
        
//         if(!preg_match("/^[^<>\'\"\/;`%@&#*?!~|]*$/", $name)) {
//             $params = array('errMessage' => 'Vui lòng nhập tên truyện hợp lệ.', 'source' => $source, 'category' => $category);
//             $this->_helper->redirector('upload', 'truyen-che', 'vmg', $params);
//         }
        
        //Check so lan upload lan nua
        if($this->_request->isPost()) {
            $data = array();
            
            $result = $this->uploadTempImage($category, $nameSeo);
            if(is_array($result) && $result) {//Luu thong tin upload
                $modelImageUpload = Admin_Model_ImageUpload::factory();
	            $uploadInfo = array('username' => $userName,
	                                'title' => $name,
	                                'category' => $category,
	                                'name_seo' => $nameSeo,
	                                'image_url' => $result['full_url'],
	                                'image_path' => $result['full_path'],
	                                'source_size' => $result['source_size'],
	                                'dateline' => time(),
	            );
	            $succFullImageUrl = base64_encode($result['full_url']);
	            $result = $modelImageUpload->insertNewUpload($uploadInfo);
            } else {//Loi xay ra
                if($result == -1) {//Dinh dang file ko hop le
                    $errMessage = $this->_config->upload->msgInvalidType;
                } else if($result == -2) {//Size vượt quá
                    $errMessage = $this->_config->upload->msgInvalidSize;
                } else if($result == -3) {//File qua nho
                    $errMessage = $this->_config->upload->msgInvalidDemension;
                } else if($result == -4) {
            	    $errMessage = $this->_config->upload->msgFileNotFound;
            	}
            }
        }
        
        if($result > 0) {
            $params = array('errMessage' => $errMessage, 'name' => $name, 'source' => $source, 'succMessage' => $succFullImageUrl);
            $this->_helper->redirector('upload', 'image-upload', 'admin', $params);
        } else {
            $params = array('errMessage' => $errMessage, 'name' => $name, 'source' => $source);
            $this->_helper->redirector('upload', 'image-upload', 'admin', $params);
        }
    }
	
    /**
     * upload into temp folder;
     * @param string $category$category : article or course etc
     * @return 0: lỗi ko xác định, -1: file type ko phù hợp, -2: size vượt quá quy định
     * 
     * */
    public function uploadTempImage($category, $nameSeo = null) {
        try{
            $upload = new Zend_File_Transfer_Adapter_Http();
            $files = $upload->getFileInfo();
            $imagePath = $upload->getFileName();
            $key = $category.'ImageUploadPath';
            $imageUploadPath    = $this->_config->upload->$key;
            $imageFileName      = '';
            
            if($files['file']) {
                //Check file valid
                if(!$files['file']['name'] || !preg_match('/jpg|jpeg|gif|png|bmp/', $files['file']['name'])) {
                    return -1;
                }
                 
                //Check file type is valid or not
                $allowTypes = $this->_config->upload->acceptedFileTypes->toArray();
                if(!in_array($files['file']['type'], $allowTypes)) {
                    return -1;//Invalid file
                }
                //Check size
                $source_size = $files['file']['size'];
                if($source_size > $this->_config->upload->maxFileSizeUpload) {
                    return -2;//Oversize
                }
                //Image chapter file name
                $originalImageFileName = str_replace(' ', '', basename($imagePath));
                $imageFileName = $originalImageFileName;
                $ext = pathinfo($imageFileName, PATHINFO_EXTENSION);
                $imageFileName = time() . '_' . $nameSeo;
                $imageFileName = $imageFileName . '.' . $ext;
            
                $imageUploadPath = $this->createTempFolder($imageUploadPath, $originalImageFileName);
                $upload->setDestination($imageUploadPath);
                 
                $upload->addFilter('Rename', array('target' => $imageUploadPath . '/' . $imageFileName));
                $result = $upload->receive();
                //Tien hanh check type 1 lan nua, make sure day la file hinh
                $fileType = mime_content_type($imageUploadPath . '/' . $imageFileName);
                if(!in_array($fileType, $allowTypes)) {
                    unlink($imageUploadPath . '/' . $imageFileName);
                    return -1;//Invalid file
                }
                 
                $imageSize = getimagesize($imageUploadPath . '/' . $imageFileName);
                if(!$imageSize) {
                    unlink($imageUploadPath . '/' . $imageFileName);
                    return -1;
                }
                if($imageSize[0] < $this->_config->upload->dimentionW || $imageSize[1] < $this->_config->upload->dimentionH) {
                    unlink($imageUploadPath . '/' . $imageFileName);
                    return -3;
                }
            } else {
                return -4;
            }
         
            $rResult = array();
            if($result) {
                $rResult['source_size'] = $source_size;
                $rResult['full_path'] = $imageUploadPath . '/' . $imageFileName;
                $rResult['full_url'] = $this->_config->imgUrl  . $category . DS . 'images'.
                                            DS . Utils_Global::hashName($originalImageFileName, 512) . DS . $imageFileName;
            }
         
            return $rResult;
        } catch(Exception $e) {
            return 0;
        }
    }
	
	private function createTempFolder($imageUploadPath, $userName) {
	    //If source is not existed
	    if(!file_exists($imageUploadPath)) {
	        mkdir($imageUploadPath);
	    }
	    
	    $hashName = Utils_Global::hashName($userName, 512);
	    $imageUploadPath .= '/' . $hashName;
	    if(!file_exists($imageUploadPath)) {
	        mkdir($imageUploadPath);
	    }
	    
	    return $imageUploadPath;
	}
}

