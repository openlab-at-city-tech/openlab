(function (factory) {
    "use strict";
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else if(typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'));
    }
    else {
        factory(jQuery);
    }
}(function ($, undefined) {
    var treeId = "#js-tree-menu";
    var nonce;
    var activeRecordID = "";
    var isItFromMedia = false;
    var isDuplicate = false;
    var isKeyActive;
    var n_o_file;
    var duplicateFolderId = 0;
    var fileFolderID = 0;
    var folderOrder = 0;
    var isMultipleRemove = false;
    var folderIDs = "";
    var folderPropertyArray = [];
    var folderCurrentURL = wcp_settings.page_url;
    var foldersArray = [];
    var hasStars;
    var hasChildren;
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
    var contextOffsetX = null;
    var contextOffsetY = null;
    var currentPage = 1;
    $(document).ready(function(){

        foldersArray = wcp_settings.taxonomies;

        isKeyActive = parseInt(wcp_settings.is_key_active);
        n_o_file = parseInt(wcp_settings.folders);
        activeRecordID = parseInt(wcp_settings.selected_taxonomy);
        hasStars = parseInt(wcp_settings.hasStars);
        hasChildren = parseInt(wcp_settings.hasChildren);
        currentPage = parseInt(wcp_settings.currentPage);

        folderPropertyArray = wcp_settings.folder_settings;

        initJSTree();

        setCustomScrollForFolder();

        var resizeDirection = (wcp_settings.isRTL == "1" || wcp_settings.isRTL == 1)?"w":"e";
        $(".wcp-content").resizable( {
            resizeHeight:   false,
            handles:        resizeDirection,
            minWidth:       100,
            maxWidth: 		500,
            resize: function( e, ui ) {
                var menuWidth = ui.size.width;
                if(menuWidth <= 275) {
                    $(".plugin-button").addClass("d-block");
                } else {
                    $(".plugin-button").removeClass("d-block");
                }
                if(menuWidth <= 225) {
                    menuWidth = 225;
                }
                if(wcp_settings.isRTL == "1") {
                    $("#wpcontent").css("padding-right", (menuWidth + 20) + "px");
                    $("#wpcontent").css("padding-left", "0px");
                } else {
                    $("#wpcontent").css("padding-left", (menuWidth + 20) + "px");
                }
                newWidth = menuWidth - 40;
                cssString = "";
                classString = "";
                for(i=0; i<=15; i++) {
                    classString += " .space > .jstree-node >";
                    currentWidth = newWidth - (13+(20*i));
                    cssString += "#js-tree-menu > "+classString+" .title { width: "+currentWidth+"px !important; } ";
                    cssString += "#js-tree-menu > "+classString+" .dynamic-menu { left: "+(currentWidth - 190)+"px !important; } ";
                    setStickyHeaderForMedia();
                }
                $("#wcp-custom-style").html("<style>"+cssString+"</style>");
                if(ui.size.width <= 185) {
                    folderStatus = "hide";
                    $(".wcp-hide-show-buttons .toggle-buttons.show-folders").addClass("active");
                    $(".wcp-hide-show-buttons .toggle-buttons.hide-folders").removeClass("active");
                    $("#wcp-content").addClass("hide-folders-area");
                    if(wcp_settings.isRTL == "1") {
                        $("#wpcontent").css("padding-right", "20px");
                        $("#wpcontent").css("padding-left", "0px");
                    } else {
                        $("#wpcontent").css("padding-left", "20px");
                    }
                } else {
                    if($("#wcp-content").hasClass("hide-folders-area")) {
                        folderStatus = "show";
                        $(".wcp-hide-show-buttons .toggle-buttons.show-folders").removeClass("active");
                        $(".wcp-hide-show-buttons .toggle-buttons.hide-folders").addClass("active");
                        $("#wcp-content").addClass("no-transition");
                        $("#wcp-content").removeClass("hide-folders-area");
                        if (wcp_settings.isRTL == "1") {
                            $("#wpcontent").css("padding-right", (parseInt(wcp_settings.folder_width) + 20) + "px");
                            $("#wpcontent").css("padding-left", "0px");
                        } else {
                            $("#wpcontent").css("padding-left", (parseInt(wcp_settings.folder_width) + 20) + "px");
                        }
                        setTimeout(function(){
                            $("#wcp-content").removeClass("no-transition");
                        }, 250);
                    }
                }
            },
            stop: function( e, ui ) {
                var menuWidth = ui.size.width;
                if(ui.size.width <= 275) {
                    $(".plugin-button").addClass("d-block");
                } else {
                    $(".plugin-button").removeClass("d-block");
                }
                if(menuWidth <= 225) {
                    menuWidth = 225;
                }
                if(ui.size.width <= 185) {
                    folderStatus = "hide";
                    $(".wcp-hide-show-buttons .toggle-buttons.show-folders").addClass("active");
                    $(".wcp-hide-show-buttons .toggle-buttons.hide-folders").removeClass("active");
                    $("#wcp-content").addClass("hide-folders-area");
                    if(wcp_settings.isRTL == "1") {
                        $("#wpcontent").css("padding-right", "20px");
                        $("#wpcontent").css("padding-left", "0px");
                    } else {
                        $("#wpcontent").css("padding-left", "20px");
                    }

                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: "type=" + wcp_settings.post_type + "&action=wcp_change_folder_display_status&status=" + folderStatus +"&nonce="+nonce,
                        method: 'post',
                        success: function (res) {
                            setStickyHeaderForMedia();
                        }
                    });
                } else {
                    if($("#wcp-content").hasClass("hide-folders-area")) {
                        folderStatus = "show";
                        $(".wcp-hide-show-buttons .toggle-buttons.show-folders").removeClass("active");
                        $(".wcp-hide-show-buttons .toggle-buttons.hide-folders").addClass("active");
                        $("#wcp-content").addClass("no-transition");
                        $("#wcp-content").removeClass("hide-folders-area");
                        if (wcp_settings.isRTL == "1") {
                            $("#wpcontent").css("padding-right", (parseInt(wcp_settings.folder_width) + 20) + "px");
                            $("#wpcontent").css("padding-left", "0px");
                        } else {
                            $("#wpcontent").css("padding-left", (parseInt(wcp_settings.folder_width) + 20) + "px");
                        }
                        setTimeout(function(){
                            $("#wcp-content").removeClass("no-transition");
                        }, 250);
                    }
                }
                nonce = wcp_settings.nonce;
                wcp_settings.folder_width = ui.size.width;
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: "type=" + wcp_settings.post_type + "&action=wcp_change_post_width&width=" + menuWidth+"&nonce="+nonce,
                    method: 'post',
                    success: function (res) {
                        setStickyHeaderForMedia();
                    }
                });
                if(ui.size.width <= 225) {
                    $(".wcp-content").width(225);
                    wcp_settings.folder_width = 225;
                }
            }
        });

        $(document).on("contextmenu", ".jstree-anchor", function(e){
            contextOffsetX = e.pageX;
            contextOffsetY = e.pageY;
            $(this).find("span.folder-inline-edit").trigger("click");
            return false;
        });

        $(document).on("click", ".folder-actions span.folder-inline-edit", function(e){
            e.stopImmediatePropagation()
            e.stopPropagation();
            if(wcp_settings.can_manage_folder == 0) {
                return;
            }
            isHigh = $(this).closest("li.jstree-node").hasClass("is-high");
            isSticky = $(this).closest("li.jstree-node").hasClass("is-sticky");
            isStickyClass = (isSticky)?true:false;
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            menuHtml = "<div class='dynamic-menu' data-id='"+$(this).closest("li").prop("id")+"'><ul>";
                if(hasChildren) {
                    menuHtml += "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>New Folder</a></li>";
                } else {
                    menuHtml += "<li class='new-folder-pro'><a target='_blank' href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>New Sub-folder (Pro)</a></li>";
                }
                menuHtml += "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span>Rename</a></li>" +
                            "<li class='sticky-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>Sticky Folder (Pro)</a></li>";
                if(hasStars) {
                    menuHtml += "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? "Remove Star" : "Add a Star") + "</a></li>";
                } else {
                    menuHtml += "<li class='mark-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? "Remove Star (Pro)" : "Add a Star (Pro)") + "</a></li>";
                }
                menuHtml += "<li class='lock-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='dashicons dashicons-lock'></span>Lock Folder (Pro)</a></li>" +
                            "<li class='duplicate-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-clone'></i></span>Duplicate folder (Pro)</a></li>";

            hasPosts = parseInt($(this).closest("a.jstree-anchor").find(".premio-folder-count").text());
            if (wcp_settings.post_type == "attachment" && hasPosts) {
                menuHtml += "<li target='_blank' class='download-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span>Download Zip (Pro)</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span>Delete</a></li>" +
                "</ul></div>";
            $("body").append(menuHtml);
            var yPosition;
            if(e.pageX == undefined || e.pageY == undefined) {
                $(".dynamic-menu").css("left", (contextOffsetX));
                $(".dynamic-menu").css("top", (contextOffsetY - 10));
                yPosition = contextOffsetY;
            } else {
                $(".dynamic-menu").css("left", (e.pageX));
                $(".dynamic-menu").css("top", (e.pageY));
                yPosition = e.pageY;
            }

            $(this).parents("li.jstree-node").addClass("active-menu");
            // if(($(this).offset().top + $(".dynamic-menu").height()) > ($(window).height() - 20)) {
            //     $(".dynamic-menu").addClass("bottom-fix");
            //
            //     if($(".dynamic-menu.bottom-fix").offset().top < $("#custom-scroll-menu").offset().top) {
            //         $(".dynamic-menu").removeClass("bottom-fix");
            //     }
            // }

            if((yPosition + $(".dynamic-menu").height()) > $(window).height()) {
                $(".dynamic-menu").css("margin-top", $(window).height() - (yPosition + $(".dynamic-menu").height()));
            }
        });

        $(document).on("click", ".sticky-folders .sticky-fldr > a", function(e) {
            e.stopPropagation();
            var folder_ID = $(this).closest("li").data("folder-id");
            if($(".jstree-node[id='"+folder_ID+"']").length) {
                $(".jstree-clicked").removeClass("jstree-clicked");
                $(".active-item").removeClass("active-item");
                $("#js-tree-menu").jstree('select_node', activeRecordID);
                $(".jstree-node[id='"+folder_ID+"'] > a.jstree-anchor").trigger("click");
                $(".jstree-node[id='"+folder_ID+"'] > a.jstree-anchor").addClass("jstree-clicked");
                $(".sticky-folders .sticky-folder-"+folder_ID+" a").addClass("active-item");
            }
        });

        $(document).on("contextmenu", ".sticky-folders li .sticky-fldr >  a", function(e){
            $(this).find("span.update-inline-record").trigger("click");
            return false;
        });

        $(document).on("click", ".tree-structure a", function(e) {
            e.stopPropagation();
            var folder_ID = $(this).data("id");
            if($(".jstree-node[id='"+folder_ID+"']").length) {
                $(".jstree-clicked").removeClass("jstree-clicked");
                $(".active-item").removeClass("active-item");
                $("#js-tree-menu").jstree('select_node', activeRecordID);
                $(".jstree-node[id='"+folder_ID+"'] > a.jstree-anchor").trigger("click");
                $(".jstree-node[id='"+folder_ID+"'] > a.jstree-anchor").addClass("jstree-clicked");
                $(".sticky-folders .sticky-folder-"+folder_ID+" a").addClass("active-item");
            }
        });

        $(document).on("click", ".update-inline-record", function(e){
            e.stopImmediatePropagation()
            e.stopPropagation();
            if(wcp_settings.can_manage_folder == 0) {
                return;
            }
            isHigh = $(this).closest("li.sticky-fldr").hasClass("is-high");
            isSticky = $(this).closest("li.sticky-fldr").hasClass("is-sticky");
            isStickyClass = (isSticky)?true:false;
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            menuHtml = "<div class='dynamic-menu' data-id='"+$(this).closest("li").data("folder-id")+"'><ul>";
            if(hasChildren) {
                menuHtml += "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>New Folder</a></li>";
            } else {
                menuHtml += "<li class='new-folder-pro'><a target='_blank' href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>New Sub-folder (Pro)</a></li>";
            }
            menuHtml += "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span>Rename</a></li>" +
                        "<li class='sticky-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>Sticky Folder (Pro)</a></li>";
            if(hasStars) {
                menuHtml += "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? "Remove Star" : "Add a Star") + "</a></li>";
            } else {
                menuHtml += "<li class='mark-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? "Remove Star (Pro)" : "Add a Star (Pro)") + "</a></li>";
            }
            menuHtml += "<li class='lock-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='dashicons dashicons-lock'></span>Lock Folder (Pro)</a></li>" +
                        "<li class='duplicate-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-clone'></i></span>Duplicate folder (Pro)</a></li>";

            hasPosts = parseInt($(this).closest("li.jstree-node").find("h3.title:first > .total-count").text());
            if (wcp_settings.post_type == "attachment" && hasPosts) {
                menuHtml += "<li class='download-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span>Download Zip (Pro)</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span> Delete</a></li>" +
                "</ul></div>";
            $("body").append(menuHtml);

            var yPosition;
            if(e.pageX == undefined || e.pageY == undefined) {
                $(".dynamic-menu").css("left", (contextOffsetX));
                $(".dynamic-menu").css("top", (contextOffsetY - 10));
                yPosition = contextOffsetY;
            } else {
                $(".dynamic-menu").css("left", (e.pageX));
                $(".dynamic-menu").css("top", (e.pageY));
                yPosition = e.pageY;
            }

            if((yPosition + $(".dynamic-menu").height()) > $(window).height()) {
                $(".dynamic-menu").css("margin-top", $(window).height() - (yPosition + $(".dynamic-menu").height()));
            }
        });

        $(document).on("click", ".dynamic-menu", function(e){
            e.stopImmediatePropagation()
            e.stopPropagation();
        });

        $(document).on("click", ".new-folder-pro", function(e){
            e.preventDefault();
            $(".dynamic-menu").remove();
            $("#sub-folder-popup").show();
        });

        $(document).on("click", ".close-popup-button a", function(){
            $(".folder-popup-form").hide();
        });

        $(document).on("click", "body, html", function(){
            $(".dynamic-menu").remove();
        });

        $(".wcp-hide-show-buttons .toggle-buttons").click(function(){
            var folderStatus = "show";
            if($(this).hasClass("hide-folders")) {
                folderStatus = "hide";
            }
            $(".wcp-hide-show-buttons .toggle-buttons").toggleClass("active");
            nonce = wcp_settings.nonce;
            if(folderStatus == "show") {
                $("#wcp-content").addClass("no-transition");
                $("#wcp-content").removeClass("hide-folders-area");
                if(wcp_settings.isRTL == "1") {
                    $("#wpcontent").css("padding-right", (parseInt(wcp_settings.folder_width) + 20) + "px");
                    $("#wpcontent").css("padding-left", "0px");
                } else {
                    $("#wpcontent").css("padding-left", (parseInt(wcp_settings.folder_width) + 20) + "px");
                }
                setTimeout(function(){
                    $("#wcp-content").removeClass("no-transition");
                }, 250);
            } else {
                $("#wcp-content").addClass("hide-folders-area");
                if(wcp_settings.isRTL == "1") {
                    $("#wpcontent").css("padding-right", "20px");
                    $("#wpcontent").css("padding-left", "0px");
                } else {
                    $("#wpcontent").css("padding-left", "20px");
                }
            }

            $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=wcp_change_folder_display_status&status=" + folderStatus +"&nonce="+nonce,
                method: 'post',
                success: function (res) {
                    setStickyHeaderForMedia();
                }
            });
        });

        /* grag and drop */
        setDragAndDropElements();

        $( document ).ajaxComplete(function( event, xhr, settings ) {
            if(settings.data != undefined && settings.data != "" && settings.data.indexOf("action=query-attachments") != -1) {
                setDragAndDropElements();
            }
        });
    });

    function setDragAndDropElements() {
        $(".wcp-move-file:not(.ui-draggable)").draggable({
            revert: "invalid",
            containment: "document",
            helper: "clone",
            cursor: "move",
            start: function( event, ui){
                $(this).closest("td").addClass("wcp-draggable");
                $("body").addClass("no-hover-css");
            },
            stop: function( event, ui ) {
                $(this).closest("td").removeClass("wcp-draggable");
                $("body").removeClass("no-hover-css");
            }
        });

        $(".wcp-move-multiple:not(.ui-draggable)").draggable({
            revert: "invalid",
            containment: "document",
            helper: function (event, ui) {
                $(".selected-items").remove();
                selectedItems = $("#the-list th input:checked").length;
                if(selectedItems > 0) {
                    selectedItems = (selectedItems == 0 || selectedItems == 1) ? "1 Item" : selectedItems + " Items";
                    return $("<div class='selected-items'><span class='total-post-count'>" + selectedItems + " Selected</span></div>");
                } else {
                    return  $("<div class='selected-items'><span class='total-post-count'>Select Items to move</span></div>");
                }
            },
            start: function( event, ui){
                $("body").addClass("no-hover-css");
            },
            cursor: "move",
            cursorAt: {
                left: 0,
                top: 0
            },
            stop: function( event, ui ) {
                $(".selected-items").remove();
                $("body").removeClass("no-hover-css");
            }
        });

        $(".jstree-anchor:not(.ui-droppable)").droppable({
            accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
            hoverClass: 'wcp-drop-hover',
            classes: {
                "ui-droppable-active": "ui-state-highlight"
            },
            drop: function( event, ui ) {
                folderID = $(this).closest("li.jstree-node").attr('id');
                if ( ui.draggable.hasClass( 'wcp-move-multiple')) {
                    if($(".wp-list-table input:checked").length) {
                        chkStr = "";
                        $(".wp-list-table input:checked").each(function(){
                            chkStr += $(this).val()+",";
                        });
                        nonce = getSettingForPost(folderID, 'nonce');
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                            method: 'post',
                            success: function (res) {
                                res = $.parseJSON(res);
                                if(res.status == "1") {
                                    resetMediaAndPosts();
                                } else {
                                    $(".folder-popup-form").hide();
                                    $(".folder-popup-form").removeClass("disabled");
                                    $("#error-folder-popup-message").html(res.message);
                                    $("#error-folder-popup").show()
                                }
                            }
                        });
                    }
                } else if( ui.draggable.hasClass( 'wcp-move-file' ) ){
                    postID = ui.draggable[0].attributes['data-id'].nodeValue;
                    nonce = getSettingForPost(folderID, 'nonce');
                    chkStr = postID+",";
                    $(".wp-list-table input:checked").each(function(){
                        if($(this).val() != postID) {
                            chkStr += $(this).val() + ",";
                        }
                    });
                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                        method: 'post',
                        success: function (res) {
                            res = $.parseJSON(res);
                            if(res.status == "1") {
                                // window.location.reload();
                                resetMediaAndPosts();
                            } else {
                                $(".folder-popup-form").hide();
                                $(".folder-popup-form").removeClass("disabled");
                                $("#error-folder-popup-message").html(res.message);
                                $("#error-folder-popup").show()
                            }
                        }
                    });
                } else if (ui.draggable.hasClass('attachment')) {
                    chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                    nonce = getSettingForPost(folderID, 'nonce');
                    if ($(".attachments-browser li.attachment.selected").length > 1) {
                        chkStr = "";
                        $(".attachments-browser li.attachment.selected").each(function(){
                            chkStr += $(this).data("id") + ",";
                        });
                    }
                    folderIDs = chkStr;
                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                        method: 'post',
                        success: function (res) {
                            // window.location.reload();
                            resetMediaAndPosts();
                        }
                    });
                }
            }
        });

        $(".un-categorised-items:not(.ui-droppable)").droppable({
            accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
            hoverClass: 'wcp-hover-list',
            classes: {
                "ui-droppable-active": "ui-state-highlight"
            },
            drop: function (event, ui) {
                folderID = -1;
                nonce = wcp_settings.nonce;
                if (ui.draggable.hasClass('wcp-move-multiple')) {
                    if ($(".wp-list-table input:checked").length) {
                        chkStr = "";
                        $(".wp-list-table input:checked").each(function(){
                            chkStr += $(this).val() + ",";
                        });
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                            method: 'post',
                            success: function (res) {
                                //window.location.reload();
                                resetMediaAndPosts();
                            }
                        });
                    }
                } else if (ui.draggable.hasClass('wcp-move-file')) {
                    postID = ui.draggable[0].attributes['data-id'].nodeValue;
                    chkStr = postID+",";
                    $(".wp-list-table input:checked").each(function(){
                        if(postID != $(this).val()) {
                            chkStr += $(this).val() + ",";
                        }
                    });
                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                        method: 'post',
                        success: function (res) {
                            //window.location.reload();
                            resetMediaAndPosts();
                        }
                    });
                } else if (ui.draggable.hasClass('attachment')) {
                    chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                    if ($(".attachments-browser li.attachment.selected").length > 1) {
                        chkStr = "";
                        $(".attachments-browser li.attachment.selected").each(function(){
                            chkStr += $(this).data("id") + ",";
                        });
                    }
                    folderIDs = chkStr;
                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                        method: 'post',
                        success: function (res) {
                            // window.location.reload();
                            resetMediaAndPosts();
                        }
                    });
                }
            }
        });

        $(".attachments-browser li.attachment:not(.ui-draggable)").draggable({
            revert: "invalid",
            containment: "document",
            helper: function (event, ui) {
                $(".selected-items").remove();
                selectedItems = $(".attachments-browser li.attachment.selected").length;
                selectedItems = (selectedItems == 0 || selectedItems == 1) ? "1 Item" : selectedItems + " Items";
                return $("<div class='selected-items'><span class='total-post-count'>" + selectedItems + " Selected</span></div>");
            },
            start: function( event, ui){
                $("body").addClass("no-hover-css");
            },
            cursor: "move",
            cursorAt: {
                left: 0,
                top: 0
            },
            stop: function( event, ui ) {
                $(".selected-items").remove();
                $("body").removeClass("no-hover-css");
            }
        });

        $(".tree-structure .folder-item:not(.ui-droppable)").droppable({
            accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
            hoverClass: 'wcp-drop-hover-list',
            classes: {
                "ui-droppable-active": "ui-state-highlight"
            },
            drop: function( event, ui ) {
                $("body").removeClass("no-hover-css");
                folderID = $(this).data('id');
                if ( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
                    nonce = getSettingForPost(folderID, 'nonce');
                    if($(".wp-list-table input:checked").length) {
                        chkStr = "";
                        $(".wp-list-table input:checked").each(function(){
                            chkStr += $(this).val()+",";
                        });
                        $.ajax({
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
                    nonce = getSettingForPost(folderID, 'nonce');
                    chkStr = postID+",";
                    $(".wp-list-table input:checked").each(function(){
                        if($(this).val() != postID) {
                            chkStr += $(this).val() + ",";
                        }
                    });
                    $.ajax({
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
                    nonce = getSettingForPost(folderID, 'nonce');
                    if($(".attachments-browser li.attachment.selected").length > 1) {
                        chkStr = "";
                        $(".attachments-browser li.attachment.selected").each(function(){
                            chkStr += $(this).data("id")+",";
                        });
                    }
                    $.ajax({
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

        $(".sticky-folders li a:not(.ui-droppable)").droppable({
            accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
            hoverClass: 'wcp-drop-hover',
            classes: {
                "ui-droppable-active": "ui-state-highlight"
            },
            drop: function( event, ui ) {
                folderID = $(this).closest("li").data('folder-id');
                if ( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
                    if($(".wp-list-table input:checked").length) {
                        chkStr = "";
                        $(".wp-list-table input:checked").each(function(){
                            chkStr += $(this).val()+",";
                        });
                        nonce = getSettingForPost(folderID, 'nonce');
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                            method: 'post',
                            success: function (res) {
                                res = $.parseJSON(res);
                                if(res.status == "1") {
                                    resetMediaAndPosts();
                                    ajaxAnimation();
                                } else {
                                    $(".folder-popup-form").hide();
                                    $(".folder-popup-form").removeClass("disabled");
                                    $("#error-folder-popup-message").html(res.message);
                                    $("#error-folder-popup").show()
                                }
                            }
                        });
                    }
                } else if( ui.draggable.hasClass( 'wcp-move-file' ) ){
                    postID = ui.draggable[0].attributes['data-id'].nodeValue;
                    nonce = getSettingForPost(folderID, 'nonce');
                    chkStr = postID+",";
                    $(".wp-list-table input:checked").each(function(){
                        if($(this).val() != postID) {
                            chkStr += $(this).val() + ",";
                        }
                    });
                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+wcp_settings.taxonomy_status+"&taxonomy="+activeRecordID,
                        method: 'post',
                        success: function (res) {
                            res = $.parseJSON(res);
                            if(res.status == "1") {
                                // window.location.reload();
                                resetMediaAndPosts();
                                ajaxAnimation();
                            } else {
                                $(".folder-popup-form").hide();
                                $(".folder-popup-form").removeClass("disabled");
                                $("#error-folder-popup-message").html(res.message);
                                $("#error-folder-popup").show()
                            }
                        }
                    });
                } else if (ui.draggable.hasClass('attachment')) {
                    chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                    nonce = getSettingForPost(folderID, 'nonce');
                    if ($(".attachments-browser li.attachment.selected").length > 1) {
                        chkStr = "";
                        $(".attachments-browser li.attachment.selected").each(function () {
                            chkStr += $(this).data("id") + ",";
                        });
                    }
                    folderIDs = chkStr;
                    $.ajax({
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

        setFolderCountAndDD();
    }

    function setFolderCount() {
        $("#js-tree-menu .jstree-node").each(function(){
            var folderCount = parseInt($(this).data("count"));
            if(folderCount > 0) {
                $(".jstree-node[id='" + $(this).attr("id") + "'] > a span.premio-folder-count").text(folderCount);
            }
        });

        if(activeRecordID != "" && activeRecordID != 0) {
            if($(".jstree-node[id='"+activeRecordID+"']").length) {
                $("#js-tree-menu").jstree('select_node', activeRecordID);
                if($(".sticky-folders .sticky-folder-"+activeRecordID+" a").length) {
                    $(".sticky-folders .sticky-folder-" + activeRecordID + " a").addClass("active-item");
                }
            }
        }
        $(".ajax-preloader").hide();
        $(".js-tree-data").show();
        setCustomScrollForFolder();
        make_sticky_folder_menu();
        if($(".sticky-folders ul > li").length > 0) {
            $(".sticky-folders").addClass("active");
        }
        add_active_item_to_list();
    }

    function getSettingForPost(postId, filedName) {
        if(folderPropertyArray.length > 0) {
            for(i=0; i<folderPropertyArray.length; i++) {
                if(parseInt(folderPropertyArray[i]['folder_id']) == parseInt(postId)) {
                    return folderPropertyArray[i][filedName];
                }
            }
        }
        return "";
    }

    function getIndexForPostSetting(postId) {
        if(folderPropertyArray.length > 0) {
            for(i=0; i<folderPropertyArray.length; i++) {
                if(parseInt(folderPropertyArray[i]['folder_id']) == parseInt(postId)) {
                    return i;
                }
            }
        }
        return null;
    }

    function resetMediaAndPosts() {
        if($(".media-toolbar").hasClass("media-toolbar-mode-select")) {
            if($("ul.attachments li.selected").length) {
                $("ul.attachments li.selected").trigger("click");
                $(".select-mode-toggle-button").trigger("click");
            }
        }
        if(folderIDs != "" && ($("#js-tree-menu a.jstree-clicked").length > 0 || activeRecordID == "-1")) {
            if($("#media-attachment-taxonomy-filter").length) {
                folderIDs = folderIDs.split(",");
                for (var i = 0; i < folderIDs.length; i++) {
                    if(folderIDs[i] != "") {
                        $(".attachments-browser li[data-id='"+folderIDs[i]+"']").remove();
                    }
                }
            }
            folderIDs = "";
        }
        if($("#media-attachment-taxonomy-filter").length) {
            resetMediaData(0);
        } else {
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=get_folders_default_list",
                method: 'post',
                success: function (res) {
                    res = $.parseJSON(res);
                    // $("#js-tree-menu > ul#space_0").html(res.data);
                    $(".header-posts .total-count").text(res.total_items);
                    $(".un-categorised-items .total-count").text(res.empty_items);

                    foldersArray = res.taxonomies;
                    setFolderCountAndDD();
                }
            });
            $(".folder-loader-ajax").addClass("active");
            if($("#folder-posts-filter").length) {
                $("#folder-posts-filter").load(folderCurrentURL+"&paged="+currentPage + " #posts-filter", function(){
                    var obj = { Title: "", Url: folderCurrentURL+"&paged="+currentPage };
                    history.pushState(obj, obj.Title, obj.Url);
                    if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                        $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                    }
                    add_active_item_to_list();
                    triggerInlineUpdate();
                });
            } else {
                $("#wpbody").load(folderCurrentURL+"&paged="+currentPage + " #wpbody-content", false, function (res) {
                    var obj = { Title: "", Url: folderCurrentURL+"&paged="+currentPage };
                    history.pushState(obj, obj.Title, obj.Url);
                    if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                        $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                    }
                    add_active_item_to_list();
                });
            }
        }
    }

    function triggerInlineUpdate() {
        add_active_item_to_list();

        $(".form-loader-count").css("width", "0");
        if(typeof inlineEditPost == "object") {

            inlineEditPost.init();

            $("#the-list").on("click",".editinline",function(){
                $(this).attr("aria-expanded","true");
                inlineEditPost.edit(this);
            });
            $(document).on("click", ".inline-edit-save .save", function(){
                var thisID = $(this).closest("tr").attr("id");
                thisID = thisID.replace("edit-","");
                thisID = thisID.replace("post-","");
                inlineEditPost.save(thisID);
            });
            $(document).on("click", ".inline-edit-save .cancel", function(){
                var thisID = $(this).closest("tr").attr("id");
                thisID = thisID.replace("edit-","");
                thisID = thisID.replace("post-","");
                inlineEditPost.revert(thisID);
            });
        }

        if(wcp_settings.post_type == "attachment") {
            if(!$(".move-to-folder-top").length) {
                $("#bulk-action-selector-top").append("<option class='move-to-folder-top' value='move_to_folder'>Move to Folder</option>");
            }
            if(!$(".move-to-folder-bottom").length) {
                $("#bulk-action-selector-bottom").append("<option class='move-to-folder-bottom' value='move_to_folder'>Move to Folder</option>");
            }
        }
    }

    function add_active_item_to_list() {

        if(folderPropertyArray.length) {
            $("li.jstree-node").each(function(){
                folderPostId = getIndexForPostSetting($(this).attr("id"));
                if(folderPostId != null) {
                    if(folderPropertyArray[folderPostId]['is_high'] == 1) {
                        $(this).addClass("is-high");
                    } else {
                        $(this).removeClass("is-high");
                    }
                    if(folderPropertyArray[folderPostId]['is_sticky'] == 1) {
                        $(this).addClass("is-sticky");
                    } else {
                        $(this).removeClass("is-sticky");
                    }
                }
            });
        }

        folderId = 0;
        $(".tree-structure ul").html("");
        folderStatus = true;
        if($(".jstree-clicked").length) {
            folderID = $(".jstree-clicked").closest(".jstree-node").attr("id");
            if($(".jstree-node[id='"+folderID+"'] > ul.jstree-children > li.jstree-node").length) {
                folderStatus = false;
                $(".jstree-node[id='"+folderID+"'] > ul.jstree-children > li.jstree-node").each(function(){
                    fID = $(this).attr("id");
                    fName = $.trim($("#js-tree-menu").jstree(true).get_node(fID).text);
                    liHtml = listFolderString.replace(/__folder_id__/g,fID);
                    liHtml = liHtml.replace(/__folder_name__/g,fName);
                    selectedClass = $(this).hasClass("is-high")?"is-high":"";
                    liHtml = liHtml.replace(/__append_class__/g,selectedClass);
                    $(".tree-structure ul").append(liHtml);
                });
            } else {
                if(!$(".jstree-node[id='"+folderID+"']").closest("ul").hasClass("jstree-container-ul")) {
                    folderStatus = false;
                }
            }
        }
        if(folderStatus){
            $("#js-tree-menu > ul > li.jstree-node").each(function(){
                fID = $(this).attr("id");
                fName = $.trim($("#js-tree-menu").jstree(true).get_node(fID).text);
                liHtml = listFolderString.replace(/__folder_id__/g,fID);
                liHtml = liHtml.replace(/__folder_name__/g,fName);
                selectedClass = $(this).hasClass("is-high")?"is-high":"";
                liHtml = liHtml.replace(/__append_class__/g,selectedClass);
                $(".tree-structure ul").append(liHtml);
            });
        }


        apply_animation_height();

        if(wcp_settings.post_type == "attachment") {
            if(!$(".move-to-folder-top").length) {
                $("#bulk-action-selector-top").append("<option class='move-to-folder-top' value='move_to_folder'>Move to Folder</option>");
            }
            if(!$(".move-to-folder-bottom").length) {
                $("#bulk-action-selector-bottom").append("<option class='move-to-folder-bottom' value='move_to_folder'>Move to Folder</option>");
            }
        }

        $(".sticky-folders .active-item").removeClass("active-item");
        if($("#js-tree-menu li.jstree-node.active-item").length) {
            var activeTermId = $("#js-tree-menu li.jstree-node.active-item").data("folder-id");
            $(".sticky-folders .sticky-folder-"+activeTermId+" a").addClass("active-item");
        }

        setDragAndDropElements();
    }

    $(window).on("resize", function(){
        setCustomScrollForFolder();
        setStickyHeaderForMedia();
    });

    $(window).scroll(function(){
        setStickyHeaderForMedia()
    });

    function setCustomScrollForFolder() {
        contentHeight = $(window).height() - $("#wpadminbar").height() - $(".sticky-wcp-custom-form").height() - 30;
        var scrollTop = 0;
        if($("#custom-scroll-menu").hasClass("mCustomScrollbar")) {
            var $scrollerOuter  = $( '#custom-scroll-menu' );
            var $dragger        = $scrollerOuter.find( '.mCSB_dragger' );
            var scrollHeight    = $scrollerOuter.find( '.mCSB_container' ).height();
            var draggerTop      = $dragger.position().top;

            scrollTop = draggerTop / ($scrollerOuter.height() - $dragger.height()) * (scrollHeight - $scrollerOuter.height());
            $("#custom-scroll-menu").mCustomScrollbar('destroy');

        }
        $("#custom-scroll-menu").mCustomScrollbar({
            axis:"y",
            scrollButtons:{enable:false},
            setHeight: contentHeight,
            theme:"3d",
            scrollbarPosition:"inside",
            scrollInertia: 500,
            mouseWheelPixels: 60
        });
        if(scrollTop != 0) {
            $("#custom-scroll-menu").mCustomScrollbar("scrollTo", scrollTop+"px",{scrollInertia:0});
        }
    }

    /* add folder code */
    $(document).ready(function(){
        $(document).on("click", "#add-new-folder", function(){
            if($("#js-tree-menu a.jstree-clicked").length) {
                fileFolderID = $("#js-tree-menu a.jstree-clicked").closest("li.jstree-node").attr("id");
                if(!hasChildren) {
                    $("#pro-notice").removeClass("hide-it");
                }
            } else {
                fileFolderID = 0;
                $("#pro-notice").addClass("hide-it");
            }
            isItFromMedia = false;
            addFolder();
        });

        $(document).on("click", ".new-folder", function(){
            fileFolderID = $(this).closest(".dynamic-menu").data("id");
            isItFromMedia = false;
            addFolder();
        });

        $(document).on("click", ".duplicate-folder", function(e){
            e.stopPropagation();
            fileFolderID = $(this).closest(".dynamic-menu").data("id");
            $(".dynamic-menu").remove();
            isItFromMedia = false;
            isDuplicate = true;
            addFolder();
            add_menu_to_list();
        });

        $(document).on("submit", "#save-folder-form", function(e){
            e.stopPropagation();
            e.preventDefault();

            folderNameDynamic = $.trim($("#add-update-folder-name").val());

            if($.trim(folderNameDynamic) == "") {
                $(".folder-form-errors").addClass("active");
                $("#add-update-folder-name").focus();
            } else {
                $("#save-folder-data").html('<span class="dashicons dashicons-update"></span>');
                $("#add-update-folder").addClass("disabled");

                var parentId = fileFolderID;
                if(isItFromMedia) {
                    parentId = 0;
                }

                if(!hasChildren) {
                    parentId = 0;
                }

                if(parentId == 0) {
                    folderOrder = $("#js-tree-menu > ul > li.jstree-node").length;
                } else {
                    folderOrder = $("#js-tree-menu > ul > li.jstree-node[id='"+parentId+"'] > ul.jstree-children > li").length + 1;
                }

                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: {
                        parent_id: parentId,
                        type: wcp_settings.post_type,
                        action: "wcp_add_new_folder",
                        nonce: wcp_settings.nonce,
                        term_id: parentId,
                        order: folderOrder,
                        name: folderNameDynamic,
                        is_duplicate: isDuplicate,
                        duplicate_from: duplicateFolderId
                    },
                    method: 'post',
                    success: function (res) {
                        result = $.parseJSON(res);
                        $(".folder-popup-form").hide();
                        $(".folder-popup-form").removeClass("disabled");
                        if (result.status == -1) {
                            $("#no-more-folder-credit").show();
                        } else if (result.status == '1') {
                            isKeyActive = parseInt(result.is_key_active);
                            n_o_file = parseInt(result.folders);
                            $("#current-folder").text(n_o_file);
                            $("#ttl-fldr").text((4*4)-(2*2)-2);
                            checkForExpandCollapse();
                            add_menu_to_list();
                            if(result.data.length) {
                                for(var i=0; i<result.data.length; i++) {
                                    var folderProperty = {
                                        'folder_id': result.data[i].term_id,
                                        'folder_count': 0,
                                        'is_sticky': result.data[i]['is_sticky'],
                                        'is_high': result.data[i]['is_high'],
                                        'nonce': result.data[i]['nonce'],
                                        'slug': result.data[i]['slug'],
                                        'is_deleted': 0
                                    };
                                    folderPropertyArray.push(folderProperty);
                                    $('#js-tree-menu').jstree().create_node(result.parent_id, {
                                        "id": result.data[i]['term_id'],
                                        "text": " " + result.data[i]['title']
                                    }, "last", function () {
                                        $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-nonce", result.data[i]['nonce']);
                                        $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-slug", result.data[i]['slug']);
                                    });
                                }
                            }
                            ajaxAnimation();
                            make_sticky_folder_menu();
                            if($("#media-attachment-taxonomy-filter").length) {
                                fileFolderID = result.term_id;
                                resetMediaData(0);
                            }
                        } else {
                            $("#error-folder-popup-message").html(result.message);
                            $("#error-folder-popup").show();
                        }
                    }
                });
            }
            return false;
        });
    });

    function add_menu_to_list() {
        add_active_item_to_list();
    }

    function addFolder() {
        if(isKeyActive == 0 && n_o_file >= ((4*4)-(3*3)+(4/4)+(8/(2*2)))) {
            $("#folder-limitation-message").html("You've "+"reached the "+((4*4)-(2*2)-2)+" folder limitation!");
            $("#no-more-folder-credit").show();
            return false;
        }

        $("#add-update-folder-title").text("Add a new folder");
        $("#save-folder-data").text("Submit");
        $(".folder-form-errors").removeClass("active");
        $("#add-update-folder-name").val("");
        if(isDuplicate) {
            duplicateFolderId = fileFolderID;
            $("#add-update-folder-name").val($.trim($("#js-tree-menu").jstree(true).get_node(fileFolderID).text)+ " #2");
            if($("#"+fileFolderID+"_anchor").closest(".jstree-node").parent().parent().hasClass("jstree-node")) {
                fileFolderID = $("#"+fileFolderID+"_anchor").closest(".jstree-node").parent().parent().attr("id");
            } else {
                fileFolderID = 0;
            }
        }

        $("#add-update-folder").removeClass("disabled");
        $("#add-update-folder").show();
        $("#add-update-folder-name").focus();
        $(".dynamic-menu").remove();
    }

    /* update folder code */
    $(document).ready(function(){
        $("#inline-update").click(function(){
            if($("#js-tree-menu a.jstree-clicked").length) {
                fileFolderID = $("#js-tree-menu a.jstree-clicked").closest("li.jstree-node").attr("id");
                updateFolder();
                //add_menu_to_list();
            }
        });

        $(document).on("click", ".rename-folder", function(e){
            e.stopPropagation();
            fileFolderID = $(this).closest(".dynamic-menu").data("id");
            updateFolder();
            $(".dynamic-menu").remove();
        });


        $(document).on("click", ".form-cancel-btn", function(){
            $(".folder-popup-form").hide();
        });

        $(document).on("click", ".folder-popup-form", function (e) {
            $(".folder-popup-form").hide();
        });

        $(document).on("click", ".popup-form-content", function (e) {
            e.stopPropagation();
        });

        $(document).on("submit", "#update-folder-form", function(e){
            e.stopPropagation();
            e.preventDefault();

            folderNameDynamic = $("#update-folder-item-name").val();

            if($.trim(folderNameDynamic) == "") {
                $(".folder-form-errors").addClass("active");
                $("#update-folder-item-name").focus();
            } else {
                $("#update-folder-data").html('<span class="dashicons dashicons-update"></span>');
                $("#update-folder-item").addClass("disabled");

                nonce = getSettingForPost(fileFolderID, 'nonce');
                parentID = $(".jstree-node[id='"+fileFolderID+"']").closest("li.jstree-node").attr("id");
                if (parentID == undefined) {
                    parentID = 0;
                }
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: {
                        parent_id: parentID,
                        type: wcp_settings.post_type,
                        action: "wcp_update_folder",
                        nonce: nonce,
                        term_id: fileFolderID,
                        order: folderOrder,
                        name: folderNameDynamic
                    },
                    method: 'post',
                    success: function (res) {
                        result = $.parseJSON(res);
                        if (result.status == '1') {
                            $("#js-tree-menu").jstree('rename_node', result.id , " "+result.term_title);
                            folderPostId = getIndexForPostSetting(result.id);
                            if(folderPostId != null) {
                                folderPropertyArray[folderPostId]['nonce'] = result.nonce;
                                folderPropertyArray[folderPostId]['slug'] = result.slug;
                            }
                            add_menu_to_list();
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            ajaxAnimation();
                            if($("#media-attachment-taxonomy-filter").length) {
                                resetMediaData(0)
                            }
                        } else {
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            $("#error-folder-popup-message").html(result.message);
                            $("#error-folder-popup").show();
                        }
                    }
                });
            }
            return false;
        });
    });

    function updateFolder() {
        folderName = $.trim($("#js-tree-menu").jstree(true).get_node(fileFolderID).text);
        parentID = $("#wcp_folder_"+fileFolderID).closest("li.jstree-node").data("folder-id");
        if(parentID == undefined) {
            parentID = 0;
        }

        $("#update-folder-data").text("Submit");
        $(".folder-form-errors").removeClass("active");
        $("#update-folder-item-name").val(folderName);
        $("#update-folder-item").removeClass("disabled");
        $("#update-folder-item").show();
        $("#update-folder-item-name").focus();
        $(".dynamic-menu").remove();
    }

    /* Remove Folders */
    $(document).ready(function(){
        $("#inline-remove").click(function(){
            if($("#js-tree-menu a.jstree-clicked").length) {
                fileFolderID = $("#js-tree-menu a.jstree-clicked").closest("li.jstree-node").attr("id");
                removeFolderFromID(1);
                $(".dynamic-menu").remove();
                $(".active-menu").removeClass("active-menu");
            } else {
                if($("#folder-hide-show-checkbox").is(":checked")) {
                    $(".dynamic-menu").remove();
                    removeFolderFromID(1);
                }
            }
        });

        $(document).on("click","#folder-hide-show-checkbox",function(){
            if($(this).is(":checked")) {
                $("#js-tree-menu").addClass("show-folder-checkbox");
            } else {
                $("#js-tree-menu input.checkbox").attr("checked", false);
                $("#js-tree-menu").removeClass("show-folder-checkbox");
            }
        });

        $(document).on("click", ".folder-checkbox, .input-checkbox", function(e){
            e.stopImmediatePropagation();
            e.stopPropagation();
        });

        $(document).on("click", ".remove-folder", function(){
            folderID = $(this).closest("li.jstree-node").data("id");
            fileFolderID = $(this).closest(".dynamic-menu").data("id");
            removeFolderFromID(0);
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
        });

        $(document).on("click", "#remove-folder-item", function (e){
            e.stopPropagation();
            $(".folder-popup-form").addClass("disabled");
            $("#remove-folder-item").html('<span class="dashicons dashicons-update"></span>');
            nonce = getSettingForPost(fileFolderID, 'nonce');
            if(isMultipleRemove) {
                removeMultipleFolderItems();
            } else {
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: "type=" + wcp_settings.post_type + "&action=wcp_remove_folder&term_id=" + fileFolderID + "&nonce=" + nonce,
                    method: 'post',
                    success: function (res) {
                        res = $.parseJSON(res);
                        if (res.status == '1') {
                            $('#js-tree-menu').jstree().delete_node(fileFolderID);
                            isKeyActive = parseInt(res.is_key_active);
                            n_o_file = parseInt(res.folders);
                            $("#current-folder").text(n_o_file);
                            $("#ttl-fldr").text((3*3)+(4/(2*2)));
                            $(".sticky-folders .sticky-folder-"+fileFolderID).remove();
                            add_menu_to_list();
                            ajaxAnimation();
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            resetMediaAndPosts();
                            make_sticky_folder_menu();
                            if (activeRecordID == fileFolderID) {
                                $(".header-posts").trigger("click");
                            }
                        } else {
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            $("#error-folder-popup-message").html(res.message);
                            $("#error-folder-popup").show();
                        }
                    }
                });
            }
        });
    });

    function check_for_sub_menu() {
        $("#js-tree-menu li.jstree-node").removeClass("has-sub-tree");
        $("#js-tree-menu li.jstree-node").each(function(){
            if($(this).find("ul.ui-sortable li").length) {
                $(this).addClass("has-sub-tree");
            } else {
                $(this).removeClass("active");
            }
        });
    }

    function removeMultipleFolderItems() {
        if($("#folder-hide-show-checkbox").is(":checked")) {
            if($("#js-tree-menu input.checkbox:checked").length > 0) {
                var folderIDs = "";
                var activeItemDeleted = false;
                $("#js-tree-menu input.checkbox:checked").each(function(){
                    folderIDs += $(this).closest("li.jstree-node").attr("id")+",";
                    if($(this).closest("li.jstree-node").hasClass("jstree-clicked")) {
                        activeItemDeleted = true;
                    }
                });
                $(".form-loader-count").css("width", "100%");
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: "type=" + wcp_settings.post_type + "&action=wcp_remove_muliple_folder&term_id=" + folderIDs+"&nonce="+wcp_settings.nonce,
                    method: 'post',
                    success: function (res) {
                        res = $.parseJSON(res);
                        $(".form-loader-count").css("width", "0px");
                        if (res.status == '1') {
                            isKeyActive = parseInt(res.is_key_active);
                            n_o_file = parseInt(res.folders);
                            $("#current-folder").text(n_o_file);
                            for(i=0; i<res.term_ids.length; i++) {
                                $('#js-tree-menu').jstree().delete_node(res.term_ids[i]);
                            }

                            $("#ttl-fldr").text((4*2)+(4/2));
                            // add_menu_to_list();
                            ajaxAnimation();
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            resetMediaAndPosts();
                            make_sticky_folder_menu();

                            ajaxAnimation();

                            check_for_sub_menu();

                            if(!$("#wcp_folder_"+activeRecordID).length) {
                                $(".header-posts a").trigger("click");
                                activeRecordID = 0;
                            }
                        } else {
                            window.location.reload();
                        }
                        $("#folder-hide-show-checkbox").attr("checked", false);
                        $("#js-tree-menu input.checkbox").attr("checked", false);
                        $("#js-tree-menu").removeClass("show-folder-checkbox");
                    }
                });
            } else {

            }
        }
    }

    function removeFolderFromID(popup_type) {
        var removeMessage = "Are you sure you want to delete the selected folder?";
        var removeNotice = "Items in the folder will not be deleted.";
        isMultipleRemove = false;
        if(popup_type == 1) {
            if($("#folder-hide-show-checkbox").is(":checked")) {
                isMultipleRemove = true;
                if($("#js-tree-menu input.checkbox:checked").length ==	 0) {
                    $(".folder-popup-form").hide();
                    $(".folder-popup-form").removeClass("disabled");
                    $("#error-folder-popup-message").html("Please select at least one folder to delete");
                    $("#error-folder-popup").show();
                    return;
                } else {
                    if($("#js-tree-menu input.checkbox:checked").length > 1) {
                        removeMessage = "Are you sure you want to delete the selected folders?";
                        removeNotice = "Items in the selected folders will not be deleted.";
                    }
                }
            }
        }
        $(".folder-popup-form").hide();
        $(".folder-popup-form").removeClass("disabled");
        $("#remove-folder-item").text("Yes, Delete it!");
        $("#remove-folder-message").text(removeMessage);
        $("#remove-folder-notice").text(removeNotice);
        $("#confirm-remove-folder").show();
        $("#remove-folder-item").focus();
    }

    $(document).ready(function(){
        $(document).on("click", ".mark-folder", function(e){
            e.stopPropagation();
            folderID = $(this).closest(".dynamic-menu").data("id");
            nonce = getSettingForPost(folderID, 'nonce');
            $(".form-loader-count").css("width","100%");
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "term_id=" + folderID + "&type=" + wcp_settings.post_type + "&action=wcp_mark_un_mark_folder&nonce="+nonce,
                method: 'post',
                cache: false,
                success: function (res) {
                    res = $.parseJSON(res);
                    $(".form-loader-count").css("width","0%");
                    if (res.status == '1') {
                        folderPostId = getIndexForPostSetting(res.id);
                        if(res.marked == '1') {
                            $("li.jstree-node[id='"+res.id+"']").addClass("is-high");
                            $(".sticky-folder-"+res.id).addClass("is-high");
                            if(folderPostId != null) {
                                folderPropertyArray[folderPostId]['is_high'] = 1;
                            }
                        } else {
                            $("li.jstree-node[id='"+res.id+"']").removeClass("is-high");
                            $(".sticky-folder-"+res.id).removeClass("is-high");
                            if(folderPostId != null) {
                                folderPropertyArray[folderPostId]['is_high'] = 0;
                            }
                        }
                        add_menu_to_list();
                        ajaxAnimation();
                    } else {
                        $(".folder-popup-form").hide();
                        $(".folder-popup-form").removeClass("disabled");
                        $("#error-folder-popup-message").html(res.message);
                        $("#error-folder-popup").show();
                    }
                }
            });
        });
    });

    /* change folder status */
    $(document).ready(function(){
        $(document).on("click", ".jstree-node .jstree-icon", function(){
            folderID = $(this).closest("li.jstree-node").attr("id");
            if($("li.jstree-node[id='"+folderID+"']").hasClass("jstree-open")) {
                folderStatus = 1;
            } else {
                folderStatus = 0;
            }
            $(".form-loader-count").css("width","100%");
            nonce = getSettingForPost(folderID, 'nonce');
            checkForExpandCollapse();
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "is_active=" + folderStatus + "&action=save_wcp_folder_state&term_id=" + folderID+"&nonce="+nonce,
                method: 'post',
                success: function (res) {
                    $(".form-loader-count").css("width","0");
                    res = $.parseJSON(res);
                    if(res.status == "0") {
                        // $(".folder-popup-form").hide();
                        // $(".folder-popup-form").removeClass("disabled");
                        // $("#error-folder-popup-message").html(res.message);
                        // $("#error-folder-popup").show();
                    } else {
                        if($("#wcp_folder_"+folderID).hasClass("active")) {
                            $("#wcp_folder_"+folderID).removeClass("active");
                            $("#wcp_folder_"+folderID).find("ul.ui-sortable:first-child > li").slideUp();
                            folderStatus = 0;
                        } else {
                            $("#wcp_folder_"+folderID).addClass("active");
                            $("#wcp_folder_"+folderID).find("ul.ui-sortable:first-child > li").slideDown();
                            folderStatus = 1;
                        }
                        ajaxAnimation();
                    }
                }
            });
        });
    });

    /* refresh listing on click */
    $(document).ready(function(){
        $(document).on("click", "a.jstree-anchor", function(e) {
            currentPage = 1;
            e.stopPropagation();
            $(".un-categorised-items").removeClass("active-item");
            $(".header-posts a").removeClass("active-item");
            $("active-item").removeClass("active-item");
            activeRecordID = $(this).closest("li.jstree-node").attr("id");
            fileFolderID = $(this).closest("li.jstree-node").attr("id");
            $(".sticky-folders .sticky-folder-"+activeRecordID+" a").addClass("active-item");
            if(!$("#media-attachment-taxonomy-filter").length) {
                var folderSlug = getSettingForPost(activeRecordID, 'slug');
                folderCurrentURL = wcp_settings.page_url + folderSlug+"&paged="+currentPage;
                $(".form-loader-count").css("width", "100%");
                if($("#folder-posts-filter").length) {
                    $("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function(){
                        var obj = { Title: folderSlug, Url: folderCurrentURL };
                        history.pushState(obj, obj.Title, obj.Url);
                        set_default_folders(folderSlug);
                        if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                            $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                        }
                        triggerInlineUpdate();
                    });
                } else {
                    $("#wpbody").load(folderCurrentURL + " #wpbody-content", function(){
                        var obj = { Title: folderSlug, Url: folderCurrentURL };
                        history.pushState(obj, obj.Title, obj.Url);
                        set_default_folders(folderSlug);
                        if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                            $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                        }
                        triggerInlineUpdate();
                    });
                }
            } else {
                var thisIndex = $(this).closest("li.jstree-node").attr("id");
                $("#media-attachment-taxonomy-filter").val(thisIndex);
                $("#media-attachment-taxonomy-filter").trigger("change");
                thisSlug = getSettingForPost(thisIndex, 'slug');
                folderCurrentURL = wcp_settings.page_url + thisSlug+"&paged="+currentPage;
                var obj = { Title: thisSlug, Url: folderCurrentURL };
                history.pushState(obj, obj.Title, obj.Url);
                set_default_folders(thisSlug);
                $(".custom-media-select").removeClass("active");
            }
            add_active_item_to_list();
            $(".sticky-folders .sticky-folder-"+activeRecordID+" a").addClass("active-item");
        });

        $(".header-posts").click(function(){
            activeRecordID = "";
            $(".wcp-container .route").removeClass("active-item");
            $(".un-categorised-items").removeClass("active-item");
            $(".sticky-folders .active-item").removeClass("active-item");
            $(".header-posts a").addClass("active-item");
            $(".jstree-clicked").removeClass("jstree-clicked");
            if(!$("#media-attachment-taxonomy-filter").length) {
                currentPage = 1;
                folderCurrentURL = wcp_settings.page_url+"&paged="+currentPage;
                $(".form-loader-count").css("width", "100%");
                if($("#folder-posts-filter").length) {
                    $("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function(){
                        var obj = { Title: "", Url: folderCurrentURL };
                        history.pushState(obj, obj.Title, obj.Url);
                        set_default_folders("all");
                        if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                            $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                        }
                        add_active_item_to_list();
                        triggerInlineUpdate();
                    });
                } else {
                    $("#wpbody").load(folderCurrentURL + " #wpbody-content", function(){
                        var obj = { Title: "", Url: folderCurrentURL };
                        history.pushState(obj, obj.Title, obj.Url);
                        set_default_folders("all");
                        if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                            $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                        }
                        add_active_item_to_list();
                        triggerInlineUpdate();
                    });
                }
            } else {
                activeRecordID = "";
                $("#media-attachment-taxonomy-filter").val("all");
                $("#media-attachment-taxonomy-filter").trigger("change");
                var obj = { Title: "", Url: wcp_settings.page_url };
                history.pushState(obj, obj.Title, obj.Url);
                set_default_folders("all");
                add_active_item_to_list();
            }
        });

        $(".un-categorised-items").click(function(){
            activeRecordID = "-1";
            $(".wcp-container .route").removeClass("active-item");
            $(".header-posts a").removeClass("active-item");
            $(".un-categorised-items").addClass("active-item");
            $(".sticky-folders .active-item").removeClass("active-item");
            $(".jstree-clicked").removeClass("jstree-clicked");
            if(!$("#media-attachment-taxonomy-filter").length) {
                currentPage = 1;
                folderCurrentURL = wcp_settings.page_url+"-1"+"&paged="+currentPage;
                $(".form-loader-count").css("width", "100%");
                if($("#folder-posts-filter").length) {
                    $("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function(){
                        var obj = { Title: "", Url: folderCurrentURL };
                        history.pushState(obj, obj.Title, obj.Url);
                        set_default_folders("-1");
                        if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                            $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                        }
                        add_active_item_to_list();
                        triggerInlineUpdate();
                    });
                } else {
                    $("#wpbody").load(folderCurrentURL + " #wpbody-content", function(){
                        var obj = { Title: "", Url: folderCurrentURL };
                        history.pushState(obj, obj.Title, obj.Url);
                        set_default_folders("-1");
                        if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                            $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                        }
                        add_active_item_to_list();
                        triggerInlineUpdate();
                    });
                }
            } else {
                $("#media-attachment-taxonomy-filter").val("unassigned");
                $("#media-attachment-taxonomy-filter").trigger("change");
                var obj = { Title: "", Url: wcp_settings.page_url+"-1" };
                history.pushState(obj, obj.Title, obj.Url);
                set_default_folders("-1");
                add_active_item_to_list();
            }
        });

        /* Expand/Collapse */
        $("#expand-collapse-list").click(function(e){
            e.stopPropagation();
            statusType = 0;
            if($(this).hasClass("all-open")) {
                $(this).removeClass("all-open");
                statusType = 0;
                $(this).attr("data-folder-tooltip","Expand");
                $("#js-tree-menu").jstree("close_all");
            } else {
                $(this).addClass("all-open");
                statusType = 1;
                $(this).attr("data-folder-tooltip","Collapse");
                $("#js-tree-menu").jstree("open_all");
            }
            folderIDs = "";
            $("#js-tree-menu .jstree-node:not(.jstree-leaf)").each(function(){
                folderIDs += $(this).attr("id")+",";
            });
            if(folderIDs != "") {
                $(".form-loader-count").css("width","100%");
                nonce = wcp_settings.nonce;
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: "type=" + wcp_settings.post_type + "&action=wcp_change_all_status&status=" + statusType + "&folders="+folderIDs+"&nonce="+nonce,
                    method: 'post',
                    success: function (res) {
                        $(".form-loader-count").css("width","0");
                        res = $.parseJSON(res);
                        if(res.status == "0") {
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            $("#error-folder-popup-message").html(res.message);
                            $("#error-folder-popup").show();
                            window.location.reload(true);
                        }
                    }
                });
            }
        });

        checkForExpandCollapse();
    });

    function initJSTree() {
        $(treeId).jstree({
            "core": {
                'cache':false,
                "animation": 0,
                "max_depth": hasChildren?"-1":1,
                // "check_callback": true,
                check_callback: function(e, t, n, r, o) {
                    $("*").removeClass("drag-bot").removeClass("drag-in").removeClass("drag-up");
                    if (("move_node" === e || "copy_node" === e) && o && o.dnd)
                        switch (o.pos) {
                            case "a":
                                o.origin.get_node(o.ref, !0).addClass("drag-bot");
                                nodeId = $(".drag-bot").attr("id");
                                $("#jstree-dnd").text("Below "+$.trim($("#js-tree-menu").jstree(true).get_node(nodeId).text));
                                break;
                            case "i":
                                if(!hasChildren) {
                                    return false;
                                }
                                o.origin.get_node(o.ref, !0).addClass("drag-in");
                                nodeId = $(".drag-in").attr("id");
                                $("#jstree-dnd").text("Inside "+$.trim($("#js-tree-menu").jstree(true).get_node(nodeId).text));
                                break;
                            case "b":
                                o.origin.get_node(o.ref, !0).addClass("drag-up");
                                nodeId = $(".drag-up").attr("id");
                                $("#jstree-dnd").text("Above "+$.trim($("#js-tree-menu").jstree(true).get_node(nodeId).text));
                                break;
                            default:
                                $("#jstree-dnd").text($("#jstree-dnd").data("txt"));
                                break;
                        }
                    return !0
                }
            },
            data: {
                cache : false
            },
            select_node: false,
            search: {
                show_only_matches: true,
                case_sensitive: false,
                fuzzy: false
            },
            plugins: ["dnd", "search", "contextmenu"],
            contextmenu: {
                select_node: 0,
                show_at_node: 0,
                items: function() {
                    return {};
                }
            }
        }).bind("ready.jstree", (function() {
            setFolderCount();
            setDragAndDropElements();
        })).bind("after_open.jstree", (function() {
            //data.text is the new name:
            setDragAndDropElements();
        })).bind("open_all.jstree", (function() {
            //data.text is the new name:
            setDragAndDropElements();
        })).bind("create_node.jstree", (function() {
            //data.text is the new name:
            setDragAndDropElements();
        })).bind("delete_node.jstree", (function() {
            //data.text is the new name:
            setDragAndDropElements();
        })).bind("close_all.jstree", (function() {
            //data.text is the new name:
            setDragAndDropElements();
        })).bind("after_close.jstree", (function() {
            //data.text is the new name:
            setDragAndDropElements();
        })).bind("move_node.jstree", (function(t, n) {
            if(n.node.parent != "#") {
                jQuery("#js-tree-menu").jstree("open_node",n.node.parent);
            }
            folderMoveId = n.node.id;
            orderString = "";
            $("#js-tree-menu .jstree-node[id='"+folderMoveId+"']").closest("ul").children().each(function(){
                if($(this).attr("id") != 'undefined') {
                    orderString += $(this).attr("id") + ",";
                }
            });
            if($("#"+folderMoveId+"_anchor").closest(".jstree-node").parent().parent().hasClass("jstree-node")) {
                parentID = $("#"+folderMoveId+"_anchor").closest(".jstree-node").parent().parent().attr("id");
            } else {
                parentID = 0;
            }
            if(orderString != "") {
                $(".form-loader-count").css("width","100%");
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: "term_ids=" + orderString + "&action=wcp_save_folder_order&type=" + wcp_settings.post_type+"&nonce="+wcp_settings.nonce+"&term_id="+folderMoveId+"&parent_id="+parentID,
                    method: 'post',
                    success: function (res) {
                        res = $.parseJSON(res);
                        if (res.status == '1') {
                            $("#wcp_folder_parent").html(res.options);
                            $(".form-loader-count").css("width", "0");
                            resetMediaAndPosts();
                            ajaxAnimation();
                            setFolderCountAndDD();
                            setDragAndDropElements();
                        } else {
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            $("#error-folder-popup-message").html(res.message);
                            $("#error-folder-popup").show();
                            window.location.reload(true);
                        }
                    }
                });
            }
        }));
    }
    
    /* sorting folders */
    $(document).ready(function(){
        $(document).on("click", "body, html", function(){
            $(".folder-order").removeClass("active");
        });

        $(document).on("click", "#sort-order-list", function(e){
            e.stopPropagation();
            $(".folder-order").toggleClass("active");
        });

        $(document).on("click", ".folder-sort-menu a:not(.pro-feature)", function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(".form-loader-count").css("width", "100%");
            $(".folder-order").removeClass("active");
            lastOrderStatus = $(this).attr("data-sort");
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=wcp_folders_by_order&nonce=" + wcp_settings.nonce+"&order="+$(this).attr("data-sort"),
                method: 'post',
                success: function (res) {
                    res = $.parseJSON(res);
                    if(res.status == 1) {
                        $("#js-tree-menu").jstree().destroy();
                        $("#js-tree-menu").append("<ul></ul>");
                        $("#js-tree-menu ul").html(res.data);
                        initJSTree();
                        foldersArray = res.terms;
                        setFolderCountAndDD();
                    }
                    $(".form-loader-count").css("width", "0");
                    add_active_item_to_list();
                }
            });
        });
    });

    /* Search functionality */
    $(document).ready(function(){
        $(document).on("keyup", "#folder-search", function(){
            checkForFolderSearch();
        });

        $(document).on("change", "#folder-search", function(){
            checkForFolderSearch();
        });

        $(document).on("blur", "#folder-search", function(){
            checkForFolderSearch();
        });
    });

    function checkForFolderSearch() {
        var searchVal = $.trim($("#folder-search").val());
        $('#js-tree-menu').jstree('search', searchVal);
    }

    /* checkbox library */
    $(document).ready(function(){
        $(document).on("click", ".folders-toggle-button", function(){
            dbStatus = 'show';
            if($(".tree-structure-content").hasClass("active")) {
                $(".tree-structure-content .tree-structure").animate({
                    height: '40px'
                }, 100, function(){
                    $(".tree-structure-content").removeClass("active");
                });
                dbStatus = 'hide';
            } else {
                newHeight = parseInt($(".tree-structure-content .tree-structure").attr("data-height"));
                $(".tree-structure-content .tree-structure").animate({
                    height: newHeight
                }, 100, function(){
                    $(".tree-structure-content").addClass("active");
                });
            }
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=wcp_hide_folders&status=" + dbStatus +"&nonce="+wcp_settings.nonce,
                method: 'post',
                success: function (res) {
                    setStickyHeaderForMedia();
                }
            });
        });
    });

    function set_default_folders(post_id) {
        $.ajax({
            url: wcp_settings.ajax_url,
            type: 'post',
            data: 'action=save_folder_last_status&post_type='+wcp_settings.post_type+"&post_id="+post_id+"&nonce="+wcp_settings.nonce,
            cache: false,
            async: false,
            success: function(){

            }
        })
    }

    /* Extra functions */
    function checkForExpandCollapse() {
        setTimeout(function(){
            currentStatus = true;
            if($("#js-tree-menu .jstree-node.jstree-leaf").length == $("#js-tree-menu .jstree-node").length) {
                $("#expand-collapse-list").removeClass("all-open");
                $("#expand-collapse-list").attr("data-folder-tooltip","Expand");
            } else {
                var totalChild = $("#js-tree-menu .jstree-node.jstree-closed").length + $("#js-tree-menu .jstree-node.jstree-open").length;
                if($("#js-tree-menu .jstree-node.jstree-closed").length == totalChild) {
                    $("#expand-collapse-list").removeClass("all-open");
                    $("#expand-collapse-list").attr("data-folder-tooltip","Expand");
                } else {
                    $("#expand-collapse-list").addClass("all-open");
                    $("#expand-collapse-list").attr("data-folder-tooltip","Collapse");
                }
            }
        }, 500);

        setDragAndDropElements();
    }

    function apply_animation_height() {
        if($(".tree-structure-content .tree-structure li").length == 0) {
            $(".tree-structure-content").hide();
        } else {
            $(".tree-structure-content").show();
            oldHeight = $(".tree-structure-content .tree-structure").height();
            $(".tree-structure-content .tree-structure").height("auto");
            if($(".tree-structure-content .tree-structure").height() > 56) {
                $(".folders-toggle-button").show();
            } else {
                $(".folders-toggle-button").hide();
            }
            newHeight = $(".tree-structure-content .tree-structure").height();
            $(".tree-structure-content .tree-structure").attr("data-height", newHeight);

            if($(".tree-structure-content").hasClass("active")) {
                $(".tree-structure-content .tree-structure").height(newHeight);
                $(".tree-structure-content .tree-structure").attr("data-height", newHeight);
            } else {
                $(".tree-structure-content .tree-structure").height(oldHeight);
            }
        }
    }

    function ajaxAnimation() {
        $(".folder-loader-ajax").addClass("active");
        $(".folder-loader-ajax img").removeClass("active");
        $(".folder-loader-ajax svg#successAnimation").addClass("active").addClass("animated");
        setTimeout(function(){
            $(".folder-loader-ajax").removeClass("active");
            $(".folder-loader-ajax img").addClass("active");
            $(".folder-loader-ajax svg#successAnimation").removeClass("active").removeClass("animated");
        }, 2000);
    }

    function make_sticky_folder_menu() {
        $(".sticky-folders > ul").html("");
        var stickyMenuHtml = "";

        $("#js-tree-menu li.jstree-node.is-sticky").each(function(){
            var folder_ID = $(this).attr("id");
            var folderName = $.trim($("#js-tree-menu").jstree(true).get_node(folder_ID).text);
            var folderCount = $("li.jstree-node[id='"+folder_ID+"'] > a span.premio-folder-count").text();
            var hasStar = $("li.jstree-node[id='"+folder_ID+"']").hasClass("is-high")?" is-high ":"";
            stickyMenuHtml += "<li data-folder-id='"+folder_ID+"' class='sticky-fldr "+hasStar+" sticky-folder-"+folder_ID+"'>" +
                "<a href='javascript:;'>" +
                "<span class='folder-title'>"+folderName+"</span>" +
                "<span class='folder-actions'>" +
                "<span class='update-inline-record'><i class='pfolder-edit-folder'></i></span>" +
                "<span class='star-icon'><i class='pfolder-star'></i></span>" +
                "<span class='premio-folder-count'>"+folderCount+"</span>" +
                "</span>"+
                "</a>" +
                "</li>";
        });
        $(".sticky-folders > ul").html(stickyMenuHtml);
        if($(".jstree-anchor.jstree-clicked").length) {
            var activeTermId = $(".jstree-anchor.jstree-clicked").closest("li.jstree-node").attr("id");
            $(".sticky-folders .sticky-folder-"+activeTermId+" a").addClass("active-item");
        }

        if($(".sticky-folders > ul > li").length > 0) {
            $(".sticky-folders").addClass("active");
        } else {
            $(".sticky-folders").removeClass("active");
        }

        setCustomScrollForFolder();
    }

    function setFolderCountAndDD() {
        if($("#media-attachment-taxonomy-filter").length) {
            $("#media-attachment-taxonomy-filter").each(function(){
                wcp_settings.terms = foldersArray;
                var selectedDD = $(this);
                currentDDVal = $(this).val();
                selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
                lastFolderData = foldersArray;
                for (var i = 0; i < foldersArray.length; i++) {
                    selectedDD.append("<option value='" + foldersArray[i].term_id + "'>" + foldersArray[i].name + " (" + foldersArray[i].trash_count + ")</option>");
                }
                selectedDD.val(currentDDVal).trigger("change");
            });
            if($("select.folder_for_media").length) {
                var selectedVal = $("select.folder_for_media").val();
                $("select.folder_for_media option:not(:first-child):not(:last-child)").remove();
                for (var i = 0; i < foldersArray.length; i++) {
                    $("select.folder_for_media option:last-child").before("<option value='" + foldersArray[i].term_id + "'>" + foldersArray[i].name +"</option>");
                }
                if(selectedVal != "") {
                    $(".folder_for_media").val(selectedVal);
                }
            }
        }
        $("span.premio-folder-count").text("");
        $(".folder-count").text("");
        for (i = 0; i < foldersArray.length; i++) {
            if(parseInt(foldersArray[i].trash_count) != 0) {
                $(".jstree-node[id='" + foldersArray[i].term_id + "'] > a.jstree-anchor span.premio-folder-count").text(foldersArray[i].trash_count);
                $(".sticky-folder-"+foldersArray[i].term_id+" .premio-folder-count").text(foldersArray[i].trash_count);
            }
        }

        if($(".media-select-folder").length) {
            $(".media-select-folder").html("<option value=''>Select Folder</option><option value='-1'>(Unassigned)</option>");
            for (i = 0; i < foldersArray.length; i++) {
                $(".media-select-folder").append("<option value='" + foldersArray[i].term_id + "'>" + foldersArray[i].name + " (" + foldersArray[i].trash_count + ")</option>");
            }
            $(".media-select-folder").val("");
        }

        if(activeRecordID != "") {
            $("#wcp_folder_"+activeRecordID).addClass("active-item");
        }

        if(isItFromMedia) {
            $("#title_"+fileFolderID).trigger("click");
            isItFromMedia = false;
        }
    }

    $(document).ready(function(){
        $(document).on("click", ".thumbnail-hover-box a", function(e){
            e.stopPropagation();
            e.stopImmediatePropagation();
            e.preventDefault();
            window.open($(this).prop("href"), "_blank");
            wp.media.frame.close();
            return false;
        });

        /* select dropdown */
        $(document).on("click", "#doaction", function(e){
            if($("#bulk-action-selector-top").val() == "move_to_folder") {
                show_folder_popup();
                return false;
            } else if($("#bulk-action-selector-top").val() == "edit") {
                if(typeof inlineEditPost == "object") {
                    inlineEditPost.setBulk();
                    return false;
                }
            }
        });
        $(document).on("click", "#doaction2", function(e){
            if($("#bulk-action-selector-bottom").val() == "move_to_folder") {
                show_folder_popup();
                return false;
            } else if($("#bulk-action-selector-bottom").val() == "edit") {
                if(typeof inlineEditPost == "object") {
                    inlineEditPost.setBulk();
                    return false;
                }
            }
        });


        $(document).on("submit", "#bulk-folder-form", function(e) {
            e.stopPropagation();
            e.preventDefault();

            if($("#bulk-select").val() != "") {
                chkStr = "";
                $(".wp-list-table input:checked").each(function () {
                    chkStr += $(this).val() + ",";
                });
                if($("#bulk-select").val() != "") {
                    if ($("#bulk-select").val() == "-1") {
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_id=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + $(this).val() + "&nonce=" + wcp_settings.nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                            method: 'post',
                            success: function (res) {
                                $("#bulk-move-folder").hide();
                                resetMediaAndPosts();
                                ajaxAnimation();
                            }
                        });
                    } else {
                        nonce = getSettingForPost($("#bulk-select").val(), 'nonce');
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + $("#bulk-select").val() + "&nonce=" + nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                            method: 'post',
                            success: function (res) {
                                res = $.parseJSON(res);
                                $("#bulk-move-folder").hide();
                                if (res.status == "1") {
                                    resetMediaAndPosts();
                                    ajaxAnimation();
                                } else {
                                    $(".folder-popup-form").hide();
                                    $(".folder-popup-form").removeClass("disabled");
                                    $("#error-folder-popup-message").html(res.message);
                                    $("#error-folder-popup").show()
                                }
                            }
                        });
                    }
                }
            }
        });
    });

    function show_folder_popup() {
        $("#bulk-action-selector-top, #bulk-action-selector-bottom").val("-1");
        if($(".wp-list-table tbody input[type='checkbox']:checked").length == 0) {
            alert("Please select items to move in folder");
        } else {
            $("#bulk-move-folder").show();
            $("#bulk-select").html("<option value=''>Loading...</option>");
            $(".move-to-folder").attr("disabled", true);
            $("#move-to-folder").prop("disabled", true);
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=wcp_get_default_list&active_id=" + activeRecordID,
                method: 'post',
                success: function (res) {
                    res = $.parseJSON(res);
                    $("#bulk-select").html("<option value=''>Select Folder</option><option value='-1'>(Unassigned)</option>");
                    $(".move-to-folder").prop("disabled", false);
                    $("#move-to-folder").prop("disabled", false);
                    if(res.status == 1) {
                        var taxonomies = res.taxonomies;
                        for(i=0;i<taxonomies.length;i++) {
                            $("#bulk-select").append("<option value='"+taxonomies[i].term_id+"'>"+taxonomies[i].name+"</option>");
                        }
                    }
                }
            });
        }
    }

    if(wcp_settings.post_type == "attachment") {

        $(document).ready(function(){
            if(wcp_settings.show_in_page == "show") {
                $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div><div class="folders-toggle-button"><span></span></div></div>');
            }

            add_menu_to_list();

            apply_animation_height();
        });



        var resetMediaFlag = null;

        function resetMediaData(loadData) {
            resetMediaFlag = $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=wcp_get_default_list&active_id="+activeRecordID,
                method: 'post',
                beforeSend: function() {
                    if(resetMediaFlag != null) {
                        resetMediaFlag.abort();
                    }
                },
                success: function (res) {
                    res = $.parseJSON(res);
                    // $("#js-tree-menu > ul#space_0").html(res.data);
                    $(".header-posts .total-count").text(res.total_items);
                    $(".un-categorised-items .total-count").text(res.empty_items);
                    selectedVal = $("#media-attachment-taxonomy-filter").val();
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
                    foldersArray = res.taxonomies;
                    setFolderCountAndDD();

                    if(activeRecordID != "") {
                        $("#wcp_folder_"+activeRecordID).addClass("active-item");
                    }

                    if(isItFromMedia) {
                        $("#title_"+fileFolderID).trigger("click");
                        isItFromMedia = false;
                    }
                }
            });
        }

        function setMediaBoxWidth() {
            $(".media-frame-content .media-toolbar").width($(".media-frame-content").width() - 20);
        }

        setMediaBoxWidth();

        $(window).resize(function(){
            setMediaBoxWidth();
        });

        $(document).on("click", ".button.organize-button", function(){
            if(!$(".media-frame").hasClass("mode-select")) {
                setCookie("media-select-mode", "on", 7);
            } else {
                eraseCookie("media-select-mode");
            }
            $("button.button.media-button.select-mode-toggle-button").trigger("click");
            if($(".media-frame").hasClass("mode-select")) {
                $(".media-info-message").addClass("active");
                $(".select-all-item-btn").addClass("active");
            } else {
                $(".media-info-message, .custom-media-select").removeClass("active");
                $(".select-all-item-btn").removeClass("active");
            }
        });

        $(document).on("click", ".select-mode-toggle-button", function(){
            setTimeout(function() {
                if(!$(".media-frame").hasClass("mode-select")) {
                    setCookie("media-select-mode", "off", -1);
                }
                if($(".media-frame").hasClass("mode-select")) {
                    $(".media-info-message").addClass("active");
                    $(".select-all-item-btn").addClass("active");
                } else {
                    $(".media-info-message, .custom-media-select").removeClass("active");
                    $(".select-all-item-btn").removeClass("active");
                }
            }, 10);
        });

        $(document).on("click", ".select-all-item-btn", function(){
            $("ul.attachments li:not(.selected)").trigger("click");
        });

        $(document).on("change", ".folder_for_media", function(){
            if($(this).val() == "add-folder") {
                fileFolderID = 0;
                isItFromMedia = true;
                addFolder();
                // $(".add-new-folder").trigger("click");
                //$(this).val("-1");
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
            if(!$(".media-position").length) {
                $(".media-frame-content .media-toolbar").before("<div class='media-position'></div>")
            }

            if($(".media-position").length) {
                setMediaBoxWidth();

                thisPosition = $(".media-position").offset().top - $(window).scrollTop();
                if(thisPosition <= 32) {
                    $(".media-frame-content .media-toolbar").addClass("sticky-media");
                    $(".media-position").height($(".media-frame-content .media-toolbar").outerHeight());
                } else {
                    $(".media-frame-content .media-toolbar").removeClass("sticky-media");
                    $(".media-position").height(1);
                }
            }
        }

        $(window).scroll(function(){
            setStickyHeaderForMedia()
        });
    } else {
        function setStickyHeaderForMedia() {}
    }

    var checkForMediaScreen;

    function setMediaButtons() {
        if($("button.button.media-button.select-mode-toggle-button").length) {
            clearInterval(checkForMediaScreen);
            $("button.button.media-button.select-mode-toggle-button").after("<button class='button organize-button'>Bulk Organize</button>");
            $(".media-toolbar-secondary").append("<span class='media-info-message'>Drag and drop your media files to the relevant folders</span>");
            $(".delete-selected-button").before("<button type='button' class='button button-primary select-all-item-btn'>Select All</button>");
            $(".media-toolbar-secondary").after("<div class='custom-media-select'>Move Selected files to: <select class='media-select-folder'></select></div>");
            $(".media-toolbar").append("<div style='clear:both;'></div><div class='media-folder-loader'><span>Uploading files</span> <span id='current_upload_files'></span>/<span id='total_upload_files'></span><div class='folder-progress'><div class='folder-meter orange-bg'><span></span></div></div></div>");
            if ($(".wcp-custom-form").length) {
                if (wp.Uploader !== undefined) {
                    wp.Uploader.queue.on('reset', function () {
                        resetMediaData(1);
                    });
                }
                $(document).ajaxComplete(function (ev, jqXHR, settings) {
                    actionName = settings.data;
                    if (typeof actionName != "undefined") {
                        if (actionName.length && actionName.indexOf("action=delete-post&id=") == 0) {
                            resetMediaData(0);
                        }
                    }
                });
            }
            setTimeout(function () {
                docReferrar = document.referrer;
                if (docReferrar.indexOf("wp-admin/upload.php") != -1) {
                    mediaMode = getCookie("media-select-mode");
                    if (mediaMode == "on") {
                        $("button.button.media-button.select-mode-toggle-button").trigger("click");
                        //$(".attachments-browser li.attachment").draggable("enable");

                        if ($(".media-frame").hasClass("mode-select")) {
                            $(".media-info-message").addClass("active");
                        } else {
                            $(".media-info-message, .custom-media-select").removeClass("active");
                        }
                    }
                } else {
                    eraseCookie("media-select-mode");
                }
                resetMediaData(1);
            }, 1000);
        }
    }

    $(document).ready(function(){
        if(wcp_settings.post_type == "attachment") {
            if($("#tmpl-media-frame").length) {
                checkForMediaScreen = setInterval(function(){
                    setMediaButtons();
                }, 1000);
            }
        }

        $(document).on("click", ".attachments-browser ul.attachments .thumbnail", function () {
            if(wcp_settings.post_type == "attachment") {
                if ($(".media-toolbar").hasClass("media-toolbar-mode-select")) {
                    if ($("ul.attachments li.selected").length == 0) {
                        $(".custom-media-select").removeClass("active");
                    } else {
                        $(".custom-media-select").addClass("active");
                    }
                }
            }
        });

        $(document).on("change", ".media-select-folder", function () {
            if(wcp_settings.post_type == "attachment") {
                if ($(this).val() != "") {
                    var checkStr = "";
                    $(".attachments-browser li.attachment.selected").each(function () {
                        checkStr += $(this).attr("data-id") + ",";
                    });
                    if ($(this).val() == "-1") {
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_id=" + checkStr + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + $(this).val() + "&nonce=" + wcp_settings.nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                            method: 'post',
                            success: function (res) {
                                if (fileFolderID != 0 && fileFolderID != $(".media-select-folder").val()) {
                                    $("ul.attachments li.selected").remove();
                                }
                                resetMediaAndPosts();
                                ajaxAnimation();
                            }
                        });
                    } else {
                        nonce = getSettingForPost($(this).val(), 'nonce');
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            data: "post_ids=" + checkStr + "&type=" + wcp_settings.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + $(this).val() + "&nonce=" + nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                            method: 'post',
                            success: function (res) {
                                res = $.parseJSON(res);
                                $("#bulk-move-folder").hide();
                                if (res.status == "1") {
                                    if (fileFolderID != 0 && fileFolderID != $(".media-select-folder").val()) {
                                        $("ul.attachments li.selected").remove();
                                    }
                                    resetMediaAndPosts();
                                    ajaxAnimation();
                                } else {
                                    $(".folder-popup-form").hide();
                                    $(".folder-popup-form").removeClass("disabled");
                                    $("#error-folder-popup-message").html(res.message);
                                    $("#error-folder-popup").show()
                                }
                            }
                        });
                    }
                }
            }
        });
    })
}));