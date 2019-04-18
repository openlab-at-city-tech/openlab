(function($) {

    "use strict";

    var johannes_app = {

        settings: {

            admin_bar: {
                height: 0,
                position: ''
            },

            pushes: {
                url: [],
                up: 0,
                down: 0
            },

            window_last_top: 0,
            infinite_allow: true
        },

        init: function() {
            this.admin_bar_check();
            this.sticky_header();
            this.sidebar();
            this.sliders();
            this.overlays();
            this.popup();
            this.instagram_slider();
            this.accordion_widget();
            this.pagination();
            this.check_history();
            this.align_full_fix();
            this.align_wide_fix();
            this.scroll_animate();
            this.go_to_top();
            this.reverse_menu();
            this.object_fit();

        },

        resize: function() {
            this.admin_bar_check();
            this.align_full_fix();
            this.align_wide_fix();
            this.sidebar();
        },

        scroll: function() {
            this.sticky_header();
        },

        admin_bar_check: function() {

            if ($('#wpadminbar').length && $('#wpadminbar').is(':visible')) {
                this.settings.admin_bar.height = $('#wpadminbar').height();
                this.settings.admin_bar.position = $('#wpadminbar').css('position');
            }

        },


        sticky_header: function() {

            if (!johannes_js_settings.header_sticky) {
                return false;
            }

            var sticky_top = this.settings.admin_bar.position == 'fixed' ? this.settings.admin_bar.height : 0;
            var top = $(window).scrollTop();
            var last_top = this.settings.window_last_top;

            $('.header-sticky').css('top', sticky_top);

            if (johannes_js_settings.header_sticky_up) {

                if (last_top > top && top >= johannes_js_settings.header_sticky_offset) {
                    if (!$("body").hasClass('johannes-header-sticky-active')) {
                        $("body").addClass("johannes-header-sticky-active");
                    }
                } else {
                    if ($("body").hasClass('johannes-header-sticky-active')) {
                        $("body").removeClass("johannes-header-sticky-active");
                    }
                }

            } else {

                if (top >= johannes_js_settings.header_sticky_offset) {
                    if (!$("body").hasClass('johannes-header-sticky-active')) {
                        $("body").addClass("johannes-header-sticky-active");
                    }

                } else {
                    if ($("body").hasClass('johannes-header-sticky-active')) {
                        $("body").removeClass("johannes-header-sticky-active");
                    }
                }
            }

            this.settings.window_last_top = top;

        },

        go_to_top: function() {

            if (!johannes_js_settings.go_to_top || !$('#johannes-goto-top').length) {
                return;
            }

            $('body').imagesLoaded(function() {

                var visible = $(window).scrollTop() >= 400 ? true : false;

                if(visible){
                    $('#johannes-goto-top').fadeTo(0, 0.8);
                } else {
                    $('#johannes-goto-top').fadeOut(100);
                }

                $(window).scroll(function() {

                    setTimeout(function() {

                    if (!visible && $(this).scrollTop() >= 400) {
                        visible = true;
                        $('#johannes-goto-top').fadeTo(0, 0.8);
                    } 

                    if (visible && $(this).scrollTop() < 400) {
                        visible = false;
                        $('#johannes-goto-top').fadeOut(100);
                    }
                    }, 700);

                });

                $('#johannes-goto-top').click(function() {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    return false;
                });

            });

        },

        accordion_widget: function() {

            $('.widget').find('.menu-item-has-children > a, .page_item_has_children > a, .cat-parent > a').after('<span class="johannes-accordion-nav"><i class="jf jf-chevron-down"></i></span>');

            $('.widget').on('click', '.johannes-accordion-nav', function() {
                $(this).closest('li').find('ul.sub-menu:first, ul.children:first').slideToggle('fast').parent().toggleClass('active');
            });

        },

        overlays: function() {

            //Sidebar
            $('body').on('click', '.johannes-hamburger a', function(e) {
                e.preventDefault();
                var top = johannes_app.settings.admin_bar.position == 'fixed' || $(window).scrollTop() == 0 ? johannes_app.settings.admin_bar.height : 0;
                $('.johannes-sidebar-hidden').css('top', top);
                $('body').addClass('overlay-lock overlay-sidebar-open');

            });

            $('body').on('click', '.johannes-action-close', function() {
                $('body').removeClass('overlay-lock overlay-sidebar-open');

            });

            //Popup header action
            $('body').on('click', '.johannes-action-close', function() {
                $('body').removeClass('overlay-lock overlay-sidebar-open');
            });

            $('body').on('click', '.johannes-action-overlay', function(e) {
                e.preventDefault();
                if (e.target === this) {
                    $('body').removeClass('overlay-lock overlay-sidebar-open');
                }
            });


            //Modal open
            $('body').on('click', '.johannes-modal-opener a', function(e) {
                e.preventDefault();

                $(this).closest('.johannes-modal-opener').next().addClass('modal-open');
                $('.johannes-header-sticky-active .header-sticky').addClass('header-sticky-modal');
                $('body').addClass('overlay-lock');

            });


            //Modal close
            $('body').on('click', '.johannes-modal-close', function(e) {
                e.preventDefault();
                $(this).closest('.johannes-modal').removeClass('modal-open');
                $('body').removeClass('overlay-lock');
                $('.header-sticky').removeClass('header-sticky-modal');
            });

            $(document).keyup(function(e) {
                if (e.keyCode == 27 && $('body').hasClass('overlay-lock')) {
                    $('body').removeClass('overlay-lock');
                    $('.johannes-modal').removeClass('modal-open');
                }
            });

            $(document).keyup(function(e) {
                if (e.keyCode == 27 && $('body').hasClass('overlay-sidebar-open')) {
                    $('body').removeClass('overlay-lock overlay-sidebar-open');
                }
            });


        },

        align_full_fix: function() {

            if (!$('body').hasClass('johannes-sidebar-none')) {
                return;
            }

            var style = '.alignfull { width: ' + $(window).width() + 'px; margin-left: -' + $(window).width() / 2 + 'px; margin-right: -' + $(window).width() / 2 + 'px; }';

            if ($('#johannes-align-fix').length) {
                $('#johannes-align-fix').html(style);
            } else {
                $('head').append('<style id="johannes-align-fix" type="text/css">' + style + '</style>');
            }

        },

        align_wide_fix: function() {
            if (!$('body').hasClass('johannes-sidebar-none')) {
                return;
            }

            var style = '.alignwide { max-width: ' + $('.johannes-section .container').width() + 'px;}';

            if ($('#johannes-alignwide-fix').length) {
                $('#johannes-alignwide-fix').html(style);
            } else {
                $('head').append('<style id="johannes-alignwide-fix" type="text/css">' + style + '</style>');
            }
        },

        instagram_slider: function() {

            var pre_footer_instagram = $('.johannes-section-instagram .meks-instagram-widget');

            if (!pre_footer_instagram.length) {
                return;
            }

            pre_footer_instagram.imagesLoaded(function() {

                pre_footer_instagram.addClass('owl-carousel').owlCarousel({
                    rtl: johannes_js_settings.rtl_mode ? true : false,
                    loop: true,
                    nav: true,
                    center: true,
                    navSpeed: 150,
                    autoWidth: true,
                    margin: 0,
                    stagePadding: 0,
                    navText: ['<i class="jf jf-arrow-left"></i>', '<i class="jf jf-arrow-right"></i>'],
                    lazyLoad: true
                });
            });

        },


        sliders: function() {

            if (!$('.johannes-slider').length) {
                return;
            }

            $('body').imagesLoaded(function() {

                $('.johannes-slider').each(function() {

                    var owl = $(this).addClass('owl-carousel');
                    var has_nav = $(this).hasClass('has-arrows') ? true : false;

                    var has_center = $(this).hasClass('slider-center') ? true : false;

                    var col_element_classes = owl.children().first().attr('class');
                    var items = col_element_classes.match(/col-(\d+)/);
                    items = items === null ? 1 : 12 / items[1];
                    var items_sm = col_element_classes.match(/col-sm-(\d+)/);
                    items_sm = items_sm === null ? items : 12 / items_sm[1];
                    var items_md = col_element_classes.match(/col-md-(\d+)/);
                    items_md = items_md === null ? items_sm : 12 / items_md[1];
                    var items_lg = col_element_classes.match(/col-lg-(\d+)/);
                    items_lg = items_lg === null ? items_md : 12 / items_lg[1];

                    var responsive_opts = {};

                    if (!$(this).closest('.widget').length) {
                        responsive_opts[johannes_js_settings.grid.breakpoint.sm] = { items: items_sm, margin: johannes_js_settings.grid.gutter.sm };
                        responsive_opts[johannes_js_settings.grid.breakpoint.md] = { items: items_md, margin: johannes_js_settings.grid.gutter.md };
                        responsive_opts[johannes_js_settings.grid.breakpoint.lg] = { items: items_lg, margin: johannes_js_settings.grid.gutter.lg };
                        responsive_opts[johannes_js_settings.grid.breakpoint.xl] = { items: items_lg, margin: johannes_js_settings.grid.gutter.xl };
                    }


                    owl.owlCarousel({
                        rtl: johannes_js_settings.rtl_mode ? true : false,
                        loop: true,
                        items: items,
                        margin: johannes_js_settings.grid.gutter.sm,
                        stagePadding: 0,
                        nav: has_nav,
                        center: has_center,
                        navSpeed: 150,
                        navText: ['<i class="jf jf-arrow-left"></i>', '<i class="jf jf-arrow-right"></i>'],
                        onInitialized: function(event) {
                            var target = $(event.currentTarget);
                            target.removeClass('row');
                            target.find('.owl-item').each(function() {
                                $(this).children().removeClass();
                            });
                        },
                        responsive: responsive_opts
                    });

                });
            });

        },

        sidebar: function() {

            if ($('.johannes-sticky').length || $('.widget-sticky').length) {

                if (window.matchMedia('(min-width: ' + johannes_js_settings.grid.breakpoint.lg + 'px)').matches && $('.widget-sticky').length && !$('.johannes-sticky').length) {

                    $('.johannes-sidebar').each(function() {
                        if ($(this).find('.widget-sticky').length) {
                            $(this).find('.widget-sticky').wrapAll('<div class="johannes-sticky"></div>');
                        }
                    });

                }

                $('body').imagesLoaded(function() {

                    var sticky_sidebar = $('.johannes-sticky');
                    var top_padding = window.matchMedia('(min-width: ' + johannes_js_settings.grid.breakpoint.xl + 'px)').matches ? johannes_js_settings.grid.gutter.xl : johannes_js_settings.grid.gutter.lg;

                    sticky_sidebar.each(function() {

                        var content = $(this).closest('.section-content').find('.johannes-order-1');
                        var parent = $(this).parent();

                        var sticky_offset = $('.johannes-header.header-sticky').length && !johannes_js_settings.header_sticky_up ? $('.johannes-header.header-sticky').outerHeight() : 0;

                        var admin_bar_offset = johannes_app.settings.admin_bar.position == 'fixed' ? johannes_app.settings.admin_bar.height : 0;
                        var offset_top = sticky_offset + admin_bar_offset + top_padding;

                        var widgets = $(this).children().addClass('widget-sticky');

                        if (window.matchMedia('(min-width: ' + johannes_js_settings.grid.breakpoint.lg + 'px)').matches) {

                            parent.height(content.height());

                            $(this).stick_in_parent({
                                offset_top: offset_top
                            });

                            johannes_app.masonry_widgets();

                        } else {
                            parent.css('height', 'auto');
                            parent.css('min-height', '1px');
                            widgets.unwrap();
                            johannes_app.masonry_widgets();
                        }

                    });
                });

            } else {
                johannes_app.masonry_widgets();
            }

        },

        masonry_widgets: function() {

            if (!$('.johannes-sidebar').not('.johannes-sidebar-hidden').length) {
                return false;
            }

            $('body').imagesLoaded(function() {

                var sidebar = $('.johannes-sidebar').not('.johannes-sidebar-hidden');

                sidebar.each(function() {
                    if (window.matchMedia('(min-width: ' + johannes_js_settings.grid.breakpoint.lg + 'px)').matches) {
                        if ($(this).hasClass('has-masonry')) {
                            $(this).removeClass('has-masonry').masonry('destroy');
                        }
                    } else {
                        $(this).addClass('has-masonry').masonry({
                            columnWidth: '.col-md-6',
                            percentPosition: true
                        });
                    }
                });
            });

        },

        popup: function() {

            if (!johannes_js_settings.popup) {
                return false;
            }

            $('body').on('click', '.wp-block-gallery .blocks-gallery-item a, .wp-block-image a', function(e) {

                if (!/\.(?:jpg|jpeg|gif|png|webp)$/i.test($(this).attr('href'))) {
                    return;
                }

                e.preventDefault();

                var pswpElement = document.querySelectorAll('.pswp')[0];
                var items = [];
                var index = 0;
                var opener = $(this);
                var is_gallery = opener.closest('.wp-block-gallery').length;
                var links = is_gallery ? $(this).closest('.wp-block-gallery').find('.blocks-gallery-item a') : $('.wp-block-image a');

                $.each(links, function(ind) {

                    if (opener.attr('href') == $(this).attr('href')) {
                        index = ind;
                    }

                    var link = $(this);
                    var item = {};
                    var image = new Image();
                    image.onload = function() {

                        item = {
                            src: link.attr('href'),
                            w: image.naturalWidth,
                            h: image.naturalHeight,
                            title: is_gallery ? link.closest('.blocks-gallery-item').find('figcaption').html() : link.closest('.wp-block-image').find('figcaption').html()
                        };

                        items.push(item);

                        if (ind == (links.length - 1)) {

                            var options = {
                                history: false,
                                index: index,
                                preload: [2, 2],
                                captionEl: true,
                                fullscreenEl: false,
                                zoomEl: false,
                                shareEl: false,
                                preloaderEl: true,
                                closeOnScroll: false
                            };

                            var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
                            gallery.init();
                        }

                    };

                    if (/\.(?:jpg|jpeg|gif|png|webp)$/i.test(link.attr('href'))) {
                        image.src = link.attr('href');
                    }


                });


            });



        },

        reverse_menu: function() {

            $('.johannes-header').on('mouseenter', 'ul li', function(e) {

                if ($(this).find('ul').length) {

                    var rt = $(window).width() - ($(this).find('ul').offset().left + $(this).find('ul').outerWidth());

                    if (rt < 0) {
                        $(this).find('ul').addClass('johannes-rev');
                    }

                }
            });

        },

        scroll_animate: function() {

            $('body').on('click', '.johannes-scroll-animate', function(e) {

                e.preventDefault();
                var target = this.hash;
                var $target = $(target);
                var offset = johannes_js_settings.header_sticky ? $('.johannes-header.header-sticky').height() : 0;

                $('html, body').stop().animate({
                    'scrollTop': $target.offset().top - offset
                }, 900, 'swing', function() {
                    window.location.hash = target;
                });

            });
        },



        pagination: function() {

            $('body').on('click', '.johannes-pagination.load-more > a', function(e) {
                e.preventDefault();

                johannes_app.load_more_items({
                    opener: $(this),
                    url: $(this).attr('href'),
                    next_url_selector: '.johannes-pagination.load-more > a'
                }, function() {});

            });

            if (!$('.johannes-pagination.johannes-infinite-scroll').length) {
                return false;
            }

            $(window).scroll(function() {

                if (johannes_app.settings.infinite_allow && $('.johannes-pagination').length) {


                    var pagination = $('.johannes-pagination');
                    var opener = pagination.find('a');

                    if ($(this).scrollTop() > (pagination.offset().top - $(this).height() - 200)) {

                        johannes_app.settings.infinite_allow = false;

                        johannes_app.load_more_items({
                            opener: opener,
                            url: opener.attr('href'),
                            next_url_selector: '.johannes-pagination.johannes-infinite-scroll a'
                        }, function() {
                            johannes_app.settings.infinite_allow = true;
                        });

                    }
                }
            });

        },

        load_more_items: function(args, callback) {

            $('.johannes-pagination').toggleClass('johannes-loader-active');

            var defaults = {
                opener: '',
                url: '',
                next_url_selector: '.load-more > a'
            };

            var options = $.extend({}, defaults, args);

            $("<div>").load(options.url, function() {

                var next_url = $(this).find(options.next_url_selector).attr('href');
                var next_title = $(this).find('title').text();
                var new_items = $(this).find('.johannes-items').children();
                var container = options.opener.closest('.section-content').find('.johannes-items');

                new_items.imagesLoaded(function() {

                    new_items.hide().appendTo(container).fadeIn();

                    if (next_url !== undefined) {
                        $(options.next_url_selector).attr('href', next_url);

                    } else {
                        $(options.next_url_selector).closest('.johannes-pagination').parent().fadeOut('fast').remove();
                    }

                    var push_obj = {
                        prev: window.location.href,
                        next: options.url,
                        offset: $(window).scrollTop(),
                        prev_title: window.document.title,
                        next_title: next_title
                    };

                    johannes_app.push_state(push_obj);

                    $('.johannes-pagination').toggleClass('johannes-loader-active');
                    johannes_app.sidebar();

                    callback();

                });
            });
        },

        push_state: function(args) {
            var defaults = {
                    prev: window.location.href,
                    next: '',
                    offset: $(window).scrollTop(),
                    prev_title: window.document.title,
                    next_title: window.document.title,
                    increase_counter: true
                },
                push_object = $.extend({}, defaults, args);

            if (push_object.increase_counter) {
                johannes_app.settings.pushes.up++;
                johannes_app.settings.pushes.down++;
            }
            delete(push_object.increase_counter);

            johannes_app.settings.pushes.url.push(push_object);
            window.document.title = push_object.next_title;
            window.history.pushState(push_object, '', push_object.next);
        },

        check_history: function() {

            if (!$('.johannes-pagination.load-more').length && !$('.johannes-pagination.johannes-infinite-scroll').length) {
                return false;
            }

            johannes_app.push_state({
                increase_counter: false
            });

            var last_up, last_down = 0;

            $(window).scroll(function() {

                if (johannes_app.settings.pushes.url[johannes_app.settings.pushes.up].offset !== last_up && $(window).scrollTop() < johannes_app.settings.pushes.url[johannes_app.settings.pushes.up].offset) {
                    last_up = johannes_app.settings.pushes.url[johannes_app.settings.pushes.up].offset;
                    last_down = 0;
                    window.document.title = johannes_app.settings.pushes.url[johannes_app.settings.pushes.up].prev_title;
                    window.history.replaceState(johannes_app.settings.pushes.url, '', johannes_app.settings.pushes.url[johannes_app.settings.pushes.up].prev); //1

                    johannes_app.settings.pushes.down = johannes_app.settings.pushes.up;
                    if (johannes_app.settings.pushes.up !== 0) {
                        johannes_app.settings.pushes.up--;
                    }
                }

                if (johannes_app.settings.pushes.url[johannes_app.settings.pushes.down].offset !== last_down && $(window).scrollTop() > johannes_app.settings.pushes.url[johannes_app.settings.pushes.down].offset) {
                    last_down = johannes_app.settings.pushes.url[johannes_app.settings.pushes.down].offset;
                    last_up = 0;

                    window.document.title = johannes_app.settings.pushes.url[johannes_app.settings.pushes.down].next_title;
                    window.history.replaceState(johannes_app.settings.pushes.url, '', johannes_app.settings.pushes.url[johannes_app.settings.pushes.down].next);

                    johannes_app.settings.pushes.up = johannes_app.settings.pushes.down;
                    if (johannes_app.settings.pushes.down < johannes_app.settings.pushes.url.length - 1) {
                        johannes_app.settings.pushes.down++;
                    }

                }
            });

        },

        object_fit: function() {
            $('body').imagesLoaded(function() {
                objectFitImages('.section-bg img, .entry-media img');
            });
        }

    };

    $(document).ready(function() {
        johannes_app.init();

    });

    $(window).resize(function() {
        johannes_app.resize();
    });

    $(window).scroll(function() {
        johannes_app.scroll();
    });

})(jQuery);