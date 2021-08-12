(function($) {
    'use strict';
    $(function() {
        livepreview();
        $('.single-slider').jRange({
            from: 0,
            to: 30,
            step: 1,
            showScale: false,
            format: '%s px',
            width: '100%',
            showLabels: true,
            onstatechange: function() {
                livepreview();
            },
        });
        $('.colorSelector').ColorPicker({
            color: '#0000ff',
            livePreview: true,
            onShow: function(colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function(colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function(hsb, hex, rgb, el) {
                setcolor(el, hex);
            }
        }).bind('click', function() {
            $(this).ColorPickerSetColor($(this).data('color'));
        });
        $('#wpdx-btn-text').keyup(function(event) {
            $('#wpdpx-btn').text($(this).val());
        });
        $('#dropr-reset').click(function(e) {
            e.preventDefault();
            // Handle generic settings.
            var $generic_sec = $('.wpdpx-generic-settings-main');
            $generic_sec.find('.wpdbx-form-radio-fields').each(function() {
                var $elem = $(this);
                var defaultVal = $(this).data('default');
                $elem.find('input[value="' + defaultVal + '"]').prop('checked', true);
            });

            // Handle Button styles.
            var $sec = $('.settings-main');
            $.each($sec.find('input'), function(i) {
                $(this).val($(this).data('default')).change();
                if ($(this).hasClass('single-slider')) {
                    $(this).jRange('setValue', $(this).data('default'));
                }
            });
            $.each($sec.find('.colorSelector'), function(i) {
                $(this).find('div').css('background-color', $(this).data('default'));
                $(this).data('color', $(this).data('default'));
            });
            livepreview();
        });

        function setcolor(el, hex) {
            var txt = $(el).data('txt');
            $(el).find('div').css('backgroundColor', '#' + hex);
            $('#' + txt).val('#' + hex);
            livepreview();
        }

        function livepreview() {
            var styleappend = "";
            $.each($('[data-style]'), function(node) {
                styleappend = "";
                if ($(this).val()) {
                    if ($(this).data('stylea')) {
                        styleappend = $(this).data('stylea');
                    }
                    if ($(this).data('style1')) {
                        $('#wpdpx-btn').css($(this).data('style1'), $(this).val() + styleappend)
                    }
                    $('#wpdpx-btn').css($(this).data('style'), $(this).val() + styleappend);
                }
            });
        }
    });
})(jQuery);
