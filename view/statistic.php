            <?php 
                $subpage = (!isset($_GET['sp']) ? "s1" : $_GET['sp']); 
                     require_once  BASE_DIR."/service/StatisticService.php";
            ?>
            <div id="left" class="box">
                <ul>
                    <li><a <?php echo isCurrent("s1", $subpage)?> href="/index.php?p=statistic">TOP 5 odberateľov</a></li>
                    <li><a <?php echo isCurrent("s2", $subpage)?> href="/index.php?p=statistic&amp;sp=s2">Mesačný pehľad</a></li>
                   <!-- <li><a <?php echo isCurrent("s3", $subpage)?> href="/index.php?p=statistic&amp;sp=s3">Štatistika tovaru</a></li> -->
                </ul>
    
    
            </div>
            <div id="right" class="box">
                
                  <?php
            
            switch ($subpage){
                case "s1" : 
                        include_once BASE_DIR."/view/statistic.s1.php";
                    break;
                case "s2" : 
                        include_once BASE_DIR."/view/statistic.s2.php";
                    break;
                case "s3" : 
                        include_once BASE_DIR."/view/statistic.s3.php";
                    break;

                default : 
                        include_once BASE_DIR."/view/404.php";
                    break;
            }
            
            ?>
                
                
                
            </div>
            <div class="clear"></div>