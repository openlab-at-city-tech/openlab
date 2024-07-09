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
    var creatingParentMenu = 0;
    var folderOrder = 0;
    var isMultipleRemove = false;
    var folderIDs = "";
    var folderPropertyArray = [];
    var folderCurrentURL = wcp_settings.current_url;
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

        $(document).on("click", ".subsubsub a", function(e){
            if($("#js-tree-menu .jstree-anchor.jstree-clicked").length) {
                var CurrentNode = $("#js-tree-menu .jstree-anchor.jstree-clicked").closest(".jstree-node").attr("id");
                var folderSlug = getSettingForPost(CurrentNode, 'slug');
                if(folderSlug != "") {
                    e.preventDefault();
                    var thisURL = $(this).attr("href");
                    thisURL = thisURL + "&" + wcp_settings.custom_type + "=" + folderSlug;
                    window.location = thisURL;
                }
            } else if($("#dynamic-tree-folders").length && $("#dynamic-tree-folders .jstree-anchor.jstree-clicked").length) {
                var CurrentNode = $("#dynamic-tree-folders .jstree-anchor.jstree-clicked").closest(".jstree-node").attr("id");
                if(CurrentNode != "") {
                    e.preventDefault();
                    var thisURL = $(this).attr("href");
                    thisURL = thisURL + "&ajax_action=premio_dynamic_folders&dynamic_folder=" + CurrentNode + "_anchor";
                    window.location = thisURL;
                }
            }
        });

        /** Media page grid/list view switch */
        $(document).on("click", ".view-switch > a", function(e){
            if(location.search != "") {
                try {
                    if($(this).hasClass("view-grid") || $(this).hasClass("view-list")) {
                        var eleLink = $(this).attr("href");
                    }
                    e.preventDefault();
                    var str = location.search.substring(1);
                    const searchArray = Object.fromEntries(new URLSearchParams(str));
                    if (Object.keys(searchArray).length) {
                        $.each(searchArray, function (key, keyVal) {
                            if (key != "mode" && key != "paged") {
                                if(keyVal != "") {
                                    eleLink += "&" + key + "=" + keyVal;
                                }
                            }
                        });
                    }
                    window.location = eleLink;
                } catch (exceptionVar) {

                }
            }
        });

        /* check for jetpack */
        if($("body").hasClass("jetpack-connected") && !$("body").hasClass("mobile")) {
            if(!$("body").hasClass("folded")) {
                if($("#adminmenuwrap").length) {
                    var yLength = parseInt($("#adminmenuwrap").width());
                    if($("html").prop("dir") == "rtl") {
                        $(".wcp-content").css("right", yLength);
                    } else {
                        $(".wcp-content").css("left", yLength);
                    }
                }
            }
        }

        $(document).on("click", "#collapse-button", function(){
            setTimeout(function(){
                if($("body").hasClass("jetpack-connected") && !$("body").hasClass("mobile")) {
                    if(!$("body").hasClass("folded")) {
                        if($("#adminmenuwrap").length) {
                            var yLength = parseInt($("#adminmenuwrap").width());
                            if($("html").prop("dir") == "rtl") {
                                $(".wcp-content").css("right", yLength);
                            } else {
                                $(".wcp-content").css("left", yLength);
                            }
                        }
                    }
                }
            }, 50);
        });


        foldersArray = wcp_settings.taxonomies;

        isKeyActive = parseInt(wcp_settings.is_key_active);
        n_o_file = parseInt(wcp_settings.folders);
        activeRecordID = parseInt(wcp_settings.selected_taxonomy);
        hasStars = parseInt(wcp_settings.hasStars);
        hasChildren = parseInt(wcp_settings.hasChildren);
        currentPage = parseInt(wcp_settings.currentPage);
        console.log(activeRecordID);

        folderPropertyArray = wcp_settings.folder_settings;

        $(document).on("click", ".folder-settings-btn > a", function(e){
            e.stopPropagation();
            $(".folder-settings-btn").toggleClass('active');
        });
        $(document).on("click", "body,html", function(){
            $(".folder-settings-btn").removeClass('active');
        });
        $(document).on("click", ".folder-setting-menu", function(e){
            e.stopPropagation();
        });

        initJSTree();

        setCustomScrollForFolder();


        $(document).ajaxComplete(function (ev, jqXHR, settings) {
            var actionName = settings.data;
            if(typeof actionName != "undefined") {
                if(actionName.length && actionName.indexOf("action=inline-save") != -1) {
                    resetMediaAndPosts();
                    triggerInlineUpdate();
                }
                if(actionName.length && actionName.indexOf("action=save-attachment&id=") == 0) {
                    resetMediaData(0);
                }
            }
        });

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
                if(menuWidth <= 245) {
                    menuWidth = 245;
                }
                if(wcp_settings.isRTL == "1") {
                    $("#wpcontent").css("padding-right", (menuWidth + 20) + "px");
                    $("#wpcontent").css("padding-left", "0px");
                } else {
                    $("#wpcontent").css("padding-left", (menuWidth + 20) + "px");
                }
                $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px - "+menuWidth+"px)");
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
                    $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px)");
                } else {
                    if($("#wcp-content").hasClass("hide-folders-area")) {
                        folderStatus = "show";
                        $(".wcp-hide-show-buttons .toggle-buttons.show-folders").removeClass("active");
                        $(".wcp-hide-show-buttons .toggle-buttons.hide-folders").addClass("active");
                        $("#wcp-content").addClass("no-transition");
                        $("#wcp-content").removeClass("hide-folders-area");
                        if(wcp_settings.isRTL == "1") {
                            $("#wpcontent").css("padding-right", (parseInt(wcp_settings.folder_width) + 20) + "px");
                            $("#wpcontent").css("padding-left", "0px");
                        } else {
                            $("#wpcontent").css("padding-left", (parseInt(wcp_settings.folder_width) + 20) + "px");
                        }
                        $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px - "+menuWidth+"px)");
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
                if(menuWidth <= 245) {
                    menuWidth = 245;
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
                    $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px)");
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
                        if(wcp_settings.isRTL == "1") {
                            $("#wpcontent").css("padding-right", (parseInt(wcp_settings.folder_width) + 20) + "px");
                            $("#wpcontent").css("padding-left", "0px");
                        } else {
                            $("#wpcontent").css("padding-left", (parseInt(wcp_settings.folder_width) + 20) + "px");
                        }
                        $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px - "+menuWidth+"px)");
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
                if(ui.size.width <= 245) {
                    $(".wcp-content").width(245);
                    wcp_settings.folder_width = 245;
                }
            }
        });

        $(document).on("contextmenu", "#js-tree-menu .jstree-anchor", function(e){
            contextOffsetX = e.pageX;
            contextOffsetY = e.pageY;
            $(this).find("span.folder-inline-edit").trigger("click");
            return false;
        });

        $(document).on("click", "#js-tree-menu .folder-actions span.folder-inline-edit", function(e){
            e.stopImmediatePropagation()
            e.stopPropagation();
            e.preventDefault();
            if(wcp_settings.can_manage_folder == 0) {
                return;
            }
            isHigh = $(this).closest("li.jstree-node").hasClass("is-high");
            isSticky = $(this).closest("li.jstree-node").hasClass("is-sticky");
            isStickyClass = (isSticky)?true:false;
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            menuHtml = "<div class='dynamic-menu' data-id='"+$(this).closest("li").prop("id")+"'><ul>";
            menuHtml += "<li class='new-main-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>"+wcp_settings.lang.NEW_FOLDER+"</a></li>";
            if(hasChildren) {
                menuHtml += "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>"+wcp_settings.lang.NEW_FOLDER+"</a></li>";
            } else {
                menuHtml += "<li class='new-folder-pro'><a target='_blank' href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>"+wcp_settings.lang.PRO.NEW_SUB_FOLDER+"</a></li>";
            }
            menuHtml += "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span>"+wcp_settings.lang.RENAME+"</a></li>";
            menuHtml += "<li class='color-folder'><a href='javascript:;'><span class=''><span class='dashicons dashicons-art'></span></span>" + wcp_settings.lang.CHANGE_COLOR + "<span class='dashicons dashicons-arrow-right-alt2'></span></a>";
            menuHtml += "<ul class='color-selector'>";
            menuHtml += "<li class='color-selector-ul'>";
            $(wcp_settings.selected_colors).each(function(key,value) {
                menuHtml += "<span class='folder-color-option' data-color='"+value+"' style='background-color:"+value+"'></span>";
            });
            menuHtml += "</li>";
            menuHtml += "<li><a href='"+wcp_settings.upgrade_url+"' target='_blank' class='change-custom-color'>"+wcp_settings.lang.ADD_CUSTOM_COLORS+"</a></li>";
            menuHtml += "<li><a href='javascript:;' class='folder-color-default'>"+wcp_settings.lang.REMOVE_COLOR+"</a></li>";
            menuHtml += "</ul>";
            menuHtml += "</li>";
            menuHtml += "<li class='default-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-active-icon'></i></span>"+wcp_settings.lang.PRO.OPEN_THIS_FOLDER+"</a></li>" +
                        "<li class='sticky-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>"+wcp_settings.lang.PRO.STICKY_FOLDER+"</a></li>";
            if(hasStars) {
                menuHtml += "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? wcp_settings.lang.REMOVE_STAR : wcp_settings.lang.ADD_STAR) + "</a></li>";
            } else {
                menuHtml += "<li class='mark-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? wcp_settings.lang.PRO.REMOVE_STAR : wcp_settings.lang.PRO.ADD_STAR) + "</a></li>";
            }
            menuHtml += "<li class='lock-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='dashicons dashicons-lock'></span>"+wcp_settings.lang.PRO.LOCK_FOLDER+"</a></li>" +
                        "<li class='duplicate-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-clone'></i></span>"+wcp_settings.lang.PRO.DUPLICATE_FOLDER+"</a></li>";

            hasPosts = parseInt($(this).closest("a.jstree-anchor").find(".premio-folder-count").text());
            if (wcp_settings.post_type == "attachment" && hasPosts) {
                menuHtml += "<li target='_blank' class='download-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span>"+wcp_settings.lang.PRO.DOWNLOAD_ZIP+"</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span>"+wcp_settings.lang.DELETE+"</a></li>" +
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
            return false;
        });

        $(document).on("click",".folder-color-option , .folder-color-default",function (e) {
            e.stopPropagation();
            folderID = $(this).closest(".dynamic-menu").data("id");
            var current_color = $(this).attr("data-color");
            if(typeof(current_color) == "undefined") {
                current_color = "";
            }

            var folderPostId = getIndexForPostSetting(folderID);
            folderPropertyArray[folderPostId]['has_color'] = current_color;
            nonce = getSettingForPost(folderID, 'nonce');
            update_custom_folder_color_css();
            $.ajax({
                url: wcp_settings.ajax_url,
                data: {
                    term_id: folderID,
                    type: wcp_settings.post_type,
                    action: "wcp_change_color_folder",
                    nonce: nonce,
                    color: current_color
                },
                method: 'post',
                cache: false,
                success: function (res) {
                    res = $.parseJSON(res);
                    update_custom_folder_color_css();
                }
            });

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
            menuHtml += "<li class='new-main-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>"+wcp_settings.lang.NEW_FOLDER+"</a></li>";
            if(hasChildren) {
                menuHtml += "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>"+wcp_settings.lang.NEW_FOLDER+"</a></li>";
            } else {
                menuHtml += "<li class='new-folder-pro'><a target='_blank' href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span>"+wcp_settings.lang.PRO.NEW_SUB_FOLDER+"</a></li>";
            }
            menuHtml += "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span>Rename</a></li>" +
                        "<li class='default-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-active-icon'></i></span>"+wcp_settings.lang.PRO.OPEN_THIS_FOLDER+"</a></li>" +
                        "<li class='sticky-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>"+wcp_settings.lang.PRO.STICKY_FOLDER+"</a></li>";
            if(hasStars) {
                menuHtml += "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? wcp_settings.lang.ADD_STAR : wcp_settings.lang.REMOVE_STAR) + "</a></li>";
            } else {
                menuHtml += "<li class='mark-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? wcp_settings.lang.PRO.ADD_STAR : wcp_settings.lang.PRO.REMOVE_STAR) + "</a></li>";
            }
            menuHtml += "<li class='lock-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class='dashicons dashicons-lock'></span>"+wcp_settings.lang.PRO.LOCK_FOLDER+"</a></li>" +
                        "<li class='duplicate-folder-pro'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-clone'></i></span>"+wcp_settings.lang.PRO.DUPLICATE_FOLDER+"</a></li>";

            hasPosts = parseInt($(this).closest("li.jstree-node").find("h3.title:first > .total-count").text());
            if (wcp_settings.post_type == "attachment" && hasPosts) {
                menuHtml += "<li class='download-folder'><a target='_blank' href='"+wcp_settings.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span>"+wcp_settings.lang.PRO.DOWNLOAD_ZIP+"</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span> "+wcp_settings.lang.DELETE+"</a></li>" +
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

        $(document).on("click", ".close-popup-button a:not(.hide-upgrade-modal):not(.is-modal)", function(){
            $(".folder-popup-form").hide();
            if($(".jstree-node[id='"+fileFolderID+"']").length) {
                $(".jstree-node[id='"+fileFolderID+"'] > a.jstree-anchor").trigger("focus");
            }
            if($(this).hasClass("upgrade-model-button")) {
                $("#upgrade-modal-popup").remove();
            }
        });

        $(document).on("click", ".close-popup-button a.hide-upgrade-modal", function(){
            if($(".rating-modal-steps#step-4").hasClass("active")) {
                set_review_reminder(-1);
                $(".rating-modal-popup").remove();
            } else if($(".rating-modal-steps#step-3").hasClass("active")) {
                set_review_reminder(14);
            } else {
                $(".rating-modal-steps").removeClass("active");
                $(".rating-modal-steps#step-3").addClass("active");
            }
        });

        $(document).on("click", "body, html", function(){
            $(".dynamic-menu").remove();
        });

        $(document).on("click", ".wcp-hide-show-buttons .toggle-buttons", function(){
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
                $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px - "+parseInt(wcp_settings.folder_width)+"px)");
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
                $("body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active").css("width", "calc(100% - 160px)");
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

        $(document).on("click", ".undo-button, .undo-folder-action:not(.disabled)", function(){
            $("#do-undo").removeClass("active");
            if(wcp_settings.useFolderUndo == "yes") {
                $.ajax({
                    url: wcp_settings.ajax_url,
                    type: 'post',
                    data: {
                        post_type: wcp_settings.post_type,
                        nonce: wcp_settings.nonce,
                        action: 'wcp_undo_folder_changes'
                    },
                    success: function(res){
                        $("#undo-done").addClass("active");
                        setTimeout(function(){
                            $("#undo-done").removeClass("active");
                        }, 2500);
                        if($("#media-attachment-taxonomy-filter").length) {
                            var wp1 = parent.wp;
                            if(wp1.media != undefined) {
                                wp1.media.frame.setState('insert');
                                if (wp1.media.frame.content.get() !== null && typeof(wp1.media.frame.content.get().collection) != "undefined") {
                                    wp1.media.frame.content.get().collection.props.set({ignore: (+new Date())});
                                    wp1.media.frame.content.get().options.selection.reset();
                                } else {
                                    wp1.media.frame.library.props.set({ignore: (+new Date())});
                                }
                            }
                        }
                        resetMediaAndPosts();
                    }
                })
            }
        });

        $(document).on("click", ".close-undo-box", function(e){
            e.preventDefault();
            $("#do-undo").removeClass("active");
        });
    });

    function checkForUndoFunctionality() {
        if(wcp_settings.useFolderUndo == "yes") {
            $("#do-undo").addClass("active");
            $('.undo-folder-action').removeClass("disabled");
            setTimeout(function(){
                $("#do-undo").removeClass("active");
                $('.undo-folder-action').addClass("disabled");
            }, parseInt(wcp_settings.defaultTimeout));
        }
    }

    function checkForCopyPaste() {
        $(".cut-folder-action, .copy-folder-action, .paste-folder-action, .delete-folder-action").addClass("disabled");
        if($("#js-tree-menu .jstree-anchor.jstree-clicked").length) {
            $(".delete-folder-action").removeClass("disabled");
        }

        if($("#menu-checkbox").is(":checked")) {
            if($("#js-tree-menu input.checkbox:checked").length > 0) {
                $(".delete-folder-action").removeClass("disabled");
            }
        }
    }



    function update_custom_folder_color_css() {
        $("#custome_folder_color_css").remove();
        var custom_color = "<style id='custome_folder_color_css'>"
        $(folderPropertyArray).each(function (key,val) {
            if(val.has_color != "") {
                custom_color += "li.jstree-node[id='" + val.folder_id + "'] .pfolder-folder-close {color: "+val.has_color+ "!important;}";
            }
        });
        custom_color += "</style>";
        $("head").append(custom_color);
    }

    function setDragAndDropElements() {

        update_custom_folder_color_css();

        checkForCopyPaste();

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
                    selectedItems = (selectedItems == 0 || selectedItems == 1) ? (wcp_settings.lang.ONE_ITEM) : (selectedItems + " " + wcp_settings.lang.ITEMS);
                    return $("<div class='selected-items'><span class='total-post-count'>" + selectedItems + " "+wcp_settings.lang.SELECTED+"</span></div>");
                } else {
                    return  $("<div class='selected-items'><span class='total-post-count'>"+wcp_settings.lang.SELECT_ITEMS+"</span></div>");
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
            tolerance: "pointer",
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
                            data: {
                                post_ids: chkStr,
                                type: wcp_settings.post_type,
                                action: "wcp_change_multiple_post_folder",
                                folder_id: folderID,
                                nonce: nonce,
                                status: wcp_settings.taxonomy_status,
                                taxonomy: activeRecordID,
                                post_status: wcp_settings.post_status
                            },
                            method: 'post',
                            success: function (res) {
                                res = $.parseJSON(res);
                                if(res.status == "1") {
                                    resetMediaAndPosts();
                                    checkForUndoFunctionality();
                                    $("#upgrade-modal-popup").show();
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
                        method: 'post',
                        data: {
                            post_ids: chkStr,
                            type: wcp_settings.post_type,
                            action: "wcp_change_multiple_post_folder",
                            folder_id: folderID,
                            nonce: nonce,
                            status: wcp_settings.taxonomy_status,
                            taxonomy: activeRecordID,
                            post_status: wcp_settings.post_status
                        },
                        success: function (res) {
                            res = $.parseJSON(res);
                            if(res.status == "1") {
                                // window.location.reload();
                                resetMediaAndPosts();
                                checkForUndoFunctionality();
                                $("#upgrade-modal-popup").show();
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
                        data: {
                            post_ids: chkStr,
                            type: wcp_settings.post_type,
                            action: "wcp_change_multiple_post_folder",
                            folder_id: folderID,
                            nonce: nonce,
                            status: wcp_settings.taxonomy_status,
                            taxonomy: activeRecordID,
                            post_status: wcp_settings.post_status
                        },
                        method: 'post',
                        success: function (res) {
                            // window.location.reload();
                            $("#upgrade-modal-popup").show();
                            resetMediaAndPosts();
                            checkForUndoFunctionality();
                        }
                    });
                }
            }
        });

        $(".un-categorised-items:not(.ui-droppable)").droppable({
            accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
            hoverClass: 'wcp-hover-list',
            tolerance: "pointer",
            classes: {
                "ui-droppable-active": "ui-state-highlight"
            },
            drop: function (event, ui) {
                folderID = -1;
                nonce = wcp_settings.nonce;
                if(ui.draggable.hasClass('wcp-move-multiple')) {
                    if($(".wp-list-table input:checked").length) {
                        chkStr = "";
                        $(".wp-list-table input:checked").each(function(){
                            chkStr += $(this).val() + ",";
                        });
                        checkForOtherFolders(chkStr);
                    }
                } else if(ui.draggable.hasClass('wcp-move-file')) {
                    postID = ui.draggable[0].attributes['data-id'].nodeValue;
                    chkStr = postID+",";
                    $(".wp-list-table input:checked").each(function(){
                        if(postID != $(this).val()) {
                            chkStr += $(this).val() + ",";
                        }
                    });
                    checkForOtherFolders(chkStr);
                } else if(ui.draggable.hasClass('attachment')) {
                    chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                    if($(".attachments-browser li.attachment.selected").length > 1) {
                        chkStr = "";
                        $(".attachments-browser li.attachment.selected").each(function(){
                            chkStr += $(this).data("id") + ",";
                        });
                    }
                    folderIDs = chkStr;
                    checkForOtherFolders(chkStr);
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
            tolerance: "pointer",
            classes: {
                "ui-droppable-active": "ui-state-highlight"
            },
            drop: function( event, ui ) {
                $("body").removeClass("no-hover-css");
                folderID = $(this).data('id');
                if( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
                    nonce = getSettingForPost(folderID, 'nonce');
                    if($(".wp-list-table input:checked").length) {
                        chkStr = "";
                        $(".wp-list-table input:checked").each(function(){
                            chkStr += $(this).val()+",";
                        });
                        $.ajax({
                            url: wcp_settings.ajax_url,
                            method: 'post',
                            data: {
                                post_ids: chkStr,
                                type: wcp_settings.post_type,
                                action: "wcp_change_multiple_post_folder",
                                folder_id: folderID,
                                nonce: nonce,
                                status: wcp_settings.taxonomy_status,
                                taxonomy: activeRecordID,
                                post_status: wcp_settings.post_status
                            },
                            success: function (res) {
                                // window.location.reload();
                                resetMediaAndPosts();
                                ajaxAnimation();
                                checkForUndoFunctionality();
                            }
                        });
                    }
                } else if( ui.draggable.hasClass( 'wcp-move-file' ) ) {
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
                        method: 'post',
                        data: {
                            post_ids: chkStr,
                            type: wcp_settings.post_type,
                            action: "wcp_change_multiple_post_folder",
                            folder_id: folderID,
                            nonce: nonce,
                            status: wcp_settings.taxonomy_status,
                            taxonomy: activeRecordID,
                            post_status: wcp_settings.post_status
                        },
                        success: function (res) {
                            // window.location.reload();
                            resetMediaAndPosts();
                            ajaxAnimation();
                            checkForUndoFunctionality();
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
                        method: 'post',
                        data: {
                            post_ids: chkStr,
                            type: wcp_settings.post_type,
                            action: "wcp_change_multiple_post_folder",
                            folder_id: folderID,
                            nonce: nonce,
                            status: wcp_settings.taxonomy_status,
                            taxonomy: activeRecordID,
                            post_status: wcp_settings.post_status
                        },
                        success: function (res) {
                            // window.location.reload();
                            resetMediaAndPosts();
                            ajaxAnimation();
                            checkForUndoFunctionality();
                        }
                    });
                }
            }
        });

        $(".sticky-folders li a:not(.ui-droppable)").droppable({
            accept: ".wcp-move-file, .wcp-move-multiple, .attachments-browser li.attachment",
            hoverClass: 'wcp-drop-hover',
            tolerance: "pointer",
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
                            method: 'post',
                            data: {
                                post_ids: chkStr,
                                type: wcp_settings.post_type,
                                action: "wcp_change_multiple_post_folder",
                                folder_id: folderID,
                                nonce: nonce,
                                status: wcp_settings.taxonomy_status,
                                taxonomy: activeRecordID,
                                post_status: wcp_settings.post_status
                            },
                            success: function (res) {
                                res = $.parseJSON(res);
                                if(res.status == "1") {
                                    resetMediaAndPosts();
                                    ajaxAnimation();
                                    checkForUndoFunctionality();
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
                        method: 'post',
                        data: {
                            post_ids: chkStr,
                            type: wcp_settings.post_type,
                            action: "wcp_change_multiple_post_folder",
                            folder_id: folderID,
                            nonce: nonce,
                            status: wcp_settings.taxonomy_status,
                            taxonomy: activeRecordID,
                            post_status: wcp_settings.post_status
                        },
                        success: function (res) {
                            res = $.parseJSON(res);
                            if(res.status == "1") {
                                // window.location.reload();
                                resetMediaAndPosts();
                                ajaxAnimation();
                                checkForUndoFunctionality();
                            } else {
                                $(".folder-popup-form").hide();
                                $(".folder-popup-form").removeClass("disabled");
                                $("#error-folder-popup-message").html(res.message);
                                $("#error-folder-popup").show()
                            }
                        }
                    });
                } else if(ui.draggable.hasClass('attachment')) {
                    chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                    nonce = getSettingForPost(folderID, 'nonce');
                    if($(".attachments-browser li.attachment.selected").length > 1) {
                        chkStr = "";
                        $(".attachments-browser li.attachment.selected").each(function () {
                            chkStr += $(this).data("id") + ",";
                        });
                    }
                    folderIDs = chkStr;
                    $.ajax({
                        url: wcp_settings.ajax_url,
                        data: {
                            post_ids: chkStr,
                            type: wcp_settings.post_type,
                            action: "wcp_change_multiple_post_folder",
                            folder_id: folderID,
                            nonce: nonce,
                            status: wcp_settings.taxonomy_status,
                            taxonomy: activeRecordID,
                            post_status: wcp_settings.post_status
                        },
                        method: 'post',
                        success: function (res) {
                            // window.location.reload();
                            resetMediaAndPosts();
                            ajaxAnimation();
                            checkForUndoFunctionality();
                        }
                    });
                }
            }
        });

        setFolderCountAndDD();
    }

    function setFolderCount() {
        $("#js-tree-menu .jstree-node").each(function(){
            var folderCount = ($(this).data("count") && $(this).data("count") != "")?$(this).data("count"):0;
            $(".jstree-node[id='" + $(this).attr("id") + "'] > a span.premio-folder-count").text(folderCount);
        });
        $("span.premio-folder-count").each(function(){
            if($(this).text() == "") {
                $(this).text(0);
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
                data: {
                    type: wcp_settings.post_type,
                    action: "get_folders_default_list",
                    post_status: wcp_settings.post_status
                },
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
                var ajaxURL = urlParam("paged");
                if(ajaxURL === false) {
                    ajaxURL = folderCurrentURL+"&paged="+currentPage;
                } else {
                    ajaxURL = folderCurrentURL;
                }
                $("#wpbody").load(ajaxURL + " #wpbody-content", false, function (res) {
                    var obj = { Title: "", Url: ajaxURL };
                    history.pushState(obj, obj.Title, obj.Url);
                    if (wcp_settings.show_in_page == "show" && !$(".tree-structure").length) {
                        $(".wp-header-end").before('<div class="tree-structure-content"><div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div></div>');
                    }
                    add_active_item_to_list();

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

                        check_for_wc_inline_edit();
                    }
                });
            }
        }
    }

    function check_for_wc_inline_edit() {
        if(wcp_settings.custom_type != "product_folder" || wcp_settings.post_type != 'product' || typeof(woocommerce_quick_edit) != "object") {
            return;
        }
        $( '#the-list' ).on(
            'click',
            '.editinline',
            function() {

                var post_id = $( this ).closest( 'tr' ).attr( 'id' );

                post_id = post_id.replace( 'post-', '' );

                var $wwop_inline_data = jQuery( '#wholesale_prices_inline_' + post_id ),
                    $base_currency = $wwop_inline_data.find( ".product_base_currency" );

                $wwop_inline_data.find( ".whole_price" ).each( function( index ) {
                    if ( $base_currency.length > 0 ) {
                        if ( jQuery( this ).attr( 'data-currencyCode' ) == $base_currency.text() ) {
                            var $wholesale_price_field = jQuery( 'input[name="' + jQuery( this ).attr( 'data-wholesalePriceKeyWithCurrency' ) + '"]' , '.inline-edit-row' );

                            if ( $wholesale_price_field.length <= 0 ) // meaning we already modified the name, so we use the name with no currency instead
                                $wholesale_price_field = jQuery( 'input[name="' + jQuery( this ).attr( 'id' ) + '"]' , '.inline-edit-row' );

                            $wholesale_price_field.val( jQuery( this ).text() );

                            $wholesale_price_field.attr( 'placeholder' , '' );

                            $wholesale_price_field.siblings( '.title' ).html( $wholesale_price_field.siblings( '.title' ).html() + ' <em><b>Base Currency</b></em>' );

                            $wholesale_price_field.attr( "name" , jQuery( this ).attr( 'id' ) );

                            var $parent_section_container = $wholesale_price_field.closest( ".section-container" );
                            $wholesale_price_field.closest( "label" ).detach().prependTo( $parent_section_container );
                        } else
                            jQuery( 'input[name="' + jQuery( this ).attr( 'id' ) + '"]' , '.inline-edit-row' ).val( jQuery( this ).text() );

                    } else
                        jQuery( 'input[name="' + jQuery( this ).attr( 'id' ) + '"]' , '.inline-edit-row' ).val( jQuery( this ).text() );

                } );

                var $wc_inline_data = $( '#woocommerce_inline_' + post_id );

                var sku        = $wc_inline_data.find( '.sku' ).text(),
                    regular_price  = $wc_inline_data.find( '.regular_price' ).text(),
                    sale_price     = $wc_inline_data.find( '.sale_price ' ).text(),
                    weight         = $wc_inline_data.find( '.weight' ).text(),
                    length         = $wc_inline_data.find( '.length' ).text(),
                    width          = $wc_inline_data.find( '.width' ).text(),
                    height         = $wc_inline_data.find( '.height' ).text(),
                    shipping_class = $wc_inline_data.find( '.shipping_class' ).text(),
                    visibility     = $wc_inline_data.find( '.visibility' ).text(),
                    stock_status   = $wc_inline_data.find( '.stock_status' ).text(),
                    stock          = $wc_inline_data.find( '.stock' ).text(),
                    featured       = $wc_inline_data.find( '.featured' ).text(),
                    manage_stock   = $wc_inline_data.find( '.manage_stock' ).text(),
                    menu_order     = $wc_inline_data.find( '.menu_order' ).text(),
                    tax_status     = $wc_inline_data.find( '.tax_status' ).text(),
                    tax_class      = $wc_inline_data.find( '.tax_class' ).text(),
                    backorders     = $wc_inline_data.find( '.backorders' ).text(),
                    product_type   = $wc_inline_data.find( '.product_type' ).text();

                var formatted_regular_price = regular_price.replace( '.', woocommerce_admin.mon_decimal_point ),
                    formatted_sale_price        = sale_price.replace( '.', woocommerce_admin.mon_decimal_point );

                $( 'input[name="_sku"]', '.inline-edit-row' ).val( sku );
                $( 'input[name="_regular_price"]', '.inline-edit-row' ).val( formatted_regular_price );
                $( 'input[name="_sale_price"]', '.inline-edit-row' ).val( formatted_sale_price );
                $( 'input[name="_weight"]', '.inline-edit-row' ).val( weight );
                $( 'input[name="_length"]', '.inline-edit-row' ).val( length );
                $( 'input[name="_width"]', '.inline-edit-row' ).val( width );
                $( 'input[name="_height"]', '.inline-edit-row' ).val( height );

                $( 'select[name="_shipping_class"] option:selected', '.inline-edit-row' ).attr( 'selected', false ).trigger( 'change' );
                $( 'select[name="_shipping_class"] option[value="' + shipping_class + '"]' ).attr( 'selected', 'selected' )
                    .trigger( 'change' );

                $( 'input[name="_stock"]', '.inline-edit-row' ).val( stock );
                $( 'input[name="menu_order"]', '.inline-edit-row' ).val( menu_order );

                $(
                    'select[name="_tax_status"] option, ' +
                    'select[name="_tax_class"] option, ' +
                    'select[name="_visibility"] option, ' +
                    'select[name="_stock_status"] option, ' +
                    'select[name="_backorders"] option'
                ).prop( 'selected', false ).removeAttr( 'selected' );

                var is_variable_product = 'variable' === product_type;
                $( 'select[name="_stock_status"] ~ .wc-quick-edit-warning', '.inline-edit-row' ).toggle( is_variable_product );
                $( 'select[name="_stock_status"] option[value="' + (is_variable_product ? '' : stock_status) + '"]', '.inline-edit-row' )
                    .attr( 'selected', 'selected' );

                $( 'select[name="_tax_status"] option[value="' + tax_status + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
                $( 'select[name="_tax_class"] option[value="' + tax_class + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
                $( 'select[name="_visibility"] option[value="' + visibility + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
                $( 'select[name="_backorders"] option[value="' + backorders + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );

                if ( 'yes' === featured ) {
                    $( 'input[name="_featured"]', '.inline-edit-row' ).prop( 'checked', true );
                } else {
                    $( 'input[name="_featured"]', '.inline-edit-row' ).prop( 'checked', false );
                }

                // Conditional display.
                var product_is_virtual = $wc_inline_data.find( '.product_is_virtual' ).text();

                var product_supports_stock_status = 'external' !== product_type;
                var product_supports_stock_fields = 'external' !== product_type && 'grouped' !== product_type;

                $( '.stock_fields, .manage_stock_field, .stock_status_field, .backorder_field' ).show();

                if ( product_supports_stock_fields ) {
                    if ( 'yes' === manage_stock ) {
                        $( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).show().removeAttr( 'style' );
                        $( '.stock_status_field' ).hide();
                        $( '.manage_stock_field input' ).prop( 'checked', true );
                    } else {
                        $( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).hide();
                        $( '.stock_status_field' ).show().removeAttr( 'style' );
                        $( '.manage_stock_field input' ).prop( 'checked', false );
                    }
                } else if ( product_supports_stock_status ) {
                    $( '.stock_fields, .manage_stock_field, .backorder_field' ).hide();
                } else {
                    $( '.stock_fields, .manage_stock_field, .stock_status_field, .backorder_field' ).hide();
                }

                if ( 'simple' === product_type || 'external' === product_type ) {
                    $( '.price_fields', '.inline-edit-row' ).show().removeAttr( 'style' );
                } else {
                    $( '.price_fields', '.inline-edit-row' ).hide();
                }

                if ( 'yes' === product_is_virtual ) {
                    $( '.dimension_fields', '.inline-edit-row' ).hide();
                } else {
                    $( '.dimension_fields', '.inline-edit-row' ).show().removeAttr( 'style' );
                }

                // Rename core strings.
                $( 'input[name="comment_status"]' ).parent().find( '.checkbox-title' ).text( woocommerce_quick_edit.strings.allow_reviews );
            }
        );

        $( '#the-list' ).on(
            'change',
            '.inline-edit-row input[name="_manage_stock"]',
            function() {

                if ( $( this ).is( ':checked' ) ) {
                    $( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).show().removeAttr( 'style' );
                    $( '.stock_status_field' ).hide();
                } else {
                    $( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).hide();
                    $( '.stock_status_field' ).show().removeAttr( 'style' );
                }
            }
        );
    }


    function urlParam(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)')
            .exec(window.location.search);

        return (results !== null) ? results[1] || 0 : false;
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

            check_for_wc_inline_edit();
        }

        if(wcp_settings.post_type == "attachment") {
            if(!$(".move-to-folder-top").length) {
                $("#bulk-action-selector-top").append("<option class='move-to-folder-top' value='move_to_folder'>"+wcp_settings.lang.MOVE_TO_FOLDER+"</option>");
            }
            if(!$(".move-to-folder-bottom").length) {
                $("#bulk-action-selector-bottom").append("<option class='move-to-folder-bottom' value='move_to_folder'>"+wcp_settings.lang.MOVE_TO_FOLDER+"</option>");
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
            update_custom_folder_color_css();
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
        var contentHeight = $(window).height() - $("#wpadminbar").height() - $(".sticky-wcp-custom-form").height() - 40;

        /*var scrollTop = 0;
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
       }*/
        $("#custom-scroll-menu").height(contentHeight);
        $("#custom-scroll-menu").overlayScrollbars({
            resize : 'none',
            sizeAutoCapable :true,
            autoUpdateInterval : 33,
            x :'scroll',
            clipAlways :false,
            y :'scroll'
        });

        if($(".custom-scroll-menu").hasClass("hor-scroll")) {
            jQuery("#custom-scroll-menu .os-viewport").on("scroll", function () {
                setActionPosition();
            });
            setActionPosition();
        }
    }

    function setActionPosition() {
        jQuery("#js-tree-menu span.folder-actions").css("right", (jQuery("#custom-scroll-menu .horizontal-scroll-menu").width() - jQuery("#custom-scroll-menu .os-viewport").width() - $("#custom-scroll-menu .os-viewport").scrollLeft() - 10));
    }

    /* add folder code */
    $(document).ready(function(){
        $(window).bind('popstate', function() {
            window.location.reload();
        });
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

        $(document).on("mouseover", ".folders-action-menu", function(){
            $("body").addClass("add-folder-zindex");
        }).on("mouseleave", ".folders-action-menu", function(){
            $("body").removeClass("add-folder-zindex");
        });

        $(document).on("change", "#media-attachment-taxonomy-filter", function(e){
            if($("#js-tree-menu").hasClass("jstree")) {
                $("#js-tree-menu").jstree(true).deselect_all();
            }
            $(".active-item").removeClass("active-item");
            if($(this).val() == "all") {
                $(".all-posts").addClass("active-item");
            } else if($(this).val() == "unassigned") {
                $(".un-categorised-items").addClass("active-item");
            } else {
                $("#js-tree-menu").jstree('select_node', $(this).val());
            }
        });

        $(document).on("click", ".new-folder", function(){
            fileFolderID = $(this).closest(".dynamic-menu").data("id");
            isItFromMedia = false;
            creatingParentMenu = 0;
            addFolder();
        });

        $(document).on("click", ".new-main-folder", function(){
            creatingParentMenu = 1;
            fileFolderID = $(this).closest(".dynamic-menu").data("id");
            isItFromMedia = false;
            addFolder();
        });

        $(document).on("click", ".duplicate-folder", function(e){
            e.stopPropagation();
            creatingParentMenu = 0;
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

                var parentIds = "";

                if(parentId == 0) {
                    folderOrder = $("#js-tree-menu > ul > li.jstree-node").length;
                    if(creatingParentMenu) {
                        folderOrder = jQuery("#js-tree-menu > ul > li[id='"+fileFolderID+"']").index();
                        jQuery("#js-tree-menu > ul > li").each(function(i){
                            parentIds += jQuery(this).attr("id")+",";
                            if(i == folderOrder) {
                                parentIds += "#,";
                            }
                        });
                        folderOrder = folderOrder + 1;
                    }
                } else {
                    folderOrder = $("#js-tree-menu > ul > li.jstree-node[id='"+parentId+"'] > ul.jstree-children > li").length + 1;
                }

                var foldersList = [];
                if(parentId == 0) {
                    if($("#js-tree-menu > .jstree-container-ul > .jstree-node").length) {
                        $("#js-tree-menu > .jstree-container-ul > .jstree-node").each(function(){
                            foldersList.push($(this).attr("id"));
                        });
                    }
                } else {
                    if($("#js-tree-menu .jstree-node[id='"+parentId+"'] > ul> li.jstree-node").length) {
                        $("#js-tree-menu .jstree-node[id='"+parentId+"'] > ul> li.jstree-node").each(function(){
                            foldersList.push($(this).attr("id"));
                        });
                    }
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
                        folders: foldersList,
                        is_duplicate: isDuplicate,
                        duplicate_from: duplicateFolderId,
                        parent_ids: parentIds,
                        parent_menu: creatingParentMenu
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
                                        'has_color': result.data[i]['has_color'],
                                        'slug': result.data[i]['slug'],
                                        'is_deleted': 0
                                    };
                                    folderPropertyArray.push(folderProperty);
                                    var folderTitle = result.data[i]['title'];
                                    folderTitle = folderTitle.replace(/\\/g, '');
                                    if(!creatingParentMenu) {
                                        $('#js-tree-menu').jstree().create_node(result.parent_id, {
                                            "id": result.data[i]['term_id'],
                                            "text": " " + folderTitle
                                        }, i, function () {
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-nonce", result.data[i]['nonce']);
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-slug", result.data[i]['slug']);
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-parent", result.parent_id);
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "'] > a.jstree-anchor .premio-folder-count").text(result.data[i].folder_count);
                                        });
                                    } else {
                                        $('#js-tree-menu').jstree().create_node('#', {
                                            "id": result.data[i]['term_id'],
                                            "text": " " + folderTitle
                                        }, i, function () {
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-nonce", result.data[i]['nonce']);
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-slug", result.data[i]['slug']);
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "']").attr("data-parent", result.parent_id);
                                            $(".jstree-node[id='" + result.data[i]['term_id'] + "'] > a.jstree-anchor .premio-folder-count").text(result.data[i].folder_count);
                                        });
                                    }
                                    creatingParentMenu = 0;

                                    if($(".jstree-node[id='"+result.data[i]['term_id']+"']").length) {
                                        $(".jstree-node[id='"+result.data[i]['term_id']+"'] > a.jstree-anchor").trigger("focus");
                                    }
                                }
                            }
                            ajaxAnimation();
                            make_sticky_folder_menu();
                            if($("#media-attachment-taxonomy-filter").length) {
                                fileFolderID = result.term_id;
                                resetMediaData(0);

                                resetSelectMediaDropDown();
                            }
                            isDuplicate = false;
                            duplicateFolderId = 0;
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

        $("#add-update-folder-title").text(wcp_settings.lang.ADD_NEW_FOLDER);
        $("#save-folder-data").text(wcp_settings.lang.SUBMIT);
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
        $("#inline-update").on("click", function(){
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


        $(document).on("click", ".form-cancel-btn:not(.avoid-cancel)", function(){
            $(".folder-popup-form").hide();
            if($(".jstree-node[id='"+fileFolderID+"']").length) {
                $(".jstree-node[id='"+fileFolderID+"'] > a.jstree-anchor").trigger("focus");
            } else if($("#js-tree-menu .jstree-anchor.jstree-clicked").length) {
                $("#js-tree-menu .jstree-anchor.jstree-clicked").trigger("focus");
            }
        });

        if($("#folder-rating").length && typeof(pr_rating_settings) == "object") {
            $("#rating-modal-popup").show();
            $("#folder-rating").starRating({
                initialRating   : 0,
                useFullStars    : true,
                strokeColor     : '#FDB10C',
                ratedColor      : '#FDB10C',
                activeColor     : '#FDB10C',
                strokeWidth     : 0,
                minRating       : 1,
                starSize        : 32,
                useGradient     : 0,
                onLeave: function() {
                    $(".upgrade-user-rating span").text("0/5");
                },
                onHover: function(currentRate) {
                    $(".upgrade-user-rating span").text(currentRate+"/5");
                },
                callback: function(currentRate) {
                    if( currentRate !== 5 ) {
                        $(".rating-modal-steps").removeClass("active");
                        $(".rating-modal-steps#step-2").addClass("active");
                        $("#folder-rated-rating").html("");
                        for(i=0; i<parseInt(currentRate); i++) {
                            var ratingStar = '<div class="jq-star"><svg shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:0px;" xml:space="preserve"> <polygon data-side="center" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: #ffa83e;"></polygon> <polygon data-side="left" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon> <polygon data-side="right" className="svg-empty-28" points="364,55.7 255.5,46.8 214,-59 213.9,181 306.5,241 281.1,129.8 " style="stroke-opacity: 0;"></polygon> </svg></div>';
                            $("#folder-rated-rating").append(ratingStar);
                        }
                    } else {
                        window.open("https://wordpress.org/support/plugin/folders/reviews/#new-post", '_blank');
                        $(".rating-logo").remove();
                        $(".rating-modal-steps").removeClass("active");
                        $(".rating-modal-steps#step-4").addClass("active");
                    }
                }
            })
        }

        $(document).on("keyup", "#upgrade-review-comment", function(){
            var commentLength = 1000 - parseInt($.trim($(this).val()).length);
            if(commentLength < 0) {
                var userComment = $.trim($(this).val());
                userComment = userComment.slice(0, 1000);
                $(".upgrade-review-textarea label span").text(0);
                $(this).val(userComment);
            } else {
                $(".upgrade-review-textarea label span").text(commentLength);
            }
        });

        $(document).on("change", "#upgrade-review-comment", function(){
            var commentLength = 1000 - parseInt($.trim($(this).val()).length);
            if(commentLength < 0) {
                var userComment = $.trim($(this).val());
                userComment = userComment.slice(0, 1000);
                $(".upgrade-review-textarea label span").text(0);
                $(this).val(userComment);
            } else {
                $(".upgrade-review-textarea label span").text(commentLength);
            }
        });

        $(document).on("click", ".hide-upgrade-popup", function(e){
            e.preventDefault();
            $("#upgrade-modal-popup").remove();
        });

        $(document).on("click", ".upgrade-footer .upgrade-button", function(e){
            $("#upgrade-modal-popup").remove();
        });

        $(document).on("click", ".folder-popup-form:not(.always-show)", function (e) {
            $(".folder-popup-form").hide();
            if($(".jstree-node[id='"+fileFolderID+"']").length) {
                $(".jstree-node[id='"+fileFolderID+"'] > a.jstree-anchor").trigger("focus");
            } else if($("#js-tree-menu .jstree-anchor.jstree-clicked").length) {
                $("#js-tree-menu .jstree-anchor.jstree-clicked").trigger("focus");
            }
            if($(this).attr("id") == "rating-modal-popup") {
                if($(".rating-modal-steps#step-4").hasClass("active")) {
                    set_review_reminder(-1);
                    $(".rating-modal-popup").remove();
                } else {
                    set_review_reminder(14);
                }
            }
            if($(this).attr("id") == "upgrade-modal-popup") {
                $("#upgrade-modal-popup").remove();
            }
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
                        if($("#dynamic-folders .jstree-clicked").length) {
                            $("#js-tree-menu .jstree-clicked").removeClass("jstree-clicked");
                        }
                    }
                });
            }
            return false;
        });

        $(document).on("click", "#upgrade-review-button", function(){
            $("#rating-modal-popup").hide();
            $.ajax({
                url: wcp_settings.ajax_url,
                data: {
                    action: "folders_review_box_message",
                    rating: $("#folder-rated-rating .jq-star").length,
                    nonce: wcp_settings.review_box_nonce,
                    message: $.trim($("#upgrade-review-comment").val())
                },
                type: "post",
                success: function() {
                    set_review_reminder(-1);
                }
            });
        });

        $(document).on("click", "#update-review-time", function(){
            set_review_reminder($("#upgrade-review-reminder").val());
        });
    });

    function set_review_reminder(noOfDays) {
        $.ajax({
            url: wcp_settings.ajax_url,
            data: {
                action: "folders_review_box",
                days: noOfDays,
                nonce: wcp_settings.review_nonce
            },
            type: "post",
        });
        $("#rating-modal-popup").remove();
    }

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
        $(document).on("click", "#inline-remove, .delete-folder-action:not(.disabled)",function(){
            if($("#js-tree-menu a.jstree-clicked").length) {
                fileFolderID = $("#js-tree-menu a.jstree-clicked").closest("li.jstree-node").attr("id");
                removeFolderFromID(1);
                $(".dynamic-menu").remove();
                $(".active-menu").removeClass("active-menu");
            } else {
                if($("#menu-checkbox").is(":checked")) {
                    $(".dynamic-menu").remove();
                    removeFolderFromID(1);
                }
            }
        });

        $(document).on("click","#menu-checkbox",function(){
            if($(this).is(":checked")) {
                $(".js-tree-data").addClass("show-folder-checkbox");
                $("#menu-checkbox").prop("checked", true);
            } else {
                $("#js-tree-menu input.checkbox").attr("checked", false);
                $(".js-tree-data").removeClass("show-folder-checkbox");
                $("#menu-checkbox").prop("checked", false);
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
                        if(res.status == '1') {
                            var nextNode = getParentNodeInfo(fileFolderID);
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
                            if(nextNode != 0 && $("#"+nextNode+"_anchor").length) {
                                $("#"+nextNode+"_anchor").trigger("click");
                            } else {
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

    function getParentNodeInfo(nodeID) {
        if($(".jstree-node[id='"+nodeID+"']").next().length) {
            return $(".jstree-node[id='"+nodeID+"']").next().attr("id");
        } else if($(".jstree-node[id='"+nodeID+"']").prev().length) {
            return $(".jstree-node[id='"+nodeID+"']").prev().attr("id");
        } else if($(".jstree-node[id='"+nodeID+"']").parent().parent().hasClass("jstree-node")) {
            return $(".jstree-node[id='"+nodeID+"']").parent().parent().attr("id");
        }
        return 0;
    }

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
        if($("#menu-checkbox").is(":checked")) {
            if($("#js-tree-menu input.checkbox:checked").length > 0) {
                var folderIDs = "";
                var activeItemDeleted = false;
                $("#js-tree-menu input.checkbox:checked").each(function(){
                    if(!$(this).closest("li.jstree-node").hasClass("is-locked")) {
                        folderIDs += $(this).closest("li.jstree-node").attr("id") + ",";
                        if($(this).closest("li.jstree-node").hasClass("jstree-clicked")) {
                            activeItemDeleted = true;
                        }
                    }
                });
                if(folderIDs == "") {
                    return;
                }
                $(".form-loader-count").css("width", "100%");
                $.ajax({
                    url: wcp_settings.ajax_url,
                    data: "type=" + wcp_settings.post_type + "&action=wcp_remove_muliple_folder&term_id=" + folderIDs+"&nonce="+wcp_settings.nonce,
                    method: 'post',
                    success: function (res) {
                        res = $.parseJSON(res);
                        $(".form-loader-count").css("width", "0px");
                        if(res.status == '1') {
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
                        $("#menu-checkbox").attr("checked", false);
                        $("#js-tree-menu input.checkbox").attr("checked", false);
                        $("#js-tree-menu").removeClass("show-folder-checkbox");
                    }
                });
            } else {

            }
        }
    }

    function removeFolderFromID(popup_type) {
        var removeMessage = wcp_settings.lang.DELETE_FOLDER_MESSAGE;
        var removeNotice = wcp_settings.lang.ITEM_NOT_DELETED;
        isMultipleRemove = false;
        if(popup_type == 1) {
            if($("#menu-checkbox").is(":checked")) {
                isMultipleRemove = true;
                if($("#js-tree-menu input.checkbox:checked").length ==	 0) {
                    $(".folder-popup-form").hide();
                    $(".folder-popup-form").removeClass("disabled");
                    $("#error-folder-popup-message").html(wcp_settings.lang.SELECT_AT_LEAST_ONE_FOLDER);
                    $("#error-folder-popup").show();
                    return;
                } else {
                    if($("#js-tree-menu input.checkbox:checked").length > 1) {
                        removeMessage = wcp_settings.lang.DELETE_FOLDERS_MESSAGE;
                        removeNotice = wcp_settings.lang.ITEMS_NOT_DELETED;
                    }
                }
            }
        }
        $(".folder-popup-form").hide();
        $(".folder-popup-form").removeClass("disabled");
        $("#remove-folder-item").text(wcp_settings.lang.YES_DELETE_IT);
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
                        if($(".jstree-node[id='"+res.id+"']").length) {
                            $(".jstree-node[id='"+res.id+"'] > a.jstree-anchor").trigger("focus");
                        }
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
            $("#js-tree-menu .jstree-clicked").removeClass("jstree-clicked");
            $("#js-tree-menu").jstree('select_node', activeRecordID);
            $("#js-tree-menu #"+activeRecordID+"_anchor").addClass("jstree-clicked");
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

        $(document).on("click", "#js-tree-menu input.checkbox", function(){
            checkForCopyPaste();
        });

        $(".header-posts").on("click", function(){
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
            checkForCopyPaste();
        });

        $(".un-categorised-items").on("click", function(){
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
            checkForCopyPaste();
        });

        /* Expand/Collapse */
        $("#expand-collapse-list").on("click", function(e){
            e.stopPropagation();
            statusType = 0;
            if($(this).hasClass("all-open")) {
                $(this).removeClass("all-open");
                statusType = 0;
                $(this).attr("data-folder-tooltip",wcp_settings.lang.EXPAND);
                $("#js-tree-menu").jstree("close_all");
                $("#expand-collapse-list .text").text(wcp_settings.lang.EXPAND);
            } else {
                $(this).addClass("all-open");
                statusType = 1;
                $(this).attr("data-folder-tooltip",wcp_settings.lang.COLLAPSE);
                $("#js-tree-menu").jstree("open_all");
                $("#expand-collapse-list .text").text(wcp_settings.lang.COLLAPSE);
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
                                if(!hasChildren && $("#do_not_show_again").is(":checked")) {
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
            checkForCopyPaste();
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

            if(!hasChildren) {
                var oldPosition = n.old_position;
                var currentParent = n.parent;
                if(currentParent != "#") {
                    $('#js-tree-menu').jstree("move_node", "#"+n.node.id, "#", oldPosition);
                    $("#sub-drag-folder-popup").show();
                    return false;
                }
            }
            setActionPosition();
            setFolderCount();
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
            setActionPosition();
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
                        setActionPosition();
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

        $(document).on("click", "#do_not_show_again", function(){
            var childStatus = $(this).is(":checked")?1:0;
            $.ajax({
                url: wcp_settings.ajax_url,
                type: 'post',
                data: {
                    action: "premio_hide_child_popup",
                    status: childStatus,
                    nonce: wcp_settings.nonce,
                    post_type: wcp_settings.post_type
                }
            })
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
                $("#expand-collapse-list").attr("data-folder-tooltip",wcp_settings.lang.EXPAND);
                $("#expand-collapse-list .text").text(wcp_settings.lang.EXPAND);
            } else {
                var totalChild = $("#js-tree-menu .jstree-node.jstree-closed").length + $("#js-tree-menu .jstree-node.jstree-open").length;
                if($("#js-tree-menu .jstree-node.jstree-closed").length == totalChild) {
                    $("#expand-collapse-list").removeClass("all-open");
                    $("#expand-collapse-list").attr("data-folder-tooltip",wcp_settings.lang.EXPAND);
                    $("#expand-collapse-list .text").text(wcp_settings.lang.EXPAND);
                } else {
                    $("#expand-collapse-list").addClass("all-open");
                    $("#expand-collapse-list").attr("data-folder-tooltip",wcp_settings.lang.COLLAPSE);
                    $("#expand-collapse-list .text").text(wcp_settings.lang.COLLAPSE);
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

        setDragAndDropElements();
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
        $(".folder-count").text("");
        for (i = 0; i < foldersArray.length; i++) {
            if(foldersArray[i].trash_count == "") {
                foldersArray[i].trash_count = 0;
            }
            $(".jstree-node[id='" + foldersArray[i].term_id + "'] > a.jstree-anchor span.premio-folder-count").text(foldersArray[i].trash_count);
            $(".sticky-folder-"+foldersArray[i].term_id+" .premio-folder-count").text(foldersArray[i].trash_count);
        }

        if($(".wp-filter #media_folder").length) {
            for (var i = 0; i < foldersArray.length; i++) {
                if($(".wp-filter #media_folder option[value='"+foldersArray[i].slug+"']").length) {
                    $(".wp-filter #media_folder option[value='"+foldersArray[i].slug+"']").html(foldersArray[i].name + " (" + foldersArray[i].trash_count + ")");
                }
            }
        }
        $("span.premio-folder-count").each(function(){
            if($(this).text() == "") {
                $(this).text(0);
            }
        });

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

        uploadFolderID = 0;
    }

    $(document).ready(function(){

        $(document).on("click", "#remove-from-all-folders:not(.disabled), #remove-from-current-folder:not(.disabled)", function(){
            $("#remove-from-all-folders, #remove-from-current-folder").addClass("disabled");
            var removeFrom = 'all';
            if($(this).hasClass("remove-from-current-folder")) {
                removeFrom = 'current';
            }
            $("#confirm-your-change").hide();
            $.ajax({
                url: wcp_settings.ajax_url,
                data: {
                    post_id: $("#unassigned_folders").val(),
                    action: 'wcp_remove_post_folder',
                    active_folder: activeRecordID,
                    type: wcp_settings.post_type,
                    folder_id: -1,
                    nonce: wcp_settings.nonce,
                    status: wcp_settings.taxonomy_status,
                    taxonomy: activeRecordID,
                    remove_from: removeFrom
                },
                method: 'post',
                success: function (res) {
                    $("#remove-from-all-folders, #remove-from-current-folder").removeClass("disabled");
                    ajaxAnimation();
                    resetMediaAndPosts();
                    checkForUndoFunctionality();
                }
            });
        });

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
                    if($("#bulk-select").val() == "-1") {
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
                            data: {
                                post_ids: chkStr,
                                type: wcp_settings.post_type,
                                action: "wcp_change_multiple_post_folder",
                                folder_id:  $("#bulk-select").val(),
                                nonce: nonce,
                                status: wcp_settings.taxonomy_status,
                                taxonomy: activeRecordID,
                                post_status: wcp_settings.post_status
                            },
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
            alert(wcp_settings.lang.SELECT_ITEMS_TO_MOVE);
        } else {
            $("#bulk-move-folder").show();
            $("#bulk-select").html("<option value=''>"+wcp_settings.lang.LOADING_FILES+"</option>");
            $(".move-to-folder").attr("disabled", true);
            $("#move-to-folder").prop("disabled", true);
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "type=" + wcp_settings.post_type + "&action=wcp_get_default_list&active_id=" + activeRecordID,
                method: 'post',
                success: function (res) {
                    res = $.parseJSON(res);
                    $("#bulk-select").html("<option value=''>"+wcp_settings.lang.SELECT_FOLDER+"</option><option value='-1'>"+wcp_settings.lang.UNASSIGNED+"</option>");
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

    function checkForOtherFolders(folderIDs) {
        var folderID = -1;
        if(activeRecordID == "" || activeRecordID == 0) {
            nonce = wcp_settings.nonce;
            $.ajax({
                url: wcp_settings.ajax_url,
                data: "post_id=" + folderIDs + "&type=" + wcp_settings.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce + "&status=" + wcp_settings.taxonomy_status + "&taxonomy=" + activeRecordID,
                method: 'post',
                success: function (res) {
                    // window.location.reload();
                    resetMediaAndPosts();
                    checkForUndoFunctionality();
                }
            });
        } else {
            $.ajax({
                url: wcp_settings.ajax_url,
                data: {
                    post_id: folderIDs,
                    action: 'premio_check_for_other_folders',
                    active_folder: activeRecordID,
                    type: wcp_settings.post_type,
                    folder_id: folderID,
                    nonce: wcp_settings.nonce,
                    status: wcp_settings.taxonomy_status,
                    taxonomy: activeRecordID
                },
                method: 'post',
                success: function (res) {
                    res = $.parseJSON(res);
                    if(res.status == -1) {
                        $("#unassigned_folders").val(res.data.post_id);
                        $("#confirm-your-change").show();
                    } else {
                        resetMediaAndPosts();
                        checkForUndoFunctionality();
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
                            if(wp1.media.frame.content.get() !== null && typeof(wp1.media.frame.content.get().collection) != "undefined") {
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
            if(days) {
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
            $("button.button.media-button.select-mode-toggle-button").after("<button class='button organize-button'>"+wcp_settings.lang.BULK_ORGANIZE+"</button>");
            $(".media-toolbar-secondary").append("<span class='media-info-message'>"+wcp_settings.lang.DRAG_AND_DROP+"</span>");
            $(".delete-selected-button").before("<button type='button' class='button button-primary select-all-item-btn'>"+wcp_settings.lang.SELECT_ALL+"</button>");
            $(".media-toolbar-secondary").after("<div class='custom-media-select'>"+wcp_settings.lang.MOVE_SELECTED_FILES+" <select class='media-select-folder'></select></div>");
            $(".media-toolbar").append("<div style='clear:both;'></div><div class='media-folder-loader'><span>"+wcp_settings.lang.UPLOADING_FILES+"</span> <span id='current_upload_files'></span>/<span id='total_upload_files'></span><div class='folder-progress'><div class='folder-meter orange-bg'><span></span></div></div></div>");
            if($(".wcp-custom-form").length) {
                if(wp.Uploader !== undefined) {
                    wp.Uploader.queue.on('reset', function () {
                        resetMediaData(1);
                    });
                }
                $(document).ajaxComplete(function (ev, jqXHR, settings) {
                    actionName = settings.data;
                    if(typeof actionName != "undefined") {
                        if(actionName.length && actionName.indexOf("action=delete-post&id=") == 0) {
                            resetMediaData(0);
                        }
                    }
                });
            }
            setTimeout(function () {
                docReferrar = document.referrer;
                if(docReferrar.indexOf("wp-admin/upload.php") != -1) {
                    mediaMode = getCookie("media-select-mode");
                    if(mediaMode == "on") {
                        $("button.button.media-button.select-mode-toggle-button").trigger("click");
                        //$(".attachments-browser li.attachment").draggable("enable");

                        if($(".media-frame").hasClass("mode-select")) {
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
                if($(".media-toolbar").hasClass("media-toolbar-mode-select")) {
                    if($("ul.attachments li.selected").length == 0) {
                        $(".custom-media-select").removeClass("active");
                    } else {
                        $(".custom-media-select").addClass("active");
                    }
                }
            }
        });

        $(document).on("change", ".media-select-folder", function () {
            if(wcp_settings.post_type == "attachment") {
                if($(this).val() != "") {
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
                            data: {
                                post_ids: chkStr,
                                type: wcp_settings.post_type,
                                action: "wcp_change_multiple_post_folder",
                                folder_id:   $(this).val(),
                                nonce: nonce,
                                status: wcp_settings.taxonomy_status,
                                taxonomy: activeRecordID,
                                post_status: wcp_settings.post_status
                            },
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
    });

    $(document).ready(function(){
        if(wcp_settings.use_shortcuts == "yes") {
            $(document).on("click", ".view-shortcodes", function (e) {
                e.preventDefault();
                $("#keyboard-shortcut").show();
            });

            $(document).keydown(function (e) {
                var isCtrlPressed = (e.ctrlKey || e.metaKey) ? true : false;

                // Alt + N : New Folder
                if(!($("input").is(":focus") || $("textarea").is(":focus"))) {
                    if (e.altKey && (e.keyCode == 78 || e.which == 78)) {
                        e.preventDefault();
                        $("#add-new-folder").trigger("click");
                    }
                }
                // F2 Rename Folder
                if(e.keyCode == 113 || e.which == 113) {
                    if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        fileFolderID = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                        updateFolder();
                        $(".dynamic-menu").remove();
                    }
                }

                // Ctrl+C/CMD+C: Copy Folder
                if(isCtrlPressed && (e.keyCode == 67 || e.which == 67)) {
                    /*if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        isFolderCopy = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                        CPCAction = "copy";
                        $(".folders-undo-notification").removeClass("active");
                        $("#copy-message").addClass("active");
                        setTimeout(function () {
                            $("#copy-message").removeClass("active");
                        }, 5000);
                        checkForCopyPaste();
                    }*/
                }

                // Ctrl+X/CMD+X: Cut Folder
                if(isCtrlPressed && (e.keyCode == 88 || e.which == 88)) {
                    /*if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        e.preventDefault();
                        isFolderCopy = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                        CPCAction = "cut";
                        $(".folders-undo-notification").removeClass("active");
                        $("#cut-message").addClass("active");
                        setTimeout(function () {
                            $("#cut-message").removeClass("active");
                        }, 5000);
                        checkForCopyPaste();
                    }*/
                }

                // Ctrl+V: Paste Folder
                if(isCtrlPressed && (e.keyCode == 86 || e.which == 86)) {
                    /*if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        e.preventDefault();
                        activeRecordID = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                        if(activeRecordID == "" || isNaN(activeRecordID)) {
                            activeRecordID = 0;
                        }
                        if(isFolderCopy != 0 && isFolderCopy != "" && isFolderCopy != activeRecordID) {
                            if(CPCAction == "cut") {
                                lastParentID = $("#" + isFolderCopy).data("parent");
                                lastCopiedFolder = isFolderCopy;
                                lastFolderOrder = $("#" + isFolderCopy).index() + 1;
                                if(activeRecordID != "" && activeRecordID != 0) {
                                    $('#js-tree-menu').jstree("move_node", "#" + isFolderCopy, "#" + activeRecordID, 0);
                                } else {
                                    $('#js-tree-menu').jstree("move_node", "#" + isFolderCopy, "#", $("#js-tree-menu > ul > li.jstree-node").length);
                                }
                                $(".folders-undo-notification").removeClass("active");
                                $("#paste-message").addClass("active");
                                setTimeout(function () {
                                    $("#paste-message").removeClass("active");
                                }, 5000);
                            } else {
                                if(activeRecordID == "" || isNaN(activeRecordID)) {
                                    activeRecordID = 0;
                                }
                                copyFolders(isFolderCopy, activeRecordID);
                            }
                            checkForCopyPaste();
                            CPCAction = "";
                            isFolderCopy = 0;
                        }
                    }*/
                }

                if(isCtrlPressed && (e.keyCode == 75 || e.which == 75)) {
                    $("#keyboard-shortcut").show();
                }

                // delete action
                if((e.keyCode == 46 || e.which == 46) || (e.keyCode == 8 || e.which == 8)) {
                    if ($("#menu-checkbox").is(":checked") && $("#js-tree-menu input.checkbox:checked").length > 0) {
                        $(".delete-folder-action").trigger("click");
                    } else if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        if(!$("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").hasClass("is-locked")) {
                            fileFolderID = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                            removeFolderFromID(0);
                            $(".dynamic-menu").remove();
                            $(".active-menu").removeClass("active-menu");
                        }
                    }
                }

                // ctrl + down
                if(isCtrlPressed && (e.keyCode == 40 || e.which == 40)) {
                    if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        fileFolderID = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                        var lastParent = parseInt($("#"+fileFolderID).data("parent"));
                        var folderOrder = parseInt($("#"+fileFolderID).index())+1;
                        var dataChild = parseInt($("#"+fileFolderID).data("child"));
                        if(isNaN(lastParent)) {
                            lastParent = ($("li#" + fileFolderID).parents("li.jstree-node").length)?$("li#" + fileFolderID).parents("li.jstree-node").data("folder"):0;
                            dataChild = ($("li#" + fileFolderID).parents("li.jstree-node").length)?$("li#" + fileFolderID).parents("li.jstree-node").children():($("#js-tree-menu > ul > li").length);
                        }
                        if(lastParent == 0) {
                            lastParent = "";
                        }
                        if(dataChild == folderOrder) {
                            $('#js-tree-menu').jstree("move_node", "#"+fileFolderID, "#"+lastParent, 0);
                        } else {
                            $('#js-tree-menu').jstree("move_node", "#"+fileFolderID, "#"+lastParent, folderOrder+1);
                        }
                    }
                }

                // ctrl + down
                if(isCtrlPressed && (e.keyCode == 38 || e.which == 38)) {
                    if($("#js-tree-menu .jstree-anchor").is(":focus")) {
                        fileFolderID = $("#js-tree-menu .jstree-anchor:focus").closest("li.jstree-node").attr("id");
                        var lastParent = parseInt($("#" + fileFolderID).data("parent"));
                        var folderOrder = parseInt($("#" + fileFolderID).index()) - 1;
                        var dataChild = parseInt($("#" + fileFolderID).data("child"));
                        if(isNaN(lastParent)) {
                            folderOrder = parseInt($("#" + fileFolderID).index()) - 1;
                            lastParent = ($("li#" + fileFolderID).parents("li.jstree-node").length)?$("li#" + fileFolderID).parents("li.jstree-node").data("folder"):0;
                            dataChild = ($("li#" + fileFolderID).parents("li.jstree-node").length)?$("li#" + fileFolderID).parents("li.jstree-node").children():($("#js-tree-menu > ul > li").length);
                        }
                        if (lastParent == 0) {
                            lastParent = "";
                        }
                        if (folderOrder == -1) {
                            $('#js-tree-menu').jstree("move_node", "#" + fileFolderID, "#" + lastParent, dataChild);
                        } else {
                            $('#js-tree-menu').jstree("move_node", "#" + fileFolderID, "#" + lastParent, folderOrder);
                        }
                    }
                }

                // esc key
                if(e.keyCode == 27 || e.which == 27) {
                    $(".folder-popup-form:not(#rating-modal-popup)").hide();
                    if($(".jstree-node[id='"+fileFolderID+"']").length) {
                        $(".jstree-node[id='"+fileFolderID+"'] > a.jstree-anchor").trigger("focus");
                    } else if($("#js-tree-menu .jstree-anchor.jstree-clicked").length) {
                        $("#js-tree-menu .jstree-anchor.jstree-clicked").trigger("focus");
                    }
                }
            });
        }
    });
}));
