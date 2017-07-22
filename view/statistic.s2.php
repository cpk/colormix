<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
      function profit($n, $p){
        if($p == 0) return "0";
        return  round(((($p - $n) / $n) * 100));
    }
    
     function opitons( $conn){
	$html = "";
	$array =  $conn->select("SELECT `givenname`,`surname` FROM `user`");	
	$c = count($array); 
	for($j=0; $j < $c;$j++) {   
            $html .= "<option value=\"".$array[$j]["surname"]."\">".$array[$j]["givenname"].' '.$array[$j]["surname"]."</option>\n";
	}   
	return $html;
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
                <input type="hidden" value="s2" name="sp" />
                <span>Uživatľ:</span>
                <select name="surname" style="display: inline;float: none;">
                    <option value="0">-- Vybrať uživateľa --</option>
                    <?php echo opitons( $conn); ?>
                </select>
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
                
                if(isset($_GET['surname'])) echo '<h2>Objednávky zaevidované uživateľom: '.$_GET['surname'].'</h2>';
        ?>
        
       <script type="text/javascript">
        google.load('visualization', '1', {packages: ['corechart']});    
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
                <?php echo  printData($data); ?>
            ]);

        var options = {
          title : 'Mesačný prehľad objednávok <?php echo (isset($_GET['q']) ? $_GET['q'] : "");?>',
          vAxis: {title: "Hodnota"},
          hAxis: {title: "Mesiac"},
          seriesType: "bars"
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
      google.setOnLoadCallback(drawVisualization);
    </script>


    <div id="chart_div" style="width: 950px; height: 450px;"></div>
    <?php } ?>

    </div>
</div>