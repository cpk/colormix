<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    require_once  BASE_DIR."/service/OrderService.php";
    require_once  BASE_DIR."/service/OrderItemService.php";
    require_once  BASE_DIR."/service/OrderRecipeService.php";
    require_once  BASE_DIR."/presenter/OrderPresenter.php";
    require_once  BASE_DIR."/presenter/OrderItemPresenter.php";
    require_once  BASE_DIR."/presenter/OrderRecipePresenter.php";
    require_once  BASE_DIR."/presenter/OrderByRecipePresenter.php";
    $os =  new OrderService($conn);;
    $p = new OrderPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $os);
?>  

<p>Celková hodnota obejdnávok vyhovujúcim zadaným kritériam</p>
            
<span>Nákup: <?php echo number_format($os->getTotalPrice(), 2, ",", " "); ?> €</span>

<span>Predaj: <?php echo number_format($os->getTotalSalePrice(), 2, ",", " "); ?> €</span>

<span>Zisk: <?php echo number_format(round($os->getTotalSalePrice() - $os->getTotalPrice(),2),2 , ",", " "); ?>€ / 
<?php echo ($os->getTotalPrice() == 0 ? 0 : round((($os->getTotalSalePrice() - $os->getTotalPrice()) / $os->getTotalPrice()) * 100,2) )?>%</span>