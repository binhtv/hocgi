<?php

class Admin_FileUploadController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->fileuploadlzz = 'active';
    }
    
    public function uploadAction() {
        $erroMessage = Utils_Global::$params['errMessage'];
        $succMessage = base64_decode(Utils_Global::$params['succMessage']);
        $name = Utils_Global::$params['name'];
        $source = Utils_Global::$params['source'];
        
        $categoryModel = Cms_Model_Category::factory();
        $this->view->errMessage = $erroMessage;
        $this->view->succMessage = $succMessage;
        $this->view->name = $name;
        $this->view->source = $source;
        
    }
    
    /**
     * Save truyen che action
     * */
    public function saveAction() {
        $succFullImageUrl = "";
        $name = strip_tags(trim(Utils_Global::$params['Caption']));
        $source = strip_tags(Utils_Global::$params['Source']);
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
        }
        if(!$identity || !$identity->username) {
            $this->_redirect('/admin');
        }
        $userName = $identity->username;
        if(!$name) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên hợp lệ!';
        	$this->_forward('upload', 'file-upload', 'admin');
            return;
        }
        
        if($this->_request->isPost()) {
            $data = array();
            $result = $this->uploadFile('documentary');
            if(is_array($result) && $result) {//Luu thong tin upload
                $modelFileUpload = Admin_Model_FileUpload::factory();
	            $uploadInfo = array('username' => $userName,
	                                'name' => $name,
	                                'file_name' => $result['file_name'],
	                                'file_type' => $result['file_type'],
	                                'file_size' => $result['file_size'],
	                                'hash_folder' => $result['hash_folder'],
	                                'dateline' => time(),
	            );
	            $result = $modelFileUpload->insertNewUpload($uploadInfo);
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
            $params = array('errMessage' => $errMessage, 'name' => $name, 'succMessage' => 'Uploaded!');
            $this->_helper->redirector('upload', 'file-upload', 'admin', $params);
        } else {
            $params = array('errMessage' => $errMessage, 'name' => $name);
            $this->_helper->redirector('upload', 'file-upload', 'admin', $params);
        }
    }
	
    /**
     * upload into temp folder;
     * @param string $category$category : article or course etc
     * @return 0: lỗi ko xác định, -1: file type ko phù hợp, -2: size vượt quá quy định
     * 
     * */
    public function uploadFile($category) {
        try{
            $upload = new Zend_File_Transfer_Adapter_Http();
            $files = $upload->getFileInfo();
            $imagePath = $upload->getFileName();
            $key = $category.'UploadPath';
            $imageUploadPath    = $this->_config->upload->$key;
            $imageFileName      = '';
            $fileType = '';
            
            if($files['file']) {
                //Check size
                $source_size = $files['file']['size'];
                if($source_size > $this->_config->upload->maxDocumentarySizeUpload) {
                    return -2;//Oversize
                }
                
                //Image chapter file name
                $originalImageFileName = str_replace(' ', '', basename($imagePath));
                $imageFileName = $originalImageFileName;
                $ext = pathinfo($imageFileName, PATHINFO_EXTENSION);
                $imageFileName = md5(time() . $imageFileName);
                $imageFileName = $imageFileName . '.' . $ext;
            
                //Get file type, not get mine type
                $fileTypes = $this->_config->upload->documentaryFileExts->toArray();
                foreach($fileTypes as $key => $types) {
                	if(preg_match($types, $ext)) {
                		$fileType = $key;
                		break;
                	}
                }
                $imageUploadPath = $this->createTempFolder($imageUploadPath, $originalImageFileName);
                $upload->setDestination($imageUploadPath);
                 
                $upload->addFilter('Rename', array('target' => $imageUploadPath . '/' . $imageFileName));
                $result = $upload->receive();
            } else {
                return -4;
            }
         
            $rResult = array();
            if($result) {
                $rResult['file_size'] = $source_size;
                $rResult['file_name'] = $imageFileName;
                $rResult['file_type'] = $fileType?$fileType:-1;//Unknow file type -1
                $rResult['hash_folder'] = Utils_Global::hashName($originalImageFileName, 512);
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

