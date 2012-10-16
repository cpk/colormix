<?php
    
    $subpage = (!isset($_GET['sp']) ? "view" : $_GET['sp']);
    require_once  BASE_DIR."/service/OrderService.php";
    require_once  BASE_DIR."/service/OrderItemService.php";
    require_once  BASE_DIR."/service/OrderRecipeService.php";
    require_once  BASE_DIR."/presenter/OrderPresenter.php";
    require_once  BASE_DIR."/presenter/OrderItemPresenter.php";
    require_once  BASE_DIR."/presenter/OrderRecipePresenter.php";
    


?> 

<div id="left" class="box">
                <ul>
                    <li><a <?php echo isCurrent("view", $subpage)?> href="/index.php?p=order">Zobraziť objednávky</a></li>
                    <li><a <?php echo isCurrent("new", $subpage)?> href="/index.php?p=order&amp;sp=new">Pridať novú objednávku</a></li>
                </ul>
    
    
            </div>
            <div id="right" class="box">
                
                  <?php
            
            switch ($subpage){
                case "view" : 
                        include_once BASE_DIR."/view/order.view.php";
                    break;
                 case "new" : 
                        include_once BASE_DIR."/view/order.new.php";
                    break;
                case "edit" : 
                        include_once BASE_DIR."/view/order.edit.php";
                    break;
                case "redit" : 
                        include_once BASE_DIR."/view/order.redit.php";
                    break;
                default : 
                        include_once BASE_DIR."/view/404.php";
                    break;
            }
            
            ?>
                
                
                
            </div>
            <div class="clear"></div>