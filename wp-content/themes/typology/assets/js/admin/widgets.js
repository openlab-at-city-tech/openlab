(function($) {
    $(document).ready(function() {

        /* Initialize sortable options */
        typology_opt_sortable();

        $(document).on('widget-added', function(e) {
            typology_opt_sortable();


        });

        $(document).on('widget-updated', function(e) {
            typology_opt_sortable();

        });


        /* Make some options sortable */
        function typology_opt_sortable() {
            $(".typology-widget-content-sortable").sortable({
                revert: false,
                cursor: "move"
            });
        }


    });

})(jQuery);