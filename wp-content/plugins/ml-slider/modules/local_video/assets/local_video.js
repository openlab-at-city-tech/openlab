var { __ } = wp.i18n;

// Toolbar for Video
wp.media.view.Toolbar.Localvideo = wp.media.view.Toolbar.extend({
    initialize: function () {
        _.defaults(this.options, {
            event: 'local_video_event',
            close: false,
            items: {
                local_video_event: {
                    text: metaslider_local_video.add_to_slideshow, //wp.media.view.l10n.customButton, // added via 'media_view_strings' filter,
                    style: 'primary',
                    priority: 80,
                    requires: false,
                    click: this.localvideoAction
                }
            }
        });

        wp.media.view.Toolbar.prototype.initialize.apply(this, arguments);

        // Enable/disable button if video is selected
        var button = this.$('.media-button-local_video_event');
        if (this.controller.state().get('selection').length === 0) {
            button.prop('disabled', true);
        } else {
            button.prop('disabled', false);
        }

    },

    // called each time the model changes
    refresh: function () {
        // call the parent refresh
        wp.media.view.Toolbar.prototype.refresh.apply(this, arguments);
    },

    // triggered when the button is clicked
    localvideoAction: function () {
        var selection = this.controller.state().get('selection');

        selection.map(function (attachment) {
			attachment = attachment.toJSON();
			var APP = window.parent.metaslider.app.MetaSlider;
			APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
				APP.__('Preparing %s slide...', 'ml-slider'),
			'1'), true);

            var data = {
                action: 'create_local_video_slide',
                video_id: attachment.id,
                slider_id: window.parent.metaslider_slider_id,
                nonce: metaslider_local_video.nonce
            };

            jQuery.post(ajaxurl, data, function(response) {
                window.parent.metaslider.after_adding_slide_success(response.data);
            }).fail(function(error) { 
                console.error(error.status,error.statusText);
                // Show the server's reason when it sends one (e.g. not a slideshow, access denied)
                var message = error.responseJSON && error.responseJSON.data;
                APP && APP.notifyError('metaslider/slide-create-failed',
                    (typeof message === 'string' && message) || __("This isn't a supported video format. Please use MP4, WebM, or MOV videos.", "ml-slider"),
                    true
                );
            });
        });
    }
});

// supersede the default MediaFrame.Post view for Video
var oldMediaFrameLV = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrameLV.extend({

    initialize: function () {
        oldMediaFrameLV.prototype.initialize.apply(this, arguments);
        
        this.states.add([
            // Main states.
            new wp.media.controller.Library({
                id: 'insert-local-video',
                title: wp.media.view.l10n.insertLocalVideo,
                priority: 25,
                toolbar: 'add-local-video-slide',
                filterable: 'video',
                multiple: false,
                editable: true,
                allowLocalEdits: true,
                displaySettings: true,
                displayUserSettings: true,
                library: wp.media.query(_.defaults({
                    type: 'video' // Override type to only show videos
                }, this.options.library))
            }),
        ]);

        // Core adds every media_upload_tabs item - Livid included - at priority 200, so no
        // value can place this between two of them. Bound after core's own iframeMenu (set
        // up in the parent initialize above), this re-sets Livid just below that band so
        // both of the plugin's own video slide types sit near the top of the menu (#2428).
        this.on('menu:render:default', function (view) {
            var livid = view.get('iframe:livid');

            if (livid) {
                view.set('iframe:livid', _.extend({}, livid.options, { priority: 30 }));
            }
        });

        this.on('toolbar:create:add-local-video-slide', this.createLocalvideoToolbar, this);
        this.on('toolbar:render:add-local-video-slide', this.renderLocalvideoToolbar, this);

        // Enable "Add to slideshow" button when video is selected or uploaded
        this.on('selection:toggle', this.videoSelection, this);
        this.on('library:selection:add', this.videoSelection, this);
        this.on('open', this.videoSelection, this);
    },

    createLocalvideoToolbar: function (toolbar) {
        toolbar.view = new wp.media.view.Toolbar.Localvideo({
            controller: this
        });
    },

    videoSelection: function () {
        if(typeof this.content.view._state !== 'undefined' 
            && this.content.view._state === 'insert-local-video') {

            var selectedVideos = this.state().get('selection').length;
            var button = this.$('.media-button-local_video_event');

            if (selectedVideos === 1) {
                button.prop('disabled', false);
            } else {
                button.prop('disabled', true);
            }
        }
    },
});

window.jQuery(function ($) {
    const APP = window.metaslider.app ? window.metaslider.app.MetaSlider : null;
    
    if(! APP || ! window.metaslider.add_image_apis || ! window.metaslider.remove_image_apis) {
        console.error('MetaSlider: at least one global var is null, so Local Video slides cannot be edited.');
        return;
    }

    // Stashed context for the video picker button that's currently open, read back when
    // metaslider/pixabay-video-imported fires (the Pixabay download+import happens async,
    // well after the wp.media 'open' event that triggered it)
    var pending_pixabay_video = null;

    /**
     * A video picked from the Pixabay Video Library tab on the "Add Slide > Local Video" screen
     * (no existing slide yet) - mirrors wp.media.view.Toolbar.Localvideo's own
     * localvideoAction() AJAX call above, just sourced from an already-imported attachment ID
     * instead of a wp.media selection.
     * 
     * @since 3.113.0
     */
    window.metaslider.app.EventManager.$on('metaslider/pixabay-video-created', function ({ attachmentId }) {
        APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
            APP.__('Preparing %s slide...', 'ml-slider'),
        '1'), true);

        var data = {
            action: 'create_local_video_slide',
            video_id: attachmentId,
            slider_id: window.parent.metaslider_slider_id,
            nonce: metaslider_local_video.nonce
        };

        jQuery.post(ajaxurl, data, function(response) {
            window.parent.metaslider.after_adding_slide_success(response.data);
        }).fail(function(error) {
            console.error(error.status, error.statusText);
            // Show the server's reason when it sends one (e.g. not a slideshow, access denied)
            var message = error.responseJSON && error.responseJSON.data;
            APP && APP.notifyError('metaslider/slide-create-failed',
                (typeof message === 'string' && message) || __("This isn't a supported video format. Please use MP4, WebM, or MOV videos.", "ml-slider"),
                true
            );
        });
    });

    /**
     * A video picked via the Pixabay Video Library tab (added to the video picker by
     * add_video_apis() below) doesn't go through wp.media's own 'select' event - External.vue
     * downloads and imports the video as a real attachment itself, then
     * emits this event with the new attachment ID. Mirrors the exact same add_video_source /
     * update_slide_video AJAX call the native wp.media 'select' handler makes further down.
     * 
     * @since 3.113.0
     */
    window.metaslider.app.EventManager.$on('metaslider/pixabay-video-imported', function ({ slideId, attachmentId }) {
        if (!pending_pixabay_video || String(pending_pixabay_video.$this.data('slideId')) !== String(slideId)) {
            return;
        }

        var $this = pending_pixabay_video.$this;
        var newMedia = pending_pixabay_video.newMedia;
        var slide_type = pending_pixabay_video.slideType;
        var current_id = pending_pixabay_video.currentId;
        pending_pixabay_video = null;

        if (!newMedia) {
            APP && APP.notifyInfo('metaslider/updating-slide', APP.__('Updating slide...', 'ml-slider'), true)
        }

        var data = {
            action: newMedia ? 'add_video_source' : 'update_slide_video',
            slide_id: $this.data('slideId'),
            slider_id: window.parent.metaslider_slider_id,
            _wpnonce: metaslider_local_video.update_slide_nonce,
            video_id: attachmentId,
            prev_video_id: current_id
        };

        if (newMedia) {
            data.slide_type = slide_type;
        }

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (error) {
                console.error(error.status, error.statusText);

                var textObj = (function() {
                    try {
                        return JSON.parse(error.responseText);
                    } catch (e) {
                        return error.responseText;
                    }
                })();

                // A refused slide or slideshow sends a plain message, other errors send { message }
                var errorData = textObj && textObj.data;
                var errorMsg = (errorData && errorData.message)
                    || (typeof errorData === 'string' && errorData)
                    || (APP && __("There is an error", "ml-slider"));

                APP && APP.notifyError('metaslider/slide-update-failed', errorMsg, true);
            },
            success: function (response) {
                var trSlide = $(`#slide-${$this.data('slideId')}`);

                if (newMedia) {
                    trSlide.find('.list-video-sources').append(response.data.html_row);

                    if (slide_type === 'html_overlay') {
                        refreshPreview(trSlide.find('.metaslider-slide-thumb--local-video .thumb'), response.data.html_embed);
                    } else {
                        refreshPreview(trSlide.find('.thumb'), response.data.html_embed);
                    }

                    if (response.data.slide_id) {
                        trSlide.trigger('metaslider/attachment/updated', response.data);
                    }

                    if (response.data.count_sources == 3) {
                        trSlide.find(`.add-video-source`).parents('div.row').hide();
                    }

                    APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video source added successfully', 'ml-slider'), true);
                } else {
                    if (slide_type === 'html_overlay') {
                        refreshPreview(trSlide.find('.metaslider-slide-thumb--local-video .thumb'), response.data.html_embed);
                    } else {
                        refreshPreview(trSlide.find('.thumb'), response.data.html_embed);
                    }

                    trSlide.find(`input.video_url[data-format="${$this.data('format')}"]`).val(response.data.video_url);

                    var $edited_slide_elms = $(`#slide-${$this.data('slideId')} [data-format="${$this.data('format')}"], #slide-${$this.data('slideId')} .update-video[data-format="${$this.data('format')}"]`);
                    $edited_slide_elms.data('attachment-id', attachmentId);

                    if (response.data.video_url) {
                        trSlide.trigger('metaslider/attachment/updated', response.data);
                    }

                    APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video updated successfully', 'ml-slider'), true);
                }

                $(".metaslider table#metaslider-slides-list").trigger('resizeSlides');
            }
        });
    });

    // Remove Unsplash tab when creating a new Local video slide
    window.create_slides.on('open activate uploader:ready', function() {
        if ($('#menu-item-insert-local-video.media-menu-item.active').length) {
            window.metaslider.remove_image_apis();

            // Add the Pixabay Video Library tab to the "Add Slide > Local Video" screen too
            // (no slide_id - the success handler below creates a brand new slide instead)
            window.metaslider.add_video_apis();
        }

        // Reset layout to add description
        var title = $('#media-frame-title h1');
                
        title.parent().find('h2').remove();
        $('#media-frame-title').css('height', '');
        title.css('border-bottom', '');
        $('.media-frame-router').css('top', '');
        $('.media-frame-content').css('top', '');

        // Apply only to Local video, Image and Layer slides
        var image_slide_active = $('#menu-item-insert').hasClass('active');
        var local_video_active = $('#menu-item-insert-local-video').hasClass('active');
        var layer_slide_active = $('#menu-item-insert-html').hasClass('active');

        if (local_video_active || image_slide_active || layer_slide_active) {
            // Adjust layout to add description
            title.css('border-bottom', '1px solid #ddd');
            title[0].style.setProperty('margin-bottom', '0', 'important');
            $('#media-frame-title').css('height', '102px');
            $('.media-frame-router').css('top', '102px');
            $('.media-frame-content').css('top', '136px');

            var label = null;
            if (local_video_active) {
                label = APP.__('Create slideshows with videos hosted in your media library', 'ml-slider');
            } else if (image_slide_active) {
                label = APP.__('Create slideshows with images in your media library', 'ml-slider');
            } else if (layer_slide_active) {
                label = APP.__('Layer Slides allow you to add text, colors, shortcodes and media on top of an image or video.', 'ml-slider');
            }
            if (label) {
                title.after(
                    ' <h2 class="ms-slide-type-heading-desc">' + label + '</h2>'
                );
            }
        }
    });

    /**
     * Reset selection to avoid errors when opening for video selection change
     */
    window.create_slides.on('escape', function() {
        window.create_slides.state()?.get('selection')?.reset();
    });

    /**
     * Changing the video
     * This works for Local video and Layer slides
     * 
     * @since 3.113.0
     */
    $('.metaslider').on('click', '.update-video', function (event) {
        event.preventDefault();
        updateMedia(this, 'video');
    });

    /**
     * Adding a new video source
     * This works for Local video and Layer slides
     * 
     * @since 3.113.0
     */
    $('.metaslider').on('click', '.add-video-source', function (event) {
        event.preventDefault();
        updateMedia(this, 'video', true);
    });

    // Changing the cover image
    $('.metaslider').on('click', '.update-cover-image', function (event) {
        event.preventDefault();
        updateMedia(this, 'image');
    });

    // Removing the cover image
    $('.metaslider').on('click', '.remove-cover-image', function (event) {
        event.preventDefault();
        var $this = $(this);
        
        $.ajax({
            url: metaslider.ajaxurl,
            data: {
                action: 'remove_cover',
                slide_id: $this.data('slideId'),
                slide_type: $this.data('slideType'),
                _wpnonce: metaslider_local_video.update_slide_nonce
            },
            type: 'POST',
            error: function (error) {
                console.error(error.status,error.statusText);

                var refused = error.responseJSON && typeof error.responseJSON.data === 'string' && error.responseJSON.data;
                APP && APP.notifyError('metaslider/slide-update-failed',
                    refused || __("There is an error", "ml-slider"),
                    true
                );
            },
            success: function (response) {
                console.log(response);

                var trSlide = $(`#slide-${$this.data('slideId')}`);

                // Refresh preview
                refreshPreview(trSlide.find(`.${$this.data('slideType')}-cover`), response.data.html_embed);

                APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Cover removed successfully.', 'ml-slider'), true)
            }
        });
    });

    /**
     * Removing video source
     * This works for Local video and Layer slides
     * 
     * @since 3.113.0
     */
    $('.metaslider').on('click', '.remove-video-source', function (event) {
        event.preventDefault();
        
        var $this = $(this);
        var slide_type = $this.data('slide-type') || null;

        var data = {
            action: 'remove_video_source',
            slide_id: $this.data('slideId'),
            video_id: $this.data('attachment-id'),
            slide_type: slide_type,
            _wpnonce: metaslider_local_video.update_slide_nonce
        };

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (error) {
                console.error(error.status,error.statusText);
                
                APP && APP.notifyError('metaslider/slide-update-failed', 
                    (error.responseJSON && typeof error.responseJSON.data === 'string' && error.responseJSON.data)
                        || __("You can't delete this video. This slide requires at least one video source.", "ml-slider"),
                    true
                );
            },
            success: function (response) {
                var trSlide = $(`#slide-${$this.data('slideId')}`);

                // Remove source video field
                trSlide.find(`.remove-video-source[data-attachment-id="${$this.data('attachment-id')}"]`).parents('div.row').remove();
                
                // Show "Add new video source" button
                trSlide.find(`.add-video-source`).parents('div.row').show();
                
                // Update preview embed
                if (slide_type === 'html_overlay') {
                    refreshPreview(trSlide.find('.metaslider-slide-thumb--local-video .thumb'), response.data.html_embed);
                } else {
                    refreshPreview(trSlide.find('.thumb'), response.data.html_embed);
                }
                
                if (response.data.slide_id) {
                    trSlide.trigger('metaslider/attachment/updated', response.data);
                }

                /*/ Remove delete video source and adjust UI
                if (response.data.count_sources == 1) {
                    trSlide.find(`.remove-video-source`).remove();
                    trSlide.find(`.video_url`).removeClass('border-r-0');
                    trSlide.find(`.video_url`).addClass('no-remove-btn');
                }*/

                APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video source removed successfully', 'ml-slider'), true)
            }
        });
    });

    // Changing the text track
    $('.metaslider').on('click', '.update-text-track', function (event) {
        event.preventDefault();
        updateMedia(this, 'text');
    });

    // Removing text track
    $('.metaslider').on('click', '.remove-text-track', function (event) {
        event.preventDefault();
        
        var $this = $(this);

        if ($this.data('attachment-id').length === 0) {
            APP && APP.notifyError('metaslider/slide-update-failed', 
                APP && __("No text track has beed selected for this slide.", "ml-slider"),
                true
            );

            return;
        }

        var data = {
            action: 'remove_slide_track',
            slide_id: $this.data('slideId'),
            track_id: $this.data('attachment-id'),
            _wpnonce: metaslider_local_video.update_slide_nonce
        };

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (error) {
                console.error(error.status,error.statusText);
                
                APP && APP.notifyError('metaslider/slide-update-failed', 
                    (error.responseJSON && typeof error.responseJSON.data === 'string' && error.responseJSON.data)
                        || __("There was an error removing the text track", "ml-slider"),
                    true
                );
            },
            success: function (response) {

                // Updates the text track url
                $('#slide-' + $this.data('slideId') + ' input.track_url').val('');
                    
                // set attachment ID as empty
                $('#slide-' + $this.data('slideId') + ' .update-text-track').data('attachment-id', '');
                $('#slide-' + $this.data('slideId') + ' .remove-text-track').data('attachment-id', '');

                if (response.data.video_url) {
                    $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                }

                APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Text track removed successfully', 'ml-slider'), true)
            }
        });
    });

    /**
     * Handles changing a video or image
     * 
     * @since 3.113.0
     * 
     * @param {object} elmnt      Button that triggers this function. e.g. this
     * @param {string} media_type 'video', 'image' or 'text'
     * @param {bool} newMedia     Are we adding a new media type to an existing slide?
     * 
     * @return void
     */
    const updateMedia = function(elmnt, media_type, newMedia = false) {
        var $this = $(elmnt);
        var current_id = $this.data('attachment-id') || null;
        var title = MetaSlider_Helpers.capitalize(metaslider_local_video[`update_${media_type}_text`]);
        var slide_type = $this.data('slide-type') || null;

        // Only when updating a video source
        var format = $this.data('format') || null;
        if (media_type === 'video' && !newMedia) {
            title = APP && APP.sprintf(
                APP.__('Select Replacement %s Video', 'ml-slider'),
                format.toUpperCase()
            );
        }

        /**
         * Opens up a media window showing media
         */
        update_slide_frame = wp.media.frames.file_frame = wp.media({
            title: title,
            library: {
                type: media_type
            },
            button: {
                text: MetaSlider_Helpers.capitalize($this.attr('data-button-text'))
            }
        });

        /**
         * Selects current media
         */
        update_slide_frame.on('open', function () {
            if (current_id) {
                var selection = update_slide_frame.state().get('selection');
                selection.reset([wp.media.attachment(current_id)]);
            }

            // Add various image APIs
            if (media_type === 'image') {
                window.metaslider.add_image_apis($this.data('slideType'), $this.data('slideId'));
            }

            // Add the Pixabay Video Library tab (only ever reachable from this picker)
            if (media_type === 'video') {
                pending_pixabay_video = { $this: $this, newMedia: newMedia, slideType: slide_type, currentId: current_id };
                window.metaslider.add_video_apis($this.data('slideId'));
            }
        });

        /**
         * Reset selection to avoid errors on second open for video selection change
         */
        update_slide_frame.on('escape', function() {
            update_slide_frame.state().get('selection').reset();
        });

        /**
         * Open media modal
         */
        update_slide_frame.open();

        update_slide_frame.on('selection:toggle', function () {

            if (media_type === 'video' && !newMedia) {
                var msgWrapper = $('.media-frame-toolbar .media-toolbar-secondary');
                var attachment = update_slide_frame.state().get('selection').toJSON();

                if (attachment.length === 1 && !attachment[0].url.toLowerCase().endsWith(format)) {

                    $('.media-frame-toolbar .media-button-select').prop('disabled', true);
                    msgWrapper.html(
                        APP && APP.sprintf(
                            `<div style="color:rgb(204,24,24) !important;font-size:1.2em;margin-top:20px;">${APP.__('Please select a %s video for this source!', 'ml-slider')}</div>`,
                            format.toUpperCase()
                        )
                    )
                } else {
                    msgWrapper.html('');
                }
            }
        });

        /**
         * Handles changing a media in DB and UI
         */
        update_slide_frame.on('select', function () {
            var selection = update_slide_frame.state().get('selection');
            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                new_media_id = attachment.id;
                selected_item = attachment;
            });

            // Let's skip this notice when adding new video source to avoid save getting frozen
            // https://github.com/MetaSlider/metaslider-pro/issues/393
            if (media_type === 'video' && !newMedia) {
                APP && APP.notifyInfo('metaslider/updating-slide', APP.__('Updating slide...', 'ml-slider'), true)
            }

            // Remove the events for image APIs
            if(media_type === 'image') {
                window.metaslider.remove_image_apis();
            }

            /**
             * Updates the meta information on the slide
             */
            var data = {
                action: `update_slide_${media_type}`,
                slide_id: $this.data('slideId'),
                slider_id: window.parent.metaslider_slider_id
            };

            if( media_type === 'video' ) {
                data._wpnonce = metaslider_local_video.update_slide_nonce,
                data.video_id = new_media_id;
                data.prev_video_id = current_id; // when newMedia is true, this becomes null

                if (newMedia) {
                    data.action = 'add_video_source',
                    data.slide_type = slide_type
                }

            } else if( media_type === 'image' ) {
                // Image - We use the nonce and action from MetaSlider Free from wp_ajax_update_slide_image
                data._wpnonce = metaslider.update_slide_image_nonce,
                data.image_id = new_media_id;
            } else if( media_type === 'text' ) {
                // Text track
                data.action = 'update_slide_track',
                data._wpnonce = metaslider_local_video.update_slide_nonce,
                data.track_id = new_media_id;
            } else {
                console.error('Invalid media type', media_type);
            }

            $.ajax({
                url: metaslider.ajaxurl,
                data: data,
                type: 'POST',
                error: function (error) {
                    console.error(error.status,error.statusText);

                    // @TODO - Check why error.responseText isn't a valid JSON
                    var textObj = (function() {
                        try {
                            return JSON.parse(error.responseText);
                        } catch (e) {
                            return error.responseText;
                        }
                    })();

                    // A refused slide or slideshow sends a plain message, other errors send { message }
                    var errorData = textObj && textObj.data;
                    var refused = typeof errorData === 'string' && errorData;
                    var errorMsg = (errorData && errorData.message)
                        || refused
                        || (APP && __("There is an error", "ml-slider"));

                    // @TODO - Let's use the error message from server side like in video for images and text
                    if( refused ) {
                        APP && APP.notifyError('metaslider/slide-update-failed', refused, true);
                    } else if( media_type === 'video' ) {
                        
                        APP && APP.notifyError('metaslider/slide-update-failed', 
                            errorMsg,
                            true
                        );
                    } else if( media_type === 'image' ) {
                        // Cover image
                        APP && APP.notifyError('metaslider/slide-update-failed', 
                            APP && __("This isn't a supported image format. Please use JPG, PNG, or GIF images.", "ml-slider"),
                            true
                        );
                    } else if( media_type === 'text' ) {
                        // Text track
                        APP && APP.notifyError('metaslider/slide-update-failed', 
                            APP && __("This isn't a supported text track format. Please use TXT or VTT files.", "ml-slider"),
                            true
                        );
                    } else {
                        // Nothing to do here
                    }
                },
                success: function (response) {
                    
                    var trSlide = $(`#slide-${$this.data('slideId')}`);

                    if( media_type === 'video' ) {

                        if (newMedia) {
                            // Adding new video source
                            trSlide.find('.list-video-sources').append(response.data.html_row);

                            // Update preview embed
                            if (slide_type === 'html_overlay') {
                                refreshPreview(trSlide.find('.metaslider-slide-thumb--local-video .thumb'), response.data.html_embed);
                            } else {
                                refreshPreview(trSlide.find('.thumb'), response.data.html_embed);
                            }

                            if (response.data.slide_id) {
                                trSlide.trigger('metaslider/attachment/updated', response.data);
                            }

                            // Hide "Add new video source" button
                            if (response.data.count_sources == 3) {
                                trSlide.find(`.add-video-source`).parents('div.row').hide();
                            }

                            APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video source added successfully', 'ml-slider'), true);
                        } else {
                            // Updating existing video source
                            
                            // Update preview embed
                            if (slide_type === 'html_overlay') {
                                refreshPreview(trSlide.find('.metaslider-slide-thumb--local-video .thumb'), response.data.html_embed);
                            } else {
                                refreshPreview(trSlide.find('.thumb'), response.data.html_embed);
                            }
                            
                            trSlide.find(`input.video_url[data-format="${$this.data('format')}"]`).val(response.data.video_url);
                            
                            // set new attachment ID
                            var $edited_slide_elms = $(`#slide-${$this.data('slideId')} [data-format="${$this.data('format')}"], #slide-${$this.data('slideId')} .update-video[data-format="${$this.data('format')}"]`);
                            $edited_slide_elms.data('attachment-id', selected_item.id);

                            if (response.data.video_url) {
                                trSlide.trigger('metaslider/attachment/updated', response.data);
                            }

                            APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video updated successfully', 'ml-slider'), true);
                        }

                    } else if( media_type === 'image' ) {

                        updateCoverPreview(
                            $this.data('slideId'),
                            $this.data('slideType'),
                            response.data,
                            selected_item.id
                        );
                    } else if( media_type === 'text' ) {

                        /**
                         * Updates the text track url
                         */
                        $('#slide-' + $this.data('slideId') + ' input.track_url').val(response.data.track_url);
                        
                        // set new attachment ID
                        $('#slide-' + $this.data('slideId') + ' .update-text-track').data('attachment-id', selected_item.id);
                        $('#slide-' + $this.data('slideId') + ' .remove-text-track').data('attachment-id', selected_item.id);

                        if (response.data.track_url) {
                            $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                        }

                        APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Text track updated successfully', 'ml-slider'), true)
                    } else {
                        // Nothing to do here
                    }

                    // TODO: run a function in SlideViewer.vue to replace this
                    $(".metaslider table#metaslider-slides-list").trigger('resizeSlides');
                }
            });
        });

        update_slide_frame.on('close', function () {
            if(media_type === 'image') {
                window.metaslider.remove_image_apis();
            }
            if (media_type === 'video') {
                window.metaslider.remove_video_apis();
                pending_pixabay_video = null;
            }
        });
    }

    /**
     * Update media preview content with a fadeIn/fadeOut effect
     * 
     * @since 3.113.0
     * 
     * @param {obj} el          Target element. e.g. $('.thumb')
     * @param {html} newHtml    HTML to replace in el
     * 
     * @return void
     */
    /**
     * Brings the Cover tab's UI in line with a newly saved cover: the button's thumbnail,
     * the attachment id it reopens the picker with, and the remove button (absent while a
     * slide has no cover). Shared by the media library picker and the external image
     * pickers (Unsplash/Pixabay), which save through a different endpoint.
     *
     * @since 3.113.0
     *
     * @param {number|string} slideId
     * @param {string} slideType
     * @param {object} data          Response data, carrying at least thumbnail_url_small.
     *                               Passed on as-is to metaslider/attachment/updated, whose
     *                               listeners read fields this function doesn't (e.g. img_url).
     * @param {number|string} attachmentId
     *
     * @return void
     */
    const updateCoverPreview = function (slideId, slideType, data, attachmentId) {
        var trSlide = $('#slide-' + slideId);
        var $cover_preview = trSlide.find('.update-cover-image');

        if (!$cover_preview.length) {
            return;
        }

        $cover_preview.css('background-image', 'url(' + data.thumbnail_url_small + ')');
        $cover_preview.html('');
        $cover_preview.attr('data-attachment-id', attachmentId);
        $cover_preview.data('attachment-id', attachmentId);

        trSlide.find('.remove-cover-image').remove();
        $cover_preview.before(`<button data-slide-id="${slideId}" data-slide-type="${slideType}" class="remove-cover-image button button-secondary ms-button-danger">
            <i>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line> <line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </i>
        </button>`);

        if (data.thumbnail_url_small) {
            trSlide.trigger('metaslider/attachment/updated', data);
        }

        APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Cover image updated successfully', 'ml-slider'), true);
    }

    /**
     * A cover picked from the Unsplash or Pixabay tab. Those import through
     * import/images and never reach the wp.media 'select' handler below, so the Cover
     * tab's own UI is brought up to date from here instead.
     *
     * @since 3.113.0
     */
    window.metaslider.app.EventManager.$on('metaslider/external-image-imported', function (data) {
        if (data.slideType !== 'local_video') {
            return;
        }

        updateCoverPreview(data.slideId, data.slideType, data, data.attachment_id);
    });

    const refreshPreview = function (el, newHtml) {
        el.animate({ opacity: 0 }, function() {
            el.html(newHtml);
            setTimeout(function() {
                el.animate({ opacity: 1 }, 500);
            }, 500);
        });
    }

    /**
    * Handles duplicating slides
    */
    $('.metaslider').on('click', '.duplicate-slide-local_video', function (event) {
        event.preventDefault();
        var APP = window.parent.metaslider.app.MetaSlider;
        var el = $(this);
        $.ajax({
            url: metaslider.ajaxurl,
            data: {
                action: 'duplicate_local_video_slide',
                slide_id: el.data('slide-id'),
                slider_id: window.parent.metaslider_slider_id,
                nonce: metaslider_local_video.duplicate_slide_nonce
            },
            type: 'POST',
            error: function (error) {
                APP && APP.notifyError('metaslider/slide-duplicate-failed', 
                    (error && error.responseJSON && typeof error.responseJSON.data === 'string' && error.responseJSON.data) || __("Duplicating the slide failed.", "ml-slider"),
                    true
                );
            },
            success: function (response) {

                var res = window.metaslider.app.Vue.compile(response.data.html)

                // Mount the slide to the beginning or end of the list
                const cont_ = (new window.metaslider.app.Vue({
                    render: res.render,
                    staticRenderFns: res.staticRenderFns
                }).$mount()).$el;

                if (metaslider.newSlideOrder === 'last') {
                    $('#metaslider-slides-list > tbody').append(cont_);
                } else {
                    $('#metaslider-slides-list > tbody').prepend(cont_);
                }

                // Display image (is hidden by default)
                $("#slide-" + response.data.slide_id).find('.update-image .thumb img').show();

                /* Add mobile icon for slides with existing mobile setting */
                var show_mobile_icon = function (slide_id) {
                    var mobile_checkboxes = $('#metaslider-slides-list #'+ slide_id +' .mobile-checkbox:checked');
                    var icon = '<span class="mobile_setting_enabled float-left"><span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smartphone"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg></span></span>';
                    var mobile_enabled = $('#metaslider-slides-list #'+ slide_id +' .slide-details .mobile_setting_enabled');
                    if (mobile_checkboxes.length > 0) {
                        if(mobile_enabled.length == 0) {
                            $('#metaslider-slides-list #'+ slide_id +' .slide-details').append(icon);
                        }
                    } else {
                        mobile_enabled.remove();
                    }
                };

                /* Add clock icon for slides with existing schedule setting */
                var show_clock_icon = function (slide_id) {
                    var sched_checkboxes = $('#metaslider-slides-list #'+ slide_id +' .schedule-slide:checked');
                    var icon = '<span class="schedule_visual_indicator float-left tipsy-tooltip-top" style="margin-top:1px;" original-title="Visible on the frontend"><span class="inline-block mr-1" style="color: rgb(70, 180, 80);"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" width="18" height="18" style=""><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span></span>';
                    var mobile_enabled = $('#metaslider-slides-list #'+ slide_id +' .slide-details .mobile_setting_enabled');
                    if (sched_checkboxes.length > 0) {
                        $('#metaslider-slides-list #'+ slide_id +' .slide-details').append(icon);
                    }
                };


                //Icon for mobile settings
                show_mobile_icon('slide-' + response.data.slide_id);
                show_clock_icon('slide-' + response.data.slide_id);

                //scroll to new slide
                $([document.documentElement, document.body]).animate({
                    scrollTop: metaslider.newSlideOrder === 'last' ? $("#slide-"+response.data.slide_id).offset().top : 0
                }, 2000);

                // Add timeouts to give some breating room to the notice animations
                setTimeout(function () {
                    APP && APP.triggerEvent('metaslider/slide-duplicated');
                    
                    setTimeout(function () {
                        APP && APP.triggerEvent('metaslider/save')
                    }, 1000);
                }, 1000);
                
            }
        }); 
    });
});
/**
 * Caption editor for Local Video slides.
 *
 * MetaSlider Slideshow Pro sets up its own richer editor for this slide type, so this only
 * runs when Pro is not active - metaslider_local_video.caption_editor says which it is. The
 * configuration is registered in the shared metaslider.tinymce list so the slide list can
 * rebuild the editor after slides are reordered.
 *
 * @since 3.113.0
 */
window.jQuery(function ($) {
    if (typeof metaslider_local_video === 'undefined' || ! Number(metaslider_local_video.caption_editor)) {
        return;
    }

    if (typeof tinymce === 'undefined' || typeof metaslider === 'undefined') {
        return;
    }

    var selector = '.metaslider-ui textarea.wysiwyg-local-video';

    var configuration = {
        toolbar: 'undo redo bold italic underline strikethrough removeformat forecolor fontsizeinput lineheight styles link unlink alignleft aligncenter alignright code',
        menubar: false,
        plugins: 'code link',
        line_height_formats: '0.8 0.9 1 1.1 1.2 1.3 1.4 1.5 1.6 1.7 1.8 1.9 2 2.1 2.2 2.3 2.4 2.5 2.6 2.7 2.8 2.9 3',
        branding: false,
        promotion: false,
        height: 240,
        preview_styles: false,
        forced_root_block: 'div',
        convert_urls: false,
        setup: function (editor) {
            var updateContent = function () {
                var el = document.getElementById(editor.id);
                if (el) {
                    el.value = editor.getContent();
                }
            };

            editor.on('input', updateContent);
            editor.on('ExecCommand', updateContent);
        }
    };

    if (typeof metaslider.tinymce.find(function (obj) { return obj.type === 'local_video'; }) === 'undefined') {
        metaslider.tinymce.push({
            type: 'local_video',
            configuration: configuration
        });
    }

    var loadEditors = function () {
        $(selector).each(function () {
            var id = $(this).attr('id');

            if (! id || tinymce.get(id)) {
                return;
            }

            tinymce.init($.extend({ selector: '#' + id }, configuration));
        });
    };

    loadEditors();

    if (window.metaslider.app && window.metaslider.app.EventManager) {
        window.metaslider.app.EventManager.$on([
            'metaslider/slides-created',
            'metaslider/slide-duplicated'
        ], loadEditors);
    }
});
