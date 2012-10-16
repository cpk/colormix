<?php

class MysqlException extends Exception{

    public function __construct($code, $message, $query = null){
        if($fp = fopen( dirname(__FILE__)."/../logs/log_mysql.txt" , 'a' )){
             $msgToLog = date("[Y-m_d H:i:s]"). " Code: $code -"." Message: $message".(isset ($query) ? " SQL: ".$query : "" )."\n";
             fwrite($fp, $msgToLog);
             fclose($fp);
        }
        parent::__construct( $message, $code);
    } 
}

?>