var defaultFolderHtml;
var folderID = 0;
var fileAddUpdateStatus = "add";
var fileFolderID = 0;
var folderNameDynamic = '';
var totalFolders = -1;
var isKeyActive = 0;
var folderLimitation = 10;


var listFolderString = "<li class='grid-view' data-id='__folder_id__' id='folder___folder_id__'>" +
	"<div class='folder-item is-folder' data-id='__folder_id__'>" +
	"<a title='__folder_name__' id='folder_view___folder_id__'" +
	"class='folder-view __append_class__'" +
	"data-id='__folder_id__'>" +
	"<span class='folder item-name'><span id='wcp_folder_text___folder_id__'" +
	"class='folder-title'>__folder_name__</span></span>" +
	"</a>" +
	"</div>" +
	"</li>";

function addFolder() {
	if(isKeyActive == 0 && totalFolders >= folderLimitation) {
		Swal.fire({
			title: "You've reached the "+totalFolders+" folder limitation!",
			text: "Unlock unlimited amount of folders by upgrading to one of our pro plans.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'See Pro Plans'
		}).then((result) => {
			if (result.value) {
				window.location = wcp_settings.register_url;
			}
		});
		return false;
	}
	folderOrder = jQuery("#space_"+fileFolderID+" > li").length+1;
	ajaxURL = wcp_settings.ajax_url+"?parent_id=" + fileFolderID + "&type=" + wcp_settings.post_type + "&action=wcp_add_new_folder&nonce=" + wcp_settings.nonce + "&term_id=" + fileFolderID + "&order=" + folderOrder+"&name=";
	Swal({
		title: 'Add Folder',
		input: 'text',
		inputAttributes: {
			autocapitalize: 'off',
			placeholder: "Folder name"
		},
		showCancelButton: true,
		confirmButtonText: 'Submit',
		showLoaderOnConfirm: true,
		reverseButtons: true,
		preConfirm: (folderName) => {
		if(folderName == "") {
			swal.showValidationError(
				'Please enter folder name'
			)
			return false;
		}
		return fetch(ajaxURL+folderName)
		.then(response => {
				if (!response.ok) {
				throw new Error(response.statusText);
			}
			return response.json();
		}).catch(error => {
			Swal.showValidationMessage(
				"Request failed: "+error
				)
			});
		},
		allowOutsideClick: () => !Swal.isLoading()
	}).then((result) => {
		if(result.value.error == 1) {
			Swal({
				type: 'error',
				title: 'Oops...',
				text: result.value.message
			});
		} else if(result.value.status == 1) {
			jQuery("#space_"+result.value.parent_id).append(result.value.term_data);
			jQuery("#wcp_folder_"+result.value.parent_id).addClass("active has-sub-tree");
			isKeyActive = parseInt(result.value.is_key_active);
			totalFolders = parseInt(result.value.folders);
			jQuery("#current-folder").text(totalFolders);
			if(totalFolders > folderLimitation) {
				folderLimitation = totalFolders;
			}
			jQuery("#total-folder").text(folderLimitation);
			checkForExpandCollapse();
			add_menu_to_list();
		}
	});
}


function updateFolder() {
	folderName = jQuery.trim(jQuery("#wcp_folder_"+fileFolderID+" > h3").text());
	parentID = jQuery("#wcp_folder_"+fileFolderID).closest("li.route").data("folder-id");
	if(parentID == undefined) {
		parentID = 0;
	}
	nonce = jQuery.trim(jQuery("#wcp_folder_"+fileFolderID).data("rename"));
	ajaxURL = wcp_settings.ajax_url+"?parent_id=" + parentID + "&nonce=" + nonce + "&type=" + wcp_settings.post_type + "&action=wcp_update_folder&term_id=" + fileFolderID + "&name=";
	Swal({
		title: 'Update Folder',
		input: 'text',
		inputValue: folderName,
		inputAttributes: {
			autocapitalize: 'off',
			placeholder: "Folder name",
			value: folderName
		},
		showCancelButton: true,
		confirmButtonText: 'Submit',
		showLoaderOnConfirm: true,
		reverseButtons: true,
		preConfirm: (folderName) => {
		if(folderName == "") {
		swal.showValidationError(
			'Please enter folder name'
		)
		return false;
	}
	return fetch(ajaxURL+folderName)
			.then(response => {
			if (!response.ok) {
		throw new Error(response.statusText);
	}
	return response.json();
	}).catch(error => {
			Swal.showValidationMessage(
			"Request failed: "+error
			)
		});
	},
	allowOutsideClick: () => !Swal.isLoading()
	}).then((result) => {
		if(result.value.error == 1) {
		Swal({
			type: 'error',
			title: 'Oops...',
			text: result.value.message
		});
	} else if(result.value.status == 1) {
		jQuery("#wcp_folder_"+result.value.id+" > h3 > .title-text").text(result.value.term_title);
		jQuery("#wcp_folder_"+result.value.id+" > h3").attr("title",result.value.term_title);
		add_menu_to_list();
	}
	});
}

function add_menu_to_list() {
	folderId = 0;
	if(jQuery(".active-term").length) {
		folderId = jQuery(".active-term").data("folder-id");
	}
	jQuery(".tree-structure ul").html("");
	jQuery("#space_"+folderId).children().each(function(){
		fID = jQuery(this).data("folder-id");
		fName = jQuery(this).find("h3.title:first .title-text").text()
		liHtml = listFolderString.replace(/__folder_id__/g,fID);
		liHtml = liHtml.replace(/__folder_name__/g,fName);
		selectedClass = jQuery(this).hasClass("is-high")?"is-high":"";
		liHtml = liHtml.replace(/__append_class__/g,selectedClass);
		jQuery(".tree-structure ul").append(liHtml);
	});
}

jQuery(document).ready(function(){

	wcp_settings.folder_width = parseInt(wcp_settings.folder_width);

	if(wcp_settings.can_manage_folder == "0") {
		jQuery(".wcp-custom-form a:not(.pink)").addClass("button-disabled");
	}

	isKeyActive = parseInt(wcp_settings.is_key_active);
	totalFolders = parseInt(wcp_settings.folders);

	if(wcp_settings.post_type == "attachment") {
		jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');

		add_menu_to_list();
	}

	calcWidth(jQuery('#title_0'));

	jQuery("#cancel-button").click(function(){
		jQuery(".wcp-form-data").hide();
	});


	jQuery(document).on("click", "h3.title", function(e) {
		e.stopPropagation();
		window.location = wcp_settings.page_url+jQuery(this).closest("li.route").data("slug");
	});

	jQuery(".tree-structure a").livequery(function(){
		jQuery(this).click(function(){
			fID = jQuery(this).data("id");
			jQuery("#title_"+fID).trigger("click");
		});
	});

	jQuery(".wcp-parent > span").click(function(){
		window.location  = wcp_settings.page_url
	});
	jQuery("h3.title").livequery(function () {
		jQuery(this).droppable({
			accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
			hoverClass: 'wcp-drop-hover',
			classes: {
				"ui-droppable-active": "ui-state-highlight"
			},
			drop: function (event, ui) {
				folderID = jQuery(this).closest("li.route").data('folder-id');
				if (ui.draggable.hasClass('wcp-move-multiple')) {
					if (jQuery(".wp-list-table input:checked").length) {
						chkStr = "";
						jQuery(".wp-list-table input:checked").each(function () {
							chkStr += jQuery(this).val() + ",";
						});
						nonce = jQuery.trim(jQuery("#wcp_folder_" + folderID).data("nonce"));
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+wcp_settings.selected_taxonomy,
							method: 'post',
							success: function (res) {
								res = jQuery.parseJSON(res);
								if(res.status == "1") {
									window.location.reload();
								} else {
									Swal.fire(
										'',
										res.message,
										'error'
									);
								}
							}
						});
					}
				} else if( ui.draggable.hasClass( 'wcp-move-file' ) ){
					postID = ui.draggable[0].attributes['data-id'].nodeValue;
					nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_id=" + postID + "&type=" + wcp_settings.post_type + "&action=wcp_change_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+wcp_settings.selected_taxonomy,
						method: 'post',
						success: function (res) {
							res = jQuery.parseJSON(res);
							if(res.status == "1") {
								window.location.reload();
							} else {
								Swal.fire(
									'',
									res.message,
									'error'
								);
							}
						}
					});
				} else if (ui.draggable.hasClass('attachment')) {
					chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
					nonce = jQuery.trim(jQuery("#wcp_folder_" + folderID).data("nonce"));
					if (jQuery(".attachments-browser li.attachment.selected").length > 1) {
						chkStr = "";
						jQuery(".attachments-browser li.attachment.selected").each(function () {
							chkStr += jQuery(this).data("id") + ",";
						});
					}
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+wcp_settings.selected_taxonomy,
						method: 'post',
						success: function (res) {
							window.location.reload();
						}
					});
				}
			}
		});
	});

	jQuery(".attachments-browser li.attachment").livequery(function () {
		jQuery(this).draggable({
			revert: "invalid",
			containment: "document",
			helper: function (event, ui) {
				jQuery(".selected-items").remove();
				selectedItems = jQuery(".attachments-browser li.attachment.selected").length;
				selectedItems = (selectedItems == 0 || selectedItems == 1) ? "1 Item" : selectedItems + " Items";
				return jQuery("<div class='selected-items'><span class='total-post-count'>" + selectedItems + " Selected</span></div>");
			},
			start: function( event, ui){
				jQuery("body").addClass("no-hover-css");
			},
			cursor: "move",
			cursorAt: {
				left: 0,
				top: 0
			},
			stop: function( event, ui ) {
				jQuery(".selected-items").remove();
				jQuery("body").removeClass("no-hover-css");
			}
		});
		jQuery(this).draggable("disable");
	});

	jQuery(".media-button").livequery(function () {
		jQuery(this).click(function () {
			if (jQuery(".delete-selected-button").hasClass("hidden")) {
				jQuery(".attachments-browser li.attachment").draggable("disable");
			} else {
				jQuery(".attachments-browser li.attachment").draggable("enable");
			}
		});
	});

	jQuery(".header-posts").click(function(){
		window.location = wcp_settings.page_url;
	});

	jQuery(".un-categorised-items").click(function(){
		window.location = wcp_settings.page_url+"-1";
	});

	jQuery(".un-categorised-items").livequery(function () {
		jQuery(this).droppable({
			accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
			hoverClass: 'wcp-hover-list',
			classes: {
				"ui-droppable-active": "ui-state-highlight"
			},
			drop: function (event, ui) {
				folderID = -1;
				nonce = wcp_settings.nonce;
				if (ui.draggable.hasClass('wcp-move-multiple')) {
					if (jQuery(".wp-list-table input:checked").length) {
						chkStr = "";
						jQuery(".wp-list-table input:checked").each(function () {
							chkStr += jQuery(this).val() + ",";
						});
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+wcp_settings.selected_taxonomy,
							method: 'post',
							success: function (res) {
								window.location.reload();
							}
						});
					}
				} else if (ui.draggable.hasClass('wcp-move-file')) {
					postID = ui.draggable[0].attributes['data-id'].nodeValue;
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_id=" + postID + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+wcp_settings.selected_taxonomy,
						method: 'post',
						success: function (res) {
							window.location.reload();
						}
					});
				} else if (ui.draggable.hasClass('attachment')) {
					chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
					if (jQuery(".attachments-browser li.attachment.selected").length > 1) {
						chkStr = "";
						jQuery(".attachments-browser li.attachment.selected").each(function () {
							chkStr += jQuery(this).data("id") + ",";
						});
					}
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+wcp_settings.selected_taxonomy,
						method: 'post',
						success: function (res) {
							window.location.reload();
						}
					});
				}
			}
		});
	});


	jQuery(".wcp-hide-show-buttons .toggle-buttons").click(function(){
		var folderStatus = "show";
		if(jQuery(this).hasClass("hide-folders")) {
			folderStatus = "hide";
		}
		jQuery(".wcp-hide-show-buttons .toggle-buttons").toggleClass("active");
		nonce = wcp_settings.nonce;
		if(folderStatus == "show") {
			jQuery("#wcp-content").addClass("no-transition");
			jQuery("#wcp-content").removeClass("hide-folders-area");
			if(wcp_settings.isRTL == "1") {
				jQuery("#wpcontent").css("padding-right", (wcp_settings.folder_width + 20) + "px");
				jQuery("#wpcontent").css("padding-left", "0px");
			} else {
				jQuery("#wpcontent").css("padding-left", (wcp_settings.folder_width + 20) + "px");
			}
			setTimeout(function(){
				jQuery("#wcp-content").removeClass("no-transition");
			}, 250);
		} else {
			jQuery("#wcp-content").addClass("hide-folders-area");
			if(wcp_settings.isRTL == "1") {
				jQuery("#wpcontent").css("padding-right", "20px");
				jQuery("#wpcontent").css("padding-left", "0px");
			} else {
				jQuery("#wpcontent").css("padding-left", "20px");
			}
		}

		jQuery.ajax({
			url: wcp_settings.ajax_url,
			data: "type=" + wcp_settings.post_type + "&action=wcp_change_folder_display_status&status=" + folderStatus +"&nonce="+nonce,
			method: 'post',
			success: function (res) {
				setStickyHeaderForMedia();
			}
		});
	});

	//if(wcp_settings.can_manage_folder == "1") {
		jQuery(".tree-structure .folder-item").livequery(function () {
			jQuery(this).droppable({
				accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
				hoverClass: 'wcp-drop-hover-list',
				classes: {
					"ui-droppable-active": "ui-state-highlight"
				},
				drop: function (event, ui) {
					folderID = jQuery(this).data('id');
					if (ui.draggable.hasClass('wcp-move-multiple')) {
						nonce = jQuery.trim(jQuery("#wcp_folder_" + folderID).data("nonce"));
						if (jQuery(".wp-list-table input:checked").length) {
							chkStr = "";
							jQuery(".wp-list-table input:checked").each(function () {
								chkStr += jQuery(this).val() + ",";
							});
							jQuery.ajax({
								url: wcp_settings.ajax_url,
								data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce=" + nonce,
								method: 'post',
								success: function (res) {
									window.location.reload();
								}
							});
						}
					} else if (ui.draggable.hasClass('wcp-move-file')) {
						postID = ui.draggable[0].attributes['data-id'].nodeValue;
						nonce = jQuery.trim(jQuery("#wcp_folder_" + folderID).data("nonce"));
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "post_id=" + postID + "&type=" + wcp_settings.post_type + "&action=wcp_change_post_folder&folder_id=" + folderID + "&nonce=" + nonce,
							method: 'post',
							success: function (res) {
								window.location.reload();
							}
						});
					} else if (ui.draggable.hasClass('attachment')) {
						chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
						nonce = jQuery.trim(jQuery("#wcp_folder_" + folderID).data("nonce"));
						if (jQuery(".attachments-browser li.attachment.selected").length > 1) {
							chkStr = "";
							jQuery(".attachments-browser li.attachment.selected").each(function () {
								chkStr += jQuery(this).data("id") + ",";
							});
						}
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce=" + nonce,
							method: 'post',
							success: function (res) {
								window.location.reload();
							}
						});
					}
				}
			});
		});
	//}

	jQuery("#expand-collapse-list").click(function(e){
		e.stopPropagation();
		statusType = 0;
		if(jQuery(this).hasClass("all-open")) {
			jQuery(this).removeClass("all-open");
			jQuery(".has-sub-tree").removeClass("active");
			statusType = 0;
		} else {
			jQuery(this).addClass("all-open");
			statusType = 1;
			jQuery(".has-sub-tree").addClass("active");
		}
		folderIDs = "";
		jQuery(".has-sub-tree").each(function(){
			folderIDs += jQuery(this).data("folder-id")+",";
		});
		if(folderIDs != "") {
			jQuery(".form-loader-count").css("width","100%");
			nonce = wcp_settings.nonce;
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_change_all_status&status=" + statusType + "&folders="+folderIDs+"&nonce="+nonce,
				method: 'post',
				success: function (res) {
					jQuery(".form-loader-count").css("width","0");
					add_menu_to_list();
					res = jQuery.parseJSON(res);
					if(res.status == "0") {
						Swal.fire(
							'',
							res.message,
							'error'
						);
						window.location.reload(true);
					}
				}
			});
		}
	});

	resizeDirection = (wcp_settings.isRTL == "1" || wcp_settings.isRTL == 1)?"w":"e";
	jQuery(".wcp-content").resizable( {
		resizeHeight:   false,
		handles:        resizeDirection,
		minWidth:       305,
		maxWidth: 		500,
		resize: function( e, ui ) {
			if(wcp_settings.isRTL == "1") {
				jQuery("#wpcontent").css("padding-right", (ui.size.width + 20) + "px");
				jQuery("#wpcontent").css("padding-left", "0px");
			} else {
				jQuery("#wpcontent").css("padding-left", (ui.size.width + 20) + "px");
			}
			newWidth = ui.size.width - 40;
			cssString = "";
			classString = "";
			for(i=0; i<=15; i++) {
				classString += " .space > .route >";
				currentWidth = newWidth - (13+(20*i));
				cssString += "#custom-menu > "+classString+" .title { width: "+currentWidth+"px !important; } ";
				setStickyHeaderForMedia();
			}
			jQuery("#wcp-custom-style").html("<style>"+cssString+"</style>");
		},
		stop: function( e, ui ) {
			nonce = wcp_settings.nonce;
			wcp_settings.folder_width = ui.size.width;
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_change_post_width&width=" + ui.size.width+"&nonce="+nonce,
				method: 'post',
				success: function (res) {
					setStickyHeaderForMedia();
				}
			});
		}
	});

	jQuery(".wcp-move-file").livequery(function(){
		jQuery(this).draggable({
			/*cancel: "a.ui-icon",*/
			revert: "invalid",
			containment: "document",
			helper: "clone",
			cursor: "move",
			start: function( event, ui){
				jQuery(this).closest("td").addClass("wcp-draggable");
				jQuery("body").addClass("no-hover-css");
			},
			stop: function( event, ui ) {
				jQuery(this).closest("td").removeClass("wcp-draggable");
				jQuery("body").removeClass("no-hover-css");
			}
		});
	});

	jQuery(".wcp-move-multiple").draggable({
		/*cancel: "a.ui-icon",*/
		revert: "invalid",
		containment: "document",
		helper: "clone",
		cursor: "move",
		start: function( event, ui){
			jQuery("body").addClass("no-hover-css");
		},
		stop: function( event, ui ) {
			jQuery("body").removeClass("no-hover-css");
		}
	});

	jQuery("h3.title").livequery(function(){
		jQuery(this).on("contextmenu",function(e) {
			e.preventDefault();
			if(wcp_settings.can_manage_folder == 0) {
				return;
			}
			isHigh = jQuery(this).closest("li.route").hasClass("is-high");
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			menuHtml = "<div class='dynamic-menu'><ul>" +
			"<li class='new-folder'><a href='javascript:;'><span class='folder-icon-create_new_folder'></span> New Folder</a></li>" +
			"<li class='rename-folder'><a href='javascript:;'><span class='folder-icon-border_color'><span class='path1'></span><span class='path2'></span></span> Rename</a></li>" +
			"<li class='mark-folder'><a href='javascript:;'><span class='folder-icon-star_rate'></span>" + ((isHigh) ? " Remove Star" : "Add a Star") + "</a></li>" +
			"<li class='remove-folder'><a href='javascript:;'><span class='folder-icon-delete'></span> Delete</a></li>" +
			"</ul></div>";
			jQuery(this).after(menuHtml);
			jQuery(this).parents("li.route").addClass("active-menu");
			return false;
		});
	});

	jQuery("body").click(function(){
		jQuery(".dynamic-menu").remove();
		jQuery(".active-menu").removeClass("active-menu");
	});

	jQuery(".dynamic-menu").livequery(function(){
		jQuery(this).click(function(e){
			e.stopPropagation();
		});
	});

	jQuery(".rename-folder").livequery(function(){
		jQuery(this).click(function(e){
			e.stopPropagation();
			fileFolderID = jQuery(this).closest("li.route").data("folder-id");
			updateFolder();
			add_menu_to_list();
		});
	});

	jQuery(".mark-folder").livequery(function(){
		jQuery(this).click(function(e){
			e.stopPropagation();
			folderID = jQuery(this).closest("li.route").data("folder-id");
			nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("star"));
			jQuery(".form-loader-count").css("width","100%");
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "term_id=" + folderID + "&type=" + wcp_settings.post_type + "&action=wcp_mark_un_mark_folder&nonce="+nonce,
				method: 'post',
				cache: false,
				success: function (res) {
					res = jQuery.parseJSON(res);
					jQuery(".form-loader-count").css("width","0%");
					if (res.status == '1') {
						if(res.marked == '1') {
							jQuery("#wcp_folder_"+res.id).addClass("is-high");
						} else {
							jQuery("#wcp_folder_"+res.id).removeClass("is-high");
						}
						add_menu_to_list();
					} else {
						Swal.fire(
							'',
							res.message,
							'error'
						);
					}
				}
			});
		});
	});

	/* Add new folder */
	jQuery(".new-folder").livequery(function(){
		jQuery(this).click(function(e) {
			e.stopPropagation();
			jQuery(".active-menu").removeClass("active-menu");
			fileFolderID = jQuery(this).closest("li.route").data("folder-id");
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			addFolder();
			add_menu_to_list();
		});
	});

	jQuery(".cancel-button").livequery(function(){
		jQuery(this).click(function(e){
			e.stopPropagation();
			jQuery(".form-li").remove();
		});
	});



	jQuery("#add-new-folder").livequery(function(){
		jQuery(this).click(function() {
			if(jQuery("#custom-menu li.active-item").length) {
				fileFolderID = jQuery("#custom-menu li.active-item").data("folder-id");
			} else {
				fileFolderID = 0;
			}
			addFolder();
			add_menu_to_list();
		});
	});

	jQuery("#inline-update").click(function(){
		if(jQuery("#custom-menu li.active-item").length) {
			fileFolderID = jQuery("#custom-menu li.active-item").data("folder-id");
			updateFolder();
			add_menu_to_list();
		}
	});

	jQuery("#inline-remove").click(function(){
		if(jQuery("#custom-menu li.active-item").length) {
			fileFolderID = jQuery("#custom-menu li.active-item").data("folder-id");
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			nonce = jQuery.trim(jQuery("#wcp_folder_"+fileFolderID).data("delete"));
			Swal.fire({
				url: wcp_settings.ajax_url,
				title: 'Are you sure you want to delete the selected folder?',
				text: 'Items in the folder will not be deleted.',
				type: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'No, keep it',
			}).then((result) => {
				if (result.value) {
				Swal({
					title: 'Please wait..',
					imageUrl: wcp_settings.ajax_image,
					imageAlt: 'The uploaded picture',
					showConfirmButton: false
				});
				jQuery.ajax({
					url: wcp_settings.ajax_url,
					data: "type=" + wcp_settings.post_type + "&action=wcp_remove_folder&term_id=" + fileFolderID+"&nonce="+nonce,
					method: 'post',
					success: function (res) {
						res = jQuery.parseJSON(res);
						if (res.status == '1') {
							Swal.fire(
								'Deleted!',
								'Your folder has been deleted.',
								'success'
							);
							jQuery("#wcp_folder_"+fileFolderID).remove();
							jQuery("#folder_"+fileFolderID).remove();
							isKeyActive = parseInt(res.is_key_active);
							totalFolders = parseInt(res.folders);
							jQuery("#current-folder").text(totalFolders);
							if(totalFolders > folderLimitation) {
								folderLimitation = totalFolders;
							}
							jQuery("#total-folder").text(folderLimitation);
							add_menu_to_list();
						} else {
							Swal.fire(
								'',
								res.message,
								'error'
							);
						}
					}
				});
			}
		});
		}
	});

	if(wcp_settings.can_manage_folder == "1") {
		jQuery('.space').livequery(function () {
			jQuery(this).sortable({
				placeholder: "ui-state-highlight",
				connectWith: '.space',
				tolerance: 'intersect',
				over: function (event, ui) {

				},
				update: function (event, ui) {
					thisId = ui.item.context.attributes['data-folder-id'].nodeValue;
					orderString = "";
					jQuery(this).children().each(function () {
						if (jQuery(this).hasClass("route")) {
							orderString += jQuery(this).data("folder-id") + ",";
						}
					});
					if (orderString != "") {
						jQuery(".form-loader-count").css("width", "100%");
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "term_ids=" + orderString + "&action=wcp_save_folder_order&type=" + wcp_settings.post_type + "&nonce=" + wcp_settings.nonce,
							method: 'post',
							success: function (res) {
								res = jQuery.parseJSON(res);
								if (res.status == '1') {
									jQuery("#wcp_folder_parent").html(res.options);
									jQuery(".form-loader-count").css("width", "0");
									add_menu_to_list();
								} else {
									Swal.fire(
										'',
										res.message,
										'error'
									);
									window.location.reload(true);
								}
							}
						});
					}
				},
				receive: function (event, ui) {
					calcWidth(jQuery(this).siblings('.title'));
					check_for_sub_menu();
					jQuery(this).closest("li.route").addClass("active");
					jQuery(this).closest("li.route").find("ul.ui-sortable:first-child > li").slideDown();
					parentId = jQuery(this).closest("li.route").data("folder-id");
					thisId = ui.item.context.attributes['data-folder-id'].nodeValue;
					if (parentId == undefined) {
						parentId = 0;
					}
					orderString = "";
					if (jQuery("#wcp_folder_" + parentId + " .ui-sortable li").length) {
						jQuery("#wcp_folder_" + parentId + " .ui-sortable li").each(function () {
							orderString += jQuery(this).data("folder-id") + ",";
						});
					} else if (parentId == 0) {
						jQuery("#custom-menu > ul.space > li").each(function () {
							orderString += jQuery(this).data("folder-id") + ",";
						});
					}
					jQuery(".form-loader-count").css("width", "100%");
					nonce = jQuery.trim(jQuery("#wcp_folder_" + thisId).data("nonce"));
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "term_id=" + thisId + "&action=wcp_update_parent_information&parent_id=" + parentId + "&type=" + wcp_settings.post_type + "&nonce=" + nonce,
						method: 'post',
						success: function (res) {
							jQuery(".form-loader-count").css("width", "0%");
							res = jQuery.parseJSON(res);
							if (res.status == 0) {
								Swal.fire(
									'',
									res.message,
									'error'
								);
							} else {
								add_menu_to_list();
							}
						}
					});
				}
			});
			jQuery(this).disableSelection();
		});
	}
	jQuery(".update-inline-record").livequery(function(){
		jQuery(this).click(function(e){
			e.stopPropagation();
			isHigh = jQuery(this).closest("li.route").hasClass("is-high");
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			menuHtml = "<div class='dynamic-menu'><ul>" +
			"<li class='new-folder'><a href='javascript:;'><span class='folder-icon-create_new_folder'></span> New Folder</a></li>" +
			"<li class='rename-folder'><a href='javascript:;'><span class='folder-icon-border_color'><span class='path1'></span><span class='path2'></span></span> Rename</a></li>" +
			"<li class='mark-folder'><a href='javascript:;'><span class='folder-icon-star_rate'></span>" + ((isHigh) ? " Remove Star" : "Add a Star") + "</a></li>" +
			"<li class='remove-folder'><a href='javascript:;'><span class='folder-icon-delete'></span> Delete</a></li>" +
			"</ul></div>";
			jQuery(this).closest("h3.title").after(menuHtml);
			jQuery(this).parents("li.route").addClass("active-menu");
		});
	});
	//check_for_sub_menu();
	//jQuery(".has-sub-tree:first").addClass("active");
	jQuery(".nav-icon").livequery(function(){
		jQuery(this).click(function(){
			folderID = jQuery(this).closest("li.route").data("folder-id");
			if(jQuery("#wcp_folder_"+folderID).hasClass("active")) {
				folderStatus = 0;
			} else {
				folderStatus = 1;
			}
			jQuery(".form-loader-count").css("width","100%");
			nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
			checkForExpandCollapse();
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "is_active=" + folderStatus + "&action=save_wcp_folder_state&term_id=" + folderID+"&nonce="+nonce,
				method: 'post',
				success: function (res) {
					jQuery(".form-loader-count").css("width","0");
					res = jQuery.parseJSON(res);
					if(res.status == "0") {
						Swal.fire(
							'',
							res.message,
							'error'
						);
					} else {
						if(jQuery("#wcp_folder_"+folderID).hasClass("active")) {
							jQuery("#wcp_folder_"+folderID).removeClass("active");
							jQuery("#wcp_folder_"+folderID).find("ul.ui-sortable:first-child > li").slideUp();
							folderStatus = 0;
						} else {
							jQuery("#wcp_folder_"+folderID).addClass("active");
							jQuery("#wcp_folder_"+folderID).find("ul.ui-sortable:first-child > li").slideDown();
							folderStatus = 1;
						}
						add_menu_to_list();
					}
				}
			});
		});
	});
	jQuery("#custom-menu .ui-icon, #custom-menu h3").livequery(function(){
		jQuery(this).click(function(){
			jQuery("#custom-menu .active-item").removeClass("active-item");
			jQuery(this).closest(".route").addClass("active-item");
			add_menu_to_list();
		});
	});
	jQuery(".remove-folder").livequery(function(){
		jQuery(this).click(function() {
			folderID = jQuery(this).closest("li.route").data("folder-id");
			fileFolderID = folderID;
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			nonce = jQuery.trim(jQuery("#wcp_folder_"+fileFolderID).data("delete"));
			Swal.fire({
				url: wcp_settings.ajax_url,
				title: 'Are you sure you want to delete the selected folder?',
				text: 'Items in the folder will not be deleted.',
				type: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'No, keep it',
				}).then((result) => {
				if (result.value) {
					Swal({
						title: 'Please wait..',
						imageUrl: wcp_settings.ajax_image,
						imageAlt: 'The uploaded picture',
						showConfirmButton: false
					});
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "type=" + wcp_settings.post_type + "&action=wcp_remove_folder&term_id=" + folderID+"&nonce="+nonce,
						method: 'post',
						success: function (res) {
							res = jQuery.parseJSON(res);
							if (res.status == '1') {
								Swal.fire(
									'Deleted!',
									'Your folder has been deleted.',
									'success'
								);
								jQuery("#wcp_folder_"+fileFolderID).remove();
								jQuery("#folder_"+fileFolderID).remove();
								isKeyActive = parseInt(res.is_key_active);
								totalFolders = parseInt(res.folders);
								jQuery("#current-folder").text(totalFolders);
								if(totalFolders > folderLimitation) {
									folderLimitation = totalFolders;
								}
								jQuery("#total-folder").text(folderLimitation);
								add_menu_to_list();
							} else {
								Swal.fire(
									'',
									res.message,
									'error'
								);
							}
						}
					});
				}
			});
		});
	});
	jQuery(".wcp-parent .fa-caret-right").livequery(function(){
		jQuery(this).click(function() {
			autoStatus = 1;
			if (jQuery(this).closest(".wcp-parent").hasClass("active")) {
				jQuery(this).closest(".wcp-parent").removeClass("active");
				jQuery("#custom-menu").removeClass("active");
				autoStatus = 0;
			} else {
				jQuery(this).closest(".wcp-parent").addClass("active");
				jQuery("#custom-menu").addClass("active");
			}
			jQuery(".form-loader-count").css("width","100%");
			add_menu_to_list();
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_save_parent_data&is_active=" + autoStatus+"&nonce="+wcp_settings.nonce,
				method: 'post',
				success: function (res) {
					jQuery(".form-loader-count").css("width","0%");
					res = jQuery.parseJSON(res);
					if (res.status == '1') {
						Swal.fire(
							'',
							res.message,
							'error'
						);
					}
				}
			});
		});
	});

	checkForExpandCollapse();
})

function checkForExpandCollapse() {
	add_menu_to_list();
	currentStatus = true;
	if((jQuery("#custom-menu .has-sub-tree").length == jQuery("#custom-menu .has-sub-tree.active").length) && jQuery("#custom-menu .has-sub-tree").length) {
		jQuery("#expand-collapse-list").addClass("all-open");
	} else {
		jQuery("#expand-collapse-list").removeClass("all-open");
	}
}

function check_for_sub_menu() {
	jQuery("#custom-menu li.route").removeClass("has-sub-tree");
	jQuery("#custom-menu li.route").each(function(){
		if(jQuery(this).find("ul.ui-sortable li").length) {
			jQuery(this).addClass("has-sub-tree");
			if(jQuery(this).find("ul.ui-sortable:first").is(":hidden")) {
				jQuery(this).removeClass("is-hidden");
			} else {
				jQuery(this).addClass("is-hidden")
			}
		} else {
			jQuery(this).removeClass("active");
		}
	});
}

//recursively calculate the Width all titles
function calcWidth(obj){
	var titles =
		jQuery(obj).siblings('.space').children('.route').children('.title');
	jQuery(titles).each(function(index, element){
		var pTitleWidth = parseInt(jQuery(obj).css('width'));
		var leftOffset = parseInt(jQuery(obj).siblings('.space').css('margin-left'));
		var newWidth = pTitleWidth - leftOffset;
		if (jQuery(obj).attr('id') == 'title_0'){
			newWidth = newWidth - 10;
		}
		jQuery(element).css({
			'width': newWidth
		});
		calcWidth(element);
	});

}

/* code for sticky menu for media screen*/

if(wcp_settings.post_type == "attachment") {
	jQuery(window).load(function() {
		jQuery("button.button.media-button.select-mode-toggle-button").after("<button class='button organize-button'>Organize</button>");
		jQuery(".media-toolbar-secondary").append("<span class='media-info-message'>Drag and drop your media files to the relevant folders</span>");
		if(jQuery(".wcp-custom-form").length) {
			if (wp.Uploader !== undefined) {
				wp.Uploader.queue.on('reset', function () {
					resetMediaData(1);
				});
			}
			jQuery(document).ajaxComplete(function(ev, jqXHR, settings) {
				actionName = settings.data;
				if(actionName.indexOf("action=delete-post&id=") == 0) {
					resetMediaData(0);
				}
			});
		}
		setTimeout(function(){
			docReferrar = document.referrer;
			if(docReferrar.indexOf("wp-admin/upload.php") != -1) {
				mediaMode = getCookie("media-select-mode");
				if (mediaMode === "on") {
					jQuery("button.button.media-button.select-mode-toggle-button").trigger("click");
					jQuery(".attachments-browser li.attachment").draggable("enable");

					if (jQuery(".media-frame").hasClass("mode-select")) {
						jQuery(".media-info-message").addClass("active");
					} else {
						jQuery(".media-info-message").removeClass("active");
					}
				}
			} else {
				eraseCookie("media-select-mode");
			}
		}, 1000)
	});

	function resetMediaData(loadData) {
		jQuery.ajax({
			url: wcp_settings.ajax_url,
			data: "type=" + wcp_settings.post_type + "&action=wcp_get_default_list",
			method: 'post',
			success: function (res) {
				res = jQuery.parseJSON(res);
				jQuery("#custom-menu > ul#space_0").html(res.data);
				jQuery(".header-posts .total-count").text(res.total_items);
				jQuery(".un-categorised-items .total-count").text(res.empty_items);
				selectedVal = jQuery("#media-attachment-taxonomy-filter").val();
				if(selectedVal != "all" && loadData == 1) {
					var wp1 = parent.wp;
					wp1.media.frame.setState('insert');
					if (wp1.media.frame.content.get() !== null) {
						wp1.media.frame.content.get().collection.props.set({ignore: (+new Date())});
						wp1.media.frame.content.get().options.selection.reset();
					} else {
						wp1.media.frame.library.props.set({ignore: (+new Date())});
					}
				}
				if(res.taxonomies.length) {
					var selectedDD = jQuery("#media-attachment-taxonomy-filter");
					selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
					for(i=0; i<res.taxonomies.length; i++) {
						selectedDD.append("<option value='"+i+"'>"+res.taxonomies[i].name+" ("+res.taxonomies[i].count+")</option>");
					}
					selectedDD.val(selectedVal);
				}
			}
		});
	}

	function setMediaBoxWidth() {
		jQuery(".media-frame-content .media-toolbar").width(jQuery(".media-frame-content").width() - 20);
	}

	setMediaBoxWidth();

	jQuery(window).resize(function(){
		setMediaBoxWidth();
	});

	jQuery(document).ready(function(){

	});

	jQuery(document).on("click", ".button.organize-button", function(){
		if(!jQuery(".media-frame").hasClass("mode-select")) {
			setCookie("media-select-mode", "on", 7);
		} else {
			eraseCookie("media-select-mode");
		}
		jQuery("button.button.media-button.select-mode-toggle-button").trigger("click");
		if(jQuery(".media-frame").hasClass("mode-select")) {
			jQuery(".media-info-message").addClass("active");
		} else {
			jQuery(".media-info-message").removeClass("active");
		}
	});

	jQuery(document).on("click", ".select-mode-toggle-button", function(){
		setTimeout(function() {
			if(!jQuery(".media-frame").hasClass("mode-select")) {
				setCookie("media-select-mode", "off", -1);
			}
			if(jQuery(".media-frame").hasClass("mode-select")) {
				jQuery(".media-info-message").addClass("active");
			} else {
				jQuery(".media-info-message").removeClass("active");
			}
		}, 10);
	});

	function setCookie(name,value,days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}


	function eraseCookie(name) {
		document.cookie = name+'=; Max-Age=-99999999;';
	}

	function setStickyHeaderForMedia() {
		if(!jQuery(".media-position").length) {
			jQuery(".media-frame-content .media-toolbar").before("<div class='media-position'></div>")
		}

		if(jQuery(".media-position").length) {
			setMediaBoxWidth();

			thisPosition = jQuery(".media-position").offset().top - jQuery(window).scrollTop();
			if(thisPosition <= 32) {
				jQuery(".media-frame-content .media-toolbar").addClass("sticky-media");
				jQuery(".media-position").height(jQuery(".media-frame-content .media-toolbar").outerHeight());
			} else {
				jQuery(".media-frame-content .media-toolbar").removeClass("sticky-media");
				jQuery(".media-position").height(1);
			}
		}
	}

	jQuery(window).scroll(function(){
		setStickyHeaderForMedia()
	});
} else {
	function setStickyHeaderForMedia() {}
}