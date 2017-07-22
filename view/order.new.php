<div class="tbox">
    <strong>Pridanie novej objednávky</strong>
    <div class="tcontent">
        <form id="new-order">
            <div class="i">
                <div id="cust-input">
                     <label><b>Objednávateľ</b>:</label>
                     <input maxlength="100" type="text" class="w300 required" name="customer"/>
                </div>
                <div id="cust-descr">
                   
                </div> 
            </div>
            <div class="i odd">
                    <label><b>Dátum objednávky</b>:</label>
                    <input maxlength="10" type="text" class="w100 date required" name="date"/>
            </div> 
             <div class="i">
                    <label>Poznámka:</label>
                    <textarea name="label" class="w300 h100" rows="10" cols="30"></textarea>
             </div>
             <div class="i odd">
                    <input type="submit" class="ibtn cst" value="Uložiť a pokračovať &raquo;" />
                    <div class="clear"></div>
             </div> 
                    <input type="hidden"  value="11" name="act" />
                    <input type="hidden"  value="0" name="id_customer" />
        </form>
    </div>
</div>