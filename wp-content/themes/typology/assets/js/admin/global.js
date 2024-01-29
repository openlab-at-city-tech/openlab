(function($) {

    "use strict";

    var WatchForChanges = {

        init: function() {
            var $watchers = $('.typology-watch-for-changes');

            if (typology_empty($watchers)) {
                return;
            }

            $watchers.each(this.initWatching);
        },

        initWatching: function(i, elem) {
            var $elem = $(elem),
                watchedElemClass = $elem.data('watch'),
                forValue = $elem.data('hide-on-value');

            $('body').on('change', '.' + watchedElemClass, hideByValue);

            function hideByValue() {
                var $this = $(this);

                if (!$this.hasClass(watchedElemClass)) {
                    $this = $('.' + watchedElemClass + ':checked, ' + '.' + watchedElemClass + ':checked');
                }

                if (typology_empty($this)) {
                    return false;
                }

                var val = $this.val();

                if (val === forValue) {
                    $elem.slideUp('fast');
                    return true;
                }

                $elem.slideDown('fast');
                return false;
            }

            hideByValue();
        }

    };


    $(document).ready(function() {

        $('body').on('click', 'img.typology-img-select', function(e) {
            e.preventDefault();
            $(this).closest('ul').find('img.typology-img-select').removeClass('selected');
            $(this).addClass('selected');
            $(this).closest('ul').find('input').removeAttr('checked').prop('checked', false);
            $(this).closest('li').find('input').attr('checked', 'checked').prop('checked', true);
        });

        WatchForChanges.init();


        $("body").on('click', '#typology_welcome_box_hide', function(e) {
            e.preventDefault();
            $(this).parent().fadeOut(300).remove();
            $.post(ajaxurl, { action: 'typology_hide_welcome' }, function(response) {});
        });

        $("body").on('click', '#typology_update_box_hide', function(e) {
            e.preventDefault();
            $(this).parent().remove();
            $.post(ajaxurl, { action: 'typology_update_version' }, function(response) {});
        });

        $('body').on('click', '.mks-twitter-share-button', function(e) {
            e.preventDefault();
            var data = $(this).attr('data-url');
            typology_social_share(data);
        });



        $('.typology-redux-marker').each(function() {
            var elem = $(this);
            elem.parents('tr:first').css({ display: 'none' }).prev('tr').css('border-bottom', 'none');
            var group = elem.parents('.redux-group-tab:first');
            if (!group.hasClass('sectionsChecked')) {
                group.addClass('sectionsChecked');
                var test = group.find('.redux-section-indent-start h3');
                jQuery.each(
                    test,
                    function(key, value) {
                        jQuery(value).css('margin-top', '20px');
                    }
                );

                if (group.find('h3:first').css('margin-top') == "20px") {
                    group.find('h3:first').css('margin-top', '0');
                }
            }

        });

    }); // end document ready

    function typology_social_share(data) {
        window.open(data, "Share", 'height=500,width=760,top=' + ($(window).height() / 2 - 250) + ', left=' + ($(window).width() / 2 - 380) + 'resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0');
    }

    /**
     * Checks if variable is empty or not
     *
     * @param variable
     * @returns {boolean}
     */
    function typology_empty(variable) {

        if (typeof variable === 'undefined') {
            return true;
        }

        if (variable === 0 || variable === '0') {
            return true;
        }

        if (variable === null) {
            return true;
        }

        if (variable.length === 0) {
            return true;
        }

        if (variable === "") {
            return true;
        }

        if (variable === false) {
            return true;
        }

        if (typeof variable === 'object' && $.isEmptyObject(variable)) {
            return true;
        }

        return false;
    }

})(jQuery);