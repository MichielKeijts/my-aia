/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
	/* Initialize autocomplete */
	$('#my-aia-order-items-add' ).autocomplete({
		source:    ajaxurl + '?action=my_aia_admin_get_products&controller=order',
		delay:     500,
		minLength: 2,
		position:  ( 'undefined' !== typeof isRtl && isRtl ) ? { my: 'right top', at: 'right bottom', offset: '0, -1' } : { offset: '0, -1' },
		open:      function() { $(this).addClass('open'); },
		close:     function() { $(this).removeClass('open'); $(this).val(''); },
		select:    function( event, ui ) { 
			//ui.item.id /value/..
			$('#my_aia_order_item_add_id').text(ui.item.id);
			$('#my_aia_order_item_add_name').text(ui.item.value);
			$('#my_aia_order_item_add_price').val(ui.item.price);
			$('#my_aia_order_item_add_count').val(1);
		}
	});
	
	
	$('#button_order_item_add' ).click(function(e){
		e.preventDefault();
		if ($('#my_aia_order_item_add_id').text() == '') {
			alert('Selecteer eerst een artikel');
			return false;
		}
		
		var id = $('#my_aia_order_item_add_id').text();
		
		// first check if id already exist, update count instead of 
		var scope=jQuery("input[name='order_items[" + id + "][id]'");
		if (scope.length) {
			jQuery("input[name='order_items[" + id + "][count]'").val(
					jQuery("input[name='order_items[" + id + "][count]'").val() * 1
					+
					$('#my_aia_order_item_add_count').val() * 1	// add count to existing entry					
			);
	
			// if update instead of insert..
			return false;
		};
		
		var item = $('#my-aia-order-items tr.hidden').clone(false);
		
		// set id and input name
		$(item.find('th label')[0]).text(id);
		$(item.find('th input.order-item-id')).attr('name', 'order_items[' + id + '][id]');
		
		// set name (description)
		$(item.find('td')[0]).text( $('#my_aia_order_item_add_name').text()	);
		
		// set count
		$(item.find('td input.order-item-count')[0] ).val( $('#my_aia_order_item_add_count').val()	);
		$(item.find('td input.order-item-count')[0] ).attr('name', 'order_items[' + id + '][count]');
		
		// set price
		$(item.find('td input.order-item-price')[0] ).val( $('#my_aia_order_item_add_price').val()	);
		$(item.find('td input.order-item-price')[0] ).attr('name', 'order_items[' + id + '][price]');				
		
		// set subtotal
		$(item.find('td')[3]).text( Math.round($('#my_aia_order_item_add_price').val() * $('#my_aia_order_item_add_count').val(), 2));
		
		item.appendTo($('#my-aia-order-items tbody'));
		item.removeClass('hidden');
		MY_AIA_ORDER_APPLICATION.update_order_listeners();
	});
	
	
	var MY_AIA_ORDER_APPLICATION = new MY_AIA_ORDER_FORM("#my-aia-order-items tbody");
	MY_AIA_ORDER_APPLICATION.update_order_listeners();
});

var MY_AIA_ORDER_FORM = function(scope) {
	this.scope = scope;
	this.$scope = jQuery(scope);
	this.$ = jQuery;
	
	// add listener to publish
	//jQuery('#publish').on('click', this.submit);
}

/**
 * Update the total column
 * @param {type} scope
 * @returns {undefined}
 */
MY_AIA_ORDER_FORM.prototype.update_totals = function() {
	var scope=jQuery("#my-aia-order-items tbody");
	var totalPrice=0.00, totalVat=0.00, price, count;
	
	jQuery(scope).find('.usubtotal-column').each(function() {
		count = jQuery(this).parent().find('input.order-item-count').val();
		price = jQuery(this).parent().find('input.order-item-price').val();
		totalPrice = totalPrice + price * count;
		totalVat = totalVat * 0.21 ;//@TODO! Dynamic VAT;
		
		//set value
		jQuery(this).text(Math.round(price * count, 2));
	});
	
	jQuery("#my-aia-order-items tfoot .usubtotal-column span").text(Math.round(totalPrice, 2));
}

MY_AIA_ORDER_FORM.prototype.update_order_listeners = function () {
	jQuery('input.order-item-count').on('change', this.update_totals); 
	jQuery('input.order-item-price').on('change', this.update_totals);
	jQuery('.umodify-column .dashicons-dismiss').on('click', this.on_remove_row);
}

MY_AIA_ORDER_FORM.prototype.on_remove_row = function() {
	jQuery(this).parent().parent().remove();
}


/*MY_AIA_ORDER_FORM.prototype.submit = function () {
	var order_items = new Array();
	this.$scope.find('tr').each(function() {
		//order_items[order_items.length] = new Array();
	}
}*/
