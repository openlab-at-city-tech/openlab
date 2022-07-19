var folders_media = {
    addMedia: function () {
        jQuery("body").hasClass("media-new-php") && setTimeout(function () {
            "undefined" != typeof uploader && jQuery(".folder_for_media").length && uploader && uploader.bind("BeforeUpload", function (e, d) {
                e.settings.multipart_params.folder_for_media = jQuery(".folder_for_media").val(), jQuery("#media-item-" + d.id).find(".filename")
            })
        }.bind(this), 500)
    }
};
jQuery(document).ready(function () {
    folders_media.addMedia()
});
var selectedFolderMediaId;
jQuery(document).on("change", ".folder_for_media", function(){
    if(jQuery(this).val() != "add-folder" && jQuery(this).val() != null) {
        selectedFolderMediaId = jQuery(this).val();
    } else if(jQuery(this).val() == "add-folder") {
        selectedFolderMediaId = -1;
        if(!jQuery("#wcp-content").length) {
            if(folders_media_options.is_key_active == 0 && folders_media_options.folders >= 10) {
                if (jQuery("#custom-folder-media-popup-form").length) {
                    jQuery("#custom-folder-media-popup-form").remove();
                }
                if (!jQuery("#custom-folder-media-popup-form").length) {
                    jQuery("body").append("<div class='folder-media-popup-form' id='custom-folder-media-popup-form'></div>");
                    jQuery("#custom-folder-media-popup-form").append("<div class='media-popup-form' id='custom-popup-form-content'></div>");
                    jQuery(".media-popup-form").append('<div id="add-update-folder-title" class="add-update-folder-title">You\'ve reached the 10 folder limitation!</div>');
                    jQuery(".media-popup-form").append('<div class="folder-form-message">Unlock unlimited amount of folders by upgrading to one of our pro plans.</div>');
                    jQuery(".media-popup-form").append('<div class="folder-form-buttons"><a href="javascript:;" class="remove-media-form">Cancel</a><a href="'+folders_media_options.activate_url+'" target="_blank" class="form-submit-btn" style="width: 120px">See Pro Plans</button></div>');
                }
            } else {
                if (jQuery("#custom-folder-media-popup-form").length) {
                    jQuery("#custom-folder-media-popup-form").remove();
                }
                if (!jQuery("#custom-folder-media-popup-form").length) {
                    jQuery("body").append("<div class='folder-media-popup-form' id='custom-folder-media-popup-form'></div>");
                    jQuery("#custom-folder-media-popup-form").append("<div class='media-popup-form' id='custom-popup-form-content'></div>");
                    jQuery("#custom-popup-form-content").append("<form action='#' id='folder-media-popup-form' method='post'></form>");
                    jQuery("#folder-media-popup-form").append('<div id="add-update-folder-title" class="add-update-folder-title">Add new folder</div>');
                    jQuery("#folder-media-popup-form").append('<div class="folder-form-input"><div class="folder-group"><input id="media-folder-name" autocomplete="off" required="required" class=""><span class="highlight"></span><span class="folder-bar"></span><label for="media-folder-name">Folder name</label></div></div>');
                    jQuery("#folder-media-popup-form").append('<div class="folder-form-errors" id="media-form-error"><span class="dashicons dashicons-info"></span> Please enter folder name</div>');
                    jQuery("#folder-media-popup-form").append('<div class="folder-form-buttons"><a href="javascript:;" class="remove-media-form">Cancel</a><button type="submit" class="form-submit-btn" id="save-media-folder" style="width: 106px">Submit</button></div>');
                    jQuery("#media-folder-name").focus();
                }
            }
        }
    }

    if(jQuery(".media-toolbar #media-attachment-taxonomy-filter").length) {
        jQuery("#media-attachment-taxonomy-filter").val(jQuery(this).val());
        jQuery("#media-attachment-taxonomy-filter").trigger("change");
    }
});

jQuery(document).on("submit", "#folder-media-popup-form", function(){
    if(jQuery.trim(jQuery("#media-folder-name").val()) == "") {
        jQuery("#media-form-error").show();
        jQuery("#media-folder-name").focus();
    } else {
        jQuery("button#save-media-folder").html("<span class='spinner-border'></span>");
        jQuery("button#save-media-folder").attr("disabled", true);
        var orderNumber = -1;
        jQuery(".folder_for_media option").each(function(){
            thisText = jQuery(this).text();
            if(jQuery.trim(thisText[0]) != "") {
                orderNumber++;
            }
        });
        jQuery.ajax({
            url: folders_media_options.ajax_url,
            data: "action=wcp_add_new_folder&parent_id=0&is_from_media=1&type=attachment&term_id=0&order="+orderNumber+"&name="+jQuery.trim(jQuery("#media-folder-name").val())+"&nonce=" +folders_media_options.nonce,
            type: 'post',
            success: function (res) {
                result = jQuery.parseJSON(res);
                if (result.status == '1') {
                    resetMediaID = result.id;
                    folders_media_options.is_key_active = result.is_key_active;
                    folders_media_options.folders = result.folders;
                    //resetSelectMediaDropDown();
                    jQuery(".folder_for_media option:last").before("<option value='"+result.id+"'>"+jQuery.trim(jQuery("#media-folder-name").val())+"</option>");
                    jQuery(".folder_for_media").val(result.id).trigger("change");
                    jQuery(".folder-media-popup-form").remove();
                } else {
                    jQuery(".folder-form-errors").html(result.message).show();
                    jQuery("button#save-media-folder").attr("disabled", false);
                    jQuery("button#save-media-folder").html("Submit");
                }
            }
        });
    }
    return false;
});

jQuery(document).on("click", ".remove-media-form", function(e){
    e.stopPropagation();
    jQuery("#custom-folder-media-popup-form").remove();
    jQuery(".folder_for_media").val("-1").trigger("change");
    jQuery("#media-attachment-taxonomy-filter").each(function(){
        jQuery(this).val("all").trigger("change");
    });
});
jQuery(document).on("click", "#custom-folder-media-popup-form", function (e) {
    jQuery("#custom-folder-media-popup-form").remove();
    jQuery(".folder_for_media").val("-1").trigger("change");
    jQuery("#media-attachment-taxonomy-filter").each(function(){
        jQuery(this).val("all").trigger("change");
    });
});
jQuery(document).on("click", ".media-popup-form", function (e) {
    e.stopPropagation();
});