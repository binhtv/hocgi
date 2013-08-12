<?php
class Cms_AuthController extends Zend_Controller_Action {
    public function init() {
        //Do something here
    }
    
	public function loginAction() {
	    $userName = trim(Utils_Global::$params['username']);
	    $password = trim(Utils_Global::$params['password']);
	    $password = md5($password);
	    $modelUser = Cms_Model_User::factory();
	    $user = $modelUser->getUser($userName);
	    if($user['password'] === $password && $user['active']) {//Login success
	        $_SESSION['userid'] = $user['id'];
	        $_SESSION['username'] = $user['username'];
	        $_SESSION['fullname'] = $user['fullname'];
	        $result = array('code' => 1, 'data' => array('username' => $user['username'], 'fullname' => $user['fullname']));
	        $this->_helper->json($result);
	    } else if($user['username']&&!$user['active']) {
	        $result = array('code' => -1);
	        $this->_helper->json($result);
	    } else {
	    
	        $result = array('code' => 0);
	        $this->_helper->json($result);
	    }
	}
	
	public function logoutAction() {
	    session_destroy();
	    $this->_helper->json(1);
	}
	
	public function checkLoginAction() {
	    if($_SESSION['username']) {//Already login
	        $result = array('code' => 1, 'data' => array('username' => $_SESSION['username'], 'fullname' => $_SESSION['fullname']));
	        $this->_helper->json($result);
	    } else {
	        $result = array('code' => 0);
	        $this->_helper->json($result);
	    }
	}
	
	public function registerAction() {
	    $username = trim(Utils_Global::$params['username']);
	    $email = trim(Utils_Global::$params['email']);
	    $password = trim(Utils_Global::$params['password']);
	    $fullName = Utils_Global::$params['fullname'];
	    $gender = Utils_Global::$params['gender'];
	    $day = Utils_Global::$params['day'];
	    $month = Utils_Global::$params['month'];
	    $year = Utils_Global::$params['year'];
	    
	    $dayofbirth = strtotime($year . '-' . $month . '-' . $day);
	    $data = array('username' => $username, 'email' => $email,
	                    'password' => md5($password), 'fullname' => $fullName,
	                    'gender' => $gender, 'birthday' => strtotime($dayofbirth),
	                    'active' => 1,
	    );

	    $modelUser = Cms_Model_User::factory();
	    $result = $modelUser->insert($data);
	    if($result > 0) {
	        $data = array('code' => 1, 'data' => array('username' => $username, 'fullname' => $fullName));
	        //Auto login
	        $_SESSION['userid'] = $result;
	        $_SESSION['username'] = $username;
	        $_SESSION['fullname'] = $fullName;
	        
	        $this->_helper->json($data);
	    } else {
	        $data = array('code' => $result, 'data' => array());
	        $this->_helper->json($data);
	    }
	}
}
