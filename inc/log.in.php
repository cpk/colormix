<?php session_start();
	
    require_once  "../config.php";
    include_once  BASE_DIR."/inc/functions.php";
    function __autoload($class){
        $file = "../libs/".$class.".php";
            if(file_exists($file)) {
                require_once $file;
            }
    }


    try{
        $conn = Database::getInstance(SERVER, USER, PASS, DB_NAME);
        $auth = new Authenticate($conn);
        $auth->login($_GET['login'] ,$_GET['pass'], $_GET['token']);
        header("Location: ../");
    }catch(AuthException $e){
            $_SESSION['status'] = $e->getMessage();
    }catch(MysqlException $e){
            exit( "Vyskytol sa problém s databázou." );
    }
    exit;

		
?>