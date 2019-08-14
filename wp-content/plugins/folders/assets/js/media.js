(function() {
    var MediaLibraryOrganizerTaxonomyFilter = wp.media.view.AttachmentFilters.extend({
        id: 'media-attachment-taxonomy-filter',
        createFilters: function() {
            var filters = {};
            _.each(folders_media_options.terms || {}, function(term, index) {
                filters[index] = {
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

jQuery(document).on("change", ".folder_for_media", function(){
    selectedFolderMediaId = $(this).val();
});


var wp = window.wp;
//Upload on page Media Library (upload.php)
if (typeof wp !== 'undefined' && typeof wp.Uploader === 'function') {
    jQuery.extend(wp.Uploader.prototype, {
        progress: function () {
        },
        init: function () {
            if (this.uploader) {
                this.uploader.bind('FileFiltered', function (up, file) {
                });
                this.uploader.bind('BeforeUpload', function (uploader, file) {
                    var folder_id = selectedFolderMediaId;
                    var params = uploader.settings.multipart_params;
                    folder_id = parseInt(folder_id);
                    if (folder_id > 0) {
                        params.folder_for_media = folder_id;
                    }
                });
                this.uploader.bind('UploadComplete', function (up, files) {
                    selectedFolderMediaId = -1;
                });
            }
        }
    });
}