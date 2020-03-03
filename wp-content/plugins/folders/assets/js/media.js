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

var selectedFolderMediaId = -1;
var filesInQueue = 0;
var uploadedFileCount = 0;
jQuery(document).on("change", ".folder_for_media", function(){
    if(jQuery(this).val() != "add-folder") {
        selectedFolderMediaId = jQuery(this).val();
    } else {
        selectedFolderMediaId = -1;
    }

    if(jQuery(".media-toolbar #media-attachment-taxonomy-filter").length) {
        jQuery("#media-attachment-taxonomy-filter").val(jQuery(this).val());
        jQuery("#media-attachment-taxonomy-filter").trigger("change");
    }
});

jQuery(document).on("change", "#media-attachment-taxonomy-filter", function(){
    jQuery(".folder_for_media").val(jQuery(this).val());
    selectedFolderMediaId = jQuery(this).val();
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
                folders_media_options.terms = res.taxonomies;
                var selectedDD = jQuery("#media-attachment-taxonomy-filter");
                selectedDD.html("<option value='all'>All Folders</option><option value='unassigned'>(Unassigned)</option>");
                for (i = 0; i < res.taxonomies.length; i++) {
                    selectedDD.append("<option value='" + res.taxonomies[i].term_id + "'>" + res.taxonomies[i].name + " (" + res.taxonomies[i].count + ")</option>");
                }
                selectedDD.val(currentDDVal);
            }
        }
    })
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
            }
        },1500);
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

                    if( wp_media.media.frame.content.get() !== null) {
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
    if(settings.data != undefined && settings.data.indexOf("action=delete-post")>-1) {
        resetDDCounter();
    }
});