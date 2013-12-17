<?php
class BaseModelSession {
	protected static $instance = NULL;
    private function __construct() {
        return;
    }
	public function __init() {
		@session_start();
	}
	public static function &getInstance(){
		if( !self::$instance ) {
			self::$instance = new BaseModelSession;
			self::$instance->__init();
		}
		return self::$instance;
	}
	public static function setName() {
		$_this = &BaseModelSession::getInstance();
		return session_name();
	}
	public static function getSid()	{
		$_this = &BaseModelSession::getInstance();
		return session_id();
	}
	public static function get($key=NULL) {
		$_this = &BaseModelSession::getInstance();
        $session_val = isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        return (NULL === $key)? $_SESSION : $session_val;
	}
	public static function set($key,$value=NULL) {
		$_this = &BaseModelSession::getInstance();
		if( NULL === $value ) {
			unset($_SESSION[$key]);
		}
		$_SESSION[$key] = $value;
		return TRUE;
	}
	public static function destroy() {
		self::$instance = NULL;
		return session_destroy();
	}
}
?>
