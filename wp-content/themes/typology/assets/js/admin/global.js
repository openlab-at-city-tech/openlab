(function($) {

    var WatchForChanges = {

        init: function (){
            var $watchers = $('.typology-watch-for-changes');

            if(typology_empty($watchers)){
                return;
            }

            $watchers.each(this.initWatching)
        },

        initWatching: function (i, elem){
            var $elem = $(elem),
                watchedElemClass = $elem.data('watch'),
                forValue = $elem.data('hide-on-value');

            $('body').on('change', '.' + watchedElemClass, hideByValue);

            function hideByValue(){
                var $this = $(this);

                if(!$this.hasClass(watchedElemClass)){
                    $this = $('.' + watchedElemClass + ':checked, ' + '.' + watchedElemClass + ':checked');
                }

                if(typology_empty($this)){
                    return false;
                }

                var val = $this.val();

                if(val === forValue){
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
            $(this).closest('ul').find('input').removeAttr('checked');
            $(this).closest('li').find('input').attr('checked', 'checked');
        });

        WatchForChanges.init();
    });


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