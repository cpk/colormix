<?php
	session_start();
	
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', dirname(__FILE__).'/logs/php_errors.txt');
	
        
    require_once  "./config.php";
    include_once  BASE_DIR."/inc/functions.php";
	function __autoload($class){
        $file = "./libs/".$class.".php";
            if(file_exists($file)) {
                require_once $file;
            }
	}
        
	try{
		$conn = Database::getInstance(SERVER, USER, PASS, DB_NAME);
        $auth = new Authenticate($conn);
        if($auth->isLogined()){
        	switch ($_GET['page']) {
        		case 'totals':
        			include_once  BASE_DIR."/view/order.view.totals.php";
        		default:
        			
        	}
        }
	}catch(MysqlException $ex){
		die('<!DOCTYPE html><html><head><meta charset="utf-8" /></head><body>Vyskytol sa problém s databázou.</body><html>');
	}
	exit;
