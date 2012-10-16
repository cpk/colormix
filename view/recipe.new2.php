<div class="tbox">
    <strong>Pridanie nového tovaru</strong>
    <div class="tcontent">
        <form id="new-recipe">
            <div class="i">
                    <label>Kód:</label>
                    <input maxlength="10" type="text" class="w100 required" name="code"/>
            </div>
            <div class="i odd">
                    <label>Názov:</label>
                    <input maxlength="45" type="text" class="w280 required" name="label"/>
            </div> 
             <div class="i">
                    <label>Cena tovaru za ks:</label>
                    <input maxlength="10" type="text" class="w100 required" name="price"/>
                    <span><?php echo PRICE_UNIT; ?></span>
             </div>
             <div class="i odd">
                    <input type="submit" class="ibtn" value="Pridať" />
                    <div class="clear"></div>
             </div> 
                    <input type="hidden"  value="5" name="act" />
                    <input type="hidden"  value="0" name="recipe" /> 
        </form>
    </div>
</div>