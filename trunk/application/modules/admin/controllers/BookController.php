<?php

class Admin_BookController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->libaryclzz = 'active';
    }
    
    public function editAction() {
        $id = trim(Utils_Global::$params['id']);
        $fileUploadModel = Admin_Model_FileUpload::factory();
        $files = $fileUploadModel->getUploads();
        if($id) {//Edit
            $documentaryModel = Admin_Model_Documentary::factory();
            $documentary = $documentaryModel->getDocumentaries(array('id' => $id));
            $this->view->documentary = $documentary[0];
            $this->view->files = $files;
            $this->view->id = $id;
            $this->view->title = "Chỉnh sửa tài liệu";
            $this->view->errMessage = Utils_Global::$params['errMessage'];
            if($this->view->errMessage) {
            	$this->view->documentary = Utils_Global::$params;
            }
        } else {//Insert
            $this->view->documentary = Utils_Global::$params;
            $this->view->files = $files;
            $this->view->title = "Tạo tài liệu mới";
            $this->view->errMessage = Utils_Global::$params['errMessage'];
        }
    }
    
    public function listAction() {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $userName = $identity->username;
        if($userName == 'admin') {
        	$userName = '';
        }
        
        $page = intval(Utils_Global::$params['page']);
        $limit = intval(Utils_Global::$params['limit']);
        $id = Utils_Global::$params['id'];
        $name = Utils_Global::$params['name'];
        $active = Utils_Global::$params['active'];
        $new = Utils_Global::$params['new'];
        
        if($limit <= 0) {
            $limit = 10;
        }
        if($page <= 0) {
            $page = 1;
        }
        
        $modelDocumentary = Admin_Model_Documentary::factory();
        $options = array('offset' => ($page - 1) * $limit, 'limit' => $limit, 'editor' => $userName,
                           'id' => $id, 'name' => $name,
                            'active' => $active, 'new' => $new,
        );
        $documentaries = $modelDocumentary->getDocumentaries($options);
        
        $this->view->documentaries = $documentaries;
        $this->view->title = "Tài liệu";
        $this->view->page = $page;
        $this->view->numRowPerPage = $limit;
        $countOptions = array('id' => $id, 'name' => $name, 'editor' => $userName,
                            'active' => $active, 'new' => $new);
        $this->view->totalItem = $modelDocumentary->getDocumentaryCount($options);
        $this->view->currentUrl = $this->view->serverUrl() . $this->view->url(array()) . '?' . http_build_query($options);
        $this->view->params = $options;
    }
    
    public function saveAction() {
        $id = Utils_Global::$params['id'];
        $name = Utils_Global::$params['name'];
        $nameSeo = Utils_CommonFunction::getNameSeo($name);
        $shortDescription = Utils_Global::$params['short_description'];
        $content = Utils_Global::$params['content'];
        $author = Utils_Global::$params['author'];
        $fileAssociate = Utils_Global::$params['file_associate'];
        $order = Utils_Global::$params['order'];
        $new = trim(Utils_Global::$params['new']);
        $active = trim(Utils_Global::$params['active']);
        if($active) {
            $active = 1;
        }
        if($new) {
            $new = 1;
        }
        $auth = Zend_Auth::getInstance();
        $userName = $auth->getIdentity()->username;
        if(!$userName) {
            Utils_Global::$params['errMessage'] = 'Vui lòng đăng nhập!';
            $this->_forward('edit', 'documentary', 'admin');
            return;
        }
        if(!$nameSeo) {
        	Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên tài liêu hợp lệ!';
        	$this->_forward('edit', 'documentary', 'admin');
        	return ;
        }
        if(!$shortDescription) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập miêu tả ngắn!';
            $this->_forward('edit', 'documentary', 'admin');
            return;
        }
        if(!$author) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập têna tác giả!';
            $this->_forward('edit', 'documentary', 'admin');
            return;
        }
        if(!$content) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập miêu tả cho tài liệu!';
            $this->_forward('edit', 'documentary', 'admin');
            return;
        }
        if(!$fileAssociate) {
            Utils_Global::$params['errMessage'] = 'Vui chọn file download khóa học!';
            $this->_forward('edit', 'documentary', 'admin');
            return;
        }
        
        if($this->_request->isPost()) {
            $fileUploadModel = Admin_Model_FileUpload::factory();
            $file = $fileUploadModel->getUploads(array('id' => $fileAssociate));
            if(!$file[0]) {
                Utils_Global::$params['errMessage'] = 'Vui chọn file download khóa học!';
                $this->_forward('edit', 'documentary', 'admin');
            }
            $file = $file[0];
            $data = array();
            $data = array('name' => $name,
            		'name_seo' => $nameSeo,
            		'content' => $content,
                    'author' => $author,
                    'short_description' => $shortDescription,
            		'last_update' => time(),
                    'active' => $active,
            		'order' => $order,
            		'new' => $new,
                    'file_type' => $file['file_type'],
                    'file_name' => $file['file_name'],
                    'file_size' => $file['file_size'],
                    'hash_download_folder' => $file['hash_folder'],
                    'file_id' => $file['id'],
            );
            $result = $this->uploadTempImage('documentary');
            if(is_array($result) && $result) {//Luu thong tin upload
            	$data['image'] = $result['image'];
            	$data['hash_folder'] = $result['hash_folder'];
            } else {//Loi xay ra
            	if($result==0 && !$id) {
                	$errMessage = $this->_config->upload->msgImageRequired;
                	Utils_Global::$params['errMessage'] = $errMessage;
                	$this->_forward('edit', 'article', 'admin');
                	return;
                } else if($result == -1) {//Dinh dang file ko hop le
            		$errMessage = $this->_config->upload->msgInvalidType;
            		Utils_Global::$params['errMessage'] = $errMessage;
            		$this->_forward('edit', 'documentary', 'admin');
            		return;
            	} else if($result == -2) {//Size vượt quá
            		$errMessage = $this->_config->upload->msgInvalidSize;
            		Utils_Global::$params['errMessage'] = $errMessage;
            		$this->_forward('edit', 'documentary', 'admin');
            		return;
            	} else if($result == -3) {//File qua nho
            		$errMessage = $this->_config->upload->msgInvalidDemension;
            		Utils_Global::$params['errMessage'] = $errMessage;
            		$this->_forward('edit', 'documentary', 'admin');
            		return;
            	} else if($result == -4) {
            	    $errMessage = $this->_config->upload->msgFileNotFound;
            	    Utils_Global::$params['errMessage'] = $errMessage;
            	    $this->_forward('edit', 'documentary', 'admin');
            	    return;
            	}
            }
                
            //Save change or insert new
            $documentaryModel = Admin_Model_Documentary::factory();
            if($id) {
            	$result = $documentaryModel->update($id, $data);
            } else {
                $data['editor'] = $userName;
                if($data['image']) {
                	$data['dateline'] = time();
                	$result = $documentaryModel->insert($data);
                }
            }
            
        }
        
        if(!is_array($result) && $result > 0) {
        	$params = array('errMessage' => "Thay đổi thành công");
        	$this->_helper->redirector('edit', 'documentary', 'admin', $params);
        } else {
            Utils_Global::$params['errMessage'] = $errMessage;
            $this->_forward('edit', 'documentary', 'admin');
            return;
        }
    }
    
    public function deleteAction() {
        
    }
    
    /**
     * upload into temp folder;
     * @param string $category$category : article or course etc
     * @return 0: lỗi ko xác định, -1: file type ko phù hợp, -2: size vượt quá quy định
     *
     * */
    public function uploadTempImage($category) {
    	try{
    		$upload = new Zend_File_Transfer_Adapter_Http();
    		$files = $upload->getFileInfo();
    		$imagePath = $upload->getFileName();
    		$key = $category.'AvatarUploadPath';
    		$imageUploadPath    = $this->_config->upload->$key;
    		$imageFileName      = '';
    		if($files['file']) {
    		    if(!$files['file']['name']) {
    		    	return 0;
    		    }
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
    			$imageFileName = md5(time() . $imageFileName);
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
    			$rResult['image'] = $imageFileName;
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

