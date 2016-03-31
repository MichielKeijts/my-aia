/* 
 * (C) Michiel Keijts 2016
 * Basic functionality for the my-aia-admin SYNC part
 */


/**
 * Initialize the Sync drag and dropable
 */
function sync_init() {
	jQuery( ".draggable" ).draggable({ revert: "invalid", helper: "clone" });
    jQuery( ".droppable.internal" ).droppable({
		accept: ".field.internal",
		hoverClass: "ui-state-hover",
		drop: function( event, ui ) {
			jQuery( this ).find( ".placeholder" ).remove();
			jQuery( this ).empty();
			jQuery( this ).text( ui.draggable.text() ).appendTo( this );
			jQuery( this ).addClass( "field" );	
		}
    });
	
	jQuery( ".droppable.external" ).droppable({
		accept: ".field.external",
		hoverClass: "ui-state-hover",
		drop: function( event, ui ) {
			jQuery( this ).find( ".placeholder" ).remove();
			jQuery( this ).empty();
			jQuery( this ).text( ui.draggable.text() ).appendTo( this );
			jQuery( this ).addClass( "field" );	
		}
    });
	
	jQuery('.remove').click(function(){remove_sync_rule(this);});
}


/**
 * add the sync rule by cloning
 * @param {type} el
 * @returns {undefined}
 */
function add_sync_rule() {
	jQuery('.sync_field:last')
			.clone()
			.appendTo(jQuery('#sync_fields'))
			.find('.droppable')
				.removeClass('field')
				.empty();	
		
	// enable sync again
	sync_init();
}

/**
 * remove the sync rule
 * @param {type} el
 * @returns {undefined}
 */
function remove_sync_rule(el) {
	if (jQuery('#sync_fields li').length > 1) {
		jQuery(el).parent().remove();
	} else {
		jQuery(el).parent().find('.droppable')
				.removeClass('field')
				.empty();
	}
}

/**
 * Get all the information (text) from the sync object and submit using AJAX
 * @returns boolean
 */
function submit_sync_form() {
	var data = {};//'static_condition':'', 'conditional_actions':'','actions':''};
	var i=0;
	var ref=jQuery('#sync_fields li');
	
	// build data static condition
	ref.each(function(){
		data[i] = {
			internal_field : jQuery(this).find('div.droppable:first').text(),
			external_field : jQuery(this).find('div.droppable:last').text()
		};
		i++;
	});
	
	// the actual query
	jQuery.post(
		ajaxurl, 
		{
			'action': 'my_aia_admin_sync_save',
			'controller': 'sync',
			'type': jQuery('#sync_fields').data('type'),
			'data':  data
		}, 
		function(response){
			alert('The server responded: ' + response);
		}
	);
}


/**
 * Main Init Function for Condition Tree
 */
jQuery(document).ready( function () {
	sync_init();
	
	jQuery('#add_sync_rule').click(function(){add_sync_rule();});
	jQuery('.my_aia_submit_button').click(function(e){e.preventDefault(); submit_sync_form()});
});