<?php
use Zend\Crypt\PublicKey\Rsa\PublicKey;
class Cms_Model_User
{
	private static $_instance;
	private static $_cacheTimeout = 300;
	
	private function __construct() {
	}
	
	/**
	 * @return Cms_Model_User
	 * */
	public static function factory() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get user by given username/email
	 * @param string $u
	 * @return array
	 * */
	public function getUser($u) {
	    $result = array();
	    if(!$u) {
	        return $result;
	    }
	    
	    $userDao = Cms_Model_DAO_User::factory();
	    try {
	        $options = array('username' => $u, 'email' => $u);
	        $result = $userDao->getUser($options);
	    } catch (Exception $exc) {
	        prBinh($exc);
	        Utils_Global::storeLog($exc, __FILE__, __LINE__);
	    }
	    
	    return $result;
	}
	
	/**
	 * Insert new user with provided information
	 * @param array $data
	 * @return true on success else false
	 * */
	public function insert($data) {
	    $result = 0;
	    if(!$this->isValid($data)) {
	        return $result;
	    }
	    try {
	        $userDao = Cms_Model_DAO_User::factory();
	        $user = $userDao->getUser(array('username' => $data['username'], 'email' => $data['email']));
	        if($user['username']) {
	            return -1;//User name or email is existed
	        }
	        
	        $result = $userDao->insert($data);	        
	    } catch (Exception $exc) {
	        prBinh($exc);
	        Utils_Global::storeLog($exc, __FILE__, __LINE__);
	    }
	    
	    return $result;
	}
	
	private function isValid($data) {
	    if(!$data['username'] || 
	        !$data['password'] ||
	        !$data['email'] ||
	        !$data['fullname'] ||
	        !$data['gender']) {
	        return false;
	    }
	    
	    return true;
	}
}