<?php

class BlockManagement_Boxes extends BlockManagement_Abstract
{
    private $_tablename = 'cms_boxes';
	private $_module;
    const KEY = 'boxes.boxid.%s'; //boxes.boxid.[id]
    const KEY_LIST = 'boxes.list';
    private static $_instance = null;

    function __construct()
    {
		$front = Zend_Controller_Front::getInstance();
		$this->_module = strtolower($front->getRequest()->getParam('module'));
	}

    /**
     * get instance of BlockManagement_Boxes
     *
     * @return BlockManagement_Boxes
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getKey($boxid)
    {
        return sprintf(self::KEY, $boxid);
    }

    public function getKeyList()
    {
        return self::KEY_LIST;
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection()
    {
        $db = Utils_Global::getDbInstance($this->_module);
        return $db;
    }

    function getCacheInstance()
    {
        $cache = Utils_Global::getCacheInstance($this->_module);
        return $cache;
    }

    public function getList()
    {
        $key = $this->getKeyList();
        $cache = $this->getCacheInstance();
        $result = false;
        if(is_object($cache)) {
        	$result = $cache->getCache($key);
        }
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " ORDER BY boxname";
            $result = $db->fetchAll($query);
            if (! is_null($result) && is_array($result) && is_object($cache)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getBox($boxid)
    {
        $key = $this->getKey($boxid);
        $cache = $this->getCacheInstance();
        $result = false;
        if(is_object($cache)) {
        	$result = $cache->getCache($key);
    	}
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename . " WHERE boxid = ?";
            $data = array(
                        $boxid
            );
            $result = $db->fetchAll($query, $data);	

            if (! is_null($result) && is_array($result) && is_object($cache)) {
                $cache->setCache($key, $result);
            }
		}
        if ($result != null && is_array($result) && count($result) > 0) {
            $result[0] = str_replace('{BASE_URL}', Utils_Global::get('baseUrl'), $result[0]);
            $result[0] = str_replace('{IMG_URL}', Utils_Global::get('imgUrl'), $result[0]);
			$result[0] = str_replace('{WEB_URL}', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], $result[0]);
			$result[0] = str_replace('{CUR_TIME}', date('D, d-m-Y h:i:s', time()), $result[0]);
			$result[0] = str_replace('{STATIC_VERSION}', Utils_Global::getConfig('cms', 'site', 'staticVersion'), $result[0]);
            return $result[0];
        } else
            return null;
    }

    public function updateBox($boxid, $data)
    {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "boxid='" . parent::adaptSQL($boxid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            //xoa cache
            $cache = $this->getCacheInstance();
            if(is_object($cache)) {
	            $key = $this->getKey($boxid);
	            $cache->deleteCache($key);
	            $key = $this->getKeyList();
	            $cache->deleteCache($key);
            };
            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function insertBox($data)
    {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        //xoa cache list
        $cache = $this->getCacheInstance();
        if(is_object($cache)) {
	        $key = $this->getKeyList();
	        $cache->deleteCache($key);
        }
        return $result;
    }

    public function deleteBox($boxid)
    {
        $block = $this->getBox($boxid);
        if ($block == null)
            return;
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "boxid='" . parent::adaptSQL($boxid) . "'";
        $result = $db->delete($this->_tablename, $where);
        $cache = $this->getCacheInstance();
        if(is_object($cache)) {
	        $key = $this->getKey($boxid);
	        $cache->deleteCache($key);
	        $key = $this->getKeyList();
	        $cache->deleteCache($key);
        }
    }
    
    public function getListSearch(array $options = null)
    {
        $result = false;
    	$key = $this->getKeyList();
    	if(!empty($options)){
    		if(!empty($options['name']) || !empty($options['content'])){
    			$key .= md5($options['name'] . '_' . $options['content']);
    		}
    	}
    	$cache = $this->getCacheInstance();
    	if(is_object($cache)) {
    	    $result = $cache->getCache($key);
    	}
    	if($result === FALSE)
    	{
    		$db = $this->getDbConnection();
    		$select = "select * from " . $this->_tablename;
    		$where = " where 1 = 1 ";
    		if(!empty($options)){
    			if(!empty($options['name'])){
    				$oName = $options['name'];
    				$where .= " AND boxname LIKE '%{$oName}%'";
    			}if(!empty($options['content'])){
    				$oContent = $options['content'];
    				$where .= " AND content LIKE '%{$oContent}%'";
    			}
    		}
    		$order = " ORDER BY boxname";
    		$query = $select . $where . $order;
    		//var_dump($query);
    		$result = $db->fetchAll($query);
    
    		if(!is_null($result) && is_array($result) && is_object($cache))
    		{
    			$cache->setCache($key,$result);
    		}
    	}
    	return $result;
    }
}
?>