jQuery(document).ready(function ( $ ) {
    var a11y = window.advgbA11y || null;
    var l10n = (a11y && a11y.l10n) || {};
    var $triggers = $('.advgb-lightbox');

    var settings = {
        title: function () {
            return $(this).find('.advgb-image-title').text();
        },
        maxWidth: '90%',
        maxHeight: '85%',
        fixed: true,
        className: 'advgb_lightbox',
        href: function () {
            const $block = $(this);
            const $img = $block.find('img.advgb-image').first();

            return $block.data('image') || $img.data('image') || $img.attr('src');
        },
        onComplete: function () {
            $('#cboxLoadedContent .cboxPhoto').attr('alt', $(this).find('img.advgb-image').first().attr('alt') || '');
        }
    };

    $triggers.colorbox(a11y ? a11y.withColorbox(settings, 'image') : settings);

    // The trigger is a <div>: make it reachable and operable from the keyboard.
    if (a11y) {
        a11y.makeButton($triggers, function ($t) {
            var name = $.trim($t.find('.advgb-image-title').first().text())
                || $.trim($t.find('img.advgb-image').first().attr('alt') || '');
            return name
                ? a11y.format(l10n.enlargeImage || 'Enlarge image: %s', name)
                : (l10n.enlargeImageNoTitle || 'Enlarge image');
        });
    }
});
