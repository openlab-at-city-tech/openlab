(function($) {
    var init = function() {
        if ($.fn.qtip) {
            $('.ari-form-tooltip').qtip({
                position: {
                    my: 'bottom left',

                    at: 'top left',

                    adjust: {
                        y: -10
                    }
                },

                style: {
                    classes: 'qtip-dark'
                }
            });
        } else if ($.fn.tooltip) {
            $('.ari-form-tooltip').tooltip({
                position: {
                    my: 'center bottom-20',
                    at: 'center top',
                    within: '.wrap'
                },
                open: function(event, ui) {
                    if (ui.tooltip.parent('.ari-theme').length == 0) {
                        ui.tooltip.wrap('<div class="ari-theme"></div>');
                    }
                },
                close: function(event, ui) {
                    var parent = ui.tooltip.parent('.ari-theme');
                    if (parent.length > 0) {
                        setTimeout(function() {
                            parent.remove();
                        }, 400);
                    }
                }
            });
        };

        $('.ari-group-switcher').on('click', function() {
            var $this = $(this),
                childGroup = $this.attr('data-child-group'),
                groupVisible = $this.is(':checked');

            if (!childGroup)
                return ;

            var childGroupSelector = '.params-' + childGroup,
                childGroupContainer = $(childGroupSelector);

            if (groupVisible) {
                childGroupContainer.show();
            } else {
                childGroupContainer.hide();
            }
        });

        $('.ari-form-slider').each(function() {
            var $this = $(this),
                elId = $this.attr('data-slider-id'),
                el = $('#' + elId),
                sliderOptions = $this.attr('data-slider-options');

            el.on('focus', function() {
                el.blur();
                $this.find('.ui-slider-handle').focus();
            });

            sliderOptions = JSON.parse(sliderOptions) || {};
            sliderOptions['slide'] = function(e, ui) {
                el.val(ui.value);
            };

            $this.slider(sliderOptions);
        });

        if ($.fn.spinner)
            $('.ari-form-spinner').each(function() {
                var $this = $(this),
                    spinnerOptions = $this.attr('data-spinner-options');

                spinnerOptions = JSON.parse(spinnerOptions) || {};
                $this.spinner(spinnerOptions);
            });

        if ($.fn.wpColorPicker)
            $('.ari-form-color').wpColorPicker();
    };

    $(document).ready(function() {
        init();
    });
})(jQuery);