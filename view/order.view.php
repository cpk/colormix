<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $os =  new OrderService($conn);;
    $p = new OrderPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $os);
    
    function printOptionOrders(){
        if(!isset($_GET['orderBy']))
            return '<option value="0">-- Zoradiť podľa -- </option><option value="1">Odberateľa A-Z</option>'.
                    '<option value="2">Odberateľa Z-A</option><option value="3">Najnovšej</option>'.
                    '<option value="4">Najstaršej</option>';
        switch ((int)$_GET['orderBy']){
            case 0 : 
                return '<option value="0">-- Zoradiť podľa -- </option><option value="1">Odberateľa A-Z</option>'.
                    '<option value="2">Odberateľa Z-A</option><option value="3">Najnovšej</option>'.
                    '<option value="4">Najstaršej</option>';
            case 1 : 
                return '<option value="1">Odberateľa A-Z</option><option value="0">-- Zoradiť podľa -- </option>'.
                    '<option value="2">Odberateľa Z-A</option><option value="3">Najnovšej</option>'.
                    '<option value="4">Najstaršej</option>';
            case 2 : 
                return '<option value="2">Odberateľa Z-A</option><option value="0">-- Zoradiť podľa -- </option>'.
                    '<option value="1">Odberateľa A-Z</option><option value="3">Najnovšej</option>'.
                    '<option value="4">Najstaršej</option>';
            case 3 : 
                return '<option value="3">Najnovšej</option><option value="0">-- Zoradiť podľa -- </option>'.
                        '<option value="1">Odberateľa A-Z</option><option value="2">Odberateľa Z-A</option>'.
                    '<option value="4">Najstaršej</option>';
            case 4 : 
                return '<option value="4">Najstaršej</option><option value="0">-- Zoradiť podľa -- </option>'.
                        '<option value="1">Odberateľa A-Z</option><option value="2">Odberateľa Z-A</option>'.
                        '<option value="3">Najnovšej</option>';    
               
        }
    }
?>

<div class="tbox">
    <strong>Obejdnávky</strong>
    <div class="tcontent">
        
        
        <form class="filter" method="get" action="index.php?p=recipe">
            <input type="hidden" value="order" name="p" />
                <select name="orderBy">
                   <?php echo printOptionOrders(); ?>
                </select>
                <span>Od dátumu:</span>
                <input type="text" name="dateFrom" value="<?php echo (isset($_GET['dateFrom'])? $_GET['dateFrom'] : ""); ?>" class="date w100" />
                <span> do:</span>
                <input type="text" name="dateTo" value="<?php echo (isset($_GET['dateTo'])? $_GET['dateTo'] : ""); ?>" class="date w100" />
                <span> Odberateľ:</span>
                <input type="text" name="q"  class="w200" value="<?php echo (isset($_GET['q'])? $_GET['q'] : ""); ?>" />
                <input type="submit" value="zobraz" class="ibtn-sm" />

            
            <div id="act" class="hidden">10</div>
        </form>
        
        
        
        <?php echo $p->printOrders($_GET['s'], PEER_PAGE); ?>
        <div class="clear"></div>
        
        <div id="stats-info">
            <p>Celková hodnota obejdnávok vyhovujúcim zadaným kritériam</p>
            
            <span>Nákup: <?php echo number_format(round($os->getTotalPrice(),2), 2); ?> €</span>
            
            <span>Predaj: <?php echo number_format(round($os->getTotalSalePrice(),2), 2); ?> €</span>
            
            <span>Zisk: <?php echo number_format(round($os->getTotalSalePrice() - $os->getTotalPrice(),2), 2); ?>€ / 
                <?php echo ($os->getTotalPrice() == 0 ? 0 : round((($os->getTotalSalePrice() - $os->getTotalPrice()) / $os->getTotalPrice()) * 100,2) )?>%</span>
        </div>
    </div>
</div>