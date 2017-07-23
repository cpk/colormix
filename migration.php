
<?php

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

		 $data = $conn->select( "SELECT id FROM order_item ");

  		for($i=0 ; $i < count($data); $i++ ){
           $conn->update(
           	"UPDATE order_item SET order_subitem_color_cost = 
		    (
		      SELECT SUM(oi.quantity_kg * oi.price) 
		      FROM order_subitem oi
		      JOIN color c ON c.id = oi.id_color
		      WHERE id_order_item = ? AND c.color_type = 1
		    )
		    WHERE id = ?
		    LIMIT 1;"
           	, array( $data[$i]['id'], $data[$i]['id'] ));

           $conn->update(
           	"UPDATE order_item SET order_subitem_package_cost = 
		    (
		      SELECT SUM(oi.quantity_kg * oi.price) 
		      FROM order_subitem oi
		      JOIN color c ON c.id = oi.id_color
		      WHERE id_order_item = ? AND c.color_type in (2,3)
		    )
		    WHERE id = ?
		    LIMIT 1;"
           	, array( $data[$i]['id'], $data[$i]['id'] ));
      	 }
       echo "Number of updated: " . count($data);
	}catch(MysqlException $ex){
		die('<!DOCTYPE html><html><head><meta charset="utf-8" /></head><body>Vyskytol sa problém s databázou.</body><html>');
	}
	exit;

