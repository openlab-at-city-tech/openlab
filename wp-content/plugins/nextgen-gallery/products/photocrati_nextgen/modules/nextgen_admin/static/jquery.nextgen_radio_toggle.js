(function($) {
    // invoke this on your radio-button set with:
    // val: 0|1 - whether to activate on 'on' or 'off'
    // target: a jquery selector - the thing to toggle
    $.fn.nextgen_radio_toggle_tr = function(val, target) {
        return this.each(function() {
            var $this = $(this);
            $this.bind('change', function() {
                if ($this.val() == val) {
                    target.show('slow');
                } else {
                    target.hide('slow');
                }
            });
        });
    }
})(jQuery);