<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    include_once BASE_DIR."/presenter/CustomerPresenter.php";
    $cp = new CustomerPresenter($conn);
    if(!isset($_GET['q'])) $_GET['q'] = null;
?>
<div class="tbox">
    <strong>Zoznam zákazníkov</strong>
    <div class="tcontent">
        
        <form class="filter" method="get" action="index.php">
            <input type="hidden" value="customer" name="p" />
            <input type="text" name="q" class="w250"  />
            <input type="submit" value="hladať" class="ibtn-sm" />
            <div id="act" class="hidden">10</div>
        </form>
        
        <?php echo $cp->printCustomers($_GET['s'], PEER_PAGE, $_GET['q']); ?>
    </div>
</div>