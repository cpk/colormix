<?php

function getOptions( $conn, $table, $colum, $first = 0, $skip = NULL){
	$html = "";
	$array =  $conn->select("SELECT `id`, `$colum` FROM `$table`");	
	$c = count($array); 
	
	for($j=0; $j < $c;$j++){
		if($array[$j]["id"] == $first){
			$html .= "<option value=\"".$array[$j]["id"]."\">".$array[$j]["$colum"]."</option>\n";
			break;
		}
	}	
	
	
	for($j=0; $j < $c;$j++) {   
		if($array[$j]["id"] == $first || $array[$j]["id"] == $skip){ continue; }
			 $html .= "<option value=\"".$array[$j]["id"]."\">".$array[$j]["$colum"]."</option>\n";
	}   
	return $html;
}


function getColorOptions( $conn){
	$html = '<option value="0">-- Vyberte materiál -- </option>';
	$array =  $conn->select("SELECT c.`id`, c.`name`, c.`code`, u.`unit`, c.`price`  
                                 FROM `color` c, `measurement` u 
                                 WHERE c.`id_measurement`=u.`id`
                                 ORDER BY `code`");	
	$c = count($array); 
	
	for($j=0; $j<$c; $j++) {   
            $html .= "<option value=\"".$array[$j]["id"]."\">".
                    $array[$j]["code"]." &nbsp; | &nbsp; ".$array[$j]["name"]." &nbsp; | &nbsp; ".
                    floatval($array[$j]["price"]) ." &euro; / ".$array[$j]["unit"]."</option>\n";
	}   
	return $html;
}

function number_clean($num){ 
    //remove zeros from end of number ie. 140.00000 becomes 140.
    $clean = rtrim($num, '0');
    //remove zeros from front of number ie. 0.33 becomes .33
    $clean = ltrim($clean, '0');
    //remove decimal point if an integer ie. 140. becomes 140
    $clean = rtrim($clean, '.');
    
    return $clean;
}

function isEmail($email){
	return (preg_match ("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i" ,$email) == 1);
}
?>
