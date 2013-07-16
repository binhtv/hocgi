<?php
class Admin_BannerController extends Zend_Controller_Action
{
    private $_config;
    private $_categories;
	protected $_models = array();
	
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->bannerclzz = 'active';
    }  
    
    public function deleteAction()
    {
		try{
			$model = $this->_getModel('Banner');		  
			$positionid = (int)$this->getRequest()->getParam('positionid',0);			
			if($this->_request->isPost()){
				$item = $this->_request->getPost('item');
				if(is_array($item) && !empty($item)){
					foreach($item as $id){ 
						$model->deleteBanner($id);
					}
				}		
			}
			else{
				$id = (int)$this->getRequest()->getParam('id',0);	
				if(!empty($id)){
					 $model->deleteBanner($id);
				}			
			}
			$this->_redirect("/admin/banner/list/positionid/{$positionid}");	
			return;				
		}
		catch(Exception $e){
			pr($e->getMessage());
		}        
    }
    
    public function listAction() {      
		  $positionId = Utils_Global::$params['position'];
		  $errMessage = Utils_Global::$params['errMessage'];
		  if(!$positionId) {
		      $this->_redirect('/admin/position/list');
		  }
		  
		  $bannerModel = Admin_Model_Banner::factory();
		  $banners = $bannerModel->getBannerByPosition(array('positionId' => $positionId));
		  $this->view->banners = $banners;
		  $this->view->staticUrl = Utils_Global::get('staticUrl') . '/banner/';
		  $this->view->positionId = $positionId;
		  $this->view->errMessage = $errMessage;
    }
    
    public function editAction() {
        $positionId = intval(Utils_Global::$params['position']);
        $bannerId = intval(Utils_Global::$params['id']);
        if(!$positionId) {
            $this->_redirect('/admin/position/list');
        }
        
        if($bannerId) {
            $bannerModel = Admin_Model_Banner::factory();
            $banner = $bannerModel->getBanners(array('id' => $bannerId));
            $this->view->banner = $banner;
            $this->view->title = "Chỉnh sửa banner";
            $this->view->positionId = $positionId;
            $this->view->errMessage = Utils_Global::$params['errMessage'];
            if($this->view->errMessage) {
                $this->view->banner = Utils_Global::$params;
            }
        } else {
            $this->view->banner = Utils_Global::$params;
            $this->view->title = "Tạo banner mới";
            $this->view->positionId = $positionId;
            $this->view->errMessage = Utils_Global::$params['errMessage'];
        }
    }
	
	/**
	 * method: update layout information
	 * Description: update layout
	 */
	 public function saveAction() {
	     $positionId = intval(Utils_Global::$params['position']);
	     $bannerId = intval(Utils_Global::$params['id']);
	     $name = Utils_Global::$params['name'];
	     $link = Utils_Global::$params['link'];
	     $width = Utils_Global::$params['width'];
	     $height = Utils_Global::$params['height'];
	     $video = Utils_Global::$params['video'];
	     $flash = Utils_Global::$params['flash'];
	     $active = Utils_Global::$params['active'];
	     $note = Utils_Global::$params['note'];
	     if($video) {
	         $video = 1;
	     }
	     if($flash) {
	         $flash = 1;
	     }
	     if($active) {
	         $active = 1;
	     }
	     
	     if(!preg_match("/^[^<>\'\"\/;`%@&#*?!~|]*$/", $name)) {
	         Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên banner hợp lệ.';
	         $this->_forward('edit', 'banner', 'admin'); 
	         return;
	     }
	     if(!$link) {
	         Utils_Global::$params['errMessage'] = 'Vui lòng nhập link hợp lệ.';
	         $this->_forward('edit', 'banner', 'admin');
	         return;
	     }
	     if(!$width || !$height) {
	         Utils_Global::$params['errMessage'] = 'Vui lòng nhập width/height hợp lệ.';
	         $this->_forward('edit', 'banner', 'admin');
	         return;
	     }
	     
	     //Check so lan upload lan nua
	     if($this->_request->isPost()) {
            $data = array('name' => $name,
            		'link' => $link,
            		'width' => $width,
            		'height' => $height,
            		'video' => $video,
                    'flash' => $flash,
            		'active' => $active,
                    'note' => $note,
            		'last_update' => time(),
                    'position' => $positionId,
            );
            
            $result = $this->uploadTempImage('banner');
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
            $bannerModel = Admin_Model_Banner::factory();
            if($bannerId) {
            	$result = $bannerModel->update($bannerId, $data);
            } else {
                if($data['image']) {
                	$data['dateline'] = time();
                	$result = $bannerModel->insert($data);
                }
            }
	     }
	     
	     if(!is_array($result) && $result > 0) {
        	$params = array('errMessage' => "Thay đổi thành công", 'position' => $positionId);
        	$this->_helper->redirector('list', 'banner', 'admin', $params);
        } else {
            Utils_Global::$params['errMessage'] = $errMessage;
            $this->_forward('edit', 'banner', 'admin');
            return;
        }
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