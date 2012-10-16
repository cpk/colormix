<?php
    $subpage = (!isset($_GET['sp']) ? "view" : $_GET['sp']);
    require_once  BASE_DIR."/presenter/CustomerPresenter.php";
    require_once  BASE_DIR."/service/CustomerService.php";

?> 

<div id="left" class="box">
                <ul>
                    <li><a <?php echo isCurrent("view", $subpage)?> href="/index.php?p=customer">Zobraziť odberateľov</a></li>
                    <li><a <?php echo isCurrent("new", $subpage)?> href="/index.php?p=customer&amp;sp=new">Pridať nového odberateľov</a></li>
                </ul>
            </div>
            <div id="right" class="box">
                
                  <?php
            
            switch ($subpage){
                case "view" : 
                        include_once BASE_DIR."/view/customer.view.php";
                    break;
                 case "edit" : 
                        include_once BASE_DIR."/view/customer.edit.php";
                    break;
                 case "new" : 
                        include_once BASE_DIR."/view/customer.new.php";
                    break;
                default : 
                        include_once BASE_DIR."/view/404.php";
                    break;
            }
            
            ?>
                
                
                
            </div>
            <div class="clear"></div>