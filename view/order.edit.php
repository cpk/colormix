<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $os = new OrderService($conn);
    $order = $os->retriveById($_GET['id']);
    $oip = new OrderItemPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $os );
?>

<div class="tbox mw">
    <strong>Editácia objednávky: <b><?php echo $_GET['id']; ?></b></strong>
    <div class="tcontent">
        
        <?php 
            if(isset($_GET['copy']) && $_GET['copy'] == 1){
                echo '<p class="ok">Objednávka je úspešne duplikovaná, nezabudnite zmeniť dátum objednávky.</p>';
            }
        ?>
        
         <!-- Poznamka ========================== -->
        <div id="printBox">
        <a id="print" target="_blank" href="/index.php?p=print&amp;doc=order&amp;id=<?php echo $_GET['id']; ?>" title="Vytlačiť">Vytlačiť</a>
        <a id="copy" class="copyOrder"  href="#<?php echo $_GET['id']; ?>" title="Kopírovať">Duplikovať objednávku</a>
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
                <td><b><a href="/index.php?p=customer&sp=edit&id=<?php echo $order[0]['id_customer']; ?>"><?php echo $order[0]['name']; ?></a></b></td>
            </tr>
            <tr>
                <td>Adresa:</td>
                <td><?php echo $order[0]['street'].", ".$order[0]['zip'].", ".$order[0]['city'] ; ?></td>
            </tr>
            <tr>
                <td>IČO/DIČ:</td>
                <td><?php echo $order[0]['ico']." / ".$order[0]['dic'] ; ?></td>
            </tr>
            <?php if(strlen($order[0]['contact_person']) != 0){?>
            <tr>
                <td>Kontaktná osoba:</td>
                <td><a href="/index.php?p=customer&sp=edit&id=<?php echo $order[0]['id_customer']; ?>"><?php echo $order[0]['contact_person']; ?></a></td>
            </tr>
            <?php } ?> 
        </table>
        <div class="clear"></div>
        
        
       
        
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
            <div id="lastOrders"> 
                <h2 class="cstHead">Posledných 5 objednávok zákazníka <em><?php echo $order[0]['name']; ?></em> v ktorých sa nachádza zvolený tovar</h2>
                    <img src="/static/img/close.png" alt="Zatvorit" />
                    <div id="lastOrdersTable"></div>
                </div>
            <strong>Pridanie novej proložky do objednávky</strong>
            <form id="pf"> 
            <div>    
                <span class="fixsize">Položka objednávky:</span>
                <input type="text" class="w400" id="p" />
                <span class="margin-l">Množstvo: </span>
                <input maxlength="10" type="text" class="w50 c required" name="quantity_kg" />

                <span class="margin-l">Pč. balení: </span>
                <input maxlength="3" type="text" class="w50 c required" name="item_count" value="1" />
                <div class="clear"></div>
            </div> 
            <div class="odd">     
                <span class="fixsize">Percentuálny zisk: </span>
                <input maxlength="10" type="text" class="w100 c required" name="profit" />
                <span>%</span>
                <span  class="margin-l">Cena za jednotku predaj: </span>
                <input maxlength="10" type="text" class="w100 c required" name="price_sale" />
                <span><?php echo PRICE_UNIT; ?></span>
                <span class="margin-l">Náklady na 1kg: <em id="recipePrice">-</em><?php echo PRICE_UNIT; ?></span>
                <input type="submit" class="ibtn-sm flr" value="Pridať +" />
             </div>
            <input type="hidden" name="act"  value="13" />
            <input type="hidden" name="confirmed"  value="0" />
            <input type="hidden" name="id_customer"  value="<?php echo $order[0]['id_customer']; ?>" />
            <input type="hidden" name="id_order"  value="<?php echo $_GET['id']; ?>" />
            <input type="hidden" name="id_product"  value="0" />
            </form>
            
            <p class="info">Pri priádavní položky obejdnávky sa musí zobraziť zelená ikonka</p>
        </div>
    </div>
</div>
