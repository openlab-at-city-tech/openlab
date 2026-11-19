jQuery(document).ready(function ( $ ) {
    var a11y = window.advgbA11y || null;
    var l10n = (a11y && a11y.l10n) || {};

    function format(str, value) {
        return a11y ? a11y.format(str, value) : String(str).replace('%s', value);
    }

    // Give each overlay link a meaningful name, or hide it when it does nothing.
    $(".advgb-images-slider-block").each(function () {
        var $block = $(this);
        var isLightbox = $block.hasClass('advgb-images-slider-lightbox');

        $block.find("a.advgb-image-slider-overlay").each(function () {
            var $link = $(this);
            var $item = $link.closest('.advgb-image-slider-item');
            var title = $.trim($item.find('.advgb-image-slider-title').first().text())
                || $.trim($item.find('img').first().attr('alt') || '');
            var href = $link.attr('href') || '';

            if (isLightbox) {
                $link.attr({
                    role: 'button',
                    'aria-label': title
                        ? format(l10n.openImage || 'Open image: %s', title)
                        : (l10n.openImageNoTitle || 'Open image')
                });
                $link.on('keydown', function (e) {
                    if (e.key === ' ' || e.key === 'Spacebar') {
                        e.preventDefault();
                        $link.trigger('click');
                    }
                });
            } else if (href && href !== '#') {
                var label = title || (l10n.openLink || 'Open link');
                if ($link.attr('target') === '_blank') {
                    label += ' ' + (l10n.newTab || '(opens in a new tab)');
                }
                $link.attr('aria-label', label);
            } else {
                // Decorative overlay with no action.
                $link.addClass('advgb-image-slider-overlay--inert');
            }
        });
    });

    // Slick resets tabindex on links inside visible slides, so re-apply after each change.
    function hideInertOverlays($scope) {
        $scope.find('a.advgb-image-slider-overlay--inert').attr({ tabindex: '-1', 'aria-hidden': 'true' });
    }

    var $sliders = $(".advgb-images-slider-block .advgb-images-slider:not(.slick-initialized)");
    $sliders.slick({
        dots: true,
        adaptiveHeight: true,
    });

    hideInertOverlays($(".advgb-images-slider-block"));

    if (a11y) {
        $sliders.each(function () {
            a11y.slider($(this), {
                label: l10n.slideshow || 'Slideshow',
                onUpdate: hideInertOverlays
            });
        });
    } else {
        $sliders.on('afterChange', function () {
            var $s = $(this);
            setTimeout(function () { hideInertOverlays($s); }, 0);
        });
    }
});
