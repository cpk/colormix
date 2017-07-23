<?php

class ConfirmationNeededException extends Exception{
    public function __construct($message){
        parent::__construct($message);
    } 
}

?>