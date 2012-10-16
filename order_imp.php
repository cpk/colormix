<?php


	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', dirname(__FILE__).'/logs/php_errors.txt');
	
	function __autoload($class){
		require_once "./libs/".$class.".php";
	}
        
	require_once "./config.php";
	
	try{
		$conn = Database::getInstance(SERVER, USER, PASS, DB_NAME);

	
        $parsed = array();
        for($i = 0 ; $i < 100; $i++){
            $parsed[] = "('".rand(1,100)."', '2012-".rand(1,12)."-".rand(1,29)."' )";
            
        }
	

	$q = "INSERT INTO `order` (`id_customer`, `date`) VALUES ".	
	implode(",", $parsed);

	$conn->simpleQuery($q);
	echo "Succesfully imported.";
}catch(MysqlException $ex){
exit( "Vyskytol sa problém s databázou." );
}
exit;
        
	
?>