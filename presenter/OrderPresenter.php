<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderPresenter
 *
 * @author Peto
 */
class OrderPresenter {

    private $priceUnit;
    private $weightUnit;
    private $conn;
    private $countOfItems = null;
    private $orderService;

    public function __construct($conn, $weightUnit, $priceUnit, $orderService = null) {
        $this->priceUnit = $priceUnit;
        $this->weightUnit = $weightUnit;
        $this->conn = $conn;
        
        if($orderService == null)
            $this->orderService = new OrderService($conn);
        else
            $this->orderService = $orderService;
       
    }
    
    

     public function printOrders($pageNumber, $peerPage){
       $data =  $this->orderService->recieveOrders($pageNumber, $peerPage);
       $this->createNavigator($pageNumber, $peerPage);
       
       $html = $this->navigator.'<div class="claer"></div><table>';
       $html .= $this->getTableHead().'<tbody class="order">';
       for($i=0 ; $i < count($data); $i++ ){
           $html .= $this->getOrderTableRow($data[$i]);
       }
       $html .= "</tbody></table>".$this->navigator;
       return $html;
    }
    
    
    private function getTableHead(){
        return '<tr><th>Obj č.</th>
                    <th>Dátum obj.</th>
                    <th>Odberateľ</th>
                    <th>Nákup</th>
                    <th>Predaj</th>
                    <th>Zisk</th>
                    <th>Zaevidoval</th>
                    <th>Upraviť</th>
                    <th>Duplik.</th>
                    <th>Zmazať</th>
                </tr>';
    }
        
    private function getOrderTableRow($row){
        return "<tr>".
                '<td class="c w50">OBJ-'.$row["id"].'</td>'.
                '<td class="c">'.date('d.m.Y', strtotime($row['date']) ).'</td>'.
                '<td>'.$row["name"].'</td>'.
                '<td class="r">'.  number_format(round($row["spolu_nakup"],2),2,","," ").' '.$this->priceUnit.'</td>'.
                '<td class="r">'.  number_format(round($row["spolu_predaj"],2),2,","," ").' '.$this->priceUnit.'</td>'.
                '<td class="r">'.  $this->getProfit($row["spolu_nakup"], $row["spolu_predaj"]).'</td>'.
                '<td class="c">'.$row["givenname"].' '.substr($row["surname"], 0, 1).'. </td>'.
                '<td class="c w50"><a class="edit" href="./index.php?p=order&amp;sp=edit&amp;id='.$row["id"].'">upraviť</a></td>'.
                '<td class="c"><a title="Duplikovať objednávku" class="copyOrder" href="#'.$row["id"].'"></a></td>'.
                '<td class="c"><a title="Zmazať objednávku" class="del" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    public function createNavigator($pageNumber, $peerPage){
        $nav = new Navigator( $this->orderService->getCountOfAllOrders() , $pageNumber , 
                    '/index.php?'.preg_replace("/&s=[0-9]*/", "", $_SERVER['QUERY_STRING']) , $peerPage);
        $nav->setSeparator("&amp;s=");
        $this->navigator =  $nav->smartNavigator();    
    }
    
  
     public function getProfit($nakup, $predaj){
        if($predaj == 0  || $nakup == 0  ) return "0";
        return  round(((($predaj - $nakup) / $nakup) * 100)). " %";
    }

    
}

?>
