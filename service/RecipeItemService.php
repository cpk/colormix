<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RecipeItemService
 *
 * @author Peto
 */
class RecipeItemService {
    
    private $conn;
    
    public function __construct($conn) {
       
        if(!$conn instanceof Database){
            throw new Exception("Vyskytol sa problém s databázou.");
        }
        
        $this->conn = $conn;
    }
    
    
    /* to do -- */
    public function create($idRecipe, $idColor, $quantityKg){
        $this->validateRecipeItem($idRecipe, $idColor, $quantityKg);
        $this->conn->insert("INSERT INTO `product_item` (`id_product`, `id_color`, `quantity_kg`) VALUES (?,?,?)", 
                array( $idRecipe, $idColor, $quantityKg ));
    }
    
    public function update($quantityKg, $itemId){
         $this->checkQuantity($quantityKg);
         $this->conn->update("UPDATE `product_item` SET `quantity_kg`=? WHERE `id`=? LIMIT 1", 
                 array($quantityKg, $itemId));
    }
    
    
    public function delete($itemId){
        $this->conn->delete("DELETE FROM `product_item` WHERE `id`=?", array( $itemId ));
    }


    public function getRecipeItemsBy($recipeId){
        return $this->conn->select("SELECT i.`id`, c.`name`, c.`code` ,c.`supplier`, c.`price`, i.`quantity_kg`, m.`unit`, m.`id` as id_unit
                                    FROM `color` c, `product_item` i, measurement m
                                    WHERE i.`id_color`=c.`id` AND i.`id_product`=? AND m.`id`=c.`id_measurement` ORDER BY c.`code`, c.`name`", 
                array( $recipeId ));
    }
    
    
    
     private function validateRecipeItem($idRecipe, $idColor, $quantityKg){
        if($idColor == 0)
            throw new ValidationException("Nie je vybrat8 položka materiálu.");
 
        $this->checkQuantity($quantityKg);
        
        $r =  $this->conn->select("SELECT count(*) FROM `product_item` WHERE id_product=? AND id_color=?", 
                array( $idRecipe, $idColor, ));
        
        if($r[0]["count(*)"] == 1)
            throw new ValidationException("Položka sa už v receptúre nachádza.");
        
    }
    
    public function checkQuantity($quantityKg){
         if(!Validator::isFloat($quantityKg, 5))
            throw new ValidationException("Dávka na 1 kg obsahuje neplatnú hodnotu.");
        
    }
    
    public function isUsed($quantityKg){
         if(!Validator::isFloat($quantityKg, 5))
            throw new ValidationException("Dávka na 1 kg obsahuje neplatnú hodnotu.");
        
    }
}

?>
