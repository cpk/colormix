<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderItemService
 *
 * @author Peto
 */
class OrderItemService {
   
    private $conn;
    
    public function __construct($conn) {
       
        if(!$conn instanceof Database){
            throw new Exception("Vyskytol sa problém s databázou.");
        }
        
        $this->conn = $conn;
    }
    
    
    public function create($idOrder, $idProduct, $quantity, $priceSale){
         $this-> checkQuantity($quantity);
         $this-> checkQuantity($priceSale);
         $this->isUsed($idOrder, $idProduct);
         $priceAndRecipe = $this->getProductPriceAndRecipe($idProduct);
         $this->conn->insert("INSERT INTO `order_item` (`id_order`, `id_product`, `quantity`, `price`, `price_sale`) VALUES (?,?,?,?,?)",
                            array($idOrder, $idProduct, $quantity, $priceAndRecipe['price'], $priceSale));
         
         if($priceAndRecipe['recipe'] == 1) 
             $this->createSubItems($idProduct, $idOrder);
    }
    
    public function update($IdItem, $quantity, $priceSale){
        $this->checkQuantity($quantity);
        $this->checkQuantity($priceSale);
        $this->conn->update("UPDATE `order_item` SET `quantity`=?,`price_sale`=?  WHERE `id`=? LIMIT 1", array($quantity,$priceSale, $IdItem));
    }

    
    public function delete($idItem){
        $data = $this->conn->select("SELECT `id_product`, `id_order` FROM `order_item` WHERE `id`=? LIMIT 1", 
                array( $idItem ));
        $this->conn->delete("DELETE FROM `order_item` WHERE `id`=? LIMIT 1", array( $idItem ));
        $this->conn->delete("DELETE FROM `order_subitem` WHERE `id_product`=? AND `id_order`=?", 
                array( $data[0]['id_product'],$data[0]['id_order'] ));
    }
    
    
    public function createSubItems($idProduct, $idOrder){
        $items = $this->conn->select("SELECT p.`id_color`, p.`quantity_kg`, c.`price` 
                                           FROM `product_item` p, `color` c 
                                           WHERE p.`id_color`= c.`id` AND p.`id_product`=?", array( $idProduct));
        $data = array();
        
        if(count($items) != 0){
            for($i = 0; $i < count($items); $i++){
                $data[] = "( ".$idProduct.", ".$items[$i]['id_color'].", ".$items[$i]['quantity_kg'].", 
                            ".$items[$i]['price'].", ".$idOrder.")";
            }

            $this->conn->simpleQuery("INSERT INTO `order_subitem` (`id_product`, `id_color`, `quantity_kg`, `price`, `id_order`) VALUES ".
                    implode(",", $data));
            }
    }
    
    
    public function getProductPriceAndRecipe($idProduct){
      $r =  $this->conn->select("SELECT `price`, `recipe` FROM `product` WHERE `id`=? LIMIT 1", array( $idProduct ));
      return $r[0];
    }
    
    
    /* PROJECTION --- */
    
    public function retriveItemsByOrderId($orderId){
        return $this->conn->select("SELECT i.`id`, i.`price`,i. `price_sale`, i.`quantity`, p.`code`, p.`label`, p.`recipe`,
                                        SUM(i.`quantity` * i.`price`) as i_price ,
                                        i.`quantity` * i.`price_sale` as total_price_sale ,
                                        SUM(i.`quantity` * si.`quantity_kg` * si.`price`) as si_price,
                                        SUM(si.`quantity_kg` * si.`price`) as r_price
                                    FROM `order_item` i
                                    JOIN `product` p ON p.`id`=i.`id_product`
                                    LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product` AND  si.`id_order`=$orderId
                                    WHERE i.`id_order`=?
                                    GROUP BY i.`id`", array( $orderId ));
    }
    
    
     public function checkQuantity($quantity){
         if(!Validator::isFloat($quantity, 5))
            throw new ValidationException("Počet obsahuje neplatnú hodnotu.");
        
    }
    
     public function checkPrice($quantity){
         if(!Validator::isFloat($quantity, 5))
            throw new ValidationException("Cena obsahuje neplatnú hodnotu.");
        
    }
    
    public function isUsed($Idorder, $idProduct){
       $r =  $this->conn->select("SELECT count(*) FROM `order_item` WHERE id_product=? AND id_order=?", 
                array( $idProduct, $Idorder, ));
        
        if($r[0]["count(*)"] > 0)
            throw new ValidationException("Položka sa už v objednávke nachádza.");
    }
}

?>
