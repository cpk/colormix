<?php

class ColorPresenter{
    
    private $colorService;
    private $priceUnit;
    private $weightUnit;
    private $conn;
    private $UPDATE = 1;
    private $CREATE = 2;

    public function __construct($conn, $weightUnit, $priceUnit) {
        $this->priceUnit = $priceUnit;
        $this->weightUnit = $weightUnit;
        $this->colorService = new ColorService($conn);
        $this->conn = $conn;
    }
    
    
    public function printColors(){
       $data =  $this->colorService->recievAllColors();
       $html = "<table>";
       $html .= $this->getTableHead().'<tbody class="color">';
       for($i=0 ; $i < count($data); $i++ ){
           $html .= $this->getTableRow($data[$i]);
       }
       $html .= "</tbody></table>";
       return $html;
    }
    
 
    public function getTableHead(){
        return '<tr><th>Kód</th><th>Názov</th><th>Cena</th><th>Upraviť</th><th>Zmazať</th></tr>';
    }
    
    
    public function getTableRow($row){
        return "<tr>".
                '<td class="l">'.$row["code"].'</td>'.
                '<td>'.$row["name"].'</td>'.
                '<td class="r">'.floatval($row["price"]).' '.$this->priceUnit.'/'.$row["unit"].'</td>'.
                '<td class="c w50"><a class="edit" href="./index.php?p=color&amp;sp=edit&amp;id='.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50"><a class="del" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    
    public function generateForm($colorId = 0){
        if($colorId != 0){
            $data =  $this->colorService->recievById($colorId);
        }
        return '
        <form class="ajaxSubmit"> 
                <div class="i ">
                    <label>Kód výrobku/pigmentu:</label><input value="'.
                ($colorId != 0 ?  $data[0]["code"] : "").'" 
                        maxlength="10" type="text" class="w100 required" name="code"/>
                </div> 	
                <div class="i odd">
                    <label>Názov/farba pigmentu:</label><input value="'.
                ($colorId != 0 ?  $data[0]["name"] : "").'" 
                        maxlength="45" type="text" class="w300 required" name="name"/>
                </div>
                <div class="i">
                    <label>Cena za jednotku:</label><input value="'.
                            ($colorId != 0 ?  floatval($data[0]["price"]) : "").'"
                        maxlength="11" type="text" class="w100 r required" name="price" />
                       <span>'.$this->priceUnit.'</span>   
                </div>
                <div class="i odd">
                    <label>Riedidlo/obal:</label><input '.
                               (isset($data[0]["riedidlo"]) && $data[0]["riedidlo"] == 1 ? 'checked="checked"' : "").'" 
                                type="checkbox" name="riedidlo" /><span>Nebude sa prepočítavať celkovou hmotnosťou</span>
                </div>
                 <div class="i ">
                        <label>Jednotka: </label><select class="w100" name="id_measurement">'.
                            getOptions( $this->conn, "measurement", "unit", ($colorId == 0 ? 0 : $data[0]["id_measurement"])).'
                        </select>
                </div>
               
                <div class="i odd">
                    <input type="hidden" value="color" name="table" />
                    <input type="hidden" value="'.($colorId == 0 ? $this->CREATE : $this->UPDATE ).'" name="act" />
                    <input type="submit" class="ibtn" value="Uložiť" />'.
                    ($colorId == 0 ? '' : '<input type="hidden" value="'.$colorId.'" name="id" />') .'
                    <div class="clear"></div>
                </div>
            </form>';
        
    }
    
    
    
    
}

?>
