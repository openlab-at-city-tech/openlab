(function($) {
    "use strict";
    var selectedFolderMediaId = -1;
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
    var folderCurrentURL = folders_media_options.page_url;
    var activeRecordID = "";
    var folderIDs = "";
    var isMultipleRemove = false;
    var isItFromMedia = false;
    var isDuplicate = false;
    var duplicateFolderId = 0;
    var $action_form;
    var lastOrderStatus = "";
    var ajaxURL = "";
    var folderOrder = 0;
    var MediaLibraryOrganizerTaxonomyFilter = wp.media.view.AttachmentFilters.extend({
        id: 'media-attachment-taxonomy-filter',
        createFilters: function() {
            var filters = {};
            var totalItems = folders_media_options.terms.length;
            _.each(folders_media_options.terms || {}, function(term, index) {
                filters[term.term_id] = {
                    text: term.name + ' (' + term.count + ')',
                    props: {
                        'media_folder': term.slug
                    }
                };
            });
            filters.all = {
                text: 'All Folders',
                props: {
                    'media_folder': ''
                },
                priority: 10
            };
            filters.unassigned = {
                text: '(Unassigned)',
                props: {
                    'media_folder': "-1"
                },
                priority: 10
            };
            this.filters = filters;
        },
        change: function() {
            var filters = {};
            _.each(folders_media_options.terms || {}, function(term, index) {
                filters[term.term_id] = {
                    text: term.name + ' (' + term.count + ')',
                    props: {
                        'media_folder': term.slug
                    }
                };
            });
            filters.all = {
                text: 'Select a folder >>',
                props: {
                    'media_folder': ''
                },
                priority: 10
            };
            filters.unassigned = {
                text: '(Unassigned)',
                props: {
                    'media_folder': "-1"
                },
                priority: 10
            };
            this.filters = filters;
            var filter = this.filters[ this.el.value ];
            if ( filter ) {
                this.model.set( filter.props );
            }
        }
    });


    var selectedFolderMediaId = -1;
    var selectedFolderPageID = "all";
    var filesInQueue = 0;
    var uploadedFileCount = 0;
    var lastFolderData = [];

    var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
    wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
        createToolbar: function() {
            AttachmentsBrowser.prototype.createToolbar.call(this);
            this.toolbar.set('MediaLibraryOrganizerTaxonomyFilter', new MediaLibraryOrganizerTaxonomyFilter({
                controller: this.controller,
                model: this.collection.props,
                priority: -75
            }).render());
        }
    });

    var Query = wp.media.model.Query;
    _.extend(Query, {
        get: (function() {
            var queries = [];
            return function(props, options) {
                var args = {},
                    orderby = Query.orderby,
                    defaults = Query.defaultProps,
                    query,
                    cache = false; // Always disable query
                delete props.query;
                delete props.cache;
                _.defaults(props, defaults);

                _.each(['include', 'exclude'], function(prop) {
                    if (props[prop] && !_.isArray(props[prop])) {
                        props[prop] = [props[prop]];
                    }
                });
                _.each(props, function(value, prop) {
                    if (_.isNull(value)) {
                        return;
                    }
                    args[Query.propmap[prop] || prop] = value;
                });
                _.defaults(args, Query.defaultArgs);
                args.orderby = orderby.valuemap[props.orderby] || props.orderby;
                // Disable query caching
                cache = false;
                // Search the query cache for a matching query.
                if (cache) {
                    query = _.find(queries, function(query) {
                        return _.isEqual(query.args, args);
                    });
                } else {
                    queries = [];
                }
                // Otherwise, create a new query and add it to the cache.
                if (!query) {
                    query = new Query([], _.extend(options || {}, {
                        props: props,
                        args: args
                    }));
                    queries.push(query);
                }
                return query;
            };
        }())
    });


    var wpMediaObj = window.wp;
    if (typeof wpMediaObj !== 'undefined' && typeof wpMediaObj.Uploader === 'function') {
        wpMediaObj.media.view.Modal.prototype.on('open', function() {
            setTimeout(function(){
                if($("#media-attachment-taxonomy-filter").length) {
                    if($("#media-attachment-taxonomy-filter").val() == "all") {
                        $("#media-attachment-taxonomy-filter option:gt(1)").remove();
                        _.each(folders_media_options.terms, function(term, index){
                            $("#media-attachment-taxonomy-filter").append("<option value='" + term.term_id + "'>" + term.name + " (" + term.count + ")</option>")
                        });
                    }

                    if(!jQuery("#wcp-content").length) {
                        if(jQuery(".editor-post-featured-image").length) {
                            jQuery(".attachment-filters").val(selectedFolderPageID).trigger("change");
                        }
                    }
                }
            },100);
        });
        jQuery.extend(wpMediaObj.Uploader.prototype, {
            progress: function () {
                
            },
            init: function () {
                if (this.uploader) {
                    this.uploader.bind('FileFiltered', function (up, file) {
                        filesInQueue++;
                        $(".folder-meter").css("width", "0%");
                        $(".media-folder-loader").show();
                        $("#total_upload_files").text(filesInQueue);
                    });
                    this.uploader.bind('BeforeUpload', function (uploader, file) {
                        var folder_id = selectedFolderMediaId;
                        var params = uploader.settings.multipart_params;
                        folder_id = parseInt(folder_id);
                        if (folder_id > 0) {
                            params.folder_for_media = folder_id;
                        }
                        if(uploadedFileCount < filesInQueue) {
                            $(".media-folder-loader").show();
                            var progress_width = uploadedFileCount/filesInQueue*100;
                            $(".folder-meter").css("width", progress_width+"%");
                        }
                        uploadedFileCount++;
                        $("#current_upload_files").text(uploadedFileCount);

                    });
                    this.uploader.bind('UploadComplete', function (up, files) {
                        selectedFolderMediaId = -1;
                    });
                    this.uploader.bind('UploadComplete', function (up, files) {
                        var wp_media = window.wp;

                        $(".folder-meter").css("width", "100%");
                        setTimeout(function(){
                            $(".media-folder-loader").hide();
                            $(".folder-meter").css("width", "0%");
                            filesInQueue = 0;
                            uploadedFileCount = 0;
                        }, 1250);

                        resetDDCounter();
                        if(typeof wp_media.media.frame !== "undefined" && wp_media.media.frame.content.get() !== null) {
                            wp_media.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
                            wp_media.media.frame.content.get().options.selection.reset();
                        } else {
                            //wp_media.media.frame.library.props.set ({ignore: (+ new Date())});
                            if($("#media-attachment-taxonomy-filter").length) {
                                $(".attachment-filters").each(function(){
                                    $(this).trigger("change");
                                });
                            }
                        }
                    });
                }
            }
        });
    }

    var wpMedia = window.wp;
    if (typeof wpMedia !== 'undefined' && typeof wpMedia.Uploader === 'function') {
        var windowMedia = window.wp.media;
        var windowModal = windowMedia.view.Modal
        windowMedia.view.Modal = windowMedia.view.Modal.extend({
            className: "folder-modal",
            initialize: function () {
                windowModal.prototype.initialize.apply(this, arguments);
            }, open: function () {
                $(".folder-modal").removeClass("folder-modal");
                if (windowModal.prototype.open.apply(this, arguments)) {
                    console.log("media frame open");
                    if($(".folder-modal").length) {
                        $(".folder-custom-menu").remove();
                        $(".folder-modal .media-frame-tab-panel").removeClass("has-folder-menu");
                        if($(".folder-modal .media-frame").hasClass("hide-menu")) {
                            if (!$(".folder-custom-menu").length) {
                                $(".folder-modal .media-frame-tab-panel").before("<div class='folder-custom-menu'><div class='folder-menu-content'></div></div>");
                                $(".folder-modal .folder-menu-content").load(folders_media_options.media_page_url + " #wcp-content-resize", function () {
                                    checkForExpandCollapse();
                                });
                            }
                        } else {
                            if (!$(".folder-custom-menu").length) {
                                $(".folder-modal .media-frame-menu").addClass("has-folder-menu");
                                $(".folder-modal .media-frame-menu .media-menu").append("<div class='folder-custom-menu'><div class='folder-menu-content'></div></div>");
                                $(".folder-modal .folder-menu-content").load(folders_media_options.media_page_url + " #wcp-content-resize", function () {
                                    checkForExpandCollapse();
                                });
                            }
                        }

                        $(".folder-form-data").remove();
                        $(".media-frame-tab-panel:first").before("<div class='folder-form-data'></div>");
                        $(".folder-form-data").load(folders_media_options.media_page_url+ " #folder-add-update-content", function(){ });
                    }
                }
            }, close: function () {
                windowModal.prototype.close.apply(this, arguments);
                $(".folder-modal").removeClass("folder-modal");
            }
        })
        // wpMedia.media.view.Modal.prototype.on('open', function() {
        //     $(".folder-custom-menu").remove();
        //     if(!$(".folder-custom-menu").length) {
        //         $(".media-frame-tab-panel").before("<div class='folder-custom-menu'><div class='folder-menu-content'></div></div>");
        //         $(".folder-menu-content").load(folders_media_options.media_page_url+ " #wcp-content-resize", function(){
        //             console.log("content loaded from media page");
        //             checkForExpandCollapse();
        //         });
        //         $(".media-frame-tab-panel").before("<div class='folder-form-data'></div>");
        //         $(".folder-form-data").load(folders_media_options.media_page_url+ " #folder-add-update-content", function(){
        //             console.log("content loaded from media page");
        //         });
        //
        //         resetMediaData(0);
        //     }
        // });
    }

    $(document).ready(function(){

        isKeyActive = parseInt(folders_media_options.is_key_active);
        n_o_file = parseInt(folders_media_options.folders);

        $(document).on("click", ".header-posts a.all-posts", function(e){
            fileFolderID = 0;
            activeRecordID = "";
            $(".active-item").removeClass("active-item");
            $(this).addClass("active-item");
            $(this).closest(".media-frame").find("#media-attachment-taxonomy-filter").val("all").trigger("change");
            $(this).closest(".media-frame").find(".folder_for_media").val("all").trigger("change");
        });

        $(document).on("click", ".un-categorised-items", function(e){
            fileFolderID = 0;
            activeRecordID = "";
            $(".active-item").removeClass("active-item");
            $(this).addClass("active-item");
            $(this).closest(".media-frame").find("#media-attachment-taxonomy-filter").val("unassigned").trigger("change");
            $(this).closest(".media-frame").find(".folder_for_media").val("-1").trigger("change");
        });

        $(document).on("click", "h3.title", function(e){
            fileFolderID = $(this).closest("li.route").data("folder-id");
            activeRecordID = fileFolderID;
            $(".active-item").removeClass("active-item");
            $(this).closest("li.route").addClass("active-item");
            $(this).closest(".media-frame").find("#media-attachment-taxonomy-filter").val(fileFolderID).trigger("change");
            $(this).closest(".media-frame").find(".folder_for_media").val(fileFolderID).trigger("change");
        });

        $(document).on("change", ".folder_for_media", function(){
            if($(this).val() != "add-folder" && $(this).val() != null) {
                selectedFolderMediaId = $(this).val();
            } else if($(this).val() == "add-folder") {
                selectedFolderMediaId = -1;
            }
        });

        /* right click menu */
        $(document).on("click", ".update-inline-record", function(e){
            e.stopPropagation();
            var isHigh = $(this).closest("li.route").hasClass("is-high");
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            var menuHtml = "<div class='dynamic-menu'><ul>" +
                "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span> New Folder</a></li>" +
                "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span> Rename</a></li>" +
                "<li class='sticky-folder'><a target='_blank' href='"+folders_media_options.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>Sticky Folder (Pro)</a></li>" +
                "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? " Remove Star" : "Add a Star") + "</a></li>" +
                "<li class='duplicate-folder'><a href='javascript:;'><span class=''><i class='pfolder-clone'></i></span> Duplicate folder</a></li>";

            var hasPosts = parseInt($(this).closest("li.route").find("h3.title:first > .total-count").text());
            if(folders_media_options.post_type == "attachment" && hasPosts) {
                menuHtml += "<li class='download-folder'><a target='_blank' href='"+folders_media_options.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span> Download Zip (Pro)</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span> Delete</a></li>" +
                "</ul></div>";
            $(this).closest("h3.title").after(menuHtml);
            $(this).parents("li.route").addClass("active-menu");

            if(($(this).closest("h3.title").offset().top + $(".dynamic-menu").height()) > ($(window).height() - 20)) {
                $(".dynamic-menu").addClass("bottom-fix");

                if($(".dynamic-menu.bottom-fix").offset().top < $("#custom-scroll-menu").offset().top) {
                    $(".dynamic-menu").removeClass("bottom-fix");
                }
            }
        });

        $(document).on("click", "body, html", function(e){
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            $(".folder-order").removeClass("active");
        });
        $(document).on("click", ".dynamic-menu, .folder-order", function(e){
            e.stopPropagation();
        });

        $(document).on("contextmenu", "h3.title", function(){
            e.preventDefault();
            if(folders_media_options.can_manage_folder == 0) {
                return false;
            }
            var isHigh = $(this).closest("li.route").hasClass("is-high");
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            var menuHtml = "<div class='dynamic-menu'><ul>" +
                "<li class='new-folder'><a href='javascript:;'><span class=''><i class='pfolder-add-folder'></i></span> New Folder</a></li>" +
                "<li class='rename-folder'><a href='javascript:;'><span class=''><i class='pfolder-edit'></i></span> Rename</a></li>" +
                "<li class='sticky-folder'><a target='_blank' href='"+folders_media_options.upgrade_url+"'><span class='sticky-pin'><i class='pfolder-pin'></i></span>Sticky Folder (Pro)</a></li>" +
                "<li class='mark-folder'><a href='javascript:;'><span class=''><i class='pfolder-star'></i></span>" + ((isHigh) ? " Remove Star" : "Add a Star") + "</a></li>"+
                "<li class='duplicate-folder'><a href='javascript:;'><span class=''><i class='pfolder-clone'></i></span> Duplicate folder</a></li>";

            /* checking for attachments */
            var hasPosts = parseInt($(this).closest("li.route").find("h3.title:first > .total-count").text());
            if(folders_media_options.post_type == "attachment" && hasPosts) {
                menuHtml += "<li class='download-folder'><a target='_blank' href='"+folders_media_options.upgrade_url+"'><span class=''><i class='pfolder-zip-file'></i></span> Download Zip (Pro)</a></li>";
            }
            menuHtml += "<li class='remove-folder'><a href='javascript:;'><span class=''><i class='pfolder-remove'></i></span> Delete</a></li>" +
                "</ul></div>";
            $(this).after(menuHtml);
            $(this).parents("li.route").addClass("active-menu");
            if(($(this).offset().top + $(".dynamic-menu").height()) > ($(window).height() - 20)) {
                $(".dynamic-menu").addClass("bottom-fix");

                if($(".dynamic-menu.bottom-fix").offset().top < $("#custom-scroll-menu").offset().top) {
                    $(".dynamic-menu").removeClass("bottom-fix");
                }
            }
            return false;
        });
        /* right click menu end */

        /* add new folder functionality */
        $(document).on("click", ".new-folder", function(e) {
            e.stopPropagation();
            $(".active-menu").removeClass("active-menu");
            fileFolderID = $(this).closest("li.route").data("folder-id");
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            isItFromMedia = false;
            isDuplicate = false;
            addFolder();
        });

        $(document).on("click", "#add-new-folder", function(e) {
            if($("#custom-menu li.active-item").length) {
                fileFolderID = $("#custom-menu li.active-item").data("folder-id");
            } else {
                fileFolderID = 0;
            }
            isItFromMedia = false;
            addFolder();
        });

        $(document).on("submit", "#save-folder-form", function(e){
            e.stopPropagation();
            e.preventDefault();

            folderNameDynamic = $("#add-update-folder-name").val();

            if($.trim(folderNameDynamic) == "") {
                $(".folder-form-errors").addClass("active");
                $("#add-update-folder-name").focus();
            } else {
                $("#save-folder-data").html('<span class="dashicons dashicons-update"></span>');
                $("#add-update-folder").addClass("disabled");

                var ajax_url = "parent_id=" + fileFolderID + "&type=" + folders_media_options.post_type + "&action=wcp_add_new_folder&nonce=" + folders_media_options.nonce + "&term_id=" + fileFolderID + "&order=" + folderOrder + "&name=" + folderNameDynamic+"&is_duplicate="+isDuplicate+"&duplicate_from="+duplicateFolderId;
                if(isItFromMedia) {
                    ajax_url = "parent_id=0&type=" + folders_media_options.post_type + "&action=wcp_add_new_folder&nonce=" + folders_media_options.nonce + "&term_id=0&order=" + folderOrder + "&name=" + folderNameDynamic+"&is_duplicate="+isDuplicate+"&duplicate_from="+duplicateFolderId;
                }

                jQuery.ajax({
                    url: folders_media_options.ajax_url,
                    data: ajax_url,
                    method: 'post',
                    success: function (res) {
                        var result = jQuery.parseJSON(res);
                        if (result.status == '1') {
                            $("#space_" + result.parent_id).append(result.term_data);
                            $("#wcp_folder_" + result.parent_id).addClass("active has-sub-tree");
                            isKeyActive = parseInt(result.is_key_active);
                            n_o_file = parseInt(result.folders);
                            $("#current-folder").text(n_o_file);
                            $("#ttl-fldr").text((4*4)-(2*2)-2);
                            checkForExpandCollapse();
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            ajaxAnimation();
                            fileFolderID = result.term_id;
                            resetMediaData(0);
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

        $(document).on("click", ".form-cancel-btn", function(){
            $(".folder-popup-form").hide();
        });
        $(document).on("click", ".folder-popup-form", function (e) {
            $(".folder-popup-form").hide();
        });
        $(document).on("click", ".popup-form-content", function (e) {
            e.stopPropagation();
        });

        document.onkeydown = function(evt) {
            evt = evt || window.event;
            var isEscape = false;
            if ("key" in evt) {
                isEscape = (evt.key === "Escape" || evt.key === "Esc");
            } else {
                isEscape = (evt.keyCode === 27);
            }
            if (isEscape) {
                $(".folder-popup-form").hide();
            }
        };

        /* Update folder */
        $(document).on("click", ".rename-folder", function (e) {
            e.stopPropagation();
            fileFolderID = $(this).closest("li.route").data("folder-id");
            updateFolder();
        });

        $(document).on("click", "#inline-update", function (e) {
            if($("#custom-menu li.active-item").length) {
                fileFolderID = $("#custom-menu li.active-item").data("folder-id");
                updateFolder();
            }
        });

        $(document).on("submit", "#update-folder-form", function(e){
            e.stopPropagation();
            e.preventDefault();
            $(".dynamic-menu").hide();
            folderNameDynamic = $("#update-folder-item-name").val();

            if($.trim(folderNameDynamic) == "") {
                $(".folder-form-errors").addClass("active");
                $("#update-folder-item-name").focus();
            } else {
                $("#update-folder-data").html('<span class="dashicons dashicons-update"></span>');
                $("#update-folder-item").addClass("disabled");

                nonce = $.trim($("#wcp_folder_" + fileFolderID).data("rename"));
                var parentID = $("#wcp_folder_" + fileFolderID).closest("li.route").data("folder-id");
                if (parentID == undefined) {
                    parentID = 0;
                }
                jQuery.ajax({
                    url: folders_media_options.ajax_url,
                    data: "parent_id=" + parentID + "&nonce=" + nonce + "&type=" + folders_media_options.post_type + "&action=wcp_update_folder&term_id=" + fileFolderID + "&name=" + folderNameDynamic,
                    method: 'post',
                    success: function (res) {
                        var result = jQuery.parseJSON(res);
                        if (result.status == '1') {
                            $("#wcp_folder_" + result.id + " > h3 > .title-text").text(result.term_title);
                            $("#wcp_folder_" + result.id + " > h3").attr("title", result.term_title);
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            ajaxAnimation();
                            resetMediaData(0);
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
        
        /* Mark Folder */
        $(document).on("click", ".mark-folder", function(e){
            e.stopPropagation();
            folderID = $(this).closest("li.route").data("folder-id");
            nonce = $.trim($("#wcp_folder_"+folderID).data("star"));
            $(".form-loader-count").css("width","100%");
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
            jQuery.ajax({
                url: folders_media_options.ajax_url,
                data: "term_id=" + folderID + "&type=" + folders_media_options.post_type + "&action=wcp_mark_un_mark_folder&nonce="+nonce,
                method: 'post',
                cache: false,
                success: function (res) {
                    var res = jQuery.parseJSON(res);
                    $(".form-loader-count").css("width","0%");
                    if (res.status == '1') {
                        if(res.marked == '1') {
                            $("#wcp_folder_"+res.id).addClass("is-high");
                        } else {
                            $("#wcp_folder_"+res.id).removeClass("is-high");
                        }
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

        /* Duplicate Folder */
        $(document).on("click", ".duplicate-folder", function(e){
            e.stopPropagation();
            $(".active-menu").removeClass("active-menu");
            fileFolderID = $(this).closest("li.route").data("folder-id");
            $(".dynamic-menu").remove();
            isDuplicate = true;
            addFolder();
        });

        /* Remove folder */
        $(document).on("click", ".remove-folder", function(e){
            folderID = $(this).closest("li.route").data("folder-id");
            fileFolderID = folderID;
            removeFolderFromID(0);
            $(".dynamic-menu").remove();
            $(".active-menu").removeClass("active-menu");
        });
        
        $(document).on("click", "#remove-folder-item", function (e){
            e.stopPropagation();
            $(".folder-popup-form").addClass("disabled");
            $("#remove-folder-item").html('<span class="dashicons dashicons-update"></span>');
            nonce = $.trim($("#wcp_folder_"+fileFolderID).data("delete"));
            if(isMultipleRemove) {
                removeMultipleFolderItems();
            } else {
                jQuery.ajax({
                    url: folders_media_options.ajax_url,
                    data: "type=" + folders_media_options.post_type + "&action=wcp_remove_folder&term_id=" + fileFolderID + "&nonce=" + nonce,
                    method: 'post',
                    success: function (res) {
                        var res = jQuery.parseJSON(res);
                        if (res.status == '1') {
                            $("#wcp_folder_" + fileFolderID).remove();
                            $("#folder_" + fileFolderID).remove();
                            isKeyActive = parseInt(res.is_key_active);
                            n_o_file = parseInt(res.folders);
                            $("#current-folder").text(n_o_file);
                            $("#ttl-fldr").text((3*3)+(4/(2*2)));
                            ajaxAnimation();
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");

                            if (activeRecordID == fileFolderID) {
                                jQuery(".header-posts a").trigger("click");
                            }
                            resetDDCounter();
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

        $(document).on("click", "#sort-order-list", function(e){
            e.stopPropagation();
            $(".folder-order").toggleClass("active");
        });

        $(document).on("click", "#expand-collapse-list", function (e){
            e.stopPropagation();
            var statusType = 0;
            if($(this).hasClass("all-open")) {
                $(this).removeClass("all-open");
                $(".has-sub-tree").removeClass("active");
                statusType = 0;
                $(this).attr("data-folder-tooltip","Expand");
            } else {
                $(this).addClass("all-open");
                statusType = 1;
                $(".has-sub-tree").addClass("active");
                $(this).attr("data-folder-tooltip","Collapse");
            }
            folderIDs = "";
            $(".has-sub-tree").each(function(){
                folderIDs += $(this).data("folder-id")+",";
            });
            if(folderIDs != "") {
                $(".form-loader-count").css("width","100%");
                nonce = folders_media_options.nonce;
                jQuery.ajax({
                    url: folders_media_options.ajax_url,
                    data: "type=" + folders_media_options.post_type + "&action=wcp_change_all_status&status=" + statusType + "&folders="+folderIDs+"&nonce="+nonce,
                    method: 'post',
                    success: function (res) {
                        $(".form-loader-count").css("width","0");
                        res = jQuery.parseJSON(res);
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

        $(document).on("click", ".folder-sort-menu a", function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(".form-loader-count").css("width", "100%");
            $(".folder-order").removeClass("active");
            lastOrderStatus = $(this).attr("data-sort");
            jQuery.ajax({
                url: folders_media_options.ajax_url,
                data: "type=" + folders_media_options.post_type + "&action=wcp_folders_by_order&nonce=" + folders_media_options.nonce+"&order="+$(this).attr("data-sort"),
                method: 'post',
                success: function (res) {
                    res = jQuery.parseJSON(res);
                    if(res.status == 1) {
                        $("#space_0").html(res.data);
                    }
                    $(".form-loader-count").css("width", "0");
                }
            });
        });
        
        $('.space').livequery(function(){
            $(this).sortable({
                placeholder: "ui-state-highlight",
                connectWith:'.space',
                tolerance:'intersect',
                over:function(event,ui){

                },
                update: function( event, ui ) {
                    var thisId = ui.item.context.attributes['data-folder-id'].nodeValue;
                    var orderString = "";
                    $(this).children().each(function(){
                        if($(this).hasClass("route")) {
                            orderString += $(this).data("folder-id")+",";
                        }
                    });
                    if(orderString != "") {
                        $(".form-loader-count").css("width","100%");
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "term_ids=" + orderString + "&action=wcp_save_folder_order&type=" + folders_media_options.post_type+"&nonce="+folders_media_options.nonce,
                            method: 'post',
                            success: function (res) {
                                res = jQuery.parseJSON(res);
                                if (res.status == '1') {
                                    $("#wcp_folder_parent").html(res.options);
                                    $(".form-loader-count").css("width", "0");
                                    resetMediaAndPosts();
                                    ajaxAnimation();
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
                },
                receive: function (event, ui) {
                    calcWidth($(this).siblings('.title'));
                    $(this).closest("li.route").addClass("active");
                    $(this).closest("li.route").find("ul.ui-sortable:first-child > li").slideDown();
                    var parentId = $(this).closest("li.route").data("folder-id");
                    var thisId = ui.item.context.attributes['data-folder-id'].nodeValue;
                    if(parentId == undefined) {
                        parentId = 0;
                    }
                    var orderString = "";
                    if($("#wcp_folder_"+parentId+" .ui-sortable li").length) {
                        $("#wcp_folder_"+parentId+" .ui-sortable li").each(function(){
                            orderString += $(this).data("folder-id")+",";
                        });
                    } else if(parentId == 0) {
                        $("#custom-menu > ul.space > li").each(function(){
                            orderString += $(this).data("folder-id")+",";
                        });
                    }
                    $(".form-loader-count").css("width","100%");
                    nonce = $.trim($("#wcp_folder_"+thisId).data("nonce"));
                    jQuery.ajax({
                        url: folders_media_options.ajax_url,
                        data: "term_id=" + thisId + "&action=wcp_update_parent_information&parent_id=" + parentId+"&type=" + folders_media_options.post_type+"&nonce="+nonce,
                        method: 'post',
                        success: function (res) {
                            $(".form-loader-count").css("width","0%");
                            res = jQuery.parseJSON(res);
                            if(res.status == 0) {
                                $(".folder-popup-form").hide();
                                $(".folder-popup-form").removeClass("disabled");
                                $("#error-folder-popup-message").html(res.message);
                                $("#error-folder-popup").show();
                            } else {
                                ajaxAnimation();
                            }
                        }
                    });
                }
            });
            $(this).disableSelection();
        });

        $("h3.title").livequery(function(){
            $(this).droppable({
                accept: ".wcp-move-file, .wcp-move-multiple, .media-frame:not(.hide-router) .attachments-browser li.attachment",
                hoverClass: 'wcp-drop-hover',
                classes: {
                    "ui-droppable-active": "ui-state-highlight"
                },
                drop: function( event, ui ) {
                    folderID = $(this).closest("li.route").data('folder-id');
                    if ( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
                        if($(".wp-list-table input:checked").length) {
                            var chkStr = "";
                            $(".wp-list-table input:checked").each(function(){
                                chkStr += $(this).val()+",";
                            });
                            nonce = $.trim($("#wcp_folder_"+folderID).data("nonce"));
                            jQuery.ajax({
                                url: folders_media_options.ajax_url,
                                data: "post_ids=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
                                method: 'post',
                                success: function (res) {
                                    res = jQuery.parseJSON(res);
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
                        var postID = ui.draggable[0].attributes['data-id'].nodeValue;
                        nonce = $.trim($("#wcp_folder_"+folderID).data("nonce"));
                        chkStr = postID+",";
                        $(".wp-list-table input:checked").each(function(){
                            if($(this).val() != postID) {
                                chkStr += $(this).val() + ",";
                            }
                        });
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID+"&nonce="+nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
                            method: 'post',
                            success: function (res) {
                                res = jQuery.parseJSON(res);
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
                        var chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                        nonce = $.trim($("#wcp_folder_" + folderID).data("nonce"));
                        if ($(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").length > 1) {
                            chkStr = "";
                            $(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").each(function () {
                                chkStr += $(this).data("id") + ",";
                            });
                        }
                        folderIDs = chkStr;
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
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

        $(".media-frame:not(.hide-router) .attachments-browser li.attachment").livequery(function () {
            $(this).draggable({
                revert: "invalid",
                containment: "document",
                helper: function (event, ui) {
                    $(".selected-items").remove();
                    var selectedItems = $(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").length;
                    selectedItems = (selectedItems == 0 || selectedItems == 1) ? "1 Item" : selectedItems + " Items";
                    return $("<div class='selected-items'><span class='total-post-count'>" + selectedItems + " Selected</span></div>");
                },
                start: function( event, ui){
                    $("body").addClass("no-hover-css");
                },
                cursor: "move",
                appendTo: ".media-modal",
                cursorAt: {
                    left: 0,
                    top: 0
                },
                stop: function( event, ui ) {
                    $(".selected-items").remove();
                    $("body").removeClass("no-hover-css");
                }
            });
        });

        $(".un-categorised-items").livequery(function () {
            $(this).droppable({
                accept: ".wcp-move-file, .wcp-move-multiple, .media-frame:not(.hide-router) .attachments-browser li.attachment",
                hoverClass: 'wcp-hover-list',
                classes: {
                    "ui-droppable-active": "ui-state-highlight"
                },
                drop: function (event, ui) {
                    folderID = -1;
                    nonce = folders_media_options.nonce;
                    if (ui.draggable.hasClass('wcp-move-multiple')) {
                        if ($(".wp-list-table input:checked").length) {
                            var chkStr = "";
                            $(".wp-list-table input:checked").each(function () {
                                chkStr += $(this).val() + ",";
                            });
                            jQuery.ajax({
                                url: folders_media_options.ajax_url,
                                data: "post_id=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
                                method: 'post',
                                success: function (res) {
                                    //window.location.reload();
                                    resetMediaAndPosts();
                                    ajaxAnimation();
                                }
                            });
                        }
                    } else if (ui.draggable.hasClass('wcp-move-file')) {
                        var postID = ui.draggable[0].attributes['data-id'].nodeValue;
                        var chkStr = postID+",";
                        $(".wp-list-table input:checked").each(function () {
                            if(postID != $(this).val()) {
                                chkStr += $(this).val() + ",";
                            }
                        });
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "post_id=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
                            method: 'post',
                            success: function (res) {
                                //window.location.reload();
                                resetMediaAndPosts();
                                ajaxAnimation();
                            }
                        });
                    } else if (ui.draggable.hasClass('attachment')) {
                        var chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                        if ($(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").length > 1) {
                            chkStr = "";
                            $(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").each(function () {
                                chkStr += $(this).data("id") + ",";
                            });
                        }
                        folderIDs = chkStr;
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "post_id=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_remove_post_folder&folder_id=" + folderID + "&nonce=" + nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
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

        $(".tree-structure .folder-item").livequery(function(){
            $(this).droppable({
                accept: ".wcp-move-file, .wcp-move-multiple, .media-frame:not(.hide-router) .attachments-browser li.attachment",
                hoverClass: 'wcp-drop-hover-list',
                classes: {
                    "ui-droppable-active": "ui-state-highlight"
                },
                drop: function( event, ui ) {
                    $("body").removeClass("no-hover-css");
                    folderID = $(this).data('id');
                    if ( ui.draggable.hasClass( 'wcp-move-multiple' ) ) {
                        nonce = $.trim($("#wcp_folder_"+folderID).data("nonce"));
                        if($(".wp-list-table input:checked").length) {
                            var chkStr = "";
                            $(".wp-list-table input:checked").each(function(){
                                chkStr += $(this).val()+",";
                            });
                            jQuery.ajax({
                                url: folders_media_options.ajax_url,
                                data: "post_ids=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
                                method: 'post',
                                success: function (res) {
                                    // window.location.reload();
                                    resetMediaAndPosts();
                                    ajaxAnimation();
                                }
                            });
                        }
                    } else if ( ui.draggable.hasClass( 'wcp-move-file' ) ) {
                        var postID = ui.draggable[0].attributes['data-id'].nodeValue;
                        nonce = $.trim($("#wcp_folder_"+folderID).data("nonce"));
                        var chkStr = postID+",";
                        $(".wp-list-table input:checked").each(function(){
                            if($(this).val() != postID) {
                                chkStr += $(this).val() + ",";
                            }
                        });
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
                            method: 'post',
                            success: function (res) {
                                // window.location.reload();
                                resetMediaAndPosts();
                                ajaxAnimation();
                            }
                        });
                    } else if( ui.draggable.hasClass( 'attachment' ) ){
                        var chkStr = ui.draggable[0].attributes['data-id'].nodeValue;
                        nonce = $.trim($("#wcp_folder_"+folderID).data("nonce"));
                        if($(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").length > 1) {
                            chkStr = "";
                            $(".media-frame:not(.hide-router) .attachments-browser li.attachment.selected").each(function(){
                                chkStr += $(this).data("id")+",";
                            });
                        }
                        jQuery.ajax({
                            url: folders_media_options.ajax_url,
                            data: "post_ids=" + chkStr + "&type=" + folders_media_options.post_type + "&action=wcp_change_multiple_post_folder&folder_id=" + folderID + "&nonce="+nonce+"&status="+folders_media_options.taxonomy_status+"&taxonomy="+activeRecordID,
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

        $(".wcp-move-file").livequery(function(){
            $(this).draggable({
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
        });

        $(".wcp-move-multiple").livequery(function(){
            $(this).draggable({
                revert: "invalid",
                containment: "document",
                helper: function (event, ui) {
                    $(".selected-items").remove();
                    var selectedItems = $("#the-list th input:checked").length;
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
        });

        $(document).on("click", ".nav-icon", function(){
            folderID = $(this).closest("li.route").data("folder-id");
            var folderStatus = 1;
            if($("#wcp_folder_"+folderID).hasClass("active")) {
                folderStatus = 0;
            } else {
                folderStatus = 1;
            }
            $(".form-loader-count").css("width","100%");
            nonce = $.trim($("#wcp_folder_"+folderID).data("nonce"));
            checkForExpandCollapse();
            jQuery.ajax({
                url: folders_media_options.ajax_url,
                data: "is_active=" + folderStatus + "&action=save_wcp_folder_state&term_id=" + folderID+"&nonce="+nonce,
                method: 'post',
                success: function (res) {
                    $(".form-loader-count").css("width","0");
                    var res = jQuery.parseJSON(res);
                    if(res.status == "0") {
                        $(".folder-popup-form").hide();
                        $(".folder-popup-form").removeClass("disabled");
                        $("#error-folder-popup-message").html(res.message);
                        $("#error-folder-popup").show();
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

        $(document).on("keyup", "#folder-search", function(){
            checkForFolderSearch();
        });
        $(document).on("change", "#folder-search", function(){
            checkForFolderSearch();
        });
        $(document).on("blur", "#folder-search", function(){
            checkForFolderSearch();
        });
        
        $(document).on("click","#folder-hide-show-checkbox",function(){
            if($(this).is(":checked")) {
                $("#custom-menu").addClass("show-folder-checkbox");
            } else {
                $("#custom-menu input.checkbox").attr("checked", false);
                $("#custom-menu").removeClass("show-folder-checkbox");
            }
        });

        $(document).on("click","#inline-remove",function(){
            if($("#custom-menu li.active-item").length) {
                fileFolderID = $("#custom-menu li.active-item").data("folder-id");
                removeFolderFromID(1);
                $(".dynamic-menu").remove();
                $(".active-menu").removeClass("active-menu");
            } else {
                if($("#folder-hide-show-checkbox").is(":checked")) {
                    //removeMultipleFolderItems();
                    $(".dynamic-menu").remove();
                    removeFolderFromID(1);
                }
            }
        });

    });

    function removeMultipleFolderItems() {
        if($("#folder-hide-show-checkbox").is(":checked")) {
            if($("#custom-menu input.checkbox:checked").length > 0) {
                var folderIDs = "";
                var activeItemDeleted = false;
                $("#custom-menu input.checkbox:checked").each(function(){
                    folderIDs += $(this).val()+",";
                    if($(this).closest("li.route").hasClass("active-item")) {
                        activeItemDeleted = true;
                    }
                });
                $(".form-loader-count").css("width", "100%");
                jQuery.ajax({
                    url: folders_media_options.ajax_url,
                    data: "type=" + folders_media_options.post_type + "&action=wcp_remove_muliple_folder&term_id=" + folderIDs+"&nonce="+folders_media_options.nonce,
                    method: 'post',
                    success: function (res) {
                        var res = jQuery.parseJSON(res);
                        $(".form-loader-count").css("width", "0px");
                        if (res.status == '1') {
                            isKeyActive = parseInt(res.is_key_active);
                            n_o_file = parseInt(res.folders);
                            $("#current-folder").text(n_o_file);
                            $("#custom-menu input.checkbox:checked").each(function(){
                                $("#wcp_folder_"+$(this).val()).closest("li.route").remove();
                                $("#space"+$(this).val()).remove();
                            });

                            $("#ttl-fldr").text((4*2)+(4/2));
                            // add_menu_to_list();
                            ajaxAnimation();
                            $(".folder-popup-form").hide();
                            $(".folder-popup-form").removeClass("disabled");
                            resetMediaAndPosts();

                            ajaxAnimation();

                            if(!$("#wcp_folder_"+activeRecordID).length) {
                                $(".header-posts a").trigger("click");
                                activeRecordID = 0;
                            }
                        } else {
                            window.location.reload();
                        }
                        $("#folder-hide-show-checkbox").attr("checked", false);
                        $("#custom-menu input.checkbox").attr("checked", false);
                        $("#custom-menu").removeClass("show-folder-checkbox");
                    }
                });
            } else {

            }
        }
    }

    function resetMediaAndPosts() {
        if($(".media-toolbar").hasClass("media-toolbar-mode-select")) {
            if($("ul.attachments li.selected").length) {
                $("ul.attachments li.selected").trigger("click");
                $(".select-mode-toggle-button").trigger("click");
            }
        }
        if(folderIDs != "" && ($("#custom-menu li.active-item").length > 0 || activeRecordID == "-1")) {
            if($("#media-attachment-taxonomy-filter").length) {
                folderIDs = folderIDs.split(",");
                for (var i = 0; i < folderIDs.length; i++) {
                    if(folderIDs[i] != "") {
                        $(".media-frame:not(.hide-router) .attachments-browser li[data-id='"+folderIDs[i]+"']").remove();
                    }
                }
            }
            folderIDs = "";
        }
        if($("#media-attachment-taxonomy-filter").length) {
            resetMediaData(0);
        } else {
            jQuery.ajax({
                url: folders_media_options.ajax_url,
                data: "type=" + folders_media_options.post_type + "&action=get_folders_default_list",
                method: 'post',
                success: function (res) {
                    var res = jQuery.parseJSON(res);
                    $(".header-posts .total-count").text(res.total_items);
                    $(".un-categorised-items .total-count").text(res.empty_items);

                    for (i = 0; i < res.taxonomies.length; i++) {
                        if(!$("#title_"+res.taxonomies[i].term_id+" .total-count").length) {
                            $("#title_"+res.taxonomies[i].term_id+" .star-icon").before("<span class='total-count'></span>");
                        }
                        $("#title_"+res.taxonomies[i].term_id+" .total-count").text(parseInt(res.taxonomies[i].trash_count));

                        if(!$(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").length) {
                            $(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" a").append("<span class='folder-count'></span>")
                        }
                        $(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").text(parseInt(res.taxonomies[i].trash_count));
                    }

                    $("#custom-menu .total-count").each(function(){
                        if(parseInt($(this).text()) == 0) {
                            $(this).remove();
                        }
                    });

                    $(".sticky-folders .folder-count").each(function(){
                        if(parseInt($(this).text()) == 0) {
                            $(this).remove();
                        }
                    });
                }
            });
            $(".folder-loader-ajax").addClass("active");
            if($("#folder-posts-filter").length) {
                $("#folder-posts-filter").load(folderCurrentURL + " #posts-filter", function () {
                    if (folders_media_options.show_in_page == "show" && !$(".tree-structure").length) {
                        $(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    add_active_item_to_list();
                    triggerInlineUpdate();
                });
            } else {
                $("#wpbody").load(folderCurrentURL + " #wpbody-content", false, function (res) {
                    if (folders_media_options.show_in_page == "show" && !$(".tree-structure").length) {
                        $(".wp-header-end").before('<div class="tree-structure"><ul></ul><div class="clear clearfix"></div></div>');
                    }
                    add_active_item_to_list();
                });
            }
        }
    }

    function resetDDCounter() {
        var currentDDVal = $("#media-attachment-taxonomy-filter").val();
        jQuery.ajax({
            url: folders_media_options.ajax_url,
            data: "type=attachment&action=wcp_get_default_list&active_id=0",
            method: 'post',
            success: function (res) {
                var res = jQuery.parseJSON(res);

                if($("#media-attachment-taxonomy-filter").length) {
                    $("#media-attachment-taxonomy-filter").each(function(){
                        folders_media_options.terms = res.taxonomies;
                        var selectedDD = $(this);
                        selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
                        lastFolderData = res.taxonomies;
                        for (var i = 0; i < res.taxonomies.length; i++) {
                            selectedDD.append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].count + ")</option>");
                        }
                        selectedDD.val(currentDDVal).trigger("change");

                        // if(resetMediaID !== false) {
                        //     selectedDD.val(resetMediaID).trigger("change");
                        // }
                    });
                    if($("select.folder_for_media").length) {
                        var selectedVal = $("select.folder_for_media").val();
                        $("select.folder_for_media option:not(:first-child):not(:last-child)").remove();
                        for (var i = 0; i < res.taxonomies.length; i++) {
                            $("select.folder_for_media option:last-child").before("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name +"</option>");
                        }
                        if(selectedVal != "") {
                            $(".folder_for_media").val(selectedVal);
                        }
                    }
                    for (var i = 0; i < res.taxonomies.length; i++) {
                        if(!$("#title_"+res.taxonomies[i].term_id+" .total-count").length) {
                            $("#title_"+res.taxonomies[i].term_id+" .star-icon").before("<span class='total-count'></span>");
                        }
                        $("#title_"+res.taxonomies[i].term_id+" .total-count").text(parseInt(res.taxonomies[i].trash_count));

                        if(!$(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").length) {
                            $(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" a").append("<span class='folder-count'></span>")
                        }
                        $(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").text(parseInt(res.taxonomies[i].trash_count));

                        $("#title_"+res.taxonomies[i].term_id).attr("title", res.taxonomies[i].term_name);
                        $("#title_"+res.taxonomies[i].term_id+" .title-text").html(res.taxonomies[i].term_name);
                    }

                    $("#custom-menu .total-count").each(function(){
                        if(parseInt($(this).text()) == 0) {
                            $(this).remove();
                        }
                    });

                    $(".sticky-folders .folder-count").each(function(){
                        if(parseInt($(this).text()) == 0) {
                            $(this).remove();
                        }
                    });
                }
            }
        });
    }

    function checkForFolderSearch() {

        if($.trim($("#folder-search").val()) != "") {
            $("#custom-menu").addClass("has-filter");
            var searchText = ($.trim($("#folder-search").val())).toLowerCase();
            $("#custom-menu span.title-text").removeClass("has-search-text");
            $("li.route").removeClass("has-search");
            $("#custom-menu span.title-text").each(function(){
                var thisText = ($(this).text()).toLowerCase();
                if(thisText.indexOf(searchText) !== -1) {
                    $(this).addClass("has-search-text");
                    $(this).parents("li.route").addClass("has-search");
                }
            });
        } else {
            $("#custom-menu").removeClass("has-filter");
            $("#custom-menu span.title-text").removeClass("has-search-text");
            $("li.route").removeClass("has-search");
        }
    }

    function removeFolderFromID(popup_type) {
        var removeMessage = "Are you sure you want to delete the selected folder?";
        var removeNotice = "Items in the folder will not be deleted.";
        isMultipleRemove = false;
        if(popup_type == 1) {
            if($("#folder-hide-show-checkbox").is(":checked")) {
                isMultipleRemove = true;
                if($("#custom-menu input.checkbox:checked").length == 0) {
                    $(".folder-popup-form").hide();
                    $(".folder-popup-form").removeClass("disabled");
                    $("#error-folder-popup-message").html("Please select at least one folder to delete");
                    $("#error-folder-popup").show();
                    return;
                } else {
                    if($("#custom-menu input.checkbox:checked").length > 1) {
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

    function updateFolder() {
        var folderName = $.trim($("#wcp_folder_"+fileFolderID+" > h3 > .title-text").text());
        var parentID = $("#wcp_folder_"+fileFolderID).closest("li.route").data("folder-id");
        if(parentID == undefined) {
            parentID = 0;
        }

        $(".dynamic-menu").hide();
        $("#update-folder-data").text("Submit");
        $(".folder-form-errors").removeClass("active");
        $("#update-folder-item-name").val(folderName);
        $("#update-folder-item").removeClass("disabled");
        $("#update-folder-item").show();
        $("#update-folder-item-name").focus();
    }

    function addFolder() {
        isKeyActive = parseInt(folders_media_options.is_key_active);

        if(isKeyActive == 0 && n_o_file >= ((4*4)-(3*3)+(4/4)+(8/(2*2)))) {
            $("#folder-limitation-message").html("You've "+"reached the "+((4*4)-(2*2)-2)+" folder limitation!");
            $("#no-more-folder-credit").show();
            return false;
        }

        $("#add-update-folder-title").text("Add new folder");
        $("#save-folder-data").text("Submit");
        $(".folder-form-errors").removeClass("active");
        $("#add-update-folder-name").val("");
        if(isDuplicate) {
            duplicateFolderId = fileFolderID;
            $("#add-update-folder-name").val($("#title_"+fileFolderID+" .title-text").text() + " #2");
            if($("li#wcp_folder_"+fileFolderID).parent().hasClass("first-space")) {
                fileFolderID = 0;
            } else {
                fileFolderID = $("li#wcp_folder_"+fileFolderID).parent().parent().data("folder-id");
            }
        }

        folderOrder = $("#space_"+fileFolderID+" > li").length+1;
        ajaxURL = folders_media_options.ajax_url+"?parent_id=" + fileFolderID + "&type=" + folders_media_options.post_type + "&action=wcp_add_new_folder&nonce=" + folders_media_options.nonce + "&term_id=" + fileFolderID + "&order=" + folderOrder+"&name=";

        $("#add-update-folder").removeClass("disabled");
        $("#add-update-folder").show();
        $("#add-update-folder-name").focus();
    }

    function checkForExpandCollapse() {
        if(($("#custom-menu .has-sub-tree").length == $("#custom-menu .has-sub-tree.active").length) && $("#custom-menu .has-sub-tree").length) {
            $("#expand-collapse-list").addClass("all-open");
            $("#expand-collapse-list").attr("data-folder-tooltip","Collapse");
        } else {
            $("#expand-collapse-list").removeClass("all-open");
            $("#expand-collapse-list").attr("data-folder-tooltip","Expand");
        }
    }

    function resetMediaData(loadData) {
        jQuery.ajax({
            url: folders_media_options.ajax_url,
            data: "type=" + folders_media_options.post_type + "&action=wcp_get_default_list&active_id="+activeRecordID,
            method: 'post',
            success: function (res) {
                res = jQuery.parseJSON(res);
                // $("#custom-menu > ul#space_0").html(res.data);
                $(".header-posts .total-count").text(res.total_items);
                $(".un-categorised-items .total-count").text(res.empty_items);
                var selectedVal = $("#media-attachment-taxonomy-filter").val();
                if(selectedVal != "all") {
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
                    if($("#media-attachment-taxonomy-filter").length) {
                        folders_media_options.terms = res.taxonomies;
                        var selectedDD = $("#media-attachment-taxonomy-filter");
                        selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
                        $(".media-select-folder").html("<option value=''>Select Folder</option><option value='-1'>(Unassigned)</option>");
                        for (var i = 0; i < res.taxonomies.length; i++) {
                            selectedDD.append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].trash_count + ")</option>");
                            $(".media-select-folder").append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].trash_count + ")</option>");

                            $("#title_"+res.taxonomies[i].term_id).attr("title", res.taxonomies[i].term_name);
                            $("#title_"+res.taxonomies[i].term_id+" .title-text").html(res.taxonomies[i].term_name);
                        }
                        selectedDD.val(selectedVal);
                        $(".media-select-folder").val("");
                    }
                    if($("select.folder_for_media").length) {
                        selectedVal = $("select.folder_for_media").val();
                        $("select.folder_for_media option:not(:first-child):not(:last-child)").remove();
                        for (var i = 0; i < res.taxonomies.length; i++) {
                            $("select.folder_for_media option:last-child").before("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name +"</option>");
                        }
                        if(selectedVal != "") {
                            $(".folder_for_media").val(selectedVal);
                        }
                    }
                    for (var i = 0; i < res.taxonomies.length; i++) {
                        if(!$("#title_"+res.taxonomies[i].term_id+" .total-count").length) {
                            $("#title_"+res.taxonomies[i].term_id+" .star-icon").before("<span class='total-count'></span>");
                        }
                        $("#title_"+res.taxonomies[i].term_id+" .total-count").text(parseInt(res.taxonomies[i].trash_count));

                        if(!$(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").length) {
                            $(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" a").append("<span class='folder-count'></span>")
                        }
                        $(".sticky-folders .sticky-folder-"+res.taxonomies[i].term_id+" .folder-count").text(parseInt(res.taxonomies[i].trash_count));

                        $("#title_"+res.taxonomies[i].term_id).attr("title", res.taxonomies[i].term_name);
                        $("#title_"+res.taxonomies[i].term_id+" .title-text").html(res.taxonomies[i].term_name);
                    }

                    $("#custom-menu .total-count").each(function(){
                        if(parseInt($(this).text()) == 0) {
                            $(this).remove();
                        }
                    });

                    $(".sticky-folders .folder-count").each(function(){
                        if(parseInt($(this).text()) == 0) {
                            $(this).remove();
                        }
                    });
                }
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

    function calcWidth(obj){
        var titles =
            $(obj).siblings('.space').children('.route').children('.title');
        $(titles).each(function(index, element){
            var pTitleWidth = parseInt($(obj).css('width'));
            var leftOffset = parseInt($(obj).siblings('.space').css('margin-left'));
            var newWidth = pTitleWidth - leftOffset;
            if ($(obj).attr('id') == 'title_0'){
                newWidth = newWidth - 10;
            }
            $(element).css({
                'width': newWidth
            });
            calcWidth(element);
        });

    }
})(jQuery, _);