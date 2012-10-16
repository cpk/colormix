<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    function printData($conn){
        $cs = new StatisticService($conn);
        $data = $cs->getTopCustomers();
        $array = array();
        if(count($data) > 0) $array[] = "['Odberateľ',  'Celková hodnota objednávok']";
        for($i = 0; $i < count($data); $i++){
            $array[] = "['".$data[$i]['name']." ',  ".(!isset($data[$i]['total_price']) ? 0 : $data[$i]['total_price'])."]";
        }
        return implode(",", $array);
    }
?>

<div class="tbox">
    <strong>TOP 10 zákazníkov</strong>
    <div class="tcontent">
        
        <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            <?php echo  printData($conn); ?>
        ]);

        var options = {
          title: 'TOP 10 odberaetľov',
          fontSize : 10
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <div id="chart_div" style="width: 810px; height: 500px;"></div>


    </div>
</div>