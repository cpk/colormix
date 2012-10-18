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

                        (
                        coalesce((SELECT SUM(x.quantity_kg * x.price )
                        FROM  `order_item` z, `order_subitem` x
                        JOIN `color` y ON y.id=x.id_color AND y.riedidlo=1
                        WHERE x.id_order=o.id AND z.id_order=x.id_order AND x.id_product=z.id_product),0)

                        + 
                        coalesce((SELECT SUM(x.quantity_kg * x.price * z.quantity )
                        FROM  `order_item` z, `order_subitem` x
                        JOIN `color` y ON y.id=x.id_color AND y.riedidlo=0
                        WHERE x.id_order=o.id AND z.id_order=x.id_order AND x.id_product=z.id_product),0) 

                        + 
                       coalesce((SELECT coalesce(SUM(z.quantity * z.price),0)  FROM `order_item`z, product p  WHERE p.recipe=0 AND p.id=z.id_product AND o.id=z.id_order),0) 

                        ) as spolu_nakup, 
                      (
                        coalesce((SELECT ROUND(coalesce(SUM(x.quantity_kg * z.price_sale),0),2)
                                           FROM  `order_item` z
                                             LEFT JOIN `order_subitem` x ON  x.id_product=z.id_product AND z.id_order=x.id_order
                                            JOIN `color` y ON  y.id=x.id_color AND y.riedidlo=1 
                                           WHERE o.id=z.id_order 

                        ),0)
                        + 
                        coalesce((SELECT ROUND(coalesce(SUM(z.quantity * z.price_sale),0),2) FROM `order_item`z  WHERE z.id_order=o.id ),0)

                        ) as spolu_predaj 

                    FROM `order` o
                    JOIN `customer` c ON o.`id_customer`=c.`id` 
                    LEFT JOIN `user` u ON o.`id_user`=u.`id_user` 
                    LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                    LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product` AND si.`id_order` = i.`id_order`';
    
    
   private  $sumQuery = 'SELECT 
  (coalesce((SELECT coalesce(SUM(x.quantity_kg * x.price),0) * z.quantity
                       FROM  `order_item` z 
                        LEFT JOIN `order_subitem` x ON  x.id_product=z.id_product
                        JOIN `color` y ON  y.id=x.id_color AND y.riedidlo=0 
                      
                       
    ),0)
     + 
    coalesce((SELECT coalesce(SUM(x.quantity_kg * x.price),0)
                       FROM  `order_item` z  
                         LEFT JOIN `order_subitem` x ON  x.id_product=z.id_product
                        JOIN `color` y ON  y.id=x.id_color AND y.riedidlo=1 
                      
                       
    ),0)
    + 
    coalesce((SELECT SUM(z.quantity * z.price) FROM `order_item`z  ),0)

    ) as spolu_nakup, 
  (
    coalesce((SELECT coalesce(SUM(x.quantity_kg * z.price_sale),0)
                       FROM  `order_item` z  
                         LEFT JOIN `order_subitem` x ON  x.id_product=z.id_product
                        JOIN `color` y ON  y.id=x.id_color AND y.riedidlo=1 
                       
                       
    ),0)
    + 
    (SELECT coalesce(SUM(z.quantity * z.price_sale),0) FROM `order_item`z)

    ) as spolu_predaj 

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
    
    public function getCountOfAllOrderByRecipeId($id){
         if($this->countOfItems == null){
            $count =  $this->conn->select(" SELECT count(*)
                                            FROM `order` o
                                            JOIN `customer` c ON o.id_customer=c.id
                                            LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                                            LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product`
                                            JOIN `product` p ON i.id_product=p.id
                                            WHERE o.`id_customer`=c.`id` AND i.id_product=?
                                            Group by o.id", array($id));
            $this->countOfItems = $count[0]["count(*)"];
        }
        return (int)$this->countOfItems;
       
    }
    
    
    public function getTotalPrice(){
        if($this->totalPrice == null)
            $this->initTotalsVals();
       return $this->totalPrice;
    }
    
    public function getTotalSalePrice(){
        if($this->totalSalePrice == null)
                     $this->initTotalsVals();
       return $this->totalSalePrice;
    }
    

    private function initTotalsVals(){
       // echo $this->sumQuery.$this->where()." LIMIT 1";
        $r =  $this->conn->select($this->sumQuery.$this->where()." LIMIT 1");
        $this->totalPrice = $r[0]['spolu_nakup'];
        $this->totalSalePrice = $r[0]['spolu_predaj'];
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
    
    
    public function getOrdersByRecipieId($recipeId, $pageNumber , $peerPage){
        $offset = ($pageNumber == 1 ? 0 :  ($pageNumber * $peerPage) - $peerPage);
        return  $this->conn->select("SELECT o.`id`, o.`date`, c.`name`,  c.`id` as id_customer,
                                        p.code,
                                        p.label,
                                        p.recipe,
                                        i.price,
                                        i.price_sale,
                                            @tdq := ROUND((i.quantity + (SELECT coalesce(SUM(x.quantity_kg),0) FROM order_subitem x, color y WHERE y.id=x.id_color AND y.riedidlo=1 AND x.id_product=i.id_product AND x.id_order=i.id_order)),2) as mnozstvo_spolu, 
                                            ROUND(@tdq  * i.price_sale ,2) as cena_spolu_predaj,
                                            @cena_tovar := ROUND(SUM(i.quantity * i.price),2) as cena_tovar,
                                            @pigmenty := (SELECT coalesce(SUM(x.quantity_kg * x.price),0) FROM order_subitem x, color y WHERE x.id_color=y.id AND y.riedidlo!=1 AND x.id_product=i.id_product  AND x.id_order=i.id_order) as pigments,
                                            @riedidla := (SELECT coalesce(SUM(x.quantity_kg * x.price),0) FROM order_subitem x, color y WHERE x.id_color=y.id AND y.riedidlo=1 AND x.id_product=i.id_product  AND x.id_order=i.id_order) as riedidla,
                                            @cena_rcp := ROUND(@pigmenty * i.quantity + @riedidla,2) as cena_spolu_nakup,
                                            ROUND(@pigmenty + @riedidla,2) as jednotkova_cena_spolu_nakup
                                        FROM `order` o
                                        JOIN `customer` c ON o.id_customer=c.id
                                        LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                                        LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product`
                                        JOIN `product` p ON i.id_product=p.id
                                        WHERE o.`id_customer`=c.`id` AND i.id_product=?
                                        GROUP BY o.`id`
                                        ORDER BY c.`name`, o.`date` DESC", array($recipeId)); 
    }
    
}

?>
