<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $cp = new CustomerPresenter($conn);
?>
<div class="tbox">
    <strong>Editácia nového odberateľa</strong>
    <div class="tcontent">
        <div id="editBox">
        <?php echo $cp->generateForm($_GET['id']);?>
        </div>
    </div>
</div>