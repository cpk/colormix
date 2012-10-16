<?php
require_once('../simpletest/autorun.php');
function __autoload($class){
    require_once "../libs/".$class.".php";
}

class ValidatorTest extends UnitTestCase {
    
    
    function testIsFloat() {
       $this->assertTrue( Validator::isFloat("25.65", 2));
    }
    
    function testIsFloat2() {
       $this->assertTrue( Validator::isFloat(5));
    } 
    
    function testPositivInt(){
        $this->assertFalse( Validator::isPositiveInt("55-", 0 , 3) );
    }
    
    function testIsEmail(){
        $this->assertFalse( Validator::isEmail("aa*@ba.com"));
    }
    
}
?>
