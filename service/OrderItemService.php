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
    
    
    public function create($idOrder, $idProduct, $quantity, $priceSale, $count = 1){
         $this-> checkQuantity($quantity);
         $this-> checkQuantity($priceSale);
         $this->isUsed($idOrder, $idProduct );
         $priceAndRecipe = $this->getProductPriceAndRecipe($idProduct);
         $this->conn->insert("INSERT INTO `order_item` (`id_order`, `id_product`, `quantity`, `price`, `price_sale`, `item_count`) VALUES (?,?,?,?,?,?)",
                            array($idOrder, $idProduct, $quantity, $priceAndRecipe['price'], $priceSale, $count));
         
         $itemId = $this->conn->getInsertId();

         if($priceAndRecipe['recipe'] == 1){
             $this->createSubItems($itemId, $idProduct);
         }
         return $itemId ;
    }
    
    public function update($IdItem, $quantity, $priceSale, $item_count){
        $this->checkQuantity($quantity);
        $this->checkQuantity($priceSale);
        $this->conn->update("UPDATE `order_item` SET `quantity`=?,`price_sale`=? ,`item_count`=? WHERE `id`=? LIMIT 1", array($quantity,$priceSale, $item_count, $IdItem));
    }
    
    public function updateItemSalePrice($priceSale, $IdItem){
        $this->checkQuantity($priceSale);
        $this->conn->update("UPDATE `order_item` SET `price_sale`=?  WHERE `id`=? LIMIT 1", array($priceSale, $IdItem));
    }

    
    public function delete($idItem){
        $data = $this->conn->select("SELECT `id_product`, `id_order` FROM `order_item` WHERE `id`=? LIMIT 1", 
                array( $idItem ));
        
        $this->conn->delete("DELETE FROM `order_subitem` WHERE `id`IN (SELECT id FROM order_item WHERE `id_order`=?)", 
                array( $data[0]['id_order'] ));

        $this->conn->delete("DELETE FROM `order_item` WHERE `id`=? LIMIT 1", array( $idItem ));
    }
    
    
    public function createSubItems($itemId, $idProduct){
        $items = $this->conn->select("SELECT p.`id_color`, p.`quantity_kg`, c.`price` 
                                           FROM `product_item` p, `color` c 
                                           WHERE p.`id_color`= c.`id` AND p.`id_product`=?", array( $idProduct));
        $data = array();
        
        if(count($items) != 0){
            for($i = 0; $i < count($items); $i++){
                $data[] = "( ".$items[$i]['id_color'].", ".$items[$i]['quantity_kg'].", ".$items[$i]['price'].", ".$itemId.")";
            }

            $this->conn->simpleQuery("INSERT INTO `order_subitem` ( `id_color`, `quantity_kg`, `price`, `id_order_item`) VALUES ".
                    implode(",", $data));
        }
    }
    
    
    public function getProductPriceAndRecipe($idProduct){
      $r =  $this->conn->select("SELECT `price`, `recipe` FROM `product` WHERE `id`=? LIMIT 1", array( $idProduct ));
      return $r[0];
    }
    
    
    /* PROJECTION --- */
    
    public function retriveItemsByOrderId($orderId){
        return $this->conn->select("SELECT 
                                    i.id,
                                    i.price,
                                    i.price_sale,
                                    i.item_count,
                                    p.code,
                                    p.label,
                                    p.recipe,
                                    p.supplier,
                                    @tdq := ROUND((i.quantity + (SELECT coalesce(SUM(x.quantity_kg),0) FROM order_subitem x, color y WHERE y.id=x.id_color AND y.color_type=2  AND x.id_order_item=i.id)),2) as mnozstvo_spolu, 
                                    ROUND(@tdq  * i.price_sale ,2) as cena_spolu_predaj,
                                    ROUND(@tdq * i.price_sale * i.item_count, 2) AS cena_spolu_predaj_total, 
                                    @cena_tovar := ROUND(SUM(i.quantity * i.price),2) as cena_tovar,
                                    @pigmenty := (SELECT coalesce(SUM(x.quantity_kg * x.price),0) FROM order_subitem x, color y WHERE x.id_color=y.id AND y.color_type=1 AND x.id_order_item=i.id) as pigments,
                                    @riedidla := (SELECT coalesce(SUM(x.quantity_kg * x.price),0) FROM order_subitem x, color y WHERE x.id_color=y.id AND (y.color_type=2 OR y.color_type=3) AND x.id_order_item=i.id) as riedidla,
                                    @cena_rcp := ROUND(@pigmenty * i.quantity + @riedidla,2) as cena_spolu_nakup,
                                    ROUND(@pigmenty + @riedidla,2) as jednotkova_cena_spolu_nakup
                                FROM
                                    order_item i
                                        JOIN
                                    product p ON p.id = i.id_product
                                        LEFT JOIN
                                    order_subitem si ON si.id_order_item = i.id
                                WHERE
                                    i.id_order =?
                                GROUP BY i.id", array( $orderId ));
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
       if(!isset($_GET['confirmed']) || $_GET['confirmed'] == 0){
            
            $r =  $this->conn->select("SELECT count(*) FROM `order_item` WHERE id_product=? AND id_order=?", 
                array( $idProduct, $Idorder));
        
            if($r[0]["count(*)"] > 0)
                throw new ConfirmationNeededException("Položka sa už v objednávke nachádza, chcete napriek tomu pridať?");
        }
    }
}

?>
