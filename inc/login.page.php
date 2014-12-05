<?php
	//if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }

	
       $page = (!isset($_GET['p']) ? "color" : $_GET['p']);
       $_GET['s'] = (!isset($_GET['s']) ? 1 : (int)$_GET['s']);
       if(isset($_GET['id'])) $_GET['id'] = (int)$_GET['id'];

function isCurrent($pageName, $param){
    return ($pageName == $param ? 'class="curr"' : '');
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>COLOR MIX</title>
<meta charset="utf-8" />
<meta name="robots" content="noindex,nofollow"/>
    
<!-- styles & js -->
<link rel="stylesheet" href="./static/css/main.css" /> 
<link rel="stylesheet" href="./static/css/blitzer/jquery-ui-1.8.21.custom.css" /> 
<link rel="stylesheet" href="./static/css/jquery.switchButton.css" /> 
<!--[if IE]> <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script src="/static/js/jquery.switchButton.js"></script>
<?php if($page == "statistic") echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>'; ?>
<script src="./static/js/scripts.js"></script>
 <script>
	$(function() {
		$('form[name=login]').submit(function (){
			if(!validate($(this))){
				return false;
			}
		});
		$('input[name=login]').focus();
         $("input[type=checkbox]").switchButton();
	});

	</script>
</head>
<body>
	<header>
            <a href="./"><img src="/static/img/logo.png" alt="COLORMIX"></a>
        </header>
       
        <section>
           <div id="status"></div>
        <div id="form">
        	
                <form method="POST" action="./inc/log.in.php" name="login" class="loginPage">
                    <h3>Prihlasovacia stránka</h3>
            	<?php echo (isset($_SESSION["status"]) ? '<p class="error">'.$_SESSION["status"].'</p>' : ""); unset($_SESSION["status"]); ?>
                <div><label>Prihlasovacie meno: </label><input type="text" name="login"  class="w200 required" /></div>
                <div><label>Prihlasovacie heslo: </label><input type="password" name="pass" class="w200 required fiveplus" /></div>
                <div class="switch-wrapper">
                  <input type="checkbox" value="1" name="supplier" checked>
                </div>
                <input type="hidden" name="token" value="<?php echo session_id(); ?>" / >
                <input type="submit" name="btn" value="Prihlásiť" class="ibtn" />
                <div class="clear"></div>
            </form>
        </div>
        </section>
        <div id="status"></div><div id="loader">Čakajte...</div>
</body>
</html>
