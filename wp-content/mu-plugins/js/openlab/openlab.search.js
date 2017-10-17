/**
 * OpenLab search dropdowns
 */

if (window.OpenLab === undefined) {
    var OpenLab = {};
}

var searchResizeTimer = {};

OpenLab.search = (function ($) {
    return{
        init: function () {

            //search
            if ($('.search-trigger-wrapper').length) {
                OpenLab.search.searchBarLoadActions();
                $('.search-trigger').on('click', function (e) {
                    e.preventDefault();
                    OpenLab.search.searchBarEventActions($(this));
                });
            }

        },
        searchBarLoadActions: function () {

            $('.search-form-wrapper').each(function () {
                var searchFormDim = OpenLab.search.invisibleDimensions($(this));
                $(this).data('thisheight', searchFormDim.height);
            });

        },
        searchBarEventActions: function (searchTrigger) {

            //var select = $('.search-form-wrapper .hidden-custom-select select');
            var adminBar = $('#wpadminbar');
            var mode = searchTrigger.data('mode');
            var location = searchTrigger.data('location');
            var searchForm = $('.search-form-wrapper.search-mode-' + mode + '.search-form-location-' + location);
            if (!searchTrigger.hasClass('in-action')) {
                searchTrigger.addClass('in-action');
                if (searchTrigger.parent().hasClass('search-live')) {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile' && searchTrigger.data('location') == 'header') {
                        adminBar.animate({
                            top: "-=" + searchForm.data('thisheight')
                        }, 700);
                        adminBar.removeClass('dropped');
                    }

                    searchForm.slideUp(800, function () {
                        searchTrigger.removeClass('in-action');
                    });


                } else {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile' && searchTrigger.data('location') == 'header') {
                        adminBar.addClass('dropped');
                        adminBar.animate({
                            top: "+=" + searchForm.data('thisheight')
                        }, 700);
                    }
                    searchForm.slideDown(700, function () {
                        searchTrigger.removeClass('in-action');
                    });
                }
                //select.customSelect();
            }

        },
        invisibleDimensions: function (el) {

            $(el).css({
                'display': 'block',
                'visibility': 'hidden'
            });
            var dim = {
                height: $(el).outerHeight(),
                width: $(el).outerWidth()
            };
            $(el).css({
                'display': 'none',
                'visibility': ''
            });
            return dim;
        },
        isBreakpoint: function (alias) {
            return $('.device-' + alias).is(':visible');
        },
    }
})(jQuery, OpenLab);

(function ($) {
    
    var legacyWidth = $(window).width();

    $(document).ready(function () {

        OpenLab.search.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(searchResizeTimer);
        searchResizeTimer = setTimeout(function () {

            if ($(this).width() != legacyWidth) {
                legacyWidth = $(this).width();
                if ($('.search-trigger-wrapper.search-live').length) {
                    OpenLab.search.searchBarEventActions($('.search-trigger-wrapper.search-live').find('.search-trigger'));
                }
            }

        }, 250);

    });

})(jQuery);