<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    include_once BASE_DIR."/presenter/ColorPresenter.php";
    $cp = new ColorPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT);
?>
<div class="tbox">
    <strong>Pigmenty</strong>
    <div class="tcontent">
        <?php echo $cp->printColors(); ?>
    </div>
</div>