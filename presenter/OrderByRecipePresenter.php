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
class OrderByRecipePresenter {

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
    
    
     public function printOrderByProductAndCustomer($idProduct, $idCustomer, $idOrder){
         $data =  $this->orderService->getProductByIdPoductAndIdCustomer($idProduct, $idCustomer, $idOrder);
         if($data == null) return false;
         $html = '<table>'.$this->getTableHead();
         for($i =0; $i < count($data); $i++){
              $html .= $this->getOrderTableRow($data[$i]);
         }
         return $html.'</table>';
     }
     
     
     
     public function printOrdersByRecipe($id, $pageNumber, $peerPage){
       $data =  $this->orderService->getOrdersByRecipieId($id, $pageNumber, $peerPage);
       if($data == null) return '<p class="alert">0 zákazníkov si objednalo tento výrobok</p>';
      // $this->createNavigator($id, $pageNumber, $peerPage);
       $html = '';
       $prevId = 0;
       for($i=0 ; $i < count($data); $i++ ){
           if($prevId != $data[$i]['id_customer']){
                 if($prevId != 0){
                    $html .= '</table>'; 
                }
                $html .= '<h2 class="cstHead">'.$data[$i]['name'].'</h2><table>'.$this->getTableHead();
                $prevId = $data[$i]['id_customer'];
           }
           $html .= $this->getOrderTableRow($data[$i]);
       }

       return $html.'</table>';
    }
    
    
    private function getTableHead(){
        return '<tr><th>Obj č.</th>
                    <th>Dátum obj.</th>
                    <th>Položka</th>
                    <th>Množstvo</th>
                    <th>Nákup j.</th>
                    <th>Predaj j.</th>
                    <th>Nákup spolu</th>
                    <th>Predaj spolu</th>
                    <th>Zisk</th>
                </tr>';
    }
        
    private function getOrderTableRow($row){
        $itemPrice = floatval(($row["recipe"] == 1 ? $row["jednotkova_cena_spolu_nakup"] : $row["price"]));
        return "<tr>".
                '<td class="c w50"><a href="/index.php?p=order&amp;sp=edit&amp;id='.$row["id"].'">OBJ-'.$row["id"].'</td>'.
                '<td class="c">'.date('d.m.Y', strtotime($row['date']) ).'</td>'.
                '<td class="c">'.$row['code'].' - '.$row['label'].'</td>'.
                '<td class="r">'. $row['mnozstvo_spolu'].' '.($row["recipe"] == 1 ? 'kg' : 'ks').'</td>'.
                '<td class="r">'.  number_format($itemPrice,2).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.  number_format(round($row["price_sale"],2),2).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.  number_format($row["cena_spolu_nakup"],2).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.  number_format(round($row["cena_spolu_predaj"],2),2).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.  $this->getProfit($itemPrice, $row["price_sale"]).'</td>'.
               "</tr>";
    }
    
    public function createNavigator($id, $pageNumber, $peerPage){
        $nav = new Navigator( $this->orderService->getCountOfAllOrderByRecipeId($id) , $pageNumber , 
                    '/index.php?'.preg_replace("/&s=[0-9]*/", "", $_SERVER['QUERY_STRING']) , $peerPage);
        $nav->setSeparator("&amp;s=");
        $this->navigator =  $nav->smartNavigator();    
    }
    
  
     public function getProfit($nakup, $predaj){
        if($predaj == 0) return "-";
        return  round(((($predaj - $nakup) / $nakup) * 100),2). " %";
    }

    
}

?>
