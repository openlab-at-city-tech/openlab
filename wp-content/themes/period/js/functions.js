jQuery(document).ready(function($){

    var body = $('body');
    var siteHeader = $('#site-header');
    var titleContainer = $('#title-container');
    var toggleNavigation = $('#toggle-navigation');
    var menuPrimaryContainer = $('#menu-primary-container');
    var menuPrimary = $('#menu-primary');
    var menuPrimaryItems = $('#menu-primary-items');
    var toggleDropdown = $('.toggle-dropdown');
    var sidebarPrimary = $('#sidebar-primary');
    var socialMediaIcons = siteHeader.find('.social-media-icons');
    var menuLink = $('.menu-item').children('a');

    objectFitAdjustment();

    toggleNavigation.on('click', openPrimaryMenu);
    toggleDropdown.on('click', openDropdownMenu);
    body.on('click', '#search-icon', openSearchBar);

    $('.post-content').fitVids({
        customSelector: 'iframe[src*="dailymotion.com"], iframe[src*="slideshare.net"], iframe[src*="animoto.com"], iframe[src*="blip.tv"], iframe[src*="funnyordie.com"], iframe[src*="hulu.com"], iframe[src*="ted.com"], iframe[src*="wordpress.tv"]'
    });

    if ( window.innerWidth < 900 ) {
        skipMenuLinks(true);
    }

    $(window).on('resize', function(){
        objectFitAdjustment();
        if ( window.innerWidth < 900 ) {
            skipMenuLinks(true);
        } else {
            skipMenuLinks(false);
        }
    });

    // Jetpack infinite scroll event that reloads posts.
    $( document.body ).on( 'post-load', function () {
        objectFitAdjustment();
    } );

    function openPrimaryMenu() {

        if( menuPrimaryContainer.hasClass('open') ) {
            menuPrimaryContainer.removeClass('open');
            $(this).removeClass('open');

            menuPrimaryContainer.css('max-height', '');

            // change screen reader text
            $(this).children('span').text(ct_period_objectL10n.openMenu);

            // change aria text
            $(this).attr('aria-expanded', 'false');
            skipMenuLinks(true)

        } else {
            menuPrimaryContainer.addClass('open');
            $(this).addClass('open');


            var newHeight = menuPrimary.outerHeight(true);
            if ( socialMediaIcons.length ) {
                newHeight += socialMediaIcons.outerHeight(true);
            }
            menuPrimaryContainer.css('max-height', newHeight + 'px');

            // change screen reader text
            $(this).children('span').text(ct_period_objectL10n.closeMenu);

            // change aria text
            $(this).attr('aria-expanded', 'true');
            skipMenuLinks(false);
            $('#menu-primary-container').find('.sub-menu').find('a').attr('tabindex', -1);
            $('#menu-primary-container').find('.sub-menu').find('button').attr('tabindex', -1);
        }
    }

    // display the dropdown menus
    function openDropdownMenu() {

        if( window.innerWidth < 900 ) {

            // get the buttons parent (li)
            var menuItem = $(this).parent();

            // if already opened
            if (menuItem.hasClass('open')) {

                // remove open class
                menuItem.removeClass('open');

                $(this).siblings('ul').css('max-height', 0);

                // change screen reader text
                $(this).children('.screen-reader-text').text(ct_period_objectL10n.openChildMenu);

                // change aria text
                $(this).attr('aria-expanded', 'false');

                menuItem.children('.sub-menu').find('a').attr('tabindex', -1);
                menuItem.children('.sub-menu').find('button').attr('tabindex', -1);
            } else {

                // add class to open the menu
                menuItem.addClass('open');

                var ulHeight = 0;

                $(this).siblings('ul').find('li').each(function () {
                    ulHeight = ulHeight + $(this).height() + ( $(this).height() * 1.5 );
                });

                $(this).siblings('ul').css('max-height', ulHeight);

                // expand entire menu for dropdowns
                // doesn't need to be precise. Just needs to allow the menu to get taller
                menuPrimaryContainer.css('max-height', 9999);

                // change screen reader text
                $(this).children('.screen-reader-text').text(ct_period_objectL10n.closeChildMenu);

                // change aria text
                $(this).attr('aria-expanded', 'true');
                menuItem.children('.sub-menu').children('li').children('a').attr('tabindex', 0);
                menuItem.children('.sub-menu').children('li').children('button').attr('tabindex', 0);
            }
        }
    }

    function openSearchBar(){

        if( $(this).hasClass('open') ) {

            $(this).removeClass('open');

            socialMediaIcons.removeClass('fade');

            // make search input inaccessible to keyboards
            siteHeader.find('.search-field').attr('tabindex', -1);

            // handle mobile width search bar sizing
            if( window.innerWidth < 900 ) {
                siteHeader.find('.search-form').attr('style', '');
            }
        } else {
            $(this).addClass('open');

            socialMediaIcons.addClass('fade');

            // make search input keyboard accessible
            siteHeader.find('.search-field').attr('tabindex', 0);
            siteHeader.find('.search-field').focus();

            // handle mobile width search bar sizing
            if( window.innerWidth < 900 ) {

                // distance to other side (35px is width of icon space)
                var leftDistance = window.innerWidth * 0.83332 - 35;

                siteHeader.find('.search-form').css('left', -leftDistance + 'px')
            }
        }
    }

    // mimic cover positioning without using cover
    function objectFitAdjustment() {

        // if the object-fit property is not supported
        if( !('object-fit' in document.body.style) ) {

            $('.featured-image').each(function () {

                if ( !$(this).parent().parent('.entry').hasClass('ratio-natural') ) {

                    var image = $(this).children('img').add($(this).children('a').children('img'));

                    // don't process images twice (relevant when using infinite scroll)
                    if ( image.hasClass('no-object-fit') ) {
                        return;
                    }

                    image.addClass('no-object-fit');

                    // if the image is not wide enough to fill the space
                    if (image.outerWidth() < $(this).outerWidth()) {

                        image.css({
                            'width': '100%',
                            'min-width': '100%',
                            'max-width': '100%',
                            'height': 'auto',
                            'min-height': '100%',
                            'max-height': 'none'
                        });
                    }
                    // if the image is not tall enough to fill the space
                    if (image.outerHeight() < $(this).outerHeight()) {

                        image.css({
                            'height': '100%',
                            'min-height': '100%',
                            'max-height': '100%',
                            'width': 'auto',
                            'min-width': '100%',
                            'max-width': 'none'
                        });
                    }
                }
            });
        }
    }

    function skipMenuLinks(skip) {
        if ( skip ) {
            $('#menu-primary-container').find('a').attr('tabindex', -1);
            $('#menu-primary-container').find('button').attr('tabindex', -1);
        } else {
            $('#menu-primary-container').find('a').attr('tabindex', 0);
            $('#menu-primary-container').find('button').attr('tabindex', 0);
        }
    }

    /* allow keyboard access/visibility for dropdown menu items */
    menuLink.on('focus', function(){
        $(this).parents('ul').addClass('focused');
    });
    menuLink.on('focusout', function(){
        $(this).parents('ul').removeClass('focused');
    });

    // ===== Scroll to Top ==== //

    if ( $('#scroll-to-top').length !== 0 ) {
        $(window).on( 'scroll', function() {
            if ($(this).scrollTop() >= 200) {        // If page is scrolled more than 50px
                $('#scroll-to-top').addClass('visible');    // Fade in the arrow
            } else {
                $('#scroll-to-top').removeClass('visible');   // Else fade out the arrow
            }
        });
        $('#scroll-to-top').click(function(e) {      // When arrow is clicked
            $('body,html').animate({
                scrollTop : 0                       // Scroll to top of body
            }, 600);
            $(this).blur();
        });
    }
});

/* fix for skip-to-content link bug in Chrome & IE9 */
window.addEventListener("hashchange", function(event) {

    var element = document.getElementById(location.hash.substring(1));

    if (element) {

        if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {
            element.tabIndex = -1;
        }

        element.focus();
    }

}, false);

// wait to see if a touch event is fired
var hasTouch;
window.addEventListener('touchstart', setHasTouch, false );

// require a double-click on parent dropdown items
function setHasTouch() {

    hasTouch = true;

    // Remove event listener once fired
    window.removeEventListener('touchstart', setHasTouch);

    // get the width of the window
    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth;


    // don't require double clicks for the toggle menu
    if (x > 899) {
        enableTouchDropdown();
    }
}

// require a second click to visit parent navigation items
function enableTouchDropdown(){

    // get all the parent menu items
    var menuParents = document.getElementsByClassName('menu-item-has-children');

    // add a 'closed' class to each and add an event listener to them
    for (i = 0; i < menuParents.length; i++) {
        menuParents[i].className = menuParents[i].className + " closed";
        menuParents[i].addEventListener('click', openDropdown);
    }
}

// check if an element has a class
function hasClass(element, cls) {
    return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
}

// open the dropdown without visiting parent link
function openDropdown(e){

    // if has 'closed' class...
    if(hasClass(this, 'closed')){
        // prevent link from being visited
        e.preventDefault();
        // remove 'closed' class to enable link
        this.className = this.className.replace('closed', '');
    }
}