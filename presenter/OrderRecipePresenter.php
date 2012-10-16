<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderRecipePresenter
 *
 * @author Peto
 */
class OrderRecipePresenter {
    
    private $orderRecipeService;
    private $totalPrice = 0;
    private $totalSalePrice = 0;
    private $totalWeight = 0;
    
    public function __construct($conn, $weightUnit, $priceUnit,  $orderRecipeService = null) {
        
        $this->priceUnit = $priceUnit;
        $this->weightUnit = $weightUnit;
        
        if($orderRecipeService == null)
            $this->orderRecipeService = new OrderRecipeService($conn);
        else
            $this->orderRecipeService = $orderRecipeService;
     
        $this->conn = $conn;
    }
    
    
    /* POLOZKY receptury --------------------------------- */
    public function printRecipieItems($itemId, $orderId){
       return '<table class="inline">'.$this->getItemsTableHead().'<tbody class="tableitems">'.
              $this->getTbodyOfTableItems($itemId, $orderId)."</tbody></table>";
     }
 
    
    private function getItemsTableHead(){
        return '<thead><tr><th>Kód</th><th>Názov</th>'.
              // '<th>Jednotka</th>'.
               '<th>Dávka celkovo</th>'.
               '<th class="il text-quantity_kg required">Dávka / 1kg</th>'.
               '<th class="il text-price required hide">Cena za jednotku</th>'.
               '<th>Cena dávky/1kg</th>'.
               '<th>Cena d. celkovo</th>'.
               '<th class="w50 hide">Upraviť</th>'.
               '<th class="w50 hide">Zmazať</th></tr></thead>';
    }
    
    private function getRecipeItemTableRow($row){
        return "<tr>".
                '<td class="c">'.$row["code"].'</td>'.
                '<td>'.$row["name"].'</td>'.
               // '<td class="c">'.$row["unit"].'</td>'.
                '<td class="r"><b>'.($row["quantity"] * $row["quantity_kg"]).' '.$row["unit"].'</b></td>'.
                '<td class="r il">'.floatval($row["quantity_kg"]).' '.$row["unit"].'</td>'.
                '<td class="r il hide">'.floatval($row["price"]).' '.$this->priceUnit.'/'.$row["unit"].'</td>'.
                '<td class="r">'.($row["price"] * $row["quantity_kg"]).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.($row["quantity"] * $row["price"] * $row["quantity_kg"]).' '.$this->priceUnit.'</td>'.
                '<td class="c w50 hide"><a class="edit" href="#id'.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50 hide"><a class="del4" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    public function getTbodyOfTableItems($itemId, $orderId){
       $data =  $this->orderRecipeService->getRecipeItemsBy($itemId, $orderId);
       if($data == null) return '<p class="alert">Neboli nájdenuie žiadne položky</p>';
       $html = '';
       $this->totalSalePrice = $data[0]["quantity"] * $data[0]["price_sale"];
       for($i=0 ; $i < count($data); $i++ ){
           $this->totalPrice += $data[$i]["quantity"] * $data[$i]['quantity_kg'] *  $data[$i]['price'];
           $this->totalWeight += $data[$i]["quantity"] * Converter::convert($data[$i]['id_unit'],$data[$i]['quantity_kg']);
           $html .= $this->getRecipeItemTableRow($data[$i]);
       }
       return  $html;
    }


    public function getResume(){
        return 'Cena nákup: <span>'.  number_format(round($this->totalPrice,2),2).' '.$this->priceUnit.'</span>'.
               'Cena predaj: <span>'.  number_format(round($this->totalSalePrice,2),2).' '.$this->priceUnit.'</span>'.
               'Zisk: <span>'. $this->getProfit().'</span>'.
               'Celková hmotnosť: <span>'.round($this->totalWeight,4).' kg</span>';
    }
    
    public function getProfit(){
        if($this->totalSalePrice == 0) return "-";
        return  round(((($this->totalSalePrice - $this->totalPrice) / $this->totalPrice) * 100),2). " %";
    }
    
}

?>
