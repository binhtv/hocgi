<?php
class Cms_CourseController extends Zend_Controller_Action {
	public function indexAction() {
				
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
									$this->view->serverUrl() . $canonical);
		$this->view->category = Utils_Global::$params['id'];
		switch ($this->view->category) {
			case 7:
			    $this->view->khoaChuyenDe = 'current';
			    break;
			case 10:
			    $this->view->hocNgoaiNgu = 'current';
			    break;
			case 11:
			    $this->view->hocCaoHoc = 'current';
			    break;
			case 20:
			    $this->view->hocKinhTe = 'current';
			    break;
			case 12:
			    $this->view->duHoc = 'current';
			    break;
			case 13:
			    $this->view->keToan = 'current';
			    break;
			case 14:
			    $this->view->kinhDoanh = 'current';
			    break;
		}
	}
	
	public function listCourseAction() {
		$courses = array();
		$page = intval(Utils_Global::$params['page']);
		$limit = intval(Utils_Global::$params['limit']);
		$tuitionFrom = intval(Utils_Global::$params['from']);
		$tuitionTo = intval(Utils_Global::$params['to']);
		$name = Utils_Global::$params['name'];
		$city = Utils_Global::$params['city'];
		$category = Utils_Global::$params['id'];
		$tab = Utils_Global::$params['tab'];
		
		if($page < 1) {
			$page = 1;
		}
		if($limit <= 0){
			$limit = Utils_Global::getConfig('cms', 'site', 'coursePerPage');
		}
		$options = array('name' => $name, 'tuition_from' => $tuitionFrom,
						'tuition_to' => $tuitionTo, 'city' => $city);
		if($tab == 'new') {
		    $options['order'] = 'dateline';
		    $options['by'] = 'DESC';
		} else if($tab == 'requested') {
		    $options['promotion'] = 1;
		}
		
		$categoryModel = Cms_Model_Category::factory();
        $childCategories = $categoryModel->getCategoriesByOptions(array('parent' => $category));
		if(is_array($childCategories) && count($childCategories) > 0) {
		    $cids = array();
    	    foreach ($childCategories as $cat) {
    	      $cids[] = $cat['id'];
    	    }
		    $options['categories'] = implode(',', $cids);
		} else {
		    $options['category'] = $category;
		}
		$courseModel = Cms_Model_Course::factory();
		$courses = $courseModel->getCourses(($page - 1) * $limit, $limit, $options);
		$this->view->courses = $courses;
		$this->view->page = $page;
		$this->view->totalItem = $courseModel->getCourseCount($options);
		$this->view->numRowPerPage = $limit;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
		$this->view->readArticle = Utils_Global::getConfig('cms', 'site', 'readArticle');
		$this->view->name = $name;
		$this->view->tuitionFrom = $tuitionFrom;
		$this->view->tuitionTo = $tuitionTo;
		$this->view->city = $city;
		$this->view->tab = $tab;
		$this->view->category = $category;
		if(Utils_Global::get('isAjax')) {
			$this->_helper->layout()->disableLayout();
		}
	}
	
	public function detailCourseAction() {
		$courseId = Utils_Global::$params['id'];
		$courseModel = Cms_Model_Course::factory();
		$course = $courseModel->getCourseById($courseId);
		$this->view->course = $course;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
		$this->view->category = Utils_Global::$params['category'];
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
				$this->view->serverUrl() . $canonical);
		Utils_Global::$params['category'] = $course['category'];
		if(Utils_Global::get('isAjax')) {
		    $this->_helper->layout()->disableLayout();
		    $this->view->isAjax = 1;
		}
	}
	
	public function otherCourseAction() {
		$limit = intval(Utils_Global::$params['limit']);
		if($limit <= 0) {
			$limit = 10;
		}
		$courseModel = Cms_Model_Course::factory();
		$options = array('hot' => 1);
		$courses = $courseModel->getCourses(0, $limit, $options);
		$this->view->courses = $courses;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
				$this->view->serverUrl() . $canonical);
	}
	
	public function courseComparisonAction() {
		$limit = intval(Utils_Global::$params['limit']);
		$name = Utils_Global::$params['name'];
		$tuitionFrom = intval(Utils_Global::$params['from']);
		$tuitionTo = intval(Utils_Global::$params['to']);
		$name = Utils_Global::$params['name'];
		$city = Utils_Global::$params['city'];
		$category = Utils_Global::$params['category'];
		if($limit <= 0) {
			$limit = 7;
		}
		$courseModel = Cms_Model_Course::factory();
		$options = array('category' => $category, 'name' => $name, 'tuition_from' => $tuitionFrom,
						'tuition_to' => $tuitionTo, 'city' => $city); 
		$courses = $courseModel->getCourses(0, $limit, $options);
		$this->view->courses = $courses;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
		$this->view->currentId = Utils_Global::$params['id'];
		$this->view->category = $category;
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
				$this->view->serverUrl() . $canonical);
		$this->view->content = $this->view->render('course/course-comparison-content.phtml');
		if(Utils_Global::get('isAjax')) {
			$this->_helper->layout()->disableLayout();
			$this->render('course-comparison-content');
		}
	}
	
	public function twoCourseAction() {
		$courseId = Utils_Global::$params['id'];
		$courseId2 = Utils_Global::$params['id2'];
		$courseModel = Cms_Model_Course::factory();
		$course = $courseModel->getCourseById($courseId);
		$course2 = $courseModel->getCourseById($courseId2);
		$this->view->course = $course;
		$this->view->course2 = $course2;
		$this->view->staticUrl = Utils_Global::get('staticUrl') . '/course/';
		$this->view->metadata = $this->view->metadata('home', array('keyword' => $this->view->keyword),
				$this->view->serverUrl() . $canonical);
		if(Utils_Global::get('isAjax')) {
			$this->_helper->layout()->disableLayout();
		}
	}
}
