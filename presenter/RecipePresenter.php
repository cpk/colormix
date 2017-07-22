<?php

class RecipePresenter{
    
    private $recipeService;
    private $recipeItemService;
    private $priceUnit;
    private $weightUnit;
    private $conn;
    private $UPDATE = 1;
    private $CREATE = 2;
    
    private $totalPrice = 0;
    private $totalWeight = 0;
    
    private $navigator = null;

    public function __construct($conn, $weightUnit, $priceUnit, $recipeService = null , $recipeItemService = null) {
        $this->priceUnit = $priceUnit;
        $this->weightUnit = $weightUnit;
        if($recipeService == null)
            $this->recipeService = new RecipeService($conn);
        else
            $this->recipeService = $recipeService;
        if($recipeItemService == null)
            $this->recipeItemService = new RecipeItemService($conn);
        else
            $this->recipeItemService = $recipeItemService;
        $this->conn = $conn;
    }
    
    
    
    /* RECEPTURA -------------------------------------- */
     public function printRecipies($pageNumber, $peerPage, $searchQuery){
       $data =  $this->recipeService->recieveRecipies($pageNumber, $peerPage, $searchQuery);
       if($data == null) return '<p class="alert">Požiadavke nevyhovuje žiadny záznam</p>';
       $this->createNavigator($pageNumber, $peerPage); 
       $html = $this->navigator.'<div class="claer"></div><table>';
       $html .= $this->getTableHead().'<tbody class="product">';
       for($i=0 ; $i < count($data); $i++ ){
           $html .= $this->getRecipeTableRow($data[$i]);
       }
       $html .= "</tbody></table>".$this->navigator;
       return $html;
    }
    
     
    private function getTableHead(){
        return '<tr>
                    <th>Odtieň</th>
                    <th>Názov tovaru</th>
                    <th>Cena 1'.$this->weightUnit.'/'.$this->priceUnit.'</th>
                    <th>Cena  s '.$this->getProfit().'% ziskom</th>
                    <th class="print-hidden">Upraviť</th>
                    <th class="print-hidden">Zmazať</th>
               </tr>';
    }
    
    
    
    
    private function getRecipeTableRow($row){
        return "<tr>".
                '<td class="l w100">'.strtoupper($row["code"]).'</td>'.
                '<td>'.strtoupper($row["label"]).'</td>'.
                '<td class="r">'.$this->getPrice($row).' '.$this->priceUnit.'</td>'.
                '<td class="r">'.$this->getTotalPriceWithProfit($row).'</td>'.
                '<td class="c w50 print-hidden"><a class="edit" href="./index.php?p=recipe&amp;sp=edit&amp;id='.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50 print-hidden"><a class="del" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }

    private function getPrice($row){
        return round($row["total_price"] + $row["price"],4);
    }
    
    /* POLOZKY receptury --------------------------------- */
    public function printRecipieItems($recipeId){
       return '<table class="inline">'.$this->getItemsTableHead().'<tbody class="tableitems">'.
              $this->getTbodyOfTableItems($recipeId)."</tbody></table>";
     }
 
    
    private function getItemsTableHead(){
        return '<thead><tr><th>Dodávateľ</th><th>Kód</th><th>Názov</th><th>Cena za jednotku</th>'.
               '<th class="il text-quantity_kg required">Dávka na 1kg</th>'.
               '<th>Cena dávky na 1kg</th><th>Upraviť</th><th>Zmazať</th></tr></thead>';
    }
    
    
    private function getRecipeItemTableRow($row){
        return "<tr>".
                '<td class="c w50 supplier-'.$row["supplier"].'"><span>'.getSupplier($row["supplier"]).'</span></td>'.
                '<td class="c">'.$row["code"].'</td>'.
                '<td>'.$row["name"].'</td>'.
                '<td class="r">'.  $this->formatPrice($row["price"],4).'/'.$row["unit"].'</td>'.
                '<td class="r il">'.  $this->replaceDot(floatval($row["quantity_kg"])).' '.$row["unit"].'</td>'.
                '<td class="r">'.$this->formatPrice(($row["price"] * $row["quantity_kg"])).'</td>'.
                '<td class="c w50"><a class="edit" href="#id'.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50"><a class="del2" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }

   
    private function getTotalPriceWithProfit($row){
        return $this->formatPrice($this->getPrice($row) * $this->getPercentageProfit());
    }

    public function getPercentageProfit(){
       $profit = $this->getProfit();
       if($profit == 0){
          return 1;
       }
       return ($profit / 100) + 1;
    }

    public function getProfit(){
        if(!isset($_SESSION['profit'])){
            $_SESSION['profit'] = 75;
        }
        return $_SESSION['profit'];
    }

    public function getTbodyOfTableItems($recipeId){
       $data =  $this->recipeItemService->getRecipeItemsBy($recipeId);
       if($data == null) return '<p class="alert">Požiadavke nevyhovuje žiadny záznam</p>';
       $html = '';
       for($i=0 ; $i < count($data); $i++ ){
           $this->totalPrice += $data[$i]['quantity_kg'] *  $data[$i]['price'];
           $this->totalWeight += Converter::convert($data[$i]['id_unit'],$data[$i]['quantity_kg']);
           $html .= $this->getRecipeItemTableRow($data[$i]);
       }
       return  $html;
    }


    public function getResume(){
        return ' Celková hmotnosť dávok <span>'.$this->replaceDot($this->totalWeight).' kg</span>'.
               'Náklady na '.$this->replaceDot($this->totalWeight).' kg = <span>'.$this->formatPrice($this->totalPrice).'</span>';
    }
    
    /* ------------------------------------------------------ */
    
     private function replaceDot($val){
        return str_replace(".", ",", $val);
    }
    
    private function formatPrice($price, $round = 2){
        return number_format(round($price, $round),$round,","," ").' '.$this->priceUnit;
    }
    
    public function createNavigator($pageNumber, $peerPage){
        $nav = new Navigator( $this->recipeService->getCountOfAllProducts($_GET['q']) , $pageNumber , 
                    '/index.php?'.preg_replace("/&s=[0-9]*/", "", $_SERVER['QUERY_STRING']) , $peerPage);
        $nav->setSeparator("&amp;s=");
        $this->navigator =  $nav->smartNavigator();    
    }
    
    
    
    public function generateForm($recipeId = 0){
        if($colorId !== 0){
            $data =  $this->colorService->recievById($colorId);
        }
        return '
        <form class="ajaxSubmit"> 
                <div class="i ">
                    <label><em>*</em>Odtieň:</label><input value="'.
                ($colorId == 0 ?  $data[0]["code"] : "").'" 
                        maxlength="10" type="text" class="w100 required" name="code"/>
                </div> 	
                <div class="i odd">
                    <label><em>*</em>Názov/farba pigmentu:</label><input value="'.
                ($colorId == 0 ?  $data[0]["name"] : "").'" 
                        maxlength="45" type="text" class="w300 required" name="name"/>
                </div>
                <div class="i">
                    <label><em>*</em>Cena 1'.$this->weightUnit.'/'.$this->priceUnit.':</label><input value="'.
                            ($colorId == 0 ?  $data[0]["price"] : "").'"
                        maxlength="11" type="text" class="w100 r required" name="price" />
                          <span>Jednotka: </span><select class="w100" name="id_measurement">'.
                            getOptions( $this->conn, "measurement", "unit", $recipeId).'"
                        <select/>
                </div>
               
                <div class="i">
                    <input type="hidden" value="color" name="table" />
                    <input type="hidden" value="'.($colorId == 0 ? $this->CREATE : $this->UPDATE ).'" name="act" />
                    <input type="submit" class="ibtn" value="Uložiť" />'.
                    ($colorId == 0 ? '' : '<input type="hidden" value="'.$colorId.'" name="id" />')
                .'
                    <div class="clear"></div>
                </div>
            </form>';
        
    }
    
    public function getTotalPrice() {
        return $this->totalPrice;
    }

    public function getTotalWeight() {
        return $this->totalWeight;
    }


}
?>
