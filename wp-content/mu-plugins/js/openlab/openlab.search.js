/**
 * OpenLab search dropdowns
 */

(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    var resizeTimer;

    OpenLab.search = {
        init: function () {

            //search
            if ($('.search-trigger-wrapper').length) {
                OpenLab.search.searchBarLoadActions();
                $('.search-trigger').on('click', function () {
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

            var select = $('.search-form-wrapper .hidden-custom-select select');
            var adminBar = $('#wpadminbar');
            var mode = searchTrigger.data('mode');
            var location = searchTrigger.data('location');
            var searchForm = $('.search-form-wrapper.search-mode-' + mode + '.search-form-location-' + location);
            if (!searchTrigger.hasClass('in-action')) {
                searchTrigger.addClass('in-action');
                if (searchTrigger.parent().hasClass('search-live')) {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile') {
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
                    if (searchTrigger.data('mode') == 'mobile') {
                        adminBar.addClass('dropped');
                        adminBar.animate({
                            top: "+=" + searchForm.data('thisheight')
                        }, 700);
                    }
                    searchForm.slideDown(700, function () {
                        searchTrigger.removeClass('in-action');
                    });
                }
                select.customSelect();
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
        }
    }

    $(document).ready(function () {

        OpenLab.search.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {

            if ($('.search-trigger-wrapper.search-live').length) {
                OpenLab.search.searchBarEventActions($('.search-trigger-wrapper.search-live').find('.search-trigger'));
            }

        }, 250);

    });

})(jQuery);