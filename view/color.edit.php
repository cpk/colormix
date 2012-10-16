<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $cp = new ColorPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT);
?>
<div class="tbox">
    <strong>Editácia pigmentu</strong>
    <div class="tcontent">
        <?php echo $cp->generateForm($_GET['id']);?>
    </div>
</div>