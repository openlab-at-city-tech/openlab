jQuery(function($){

    /* Set active link color and show form sections when menu item is clicked */
    $('.ngg_page_content_menu a:first-of-type').addClass("ngg_page_content_menu_active");

    $('.ngg_page_content_menu a').click( function(e) {
        
        /* Add color to only the active link */
        $('.ngg_page_content_menu a').removeClass("ngg_page_content_menu_active");
        $(this).addClass("ngg_page_content_menu_active");

        /* Show the associated div */
        var id = $(this).attr('data-id');
        $('.ngg_page_content_main > div').css("display", "none");
        $('div[data-id="' + $(this).attr('data-id') + '"]').css("display", "block");

    });

    /* Set up responsive menu for mobile devices. */   
    $(".responsive-menu-icon").remove();
    $(".ngg_page_content_menu").addClass("responsive-menu").before('<div class="responsive-menu-icon"></div>');

    $(".responsive-menu-icon").click(function(){
        $(this).next(".ngg_page_content_menu").slideToggle( "fast" );
    });

    $(window).resize(function(){
        if(window.innerWidth > 640) {
            $(".ngg_page_content_menu").removeAttr("style");
            $(".responsive-menu > a").removeClass("menu-open");
        }
    });

    $(".responsive-menu > a").click(function(event){
        if(window.innerWidth < 782) {
            $(this).parent(".ngg_page_content_menu").slideToggle( "fast" );
        }
    });

    // When a submit button is clicked...
    $('input[type="submit"], button[type="submit"]').click(function(e){
        var $button = $(this);
        var message = false;

        // Check if a confirmation dialog is required
        if ((message = $button.attr('data-confirm'))) {
            if (!confirm(message)) {
                e.preventDefault();
                return;
            }
        }

        // Check if this is a proxy button for another field
        if ($button.attr('name') && $button.attr('name').indexOf('_proxy') != -1) {

            // Get the value to set
            var value = $button.attr('data-proxy-value');
            if (!value) value = $button.attr('value');

            // Get the name of the field that is being proxied
            var field_name = $button.attr('name').replace('_proxy', '');

            // Try getting the existing field
            var $field = $('input[name="'+field_name+'"]');
            if ($field.length > 0) $field.val(value);
            else {
                $field = $('<input/>').attr({
                    type: 'hidden',
                    name: field_name,
                    value: value
                });
                $button.parents('form').append($field);
            }
        }
    });


    // Toggle the advanced settings
    $('.nextgen_advanced_toggle_link').on('click', function(e){
        e.preventDefault();
        var form_id = '#'+$(this).attr('rel');
        var btn = $(this);
        $(form_id).toggle(500, 'swing', function(){
            if ($(this).hasClass('hidden')) {
                $(this).removeClass('hidden');
                btn.text(btn.attr('active_label'));
            }
            else {
                $(this).addClass('hidden');
                btn.text(btn.attr('hidden_label'));
            }
        });
    });

    $('input.nextgen_settings_field_colorpicker').wpColorPicker();
    $('#ngg_page_content').css('visibility', 'visible');

    // Handle the "recover" and "delete" links in Manage Gallery > each image row
    $('#ngg-listimages .row-actions .confirmrecover, #ngg-listimages .row-actions.submitdelete.delete').on(
        'click',
        function(event) {
            var target = event.target;
            if (!confirm(target.getAttribute('data-question') + ' "' + target.getAttribute('data-text') + '"')) {
                event.preventDefault();
            }
        }
    );
});
