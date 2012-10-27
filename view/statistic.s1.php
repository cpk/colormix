<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    
    function profit($n, $p){
        if($p == 0) return "0";
        return  round(((($p - $n) / $n) * 100));
    }
    
    
    function printData($data){
        $array = array();
        if(count($data) > 0) $array[] = "['Odberateľ',  'Nákup EURO', 'Predaj EURO', 'Zisk %']";
        for($i = 0; $i < count($data); $i++){
            $array[] = "['".$data[$i]['name']."', ".$data[$i]['spolu_nakup'].", ".$data[$i]['spolu_predaj'].",".profit($data[$i]['spolu_nakup'],$data[$i]['spolu_predaj'])."]";
        }
        return implode(",", $array);
    }
?>

<div class="tbox">
    <strong>TOP 5 odberatlov</strong>
    
    <div class="tcontent">
    <form class="filter" method="get" action="/index.php">
                <input type="hidden" value="statistic" name="p" />   
                <span>Od dátumu:</span>
                <input type="text" name="dateFrom" value="<?php echo (isset($_GET['dateFrom'])? $_GET['dateFrom'] : ""); ?>" class="date w100" />
                <span> do:</span>
                <input type="text" name="dateTo" value="<?php echo (isset($_GET['dateTo'])? $_GET['dateTo'] : ""); ?>" class="date w100" />
                <input type="submit" value="zobraz" class="ibtn-sm" />
    </form>    
        
        <?php
            $cs = new StatisticService($conn);
            $data = $cs->getTopCustomers();
            if(count($data)==0){
                echo '<p class="alert">Požiadavne nevyhovuje žiadny záznam</p>';
            }else{
        ?>
            <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
                <?php echo  printData($data); ?>
            ]);

            var options = {
              title: 'TOP 5 odberaetľov',
              fontSize : 10
            };

            var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
            chart.draw(data, options);
          }
        </script>

    <div id="chart_div" style="width: 850px; height: 800px;"></div>
    <?php } ?>

    </div>
</div>