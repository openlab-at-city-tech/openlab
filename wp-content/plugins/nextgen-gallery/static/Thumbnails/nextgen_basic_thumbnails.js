jQuery(function($) {
    var ngg_imagebrowser_init = function() {
        var imagebrowser_links = $('a.use_imagebrowser_effect');
        imagebrowser_links.on('click', function(e) {
            e.preventDefault();
            var success = true;
            var $this   = $(this);
            var image_id   = $this.attr('data-image-id');
            var image_slug = $this.attr('data-image-slug');
            var image_url  = $this.attr('data-imagebrowser-url');

            var url = window.location.toString();
            url = url.split('/' + photocrati_ajax.ngg_param_slug + '/').shift();
            if (url.substr(-1) === '/')
                url = url.substr(0, url.length - 1);

            image_id = image_slug ? image_slug : image_id;
            try {
                if (!image_id)
                    image_id = parseInt($this.parents('.ngg-gallery-thumbnail-box').attr('id').match(/\d+/).join(''));
            } catch (ex) {
                success = false;
            }

            if (success) {

                url = image_url.replace('%STUB%', image_id);

                /* TODO: Remove this entire chunk. It should be unecessary.
                // Custom permalinks are disabled. So we have to redirect to /index.php/nggallery/image/n?qs=1
                if (photocrati_ajax.wp_root_url.indexOf('index.php') >= 0) {
                    url = photocrati_ajax.wp_root_url + "/" + photocrati_ajax.ngg_param_slug + "/image/" + image_id;
                    if (window.location.toString().indexOf('?') >= 0)
                        url += '?'+window.location.toString().split('?').pop();
                } else {
                    // Just append the slug
                    url += "/" + photocrati_ajax.ngg_param_slug + "/image/" + image_id;
                } */

                window.location = url;
            }
        });

        // Unregister any onclick handlers added after the above has executed to avoid conflicts
        if (imagebrowser_links.length > 0) {
            setTimeout(function() {
                imagebrowser_links.each(function() {
                    this.onclick = null;
                });
            }, 200);
        }
    };

    $(this).on('refreshed', ngg_imagebrowser_init);
    ngg_imagebrowser_init();
});
