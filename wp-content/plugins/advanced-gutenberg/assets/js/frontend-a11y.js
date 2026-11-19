/**
 * Front-end accessibility helpers shared by PublishPress Blocks sliders (slick)
 * and lightboxes (colorbox). Runtime-only: never changes saved block markup.
 *
 * Strings are localized via wp_localize_script( 'advgb_frontend_a11y', 'advgbA11yL10n' ).
 */
(function ($, window, document) {
    'use strict';

    if (!$ || window.advgbA11y) {
        return;
    }

    var l10n = $.extend({
        slideshow: 'Slideshow',
        carousel: 'carousel',
        slide: 'slide',
        slideOf: '%1$s of %2$s',
        goToSlide: 'Go to slide %s',
        previousSlide: 'Previous slide',
        nextSlide: 'Next slide',
        pauseSlideshow: 'Pause slideshow',
        imageViewer: 'Image viewer',
        videoPlayer: 'Video player',
        video: 'Video',
        close: 'Close',
        previous: 'Previous',
        next: 'Next',
        imageCurrent: 'Image {current} of {total}'
    }, window.advgbA11yL10n || {});

    var uid = 0;

    function format(str) {
        var args = Array.prototype.slice.call(arguments, 1);
        var i = 0;
        return String(str).replace(/%(?:(\d+)\$)?s/g, function (m, pos) {
            var val = pos ? args[parseInt(pos, 10) - 1] : args[i++];
            return typeof val === 'undefined' ? '' : val;
        });
    }

    function prefersReducedMotion() {
        return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    }

    function ensureId($el, prefix) {
        var id = $el.attr('id');
        if (!id) {
            uid++;
            id = (prefix || 'advgb-a11y') + '-' + uid;
            $el.attr('id', id);
        }
        return id;
    }

    function getSlick($slider) {
        var el = $slider.get(0);
        return el && el.slick ? el.slick : null;
    }

    /**
     * Apply carousel semantics. Runs after slick's own initADA() so our values win
     * (slick re-applies its tab roles after every slide change).
     */
    function applySliderAria($slider, label) {
        var slick = getSlick($slider);
        if (!slick) {
            return;
        }

        $slider.attr({
            role: 'region',
            'aria-roledescription': l10n.carousel,
            'aria-label': label || l10n.slideshow
        });

        var $realSlides = slick.$slides ? slick.$slides : $slider.find('.slick-slide').not('.slick-cloned');
        var total = $realSlides.length;
        $realSlides.each(function (index) {
            $(this)
                .attr({
                    role: 'group',
                    'aria-roledescription': l10n.slide,
                    'aria-label': format(l10n.slideOf, index + 1, total)
                })
                .removeAttr('aria-describedby');
        });

        // Default slick arrows (custom arrows keep their own labels).
        $slider.find('> button.slick-prev').attr('aria-label', l10n.previousSlide);
        $slider.find('> button.slick-next').attr('aria-label', l10n.nextSlide);

        var $dots = slick.$dots;
        if ($dots && $dots.length) {
            $dots.removeAttr('role');
            $dots.find('li').removeAttr('role').each(function (index) {
                var $li = $(this);
                var $btn = $li.find('button').first();
                $btn
                    .removeAttr('role aria-selected aria-controls tabindex')
                    .attr('aria-label', format(l10n.goToSlide, index + 1));
                if ($li.hasClass('slick-active')) {
                    $btn.attr('aria-current', 'true');
                } else {
                    $btn.removeAttr('aria-current');
                }
            });
        }
    }

    function isAutoplaying($slider) {
        var slick = getSlick($slider);
        return !!(slick && slick.options && slick.options.autoplay);
    }

    function syncPauseButton($slider) {
        var $btn = $slider.data('advgbA11yPauseButton');
        var slick = getSlick($slider);
        if (!$btn || !slick) {
            return;
        }
        $btn.attr('aria-pressed', slick.paused ? 'true' : 'false');
    }

    /**
     * Inject a visible pause/play toggle before the slider when autoplay is on.
     * Idempotent: safe to call repeatedly (e.g. when another script enables autoplay later).
     */
    function ensurePauseButton($slider, $before) {
        if ($slider.data('advgbA11yPauseButton') || !isAutoplaying($slider)) {
            return;
        }

        var $btn = $('<button type="button" class="advgb-slider-pause"></button>')
            .attr('aria-pressed', 'false')
            .append($('<span class="advgb-slider-pause-icon" aria-hidden="true"></span>'))
            .append($('<span class="advgb-slider-pause-text"></span>').text(l10n.pauseSlideshow));

        $btn.attr('aria-controls', ensureId($slider, 'advgb-slider'));

        ($before && $before.length ? $before : $slider).before($btn);
        $slider.data('advgbA11yPauseButton', $btn);

        $btn.on('click', function () {
            var slick = getSlick($slider);
            if (!slick) {
                return;
            }
            if (slick.paused) {
                $slider.slick('slickPlay');
            } else {
                $slider.slick('slickPause');
            }
            syncPauseButton($slider);
        });

        if (prefersReducedMotion()) {
            $slider.slick('slickPause');
        }

        syncPauseButton($slider);
    }

    /**
     * Enhance an initialized slick slider.
     *
     * @param {jQuery} $slider  Element slick() was called on.
     * @param {Object} options  { label: string, pauseBefore: jQuery, onUpdate: function($slider) }
     */
    function slider($slider, options) {
        options = options || {};

        $slider.each(function () {
            var $s = $(this);
            if ($s.data('advgbA11ySlider')) {
                return;
            }
            $s.data('advgbA11ySlider', true);

            var update = function () {
                // Defer so slick's own initADA (run after afterChange) does not overwrite us.
                window.setTimeout(function () {
                    applySliderAria($s, options.label);
                    ensurePauseButton($s, options.pauseBefore);
                    syncPauseButton($s);
                    if (typeof options.onUpdate === 'function') {
                        options.onUpdate($s);
                    }
                }, 0);
            };

            $s.on('init.advgbA11y reInit.advgbA11y afterChange.advgbA11y breakpoint.advgbA11y', update);

            update();
        });
    }

    /* ----------------------------------------------------------------------
     * Colorbox helpers
     * -------------------------------------------------------------------- */

    var lastOpener = null;

    function isFocusable(el) {
        if (!el) {
            return false;
        }
        var $el = $(el);
        if ($el.is('a[href], button, input, select, textarea')) {
            return true;
        }
        var tabindex = $el.attr('tabindex');
        return typeof tabindex !== 'undefined' && parseInt(tabindex, 10) >= 0;
    }

    function focusTarget(el) {
        if (!el) {
            return null;
        }
        if (isFocusable(el)) {
            return el;
        }
        var $inner = $(el).find('a[href], button, [tabindex]').filter(function () {
            return isFocusable(this) && $(this).attr('aria-hidden') !== 'true';
        }).first();
        return $inner.length ? $inner.get(0) : null;
    }

    /**
     * Common colorbox options: localized controls, no built-in returnFocus
     * (it targets the often non-focusable element); focus goes back to the opener.
     */
    function colorboxOptions() {
        return {
            returnFocus: false,
            close: l10n.close,
            previous: l10n.previous,
            next: l10n.next,
            current: l10n.imageCurrent,
            onOpen: function () {
                lastOpener = this;
            },
            onClosed: function () {
                var target = focusTarget(lastOpener);
                lastOpener = null;
                if (target && typeof target.focus === 'function') {
                    target.focus();
                }
            }
        };
    }

    function colorboxComplete(type) {
        var $box = $('#colorbox');
        var title = $.trim($('#cboxTitle').text());
        $box.attr({
            'aria-modal': 'true',
            'aria-label': title || (type === 'video' ? l10n.videoPlayer : l10n.imageViewer)
        });
    }

    /**
     * Merge our options into a colorbox settings object, chaining callbacks.
     */
    function withColorbox(settings, type) {
        var ours = colorboxOptions();
        var merged = $.extend({}, settings);

        $.each(['returnFocus', 'close', 'previous', 'next', 'current'], function (i, key) {
            if (typeof settings[key] === 'undefined') {
                merged[key] = ours[key];
            }
        });
        merged.returnFocus = false;

        $.each(['onOpen', 'onClosed'], function (i, key) {
            var theirs = settings[key];
            merged[key] = function () {
                ours[key].apply(this, arguments);
                if (typeof theirs === 'function') {
                    theirs.apply(this, arguments);
                }
            };
        });

        var theirComplete = settings.onComplete;
        merged.onComplete = function () {
            if (typeof theirComplete === 'function') {
                theirComplete.apply(this, arguments);
            }
            colorboxComplete(type);
        };

        return merged;
    }

    /**
     * Make a non-interactive lightbox trigger keyboard operable.
     */
    function makeButton($el, label) {
        $el.each(function () {
            var $t = $(this);
            if ($t.data('advgbA11yButton')) {
                return;
            }
            $t.data('advgbA11yButton', true);
            $t.attr({ role: 'button', tabindex: '0' });
            if (label) {
                $t.attr('aria-label', typeof label === 'function' ? label.call(this, $t) : label);
            }
            $t.on('keydown.advgbA11y', function (e) {
                if (e.target !== this) {
                    return;
                }
                if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                    e.preventDefault();
                    $t.trigger('click');
                }
            });
        });
    }

    window.advgbA11y = {
        l10n: l10n,
        format: format,
        ensureId: ensureId,
        prefersReducedMotion: prefersReducedMotion,
        slider: slider,
        withColorbox: withColorbox,
        colorboxComplete: colorboxComplete,
        makeButton: makeButton
    };
})(window.jQuery, window, document);
