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
    
    
    public function getTopCustomers($limit = 20){
        return $this->conn->select("SELECT name, 
                                            ROUND(SUM(spolu_nakup)) as spolu_nakup, 
                                            ROUND(SUM(spolu_predaj)) as spolu_predaj 
                                    FROM view_order
                                    ".$this->where()."
                                    GROUP BY name 
                                    ORDER BY spolu_predaj DESC 
                                    LIMIT $limit");
    }
    
    
    public function getMonthlyReport(){
        return $this->conn->select("SELECT  MONTH(`date`) as m, 
                                            YEAR(`date`) as y, 
                                            ROUND(SUM(spolu_nakup)) as spolu_nakup, 
                                            ROUND(SUM(spolu_predaj)) as spolu_predaj 
                                    FROM view_order ".
                                    $this->where().
                                    " GROUP BY YEAR(`date`), MONTH(`date`) ASC");                   
    }
    
    public function where(){
        $where = array();
        if(isset($_GET['dateFrom']) && strlen($_GET['dateFrom']) > 0) 
            $where[] =  " `date` >='".$_GET['dateFrom']."' "; 
         if(isset($_GET['dateTo']) && strlen($_GET['dateTo']) > 0) 
            $where[] =  " `date` <='".$_GET['dateTo']."' ";   
         if(isset($_GET['q']) && strlen($_GET['q']) > 0) 
            $where[] =  " `name` LIKE '%".$_GET['q']."%' "; 
         if(isset($_GET['surname']) && strlen($_GET['surname']) > 0) 
            $where[] =  " `surname` LIKE '%".$_GET['surname']."%' "; 
         return (count($where) > 0 ? " WHERE " : "").implode(" AND ", $where);
    }
    
}

?>
