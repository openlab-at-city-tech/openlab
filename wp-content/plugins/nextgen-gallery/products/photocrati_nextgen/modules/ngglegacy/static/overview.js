/*
 * Overview Page JS for NextGEN Gallery: Provides JS for 
 * navigating tabs on the NextGEN Overview page. 
 * 
 */
(function($) {
    $(function() {
        $('#ngg-tabs-wrapper').find('a').on('click', function () {
            $('#ngg-tabs-wrapper').find('a').removeClass('nav-tab-active');
            $('.ngg-tab').removeClass('active');

            var id = jQuery(this).attr('id').replace('-link', '');
            $('#' + id).addClass('active');
            $(this).addClass('nav-tab-active');
        });

        var activeTab = window.location.hash.replace('#top#', '');
        if ('' === activeTab) {
            activeTab = $('.ngg-tab').attr('id');
        }

        $('#' + activeTab).addClass('active');
        $('#' + activeTab + '-tab').addClass('nav-tab-active');
        $('.nav-tab-active').trigger('click');
    });
})(jQuery);