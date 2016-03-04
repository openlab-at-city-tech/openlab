var Hero = ( function($){

    var $window             = $(window),
        $video              = $(".videoContainer"),
        $homeBanner         = $('#homeBanner'),         /** Home Banner */
        $homeBannerH2       = $homeBanner.find('h2'),
        $bannerText         = $('#bannerText'),
        $isotopeContainer   = $('.thumbs'),              /** Isotope */
        $brick              = $('.project.small'),
        $filter             = $('#filterNav').find('a');

    var settings            = {
            verbose: false
        },
        tapTargets, scrollPos, oneOff;


    var init = function( options ){

        $.extend( settings, options );

        oneOff = true;
        tapTargets = [];

        if( settings.verbose )
            console.log( 'Verbose mode on.' );

        /** Banner Parallax */
        $window.scroll(function () {
            scrollBanner();
        });

        if( $homeBannerH2.length > 0 )
            $homeBannerH2.fitText(1.7, { minFontSize: '24px', maxFontSize: '64px' });

        /** FitVids */
        if( $video.length > 0 )
            $video.fitVids();

        /** Isotope */
        $(window).load(function(){
		    /** Isotope */
		    if( $isotopeContainer.length > 0 )
				isotopeInit();
		});

        if( settings.verbose)
            console.log( 'All of init fired.' );

    };

    var isMobile = function(){

        if( settings.verbose)
            console.log( 'isMobile' );

        return (
        (navigator.userAgent.match(/Android/i)) ||
        (navigator.userAgent.match(/webOS/i)) ||
        (navigator.userAgent.match(/iPhone/i)) ||
        (navigator.userAgent.match(/iPod/i)) ||
        (navigator.userAgent.match(/iPad/i)) ||
        (navigator.userAgent.match(/BlackBerry/))
        );
    };

    /**
     * Initialize Isotope.
     *
     * Get the Isotope display mode via the parent class. The column width is set via CSS.
     */
    var isotopeInit = function() {

        if( settings.verbose )
            console.log( 'isotopeInit' );

        $isotopeContainer.waitForImages( function(){

            $isotopeContainer.isotope({
                itemSelector: '.project.small',
                resizesContainer: true
            });

            $brick.css("opacity", "1");

        });

        if( $filter.length > 0 )
            filterInit();

        mobile_tap();

    };

    /**
     * Adds the touch-hover class to the project on first tap for mobile devices and follows the link on tap two.
     */
    var mobile_tap = function() {

        if (!isMobile())
            return false;

        /** Add touchstart event to the bricks */
        $(".project.small a").on("touchstart", function (e) {

            /** Local variables will track who has and hasn't been tapped */
            var $el = $(this),
                elParID = $el.parents('.project').attr('id'),
                isInArray = $.inArray(elParID, tapTargets) !== -1;

            if (settings.verbose && isInArray)
                console.log('User tapped twice.');

            if (!isInArray) {

                if (settings.verbose)
                    console.log('User tapped once.');

                e.preventDefault();
                $(this).addClass('touch-hover');
                $(this).parents('.project').siblings().find('a').removeClass('touch-hover');

                /** Now add that ID to the list of already tapped members */
                tapTargets.push(elParID);

            } else
                $(this).removeClass('touch-hover');


        });
    };

    /**
     * Initializes the filter navigation.
     *
     * Note: No need to add this to the function queue, as it will be triggered by isotopeInit if #filter-nav is present.
     */
    var filterInit = function(){

        if( settings.verbose)
            console.log( 'filterInit' );

        $filter.click( function(){

            var selector = jQuery(this).attr( 'data-filter' );

            if( settings.verbose)
                console.log( "Selector is " + selector );

            $isotopeContainer.isotope({
                filter: selector
            });

            if( settings.verbose)
                console.log( "You should see only " + selector );

            if ( ! jQuery(this).hasClass( 'selected' ) ) {

                $(this).parents('#filterNav').find('a.selected').removeClass('selected');
                $(this).addClass( 'selected' );

            }

            return false;

        });

    };

    /**
     * Parallax Banner Function
     */
    var scrollBanner = function() {

        if( settings.verbose && oneOff ) {
            console.log('scrollBanner');
            oneOff = false;
        }

        /** Get the scroll position of the page */
        scrollPos = $(this).scrollTop();

        if( settings.verbose)
            console.log( 'Scroll top: ' + scrollPos );

        /** Scroll and fade out the banner text */
        $bannerText.css({
            'margin-top' : -( scrollPos / 3 ) + "px",
            'opacity' : 1 - ( scrollPos / 300 ),
            '-ms-filter' : 'progid:DXImageTransform.Microsoft.Alpha(Opacity=' + 1 - ( scrollPos / 300 ) + ')'
        });

        /** Scroll the background of the banner */
        $homeBanner.css({
            'background-position' : 'center ' + (- scrollPos / 8 ) + "px"
        });
    };

    /** Public API */
    return {
        init: init,
        mobileTap: mobile_tap
    }

})(jQuery);

jQuery(document).ready( function() {
    Hero.init();
});