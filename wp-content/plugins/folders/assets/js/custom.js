var defaultFolderHtml;
var folderID = 0;
var fileAddUpdateStatus = "add";
var fileFolderID = 0;
var folderNameDynamic = "";
var n_o_file = -1;
var isKeyActive = 0;
var nonce = "";
var folderId = 0;
var fID = 0;
var folderCurrentURL = wcp_settings.page_url;
var activeRecordID = "";
var folderIDs = "";
var isMultipleRemove = false;
var isItFromMedia = false;
var isDuplicate = false;
var duplicateFolderId = 0;
var $action_form;
var lastOrderStatus = "";

var listFolderString = "<li class='grid-view' data-id='__folder_id__' id='folder___folder_id__'>" +
	"<div class='folder-item is-folder' data-id='__folder_id__'>" +
	"<a title='__folder_name__' id='folder_view___folder_id__'" +
	"class='folder-view __append_class__ has-new-folder'" +
	"data-id='__folder_id__'>" +
	"<span class='folder item-name'><span id='wcp_folder_text___folder_id__'" +
	"class='folder-title'>__folder_name__</span></span>" +
	"</a>" +
	"</div>" +
	"</li>";

jQuery(document).ready(function(){
    //jQuery("#bulk-action-selector-top").closest("form").on("submit", function(){
    //    alert("submitted");
    //    return false;
    //});

	jQuery(document).on("click", ".folder-sort-menu a", function(e) {
		e.stopPropagation();
		e.preventDefault();
		jQuery(".form-loader-count").css("width", "100%");
		jQuery(".folder-order").removeClass("active");
		lastOrderStatus = jQuery(this).attr("data-sort");
		jQuery.ajax({
			url: wcp_settings.ajax_url,
			data: "type=" + wcp_settings.post_type + "&action=wcp_folders_by_order&nonce=" + wcp_settings.nonce+"&order="+jQuery(this).attr("data-sort"),
			method: 'post',
			success: function (res) {
				res = jQuery.parseJSON(res);
				if(res.status == 1) {
					jQuery("#space_0").html(res.data);
				}
				jQuery(".form-loader-count").css("width", "0");
				add_active_item_to_list();
			}
		});
	});

	jQuery(document).on("click", "body, html", function(){
		jQuery(".folder-order").removeClass("active");
	});

	jQuery(document).on("click", "#sort-order-list", function(e){
		e.stopPropagation();
		jQuery(".folder-order").toggleClass("active");
	});

    if(wcp_settings.post_type == "attachment") {
        if(!jQuery(".move-to-folder-top").length) {
            jQuery("#bulk-action-selector-top").append("<option class='move-to-folder-top' value='move_to_folder'>Move to Folder</option>");
        }
        if(!jQuery(".move-to-folder-bottom").length) {
            jQuery("#bulk-action-selector-bottom").append("<option class='move-to-folder-bottom' value='move_to_folder'>Move to Folder</option>");
        }
    }


    if(wcp_settings.page_url != wcp_settings.current_url) {
        folderCurrentURL = wcp_settings.current_url;
    }
    activeRecordID = wcp_settings.selected_taxonomy;
    jQuery(document).on("click", ".select-all-item-btn", function(e){
        if(jQuery("ul.attachments li.selected").length == 0) {
            jQuery(".custom-media-select").removeClass("active");
        } else {
            jQuery(".custom-media-select").addClass("active");
        }
    });
    jQuery(document).on("click", "#doaction", function(e){
        if(jQuery("#bulk-action-selector-top").val() == "move_to_folder") {
            show_folder_popup();
            return false;
        } else if(jQuery("#bulk-action-selector-top").val() == "edit") {
            if(typeof inlineEditPost == "object") {
                inlineEditPost.setBulk();
                return false;
            }
        }
    });
    jQuery(document).on("click", "#doaction2", function(e){
        if(jQuery("#bulk-action-selector-bottom").val() == "move_to_folder") {
            show_folder_popup();
            return false;
        } else if(jQuery("#bulk-action-selector-bottom").val() == "edit") {
            if(typeof inlineEditPost == "object") {
                inlineEditPost.setBulk();
                return false;
            }
        }
    });
	jQuery(document).on("click", ".form-cancel-btn", function(){
		jQuery(".folder-popup-form").hide();
	});
	jQuery(document).on("click", ".folder-popup-form", function (e) {
		jQuery(".folder-popup-form").hide();
	});
	jQuery(document).on("click", ".popup-form-content", function (e) {
		e.stopPropagation();
	});
	jQuery(document).on("submit", "#save-folder-form", function(e){
		e.stopPropagation();
		e.preventDefault();

		folderNameDynamic = jQuery("#add-update-folder-name").val();

		if(jQuery.trim(folderNameDynamic) == "") {
			jQuery(".folder-form-errors").addClass("active");
			jQuery("#add-update-folder-name").focus();
		} else {
			jQuery("#save-folder-data").html('<span class="dashicons dashicons-update"></span>');
			jQuery("#add-update-folder").addClass("disabled");

			var ajax_url = "parent_id=" + fileFolderID + "&type=" + wcp_settings.post_type + "&action=wcp_add_new_folder&nonce=" + wcp_settings.nonce + "&term_id=" + fileFolderID + "&order=" + folderOrder + "&name=" + folderNameDynamic+"&is_duplicate="+isDuplicate+"&duplicate_from="+duplicateFolderId;
			if(isItFromMedia) {
				ajax_url = "parent_id=0&type=" + wcp_settings.post_type + "&action=wcp_add_new_folder&nonce=" + wcp_settings.nonce + "&term_id=0&order=" + folderOrder + "&name=" + folderNameDynamic+"&is_duplicate="+isDuplicate+"&duplicate_from="+duplicateFolderId;
			}

			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: ajax_url,
				method: 'post',
				success: function (res) {
					result = jQuery.parseJSON(res);
					if (result.status == '1') {
						jQuery("#space_" + result.parent_id).append(result.term_data);
						jQuery("#wcp_folder_" + result.parent_id).addClass("active has-sub-tree");
						isKeyActive = parseInt(result.is_key_active);
						n_o_file = parseInt(result.folders);
						jQuery("#current-folder").text(n_o_file);
						jQuery("#ttl-fldr").text((4*4)-(2*2)-2);
						checkForExpandCollapse();
						add_menu_to_list();
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						ajaxAnimation();
						if(jQuery("#media-attachment-taxonomy-filter").length) {
							fileFolderID = result.term_id;
							resetMediaData(0);
						}
					} else {
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(result.message);
						jQuery("#error-folder-popup").show();
					}
				}
			});
		}
		return false;
	});
	jQuery(document).on("change", "#bulk-select", function(e) {
        if(jQuery("#bulk-select").val() != "") {
            jQuery("#move-to-folder").attr("disabled", false);
        } else {
            jQuery("#move-to-folder").attr("disabled", true);
        }
    });
	jQuery(document).on("submit", "#bulk-folder-form", function(e) {
        e.stopPropagation();
        e.preventDefault();

        if(jQuery("#bulk-select").val() != "") {
            chkStr = "";
            jQuery(".wp-list-table input:checked").each(function () {
                chkStr += jQuery(this).val() + ",";
            });
            if(jQuery("#bulk-select").val() != "") {
                if (jQuery("#bulk-select").val() == "-1") {
                    jQuery.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + jQuery(this).val() + "&nonce=" + wcp_settings.nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                        method: 'post',
                        success: function (res) {
                            jQuery("#bulk-move-folder").hide();
                            resetMediaAndPosts();
                            ajaxAnimation();
                        }
                    });
                } else {
                    nonce = jQuery.trim(jQuery("#wcp_folder_" + jQuery("#bulk-select").val()).data("nonce"));
                    jQuery.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + jQuery("#bulk-select").val() + "&nonce=" + nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                        method: 'post',
                        success: function (res) {
                            res = jQuery.parseJSON(res);
                            jQuery("#bulk-move-folder").hide();
                            if (res.status == "1") {
                                resetMediaAndPosts();
                                ajaxAnimation();
                            } else {
                                jQuery(".folder-popup-form").hide();
                                jQuery(".folder-popup-form").removeClass("disabled");
                                jQuery("#error-folder-popup-message").html(res.message);
                                jQuery("#error-folder-popup").show()
                            }
                        }
                    });
                }
            }
        }
    });
	jQuery(document).on("submit", "#update-folder-form", function(e){
		e.stopPropagation();
		e.preventDefault();

		folderNameDynamic = jQuery("#update-folder-item-name").val();

		if(jQuery.trim(folderNameDynamic) == "") {
			jQuery(".folder-form-errors").addClass("active");
			jQuery("#update-folder-item-name").focus();
		} else {
			jQuery("#update-folder-data").html('<span class="dashicons dashicons-update"></span>');
			jQuery("#update-folder-item").addClass("disabled");

			nonce = jQuery.trim(jQuery("#wcp_folder_" + fileFolderID).data("rename"));
			parentID = jQuery("#wcp_folder_" + fileFolderID).closest("li.route").data("folder-id");
			if (parentID == undefined) {
				parentID = 0;
			}
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "parent_id=" + parentID + "&nonce=" + nonce + "&type=" + wcp_settings.post_type + "&action=wcp_update_folder&term_id=" + fileFolderID + "&name=" + folderNameDynamic,
				method: 'post',
				success: function (res) {
					result = jQuery.parseJSON(res);
					if (result.status == '1') {
						jQuery("#wcp_folder_" + result.id + " > h3 > .title-text").text(result.term_title);
						jQuery("#wcp_folder_" + result.id + " > h3").attr("title", result.term_title);
						add_menu_to_list();
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						ajaxAnimation();
						if(jQuery("#media-attachment-taxonomy-filter").length) {
							resetMediaData(0)
						}
					} else {
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(result.message);
						jQuery("#error-folder-popup").show();
					}
				}
			});
		}
		return false;
	});
	jQuery(document).on("click", "#remove-folder-item", function (e){
		e.stopPropagation();
		jQuery(".folder-popup-form").addClass("disabled");
		jQuery("#remove-folder-item").html('<span class="dashicons dashicons-update"></span>');
		nonce = jQuery.trim(jQuery("#wcp_folder_"+fileFolderID).data("delete"));
		if(isMultipleRemove) {
			removeMultipleFolderItems();
		} else {
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_remove_folder&term_id=" + fileFolderID + "&nonce=" + nonce,
				method: 'post',
				success: function (res) {
					res = jQuery.parseJSON(res);
					if (res.status == '1') {
						jQuery("#wcp_folder_" + fileFolderID).remove();
						jQuery("#folder_" + fileFolderID).remove();
						isKeyActive = parseInt(res.is_key_active);
						n_o_file = parseInt(res.folders);
						jQuery("#current-folder").text(n_o_file);
						jQuery("#ttl-fldr").text((3*3)+(4/(2*2)));
						add_menu_to_list();
						ajaxAnimation();
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						resetMediaAndPosts();

						if (activeRecordID == fileFolderID) {
							jQuery(".header-posts").trigger("click");
						}
					} else {
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(res.message);
						jQuery("#error-folder-popup").show();
					}
				}
			});
		}
	});
});

function show_folder_popup() {
    jQuery("#bulk-action-selector-top, #bulk-action-selector-bottom").val("-1");
    if(jQuery(".wp-list-table tbody input[type='checkbox']:checked").length == 0) {
        alert("Please select items to move in folder");
    } else {
        jQuery("#bulk-move-folder").show();
        jQuery("#bulk-select").html("<option value=''>Loading...</option>");
        jQuery(".move-to-folder").attr("disabled", true);
        jQuery.ajax({
            url: wcp_settings.ajax_url,
            data: "type=" + wcp_settings.post_type + "&action=wcp_get_default_list&active_id=" + activeRecordID,
            method: 'post',
            success: function (res) {
                res = jQuery.parseJSON(res);
                jQuery("#bulk-select").html("<option value=''>Select Folder</option><option value='-1'>(Unassigned)</option>");
                jQuery(".move-to-folder").attr("disabled", false);
                jQuery("#move-to-folder").attr("disabled", true);
                if(res.status == 1) {
                    var taxonomies = res.taxonomies;
                    for(i=0;i<taxonomies.length;i++) {
                        jQuery("#bulk-select").append("<option value='"+taxonomies[i].term_id+"'>"+taxonomies[i].name+"</option>");
                    }
                }
            }
        });
    }
}

function removeMultipleFolderItems() {
	if(jQuery("#folder-hide-show-checkbox").is(":checked")) {
		if(jQuery("#custom-menu input.checkbox:checked").length > 0) {
			var folderIDs = "";
			var activeItemDeleted = false;
			jQuery("#custom-menu input.checkbox:checked").each(function(){
				folderIDs += jQuery(this).val()+",";
				if(jQuery(this).closest("li.route").hasClass("active-item")) {
					activeItemDeleted = true;
				}
			});
			jQuery(".form-loader-count").css("width", "100%");
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_remove_muliple_folder&term_id=" + folderIDs+"&nonce="+wcp_settings.nonce,
				method: 'post',
				success: function (res) {
					res = jQuery.parseJSON(res);
					jQuery(".form-loader-count").css("width", "0px");
					if (res.status == '1') {
						isKeyActive = parseInt(res.is_key_active);
						n_o_file = parseInt(res.folders);
						jQuery("#current-folder").text(n_o_file);
						jQuery("#custom-menu input.checkbox:checked").each(function(){
							jQuery("#wcp_folder_"+jQuery(this).val()).closest("li.route").remove();
							jQuery("#space"+jQuery(this).val()).remove();
						});

						jQuery("#ttl-fldr").text((4*2)+(4/2));
						// add_menu_to_list();
						ajaxAnimation();
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						resetMediaAndPosts();

						ajaxAnimation();

						check_for_sub_menu();

						if(!jQuery("#wcp_folder_"+activeRecordID).length) {
							jQuery(".header-posts a").trigger("click");
							activeRecordID = 0;
						}
					} else {
                        window.location.reload();
                    }
					jQuery("#folder-hide-show-checkbox").attr("checked", false);
					jQuery("#custom-menu input.checkbox").attr("checked", false);
					jQuery("#custom-menu").removeClass("show-folder-checkbox");
				}
			});
		} else {

		}
	}
}

function triggerInlineUpdate() {
	add_active_item_to_list();

	jQuery(".form-loader-count").css("width", "0");
	if(typeof inlineEditPost == "object") {

        inlineEditPost.init();

		jQuery("#the-list").on("click",".editinline",function(){
            jQuery(this).attr("aria-expanded","true");
            inlineEditPost.edit(this);
        });
		jQuery(document).on("click", ".inline-edit-save .save", function(){
			var thisID = jQuery(this).closest("tr").attr("id");
			thisID = thisID.replace("edit-","");
			thisID = thisID.replace("post-","");
			inlineEditPost.save(thisID);
		});
		jQuery(document).on("click", ".inline-edit-save .cancel", function(){
			var thisID = jQuery(this).closest("tr").attr("id");
			thisID = thisID.replace("edit-","");
			thisID = thisID.replace("post-","");
			inlineEditPost.revert(thisID);
		});
	}

    if(wcp_settings.post_type == "attachment") {
        if(!jQuery(".move-to-folder-top").length) {
            jQuery("#bulk-action-selector-top").append("<option class='move-to-folder-top' value='move_to_folder'>Move to Folder</option>");
        }
        if(!jQuery(".move-to-folder-bottom").length) {
            jQuery("#bulk-action-selector-bottom").append("<option class='move-to-folder-bottom' value='move_to_folder'>Move to Folder</option>");
        }
    }
}

function set_default_folders(post_id) {
    jQuery.ajax({
        url: wcp_settings.ajax_url,
        type: 'post',
        data: 'action=save_folder_last_status&post_type='+wcp_settings.post_type+"&post_id="+post_id+"&nonce="+wcp_settings.nonce,
        cache: false,
        async: false,
        success: function(){

        }
    })
}

function ajaxAnimation() {
	jQuery(".folder-loader-ajax").addClass("active");
	jQuery(".folder-loader-ajax img").removeClass("active");
	jQuery(".folder-loader-ajax svg#successAnimation").addClass("active").addClass("animated");
	setTimeout(function(){
		jQuery(".folder-loader-ajax").removeClass("active");
		jQuery(".folder-loader-ajax img").addClass("active");
		jQuery(".folder-loader-ajax svg#successAnimation").removeClass("active").removeClass("animated");
	}, 2000);
}

function addFolder() {
	if(isKeyActive == 0 && n_o_file >= ((4*4)-(3*3)+(4/4)+(8/(2*2)))) {
		jQuery("#folder-limitation-message").html("You've "+"reached the "+((4*4)-(2*2)-2)+" folder limitation!");
		jQuery("#no-more-folder-credit").show();
		return false;
	}

	jQuery("#add-update-folder-title").text("Add new folder");
	jQuery("#save-folder-data").text("Submit");
	jQuery(".folder-form-errors").removeClass("active");
	jQuery("#add-update-folder-name").val("");
    if(isDuplicate) {
        duplicateFolderId = fileFolderID;
        jQuery("#add-update-folder-name").val(jQuery("#title_"+fileFolderID+" .title-text").text() + " #2");
        if(jQuery("li#wcp_folder_"+fileFolderID).parent().hasClass("first-space")) {
            fileFolderID = 0;
        } else {
            fileFolderID = jQuery("li#wcp_folder_"+fileFolderID).parent().parent().data("folder-id");
        }
    }

    folderOrder = jQuery("#space_"+fileFolderID+" > li").length+1;
    ajaxURL = wcp_settings.ajax_url+"?parent_id=" + fileFolderID + "&type=" + wcp_settings.post_type + "&action=wcp_add_new_folder&nonce=" + wcp_settings.nonce + "&term_id=" + fileFolderID + "&order=" + folderOrder+"&name=";

	jQuery("#add-update-folder").removeClass("disabled");
	jQuery("#add-update-folder").show();
	jQuery("#add-update-folder-name").focus();
}

function updateFolder() {
	folderName = jQuery.trim(jQuery("#wcp_folder_"+fileFolderID+" > h3 > .title-text").text());
	parentID = jQuery("#wcp_folder_"+fileFolderID).closest("li.route").data("folder-id");
	if(parentID == undefined) {
		parentID = 0;
	}

	jQuery("#update-folder-data").text("Submit");
	jQuery(".folder-form-errors").removeClass("active");
	jQuery("#update-folder-item-name").val(folderName);
	jQuery("#update-folder-item").removeClass("disabled");
	jQuery("#update-folder-item").show();
	jQuery("#update-folder-item-name").focus();
}

function removeFolderFromID(popup_type) {
	var removeMessage = "Are you sure you want to delete the selected folder?";
	var removeNotice = "Items in the folder will not be deleted.";
	isMultipleRemove = false;
	if(popup_type == 1) {
		if(jQuery("#folder-hide-show-checkbox").is(":checked")) {
			isMultipleRemove = true;
			if(jQuery("#custom-menu input.checkbox:checked").length ==	 0) {
				jQuery(".folder-popup-form").hide();
				jQuery(".folder-popup-form").removeClass("disabled");
				jQuery("#error-folder-popup-message").html("Please select at least one folder to delete");
				jQuery("#error-folder-popup").show();
				return;
			} else {
				if(jQuery("#custom-menu input.checkbox:checked").length > 1) {
					removeMessage = "Are you sure you want to delete the selected folders?";
					removeNotice = "Items in the selected folders will not be deleted.";
				}
			}
		}
	}
	jQuery(".folder-popup-form").hide();
	jQuery(".folder-popup-form").removeClass("disabled");
	jQuery("#remove-folder-item").text("Yes, Delete it!");
	jQuery("#remove-folder-message").text(removeMessage);
	jQuery("#remove-folder-notice").text(removeNotice);
	jQuery("#confirm-remove-folder").show();
	jQuery("#remove-folder-item").focus();
}

function resetMediaAndPosts() {
    if(jQuery(".media-toolbar").hasClass("media-toolbar-mode-select")) {
        if(jQuery("ul.attachments li.selected").length) {
            jQuery("ul.attachments li.selected").trigger("click");
            jQuery(".select-mode-toggle-button").trigger("click");
        }
    }
	if(folderIDs != "" && (jQuery("#custom-menu li.active-item").length > 0 || activeRecordID == "-1")) {
		if(jQuery("#media-attachment-taxonomy-filter").length) {
			folderIDs = folderIDs.split(",");
			for (var i = 0; i < folderIDs.length; i++) {
				if(folderIDs[i] != "") {
					jQuery(".attachments-browser li[data-id='"+folderIDs[i]+"']").remove();
				}
			}
		}
		folderIDs = "";
	}
	if(jQuery("#media-attachment-taxonomy-filter").length) {
		resetMediaData(0);
	} else {
		jQuery.ajax({
			url: wcp_settings.ajax_url,
			data: "type=" + wcp_settings.post_type + "&action=get_folders_default_list",
			method: 'post',
			success: function (res) {
				res = jQuery.parseJSON(res);
				// jQuery("#custom-menu > ul#space_0").html(res.data);
				jQuery(".header-posts .total-count").text(res.total_items);
				jQuery(".un-categorised-items .total-count").text(res.empty_items);

				for (i = 0; i < res.taxonomies.length; i++) {
					if(!jQuery("#title_"+res.taxonomies[i].term_id+" .total-count").length) {
						jQuery("#title_"+res.taxonomies[i].term_id+" .star-icon").before("<span class='total-count'></span>");
					}
					jQuery("#title_"+res.taxonomies[i].term_id+" .total-count").text(parseInt(res.taxonomies[i].trash_count));

                    if(!jQuery(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").length) {
                        jQuery(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" a").append("<span class='folder-count'></span>")
                    }
                    jQuery(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").text(parseInt(res.taxonomies[i].trash_count));
				}

				jQuery("#custom-menu .total-count").each(function(){
					if(parseInt(jQuery(this).text()) == 0) {
						jQuery(this).remove();
					}
				});

				jQuery(".sticky-folders .folder-count").each(function(){
					if(parseInt(jQuery(this).text()) == 0) {
						jQuery(this).remove();
					}
				});
			}
		});
		jQuery(".folder-loader-ajax").addClass("active");
        if(jQuery("#folder-posts-filter").length) {
            jQuery("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function () {
                var obj = { Title: "", Url: folderCurrentURL };
                history.pushState(obj, obj.Title, obj.Url);
                if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                    jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                }
                add_active_item_to_list();
                triggerInlineUpdate();
            });
        } else {
            jQuery("#wpbody").load(folderCurrentURL + " #wpbody-content", false, function (res) {
                var obj = { Title: "", Url: folderCurrentURL };
                history.pushState(obj, obj.Title, obj.Url);
                if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                    jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                }
                add_active_item_to_list();
                add_menu_to_list();
                // triggerInlineUpdate();
            });
        }
	}
}

function add_active_item_to_list() {
	folderId = 0;
	if(jQuery(".active-item").length) {
		folderId = jQuery(".active-item").data("folder-id");
		if(folderId == undefined) {
			folderId = 0;
		}
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

	apply_animation_height();

    if(wcp_settings.post_type == "attachment") {
        if(!jQuery(".move-to-folder-top").length) {
            jQuery("#bulk-action-selector-top").append("<option class='move-to-folder-top' value='move_to_folder'>Move to Folder</option>");
        }
        if(!jQuery(".move-to-folder-bottom").length) {
            jQuery("#bulk-action-selector-bottom").append("<option class='move-to-folder-bottom' value='move_to_folder'>Move to Folder</option>");
        }
    }

    jQuery(".sticky-folders .active-item").removeClass("active-item");
    if(jQuery("#custom-menu li.route.active-item").length) {
        var activeTermId = jQuery("#custom-menu li.route.active-item").data("folder-id");
        jQuery(".sticky-folders .sticky-folder-"+activeTermId+" a").addClass("active-item");
    }
}

document.onkeydown = function(evt) {
	evt = evt || window.event;
	var isEscape = false;
	if ("key" in evt) {
		isEscape = (evt.key === "Escape" || evt.key === "Esc");
	} else {
		isEscape = (evt.keyCode === 27);
	}
	if (isEscape) {
		jQuery(".folder-popup-form").hide();
	}
};

jQuery(window).on('load', function(){
	add_active_item_to_list();
});

function add_menu_to_list() {

	add_active_item_to_list();

	//apply_animation_height();
}

function apply_animation_height() {
	if(jQuery(".tree-structure-content .tree-structure li").length == 0) {
		jQuery(".tree-structure-content").hide();
	} else {
		jQuery(".tree-structure-content").show();
		oldHeight = jQuery(".tree-structure-content .tree-structure").height();
		jQuery(".tree-structure-content .tree-structure").height("auto");
		if(jQuery(".tree-structure-content .tree-structure").height() > 56) {
			jQuery(".folders-toggle-button").show();
		} else {
			jQuery(".folders-toggle-button").hide();
		}
		newHeight = jQuery(".tree-structure-content .tree-structure").height();
		jQuery(".tree-structure-content .tree-structure").attr("data-height", newHeight);

		if(jQuery(".tree-structure-content").hasClass("active")) {
			jQuery(".tree-structure-content .tree-structure").height(newHeight);
			jQuery(".tree-structure-content .tree-structure").attr("data-height", newHeight);
		} else {
			jQuery(".tree-structure-content .tree-structure").height(oldHeight);
		}
	}
}

jQuery(document).ready(function(){

	wcp_settings.folder_width = parseInt(wcp_settings.folder_width);

	apply_animation_height();

	jQuery(document).on("click", ".folders-toggle-button", function(){

		dbStatus = 'show';
		if(jQuery(".tree-structure-content").hasClass("active")) {
			jQuery(".tree-structure-content .tree-structure").animate({
				height: '55px'
			}, 100, function(){
				jQuery(".tree-structure-content").removeClass("active");
			});
			dbStatus = 'hide';
		} else {
			newHeight = parseInt(jQuery(".tree-structure-content .tree-structure").attr("data-height"));
			jQuery(".tree-structure-content .tree-structure").animate({
				height: newHeight
			}, 100, function(){
				jQuery(".tree-structure-content").addClass("active");
			});
		}

		jQuery.ajax({
			url: wcp_settings.ajax_url,
			data: "type=" + wcp_settings.post_type + "&action=wcp_hide_folders&status=" + dbStatus +"&nonce="+wcp_settings.nonce,
			method: 'post',
			success: function (res) {
				setStickyHeaderForMedia();
			}
		});
	});

	if(wcp_settings.can_manage_folder == "0") {
		jQuery(".wcp-custom-form a:not(.pink)").addClass("button-disabled");
	}

	isKeyActive = parseInt(wcp_settings.is_key_active);
	n_o_file = parseInt(wcp_settings.folders);

	if(wcp_settings.post_type == "attachment") {
        if(wcp_settings.show_in_page == "show") {
            jQuery(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div><div class="folders-toggle-button"><span></span></div></div>');
        }

		add_menu_to_list();

		apply_animation_height();
	}

	calcWidth(jQuery('#title_0'));

	jQuery("#cancel-button").click(function(){
		jQuery(".wcp-form-data").hide();
	});


	jQuery(document).on("click", "h3.title", function(e) {
		e.stopPropagation();
		jQuery(".un-categorised-items").removeClass("active-item");
		jQuery(".header-posts a").removeClass("active-item");
		activeRecordID = jQuery(this).closest("li.route").data("folder-id");
		if(!jQuery("#media-attachment-taxonomy-filter").length) {
			folderCurrentURL = wcp_settings.page_url + jQuery(this).closest("li.route").data("slug");
			jQuery(".form-loader-count").css("width", "100%");
            if(jQuery("#folder-posts-filter").length) {
                jQuery("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function () {
                    var obj = { Title: jQuery("#wcp_folder_"+activeRecordID).data("slug"), Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders(jQuery("#wcp_folder_"+activeRecordID).data("slug"));
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    triggerInlineUpdate();
                });
            } else {
                jQuery("#wpbody").load(folderCurrentURL + " #wpbody-content", function () {
                    var obj = { Title: jQuery("#wcp_folder_"+activeRecordID).data("slug"), Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders(jQuery("#wcp_folder_"+activeRecordID).data("slug"));
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    triggerInlineUpdate();
                });
            }
		} else {
			var thisIndex = jQuery(this).closest("li.route").data("folder-id");
			jQuery("#media-attachment-taxonomy-filter").val(thisIndex);
			jQuery("#media-attachment-taxonomy-filter").trigger("change");
            thisSlug = jQuery(this).closest("li.route").data("slug");
            folderCurrentURL = wcp_settings.page_url + jQuery(this).closest("li.route").data("slug");
            var obj = { Title: thisSlug, Url: folderCurrentURL };
            history.pushState(obj, obj.Title, obj.Url);
            set_default_folders(thisSlug);
            jQuery(".custom-media-select").removeClass("active");
			//add_menu_to_list();
		}
		add_active_item_to_list();
	});


	jQuery(".tree-structure a").livequery(function(){
		jQuery(this).click(function(){
			fID = jQuery(this).data("id");
			jQuery("#title_"+fID).trigger("click");
		});
	});

	jQuery(".wcp-parent > span").click(function(e){
		activeRecordID = "";
		jQuery(".wcp-container .route").removeClass("active-item");
		if(!jQuery("#media-attachment-taxonomy-filter").length) {
			folderCurrentURL = wcp_settings.page_url;
			jQuery(".form-loader-count").css("width", "100%");
            if(jQuery("#folder-posts-filter").length) {
                jQuery("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function () {
                    var obj = { Title: "", Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders("all");
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    triggerInlineUpdate();
                });
            } else {
                jQuery("#wpbody").load(folderCurrentURL + " #wpbody-content", function () {
                    var obj = { Title: "", Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders("all");
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    triggerInlineUpdate();
                });
            }
		} else {
			jQuery("#media-attachment-taxonomy-filter").val("all");
			jQuery("#media-attachment-taxonomy-filter").trigger("change");
		}
		add_active_item_to_list();
	});

	jQuery("h3.title").livequery(function(){
		jQuery(this).droppable({
			accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
			hoverClass: 'wcp-drop-hover',
			classes: {
				"ui-droppable-active": "ui-state-highlight"
			},
			drop: function( event, ui ) {
				folderID = jQuery(this).closest("li.route").data('folder-id');
				if ( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
					if(jQuery(".wp-list-table input:checked").length) {
						chkStr = "";
						jQuery(".wp-list-table input:checked").each(function(){
							chkStr += jQuery(this).val()+",";
						});
						nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
							method: 'post',
							success: function (res) {
								res = jQuery.parseJSON(res);
								if(res.status == "1") {
									resetMediaAndPosts();
									ajaxAnimation();
								} else {
									jQuery(".folder-popup-form").hide();
									jQuery(".folder-popup-form").removeClass("disabled");
									jQuery("#error-folder-popup-message").html(res.message);
									jQuery("#error-folder-popup").show()
								}
							}
						});
					}
				} else if( ui.draggable.hasClass( 'wcp-move-file' ) ){
					postID = ui.draggable[0].attributes['data-id'].nodeValue;
					nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
                    chkStr = postID+",";
                    jQuery(".wp-list-table input:checked").each(function(){
                        if(jQuery(this).val() != postID) {
                            chkStr += jQuery(this).val() + ",";
                        }
                    });
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
						method: 'post',
						success: function (res) {
							res = jQuery.parseJSON(res);
							if(res.status == "1") {
								// window.location.reload();
								resetMediaAndPosts();
								ajaxAnimation();
							} else {
								jQuery(".folder-popup-form").hide();
								jQuery(".folder-popup-form").removeClass("disabled");
								jQuery("#error-folder-popup-message").html(res.message);
								jQuery("#error-folder-popup").show()
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
					folderIDs = chkStr;
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
						method: 'post',
						success: function (res) {
							// window.location.reload();
							resetMediaAndPosts();
							ajaxAnimation();
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
	});

	jQuery(".media-button").livequery(function () {
		jQuery(this).click(function () {
			if (jQuery(".delete-selected-button").hasClass("hidden")) {
				//jQuery(".attachments-browser li.attachment").draggable("disable");
			} else {
				// jQuery(".attachments-browser li.attachment").draggable("enable");
			}
		});
	});

	jQuery(".header-posts").click(function(){
		activeRecordID = "";
		jQuery(".wcp-container .route").removeClass("active-item");
		jQuery(".un-categorised-items").removeClass("active-item");
		jQuery(".header-posts a").addClass("active-item");
		if(!jQuery("#media-attachment-taxonomy-filter").length) {
			folderCurrentURL = wcp_settings.page_url;
			jQuery(".form-loader-count").css("width", "100%");
            if(jQuery("#folder-posts-filter").length) {
                jQuery("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function () {
                    var obj = { Title: "", Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders("all");
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    add_active_item_to_list();
                    triggerInlineUpdate();
                });
            } else {
                jQuery("#wpbody").load(folderCurrentURL + " #wpbody-content", function () {
                    var obj = { Title: "", Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders("all");
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    add_active_item_to_list();
                    triggerInlineUpdate();
                });
            }
		} else {
			activeRecordID = "";
			jQuery("#media-attachment-taxonomy-filter").val("all");
			jQuery("#media-attachment-taxonomy-filter").trigger("change");
            var obj = { Title: "", Url: wcp_settings.page_url };
            history.pushState(obj, obj.Title, obj.Url);
            set_default_folders("all");
			add_active_item_to_list();
		}
	});

	jQuery(".un-categorised-items").click(function(){
		activeRecordID = "-1";
		jQuery(".wcp-container .route").removeClass("active-item");
		jQuery(".header-posts a").removeClass("active-item");
		jQuery(".un-categorised-items").addClass("active-item");
		if(!jQuery("#media-attachment-taxonomy-filter").length) {
			folderCurrentURL = wcp_settings.page_url+"-1";
			jQuery(".form-loader-count").css("width", "100%");
            if(jQuery("#folder-posts-filter").length) {
                jQuery("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function () {
                    var obj = { Title: "", Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders("-1");
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    add_active_item_to_list();
                    triggerInlineUpdate();
                });
            } else {
                jQuery("#wpbody").load(folderCurrentURL + " #wpbody-content", function () {
                    var obj = { Title: "", Url: folderCurrentURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    set_default_folders("-1");
                    if (wcp_settings.show_in_page == "show" && !jQuery(".tree-structure").length) {
                        jQuery(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    add_active_item_to_list();
                    triggerInlineUpdate();
                });
            }
		} else {
			jQuery("#media-attachment-taxonomy-filter").val("unassigned");
			jQuery("#media-attachment-taxonomy-filter").trigger("change");
            var obj = { Title: "", Url: wcp_settings.page_url+"-1" };
            history.pushState(obj, obj.Title, obj.Url);
            set_default_folders("-1");
			add_active_item_to_list();
		}
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
							data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
							method: 'post',
							success: function (res) {
								//window.location.reload();
								resetMediaAndPosts();
								ajaxAnimation();
							}
						});
					}
				} else if (ui.draggable.hasClass('wcp-move-file')) {
					postID = ui.draggable[0].attributes['data-id'].nodeValue;
                    chkStr = postID+",";
                    jQuery(".wp-list-table input:checked").each(function () {
                        if(postID != jQuery(this).val()) {
                            chkStr += jQuery(this).val() + ",";
                        }
                    });
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
						method: 'post',
						success: function (res) {
							//window.location.reload();
							resetMediaAndPosts();
							ajaxAnimation();
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
					folderIDs = chkStr;
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
						method: 'post',
						success: function (res) {
							// window.location.reload();
							resetMediaAndPosts();
							ajaxAnimation();
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

	jQuery(".tree-structure .folder-item").livequery(function(){
		jQuery(this).droppable({
			accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
			hoverClass: 'wcp-drop-hover-list',
			classes: {
				"ui-droppable-active": "ui-state-highlight"
			},
			drop: function( event, ui ) {
				jQuery("body").removeClass("no-hover-css");
				folderID = jQuery(this).data('id');
				if ( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
					nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
					if(jQuery(".wp-list-table input:checked").length) {
						chkStr = "";
						jQuery(".wp-list-table input:checked").each(function(){
							chkStr += jQuery(this).val()+",";
						});
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
							method: 'post',
							success: function (res) {
								// window.location.reload();
								resetMediaAndPosts();
								ajaxAnimation();
							}
						});
					}
				} else if ( ui.draggable.hasClass( 'wcp-move-file' ) ) {
					postID = ui.draggable[0].attributes['data-id'].nodeValue;
					nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
                    chkStr = postID+",";
                    jQuery(".wp-list-table input:checked").each(function(){
                        if(jQuery(this).val() != postID) {
                            chkStr += jQuery(this).val() + ",";
                        }
                    });
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
						method: 'post',
						success: function (res) {
							// window.location.reload();
							resetMediaAndPosts();
							ajaxAnimation();
						}
					});
				} else if( ui.draggable.hasClass( 'attachment' ) ){
					chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
					nonce = jQuery.trim(jQuery("#wcp_folder_"+folderID).data("nonce"));
					if(jQuery(".attachments-browser li.attachment.selected").length > 1) {
						chkStr = "";
						jQuery(".attachments-browser li.attachment.selected").each(function(){
							chkStr += jQuery(this).data("id")+",";
						});
					}
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
						method: 'post',
						success: function (res) {
							// window.location.reload();
							resetMediaAndPosts();
							ajaxAnimation();
						}
					});
				}
			}
		});
	});

	jQuery("#expand-collapse-list").click(function(e){
		e.stopPropagation();
		statusType = 0;
		if(jQuery(this).hasClass("all-open")) {
			jQuery(this).removeClass("all-open");
			jQuery(".has-sub-tree").removeClass("active");
			statusType = 0;
			jQuery(this).attr("data-folder-tooltip","Expand");
		} else {
			jQuery(this).addClass("all-open");
			statusType = 1;
			jQuery(".has-sub-tree").addClass("active");
			jQuery(this).attr("data-folder-tooltip","Collapse");
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
					// add_menu_to_list();
					res = jQuery.parseJSON(res);
					if(res.status == "0") {
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(res.message);
						jQuery("#error-folder-popup").show();
						window.location.reload(true);
					}
				}
			});
		}
	});

	if(wcp_settings.folder_width <= 275) {
		jQuery(".plugin-button").addClass("d-block");
	} else {
		jQuery(".plugin-button").removeClass("d-block");
	}

	resizeDirection = (wcp_settings.isRTL == "1" || wcp_settings.isRTL == 1)?"w":"e";
	jQuery(".wcp-content").resizable( {
		resizeHeight:   false,
		handles:        resizeDirection,
		minWidth:       100,
		maxWidth: 		500,
		resize: function( e, ui ) {
			var menuWidth = ui.size.width;
			if(menuWidth <= 275) {
				jQuery(".plugin-button").addClass("d-block");
			} else {
				jQuery(".plugin-button").removeClass("d-block");
			}
			if(menuWidth <= 225) {
				menuWidth = 225;
			}
			if(wcp_settings.isRTL == "1") {
				jQuery("#wpcontent").css("padding-right", (menuWidth + 20) + "px");
				jQuery("#wpcontent").css("padding-left", "0px");
			} else {
				jQuery("#wpcontent").css("padding-left", (menuWidth + 20) + "px");
			}
			newWidth = menuWidth - 40;
			cssString = "";
			classString = "";
			for(i=0; i<=15; i++) {
				classString += " .space > .route >";
				currentWidth = newWidth - (13+(20*i));
				cssString += "#custom-menu > "+classString+" .title { width: "+currentWidth+"px !important; } ";
				cssString += "#custom-menu > "+classString+" .dynamic-menu { left: "+(currentWidth - 190)+"px !important; } ";
				setStickyHeaderForMedia();
			}
			jQuery("#wcp-custom-style").html("<style>"+cssString+"</style>");
			if(ui.size.width <= 185) {
				folderStatus = "hide";
				jQuery(".wcp-hide-show-buttons .toggle-buttons.show-folders").addClass("active");
				jQuery(".wcp-hide-show-buttons .toggle-buttons.hide-folders").removeClass("active");
				jQuery("#wcp-content").addClass("hide-folders-area");
				if(wcp_settings.isRTL == "1") {
					jQuery("#wpcontent").css("padding-right", "20px");
					jQuery("#wpcontent").css("padding-left", "0px");
				} else {
					jQuery("#wpcontent").css("padding-left", "20px");
				}
			} else {
				if(jQuery("#wcp-content").hasClass("hide-folders-area")) {
					folderStatus = "show";
					jQuery(".wcp-hide-show-buttons .toggle-buttons.show-folders").removeClass("active");
					jQuery(".wcp-hide-show-buttons .toggle-buttons.hide-folders").addClass("active");
					jQuery("#wcp-content").addClass("no-transition");
					jQuery("#wcp-content").removeClass("hide-folders-area");
					if (wcp_settings.isRTL == "1") {
						jQuery("#wpcontent").css("padding-right", (wcp_settings.folder_width + 20) + "px");
						jQuery("#wpcontent").css("padding-left", "0px");
					} else {
						jQuery("#wpcontent").css("padding-left", (wcp_settings.folder_width + 20) + "px");
					}
					setTimeout(function () {
						jQuery("#wcp-content").removeClass("no-transition");
					}, 250);
				}
			}
		},
		stop: function( e, ui ) {
			var menuWidth = ui.size.width;
			if(ui.size.width <= 275) {
				jQuery(".plugin-button").addClass("d-block");
			} else {
				jQuery(".plugin-button").removeClass("d-block");
			}
			if(menuWidth <= 225) {
				menuWidth = 225;
			}
			if(ui.size.width <= 185) {
				folderStatus = "hide";
				jQuery(".wcp-hide-show-buttons .toggle-buttons.show-folders").addClass("active");
				jQuery(".wcp-hide-show-buttons .toggle-buttons.hide-folders").removeClass("active");
				jQuery("#wcp-content").addClass("hide-folders-area");
				if(wcp_settings.isRTL == "1") {
					jQuery("#wpcontent").css("padding-right", "20px");
					jQuery("#wpcontent").css("padding-left", "0px");
				} else {
					jQuery("#wpcontent").css("padding-left", "20px");
				}

				jQuery.ajax({
					url: wcp_settings.ajax_url,
					data: "type=" + wcp_settings.post_type + "&action=wcp_change_folder_display_status&status=" + folderStatus +"&nonce="+nonce,
					method: 'post',
					success: function (res) {
						setStickyHeaderForMedia();
					}
				});
			} else {
				if(jQuery("#wcp-content").hasClass("hide-folders-area")) {
					folderStatus = "show";
					jQuery(".wcp-hide-show-buttons .toggle-buttons.show-folders").removeClass("active");
					jQuery(".wcp-hide-show-buttons .toggle-buttons.hide-folders").addClass("active");
					jQuery("#wcp-content").addClass("no-transition");
					jQuery("#wcp-content").removeClass("hide-folders-area");
					if (wcp_settings.isRTL == "1") {
						jQuery("#wpcontent").css("padding-right", (wcp_settings.folder_width + 20) + "px");
						jQuery("#wpcontent").css("padding-left", "0px");
					} else {
						jQuery("#wpcontent").css("padding-left", (wcp_settings.folder_width + 20) + "px");
					}
					setTimeout(function () {
						jQuery("#wcp-content").removeClass("no-transition");
					}, 250);
				}
			}
			nonce = wcp_settings.nonce;
			wcp_settings.folder_width = ui.size.width;
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_change_post_width&width=" + menuWidth+"&nonce="+nonce,
				method: 'post',
				success: function (res) {
					setStickyHeaderForMedia();
				}
			});
			if(ui.size.width <= 225) {
				jQuery(".wcp-content").width(225);
				wcp_settings.folder_width = 225;
			}
		}
	});

	jQuery(".wcp-move-file").livequery(function(){
		jQuery(this).draggable({
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

	jQuery(".wcp-move-multiple").livequery(function(){
		jQuery(this).draggable({
			// /*cancel: "a.ui-icon",*/
			// revert: "invalid",
			// containment: "document",
			// helper: "clone",
			// cursor: "move",
			// start: function( event, ui){
			// 	jQuery("body").addClass("no-hover-css");
			// },
			// stop: function( event, ui ) {
			// 	jQuery("body").removeClass("no-hover-css");
			// }
			revert: "invalid",
			containment: "document",
			helper: function (event, ui) {
				jQuery(".selected-items").remove();
				selectedItems = jQuery("#the-list th input:checked").length;
				if(selectedItems > 0) {
					selectedItems = (selectedItems == 0 || selectedItems == 1) ? "1 Item" : selectedItems + " Items";
					return jQuery("<div class='selected-items'><span class='total-post-count'>" + selectedItems + " Selected</span></div>");
				} else {
					return  jQuery("<div class='selected-items'><span class='total-post-count'>Select Items to move</span></div>");
				}
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
                        "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span> New Folder</a></li>" +
                        "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span> Rename</a></li>" +
                        "<li class='sticky-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>Sticky Folder (Pro)</a></li>" +
                        "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? " Remove Star" : "Add a Star") + "</a></li>"+
                        "<li class='duplicate-folder'><a href='javascript:;'><span class=''><i class='pfolder-clone'></i></span> Duplicate folder</a></li>";

            /* checking for attachments */
            hasPosts = parseInt(jQuery(this).closest("li.route").find("h3.title:first > .total-count").text());
            if(wcp_settings.post_type == "attachment" && hasPosts) {
                menuHtml += "<li class='download-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span> Download Zip (Pro)</a></li>";
            }
				menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span> Delete</a></li>" +
                        "</ul></div>";
			jQuery(this).after(menuHtml);
			jQuery(this).parents("li.route").addClass("active-menu");
            if((jQuery(this).offset().top + jQuery(".dynamic-menu").height()) > (jQuery(window).height() - 20)) {
                jQuery(".dynamic-menu").addClass("bottom-fix");

                if(jQuery(".dynamic-menu.bottom-fix").offset().top < jQuery("#custom-scroll-menu").offset().top) {
                    jQuery(".dynamic-menu").removeClass("bottom-fix");
                }
            }
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
			// add_menu_to_list();
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
						ajaxAnimation();
					} else {
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(res.message);
						jQuery("#error-folder-popup").show();
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
			isItFromMedia = false;
            isDuplicate = false;
			addFolder();
			add_menu_to_list();
		});
	});

	jQuery(".duplicate-folder").livequery(function(){
		jQuery(this).click(function(e) {
			e.stopPropagation();
			jQuery(".active-menu").removeClass("active-menu");
			fileFolderID = jQuery(this).closest("li.route").data("folder-id");
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
			isItFromMedia = false;
            isDuplicate = true;
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
			isItFromMedia = false;
			addFolder();
			//add_menu_to_list();
		});
	});

	jQuery("#inline-update").click(function(){
		if(jQuery("#custom-menu li.active-item").length) {
			fileFolderID = jQuery("#custom-menu li.active-item").data("folder-id");
			updateFolder();
			//add_menu_to_list();
		}
	});

	jQuery("#inline-remove").click(function(){
		if(jQuery("#custom-menu li.active-item").length) {
			fileFolderID = jQuery("#custom-menu li.active-item").data("folder-id");
			removeFolderFromID(1);
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
		} else {
			if(jQuery("#folder-hide-show-checkbox").is(":checked")) {
				//removeMultipleFolderItems();
				jQuery(".dynamic-menu").remove();
				removeFolderFromID(1);
			}
		}
	});

	if(wcp_settings.can_manage_folder == "1") {
		jQuery('.space').livequery(function(){
			jQuery(this).sortable({
				placeholder: "ui-state-highlight",
				connectWith:'.space',
				tolerance:'intersect',
				over:function(event,ui){

				},
				update: function( event, ui ) {
					thisId = ui.item.context.attributes['data-folder-id'].nodeValue;
					orderString = "";
					jQuery(this).children().each(function(){
						if(jQuery(this).hasClass("route")) {
							orderString += jQuery(this).data("folder-id")+",";
						}
					});
					if(orderString != "") {
						jQuery(".form-loader-count").css("width","100%");
						jQuery.ajax({
							url: wcp_settings.ajax_url,
							data: "term_ids=" + orderString + "&action=wcp_save_folder_order&type=" + wcp_settings.post_type+"&nonce="+wcp_settings.nonce,
							method: 'post',
							success: function (res) {
								res = jQuery.parseJSON(res);
								if (res.status == '1') {
									jQuery("#wcp_folder_parent").html(res.options);
									jQuery(".form-loader-count").css("width", "0");
									add_menu_to_list();
									resetMediaAndPosts();
									ajaxAnimation();
								} else {
									jQuery(".folder-popup-form").hide();
									jQuery(".folder-popup-form").removeClass("disabled");
									jQuery("#error-folder-popup-message").html(res.message);
									jQuery("#error-folder-popup").show();
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
					if(parentId == undefined) {
						parentId = 0;
					}
					orderString = "";
					if(jQuery("#wcp_folder_"+parentId+" .ui-sortable li").length) {
						jQuery("#wcp_folder_"+parentId+" .ui-sortable li").each(function(){
							orderString += jQuery(this).data("folder-id")+",";
						});
					} else if(parentId == 0) {
						jQuery("#custom-menu > ul.space > li").each(function(){
							orderString += jQuery(this).data("folder-id")+",";
						});
					}
					jQuery(".form-loader-count").css("width","100%");
					nonce = jQuery.trim(jQuery("#wcp_folder_"+thisId).data("nonce"));
					jQuery.ajax({
						url: wcp_settings.ajax_url,
						data: "term_id=" + thisId + "&action=wcp_update_parent_information&parent_id=" + parentId+"&type=" + wcp_settings.post_type+"&nonce="+nonce,
						method: 'post',
						success: function (res) {
							jQuery(".form-loader-count").css("width","0%");
							res = jQuery.parseJSON(res);
							if(res.status == 0) {
								jQuery(".folder-popup-form").hide();
								jQuery(".folder-popup-form").removeClass("disabled");
								jQuery("#error-folder-popup-message").html(res.message);
								jQuery("#error-folder-popup").show();
							} else {
								add_menu_to_list();
								ajaxAnimation();
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
                        "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span> New Folder</a></li>" +
                        "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span> Rename</a></li>" +
                        "<li class='sticky-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>Sticky Folder (Pro)</a></li>" +
                        "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? " Remove Star" : "Add a Star") + "</a></li>" +
                        "<li class='duplicate-folder'><a href='javascript:;'><span class=''><i class='pfolder-clone'></i></span> Duplicate folder</a></li>";

            hasPosts = parseInt(jQuery(this).closest("li.route").find("h3.title:first > .total-count").text());
            if(wcp_settings.post_type == "attachment" && hasPosts) {
                menuHtml += "<li class='download-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span> Download Zip (Pro)</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span> Delete</a></li>" +
                        "</ul></div>";
			jQuery(this).closest("h3.title").after(menuHtml);
			jQuery(this).parents("li.route").addClass("active-menu");

			if((jQuery(this).closest("h3.title").offset().top + jQuery(".dynamic-menu").height()) > (jQuery(window).height() - 20)) {
				jQuery(".dynamic-menu").addClass("bottom-fix");

				if(jQuery(".dynamic-menu.bottom-fix").offset().top < jQuery("#custom-scroll-menu").offset().top) {
					jQuery(".dynamic-menu").removeClass("bottom-fix");
				}
			}
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
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(res.message);
						jQuery("#error-folder-popup").show();
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
						// add_menu_to_list();
						ajaxAnimation();
					}
				}
			});
		});
	});
	jQuery("#custom-menu .ui-icon, #custom-menu h3").livequery(function(){
		jQuery(this).click(function(){
			jQuery("#custom-menu .active-item").removeClass("active-item");
			jQuery(this).closest(".route").addClass("active-item");
			// add_menu_to_list();
		});
	});
	jQuery(document).on("keyup", "#folder-search", function(){
	   checkForFolderSearch();
    });
	jQuery(document).on("change", "#folder-search", function(){
	   checkForFolderSearch();
    });
	jQuery(document).on("blur", "#folder-search", function(){
	   checkForFolderSearch();
    });
	jQuery(".remove-folder").livequery(function(){
		jQuery(this).click(function() {
			folderID = jQuery(this).closest("li.route").data("folder-id");
			fileFolderID = folderID;
			removeFolderFromID(0);
			jQuery(".dynamic-menu").remove();
			jQuery(".active-menu").removeClass("active-menu");
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
			// add_menu_to_list();
			jQuery.ajax({
				url: wcp_settings.ajax_url,
				data: "type=" + wcp_settings.post_type + "&action=wcp_save_parent_data&is_active=" + autoStatus+"&nonce="+wcp_settings.nonce,
				method: 'post',
				success: function (res) {
					jQuery(".form-loader-count").css("width","0%");
					res = jQuery.parseJSON(res);
					if (res.status == '1') {
						jQuery(".folder-popup-form").hide();
						jQuery(".folder-popup-form").removeClass("disabled");
						jQuery("#error-folder-popup-message").html(res.message);
						jQuery("#error-folder-popup").show();
					}
				}
			});
		});
	});
	jQuery(document).on("click","#folder-hide-show-checkbox",function(){
		if(jQuery(this).is(":checked")) {
			jQuery("#custom-menu").addClass("show-folder-checkbox");
		} else {
			jQuery("#custom-menu input.checkbox").attr("checked", false);
			jQuery("#custom-menu").removeClass("show-folder-checkbox");
		}
	});
	jQuery("input.checkbox").click(function(e){
		e.stopPropagation();
		e.stopImmediatePropagation();
	});
	jQuery("input.checkbox").livequery(function(){
		jQuery(this).click(function(e){
			e.stopPropagation();
			e.stopImmediatePropagation();
		});
	});
	checkForExpandCollapse();

	//setCustomScrollForFolder();
});

jQuery(window).resize(function(){
	//setCustomScrollForFolder();
	apply_animation_height();
});

function checkForFolderSearch() {
    if(jQuery.trim(jQuery("#folder-search").val()) != "") {
        jQuery("#custom-menu").addClass("has-filter");
        var searchText = (jQuery.trim(jQuery("#folder-search").val())).toLowerCase();
        jQuery("#custom-menu span.title-text").removeClass("has-search-text");
        jQuery("li.route").removeClass("has-search");
        jQuery("#custom-menu span.title-text").each(function(){
            var thisText = (jQuery(this).text()).toLowerCase();
            if(thisText.indexOf(searchText) !== -1) {
                jQuery(this).addClass("has-search-text");
                jQuery(this).parents("li.route").addClass("has-search");
            }
        });
    } else {
        jQuery("#custom-menu").removeClass("has-filter");
        jQuery("#custom-menu span.title-text").removeClass("has-search-text");
        jQuery("li.route").removeClass("has-search");
    }
}

function setCustomScrollForFolder() {
	contentHeight = jQuery(window).height() - jQuery("#wpadminbar").height() - jQuery(".sticky-wcp-custom-form").height() - 30;
	if(jQuery("#custom-scroll-menu").hasClass("mCustomScrollbar")) {
		jQuery("#custom-scroll-menu").mCustomScrollbar('destroy');
	}

	jQuery("#custom-scroll-menu").mCustomScrollbar({
		axis:"y",
		scrollButtons:{enable:true},
		setHeight: contentHeight,
		theme:"3d",
		scrollbarPosition:"outside"
	});
}

function checkForExpandCollapse() {
	// add_menu_to_list();
	currentStatus = true;
	if((jQuery("#custom-menu .has-sub-tree").length == jQuery("#custom-menu .has-sub-tree.active").length) && jQuery("#custom-menu .has-sub-tree").length) {
		jQuery("#expand-collapse-list").addClass("all-open");
		jQuery("#expand-collapse-list").attr("data-folder-tooltip","Collapse");
	} else {
		jQuery("#expand-collapse-list").removeClass("all-open");
		jQuery("#expand-collapse-list").attr("data-folder-tooltip","Expand");
	}
}

function check_for_sub_menu() {
	jQuery("#custom-menu li.route").removeClass("has-sub-tree");
	jQuery("#custom-menu li.route").each(function(){
		if(jQuery(this).find("ul.ui-sortable li").length) {
			jQuery(this).addClass("has-sub-tree");
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


jQuery(window).on('load', function(){
	if(jQuery("#posts-filter").length) {
		jQuery("#posts-filter").wrap("<div id='folder-posts-filter'></div>");
	}
	if(!jQuery("#inlineedit").length && (wcp_settings.selected_taxonomy != "" || parseInt(wcp_settings.selected_taxonomy) == 0)) {
		jQuery("#ajax-response").before('<form method="get"><table style="display: none" id="folder-ajax-form"></table></form>');
		if(jQuery("#folder-ajax-form").length) {
			jQuery("#folder-ajax-form").load(wcp_settings.page_url+" #inlineedit", function(){});
		}
	}
});
/* code for sticky menu for media screen*/

if(wcp_settings.post_type == "attachment") {

	jQuery(window).on('load', function(){
		jQuery("button.button.media-button.select-mode-toggle-button").after("<button class='button organize-button'>Bulk Organize</button>");
		jQuery(".media-toolbar-secondary").append("<span class='media-info-message'>Drag and drop your media files to the relevant folders</span>");
		jQuery(".delete-selected-button").before("<button type='button' class='button button-primary select-all-item-btn'>Select All</button>");
        jQuery(".media-toolbar-secondary").after("<div class='custom-media-select'>Move Selected files to: <select class='media-select-folder'></select></div>");
        jQuery(".media-toolbar").append("<div style='clear:both;'></div><div class='media-folder-loader'><span>Uploading files</span> <span id='current_upload_files'></span>/<span id='total_upload_files'></span><div class='folder-progress'><div class='folder-meter orange-bg'><span></span></div></div></div>");
		if(jQuery(".wcp-custom-form").length) {
			if (wp.Uploader !== undefined) {
				wp.Uploader.queue.on('reset', function () {
					resetMediaData(1);
				});
			}
			jQuery(document).ajaxComplete(function(ev, jqXHR, settings) {
				actionName = settings.data;
				if (typeof actionName != "undefined") {
					if (actionName.length && actionName.indexOf("action=delete-post&id=") == 0) {
						resetMediaData(0);
					}
				}
			});
		}
		setTimeout(function(){
			docReferrar = document.referrer;
			if(docReferrar.indexOf("wp-admin/upload.php") != -1) {
				mediaMode = getCookie("media-select-mode");
				if (mediaMode == "on") {
					jQuery("button.button.media-button.select-mode-toggle-button").trigger("click");
					//jQuery(".attachments-browser li.attachment").draggable("enable");

					if (jQuery(".media-frame").hasClass("mode-select")) {
						jQuery(".media-info-message").addClass("active");
					} else {
						jQuery(".media-info-message, .custom-media-select").removeClass("active");
					}
				}
			} else {
				eraseCookie("media-select-mode");
			}
			resetMediaData(1);
		}, 1000);

        jQuery(document).on("click", ".attachments-browser ul.attachments .thumbnail", function(){
            if(jQuery(".media-toolbar").hasClass("media-toolbar-mode-select")) {
                if(jQuery("ul.attachments li.selected").length == 0) {
                    jQuery(".custom-media-select").removeClass("active");
                } else {
                    jQuery(".custom-media-select").addClass("active");
                }
            }
        });

        jQuery(document).on("change", ".media-select-folder", function(){
            if(jQuery(this).val() != "") {
                var checkStr = "";
                jQuery(".attachments-browser li.attachment.selected").each(function(){
                    checkStr += jQuery(this).attr("data-id")+",";
                });
                if(jQuery(this).val() == "-1") {
                    jQuery.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_id=" + checkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + jQuery(this).val() + "&nonce=" + wcp_settings.nonce +"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                        method: 'post',
                        success: function (res) {
                            resetMediaAndPosts();
                            ajaxAnimation();
                        }
                    });
                } else {
                    nonce = jQuery.trim(jQuery("#wcp_folder_" + jQuery(this).val()).data("nonce"));
                    jQuery.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_ids=" + checkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + jQuery(this).val() + "&nonce=" + nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                        method: 'post',
                        success: function (res) {
                            res = jQuery.parseJSON(res);
                            jQuery("#bulk-move-folder").hide();
                            if (res.status == "1") {
                                resetMediaAndPosts();
                                ajaxAnimation();
                            } else {
                                jQuery(".folder-popup-form").hide();
                                jQuery(".folder-popup-form").removeClass("disabled");
                                jQuery("#error-folder-popup-message").html(res.message);
                                jQuery("#error-folder-popup").show()
                            }
                        }
                    });
                }
            }
        });
    });

	function resetMediaData(loadData) {
		jQuery.ajax({
			url: wcp_settings.ajax_url,
			data: "type=" + wcp_settings.post_type + "&action=wcp_get_default_list&active_id="+activeRecordID,
			method: 'post',
			success: function (res) {
				res = jQuery.parseJSON(res);
				// jQuery("#custom-menu > ul#space_0").html(res.data);
				jQuery(".header-posts .total-count").text(res.total_items);
				jQuery(".un-categorised-items .total-count").text(res.empty_items);
				selectedVal = jQuery("#media-attachment-taxonomy-filter").val();
				if(selectedVal != "all" && loadData == 1) {
					var wp1 = parent.wp;
                    if(wp1.media != undefined) {
                        wp1.media.frame.setState('insert');
                        if (wp1.media.frame.content.get() !== null) {
                            wp1.media.frame.content.get().collection.props.set({ignore: (+new Date())});
                            wp1.media.frame.content.get().options.selection.reset();
                        } else {
                            wp1.media.frame.library.props.set({ignore: (+new Date())});
                        }
                    }
				}
				if(res.taxonomies.length) {
					if(jQuery("#media-attachment-taxonomy-filter").length) {
						folders_media_options.terms = res.taxonomies;
						var selectedDD = jQuery("#media-attachment-taxonomy-filter");
						selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
						jQuery(".media-select-folder").html("<option value=''>Select Folder</option><option value='-1'>(Unassigned)</option>");
						for (i = 0; i < res.taxonomies.length; i++) {
							selectedDD.append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].trash_count + ")</option>");
                            jQuery(".media-select-folder").append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].trash_count + ")</option>");

                            jQuery("#title_"+res.taxonomies[i].term_id).attr("title", res.taxonomies[i].term_name);
                            jQuery("#title_"+res.taxonomies[i].term_id+" .title-text").html(res.taxonomies[i].term_name);
						}
						selectedDD.val(selectedVal);
                        jQuery(".media-select-folder").val("");
					}
					if(jQuery("select.folder_for_media").length) {
						selectedVal = jQuery("select.folder_for_media").val();
						jQuery("select.folder_for_media option:not(:first-child):not(:last-child)").remove();
						for (i = 0; i < res.taxonomies.length; i++) {
							jQuery("select.folder_for_media option:last-child").before("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name +"</option>");
						}
						if(selectedVal != "") {
							jQuery(".folder_for_media").val(selectedVal);
						}
					}
					for (i = 0; i < res.taxonomies.length; i++) {
						if(!jQuery("#title_"+res.taxonomies[i].term_id+" .total-count").length) {
							jQuery("#title_"+res.taxonomies[i].term_id+" .star-icon").before("<span class='total-count'></span>");
						}
						jQuery("#title_"+res.taxonomies[i].term_id+" .total-count").text(parseInt(res.taxonomies[i].trash_count));

                        if(!jQuery(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").length) {
                            jQuery(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" a").append("<span class='folder-count'></span>")
                        }
                        jQuery(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").text(parseInt(res.taxonomies[i].trash_count));

                        jQuery("#title_"+res.taxonomies[i].term_id).attr("title", res.taxonomies[i].term_name);
                        jQuery("#title_"+res.taxonomies[i].term_id+" .title-text").html(res.taxonomies[i].term_name);
					}

					jQuery("#custom-menu .total-count").each(function(){
						if(parseInt(jQuery(this).text()) == 0) {
							jQuery(this).remove();
						}
					});

                    jQuery(".sticky-folders .folder-count").each(function(){
                        if(parseInt(jQuery(this).text()) == 0) {
                            jQuery(this).remove();
                        }
                    });
				}
				if(activeRecordID != "") {
					jQuery("#wcp_folder_"+activeRecordID).addClass("active-item");
				}

				if(isItFromMedia) {
					jQuery("#title_"+fileFolderID).trigger("click");
					isItFromMedia = false;
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
			jQuery(".select-all-item-btn").addClass("active");
		} else {
			jQuery(".media-info-message, .custom-media-select").removeClass("active");
			jQuery(".select-all-item-btn").removeClass("active");
		}
	});

	jQuery(document).on("click", ".select-mode-toggle-button", function(){
		setTimeout(function() {
			if(!jQuery(".media-frame").hasClass("mode-select")) {
				setCookie("media-select-mode", "off", -1);
			}
			if(jQuery(".media-frame").hasClass("mode-select")) {
				jQuery(".media-info-message").addClass("active");
				jQuery(".select-all-item-btn").addClass("active");
			} else {
				jQuery(".media-info-message, .custom-media-select").removeClass("active");
				jQuery(".select-all-item-btn").removeClass("active");
			}
		}, 10);
	});

	jQuery(document).on("click", ".select-all-item-btn", function(){
		jQuery("ul.attachments li:not(.selected)").trigger("click");
	});

	jQuery(document).on("change", ".folder_for_media", function(){
		if(jQuery(this).val() == "add-folder") {
			fileFolderID = 0;
			isItFromMedia = true;
			addFolder();
			// jQuery(".add-new-folder").trigger("click");
			//jQuery(this).val("-1");
		}
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