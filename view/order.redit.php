<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $ors = new OrderRecipeService($conn);
    $orp = new OrderRecipePresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $ors);
    $data = $ors->getRecipeInfo($_GET['id']);

?>
<div class="tbox">
    <strong>Editácia </strong>
    <div class="tcontent">
        
        
        
        <!-- HEAD   ==========================  -->
        <h1 class="rcp">Receptúra: <em><?php echo $data[0]['code']." - ". $data[0]['label']; ?></em></h1>
        <a id="print" target="_blank" href="/index.php?p=print&amp;doc=recipe&amp;id=<?php echo $_GET['id']; ?>" title="Vytlačiť receptúru"></a>
        <form class="edit-rcp" id="ercp">
            <span>Cena predaj / kg:</span>
            <input type="text" name="price_sale" class="c w50 required" value="<?php echo (float)$data[0]['price_sale']; ?>" /><?php echo PRICE_UNIT; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span>Počet:</span>
            <input type="text" name="quantity" class="c w50 required" value="<?php echo (float)$data[0]['quantity']; ?>" /><?php echo WEIGHT_UNIT; ?>
            <input type="submit" class="ibtn-sm" value="Uložiť" />
            <input type="hidden" name="act"  value="15" />
            <input type="hidden" name="id"  value="<?php echo $_GET['id']; ?>" />
        </form>
        <a href="index.php?p=order&amp;sp=edit&amp;id=<?php echo $data[0]['id_order']; ?>" title="Späť k objednávke" id="back">&laquo; späť</a>
        <div class="clear"></div>
        
        
        
        
        
        <!-- TABLE with items   ==========================  -->
        <form class="inlineEditing ercp">
        <?php
            echo $orp->printRecipieItems($_GET['id'], $data[0]['id_order']);
        ?>
            <input type="hidden" name="act" value="16" />
            <input type="hidden" value="order_item" name="table" />
            <input type="hidden" name="idOrder"  value="<?php echo $data[0]['id_order']; ?>" />
            <input type="hidden" name="recepeId"  value="<?php echo $_GET['id']; ?>" />
        </form>
        
        <div class="totalPrice">
           <?php echo $orp->getResume();?>
        </div> 
            
        
        
        <!-- ADDING FORM   ==========================  -->
        
        <div class="add-recipe">
            <form id="recipe-item-order"> 
                <select name="id_color" class="w400">
                        <?php echo getColorOptions( $conn); ?>
                </select>
            
            <span>Dávka na 1kg: </span>
            <input maxlength="10" type="text" class="w50 c required" name="quantity_kg" />
            <span id="unit"></span>
            <span>Cena za j.: </span>
            <input maxlength="10" type="text" class="w50 c required" name="price" />
            <input type="submit" class="ibtn-sm" value="Pridať" />
            <input type="hidden" name="act"  value="18" />
            <input type="hidden" name="idOrder"  value="<?php echo $data[0]['id_order']; ?>" />
            <input type="hidden" name="id"  value="<?php echo $_GET['id']; ?>" />
            </form>
        </div>
        
    </div>
</div>

