<?php
defined('DIRECT_ACCESS') or die('Direct access is not allowed');

class Database {
	private $_dbLink = NULL;
	private $_qryExc = NULL;
	private $_error = NULL;
	
	public function __construct(){
		$this->dbConnect();
	}
	
	private function dbConnect(){
		if($this->_dbLink)
			return true;
		
		$this->_dbLink = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		if($this->_dbLink) {
			if(!mysql_select_db(DB_NAME, $this->_dbLink)){
				$this->_error = 'Could not select database. MySql Said: '.mysql_error($this->_dbLink);
				return false;
			}
		}
		else {
			$this->_error = 'Could not connect to database. MySql Said: '.mysql_error($this->_dbLink);
			return false;
		}
		return true;
	}
	
	public function getLinkID(){
		return $this->_dbLink;
	}
	
	public function quote( $text ) {
		if ( get_magic_quotes_gpc() )
			$text = stripslashes($text);

		return '\'' . mysql_real_escape_string($text) . '\'';
	}
	
	public function query( $sql = NULL ) {
		if(!$sql) {
			$this->_error = 'Query was empty';
			return false;
		}
		
		$addSqlPrefix = str_replace('PRE_', DB_PREFIX, $sql);
		$this->_qryExc = mysql_query($addSqlPrefix, $this->_dbLink);
		if(!$this->_qryExc){
			$this->_error = 'There was an error in query. MySql Said: '.mysql_error($this->_dbLink);
			return false;
		}
		return true;			
	}
	
	public function numRows() {
		return mysql_num_rows($this->_qryExc);
	}
	
	public function getObjectField( $fieldName = NULL ) {
		if($fieldName) {
			$object = $this->_fetchObject();
			return $object->$fieldName;
		}
		return false;
	}
	
	public function getObjectRow(){
		return $this->_fetchObject();
	}
	
	public function error(){
		if($this->_error)
			return $this->_error;
			
		return false;
	}
	
	private function _fetchObject() {
		if($this->_qryExc)
			return mysql_fetch_object($this->_qryExc);
			
		return false;
	}
}