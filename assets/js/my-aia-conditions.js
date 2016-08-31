/* 
 * (C) Michiel Keijts 2016
 * Basic functionality for the my-aia-admin
 */

function condition_create(type) {
	var ref=jQuery('#conditions_tree').jstree(true);
	
	if (!ref.get_selected().length) 
		return false;
	
	var text = (type=="folder")?"AND":"Voorwaarde";
	
	var el = ref.create_node(ref.get_selected()[0], {"text":text,"type":type});
	if (el) {
		//ref.edit(el);
	}
}

function condition_delete(e) {
	var ref=jQuery('#conditions_tree').jstree(true);

	if (!ref.get_selected().length) 
		return false;
	
	ref.delete_node(ref.get_selected());
}

/**
 * Initialize the Condition Tree for the condition settings for the process
 * flow of the MY_AIA plugin Admin
 */
function condition_tree_init() {
	jQuery('#condition-delete').on('click', function(e) {e.preventDefault(); condition_delete(); });
	jQuery('#condition-add').on('click', function(e) {e.preventDefault(); condition_create("condition"); });
	jQuery('#condition-add-folder').on('click', function(e) {e.preventDefault(); condition_create("folder"); });
	
	jQuery('#conditions_tree').jstree({
		"core" : {  
			"check_callback" : true,
			"themes" : {
				"stripes" : true
			},
			"data" : {
				"url" : ajaxurl,
				"dataType" : "json", // needed only if you do not supply JSON headers,
				"method": 'post',
				'data' : {
					controller: 'processflow',
					action: 'my_aia_admin_static_condition_load',
					id: '56d41c11a41f3'
				}
			},
			
		},
		"plugins" : ['dnd', 'state', 'types'],
		"types" : {
			"#" : {
			  "max_children" : 1,
			  "max_depth" : 10,
			  "valid_children" : ["folder"]
			},
			"folder" : {
				"icon" : "glyphicon glyphicon-folder",
				"valid_children" : ["folder","condition"]
			},
			"condition" : {
			  "icon" : "glyphicon glyphicon-file",
			  "valid_children" : []
			}
		},
	});
	
	jQuery('#conditions_tree').on("select_node.jstree", function (e, data) { 
		if (data.node.type == 'folder') {
			jQuery('#condition_edit').addClass('hidden');
			jQuery('#condition_folder_edit').removeClass('hidden');
			condition_folder_edit(data.node);			
		}
		else {
			jQuery('#condition_folder_edit').addClass('hidden');
			jQuery('#condition_edit').removeClass('hidden');
			condition_edit(data.node);
		} 
	});
	
	jQuery('.my_aia_submit_button').click(function (e) {e.preventDefault(); condition_save();});
	//jQuery('#condition-save').on("click", function (e) {e.preventDefault(); condition_save();});
	//jQuery('#conditions_tree').on("create_node.jstree", function (node) { condition_edit(node);	});
}

/*
 * Function to set the panel values if created
 * @param \Node data
 * @returns {undefined}
 */
function condition_edit(node) {
	var ref=jQuery('#conditions_tree').jstree(true);
	if (typeof node ==="object" && !node.data) {
		// set default values
		node.data = {field1:'', field2:'', function1:'constant',function2:'constant',operator:'=='};
	}
	
	// if id exists, update
	if (jQuery('#conditions').data('current_id')) {
		// if not deleted update the node
		if (ref._model.data[jQuery('#conditions').data('current_id')]) {
			ref._model.data[jQuery('#conditions').data('current_id')].data = {
				field1:		jQuery('#conditions input[name=field1]').val(),
				field2:		jQuery('#conditions input[name=field2]').val(),
				operator:	jQuery('#conditions select[name=operator]').val(),
				function1:	jQuery('#conditions select[name=function1]').val(),
				function2:	jQuery('#conditions select[name=function2]').val()
			};
		}
	}
	
	// only update if node is object. We use this function for saving as well.
	if (typeof node ==="object") {
		jQuery('#conditions').data('current_id', node.id);

		jQuery('#conditions input[name=field1]').val(node.data.field1);
		jQuery('#conditions input[name=field2]').val(node.data.field2);
		jQuery('#conditions select[name=operator]').val(node.data.operator);
		jQuery('#conditions select[name=function1]').val(node.data.function1);
		jQuery('#conditions select[name=function2]').val(node.data.function2);
	}
}

/**
 * Edit a node as folder (group of conditions
 * @param {type} node
 * @returns {undefined}
 */
function condition_folder_edit(node) {
	var ref=jQuery('#conditions_tree').jstree(true);

	// if id exists, update
	if (jQuery('#conditions').data('current_folder_id')) {
		// if not deleted update the node
		if (ref._model.data[jQuery('#conditions').data('current_folder_id')]) {
			ref.rename_node(ref.get_node(jQuery('#conditions').data('current_folder_id')), jQuery('#conditions select[name=folder]').val())
		}
	}
	
	if (typeof node ==="object") {
		jQuery('#conditions').data('current_folder_id', node.id);
		jQuery('#conditions select[name=folder]').val(ref._model.data[jQuery('#conditions').data('current_folder_id')].text);
	}
}

/**
 * ajax_save the options tree
 * @param {type} id
 * @returns {undefined}
 */
function condition_save() { 
	// save open folder/condition
	condition_edit(false);	// no node as argument
	condition_folder_edit(false); // no node as argument
	
	var hook_name = jQuery('select[name=hook_name]').val();
	if (!hook_name.length) {
		alert('please select a hook');
		return false
	}
	
	
	var data={};
	var _data = {'static_condition':'', 'conditional_actions':'','actions':''};
	var ref=jQuery('#conditions_tree').jstree(true)._model.data;
	var key="";
	
	// build data static condition
	for (var i=0; i<Object.keys(ref).length; i++) {
		key=Object.keys(ref)[i];
		
		if (ref[key].type == 'folder' || ref[key].type == '#') {
			data[key] = {
				type :		ref[key].type,
				text :		ref[key].text,	
				children :  ref[key].children
			};
		} else {
			data[key] = {
				type :		ref[key].type,
				text :		ref[key].text,				
				field1 :	ref[key].data.field1,
				field2 :	ref[key].data.field2,
				function1 :	ref[key].data.function1,
				function2 :	ref[key].data.function2,
				operator :	ref[key].data.operator,
				children :  ref[key].children
			};
		}
	}
	_data.static_condition = data;
	
	// the actual query
	jQuery.post(
		ajaxurl, 
		{
			'action': 'my_aia_admin_hook_save',
			'controller': 'processflow',
			'id': jQuery('input[name=id]').val(),
			'hook_name': hook_name,
			'description': jQuery('input[name=description]').val(),
			'data':   _data
		}, 
		function(response){
			alert('The server responded: ' + response);
		}
	);
}

/**
 * Initiate the tabs for clicking and showing.
 * @returns void
 */
function my_aia_initiate_tabs() {
	jQuery('.my-aia-tabs li > a').click(function(e) {
		e.preventDefault();
		jQuery('.my-aia-tabs li.active').removeClass('active');
		jQuery('.my-aia-tabs-content > div.active').removeClass('active').hide();;
		jQuery(this).parent().addClass('active');
		jQuery(jQuery(this).attr('href')).addClass('active').show();
	});
}

/**
 * Initiate (render) the sortable elements, according to JQuery UI
 * @returns {undefined}
 */
function initiate_sortable() {
	jQuery('.sortable').sortable();
	
	// the actual query
	jQuery('.my_aia_submit_button').click(function(e) {
		e.preventDefault();
		
		var hook_name = jQuery('.my-aia-tabs .active span').data('hook_name');
		var data = new Array();

		jQuery('.my-aia-tabs-content .active ol > li').each(function(){ 
			data[data.length] = jQuery(this).data('id');
		});

		jQuery.post(
			ajaxurl, 
			{
				'action': 'my_aia_admin_processflow_order_save',
				'controller': 'processflow',
				'hook_name': hook_name,
				'data':   data
			}, 
			function(response){
				alert('The server responded: ' + response);
			}
		);
	
	});
}	

/**
 * Main Init Function for Admin
 */
jQuery(document).ready( function () {
	my_aia_initiate_tabs();
	
	condition_tree_init();
	
	initiate_sortable();
});