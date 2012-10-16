<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Converter
 *
 * @author Peto
 */
class Converter {

    private static $KG_UNIT = 1;
    
    private static $G_UNIT = 2;
    
    private static $KS_UNIT = 3;
    
    private static $ML_UNIT = 4;
    
       
    public static function gramsToKilorams($gVal){
        return $gVal / 1000;
    }
    
     public static function mililitersToKilorams($mlVal){
        return $mlVal * 1000;
    }
    
    
    public static function convert($idMeasurenebt, $val){
        switch ($idMeasurenebt){
            case self::$G_UNIT :
                return self::gramsToKilorams($val);
             break;
            case self::$KS_UNIT :
            case self::$KG_UNIT :
                return $val;
             break;
            case self::$ML_UNIT :
                return $this->mililitersToKilorams($val);
             break;
             default : 
                 throw new Exception("Unknown measurment");
        }
    }
    
}

?>
