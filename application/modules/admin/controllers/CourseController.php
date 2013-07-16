<?php

class Admin_CourseController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->courseclzz = 'active';
    }
    
    public function editAction() {
        $id = trim(Utils_Global::$params['id']);
        $categoryModel = Admin_Model_Category::factory();
        $categories = $categoryModel->getCategories(array('for_course' => 1));
        $centerModel = Admin_Model_Center::factory();
        $centers = $centerModel->getCenters();
        if($id) {//Edit
            $courseModel = Admin_Model_Course::factory();
            $course = $courseModel->getCourses(array('id' => $id));
            $this->view->course = $course;
            $this->view->categories = $categories;
            $this->view->centers = $centers;
            $this->view->id = $id;
            $this->view->title = "Chỉnh sửa khóa học";
            $this->view->errMessage = Utils_Global::$params['errMessage'];
            if($this->view->errMessage) {
            	$this->view->course = Utils_Global::$params;
            }
        } else {//Insert
            $this->view->course = Utils_Global::$params;
            $this->view->categories = $categories;
            $this->view->centers = $centers;
            $this->view->title = "Tạo khóa học mới";
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
        
        $modelCourse = Admin_Model_Course::factory();
        $options = array('offset' => ($page - 1) * $limit, 'limit' => $limit);
        $courses = $modelCourse->getCourses($options);
        
        $this->view->courses = $courses;
        $this->view->title = "Tin tức";
        $this->view->page = $page;
        $this->view->numRowPerPage = $limit;
        $options = array();
        $this->view->totalItem = $modelCourse->getCoursesCount($options);
        $this->view->currentUrl = $this->view->serverUrl() . $this->view->url(array());
    }
    
    public function saveAction() {
        $id = Utils_Global::$params['id'];
        $name = Utils_Global::$params['name'];
        $nameSeo = Utils_CommonFunction::getNameSeo($name);
        $link = Utils_Global::$params['course_link'];
        $content = Utils_Global::$params['content'];
        $tuition = Utils_Global::$params['tuition'];
        $order = Utils_Global::$params['order'];
        $best = trim(Utils_Global::$params['best']);
        $hot = trim(Utils_Global::$params['hot']);
        $promotion = trim(Utils_Global::$params['promotion']);
        $active = trim(Utils_Global::$params['active']);
        $category = Utils_Global::$params['category'];
        $centerId = Utils_Global::$params['center_id'];
        $openingDate = Utils_Global::$params['opening_date'];
        if($hot) {
            $hot = 1;
        }
        if($best) {
            $best = 1;
        }
        if($promotion) {
            $promotion = 1;
        }
        if($active) {
            $active = 1;
        }
        $auth = Zend_Auth::getInstance();
        $userName = $auth->getIdentity();
        $userName = 'binhtv';
        if(!$userName) {
            Utils_Global::$params['errMessage'] = 'Vui lòng đăng nhập!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$nameSeo) {
        	Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên khóa học hợp lệ!';
        	$this->_forward('edit', 'course', 'admin');
        	return ;
        }
        
        if(!preg_match("/^[^<>\'\"\/;`%@&#*?!~|]*$/", $name)) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên khóa học hợp lệ!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$link) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập course link!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$openingDate) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập ngày khai giảng!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$content) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập nội dung khóa học!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if(!$tuition) {
            Utils_Global::$params['errMessage'] = 'Vui lòng nhập học phí!';
            $this->_forward('edit', 'course', 'admin');
            return;
        }
        if($this->_request->isPost()) {
            $data = array();
            $data = array('name' => $name,
            		'name_seo' => $nameSeo,
            		'content' => $content,
                    'tuition' => $tuition,
                    'opening_date' => strtotime($openingDate),
                    'course_link' => $link,
            		'category' => $category,
                    'center_id' => $centerId,
            		'last_update' => time(),
                    'active' => $active,
            		'order' => $order,
            		'hot' => $hot,
                    'best' => $best,
                    'promotion' => $promotion,
            );
            $result = $this->uploadTempImage('course');
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
            $courseModel = Admin_Model_Course::factory();
            if($id) {
            	$result = $courseModel->update($id, $data);
            } else {
                if($data['image']) {
                	$data['dateline'] = time();
                	$result = $courseModel->insert($data);
                }
            }
            
        }
        if(!is_array($result) && $result > 0) {
        	$params = array('errMessage' => "Thay đổi thành công");
        	$this->_helper->redirector('edit', 'course', 'admin', $params);
        } else {
            Utils_Global::$params['errMessage'] = $errMessage;
            $this->_forward('edit', 'course', 'admin');
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

