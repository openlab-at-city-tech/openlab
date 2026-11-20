jQuery(document).ready(function ( $ ) {
    var a11y = window.advgbA11y || null;
    var l10n = (a11y && a11y.l10n) || {};
    var $triggers = $('.advgb-video-lightbox');

    function videoTitle($el) {
        // data-advgb-video-title is added at render time by the render_block filter.
        return $.trim(String($el.attr('data-advgb-video-title') || ''));
    }

    // Decorative play icon.
    $triggers.find('.advgb-play-button svg').attr({ 'aria-hidden': 'true', focusable: 'false' });

    var settings = {
        title: function () {
            return null;
        },
        maxWidth: '90%',
        maxHeight: '85%',
        fixed: true,
        className: 'advgb_lightbox',
        innerWidth: '80%',
        innerHeight: '80%',
        iframe: function () {
            return $(this).data('source') !== 'local';
        },
        href: function () {
            if ($(this).data('source') === 'local')
                return false;

            return $(this).data('video');
        },
        html: function () {
            if ($(this).data('source') !== 'local')
                return false;

            let videoAttributesStr = String($(this).data('video-attr') || '');
            let videoPreload = $(this).data('video-preload');
            let attrs = videoAttributesStr.split(',').filter(Boolean);
            // Autoplaying sound must be stoppable: force controls when autoplay is not muted.
            if (attrs.indexOf('autoplay') !== -1 && attrs.indexOf('muted') === -1 && attrs.indexOf('controls') === -1) {
                attrs.push('controls');
            }
            let videoAttributes = attrs.join(' ');
            return '<video src="'+ $(this).data('video') +'" '+videoAttributes+' preload="'+videoPreload+'" style="height: 99%; width: 100%; display: block; margin: auto"></video>';
        },
        onComplete: function () {
            var title = videoTitle($(this)) || l10n.video || 'Video';
            $('#cboxLoadedContent iframe').attr('title', title);
            $('#cboxLoadedContent video').attr('aria-label', title);
            $('#colorbox').attr('aria-label', title);
        }
    };

    if (a11y) {
        settings = a11y.withColorbox(settings, 'video');
        // withColorbox labels the dialog after our onComplete; keep the video title when known.
        var completeWithLabel = settings.onComplete;
        settings.onComplete = function () {
            completeWithLabel.apply(this, arguments);
            var title = videoTitle($(this));
            if (title) {
                $('#colorbox').attr('aria-label', title);
            }
        };
    }

    $triggers.colorbox(settings);

    // The trigger is a <div>: make it reachable and operable from the keyboard.
    if (a11y) {
        a11y.makeButton($triggers, function ($t) {
            var title = videoTitle($t);
            return title
                ? a11y.format(l10n.playVideo || 'Play video: %s', title)
                : (l10n.playVideoNoTitle || 'Play video');
        });
    }
});
