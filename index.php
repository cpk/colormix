<?php
	session_start();
	
	ini_set("display_errors", 0);
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
                    if(isset($_GET['p']) && $_GET['p'] == "print")
                        include_once  BASE_DIR."/inc/print.".$_GET['doc'].".php";
                    else  
                        include_once  BASE_DIR."/inc/main.index.php";
                }else{
                    include_once  BASE_DIR."/inc/login.page.php";
                }
	}catch(MysqlException $ex){
		exit("Vyskytol sa problém s databázou." );
	}
	exit;
