<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }

	
       $page = (!isset($_GET['p']) ? "color" : $_GET['p']);
       $_GET['s'] = (!isset($_GET['s']) ? 1 : (int)$_GET['s']);
       if(isset($_GET['id'])) $_GET['id'] = (int)$_GET['id'];

function isCurrent($pageName, $param){
    return ($pageName == $param ? 'class="curr"' : '');
}
    if(isset($_GET['switch'])){
        $_SESSION['supplier'] = $_SESSION['supplier'] == 1 ? 2 : 1;
    }
?>
<!DOCTYPE HTML>
<html>
<head>
<title>COLOR MIX</title>
<meta charset="utf-8" />
<meta name="robots" content="noindex,nofollow"/>
    
<!-- styles & js -->
<link rel="stylesheet" href="./static/css/main.css?v=1" /> 
<link rel="stylesheet" media="print"  href="./static/css/main.print.css" /> 
<link rel="stylesheet" href="./static/css/chosen.min.css" /> 
<link rel="stylesheet" href="./static/css/blitzer/jquery-ui-1.8.21.custom.css" /> 
<link rel="stylesheet" href="./static/css/<?php echo $_SESSION['supplier']; ?>.css" /> 
<!--[if IE]> <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="./static/js/chosen.jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<?php if($page == "statistic") echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>'; ?>
<script src="./static/js/scripts.js?v=1"></script>
</head>
<body>
	<header>
            <a id="logo" href="./"><img src="/static/img/logo.png" alt="COLORMIX"> <span><?php echo $_SESSION['supplier'] == 1 ? 'VITON' : 'COLORWEST' ?></span></a>

            <div id="switch">
                <span>Prepnúť na dodávateľa:</span>
                <a href="?<?php echo $_SERVER['QUERY_STRING']; ?>&amp;switch=true"><?php echo $_SESSION['supplier'] == 1 ?  'COLORWEST' : 'VITON' ?></a>
            </div>
    </header>
        <nav>
            <ul class="shadow">
                <li><a <?php echo isCurrent("color", $page)?> href="/index.php?p=color">Správa materiálu</a></li>
                <li><a <?php echo isCurrent("recipe", $page)?> href="/index.php?p=recipe">Správa tovaru a receptúr</a></li>
                <li><a <?php echo isCurrent("order", $page)?> href="/index.php?p=order">Správa objednávok</a></li>
                <li><a <?php echo isCurrent("customer", $page)?> href="/index.php?p=customer">Správa odberateľov</a></li>
                <li><a <?php echo isCurrent("statistic", $page)?> href="/index.php?p=statistic">Štatistiky</a></li>
                <li><a href="/inc/log.out.php">Odhlásiť</a></li>
            </ul>
        </nav>
        <section>
            <?php
            
            switch ($page){
                case "color" : 
                        include_once BASE_DIR."/view/color.php";
                    break;
                case "recipe" : 
                        include_once BASE_DIR."/view/recipe.php";
                    break;
                case "order" : 
                        include_once BASE_DIR."/view/order.php";
                    break;
                case "customer" : 
                        include_once BASE_DIR."/view/customer.php";
                    break;
                case "statistic" : 
                        include_once BASE_DIR."/view/statistic.php";
                    break;
                default : 
                        include_once BASE_DIR."/view/404.php";
                    break;
            }
            
            ?>
        </section>
        <div id="status"></div><div id="loader">Čakajte...</div>
</body>
</html>
