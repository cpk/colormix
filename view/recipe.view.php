<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    if(isset($_GET['profit'])){
        $_SESSION['profit'] = intval($_GET['profit']);
    }
    if(!isset($_SESSION['peerPage'])){
        $_SESSION['peerPage'] = PEER_PAGE;
    }else if(isset($_GET['peerPage'])){
        $_SESSION['peerPage'] = intval($_GET['peerPage']);
    }

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
        
        <form method="get" action="index.php?p=recipe" class="profit">
            <input type="hidden" value="recipe" name="p" />
            <label>Pč. položiek na stránke
                <input type="text" name="peerPage" class="w30" value="<?php echo  $_SESSION['peerPage'] ?>"/>
            </label>
            <label>Zľava
            <input type="text" name="profit" class="w30" value="<?php echo  $rp->getProfit() ?>"/>
            %</label>
            <input type="submit" value="Uložiť" class="ibtn-sm" />
            <div id="act" class="hidden">19</div>
            <a href="#" class="print" onClick="window.print();return false" title="Tlačiť"></a>
        </form>
        
        <div class="clear"></div>
        <?php echo $rp->printRecipies($_GET['s'], $_SESSION['peerPage'] , $_GET['q']); ?>
        <div class="clear"></div>
    </div>
</div>