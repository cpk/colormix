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
    private $thinner = 0;
    
    private $totalWeightGiven = 0;
    
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
    
    
    private function formatPrice($price){
        return number_format(round($price, 2),2);
    }
    
    private function getRecipeItemTableRow($row){
        return "<tr>".
                '<td class="c">'.$row["code"].'</td>'.
                '<td>'.$row["name"].'</td>'.
               // '<td class="c">'.$row["unit"].'</td>'.
                '<td class="r"><b>'.floatval($row['riedidlo']== 1 ? $row["quantity_kg"] : ($row["quantity"] * $row["quantity_kg"])).' '.$row["unit"].'</b></td>'.
                '<td class="r il">'.floatval($row["quantity_kg"]).' '.$row["unit"].'</td>'.
                '<td class="r il hide">'.floatval($row["price"]).' '.$this->priceUnit.'/'.$row["unit"].'</td>'.
                '<td class="r">'.$this->formatPrice($row["price"] * $row["quantity_kg"]).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.$this->formatPrice(($row['riedidlo']== 1 ? ($row["price"] * $row["quantity_kg"]) : ($row["quantity"] * $row["price"] * $row["quantity_kg"]))).' '.$this->priceUnit.'</td>'.
                '<td class="c w50 hide"><a class="edit" href="#id'.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50 hide"><a class="del4" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    
    
    
    public function getTbodyOfTableItems($itemId, $orderId){
       $data =  $this->orderRecipeService->getRecipeItemsBy($itemId, $orderId);
       if($data == null) return '<p class="alert">Neboli nájdenuie žiadne položky</p>';
       $html = '';
       $this->totalWeightGiven = $data[0]["quantity"];
       for($i=0 ; $i < count($data); $i++ ){
           
           if($data[$i]["riedidlo"] == 1){
               $this->thinner +=  Converter::convert($data[$i]['id_unit'],$data[$i]['quantity_kg']);
               $this->totalPrice += $data[$i]['quantity_kg'] *  $data[$i]['price'];
           }else{
               $this->totalWeight += $data[$i]["quantity"] * Converter::convert($data[$i]['id_unit'],$data[$i]['quantity_kg']);
               $this->totalPrice += $data[$i]["quantity"] * $data[$i]['quantity_kg'] *  $data[$i]['price'];
           }
           $html .= $this->getRecipeItemTableRow($data[$i]);
       }
       $this->totalSalePrice = $this->getWeight() * $data[0]["price_sale"];
       return  $html;
    }
    
    
    
    
    public function getTotalWeight(){
            $html = '<b>'.$this->getWeight() .' kg</b>';
            if($this->thinner != 0)
                return $html.'<p>'.floatval($this->totalWeightGiven). 'kg + '.$this->thinner.'L</p>';
            return $html;
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
    
    /**
     * Riedidlo + celkoma zadana hmotnost baz a pigmentov
     * 
     * @return float
     */
    public function getWeight(){
        return floatval($this->totalWeightGiven + $this->thinner);
    }
}

?>
