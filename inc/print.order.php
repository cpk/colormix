<?php 
	if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }

	
       if(isset($_GET['id'])) $_GET['id'] = (int)$_GET['id'];
        require_once  BASE_DIR."/service/OrderService.php";
        require_once  BASE_DIR."/service/OrderItemService.php";
        require_once  BASE_DIR."/service/OrderRecipeService.php";
        require_once  BASE_DIR."/presenter/OrderPresenter.php";
        require_once  BASE_DIR."/presenter/OrderItemPresenter.php";
        require_once  BASE_DIR."/presenter/OrderRecipePresenter.php";


?>
<!DOCTYPE HTML>
<html>
<head>
<title>COLOR MIX</title>
<meta charset="utf-8" />
<link rel="stylesheet" href="./static/css/print.css" media="print, screen" /> 
<!--[if IE]> <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>$(function(){ window.print(); });</script>
</head>
<body>
	<?php
    $os = new OrderService($conn);
    $data = $os->getOrderById($_GET['id']);
    $order = $os->retriveById($_GET['id']);
    $oip = new OrderItemPresenter($conn, WEIGHT_UNIT ,PRICE_UNIT, $os );
?>

        
    <div id="body">
        <!-- HEAD   ==========================  -->
        <h1 class="rcp">Objednávka: <?php echo $_GET['id']; ?></h1>
        


        <table class="info">
            <tr>
                <td>Číslo objednávky:</td>
                <td><strong><?php echo $_GET['id']; ?></strong></td>
            </tr>
            <tr>
                <td>Objednávka bola evidovaná:</td>
                <td><?php echo date('d.m.Y / h:i', strtotime( $order[0]['create'])); ?></td>
            </tr>
            <tr>
                <td>Dátum objednávky:</td>
                <td><?php echo date('d.m.Y', strtotime( $order[0]['date'])); ?></td>
            </tr>
             
        </table>
         
        <table  class="info">
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
        
        <!-- TABLE with items   ==========================  -->
        
        <?php   
            echo     $oip->printOrderItems($_GET['id']);
            //echo $orp->printRecipieItems(, $data[0]['id_order']);
        ?>
           
        
        <div class="total-price">
           <?php echo $oip->getResume(PRICE_UNIT); ?>
        </div> 
        <div class="clear"></div>
 </div>           
        
       
    
</body>
</html>
