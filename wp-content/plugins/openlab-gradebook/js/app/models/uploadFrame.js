define(['backbone'], function (Backbone) {

    var MediaFrame = wp.media.view.MediaFrame,
            l10n = wp.media.view.l10n;

    var uploadFrame = MediaFrame.extend({

        initialize: function () {
            // Call 'initialize' directly on the parent class.
            MediaFrame.prototype.initialize.apply(this, arguments);

            _.defaults(this.options, {
                selection: [],
                library: {},
                multiple: false,
                state: 'insert'
            });

            this.createSelection();
            this.createStates();
            this.bindHandlers();
        },

        /**
         * Attach a selection collection to the frame.
         *
         * A selection is a collection of attachments used for a specific purpose
         * by a media frame. e.g. Selecting an attachment (or many) to insert into
         * post content.
         *
         * @see media.model.Selection
         */
        createSelection: function () {
            var selection = this.options.selection;

            if (!(selection instanceof wp.media.model.Selection)) {
                this.options.selection = new wp.media.model.Selection(selection, {
                    multiple: this.options.multiple
                });
            }

            this._selection = {
                attachments: new wp.media.model.Attachments(),
                difference: []
            };
        },

        /**
         * Create the default states on the frame.
         */
        createStates: function () {
            var options = this.options;

            if (this.options.states) {
                return;
            }

            this.$el.addClass('upload-csv-modal');

            // Add the default states.
            this.states.add([
                new wp.media.controller.Library({

                    id: 'insert',
                    title: 'Upload CSV',
                    priority: 20,
                    toolbar: 'select',
                    filterable: false,
                    library: wp.media.query(options.library),
                    multiple: options.multiple ? 'reset' : false,
                    editable: true,
                    syncSelection: false,

                    // If the user isn't allowed to edit fields,
                    // can they still edit it locally?
                    allowLocalEdits: true,

                    // Show the attachment display settings.
                    displaySettings: true,
                    // Update user settings when users adjust the
                    // attachment display settings.
                    displayUserSettings: true
                }),
            ]);
        },

        /**
         * Bind region mode event callbacks.
         *
         * @see media.controller.Region.render
         */
        bindHandlers: function () {
            this.on('router:create:browse', this.createRouter, this);
            this.on('router:render:browse', this.browseRouter, this);
            this.on('content:create:browse', this.browseContent, this);
            this.on('content:render:upload', this.uploadContent, this);
            this.on('toolbar:create:select', this.createSelectToolbar, this);
        },

        /**
         * Render callback for the router region in the `browse` mode.
         *
         * @param {wp.media.view.Router} routerView
         */
        browseRouter: function (routerView) {
            routerView.set({
                browse: {
                    text: l10n.mediaLibraryTitle,
                    priority: 40
                }
            });
        },

        /**
         * Render callback for the content region in the `browse` mode.
         *
         * @param {wp.media.controller.Region} contentRegion
         */
        browseContent: function (contentRegion) {
            var state = this.state();

            this.$el.removeClass('hide-toolbar');

            // Browse our library of attachments.
            contentRegion.view = new wp.media.view.AttachmentsBrowser({
                controller: this,
                collection: state.get('library'),
                selection: state.get('selection'),
                model: state,
                sortable: state.get('sortable'),
                search: false,
                filters: {},
                date: false,
                display: state.has('display') ? state.get('display') : state.get('displaySettings'),
                dragInfo: state.get('dragInfo'),

                idealColumnWidth: state.get('idealColumnWidth'),
                suggestedWidth: state.get('suggestedWidth'),
                suggestedHeight: state.get('suggestedHeight'),

                AttachmentView: state.get('AttachmentView')
            });
        },

        /**
         * Render callback for the content region in the `upload` mode.
         */
        uploadContent: function () {
            this.$el.removeClass('hide-toolbar');
            this.content.set(new wp.media.view.UploaderInline({
                controller: this,
                message: 'Upload CSV'
            }));
        },

        /**
         * Toolbars
         *
         * @param {Object} toolbar
         * @param {Object} [options={}]
         * @this wp.media.controller.Region
         */
        createSelectToolbar: function (toolbar, options) {
            options = options || this.options.button || {};
            options.controller = this;

            toolbar.view = new wp.media.view.Toolbar.Select(options);
        }
    });
    return uploadFrame;
});