var getUrl = './inc/ajax.get.php?cb=?';

function isNumber(n) {return !isNaN(Number(n));}
function createClasses(){$('tr:even').addClass('odd');} 

function showStatus(data){
	var html = '<p class="'+ (data.err === 0 ? "ok" : "err") +'">'+ data.msg +'</p>',
	o = $("#status");
	o.html(html).center().fadeIn();
	setTimeout(function() {o.fadeOut(100);}, 4000);
}


function renameArr(a){
	var d = {};	
	for (i in a) {
		d[a[i].name] = a[i].value;
	}
	return d;
}


function request(form){
	var data = renameArr(form.serializeArray());
		if(!validate( form )){
			return false;
		}
		$.getJSON(getUrl, data, function(json) {  
		 	if(json.err === 0){
				if(json.html !== undefined){
					$(json.selector).html(json.html);
				}
				if(json.append !== undefined){
					$("table tr").removeClass("mark");
					$(json.selector).append(json.append);
				}
				if(json.pagi !== undefined){
						$('#pagi').html(json.pagi);
					}
				createClasses();
				if(json.update === undefined || json.update !== 1){
					$(form).find('input[type=text], textarea').val('');
				}
			}
			if(json.msg.length > 0)	showStatus(json); 
		}); 
		return false;
}


function validate(f){
	var inputs = f.find('input.required, textarea.required'),
	valid = true,

	vldt = {
		required : function(v,i) {return {r : !!v ,  msg : 'Nie sú výplnené povinné hodnoty'}},
		email	 : function(v,i) {return {r : v.match( /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/ ), msg : 'Neplatná e-mailová adresa'}},
		fiveplus : function(v,i) {return {r : v.length >= 5, msg : 'Hodnota musí mať min. 5 znakov'}},
		numeric  : function(v,i) {return {r : !isNaN(v), msg : 'Hodnota '+v+' nie je číslo.'}},
		unique   : function(v,i) {var d = {coll : i.attr("name"),id : $('input[name=id]').eq(0).val(),table : i.parents("form").eq(0).attr("name"),val:v,act : 21};
			return {r : $.getCount(d), msg : 'Hodnota <strong>'+v+'</strong> sa už v databáze nachádza.'}
		}
	};
	inputs.removeClass('formerr');
	inputs.each(function(){
		var input = $(this),
			val = input.val(),
			cls = input.attr("class").split(' ');

		for(i in cls){
			if(vldt.hasOwnProperty(cls[i])){
				var res = vldt[cls[i]](val,input);
				if(!res.r){
					input.addClass('formerr');
					showStatus({err : 1, msg : res.msg});
					valid = false;
				}
			}
		}
	});
	return valid;	
}

function getProductPrice(id){
    $('input[name=price_sale]').val('');
    $('input[name=profit]').val('');
    $.getJSON(getUrl, {act: 25, id: id}, function(json) {
       if(json.err === 0){
           $('#recipePrice').text(json.price);
       }else{
           showStatus(json);
       }
       
    });  
}

function initDate(){
     $('.date').datepicker({
            dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Št', 'Pi', 'So'], 
            monthNames: ['Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'], 
            autoSize: false,
            dateFormat: 'yy-mm-dd',
            firstDay: 1});
}

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}

$(function() {
	$('.ajaxSubmit').submit(function (){request($(this));return false;})
	createClasses();
        // Datepicker ------------------------------------------------------
        $('#note .inactive').click(function(){
            $(this).val('').removeClass('inactive');
        });
        
       initDate();
        
        
        // Vypocita percentualny zisk v polozke objednavky
        $('input[name=profit]').change(function(){
            var priceNakup = parseFloat($('#recipePrice').text()),
                priceInput = $('input[name=price_sale]'),
                percProfit = parseFloat($(this).val());
            if(priceNakup === '-') return;
            if(percProfit === 0){
                priceInput.val(priceNakup);
            }else{
                var price = (priceNakup / 100) * (percProfit + 100);
                priceInput.val(Math.round(price*10000)/10000);
            }
        });
        
        // Vypocita percentualny zisk v polozke objednavky
        $('input[name=price_sale]').change(function(){
            var priceNakup = parseFloat($('#recipePrice').text()),
                profitInput = $('input[name=profit]'),
                priceSale = parseFloat($(this).val());
            if(priceNakup === '-') return;
            if(priceNakup === priceSale){
                profitInput.val(0);
            }else{
                var price = (priceSale - priceNakup) / priceNakup *100;
                profitInput.val(Math.round(price*100)/100);
            }
        });
        
        
        
        // AUTOCOMPLETE product --------------------------------------------------
	$( "#p" ).autocomplete({
            source: function(reques, response){
                reques.act = 12;
                $.getJSON(getUrl, reques, function(data) {  
                    response( $.map( data, function( item ) {
                        return {label: item.code +' - '+ item.label , value :  item.label,  v: item};
                    }));
                });  
             },
            select: function( e, ui ) {
                   getProductPrice(ui.item.v.id);
                   $('input[name=id_product]').val(ui.item.v.id);
                   $('#p').addClass('ok2');
            },
            change : function(e, ui){
                   $('#p').removeClass('ok2');
            }
	});
        
        $('#dt').click(function(){
            $(this).parent().html('<input maxlength="10" type="text" class="w100 date required" name="date"/>&nbsp;<input type="submit" class="ibtn-sm" value="Ulož" /> ');
             initDate();
        })
        
        // AUTOCOMPLETE customer --------------------------------------------------
	$( "input[name=q]" ).autocomplete({
            source: function(reques, response){
                reques.act = $('#act').text();
                $.getJSON(getUrl, reques, function(data) {  
                    response( $.map( data, function( item ) {
                        return {label: item.name, value :item.name};
                    }));
                });  
             },
            select: function( e, ui ) {
                ui.item.label;
            }
	});
        
        // AUTOCOMPLETE customer --------------------------------------------------
	$( "input[name=customer]" ).autocomplete({
            source: function(reques, response){
                reques.act = 10;
                $.getJSON(getUrl, reques, function(data) {  
                    response( $.map( data, function( item ) {
                        return {label: item.name, value :item.name,  v: item};
                    }));
                });  
             },
            select: function( e, ui ) {
                   $('input[name=id_customer]').val(ui.item.v.id);
                   $('#cust-descr').html('<p><span>'+ui.item.v.name+'</span></p>' + 
                                        '<p><b>Adresa:</b>'+ui.item.v.street+' '+ ui.item.v.zip + ' ' +ui.item.v.city + '</p>'+
                                        '<p><b>IČO:</b>'+ui.item.v.ico+' &nbsp; <b> DIČ:</b>'+ui.item.v.dic+'</p>');
            }
	});
        
         // MAZANIE POLOZKY objednávky ---------------------------------------------------
	$("table").delegate(".del3", 'click', function (e) {
            var o = $(this),
            data = {
                id : o.attr("href").replace("#id",""),
                act : 144,
                orderId : $('input[name=orderId]').val()
            };
            if(!confirm("Skutočne chcete zmazať položku?")){
                    return false;
            }
            $('.inline').addClass('exe');
            $.getJSON(getUrl, data, function(json) {  
                if(json.err == 0){
                     if(json.totalPrice !== undefined){ 
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                         $('.inline').removeClass('exe');
                    }
                }
                createClasses();
            });
        return false;
	})
        
         // pridanie novej objednavky --------------------------------------------
        $('#new-order').submit( function(e) {
            var data = renameArr($(this).serializeArray());
                $.getJSON(getUrl, data, function(json) {  
                    if(json.err === 0)
                        location.href = 'index.php?p=order&sp=edit&id=' + json.id;
                    else
                        showStatus(json);
                });
               return false;
            });
            
        $('#pf').submit(function(e){
             var data = renameArr( $(this).serializeArray() );
             data.quantity_kg = data.quantity_kg.replace(',', ".");
             if(data.id_product === "0"){
                 showStatus({'err' : 1 , 'msg' : 'Nie je vybraný tovar.'});
                 return false;
             }else if(!isNumber(data.quantity_kg)){
                 showStatus({'err' : 1 , 'msg' : 'Dávka na kg nie je číslo.'});
                 return false;
             }
             
             $.getJSON(getUrl, data, function(json) {  
                    if(json.err === 0){
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                        $('input[type=text]').val('');
                        createClasses();
                    }else{
                        showStatus(json);
                    }       
            });
             
            return false;
        });   
        // pridanie novej receptury --------------------------------------------
        $('#new-recipe').submit( function() {
            var data = renameArr($(this).serializeArray());
            if(!validate( $(this) ))return false;
                $.getJSON(getUrl, data, function(json) {  
                    if(json.err === 0){
                        if(json.id !== undefined)
                            location.href = 'index.php?p=recipe&sp=edit&id=' + json.id;
                        else{
                            $('input').val('');
                            showStatus(json);
                        }
                    }else{
                        showStatus(json);
                    }
                });
               return false;
            });
           

        // upravenie poctu kg v recepture ------------------------------------
        $('#ercp').submit(function(){
             var data = renameArr( $(this).serializeArray() );
             data.quantity = data.quantity.replace(',', ".");
              if(!isNumber(data.quantity)){
                 showStatus({'err' :1 ,'msg' : 'Zadaná hodnota nie je číslo.'});
                 return false;
             }
             $('.inline').addClass('exe');
             $.getJSON(getUrl, data, function(json) {  
                    if(json.err === 0){
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                        $('#total').html(json.total);
                        createClasses();
                        $('.inline').removeClass('exe');
                    }else{
                        showStatus(json);
                    }       
            });
            return false;
        });
        
        
        
        
        /* pridanie novej polozky do receptury */
        $('#recipe-item').submit(function(){
             var data = renameArr( $(this).serializeArray() );
             if(!validate( $(this) ))return false;
             data.quantity_kg = data.quantity_kg.replace(',', ".");
             if(data.id_color === "0"){
                 showStatus({'err' : 1 , 'msg' : 'Nie je vybraný materiál.'});
                 return false;
             }else if(!isNumber(data.quantity_kg)){
                 showStatus({'err' : 1 , 'msg' : 'Dávka na kg nie je číslo.'});
                 return false;
             }
             $.getJSON(getUrl, data, function(json) {  
                    if(json.err === 0){
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                        createClasses();
                    }else{
                        showStatus(json);
                    }       
            });
             
            return false;
        });
        
        /* pridanie novej polozky do receptury v Objednavke */
        $('#recipe-item-order').submit(function(){
             var data = renameArr( $(this).serializeArray() );
             if(!validate( $(this) ))return false;
             data.quantity_kg = data.quantity_kg.replace(',', ".");
             data.price = data.price.replace(',', ".");
             if(data.id_color === "0"){
                 showStatus({'err' : 1 , 'msg' : 'Nie je vybraný materiál.'});
                 return false;
             }else if(!isNumber(data.quantity_kg) || ! isNumber(data.price)){
                 showStatus({'err' : 1 , 'msg' : 'Zadané neplatné hodnoty.'});
                 return false;
             }
             $.getJSON(getUrl, data, function(json) {  
                    if(json.err === 0){
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                        $('#total').html(json.total);
                        createClasses();
                    }else{
                        showStatus(json);
                    }       
            });
             
            return false;
        });
        
       // order reciptie change ---------------------------------------------
       $('select[name=id_color]').change(function(){
           var id = $('select[name=id_color] option:selected').val(),
               price = $('input[name=price]'),
               unit = $('#unit'),
               label = $('#label');
           
           if(id === 0){
               if(price !== undefined)
                    price.val('');
               unit.text('');
           }else{
                $.getJSON(getUrl, {act : 24, id : id }, function(json) {  
                    if(json.err === 0){
                        if(price !== undefined)
                            price.val(json.price);
                        unit.text(json.unit);
                        if(json.riedidlo == 1){
                            label.text('Dávka celkovo:');
                        }else{
                            label.text('Dávka na 1kg:');    
                        }
                    }else{
                        showStatus(json);
                    }
                });
           } 
        });
  
        
        // MAZANIE -----------------------------------------------------------
	$("table").delegate(".del", 'click', function () {
            var o = $(this),
            data = {
                id : o.attr("href").replace("#id",""),
                act : 3,
                table : o.parents('tbody').eq(0).attr("class")
            };
            if(!confirm("Skutočne chcete zmazať položku?")){
                    return false;
            }
            $.getJSON(getUrl, data, function(json) {  
                if(json.err === 0)
                    o.parent().parent().hide(1000);
                else
                    showStatus(json);
                createClasses();
            });
        return false;
	})
         // MAZANIE POLOZKY RECEPTURY Z OBJENAVKY ---------------------------------------------------
	$("table").delegate(".del4", 'click', function () {
            var o = $(this),
            data = {
                id : o.attr("href").replace("#id",""),
                act : 17,
                recepeId : $('input[name=id]').val(),
                table : o.parents('tbody').eq(0).attr("class")
            };
            if(!confirm("Skutočne chcete zmazať položku?")){
                    return false;
            }
            $('.inline').addClass('exe');
            $.getJSON(getUrl, data, function(json) {  
                if(json.err == 0){
                     if(json.totalPrice !== undefined){ 
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                        $('#total').html(json.total);
                         $('.inline').removeClass('exe');
                    }
                }
                createClasses();
            });
        return false;
	})
        
         // MAZANIE POLOZKY RECEPTURY ---------------------------------------------------
	$("table").delegate(".del2", 'click', function () {
            var o = $(this),
            data = {
                id : o.attr("href").replace("#id",""),
                act : 9,
                recepeId : $('input[name=recepeId]').val(),
                table : o.parents('tbody').eq(0).attr("class")
            };
            if(!confirm("Skutočne chcete zmazať položku?")){
                    return false;
            }
            $('.inline').addClass('exe');
            $.getJSON(getUrl, data, function(json) {  
                if(json.err == 0){
                     if(json.totalPrice !== undefined){ 
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                         $('.inline').removeClass('exe');
                    }
                }
                createClasses();
            });
        return false;
	})
        
	
	// INLINE EDITING -----------------------------------------------------------
	$(".inline").delegate(".inline .edit", 'click', function () {
                var o = $(this),
                id = o.attr("href").replace("#id",""),
                tr = o.parent().parent().addClass("editing").find('.il'), 
                names = o.parents('.inline').find('th.il');
                $('body').data("id", id);
                
                names.each(function(i){
                    var cls = $(this).attr("class").split(" "),
                            input = cls[1].split("-");
                            obj = $(this); // current thead th item
                    if(input.length === 2 && input[0] === "text"){
                            tr.eq(i).html('<input style="width:'+ (obj.width() - 10) +'px" type="text" name="' + 
                            input[1] + '" value="'+ tr.eq(i).text().replace(/(kg|ks|ml|L|€|g|\/)/ig ,"").trim() +'" class="ii '+(obj.hasClass("required") ? 'required' : '')+ '" />');
                    }else{
                            tr.eq(i).html('<textarea style="width:'+ (obj.width() - 10) +'px;height:70px;" name="' +input[1] + '" class="ii '+
                            (obj.hasClass("required") ? 'required' : '')+ '" >'+ tr.eq(i).text() +'</textarea>');
                    }
                });
                o.parent().append('<input type="submit" id="#iibtn" value="Uložiť" class="ibtn" />');
                $('.inline .edit').hide();
        return false;
	})
	
	$(".inlineEditing").submit( function () {
            var o = $(this),
            tr = $("tr.editing").eq(0);
            if(!validate( o )){
                    return false;
            }
            var data = renameArr(o.serializeArray());
            data.id = $('body').data('id');	
            $('.inline').addClass('exe');
            
            $.getJSON(getUrl, data, function(json) {  
                if(json.err === 1){ 
                    showStatus(json);
                    return false;
                }else{
                    if(json.totalPrice !== undefined){ 
                        $('.tableitems').html(json.data);
                        $('.totalPrice').html(json.totalPrice);
                        if(json.total !== undefined)
                            $('#total').html(json.total);
                        createClasses();
                    }else{
                        tr.find('.ii').each(function(){
                        var input = $(this),
                            val = input.val();
                            input.parent('td').text(val);
                            input.remove();				
                        });
                        tr.removeClass("editing");
                        $(".inline .ibtn").remove();
                        $('.inline .edit').show();
                    }
                    $('.inline').removeClass('exe');
                }
            });	
    return false;
	})
	
});
