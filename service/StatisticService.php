<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatisticService
 *
 * @author Peto
 */
class StatisticService {
     private $conn;

    
    public function __construct($conn) {
       
        if(!$conn instanceof Database){
            throw new Exception("Vyskytol sa problém s databázou.");
        }
        
        $this->conn = $conn;
    }
    
    
    public function getTopCustomers($limit = 10){
        return $this->conn->select("SELECT c.`name`,
                                        SUM(i.`quantity` * i.`price`) +
                                        SUM(i.`quantity` * si.`quantity_kg` * si.`price`)  as total_price
                                    FROM `order` o
                                    JOIN `customer` c ON o.`id_customer`=c.`id`
                                    LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                                    LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product`
                                    GROUP BY c.`name`
                                    ORDER BY total_price DESC
                                    LIMIT $limit");
    }
    
    
    public function getMonthlyReportOfLastYear(){
        return $this->conn->select("SELECT MONTHNAME(o.`date`) as m, YEAR(o.`date`) as y,
                                    SUM(i.`quantity` * i.`price`) +
                                    SUM(i.`quantity` * coalesce(si.`quantity_kg`,0) * coalesce(si.`price`,0)) as total_price
                                    FROM `order` o
                                    JOIN `customer` c ON o.`id_customer`=c.`id`
                                    LEFT JOIN `order_item` i ON i.`id_order`= o.`id`
                                    LEFT JOIN `order_subitem` si ON si.`id_product`= i.`id_product`
                                    WHERE o.`date` > DATE_SUB(NOW(), INTERVAL 1 YEAR)
                                    GROUP BY YEAR(o.`date`), MONTH(o.`date`) ASC");
    }
    
    
}

?>
