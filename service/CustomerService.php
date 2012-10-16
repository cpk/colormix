<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerService
 *
 * @author Peto
 */
class CustomerService {

    private $conn;
    
    private $countOfItems = null;
    
    public function __construct($conn) {
       
        if(!$conn instanceof Database){
            throw new Exception("Vyskytol sa problém s databázou.");
        }
        
        $this->conn = $conn;
    }
    
    
    public function retrieveCustomers($pageNumber, $peerPage, $searchQuery){
        
        $offset = ($pageNumber == 1 ? 0 :  ($pageNumber * $peerPage) - $peerPage);
        return  $this->conn->select("SELECT * FROM `customer` ".$this->where($searchQuery).
                                    "ORDER BY `name` LIMIT $offset,  $peerPage");
    }
    
    
    
    public function create($name, $street, $zip, $city, $ico, $dic){
        $this->validateOrder($name, $street, $zip, $city, $ico, $dic);
        $this->conn->insert("INSERT INTO `customer` (`name`, `street`, `zip`, `city`, `ico`, `dic`) 
                             VALUES (?,?,?,?,?,?)",
                array ($name, $street, $zip, $city, $ico, $dic) );
    }
    
    
    
    
    
     public function update($name, $street, $zip, $city, $ico, $dic, $id){
        $this->validateOrder($name, $street, $zip, $city, $ico, $dic);
        $this->conn->insert("UPDATE `customer` 
                             SET `name`=?, `street`=?, `zip`=?, `city`=?, `ico`=?, `dic`=? 
                             WHERE `id`= ?
                             LIMIT 1",
                array ($name, $street, $zip, $city, $ico, $dic, $id) );
    }
    
    
    
    
    public function delete($Idcustomer){
        $this->conn->delete("DELETE FROM `customer` WHERE `id`=? LIMIT 1");
    }
    
    public function getCustomerById($idCustomer){
        return  $this->conn->select("SELECT * FROM `customer` WHERE `id`=? LIMIT 1", array( $idCustomer ));
    }


    public function getInsertId(){
        return $this->conn->getInsertId();
    }
    
    
    
    public function getCountCustomers($searchQuery){
        if($this->countOfItems == null){
            $count =  $this->conn->select("SELECT count(*) FROM `customer` ". $this->where($searchQuery));
            $this->countOfItems = $count[0]["count(*)"];
        }
        return (int)$this->countOfItems;
    }
    
    

    private function validateOrder($name, $street, $zip, $city, $ico, $dic){
        
        if(strlen($name) == 0){
            throw new ValidationException("Názov odberateľa nie je vyplnený");
        }
        
        if(strlen($street) > 0 && strlen($street) > 45){
            throw new ValidationException("Ulica môže obsahovať max. 45 znakov");
        }
        
        if(strlen($zip) > 0 && strlen($zip) > 6){
            throw new ValidationException("PSČ môže obsahovať max. 6 znakov");
        }
        
        if(strlen($city) > 0 && strlen($city) > 45){
            throw new ValidationException("Mesto môže obsahovať max. 45 znakov");
        }
        
        if(strlen($ico) > 0 && strlen($ico) >8){
            throw new ValidationException("IČO môže obsahovať max. 8 znakov");
        }
        if(strlen($dic) > 0 && strlen($dic) > 12){
            throw new ValidationException("DIČ môže obsahovať max. 12 znakov");
        }
    }
    
    
    private function where($searchQuery){
        if($searchQuery != null)   return " WHERE `name` LIKE '%".$searchQuery."%' ";
    }


}

?>
