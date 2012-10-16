<?php
require_once('../config.php');
require_once('../simpletest/autorun.php');
require_once('../service/ColorService.php');
function __autoload($class){
    require_once "../libs/class.".$class.".php";
}

class ColorServiceTest extends UnitTestCase {
    
    private $cs;
    
    function setUp() {
        $conn = Database::getInstance(SERVER, USER, PASS, DB_NAME);
        $this->cs = new ColorService($conn);
    }
    
    function testRecievingAll() {
      // $this->assertEqual( count($this->cs->recievAllColor()) , 17);
    }
    
    
    function testCreateing(){
      $this->expectException();
      $this->cs->create("01234567890", "ABC", 2, 0.5);
    }
    
    function testDeleting(){
        $this->cs->create("0123456789", "ABC", 2, 0.5);
        $id = $this->cs->getInsertId();
        $this->assertEqual( count($this->cs->recievAllColors()) , 18);
        $this->cs->delete($id);
        $this->assertEqual( count($this->cs->recievAllColors()) , 17);
    }
}
