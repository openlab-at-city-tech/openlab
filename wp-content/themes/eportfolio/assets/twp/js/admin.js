(function ($) {

    var ajaxurl = eportfolio_admin.ajax_url;
    var eportfolioNonce = eportfolio_admin.ajax_nonce;
    var custom_theme_file_frame;

    // Dismiss notice
    $('.twp-custom-setup').click(function(){
        
        var data = {
            'action': 'eportfolio_notice_dismiss',
            '_wpnonce': eportfolioNonce,
        };
 
        $.post(ajaxurl, data, function( response ) {

            $('.twp-eportfolio-notice').hide();
            
        });

    });

    $(function() {
        var $menu_tabs = $('.about-tab-navbar .tab-navbar-list li a');
        $menu_tabs.on('click', function(e) {
            e.preventDefault();
            $menu_tabs.removeClass('active');
            $(this).addClass('active');

            $('.about-panel-item').fadeOut(300);
            $(this.hash).delay(300).fadeIn();
        });

    });

    // Getting Start action
    $('.twp-install-active').click(function(){

        $(this).closest('.twp-eportfolio-notice').addClass('twp-installing');

        var data = {
            'action': 'eportfolio_install_plugins',
            '_wpnonce': eportfolioNonce,
        };
 
        $.post(ajaxurl, data, function( response ) {

            window.location.href = response;
            
        });

    });


    $('.theme-recommended-plugin .recommended-plugin-status').click(function(){
        
        var id = $(this).closest('.about-items-wrap').attr('id');

        $(this).addClass('twp-activating-plugin')
        var PluginName = $(this).closest('.theme-recommended-plugin').find('h2').text();
        var PluginStatus = $(this).attr('plugin-status');
        var PluginFile = $(this).attr('plugin-file');
        var PluginFolder = $(this).attr('plugin-folder');
        var PluginSlug = $(this).attr('plugin-slug');
        var pluginClass = $(this).attr('plugin-class');

        var data = {
            'single': true,
            'PluginStatus': PluginStatus,
            'PluginFile': PluginFile,
            'PluginFolder': PluginFolder,
            'PluginSlug': PluginSlug,
            'PluginName': PluginName,
            'pluginClass': pluginClass,
            'action': 'eportfolio_install_plugins',
            '_wpnonce': eportfolioNonce,
        };
 
        $.post(ajaxurl, data, function( response ) {
            
            var active = eportfolio_admin.active
            var deactivate = eportfolio_admin.deactivate
            $('#'+id+' .recommended-plugin-status').empty();

            if( response == 'Deactivated' ){
                
                $('#'+id+' .theme-recommended-plugin').removeClass('recommended-plugin-active');
                $('#'+id+' .recommended-plugin-status').removeClass('twp-plugin-active');
                $('#'+id+' .recommended-plugin-status').addClass('twp-plugin-deactivate');
                $('#'+id+' .recommended-plugin-status').html(active);
                $('#'+id+' .recommended-plugin-status').attr('plugin-status','deactivate');

            }else if( response == 'Activated' ){
                
                $('#'+id+' .theme-recommended-plugin').addClass('recommended-plugin-active');
                $('#'+id+' .recommended-plugin-status').removeClass('twp-plugin-deactivate');
                $('#'+id+' .recommended-plugin-status').addClass('twp-plugin-active');
                $('#'+id+' .recommended-plugin-status').html(deactivate);
                $('#'+id+' .recommended-plugin-status').attr('plugin-status','active');

            }else{
                
                $('#'+id+' .theme-recommended-plugin').removeClass('recommended-plugin-active');
                $('#'+id+' .recommended-plugin-status').removeClass('twp-plugin-not-install');
                $('#'+id+' .recommended-plugin-status').addClass('twp-plugin-active');
                $('#'+id+' .recommended-plugin-status').html(active);
                $('#'+id+' .recommended-plugin-status').attr('plugin-status','deactivate');

            }

            $('.recommended-plugin-status').removeClass('twp-activating-plugin');
            
        });
    });

}(jQuery));