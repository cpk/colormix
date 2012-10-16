<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $rp = new RecipePresenter($conn, WEIGHT_UNIT ,PRICE_UNIT);
    if(!isset($_GET['q'])) $_GET['q'] = null;
?>
<div class="tbox">
    <strong>Tovar a receptúry</strong>
    <div class="tcontent">
        
        <form class="filter" method="get" action="index.php?p=recipe">
            <input type="hidden" value="recipe" name="p" />
            <input type="text" name="q" class="w250" />
            <input type="submit" value="hladať" class="ibtn-sm" />
            <div id="act" class="hidden">19</div>
        </form>
        
        
        <div class="clear"></div>
        <?php echo $rp->printRecipies($_GET['s'], PEER_PAGE, $_GET['q']); ?>
        <div class="clear"></div>
    </div>
</div>