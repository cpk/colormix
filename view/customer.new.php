<?php
    if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
    $cp = new CustomerPresenter($conn);
?>
<div class="tbox">
    <strong>Pridanie nového odberateľa</strong>
    <div class="tcontent">
        <?php echo $cp->generateForm();?>
    </div>
</div>