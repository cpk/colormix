<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $os = new OrderService($conn);
    $order = $os->retriveById($_GET['id']);
    $oip = new OrderItemPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $os );

?>
<div class="tbox mw">
    <strong>Editácia objednávky: <b><?php echo $_GET['id']; ?></b></strong>
    <div class="tcontent">
        
        <!-- TABULKY OBEJDNAVKY ========================== -->
        <form class="ajaxSubmit nostyle">
        <table class="cst">
            <tr>
                <td>Objednávka bola evidovaná:</td>
                <td><?php echo date('d.m.Y / h:i', strtotime( $order[0]['create'])); ?></td>
            </tr>
            <tr>
                <td>Dátum objednávky:</td>
                <td><?php echo date('d.m.Y', strtotime( $order[0]['date'])); ?>&nbsp;&nbsp;<a id="dt" href="#">zmeniť</a></td>
            </tr>
             <tr>
                <td>Objednávku zaevidoval:</td>
                <td><?php echo $order[0]['givenname'].' '.$order[0]['surname']; ?></td>
            </tr>
        </table>
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
            <input type="hidden" name="act" value="23" />
        </form>

        <table  class="cst">
            <tr>
                <td>Objednávateľ:</td>
                <td><b><?php echo $order[0]['name']; ?></b></td>
            </tr>
            <tr>
                <td>Adresa:</td>
                <td><?php echo $order[0]['street'].", ".$order[0]['zip'].", ".$order[0]['city'] ; ?></td>
            </tr>
            <tr>
                <td>IČO/DIČ:</td>
                <td><?php echo $order[0]['ico']." / ".$order[0]['dic'] ; ?></td>
            </tr>
             
        </table>
        <div class="clear"></div>
        
        
        <!-- Poznamka ========================== -->
        <div id="printBox">
        <a id="print" target="_blank" href="/index.php?p=print&amp;doc=order&amp;id=<?php echo $_GET['id']; ?>" title="Vytlačiť">Vytlačiť</a>
        <form class="ajaxSubmit " id="note">
            <?php
                if(strlen(($order[0]['label'])) == 0 || $order[0]['label']== "Poznámka k objednávke...") 
                   echo ' <textarea name="label" class="inactive">Poznámka k objednávke...</textarea>';
                else
                   echo ' <textarea name="label">'.$order[0]['label'].'</textarea>';
            ?>
            <input type="hidden" name="act" value="22" />
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
            <input type="submit" class="ibtn-sm" value="Uložiť" />
            <div class="clear"></div>
        </form>
         <div class="clear"></div>
        </div>
        
        <!-- POLOZKY OBEJDNAVKY ========================== -->
        
        
        <h1>Položky obejdnávky</h1>
        <form class="inlineEditing">
        <?php
            echo $oip->printOrderItems($_GET['id']); 
        ?>
            <input type="hidden" name="act" value="14" />
            <input type="hidden" value="order_item" name="table" />
            <input type="hidden" name="orderId"  value="<?php echo $_GET['id']; ?>" />
        </form>
        <div class="totalPrice">
           <?php echo $oip->getResume(PRICE_UNIT); ?>
        </div> 
        
        
        <!-- PRIDANIE POLOZKY OBEJDNAVKY ========================== -->
        
        <div class="add-product">
            <form id="pf"> 
                <span class="fixsize">Položka objednávky</span>
            <input type="text" class="w300" id="p" />
            <span  class="fixsize">Množstvo: </span>
            <input maxlength="10" type="text" class="w50 c required" name="quantity_kg" />
            
            <span  class="fixsize">Cena za j. predaj: </span>
            <input maxlength="10" type="text" class="w50 c required" name="price_sale" />
            
            <input type="submit" class="ibtn-sm" value="Pridať" />
            <input type="hidden" name="act"  value="13" />
            <input type="hidden" name="id_order"  value="<?php echo $_GET['id']; ?>" />
            <input type="hidden" name="id_product"  value="0" />
            </form>
            
            <p class="info">Pri priádavní položky obejdnávky sa musí zobraziť zelená ikonka</p>
        </div>
    </div>
</div>