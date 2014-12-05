<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderItemPresenter
 *
 * @author Peto
 */
class OrderItemPresenter {
    
    private $priceUnit;
    private $weightUnit;
    private $conn;
    private $countOfItems = null;
    private $totalPrice = 0;
    private $saleTotalPrice = 0;
    private $orderService;
    private $orderItemService;

    public function __construct($conn, $weightUnit, $priceUnit, $orderService = null, $orderItemService = null) {
        $this->priceUnit = $priceUnit;
        $this->weightUnit = $weightUnit;
        $this->conn = $conn;
        
        if($orderService == null)
            $this->orderService = new OrderService($conn);
        else
            $this->orderService = $orderService;
        
         if($orderItemService == null)
            $this->orderItemService = new OrderItemService($conn);
        else
            $this->orderItemService = $orderItemService;
       
    }
    
    
    /* POLOZKY receptury --------------------------------- */
    public function printOrderItems($oderId){
       return '<table class="inline">'.$this->getItemsTableHead().'<tbody class="tableitems">'.
              $this->getTbodyOfTableItems($oderId)."</tbody></table>";
    }
    
    
     private function getItemsTableHead(){
        return '<thead><tr>
                    <th>Dodávateľ</th>
                    <th class="nm">Názov</th>
                    <th class="il text-quantity required">Pč. j.</th>
                    <th>Cena za j. nákup</th>
                    <th>Cena spolu nákup</th>
                    <th class="il text-price_sale required">Cena za j. predaj</th>
                    <th>Cena spolu predaj</th>
                    <th>Zisk</th>
                    <th class="hide">Upraviť</th>
                    <th class="hide">Zmazať</th>
               </tr></thead>';
    }
        
    // (Sell price - cost price)/cost price*100 
     private function getRecipeItemTableRow($row){
         $itemPrice = floatval(($row["recipe"] == 1 ? $row["jednotkova_cena_spolu_nakup"] : $row["price"]));
        return "<tr>".
                '<td class="c w50 supplier-'.$row["supplier"].'"><span>'.($row["supplier"] == 1 ? 'VTN' : 'XYZ').'</span></td>'.
                ($row["recipe"] == 1 ? '<td class="recipe nm"><a href="index.php?p=order&amp;sp=redit&amp;id='.$row["id"].'">'.$row["code"].' - '
                .$row["label"].'</a></td>' : '<td class="nm">'.$row["code"].' - '.$row["label"].'</td>') .
                '<td class="r il">'.$row["mnozstvo_spolu"].' '.($row["recipe"] == 1 ? 'kg' : 'ks').'</td>'.
                '<td class="r">'.$this->formatPrice($itemPrice).'</td>'.
                '<td class="r">'.$this->formatPrice($row["cena_spolu_nakup"] + $row["cena_tovar"]).'</td>'.
                '<td class="r il">'.$this->formatPrice($row["price_sale"]).'</td>'.
                '<td class="r">'.$this->formatPrice($row["cena_spolu_predaj"]).'</td>'.
                '<td class="r">'.$this->getProfit($row["cena_spolu_nakup"] + $row["cena_tovar"], $row["cena_spolu_predaj"]).'</td>'.
                '<td class="c w50 hide"><a class="edit" href="#id'.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50 hide"><a class="del3" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    private function formatPrice($price){
        return number_format($price,2,","," ").' '.$this->priceUnit;
    }
    
    public function getTbodyOfTableItems($orderId){
       $data =  $this->orderItemService->retriveItemsByOrderId($orderId);
       if($data == null) return '<p class="alert">Objednávka neobsahuje žiadne položky</p>';
       $html = '';
       for($i=0 ; $i < count($data); $i++ ){
           $this->totalPrice += round($data[$i]["cena_spolu_nakup"] + $data[$i]["cena_tovar"],2);
           $this->saleTotalPrice += round($data[$i]['cena_spolu_predaj'],2);
           $html .= $this->getRecipeItemTableRow($data[$i]);
       }
       return  $html;
    }


    public function getTotalPrice(){
        return $this->totalPrice;
    }
    
    public function getSaleTotalPrice(){
        return $this->saleTotalPrice;
    }
    
    public function getProfit($nakup, $predaj){
        if($predaj == 0   || $nakup == 0  ) return "0";
        return  round(((($predaj - $nakup) / $nakup) * 100),2). " %";
    }
        
    
      public function getResume($priceUnit){
        return  'Celková cena nákup: <span>'.$this->formatPrice($this->getTotalPrice()).'</span>'.
                'Celková cena predaj: <span>'.$this->formatPrice($this->getSaleTotalPrice()).'</span>'.
                'Zisk: <span>'.$this->getProfit($this->totalPrice, $this->saleTotalPrice).'</span>';
}
    
    
}

?>
