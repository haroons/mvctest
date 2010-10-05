<?php
defined('DIRECT_ACCESS') or die('Direct access is not allowed');

define('SESSION_TYPE'		, 'user');
define('GC_MAXLIFETIME'		, 14400);
define('GC_PROBABILITY'		, 1);
define('USE_COOKIES'		, 1);
define('SESSION_NAME'		, 'SLBoX');
define('USE_ONLY_COOKIES'	, 0);
define('SECURITY_CODE'		, 'SLBox');

class Session
{
	private $dbObj;
	private $db_link;
	private $gc_maxlifetime;

	public function __construct()
	{
		$this->dbObj =& $GLOBALS['dbObj'];
		$this->db_link = $this->dbObj->getLinkID();
	
		@ini_set('session.name', SESSION_NAME);
		@ini_set('session.use_cookies', (int)USE_COOKIES);
		@ini_set('session.gc_maxlifetime', (int)GC_MAXLIFETIME);
		@ini_set('session.gc_probability', (int)GC_PROBABILITY);
		@ini_set('session.use_only_cookies', (int)USE_ONLY_COOKIES);
		@ini_set('session.save_path', TMP_DIR. '/sessions');
		
		if ( SESSION_TYPE == 'user' ) {
			@ini_set('session.save_handler', 'user');
			@session_set_save_handler( array( &$this, 'open' ),
			                           array( &$this, 'close' ),
						   array( &$this, 'read' ),
						   array( &$this, 'write' ),
						   array( &$this, 'destroy' ),
						   array( &$this, 'gc' ) );
		}
		
		session_start();
	}
	
	public function &getInstance()
	{
		$sessObj	=& $GLOBALS['sessObj'];
		if( !is_object($GLOBALS['sessObj']) )
			$sessObj = new Session();
		
		return $sessObj;
	}
	
	public function open( $save_path, $session_name )
	{
		return true;
	}
	
	public function close()
	{
		return true;
	}
	
	public function read( $session_id )
	{
		$query = sprintf("SELECT session_data FROM PRE_sessions
		                  WHERE session_id = %s AND session_ip = %s
				  AND session_hijack = %s LIMIT 1;",
				  $this->dbObj->quote($session_id),
				  $this->dbObj->quote($_SERVER['REMOTE_ADDR']),
				  $this->dbObj->quote(md5($_SERVER['HTTP_USER_AGENT'] . SECURITY_CODE)));
		$this->dbObj->query($query);
		if( $this->dbObj->numRows() ) {
			$record = $this->dbObj->getObjectField('session_data');
			return $record;
		}
		
		return '';
	}
	
	public function write( $session_id, $session_data )
	{
		return $this->dbObj->query(sprintf("REPLACE INTO PRE_sessions ( session_id, session_ip, session_hijack, session_browser, session_data, session_expire )
		                                    VALUES ( %s, %s, %s, %s, %s, '%d' );",
						    $this->dbObj->quote($session_id),
						    $this->dbObj->quote($_SERVER['REMOTE_ADDR']),
						    $this->dbObj->quote(md5($_SERVER['HTTP_USER_AGENT'] . SECURITY_CODE)),
						    $this->dbObj->quote($_SERVER['HTTP_USER_AGENT']),
						    $this->dbObj->quote($session_data),
						    time()));
	}
	
	public function destroy( $session_id )
	{
		return $this->dbObj->query(sprintf("DELETE FROM PRE_sessions WHERE session_id < %s;", $this->dbObj->quote($session_id)));
	}
	
	public function gc( $max_lifetime )
	{
		return $this->dbObj->query(sprintf("DELETE FROM PRE_sessions WHERE session_expire < '%d';", time() - $max_lifetime));
	}
	
	public function regenerateID()
	{
		$old_session_id = session_id();
		session_regenerate_id();
		$this->destroy( $old_session_id );
	}
	
	public function getOnlineUsers()
	{
		$this->gc(GC_MAXLIFETIME);
		$this->dbObj->query("SELECT count(session_id) AS online_users FROM PRE_sessions");
		if ( $this->dbObj->affectedRows() )
			return $this->dbObj->fetchField('online_users');
		
		return false;
	}
}
?>
