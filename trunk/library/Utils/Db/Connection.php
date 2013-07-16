<?php
class Utils_Db_Connection
{
    /**
     * @return Utils_Db_Connection_Abstract
     */
    public static function factory ()
    {
        $config  = Utils_Config::getConfig();
		$adapter = $config->db->adapter;
        $adapter = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));
		$class 	 = 'Utils_Db_Connection_' . $adapter;
		if (!class_exists($class)) {
			throw new Exception('Does not support ' . $adapter . ' connection');
		}
		$instance = new $class($adapter);
		return $instance;
    }
}
?>