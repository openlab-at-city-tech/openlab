(function($) {
    window.NggPaginatedGallery = function(displayed_gallery_id, container) {
        this.displayed_gallery_id = displayed_gallery_id;
        this.container            = $(container);
        this.container_name       = container;

        this.get_displayed_gallery_obj = function() {
            var index = 'gallery_' + this.displayed_gallery_id;
            if (typeof(window.galleries[index]) == 'undefined') {
                return false;
            } else {
                return window.galleries[index];
            }
        };

        this.enable_ajax_pagination = function() {
            var self = this;
            // Attach a click event handler for each pagination link to adjust the request to be sent via XHR
            $('body').on('click', 'a.ngg-browser-prev, a.ngg-browser-next', function (event) {

                var skip = true;
                $(this).parents(container).each(function() {
                    if ($(this).data('nextgen-gallery-id') != self.displayed_gallery_id) {
                        return true;
                    }
                    skip = false;
                });

                if (!skip) {
                    event.preventDefault();
                } else {
                    return;
                }

                // Adjust the user notification
                window['ngg_ajax_operaton_count']++;
                $('body, a').css('cursor', 'wait');

                // Send the AJAX request
                $.get({
                    url: $(this).attr('href'),
                    headers: { 'X-NGG-Pagination-Request': true },
                    success: function (response) {
                        window['ngg_ajax_operaton_count']--;
                        if (window['ngg_ajax_operaton_count'] <= 0) {
                            window['ngg_ajax_operaton_count'] = 0;
                            $('body, a').css('cursor', 'auto');
                        }

                        if (response) {
                            var html = $(response);
                            var replacement = false;
                            html.find(self.container_name).each(function() {
                                if (replacement) {
                                    return true;
                                }
                                if ($(this).data('nextgen-gallery-id') != self.displayed_gallery_id) {
                                    return true;
                                }
                                replacement = $(this);
                            });
                            if (replacement) {
                                self.container.each(function () {
                                    var $this = $(this);

                                    if ($this.data('nextgen-gallery-id') != self.displayed_gallery_id) {
                                        return true;
                                    }

                                    // If the image gallery makes up the bulk of the post/page content the .html() call
                                    // below will empty the contents causing the browser's scroll position to be reset to
                                    // zero as the browser believes it has been pushed back to the top of the page. Here
                                    // we give the parent container a min-height equal to the gallery's height to prevent
                                    // this flicker and resetting of the scroll position.
                                    var $new_element = $(replacement.html());
                                    var promises = $new_element.find('img').toArray().map(function(img){
                                        return new Promise(function(resolve, reject){
                                            var i = new Image();
                                            i.src = img.src;
                                            $(i).on('load', resolve);
                                        });
                                    });

                                    Promise.all(promises).then(function(){
                                        $this.html($new_element);

                                        // Let the user know that we've refreshed the content
                                        $(document).trigger('refreshed');

                                        // Emit an event that doesn't require jQuery
                                        const event = new Event("nextgen_page_refreshed");
                                        document.dispatchEvent(event);
                                    });

                                    return true;
                                });
                            }
                        }
                    }
                });
            });
        };

        // Initialize
        var displayed_gallery = this.get_displayed_gallery_obj();
        if (displayed_gallery) {
            if (typeof(displayed_gallery.display_settings['ajax_pagination']) != 'undefined') {
                if (parseInt(displayed_gallery.display_settings['ajax_pagination'])) {
                    this.enable_ajax_pagination();
                }
            }
        }

        // We maintain a count of all the current AJAX actions initiated
        if (typeof(window['ngg_ajax_operation_count']) == 'undefined') {
            window['ngg_ajax_operaton_count'] = 0;
        }
    };

    // Polyfill for older browsers
    Object.setPrototypeOf = Object.setPrototypeOf || function(obj, proto) {
        obj.__proto__ = proto;
        return obj;
    };

    if (typeof window.galleries !== 'undefined') {
    Object.setPrototypeOf(
        window.galleries,
        {
            get_api_version: function() {
                return '0.1';
            },

            get_from_id: function (gallery_id) {
                var self   = this;
                var retval = null;
                var keys   = Object.keys(this);

                for (var i = 1; i <= keys.length; i++) {
                    var gallery = self[keys[i - 1]];
                    if (gallery.ID === gallery_id || gallery.ID === 'gallery_' + gallery_id || gallery.ID === parseInt(gallery_id)) {
                        retval = gallery;
                        break;
                    }
                }

                return retval;
            },

            get_from_slug: function (slug) {
                var self   = this;
                var retval = null;
                var keys   = Object.keys(this);

                for (var i = 1; i <= keys.length; i++) {
                    var gallery = self[keys[i - 1]];
                    if (gallery.slug === slug) {
                        retval = gallery;
                        break;
                    }
                }

                return retval;
            },

            get_setting: function(gallery_id, name, def) {
                var tmp = '';
                var gallery = this.get_from_id(gallery_id);
                if (gallery && typeof gallery[name] !== 'undefined') {
                    tmp = gallery[name];
                } else {
                    tmp = def;
                }

                if (tmp === 1)       tmp = true;
                if (tmp === 0)       tmp = false;
                if (tmp === '1')     tmp = true;
                if (tmp === '0')     tmp = false;
                if (tmp === 'false') tmp = false;
                if (tmp === 'true')  tmp = true;

                return tmp;
            },

            get_display_setting: function(gallery_id, name, def) {
                var tmp = '';
                var gallery = this.get_from_id(gallery_id);
                if (gallery && typeof gallery.display_settings[name] !== 'undefined') {
                    tmp = gallery.display_settings[name];
                } else {
                    tmp = def;
                }

                if (tmp === 1)       tmp = true;
                if (tmp === 0)       tmp = false;
                if (tmp === '1')     tmp = true;
                if (tmp === '0')     tmp = false;
                if (tmp === 'false') tmp = false;
                if (tmp === 'true')  tmp = true;

                return tmp;
            },

            is_widget: function(gallery_id) {
                var retval  = false;
                var gallery = this.get_from_id(gallery_id);
                var slug    = gallery.slug;

                if (slug) {
                    return slug.indexOf('widget-ngg-images') !== -1;
                }

                return retval;
            }
        }
    ); }

})(jQuery);
