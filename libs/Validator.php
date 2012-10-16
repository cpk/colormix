<?php

class Validator{
    
    
    public static function isInteger(){}
            
            
    public static function isUsed($conn, $table, $colum, $val){
	$r =  $conn->select("SELECT `id` FROM `".$table."` WHERE `".$colum."`=? LIMIT 1", array( $val ));
	return (count( $r ) == 1 ? true : false );
    }
       
    
    /*
     * Check if has value in decimal format xx.xx
     * @param tested value
     * @param count of chars from dot, resp. coma 
     */
    public static function isFloat($number, $length = 4){
	return (preg_match ("/^[+]?(([0-9]+)|([0-9]+[\.,]{1}[0-9]{0,$length}))$/" ,$number) == 1) && $number >= 0;
    }



    public static function isPositiveInt($number, $min = 0, $max = 2){
            return (preg_match ("/^[0-9]{".$min.",".$max."}$/" ,$number) == 1);
    }


    /*
     * Check if ist value in date format dd.mm.yyy
     * @param date
     */
    public static function isDate($date){
	return (preg_match ("/^\d{4}\-\d{1,2}\-\d{1,2}$/" ,$date) == 1);

    }
    
    
   public static  function isEmail($email) {
        return (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", strtolower($email)) ? true : false);
   }
   
   
   
}

?>
