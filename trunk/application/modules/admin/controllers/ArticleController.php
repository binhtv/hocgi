<?php

class Admin_ArticleController extends Zend_Controller_Action
{
    private $_config = null;
    public function init()
    {
        $this->_config = Utils_Global::getConfig('admin', 'upload');
        $this->view->articleclzz = 'active';
    }
    
    public function editAction() {
        $id = trim(Utils_Global::$params['id']);
        $categoryModel = Admin_Model_Category::factory();
        $categories = $categoryModel->getCategories();
        if($id) {//Edit
            $articleModel = Admin_Model_Article::factory();
            $article = $articleModel->getArticles(array('id' => $id)); 
            $this->view->article = $article;
            $this->view->categories = $categories;
            $this->view->id = $id;
            $this->view->title = "Chỉnh sửa tin tức";
            $this->view->errMessage = Utils_Global::$params['errMessage'];
            if($this->view->errMessage) {
            	$this->view->article = Utils_Global::$params;
            }
        } else {//Insert
            $this->view->article = Utils_Global::$params;
            $this->view->categories = $categories;
            $this->view->title = "Tạo tin tức mới";
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
        
        $modelArticle = Admin_Model_Article::factory();
        $options = array('offset' => ($page - 1) * $limit, 'limit' => $limit);
        $articles = $modelArticle->getArticles($options);
        
        $this->view->articles = $articles;
        $this->view->title = "Tin tức";
        $this->view->page = $page;
        $this->view->numRowPerPage = $limit;
        $options = array();
        $this->view->totalItem = $modelArticle->getArticlesCount($options);
        $this->view->currentUrl = $this->view->serverUrl() . $this->view->url(array());
    }
    
    public function saveAction() {
        $id = Utils_Global::$params['id'];
        $title = Utils_Global::$params['title'];
        $author = Utils_Global::$params['author'];
        $shortDescription = Utils_Global::$params['short_description'];
        $content = Utils_Global::$params['content'];
        $order = Utils_Global::$params['order'];
        $titleSeo = Utils_CommonFunction::getNameSeo($title);
        $top = intval(trim(Utils_Global::$params['top']));
        $category = Utils_Global::$params['category'];
        $active = trim(Utils_Global::$params['active']);
        $hot = trim(Utils_Global::$params['hot']);
        
        if($active) {
            $active = 1;
        }
        if($hot) {
            $hot = 1;
        }
        $auth = Zend_Auth::getInstance();
        $userName = $auth->getIdentity();
        $userName = 'binhtv';
        if(!$userName) {
            Utils_Global::$params['errMessage'] = 'Vui lòng đăng nhập!';
            $this->_forward('edit', 'article', 'admin');
            return;
        }
        if(!Utils_CommonFunction::getNameSeo($title)) {
        	Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên bài hợp lệ!';
        	$this->_forward('edit', 'article', 'admin');
        	return;
        }
        
//         if(!preg_match("/^[^<>\'\"\/;`%@&#*?!~|]*$/", $title)) {
//             Utils_Global::$params['errMessage'] = 'Vui lòng nhập tên bài hợp lệ!';
//             $this->_forward('edit', 'article', 'admin');
//             return;
//         }
        if($this->_request->isPost()) {
            $data = array();
            $data = array('editor' => $userName,
            		'title' => $title,
            		'title_seo' => $titleSeo,
            		'author' => $author,
            		'short_description' => $shortDescription,
            		'content' => $content,
            		'category' => $category,
            		'last_update' => time(),
                    'active' => $active,
            		'order' => $order,
            		'top' => $top,
                    'hot' => $hot,
            );
            
            $result = $this->uploadTempImage('article');
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
            $articleModel = Admin_Model_Article::factory();
            if($id) {
            	$result = $articleModel->update($id, $data);
            } else {
                if($data['image']) {
                	$data['dateline'] = time();
                	$result = $articleModel->insert($data);
                }
            }
            
        }
        
        if(!is_array($result) && $result > 0) {
        	$params = array('errMessage' => "Thay đổi thành công");
        	$this->_helper->redirector('edit', 'article', 'admin', $params);
        } else {
            Utils_Global::$params['errMessage'] = $errMessage;
            $this->_forward('edit', 'article', 'admin');
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
    			if(!$files['file']['name'] || !preg_match('/jpg|jpeg|gif|png|bmp/', strtolower($files['file']['name']))) {
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

