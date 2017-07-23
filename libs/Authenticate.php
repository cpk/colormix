<?php

class Authenticate 
{
	private $conn;
	private $expiration = 9600;
	
	public function __construct($conn){
		$this->conn = $conn;
	}
	
	public function login($login, $pass, $token){
		if(session_id() !=  $token){
			throw new AuthException("Platnosť formulára vypršala.");	
		}	
			
		$data = $this->conn->select("SELECT `id_user`,`id_user_type`, `login`, `pass`, `salt` FROM `user` WHERE `login`=? AND `active`=1 AND `blocked`=0 LIMIT 1", array ( $login ) );
		if(count($data) == 1){
                    if(	hash_hmac( 'sha256', $pass , $data[0]['salt']) == $data[0]['pass']){
                        $_SESSION = array();
                        session_regenerate_id();
                        $_SESSION['login'] 	= $data[0]['login'];
                        $_SESSION['id'] 	= $data[0]['id_user'];
                        $this->deleteSessionById($data[0]['id_user']);
                        $this->newSession($data[0]['id_user']);
                        if($data[0]['id_user_type'] != 5){
                                $this->logEntry($data[0]['id_user']);
                        }
                        return true;
                    }else{
                        throw new AuthException("Neplatné uživateľské heslo." );	
                    }
		}else{
			throw new AuthException("Neplaté uživateľské meno." );	
		}
		return false;
	}
	
	
	
	public function isLogined(){
		if(!isset($_SESSION['id'])){ 
			return false;
		}
		if(count( $this->getSession($_SESSION['id'])) != 1){
			return false;
		}
		$this->updateSession($_SESSION['id']);	
		return true;
	}
	
	public function logout(){
		$this->deleteOldSessions();
		$_SESSION = array();
		session_destroy();
	}
	
	
	
	public function logEntry($id){
		$this->conn->insert("INSERT INTO `user_log` (`id_user`, `user_agent`, `ip`, `time`) VALUES (?,?,?,?)", array($id, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], time()));
	}
	
	
	private function getHash(){
		if($_SESSION['login'] == "demo"){
			return 1;
		}
		return md5( $_SERVER['REMOTE_ADDR']. "*" .$_SERVER['HTTP_USER_AGENT'] );
	}
	
	
	private function newSession($uid){
		$this->conn->insert("INSERT INTO `user_session` (`id_user`, `session`, `time`) VALUES (? , ? , ?)", array( $uid, $this->getHash(), time() ));	
	}
	
	
	private function getSession($uid){
		return $this->conn->select("SELECT `session` FROM `user_session` WHERE `id_user`=? AND `time`>? AND `session`=? LIMIT 1", array( $uid, time() - $this->expiration, $this->getHash() ));	
	}
	
	
	private function deleteOldSessions(){
		$this->conn->insert("DELETE FROM `user_session` WHERE `time`<?", array( time() - $this->expiration ));	
	}
	
	private function deleteSessionById($uid){
		$this->conn->insert("DELETE FROM `user_session` WHERE `id_user`=? LIMIT 1	", array($uid));	
	}	
	
	private function updateSession($uid){
		$this->conn->update("UPDATE `user_session` SET `time`=".time()." WHERE `id_user`=? LIMIT 1", array( $uid ));	
	}
	
	


}
?>