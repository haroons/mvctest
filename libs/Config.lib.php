<?php
defined('DIRECT_ACCESS') or die('Direct access is not allowed');

class Config {
	private $_dbObj;
	
	public function __construct(){
		$this->_dbObj = $GLOBALS['dbObj'];
	}
	
	public function getValue( $variable = NULL ) {
		if(!$variable)
			return false;
			
		$sql = 'SELECT value FROM PRE_configs WHERE variable = "'.$variable.'"';
		$this->_dbObj->query($sql);
		
		if($this->_dbObj->error())
			die($this->_dbObj->error());
		else
			return $this->_dbObj->getObjectField('value');
	}
}