<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
     function printData($conn){
        $cs = new StatisticService($conn);
        $data = $cs->getMonthlyReportOfLastYear();
        $array = array();
        if(count($data) > 0) $array[] = "['Obdobie',  'Hodnota objednávok v EURO']";
        for($i = 0; $i < count($data); $i++){
            $array[] = "['".$data[$i]['m']." ".$data[$i]['y']." ',  ".(!isset($data[$i]['total_price']) ? 0 : $data[$i]['total_price'])."]";
        }
        return implode(",", $array);
    }
?>

<div class="tbox">
    <strong>Mesačný prehľad objednávok za posledný rok</strong>
    <div class="tcontent">
        
       <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          <?php echo printData($conn); ?>
        ]);

        var options = {
          title: 'Mesačný pehľad'
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

    <div id="chart_div" style="width: 900px; height: 400px;"></div>


    </div>
</div>