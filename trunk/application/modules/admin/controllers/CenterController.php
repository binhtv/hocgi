<?php

class Admin_CenterController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->centerclzz = 'active';
    }
    
    public function editAction() {
        $id = trim(Utils_Global::$params['id']);
        $centerModel = Admin_Model_Center::factory();
        if($id) {//Edit
            $center = $centerModel->getCenters(array('id' => $id));
            $this->view->center = $center;
            $this->view->id = $id;
            $this->view->title = "Chỉnh sửa trung tâm";
            $this->view->errMessage = Utils_Global::$params['errMessage'];
            if($this->view->errMessage) {//Truong hop edit bi loi
                $this->view->center = Utils_Global::$params;
            }
        } else {//Insert
            $this->view->center = Utils_Global::$params;
            $this->view->title = "Tạo trung tâm mới";
            $this->view->errMessage = Utils_Global::$params['errMessage'];
        }
    }
    
    public function listAction() {
        $page = intval(Utils_Global::$params['page']);
        $limit = intval(Utils_Global::$params['limit']);
        if($limit <= 0) {
            $limit = 10;
        }
        if($page <= 0) {
            $page = 1;
        }
        
        $modelCenter = Admin_Model_Center::factory();
        $options = array('offset' => ($page - 1) * $limit, 'limit' => $limit);
        $centers = $modelCenter->getCenters($options);
        
        $this->view->centers = $centers;
        $this->view->title = "Tin tức";
        $this->view->page = $page;
        $this->view->numRowPerPage = $limit;
        $options = array();
        $this->view->totalItem = $modelCenter->getCentersCount($options);
        $this->view->currentUrl = $this->view->serverUrl() . $this->view->url(array());
    }
    
    public function saveAction() {
        $id = Utils_Global::$params['id'];
        $name = Utils_Global::$params['name'];
        $contactInfo = Utils_Global::$params['contact_info'];
        $address = Utils_Global::$params['address'];
        $city = Utils_Global::$params['city'];
        $cityCode = Utils_Global::$params['city_code'];
        $auth = Zend_Auth::getInstance();
        $userName = $auth->getIdentity();
        $userName = 'binhtv';
        if(!$userName) {
            Utils_Global::$params['errMessage'] = 'Vui lòng đăng nhập!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!preg_match("/^[^<>\'\"\/;`%@&#*?!~|]*$/", $name)) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên trung tâm hợp lệ!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$contactInfo) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập thông tin liên lạc!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$address) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập địa chỉ!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$city) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập city!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$cityCode) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập city code!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if($this->_request->isPost()) {
            $data = array();
            $data = array('name' => $name,
            		'contact_info' => $contactInfo,
                    'address' => $address,
                    'city' => $city,
                    'city_code' => $cityCode,
                    'last_update' => time(),
            );
            
            $result = $this->uploadTempImage('center');
            if(is_array($result) && $result) {//Luu thong tin upload
            	$data['image'] = $result['image'];
            	$data['hash_folder'] = $result['hash_folder'];
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
                
            //Save change or insert new
            $centerModel = Admin_Model_Center::factory();
            if($id) {
            	$result = $centerModel->update($id, $data);
            } else {
                if($data['image']) {
                	$data['dateline'] = time();
                	$result = $centerModel->insert($data);
                }
            }
            
        }
        if(!is_array($result) && $result > 0) {
        	$params = array('errMessage' => "Thay đổi thành công");
        	$this->_helper->redirector('edit', 'center', 'admin', $params);
        } else {
            Utils_Global::$params['errMessage'] = $errMessage;
            $this->_forward('edit', 'center', 'admin');
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

