<?php
    $subpage = (!isset($_GET['sp']) ? "view" : $_GET['sp']);
    require_once  BASE_DIR."/presenter/ColorPresenter.php";
    require_once  BASE_DIR."/service/ColorService.php";

?> 

<div id="left" class="box">
                <ul>
                    <li><a <?php echo isCurrent("view", $subpage)?> href="/index.php?p=color">Zobraziť pigmenty</a></li>
                    <li><a <?php echo isCurrent("new", $subpage)?> href="/index.php?p=color&amp;sp=new">Pridať nový materiál</a></li>
                </ul>
            </div>
            <div id="right" class="box">
                
                  <?php
            
            switch ($subpage){
                case "view" : 
                        include_once BASE_DIR."/view/color.view.php";
                    break;
                 case "edit" : 
                        include_once BASE_DIR."/view/color.edit.php";
                    break;
                 case "new" : 
                        include_once BASE_DIR."/view/color.new.php";
                    break;
                default : 
                        include_once BASE_DIR."/view/404.php";
                    break;
            }
            
            ?>
                
                
                
            </div>
            <div class="clear"></div>