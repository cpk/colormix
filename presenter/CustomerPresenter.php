<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerPresenter
 *
 * @author Peto
 */
class CustomerPresenter {
   
    private $conn;
    
    private $UPDATE = 21;
    private $CREATE = 20;
    private $navigator;
    
    public function __construct($conn) {
        $this->customerService = new CustomerService($conn);
        $this->conn = $conn;
    }
 
    
     public function printCustomers($pageNumber, $peerPage, $searchQuery){
       $this->createNavigator($pageNumber, $peerPage, $searchQuery); 
        return $this->navigator."<table>".$this->getTableHead().'<tbody class="customer">'.
               $this->getTbody($pageNumber, $peerPage, $searchQuery).'</tbody></table>';
    }
    
    
    public function getTbody($pageNumber, $peerPage, $searchQuery){
       $data =  $this->customerService->retrieveCustomers($pageNumber, $peerPage, $searchQuery);
       if($data == null) return '<p class="alert">Objednávka neobsahuje žiadne položky</p>';
       $html = '';
       for($i=0 ; $i < count($data); $i++ ){
           $html .= $this->getRecipeItemTableRow($data[$i]);
       }
       return  $html;
    }
    
    public function getTableHead(){
        return '<tr><th>Názov</th>
                    <th>Mesto</th>
                    <th>PSČ</th>
                    <th>Ulica</th>
                    <th>IČO</th>
                    <th>DIČ</th>
                    <th>Upraviť</th>
                    <th>Zmazať</th></tr>';
    }
    
    private function getRecipeItemTableRow($row){
        return "<tr>".
                '<td>'.$row["name"].'</td>'.
                '<td>'.$row["city"].'</td>'.
                '<td>'.$row["zip"].'</td>'.
                '<td>'.$row["street"].'</td>'.
                '<td>'.$row["ico"].'</td>'.
                '<td>'.$row["dic"].'</td>'.                
                '<td class="c w50"><a class="edit" href="index.php?p=customer&amp;sp=edit&amp;id='.$row["id"].'">upraviť</a></td>'.
                '<td class="c w50"><a class="del" href="#id'.$row["id"].'"></a></td>'.
               "</tr>";
    }
    
    
    public function createNavigator($pageNumber, $peerPage, $searchQuery){
        $nav = new Navigator( $this->customerService->getCountCustomers( $searchQuery ) , $pageNumber , 
                    '/index.php?'.preg_replace("/&s=[0-9]*/", "", $_SERVER['QUERY_STRING']) , $peerPage);
        $nav->setSeparator("&amp;s=");
        $this->navigator =  $nav->smartNavigator();    
    }
    
    public function generateForm($customerId = 0){
        if($customerId != 0){
            $data =  $this->customerService->getCustomerById($customerId);
        }
        return '
        <form class="ajaxSubmit"> 
                <div class="i ">
                    <label><b>Meno/Firma:</b></label><input value="'.
                ($customerId != 0 ?  $data[0]["name"] : "").'" 
                        maxlength="100" type="text" class="w300 required" name="name"/>
                </div> 	
                <div class="i odd">
                    <label>Mesto:</label><input value="'.
                ($customerId != 0 ?  $data[0]["city"] : "").'" 
                        maxlength="45" type="text" class="w200" name="city"/>
                </div>
                <div class="i">
                    <label>Ulica:</label><input value="'.
                ($customerId != 0 ?  $data[0]["street"] : "").'" 
                        maxlength="45" type="text" class="w200" name="street"/>
                        <span>PSČ: <span><input value="'.($customerId != 0 ?  $data[0]["zip"] : "").'" 
                        maxlength="6" type="text" class="w100" name="zip"/>
                </div>
                <div class="i odd">
                    <label>IČO:</label><input value="'.
                ($customerId != 0 ?  $data[0]["ico"] : "").'" 
                        maxlength="8" type="text" class="w100" name="ico"/>
                </div>
                 <div class="i">
                    <label>DIČ:</label><input value="'.
                ($customerId != 0 ?  $data[0]["dic"] : "").'" 
                        maxlength="12" type="text" class="w100" name="dic"/>
                </div>
                <div class="i">
                    <input type="hidden" value="'.($customerId == 0 ? $this->CREATE : $this->UPDATE ).'" name="act" />
                    <input type="submit" class="ibtn" value="Uložiť" />'.
                    ($customerId == 0 ? '' : '<input type="hidden" value="'.$customerId.'" name="id" />') .'
                    <div class="clear"></div>
                </div>
            </form>';
        
    }
}

?>
