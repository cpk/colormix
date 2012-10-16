<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
$rs = new RecipeService($conn);
    $rp = new RecipePresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $rs);
    $recipieData = $rs->getRecipeById($_GET['id']);
    
    if($recipieData == null){
        echo '<p class="alert">Požadovaná položka sa v databáze nenachádza.</p>';
        exit;
    }
    
?>
<div class="tbox">
    <strong>Editácia <?php echo ($recipieData[0]['recipe'] == 0? 'tovaru' : 'receptúry')?></strong>
    <div class="tcontent">
        <?php 
            if($recipieData[0]['recipe'] == 1){
        ?>  
        
        <form class="ajaxSubmit"> 
                <div class="i ">
                    <p>Receptúra bola vytovorená: <b><?php echo $recipieData[0]['create']; ?></b></p>
                    <p>Objednalo si ju: <a href="">0 zákazníkov</a></p>
                </div> 	
                <div class="i ">
                    <span>Kód:</span><input 
                        maxlength="10" type="text" class="w100 required" name="code" value="<?php echo $recipieData[0]['code']; ?>"/>
                     <span>Názov:</span><input
                        maxlength="255" type="text" class="w280 required" name="label" value="<?php echo $recipieData[0]['label']?>"/>
                      <input type="submit" class="ibtn no-margin" value="Uložiť zmeny" />
                      <input type="hidden" value="6" name="act" />
                      <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
                </div> 	
            </form>
        <br />
        <form class="inlineEditing">
        <?php
            echo $rp->printRecipieItems($_GET['id'])
        ?>
            <input type="hidden" name="act" value="8" />
            <input type="hidden" value="product_item" name="table" />
            <input type="hidden" name="recepeId"  value="<?php echo $_GET['id']; ?>" />
        </form>
        <div class="totalPrice">
           <?php echo $rp->getResume();?>
        </div> 
            
        
        <div class="add-recipe">
            <form id="recipe-item"> 
                <select name="id_color" class="w400">
                        <?php echo getColorOptions( $conn); ?>
                </select>
            
            <span>Dávka na 1kg: </span>
            <input maxlength="10" type="text" class="w100 r required" name="quantity_kg" />
            <span id="unit"></span>
            <input type="submit" class="ibtn-sm" value="Pridať" />
            <input type="hidden" name="act"  value="7" />
            <input type="hidden" name="id"  value="<?php echo $_GET['id']; ?>" />
            </form>
        </div>
        
          <?php 
            }else{
            ?> 
            
         <form  class="ajaxSubmit">
            <div class="i ">
                    <p>Tovar bol pridaný: <b><?php echo $recipieData[0]['create']; ?></b></p>
                    <p>Objednalo si ju: <a href="">0 zákazníkov</a></p>
            </div> 	
            <div class="i">
                    <label>Kód:</label><input maxlength="10" type="text" class="w100 required" name="code" 
                                              value="<?php echo $recipieData[0]['code']; ?>"/>
            </div>
            <div class="i odd">
                    <label>Názov:</label><input maxlength="45" type="text" class="w280 required" name="label"
                                                value="<?php echo $recipieData[0]['label']; ?>"/>
            </div> 
             <div class="i">
                    <label>Cena tovaru za ks:</label><input maxlength="10" type="text" class="w100 required" name="price" 
                                               value="<?php echo (float)$recipieData[0]['price']; ?>"/> <span><?php echo PRICE_UNIT; ?></span>
             </div>
             <div class="i odd">
                      <input type="submit" class="ibtn no-margin" value="Uložiť zmeny" />
                      <input type="hidden" value="6" name="act" />
                      <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id" />
             </div> 
        </form>
        
        <?php } ?>
    </div>
</div>

