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
                    <th>Kód</th>
                    <th>Názov</th>
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
         $itemPrice = floatval(($row["recipe"] == 1 ? $row["r_price"] : $row["price"]));
        return "<tr>".
                '<td class="c w50">'.$row["code"].'</td>'.
                ($row["recipe"] == 1 ? '<td class="recipe nm"><a href="index.php?p=order&amp;sp=redit&amp;id='.$row["id"].'">'
                .$row["label"].'</a></td>' : '<td class="nm">'.$row["label"].'</td>') .
                '<td class="r il">'.floatval($row["quantity"]).' '.($row["recipe"] == 1 ? 'kg' : 'ks').'</td>'.
                '<td class="r">'.$itemPrice.' '.$this->priceUnit.'</td>'.
                '<td class="r">'.number_format(round($row["i_price"] + $row["si_price"],2),2).' '.$this->priceUnit.'</td>'.
                '<td class="r il">'.$row["price_sale"].' '.$this->priceUnit.'</td>'.
                '<td class="r">'.number_format(round($row["total_price_sale"],2),2).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.round((($row["price_sale"] - $itemPrice) / $itemPrice)*100,2 ).' %</td>'.
                '<td class="c w50 hide"><a class="edit" href="#id'.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50 hide"><a class="del3" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    
    public function getTbodyOfTableItems($orderId){
       $data =  $this->orderItemService->retriveItemsByOrderId($orderId);
       if($data == null) return '<p class="alert">Objednávka neobsahuje žiadne položky</p>';
       $html = '';
       for($i=0 ; $i < count($data); $i++ ){
           $this->totalPrice += round($data[$i]["i_price"] + $data[$i]["si_price"],2);
           $this->saleTotalPrice += round($data[$i]['total_price_sale']);
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
    
    public function getProfit(){
        if($this->totalPrice == 0) return 0;
        return round((($this->saleTotalPrice - $this->totalPrice) / $this->totalPrice) * 100, 2)." %";
    }
    
    
      public function getResume($priceUnit){
        return  'Celková cena nákup: <span>'.number_format($this->getTotalPrice(), 2). ' '.$priceUnit.'</span>'.
                'Celková cena predaj: <span>'.number_format($this->getSaleTotalPrice(), 2). ' '.$priceUnit.'</span>'.
                'Zisk: <span>'.$this->getProfit().'</span>';
}
    
    
}

?>
