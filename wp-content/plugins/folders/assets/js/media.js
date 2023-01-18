(function() {
    var selectedFolderMediaId = -1;
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
})(jQuery, _);

jQuery(document).on("click", ".media-frame-router .media-menu-item", function(){
    setTimeout(function(){
        if(lastFolderData.length > 0) {
            if(jQuery(".folder_for_media option").length != (lastFolderData.length+2)) {
                var selectedVal = jQuery(".folder_for_media").val();
                var selectedDD = jQuery(".folder_for_media");
                selectedDD.html("<option value='-1'>(Unassigned)</option>");
                for (i = 0; i < lastFolderData.length; i++) {
                    selectedDD.append("<option value='" + lastFolderData[i].term_id + "'>"+lastFolderData[i].name+"</option>");
                }
                selectedDD.append("<option value='add-folder'>+ Create a New Folder</option>");
                if(selectedFolderMediaId != "" && selectedFolderMediaId != null) {
                    selectedDD.val(selectedFolderMediaId);
                } else {
                    selectedDD.val(selectedVal);
                }
            }
        }
    }, 100);
});
var selectedFolderMediaId = -1;
var selectedFolderPageID = "all";
var filesInQueue = 0;
var uploadedFileCount = 0;
var lastFolderData = [];
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
                    jQuery("#folder-media-popup-form").append('<div class="folder-form-input"><input id="media-folder-name" autocomplete="off" placeholder="Folder name" class=""></div>');
                    jQuery("#folder-media-popup-form").append('<div class="folder-form-errors" id="media-form-error"><span class="dashicons dashicons-info"></span> Please enter folder name</div>');
                    jQuery("#folder-media-popup-form").append('<div class="folder-form-buttons"><button type="submit" class="form-submit-btn" id="save-media-folder" style="width: 106px">Submit</button><a href="javascript:;" class="remove-media-form">Cancel</a></div>');
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

jQuery(document).on("change", "#media-attachment-taxonomy-filter", function(){
    if(jQuery(this).val() !=  null && jQuery(this).val() != "add-folder") {
        jQuery(".folder_for_media").val(jQuery(this).val());
        selectedFolderPageID = jQuery(this).val();
        selectedFolderMediaId = jQuery(this).val();
    }
});

function resetDDCounter() {
    var currentDDVal = jQuery("#media-attachment-taxonomy-filter").val();
    jQuery.ajax({
        url: folders_media_options.ajax_url,
        data: "type=attachment&action=wcp_get_default_list&active_id=0",
        method: 'post',
        success: function (res) {
            res = jQuery.parseJSON(res);

            if(jQuery("#media-attachment-taxonomy-filter").length) {
                jQuery("#media-attachment-taxonomy-filter").each(function(){
                    folders_media_options.terms = res.taxonomies;
                    var selectedDD = jQuery(this);
                    selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
                    lastFolderData = res.taxonomies;
                    for (i = 0; i < res.taxonomies.length; i++) {
                        selectedDD.append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].count + ")</option>");
                    }
                    selectedDD.val(currentDDVal).trigger("change");

                    if(resetMediaID !== false) {
                        selectedDD.val(resetMediaID).trigger("change");
                    }
                });
            }
            resetMediaID = false;
        }
    })
}

function resetSelectMediaDropDown() {
    jQuery.ajax({
        url: folders_media_options.ajax_url,
        data: "type=attachment&action=wcp_get_default_list&active_id=0",
        method: 'post',
        success: function (res) {
            res = jQuery.parseJSON(res);
            if(jQuery(".folder_for_media").length) {
                if(!jQuery("#wcp-content").length) {
                    var selectedDD = jQuery(".folder_for_media");
                    selectedDD.html("<option value='-1'>(Unassigned)</option>");
                    lastFolderData = res.taxonomies;
                    for (i = 0; i < res.taxonomies.length; i++) {
                        selectedDD.append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].count + ")</option>");
                    }
                    selectedDD.append("<option value='add-folder'>+ Create a New Folder</option>");
                }
                jQuery("#custom-folder-media-popup-form").remove();
            }
            if(!jQuery("#media-attachment-taxonomy-filter").length && resetMediaID != false) {
                jQuery(".folder_for_media").val(resetMediaID).trigger("change");
            } else {
                resetDDCounter();
            }
        }
    });
}
var wp = window.wp;
//Upload on page Media Library (upload.php)
if (typeof wp !== 'undefined' && typeof wp.Uploader === 'function') {
    wp.media.view.Modal.prototype.on('open', function() {
        setTimeout(function(){
            if(jQuery("#media-attachment-taxonomy-filter").length) {
                if(jQuery("#media-attachment-taxonomy-filter").val() == "all") {
                    jQuery("#media-attachment-taxonomy-filter option:gt(1)").remove();
                    _.each(folders_media_options.terms, function(term, index){
                        jQuery("#media-attachment-taxonomy-filter").append("<option value='" + term.term_id + "'>" + term.name + " (" + term.count + ")</option>")
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
    jQuery.extend(wp.Uploader.prototype, {
        progress: function () {
        },
        init: function () {
            if (this.uploader) {
                this.uploader.bind('FileFiltered', function (up, file) {
                    filesInQueue++;
                    jQuery(".folder-meter").css("width", "0%");
                    jQuery(".media-folder-loader").show();
                    jQuery("#total_upload_files").text(filesInQueue);
                });
                this.uploader.bind('BeforeUpload', function (uploader, file) {
                    var folder_id = selectedFolderMediaId;
                    var params = uploader.settings.multipart_params;
                    folder_id = parseInt(folder_id);
                    if (folder_id > 0) {
                        params.folder_for_media = folder_id;
                    }
                    if(uploadedFileCount < filesInQueue) {
                        jQuery(".media-folder-loader").show();
                        var progress_width = uploadedFileCount/filesInQueue*100;
                        jQuery(".folder-meter").css("width", progress_width+"%");
                    }
                    uploadedFileCount++;
                    jQuery("#current_upload_files").text(uploadedFileCount);

                });
                this.uploader.bind('UploadComplete', function (up, files) {
                    selectedFolderMediaId = -1;
                });
                this.uploader.bind('UploadComplete', function (up, files) {
                    var wp_media = window.wp;

                    jQuery(".folder-meter").css("width", "100%");
                    setTimeout(function(){
                        jQuery(".media-folder-loader").hide();
                        jQuery(".folder-meter").css("width", "0%");
                        filesInQueue = 0;
                        uploadedFileCount = 0;
                    }, 1250);

                    resetDDCounter();

                    if(typeof wp_media.media.frame !== "undefined" && wp_media.media.frame.content.get() !== null) {
                        wp_media.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
                        wp_media.media.frame.content.get().options.selection.reset();
                    } else {
                        //wp_media.media.frame.library.props.set ({ignore: (+ new Date())});
                        if(jQuery("#media-attachment-taxonomy-filter").length) {
                            jQuery(".attachment-filters").each(function(){
                                jQuery(this).trigger("change");
                            });
                        }
                    }
                });
            }
        }
    });
}

jQuery( document ).ajaxComplete(function( event, request, settings ) {
    if(settings.data != undefined && typeof settings.data == 'string' && settings.data.indexOf("action=delete-post")>-1) {
        resetDDCounter();
    }
});

jQuery(document).on("click", "#menu-item-browse", function (e) {
    setTimeout(function(){
        if(resetMediaScreen) {
            resetDDCounter();
        }
    }, 100);
});
var resetMediaScreen = false;
var resetMediaID = false;
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
                    resetSelectMediaDropDown();
                    resetMediaScreen = true;
                    //resetSelectMediaDropDown();
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