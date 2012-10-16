<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderService
 *
 * @author Peto
 */
class OrderService {
    
    private $conn;
    
    private $countOfItems = null;
    
    private $totalPrice = null;
    
    private $totalSalePrice = null;
    
    
    private $q = 'SELECT o.`id`, o.`date`, c.`name`, u.`givenname`, u.`surname`,
                    (SELECT SUM( ci.`quantity` * ci.`price_sale`) FROM `order_item` ci WHERE ci.`id_order`=o.`id` ) as total_sale,
                    SUM(i.`quantity` * i.`price`) + 
                    SUM(i.`quantity` * coalesce(si.`quantity_kg`,0) * coalesce(si.`price`,0)) as total
                 FROM `order` o
                 JOIN `customer` c ON o.`id_customer`=c.`id` 
                 LEFT JOIN `user` u ON o.`id_user`=u.`id_user` 
                 LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                 LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product` AND si.`id_order` = i.`id_order`';
    
    public function __construct($conn) {
       
        if(!$conn instanceof Database){
            throw new Exception("Vyskytol sa problém s databázou.");
        }
        
        $this->conn = $conn;
    }
    
    
    public function recieveOrders($pageNumber, $peerPage){
        
        $offset = ($pageNumber == 1 ? 0 :  ($pageNumber * $peerPage) - $peerPage);
      
        
        return  $this->conn->select( $this->q.$this->where()."
                                     GROUP BY o.`id`".
                                     $this->orderBy()."
                                     LIMIT $offset,  $peerPage");
    }
    
    public function getOrderById($orderId){        
        return  $this->conn->select($this->q.
                                     "WHERE ?
                                     GROUP BY o.`id`
                                     LIMIT 1", array($orderId));
    }
    
    
    
    public function create($idCustomer, $date, $label){
        $this->validateOrder($idCustomer, $date, $label);
        $this->conn->insert("INSERT INTO `order` (`id_customer`, `label`, `date`, `id_user`) VALUES (?,?,?,?)",
                array ($idCustomer, $label, $date, $_SESSION['id']) );
    }
    
    
    public function delete($idOrder){
        $this->conn->delete("DELETE FROM `order` WHERE id=? LIMIT 1", array( $idOrder ));
        $this->conn->delete("DELETE FROM `order_item` WHERE id_order=?", array( $idOrder ));
        $this->conn->delete("DELETE FROM `order_subitem` WHERE id_order=? ", array( $idOrder ));
    }
    
    
    
    public function retriveById($idOrder){
        return $this->conn->select("SELECT * FROM `order` o, `customer` c, `user` u
                                    WHERE o.`id`=? 
                                    AND o.`id_customer`=c.`id` AND u.`id_user`=o.`id_user`
                                    LIMIT 1", array($idOrder));
    }

    
    
    public function getInsertId(){
        return $this->conn->getInsertId();
    }
    
    public function updateLabel($id, $label){
        $this->conn->update("UPDATE `order` SET label=? WHERE id=? LIMIT 1", array( $label, $id ));
    }
    
    public function updateDate($id, $date){
        $this->conn->update("UPDATE `order` SET `date`=? WHERE id=? LIMIT 1", array( $date, $id ));
    }


    public function getCountOfAllOrders(){
        if($this->countOfItems == null){
            $count =  $this->conn->select("SELECT count(*) 
                                           FROM `order` o
                                           JOIN `customer` c ON o.`id_customer`=c.`id` 
                                           ".$this->where());
            
            
            $this->countOfItems = $count[0]["count(*)"];
        }
        return (int)$this->countOfItems;
    }
    
    public function getTotalPrice(){
        
        if($this->totalPrice == null){
        $r =  $this->conn->select("SELECT 
                                        SUM(i.`quantity` * i.`price`) + 
                                        SUM(i.`quantity` * coalesce(si.`quantity_kg`,0) * coalesce(si.`price`,0)) as total
                                     FROM `order` o
                                     JOIN `customer` c ON o.`id_customer`=c.`id`
                                     LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                                     LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product` AND si.`id_order` = i.`id_order`".
                                     $this->where());
       $this->totalPrice = $r[0]['total'];
       }
       return $this->totalPrice;
    }
    
    public function getTotalSalePrice(){
        if($this->totalSalePrice == null){
        $WHERE = $this->where();
        $r =  $this->conn->select("SELECT 
                                     SUM(i.`quantity` * i.`price_sale`) as total
                                     FROM `order` o, `order_item` i, `customer` c ". 
                                     ($WHERE == "" ? "WHERE o.id=i.id_order AND o.`id_customer`=c.`id`" : $WHERE ." AND o.id=i.id_order AND o.`id_customer`=c.`id`")." ");
       $this->totalSalePrice = $r[0]['total'];
       }
       return $this->totalSalePrice;
    }
    


    public function orderBy(){
        if(!isset($_GET['orderBy'])) $_GET['orderBy'] = 3;
        switch ($_GET['orderBy']){
            case 1 :
                return ' ORDER BY c.`name` ASC ';
            case 2 :
                return ' ORDER BY c.`name` DESC';
            case 0 :
            case 3 :
                return ' ORDER BY o.`date` DESC ';
            case 4 :
                return ' ORDER BY o.`date` ASC ';  
             default : 
                 throw new Exception('Can not order data.');
        }
        
    }
    
    public function where(){
        $where = array();
        if(isset($_GET['dateFrom']) && strlen($_GET['dateFrom']) > 0) 
            $where[] =  " o.`date` >='".$_GET['dateFrom']."' "; 
         if(isset($_GET['dateTo']) && strlen($_GET['dateTo']) > 0) 
            $where[] =  " o.`date` <='".$_GET['dateTo']."' "; 
         if(isset($_GET['q']) && strlen($_GET['q']) > 0) 
            $where[] =  " c.`name` LIKE '%".$_GET['q']."%' "; 
         return (count($where) > 0 ? " WHERE " : "").implode(" AND ", $where);
    }


    private function validateOrder($idCustomer, $date, $label){
        
        if($idCustomer == 0){
            throw new ValidationException("Nie je vybraný odberateľ.");
        }
        
        if($date != "" && !Validator::isDate($date)){
            throw new ValidationException("Dátum je v nesprávnom tvare.");
        }
        
        if(strlen($label) > 255 ){
            throw new ValidationException("Max. dĺžka popisu je 255 znakov.");
        }
    }
}

?>
