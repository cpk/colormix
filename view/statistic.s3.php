<?php

    function profit($n, $p){
        if($p == 0) return "0";
        return  round(((($p - $n) / $n) * 100));
    }
    
    
    function printData($data){
        $array = array();
        if(count($data) > 0) $array[] = "['Dátum',  'Nákup EURO', 'Predaj EURO', 'Zisk %']";
        for($i = 0; $i < count($data); $i++){
            $array[] = "['".$data[$i]['m']."/".$data[$i]['y']."', ".
                          $data[$i]['spolu_nakup'].", ".
                          $data[$i]['spolu_predaj'].", ".
                          profit($data[$i]['spolu_nakup'],$data[$i]['spolu_predaj'])."]";
        }
        return implode(",", $array);
    }
?>

<div class="tbox">
    <strong>Mesačný prehľad objednávok</strong>
    <div class="tcontent">
    <form class="filter" method="get" action="/index.php">
                <input type="hidden" value="statistic" name="p" /> 
                <input type="hidden" value="s3" name="sp" /> 
                <span>Od dátumu:</span>
                <input type="text" name="dateFrom" value="<?php echo (isset($_GET['dateFrom'])? $_GET['dateFrom'] : ""); ?>" class="date w100" />
                <span> do:</span>
                <input type="text" name="dateTo" value="<?php echo (isset($_GET['dateTo'])? $_GET['dateTo'] : ""); ?>" class="date w100" />
                <span> Odberateľ:</span>
                <input type="text" name="q"  class="w300" value="<?php echo (isset($_GET['q'])? $_GET['q'] : ""); ?>" />
                <input type="submit" value="zobraz" class="ibtn-sm" />
                <div id="act" class="hidden">10</div>
    </form> 
        
        <?php
            $cs = new StatisticService($conn);
            $data = $cs->getMonthlyReport();
            if(count($data)==0){
                echo '<p class="alert">Požiadavne nevyhovuje žiadny záznam</p>';
            }else{
        ?>
      


    <div id="chart_div" style="width: 900px; height: 450px;"></div>
    <?php } ?>

    </div>
</div>