<?php


class ColorService{
    
    private $conn;
    
    public function __construct($conn) {
       
        if(!$conn instanceof Database){
            throw new Exception("Vyskytol sa problém s databázou.");
        }
        
        $this->conn = $conn;
    }
    
    
    public function recievAllColors(){
       return  $this->conn->select("SELECT c.`id`,c.`code`,c.`name`,c.`price`,c.`color_type`, m.`unit` 
                                    FROM `color` c, `measurement` m
                                    WHERE m.`id`=c.`id_measurement` AND c.`supplier`= ".$_SESSION['supplier']." ORDER BY c.`code`");
    }
    
    public function recievById( $id ){
        return  $this->conn->select("SELECT c.`id`,c.`code`,c.`name`,c.`price`,c.`id_measurement`,c.`color_type`, m.`unit` 
                                     FROM `color` c, `measurement` m 
                                     WHERE m.`id`=c.`id_measurement` AND c.`id`=".intval($id). " LIMIT 1");
    }
    
     public function recievByCode( $code ){
        return  $this->conn->select("SELECT c.`id`,c.`code`,c.`name`,c.`price`,c.`color_type`, m.`unit` 
                                     FROM `color` c, `measurement` m
                                     WHERE m.`id`=c.`id_measurement` AND c.`supplier`= ".$_SESSION['supplier']." AND c.`id`=?", array($code));
    }
    
    
    
    
    public function create($code, $name, $price, $color_type, $id_measurement){
        $this->validateColor($code, $name, $price);
        $this->conn->insert("INSERT INTO `color` (`code`, `name`, `price`, `color_type`, `id_measurement`, `supplier`) VALUES (?,?,?,?,?,?)", 
                array($code, $name, $price, $color_type, $id_measurement, $_SESSION['supplier']));
    }
    
    
    
    
    public function delete($id){
        $this->conn->select("DELETE FROM `color` WHERE `id`=? LIMIT 1", array( $id ));
    }
    
    
    public function update($id, $code, $name, $price, $color_type, $id_measurement ){
        $this->validateColor($code, $name, $price);
        $this->conn->update("UPDATE `color` SET code=?, name=?, price=?, color_type=?, id_measurement=? WHERE `id`=? LIMIT 1", 
                array($code, $name, $price, $color_type, $id_measurement, $id));
    }
    
            
            
    
    public function setConn($conn) {
        $this->conn = $conn;
    }


    public function getInsertId(){
        return $this->conn->getInsertId();
    }
   
    
    
     private function validateColor($code, $name, $price){
        
        if(strlen($code) > 20)
            throw new ValidationException("Kód farby môže mať max. 20 znakov");
        
        if(strlen($name) > 45)
            throw new ValidationException("Nazov farby môže mať max. 45 znakov");     
        
        if(! Validator::isFloat($price, 5))
            throw new ValidationException("Cena farby obsahuje neplatnú hodnotu.");
        
    }
    
    
}
?>
