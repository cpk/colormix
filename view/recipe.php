<?php
    $subpage = (!isset($_GET['sp']) ? "view" : $_GET['sp']);
    require_once  BASE_DIR."/service/RecipeService.php";
    require_once  BASE_DIR."/service/RecipeItemService.php";
    require_once  BASE_DIR."/presenter/RecipePresenter.php";

?> 

<div id="left" class="box">
                <ul>
                    <li><a <?php echo isCurrent("view", $subpage)?> href="/index.php?p=recipe">Zobraziť tovar</a></li>
                    <li><a <?php echo isCurrent("new", $subpage)?> href="/index.php?p=recipe&amp;sp=new">Pridať novú receptúru</a></li>
                    <li><a <?php echo isCurrent("new2", $subpage)?> href="/index.php?p=recipe&amp;sp=new2">Pridať nový tovar</a></li>
                </ul>
            </div>
            <div id="right" class="box">
                
                  <?php
            
            switch ($subpage){
                case "view" : 
                        include_once BASE_DIR."/view/recipe.view.php";
                    break;
                 case "edit" : 
                        include_once BASE_DIR."/view/recipe.edit.php";
                    break;
                 case "new" : 
                        include_once BASE_DIR."/view/recipe.new.php";
                    break;
                 case "new2" : 
                        include_once BASE_DIR."/view/recipe.new2.php";
                    break;
                default : 
                        include_once BASE_DIR."/view/404.php";
                    break;
            }
            
            ?>
                
                
                
            </div>
            <div class="clear"></div>