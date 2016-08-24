/*
 * @Package my-aia
 * @Author Michiel Keijts (c)2016
 */


jQuery(document).ready(function($) {
	$( 'select[name=parent_type]' ).change(function(e){
		e.preventDefault();
		var content_element = $('#template_field_list');
		content_element.empty();
		$.get(
			ajaxurl,
			{
				controller: "template",
				action:		"my_aia_admin_get_template_fields",
				post_type:	$( this ).val()
			},
			function(response) {
				// parse the fields
				if (response.success) {
					$.each(response.data.fields, function(index, val){
						content_element.append('<li><a href="#" title="Toevoegen op plaats van cursor">%'+val.toUpperCase()+'%</a><li>');
					});
				}
				
				/** 
				 * Helper to insert content on click
				 */
				content_element.find('a').click(function(e) {
					e.preventDefault();
					tinymce.editors['content'].insertContent($(this).text());
				});
			},
			'json'
		)
	});
	
	// initialize, call change
	$( 'select[name=parent_type]' ).change();
});