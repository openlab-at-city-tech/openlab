/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'color': to,
					'position': 'relative'
				} );
			}
		} );
	} );

	//Site title
	wp.customize('site_title_color',function( value ) {
		value.bind( function( newval ) {
			$('.site-title a').css('color', newval );
		} );
	});
	//Site desc
	wp.customize('site_desc_color',function( value ) {
		value.bind( function( newval ) {
			$('.site-description').css('color', newval );
		} );
	});

	//Top level menu items
	wp.customize('top_items_color',function( value ) {
		value.bind( function( newval ) {
			$('#mainnav ul li a').not('#mainnav .sub-menu li a').css('color', newval );
		} );
	});	
	//Sub-menu items
	wp.customize('submenu_items_color',function( value ) {
		value.bind( function( newval ) {
			$('#mainnav .sub-menu li a ').css('color', newval );
		} );
	});
	//Slider text
	wp.customize('slider_text',function( value ) {
		value.bind( function( newval ) {
			$('.text-slider .maintitle, .text-slider .subtitle').css('color', newval );
		} );
	});	
	// Body text color
	wp.customize('body_text_color',function( value ) {
		value.bind( function( newval ) {
			$('body').css('color', newval );
		} );
	});		
	//Sidebar background
	wp.customize('sidebar_background',function( value ) {
		value.bind( function( newval ) {
			$('.widget-area').css('background-color', newval );
		} );
	});	
	//Sidebar color
	wp.customize('sidebar_color',function( value ) {
		value.bind( function( newval ) {
			$('.widget-area,.widget-area a, .widget-area .widget-title').css('color', newval );
		} );
	});
	//Footer widgets background
	wp.customize('footer_widgets_background',function( value ) {
		value.bind( function( newval ) {
			$('.footer-widgets').css('background-color', newval );
		} );
	});	
	//Footer widgets color
	wp.customize('footer_widgets_color',function( value ) {
		value.bind( function( newval ) {
			$('.sidebar-column .widget').css('color', newval );
		} );
	});	
	wp.customize('footer_widgets_links_color',function( value ) {
		value.bind( function( newval ) {
			$('.sidebar-column .widget a').css('color', newval );
		} );
	});		
	//Footer background
	wp.customize('footer_background',function( value ) {
		value.bind( function( newval ) {
			$('.site-footer').css('background-color', newval );
		} );
	});
	//Footer color
	wp.customize('footer_color',function( value ) {
		value.bind( function( newval ) {
			$('.sydney-credits,.sydney-credits a').css('color', newval );
		} );
	});

	//Slider
	wp.customize( 'slider_image_1', function( value ) {
		value.bind( function( to ) {
			$( '.slide-item:eq(0)' ).css( 'background-image', 'url(' + to + ')' );
		} );
	} );	
	wp.customize( 'slider_image_2', function( value ) {
		value.bind( function( to ) {
			$( '.slide-item:eq(1)' ).css( 'background-image', 'url(' + to + ')' );
		} );
	} );	
	wp.customize( 'slider_image_3', function( value ) {
		value.bind( function( to ) {
			$( '.slide-item:eq(2)' ).css( 'background-image', 'url(' + to + ')' );
		} );
	} );	
	wp.customize( 'slider_image_4', function( value ) {
		value.bind( function( to ) {
			$( '.slide-item:eq(3)' ).css( 'background-image', 'url(' + to + ')' );
		} );
	} );	
	wp.customize( 'slider_image_5', function( value ) {
		value.bind( function( to ) {
			$( '.slide-item:eq(4)' ).css( 'background-image', 'url(' + to + ')' );
		} );
	} );
	wp.customize( 'slider_button_url', function( value ) {
		value.bind( function( to ) {
			$( '.button-slider' ).attr( 'href', to );
		} );
	} );	


	//Primary color
	wp.customize( 'primary_color', function( value ) {
		value.bind( function( to ) {

			var styleContents = $( '#sydney-style-min-inline-css' ).text();

			/* Break function in two */
			var newStyle = styleContents + '.go-top:hover svg,.sydney_contact_info_widget span { fill:' + to + '}.widget-area .widget_fp_social a,#mainnav ul li a:hover, .sydney_contact_info_widget span, .roll-team .team-content .name,.roll-team .team-item .team-pop .team-social li:hover a,.roll-infomation li.address:before,.roll-infomation li.phone:before,.roll-infomation li.email:before,.roll-testimonials .name,.roll-button.border,.roll-button:hover,.roll-icon-list .icon i,.roll-icon-list .content h3 a:hover,.roll-icon-box.white .content h3 a,.roll-icon-box .icon i,.roll-icon-box .content h3 a:hover,.switcher-container .switcher-icon a:focus,.go-top:hover,.hentry .meta-post a:hover,#mainnav > ul > li > a.active, #mainnav > ul > li > a:hover, button:hover, input[type=\"button\"]:hover, input[type=\"reset\"]:hover, input[type=\"submit\"]:hover, .text-color, .social-menu-widget a, .social-menu-widget a:hover, .archive .team-social li a, a, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a,.classic-alt .meta-post a,.single .hentry .meta-post a { color:' + to + '}.reply,.woocommerce div.product .woocommerce-tabs ul.tabs li.active,.woocommerce #respond input#submit,.woocommerce a.button,.woocommerce button.button,.woocommerce input.button,.project-filter li a.active, .project-filter li a:hover,.preloader .pre-bounce1, .preloader .pre-bounce2,.roll-team .team-item .team-pop,.roll-progress .progress-animate,.roll-socials li a:hover,.roll-project .project-item .project-pop,.roll-project .project-filter li.active,.roll-project .project-filter li:hover,.roll-button.light:hover,.roll-button.border:hover,.roll-button,.roll-icon-box.white .icon,.owl-theme .owl-controls .owl-page.active span,.owl-theme .owl-controls.clickable .owl-page:hover span,.go-top,.bottom .socials li:hover a,.sidebar .widget:before,.blog-pagination ul li.active,.blog-pagination ul li:hover a,.content-area .hentry:after,.text-slider .maintitle:after,.error-wrap #search-submit:hover,#mainnav .sub-menu li:hover > a,#mainnav ul li ul:after, button, input[type=\"button\"], input[type=\"reset\"], input[type=\"submit\"], .panel-grid-cell .widget-title:after, .cart-amount { background-color:' + to + '}.roll-socials li a:hover,.roll-socials li a,.roll-button.light:hover,.roll-button.border,.roll-button,.roll-icon-list .icon,.roll-icon-box .icon,.owl-theme .owl-controls .owl-page span,.comment .comment-detail,.widget-tags .tag-list a:hover,.blog-pagination ul li,.hentry blockquote,.error-wrap #search-submit:hover,textarea:focus,input[type=\"text\"]:focus,input[type=\"password\"]:focus,input[type=\"datetime\"]:focus,input[type=\"datetime-local\"]:focus,input[type=\"date\"]:focus,input[type=\"month\"]:focus,input[type=\"time\"]:focus,input[type=\"week\"]:focus,input[type=\"number\"]:focus,input[type=\"email\"]:focus,input[type=\"url\"]:focus,input[type=\"search\"]:focus,input[type=\"tel\"]:focus,input[type=\"color\"]:focus, button, input[type=\"button\"], input[type=\"reset\"], input[type=\"submit\"], .archive .team-social li a { border-color:' + to + '}';
			$( '#sydney-style-min-inline-css' ).text(newStyle);

		} );
	} );	   


	//Body font family
	wp.customize( 'body_font', function( value ) {
		value.bind( function( to ) {

			$( '#sydney-preview-google-fonts-body-css' ).attr( 'href', 'https://fonts.googleapis.com/css?family=' + to.replace(/ /g, '+') + '&display=swap' );

			$( 'body, #mainnav ul ul a' ).css( 'font-family', to );
		} );
	} );
	
	
	//Headings font family
	wp.customize( 'headings_font', function( value ) {
		value.bind( function( to ) {

			$( '#sydney-preview-google-fonts-headings-css' ).attr( 'href', 'https://fonts.googleapis.com/css?family=' + to.replace(/ /g, '+') + '&display=swap' );

			$( 'h1, h2, h3, h4, h5, h6, #mainnav ul li a, .portfolio-info, .roll-testimonials .name, .roll-team .team-content .name, .roll-team .team-item .team-pop .name, .roll-tabs .menu-tab li a, .roll-testimonials .name, .roll-project .project-filter li a, .roll-button, .roll-counter .name-count, .roll-counter .numb-count button, input[type=\"button\"], input[type=\"reset\"], input[type=\"submit\"]' ).css( 'font-family', to );
		} );
	} );	


	//Start port
	//Back to top
	wp.customize( 'scrolltop_radius', function( value ) {
		value.bind( function( to ) {
			$( '.go-top.show' ).css( 'border-radius', to + 'px' );
		} );
	} );
	wp.customize( 'scrolltop_side_offset', function( value ) {
		value.bind( function( to ) {
			$( '.go-top.position-right' ).css( 'right', to + 'px' );
			$( '.go-top.position-left' ).css( 'left', to + 'px' );
		} );
	} );
	wp.customize( 'scrolltop_bottom_offset', function( value ) {
		value.bind( function( to ) {
			$( '.go-top' ).css( 'bottom', to + 'px' );
		} );
	} );
	wp.customize( 'scrolltop_icon_size', function( value ) {
		value.bind( function( to ) {
			$( '.go-top .sydney-svg-icon, .go-top .sydney-svg-icon svg' ).css( 'width', to + 'px' );
			$( '.go-top .sydney-svg-icon, .go-top .sydney-svg-icon svg' ).css( 'height', to + 'px' );
		} );
	} );
	wp.customize( 'scrolltop_padding', function( value ) {
		value.bind( function( to ) {
			$( '.go-top' ).css( 'padding', to + 'px' );
		} );
	} );


	//Background colors
	var $bg_color_options = { "color_forms_background":"input[type=\"text\"],input[type=\"email\"],input[type=\"url\"],input[type=\"password\"],input[type=\"search\"],input[type=\"number\"],input[type=\"tel\"],input[type=\"range\"],input[type=\"date\"],input[type=\"month\"],input[type=\"week\"],input[type=\"time\"],input[type=\"datetime\"],input[type=\"datetime-local\"],input[type=\"color\"],textarea,select,.woocommerce .select2-container .select2-selection--single,.woocommerce-page .select2-container .select2-selection--single,.woocommerce-cart .woocommerce-cart-form .actions .coupon input[type=\"text\"]","shop_product_card_background":".woocommerce-page ul.products li.product","offcanvas_submenu_color": ".sydney-offcanvas-menu #mainnav ul ul a","offcanvas_menu_background":".sydney-offcanvas-menu","mobile_header_background":"#masthead-mobile","main_header_submenu_background":".mainnav ul ul li","main_header_bottom_background":".bottom-header-row","main_header_background":".main-header,.header-search-form","main_header_background_sticky":".main-header.sticky-active","topbar_background":".top-bar","button_background_color":"button,.roll-button,a.button,.wp-block-button:not(.is-style-outline) a,input[type=\"button\"],input[type=\"reset\"],input[type=\"submit\"]","scrolltop_bg_color":".go-top", };

	$.each( $bg_color_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {
				$( selector ).css( 'background-color', to );
			} );
		} );
	});

	//Colors
	var $color_options = { "color_forms_text":"input[type=\"text\"],input[type=\"email\"],input[type=\"url\"],input[type=\"password\"],input[type=\"search\"],input[type=\"number\"],input[type=\"tel\"],input[type=\"range\"],input[type=\"date\"],input[type=\"month\"],input[type=\"week\"],input[type=\"time\"],input[type=\"datetime\"],input[type=\"datetime-local\"],input[type=\"color\"],textarea,select,.woocommerce .select2-container .select2-selection--single,input[type=\"text\"]:focus,input[type=\"email\"]:focus,input[type=\"url\"]:focus,input[type=\"password\"]:focus,input[type=\"search\"]:focus,input[type=\"number\"]:focus,input[type=\"tel\"]:focus,input[type=\"range\"]:focus,input[type=\"date\"]:focus,input[type=\"month\"]:focus,input[type=\"week\"]:focus,input[type=\"time\"]:focus,input[type=\"datetime\"]:focus,input[type=\"datetime-local\"]:focus,input[type=\"color\"]:focus,textarea:focus,select:focus,.woocommerce .select2-container .select2-selection--single:focus,.woocommerce-page .select2-container .select2-selection--single,.select2-container--default .select2-selection--single .select2-selection__rendered","color_link_default":".entry-content a:not(.button):not(.elementor-button-link)","color_heading_1":"h1","color_heading_2":"h2,.wp-block-search .wp-block-search__label","color_heading_3":"h3","color_heading_4":"h4,.product-gallery-summary .product_meta,.product-gallery-summary .product_meta a,.woocommerce-breadcrumb,.woocommerce-breadcrumb a,.woocommerce-tabs ul.tabs li a,.product-gallery-summary .woocommerce-Price-amount,.woocommerce-mini-cart-item .quantity,.woocommerce-mini-cart__total .woocommerce-Price-amount,.order-total .woocommerce-Price-amount","color_heading_5":"h5:not(.sticky-addtocart-title)","color_heading_6":"h6","offcanvas_menu_color": ".sydney-offcanvas-menu, .sydney-offcanvas-menu a:not(.button)","mobile_header_color":"#masthead-mobile,#masthead-mobile .site-description,#masthead-mobile a:not(.button)","main_header_submenu_color":".mainnav ul ul a","main_header_bottom_color":".bottom-header-row, .bottom-header-row .header-contact a,.bottom-header-row .mainnav .menu > li > a","main_header_color":".main-header .site-title a,.main-header .site-description,.main-header .mainnav .menu > li > a, .main-header .header-contact a", "topbar_color":".top-bar, .top-bar a","single_post_meta_color":".single .entry-header .entry-meta,.single .entry-header .entry-meta a","single_post_title_color":".single .entry-header .entry-title","loop_post_text_color":".posts-layout .entry-post","loop_post_title_color":".posts-layout .entry-title a","loop_post_meta_color":".posts-layout .entry-meta,.posts-layout .entry-meta a","button_color":"button,.roll-button,a.button,.wp-block-button__link,input[type=\"button\"],input[type=\"reset\"],input[type=\"submit\"]","scrolltop_color":".go-top", "footer_widgets_headings_color":".sidebar-column .widget h1, .sidebar-column .widget h2, .sidebar-column .widget h3, .sidebar-column .widget h4, .sidebar-column .widget h5, .sidebar-column .widget h6", "footer_widgets_title_color":".sidebar-column .widget .widget-title" };

	$.each( $color_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {
				$( selector ).css( 'color', to );
			} );
		} );
	});	

	//Stroke
	var $stroke_options = { "scrolltop_color":".go-top svg", };

	$.each( $stroke_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {
				$( selector ).css( 'stroke', to );
			} );
		} );
	});		

	//Background hover
	var $bg_hover_options = { "button_background_color_hover":"button:hover,.roll-button:hover,a.button:hover,.wp-block-button__link:hover,input[type=\"button\"]:hover,input[type=\"reset\"]:hover,input[type=\"submit\"]:hover","scrolltop_bg_color_hover":".go-top:hover" , };

	$.each( $bg_hover_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {

				$( 'head' ).find( '#sydney-customizer-styles-' + option ).remove();

				var output = selector + ' { background-color:' + to + '!important; }';

				$( 'head' ).append( '<style id="sydney-customizer-styles-' + option + '">' + output + '</style>' );

			} );
		} );
	});		

	//Border hover
	var $border_hover_options = { "button_border_color_hover":"button:hover,.roll-button:hover,a.button:hover,.wp-block-button__link:hover,input[type=\"button\"]:hover,input[type=\"reset\"]:hover,input[type=\"submit\"]:hover", };

	$.each( $border_hover_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {
		
				$( 'head' ).find( '#sydney-customizer-styles-' + option ).remove();
	
				var output = selector + ' { border-color:' + to + '!important; }';
	
				$( 'head' ).append( '<style id="sydney-customizer-styles-' + option + '">' + output + '</style>' );
	
			} );
		} );
	});		

	//Stroke hover
	var $stroke_hover_options = { "scrolltop_color_hover":".go-top:hover svg", };

	$.each( $stroke_hover_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {

				$( 'head' ).find( '#sydney-customizer-stroke-' + option ).remove();

				var output = selector + ' { stroke:' + to + '!important; }';

				$( 'head' ).append( '<style id="sydney-customizer-stroke-' + option + '">' + output + '</style>' );

			} );
		} );
	});		

	//Color hover
	var $color_hover_options = { "color_link_hover":".entry-content a:not(.button):not(.elementor-button-link):hover","button_color_hover":"button:hover,.roll-button:hover,a.button:hover,.wp-block-button__link:hover,input[type=\"button\"]:hover,input[type=\"reset\"]:hover,input[type=\"submit\"]:hover","scrolltop_color_hover":".go-top:hover", "footer_widgets_links_hover_color":".sidebar-column .widget a:hover"};

	$.each( $color_hover_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {

				$( 'head' ).find( '#sydney-customizer-styles-' + option ).remove();

				var output = selector + ' { color:' + to + '!important; }';

				$( 'head' ).append( '<style id="sydney-customizer-styles-' + option + '">' + output + '</style>' );

			} );
		} );
	});

	//Fill
	var $fill_options = {"main_header_submenu_color":".mainnav ul ul li svg","offcanvas_menu_color": ".sydney-offcanvas-menu svg","mobile_header_color":"#masthead-mobile svg","offcanvas_menu_color":".sydney-offcanvas-menu svg","mobile_header_color":"#masthead-mobile svg","main_header_bottom_color":".bottom-header-row .sydney-svg-icon svg,.dropdown-symbol .ws-svg-icon svg","main_header_color":".main-header .header-item svg, .main-header .dropdown-symbol .sydney-svg-icon svg", "topbar_color":".top-bar svg","footer_color":".site-info .sydney-svg-icon svg"};

	$.each( $fill_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {
				$( selector ).css( 'fill', to );
			} );
		} );
	});	

	//Border color
	var $border_color_options = { "color_forms_borders":"input[type=\"text\"],input[type=\"email\"],input[type=\"url\"],input[type=\"password\"],input[type=\"search\"],input[type=\"number\"],input[type=\"tel\"],input[type=\"range\"],input[type=\"date\"],input[type=\"month\"],input[type=\"week\"],input[type=\"time\"],input[type=\"datetime\"],input[type=\"datetime-local\"],input[type=\"color\"],textarea,select,.woocommerce .select2-container .select2-selection--single,.woocommerce-page .select2-container .select2-selection--single,.woocommerce-account fieldset,.woocommerce-account .woocommerce-form-login, .woocommerce-account .woocommerce-form-register,.woocommerce-cart .woocommerce-cart-form .actions .coupon input[type=\"text\"]","shop_product_card_border_color": ".woocommerce-page ul.products li.product","link_separator_color":".sydney-offcanvas-menu .mainnav ul li","button_border_color":"button,.roll-button,a.button,.wp-block-button__link,input[type=\"button\"],input[type=\"reset\"],input[type=\"submit\"]","footer_credits_divider_color":".site-info,.site-footer","footer_widgets_divider_color":".footer-widgets,.footer-widgets-grid" };

	$.each( $border_color_options, function( option, selector ) {
		wp.customize( option, function( value ) {
			value.bind( function( to ) {
				$( selector ).css( 'border-color', to );
			} );
		} );
	});		

	//Responsive
	var $devices 	= { "desktop": "(min-width: 992px)", "tablet": "(min-width: 576px) and (max-width: 991px)", "mobile": "(max-width: 575px)" };

	var $topBottPad = { "breadcrumbs_padding":".sydney-breadcrumb-trail","footer_widgets_padding":".footer-widgets-grid", "button_top_bottom_padding":"button,.roll-button,a.button,.wp-block-button__link,input[type='button'],input[type='reset'],input[type='submit']", };
	$.each( $topBottPad, function( option, selector ) {
		$.each( $devices, function( device, mediaSize ) {
			wp.customize( option + '_' + device, function( value ) {
				value.bind( function( to ) {

					$( 'head' ).find( '#sydney-customizer-styles-' + option + '_' + device ).remove();

					var output = '@media ' + mediaSize + ' {' + selector + ' { padding-top:' + to + 'px;padding-bottom:' + to + 'px; } }';

					$( 'head' ).append( '<style id="sydney-customizer-styles-' + option + '_' + device + '">' + output + '</style>' );
				} );
			} );
		});
	});
	var $leftRightPad = { "button_left_right_padding":"button,.roll-button,a.button,.wp-block-button__link,input[type='button'],input[type='reset'],input[type='submit']", };
	$.each( $leftRightPad, function( option, selector ) {
		$.each( $devices, function( device, mediaSize ) {
			wp.customize( option + '_' + device, function( value ) {
				value.bind( function( to ) {

					$( 'head' ).find( '#sydney-customizer-styles-' + option + '_' + device ).remove();

					var output = '@media ' + mediaSize + ' {' + selector + ' { padding-left:' + to + 'px;padding-right:' + to + 'px; } }';

					$( 'head' ).append( '<style id="sydney-customizer-styles-' + option + '_' + device + '">' + output + '</style>' );
				} );
			} );
		});
	});
	var $fontSizes 	= { "footer_copyright_font_size":".site-info","button_font_size":"button,.roll-button,a.button,.wp-block-button__link,input[type='button'],input[type='reset'],input[type='submit']","sydney_menu_font_size":"#mainnav > div > ul > li > a","single_product_title_size":".woocommerce div.product .product-gallery-summary .entry-title","single_product_price_size":".woocommerce div.product .product-gallery-summary .price .amount","site_desc_font_size":".site-description","site_title_font_size":".site-title","body_font_size":"body, .posts-layout .entry-post","h1_font_size":"h1:not(.site-title)","h2_font_size":"h2","h3_font_size":"h3","h4_font_size":"h4","h5_font_size":"h5","h6_font_size":"h6","single_product_title_size":".product-gallery-summary .entry-title","single_product_price_size":".product-gallery-summary .price","loop_post_text_size":".posts-layout .entry-post","loop_post_meta_size":".posts-layout .entry-meta","loop_post_title_size":".posts-layout .entry-title","single_post_title_size": ".single .entry-header .entry-title","single_post_meta_size": ".single .entry-meta","footer_widgets_title_size":".sidebar-column .widget .widget-title","footer_widgets_body_size":".footer-widgets" };
	$.each( $fontSizes, function( option, selector ) {
		$.each( $devices, function( device, mediaSize ) {
			wp.customize( option + '_' + device, function( value ) {
				value.bind( function( to ) {

					$( 'head' ).find( '#sydney-customizer-styles-' + option + '_' + device ).remove();

					var output = '@media ' + mediaSize + ' {' + selector + ' { font-size:' + to + 'px; } }';

					$( 'head' ).append( '<style id="sydney-customizer-styles-' + option + '_' + device + '">' + output + '</style>' );
				} );
			} );
		});
	});

	//Footer
	wp.customize( 'footer_widgets_column_spacing_desktop', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets-grid' ).css( 'gap', to + 'px' );
		} );
	} );
	wp.customize( 'footer_widgets_divider_size', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets,.footer-widgets-grid' ).css( 'border-top-width', to + 'px');
		} );
	} );
	wp.customize( 'footer_credits_divider_size', function( value ) {
		value.bind( function( to ) {
			$( '.site-info,.site-footer' ).css( 'border-top-width', to + 'px');
		} );
	} );	
	wp.customize( 'footer_credits_padding_desktop', function( value ) {
		value.bind( function( to ) {
			$( '.site-info' ).css( {
				'padding-top': to + 'px',
				'padding-bottom': to + 'px'
			} );
		} );
	} );

	//Blog
	wp.customize( 'archive_featured_image_size_desktop', function( value ) {
		value.bind( function( to ) {
			$( '.posts-layout .list-image' ).css( 'width', to + '%' );
			$( '.posts-layout .list-content' ).css( 'width', 100 - to + '%' );
		} );
	} );
	wp.customize( 'archive_featured_image_size_desktop', function( value ) {
		value.bind( function( to ) {
			$( '.posts-layout .list-image' ).css( 'width', to + '%' );
			$( '.posts-layout .list-content' ).css( 'width', 100 - to + '%' );
		} );
	} );
	wp.customize( 'archive_meta_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.posts-layout .entry-meta.below-excerpt' ).css( 'margin-top', to + 'px' );
			$( '.posts-layout .entry-meta.above-title' ).css( 'margin-bottom', to + 'px' );
		} );
	} );
	wp.customize( 'archive_title_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.posts-layout .entry-header' ).css( 'margin-bottom', to + 'px' );
		} );
	} );	
	wp.customize( 'single_post_header_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.single .entry-header' ).css( 'margin-bottom', to + 'px' );
		} );
	} );	
	wp.customize( 'single_post_image_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.single-post .entry-thumb' ).css( 'margin-bottom', to + 'px' );
		} );
	} );
	wp.customize( 'single_post_meta_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.single .entry-meta-above' ).css( 'margin-bottom', to + 'px' );
			$( '.single .entry-meta-below' ).css( 'margin-top', to + 'px' );
		} );
	} );
	

	//Header		
	wp.customize( 'main_header_padding', function( value ) {
		value.bind( function( to ) {
			$( '.site-header-inner, .top-header-row' ).css( {
				paddingTop:  to + 'px',
				paddingBottom:  to + 'px',
			} );
		} );
	} );
	wp.customize( 'main_header_bottom_padding', function( value ) {
		value.bind( function( to ) {
			$( '.bottom-header-inner' ).css( {
				paddingTop:  to + 'px',
				paddingBottom:  to + 'px',
			} );
		} );
	} );	
	
	wp.customize( 'main_header_divider_color', function( value ) {
		value.bind( function( to ) {
			$( '.main-header, .bottom-header-row,.top-header-row,.site-header-inner, .bottom-header-inner' ).css( 'border-color', to );
		} );
	} );

	wp.customize( 'mobile_menu_alignment', function( value ) {
		value.bind( function( to ) {
			$( '.sydney-offcanvas-menu .mainnav ul li' ).css( 'text-align', to );
			$( '.mobile-header-item.offcanvas-items' ).css( 'text-align', to );
			$( '.mobile-header-item.offcanvas-items .social-profile' ).css( 'text-align', to );

			if ( 'center' == to ) {
				$( '.sydney-offcanvas-menu .header-item.header-woo' ).css( 'justify-content', 'center' );
				$( '.mobile-header-item.offcanvas-items .button' ).css( 'align-self', 'center' );
			} else if ( 'right' == to ) {
				$( '.sydney-offcanvas-menu .header-item.header-woo' ).css( 'justify-content', 'flex-end' );
				$( '.mobile-header-item.offcanvas-items .button' ).css( 'align-self', 'flex-end' );
			} else {
				$( '.sydney-offcanvas-menu .header-item.header-woo' ).css( 'justify-content', 'flex-start' );
				$( '.mobile-header-item.offcanvas-items .button' ).css( 'align-self', 'flex-start' );
			}
		} );
	} );	

	wp.customize( 'mobile_menu_link_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.sydney-offcanvas-menu .mainnav a' ).css( 'padding-top', to/2 );
			$( '.sydney-offcanvas-menu .mainnav a' ).css( 'padding-bottom', to/2 );
		} );
	} );		

	wp.customize( 'mobile_header_padding', function( value ) {
		value.bind( function( to ) {
			$( '.mobile-header' ).css( {
				paddingTop:  to + 'px',
				paddingBottom:  to + 'px',
			} );
		} );
	} );	

	wp.customize( 'mobile_header_separator_width', function( value ) {
		value.bind( function( to ) {
			$( '.sydney-offcanvas-menu .mainnav ul li' ).css( 'border-bottom-width', to + 'px' );
		} );
	} );	

	var $maxWidth = {
		"site_logo_size": ".site-logo"
	};
	$.each($maxWidth, function (option, selector) {
		$.each($devices, function (device, mediaSize) {
			wp.customize(option + '_' + device, function (value) {
			value.bind(function (to) {
				$('head').find('#sydney-customizer-styles-' + option + '_' + device).remove();
				var output = '@media ' + mediaSize + ' {' + selector + ' { max-height:' + to + 'px; } }';
				$('head').append('<style id="sydney-customizer-styles-' + option + '_' + device + '">' + output + '</style>');
			});
			});
		});
	});	


	//Typography
	wp.customize( 'sydney_body_font', function( value ) {
		value.bind( function( to ) {

			$( 'head' ).find( '#sydney-preview-google-fonts-body-css' ).remove();
			$( 'head' ).find( '#sydney-preview-body-weight-css' ).remove();

			$( 'head' ).append( '<link id="sydney-preview-google-fonts-body-css" href="" rel="stylesheet">' );

			$( '#sydney-preview-google-fonts-body-css' ).attr( 'href', 'https://fonts.googleapis.com/css?family=' + jQuery.parseJSON( to )['font'].replace(/ /g, '+') + ':' + jQuery.parseJSON( to )['regularweight'] + '&display=swap' );

			$( 'body' ).css( 'font-family', jQuery.parseJSON( to )['font'] );

			$( 'head' ).append('<style id="sydney-preview-body-weight-css" type="text/css">body {font-weight:' + jQuery.parseJSON( to )['regularweight'] + ';}</style>');

		} );
	} );	

	wp.customize( 'sydney_headings_font', function( value ) {
		value.bind( function( to ) {

			$( 'head' ).find( '#sydney-preview-google-fonts-headings-css' ).remove();
			$( 'head' ).find( '#sydney-preview-headings-weight-css' ).remove();

			$( 'head' ).append( '<link id="sydney-preview-google-fonts-headings-css" href="" rel="stylesheet">' );

			$( '#sydney-preview-google-fonts-headings-css' ).attr( 'href', 'https://fonts.googleapis.com/css?family=' + jQuery.parseJSON( to )['font'].replace(/ /g, '+') + ':' + jQuery.parseJSON( to )['regularweight'] + '&display=swap' );

			$( 'h1,h2,h3,h4,h5,h6,.site-title' ).css( 'font-family', jQuery.parseJSON( to )['font'] );

			$( 'head' ).append('<style id="sydney-preview-headings-weight-css" type="text/css">h1,h2,h3,h4,h5,h6,.site-title {font-weight:' + jQuery.parseJSON( to )['regularweight'] + ';}</style>');

		} );
	} );	

	wp.customize( 'sydney_menu_font', function( value ) {
		value.bind( function( to ) {

			$( 'head' ).find( '#sydney-preview-google-fonts-menu-css' ).remove();
			$( 'head' ).find( '#sydney-preview-menu-weight-css' ).remove();

			$( 'head' ).append( '<link id="sydney-preview-google-fonts-menu-css" href="" rel="stylesheet">' );

			$( '#sydney-preview-google-fonts-menu-css' ).attr( 'href', 'https://fonts.googleapis.com/css?family=' + jQuery.parseJSON( to )['font'].replace(/ /g, '+') + ':' + jQuery.parseJSON( to )['regularweight'] + '&display=swap' );

			$( '#mainnav > div > ul > li > a' ).css( 'font-family', jQuery.parseJSON( to )['font'] );

			$( 'head' ).append('<style id="sydney-preview-menu-weight-css" type="text/css">#mainnav > div > ul > li > a {font-weight:' + jQuery.parseJSON( to )['regularweight'] + ';}</style>');

		} );
	} );		

	wp.customize( 'headings_font_style', function( value ) {
		value.bind( function( to ) {
			$( 'h1,h2,h3,h4,h5,h6,.site-title' ).css( 'font-style', to );
		} );
	} );

	wp.customize( 'headings_line_height', function( value ) {
		value.bind( function( to ) {
			$( 'h1,h2,h3,h4,h5,h6,.site-title' ).css( 'line-height', to );
		} );
	} );

	wp.customize( 'headings_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'h1,h2,h3,h4,h5,h6,.site-title' ).css( 'letter-spacing', to + 'px' );
		} );
	} );
	
	wp.customize( 'headings_text_transform', function( value ) {
		value.bind( function( to ) {
			$( 'h1,h2,h3,h4,h5,h6,.site-title' ).css( 'text-transform', to );
		} );
	} );	

	wp.customize( 'menu_items_text_transform', function( value ) {
		value.bind( function( to ) {
			$( '#mainnav > div > ul > li > a' ).css( 'text-transform', to );
		} );
	} );	

	wp.customize( 'headings_text_decoration', function( value ) {
		value.bind( function( to ) {
			$( 'h1,h2,h3,h4,h5,h6,.site-title' ).css( 'text-decoration', to );
		} );
	} );	

	wp.customize( 'body_font_style', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'font-style', to );
		} );
	} );

	wp.customize( 'body_line_height', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'line-height', to );
		} );
	} );

	wp.customize( 'body_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'letter-spacing', to + 'px' );
		} );
	} );
	
	wp.customize( 'body_text_transform', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'text-transform', to );
		} );
	} );	

	wp.customize( 'body_text_decoration', function( value ) {
		value.bind( function( to ) {
			$( 'p, .posts-layout .entry-post' ).css( 'text-decoration', to );
		} );
	} );	

	//Single product title
	wp.customize('swc_single_product_title_font_size',function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce .product-gallery-summary .product_title' ).css('font-size', to + 'px');
		} );
	});	
	wp.customize( 'swc_single_product_title_color', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce .product-gallery-summary .product_title' ).css( 'color', to );
		} );
	} );

	wp.customize('shop_cart_show_coupon_form', function (value) {
		value.bind(function (to) {
		  if (!to) {
			$('.woocommerce-cart .coupon').css('display', 'none');
		  } else {
			$('.woocommerce-cart .coupon').css('display', 'block');
		  }
		});
	}); //Responsive
	
	//Woocommerce port
	wp.customize( 'shop_product_element_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce  ul.products li.product .col-md-7 > *,.woocommerce  ul.products li.product .col-md-8 > *,.woocommerce  ul.products li.product > *' ).css( 'margin-bottom', to + 'px' );
		} );
	} );
	
	wp.customize( 'shop_sale_tag_radius', function( value ) {
		value.bind( function( to ) {
			$( '.wc-block-grid__product-onsale, span.onsale' ).css( 'border-radius', to + 'px' );
		} );
	} );	

	wp.customize( 'shop_product_card_radius', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce ul.products li.product' ).css( 'border-radius', to + 'px' );
		} );
	} );

	wp.customize( 'shop_product_card_thumb_radius', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce ul.products li.product .loop-image-wrap' ).css( 'border-radius', to + 'px' );
		} );
	} );

	wp.customize( 'shop_product_card_border_size', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce ul.products li.product' ).css( 'border-width', to + 'px' );
		} );
	} );	

	//Placeholders
	wp.customize( 'color_forms_placeholder', function( value ) {
		value.bind( function( to ) {
			$( 'head' ).find( '#sydney-customizer-styles-color_forms_placeholder' ).remove();
		
			var output = 'input::placeholder {color:' + to + ';opacity:1;} input:-ms-input-placeholder {color:' + to + ';} input::-ms-input-placeholder {color:' + to + ';}';

			$( 'head' ).append( '<style id="sydney-customizer-styles-color_forms_placeholder">' + output + '</style>' )			
		} );
	} );	
	

	var $types = syd_data.post_types;
	$.each($types, function (index, value) {
		// Boxed content
		wp.customize( value + '_boxed_content', function( val ) {
			val.bind( function( to ) {
				if ( 'unboxed' === to ) {
					$( '.content-inner' ).css( {
						'padding': 0,
						'background': 'transparent',
						'box-shadow': 'none',
					} );
				} else {
					$( '.content-inner' ).css( {
						'padding': 60,
						'background': '#fff',
						'box-shadow': '0 0 15px 0 rgba(0,0,0,0.05)',
					} );
				}
			} );
		} );

		wp.customize( 'sidebar_single_' + value + '_position', function( val ) {
			val.bind( function( to ) {
				if ( 'page' === value ) {
					$( '.page' ).find( '.content-area' ).removeClass( 'sidebar-left sidebar-right' ).addClass( to );
				} else {
					$( '.single-' + value ).find( '.content-area' ).removeClass( 'sidebar-left sidebar-right' ).addClass( to );
				}
			} );
		} );
	} );

	// Color options
	var $color_options = sydney_theme_options;

	$.each( $color_options, function( key, css ) {
		wp.customize( css.option, function( value ) {
			
			value.bind( function( to, prev ) {

				var output = '';

				$.each( $color_options, function( key, css2 ) {	
					if( css.option === css2.option ) {
						var unit = typeof css2.unit !== 'undefined' ? css2.unit : '';

						if( typeof css2.condition !== 'undefined' ) {
							if( typeof window.parent.window.wp.customize( css2.condition ) !== 'undefined' ) {
								if( window.parent.window.wp.customize.control( css2.condition ).setting._value !== css2.cond_value ) {
									return;
								}
							}
						}

						if( ! to ) {
							to = 'transparent';
						}

						if( ! unit ) {
							to = typeof css2.rgba !== 'undefined' ? hexToRGB( to, css2.rgba ) : to;
						}

						if( typeof css2.pseudo === 'undefined' ) {
	
							if( typeof css2.prop === 'string' ) {
								$( css2.selector ).css( css2.prop, to + unit );
							} else {
								$.each( css2.prop, function( propkey, propvalue ) {
									$( css2.selector ).css( propvalue, to + unit );
								} );
							}
	
						} else {
							
							if( typeof css2.prop === 'string' ) {
								output += css2.selector + '{ '+ css2.prop +': '+ to +'!important; }'; 
							} else {
								$.each( css2.prop, function( propkey, propvalue ) {
									output += css2.selector + '{ '+ propvalue +': '+ to +'!important; }';
								} );
							}
						}
					}
				});

				if( output ) {

					if( $( '#sydney-customizer-styles-misc-'+ css.option ).get(0) ) {
						$( '#sydney-customizer-styles-misc-'+ css.option ).text( output );
					} else {
						$( 'head' ).append( '<style id="sydney-customizer-styles-misc-'+ css.option +'">' + output + '</style>' );
					}

				}

			} );

		} );
	} );	

	//Containers
	wp.customize( 'container_width', function( val ) {
		val.bind( function( to ) {

			$( 'head' ).find( '#sydney-customizer-styles-container_width' ).remove();

			var output = '@media (min-width: 1200px) { .container { width:100%;max-width: ' + to + 'px; } }';

			$( 'head' ).append( '<style id="sydney-customizer-styles-container_width">' + output + '</style>' );
		} );
	} );

	//Narrow container
	wp.customize( 'narrow_container_width', function( val ) {
		val.bind( function( to ) {

			$( 'head' ).find( '#sydney-customizer-styles-narrow_container_width' ).remove();

			var output = '@media (min-width: 1200px) { .container-narrow { width:100%;max-width: ' + to + 'px; } }';

			$( 'head' ).append( '<style id="sydney-customizer-styles-narrow_container_width">' + output + '</style>' );
		} );
	} );

	//wrapper_top_padding
	wp.customize( 'wrapper_top_padding', function( val ) {
		val.bind( function( to ) {
			$( '.page-wrap' ).css( 'padding-top', to + 'px' );
		} );
	} );

	//wrapper_bottom_padding
	wp.customize( 'wrapper_bottom_padding', function( val ) {
		val.bind( function( to ) {
			$( '.page-wrap' ).css( 'padding-bottom', to + 'px' );
		} );
	} );

	wp.customize( 'breadcrumbs_position', function( val ) {
		val.bind( function( to ) {
			if ( 'sydney_before_title' === to ) {
				$( '.sydney-breadcrumb-trail' ).insertBefore( '.entry-header' ).find( '.container' ).css( 'padding', '0' );;
			} else {
				$( '.sydney-breadcrumb-trail' ).insertAfter( '#masthead-mobile' ).find( '.container' ).css( 'padding', '0 15px' );
			}
		} );
	} );

	wp.customize( 'scrolltop_position', function( val ) {
		val.bind( function( to ) {
			if ( 'left' === to ) {
				$( '.go-top' ).removeClass( 'position-right' ).addClass( 'position-left' ).css( 'right', 'auto').css( 'left', '' );
			} else {
				$( '.go-top' ).removeClass( 'position-left' ).addClass( 'position-right' ).css( 'left', 'auto' ).css( 'right', '' );
			}
		} );
	} );

	//buttons_radius
	wp.customize( 'buttons_radius', function( val ) {
		val.bind( function( to ) {
			$( 'button,.roll-button,a.button,.wp-block-button__link,input[type=\"button\"],input[type=\"reset\"],input[type=\"submit\"], .wp-block-button__link' ).css( 'border-radius', to + 'px' );
		} );
	} );

	//button_text_transform
	wp.customize( 'button_text_transform', function( val ) {
		val.bind( function( to ) {
			$( 'button,.roll-button,a.button,.wp-block-button__link,input[type=\"button\"],input[type=\"reset\"],input[type=\"submit\"]' ).css( 'text-transform', to );
		} );
	} );

	//modal
	wp.customize( 'modal_popup_layout', function( val ) {
		val.bind( function( to ) {
			$( '.sydney-popup-wrapper' ).removeClass( 'sydney-popup-wrapper--layout-style1 sydney-popup-wrapper--layout-style2 sydney-popup-wrapper--layout-style3' ).addClass( 'sydney-popup-wrapper--layout-' + to );

			if ( 'style3' === to ) {
				$( '.sydney-popup-wrapper__content-side-image' ).hide();
			} else {
				$( '.sydney-popup-wrapper__content-side-image' ).show();
			}
		} );
	} );

	wp.customize( 'live_chat_position', function( val ) {
		val.bind( function( to ) {
			if ( 'left' === to ) {
				$( '.sydney-whatsapp-live-chat' ).removeClass( 'live-chat-right' ).addClass( 'live-chat-left' );
			} else {
				$( '.sydney-whatsapp-live-chat' ).removeClass( 'live-chat-left' ).addClass( 'live-chat-right' );
			}
		} );
	} );

	wp.customize( 'live_chat_popup_header_title', function( val ) {
		val.bind( function( to ) {
			$( '.live-chat-popup-header' ).find('h5').text( to );
		} );
	} );

	wp.customize( 'live_chat_whatsapp_cta', function( val ) {
		val.bind( function( to ) {
			$( '.live-chat-popup-body' ).text( to );
		} );
	} );

	wp.customize( 'live_chat_popup_button_text', function( val ) {
		val.bind( function( to ) {
			$( '.live-chat-popup-button' ).text( to );
		} );
	} );	

	wp.customize( 'live_chat_terms', function( val ) {
		val.bind( function( to ) {
			$( '.live-chat-terms' ).find( 'label' ).html( to );
		} );
	} );

	wp.customize( 'live_chat_only_mobile', function( val ) {
		val.bind( function( to ) {
			if ( true === to ) {
				$( '.sydney-whatsapp-live-chat' ).addClass( 'live-chat-mobile' );
			} else {
				$( '.sydney-whatsapp-live-chat' ).removeClass( 'live-chat-mobile' );
			}
		} );
	} );

	wp.customize( 'live_chat_whatsapp', function( val ) {
		val.bind( function( to ) {
			$( '.live-chat-popup-button' ).attr( 'href', 'https://wa.me/' + to );
		} );
	} );

	wp.customize( 'live_chat_whatsapp_initial_text', function( val ) {
		val.bind( function( to ) {
			var href = $( '.live-chat-popup-button' ).attr( 'href' );
			href = href.replace( /(\?text=)(.*)/, '' );
			$( '.live-chat-popup-button' ).attr( 'href', href + '?text=' + to );
		} );
	} );

	wp.customize( 'topbar_container', function( val ) {
		val.bind( function( to ) {
			if ( 'container' === to ) {
				$( '.top-bar > div' ).removeClass( 'container-fluid' ).addClass( 'container' );
			} else {
				$( '.top-bar > div' ).removeClass( 'container' ).addClass( 'container-fluid' );
			}
		} );
	} );

	wp.customize( 'topbar_delimiter', function( val ) {
		val.bind( function( to ) {
			if ( 'none' === to ) {
				$( '.top-bar .header-elements' ).removeClass( 'delimiter-dot delimiter-vertical delimiter-horizontal' ).addClass( 'delimiter-none' );
			} else if ( 'dot' === to ) {
				$( '.top-bar .header-elements' ).removeClass( 'delimiter-none delimiter-vertical delimiter-horizontal' ).addClass( 'delimiter-dot' );
			} else if ( 'vertical' === to ) {
				$( '.top-bar .header-elements' ).removeClass( 'delimiter-none delimiter-dot delimiter-horizontal' ).addClass( 'delimiter-vertical' );
			} else if ( 'horizontal' === to ) {
				$( '.top-bar .header-elements' ).removeClass( 'delimiter-none delimiter-dot delimiter-vertical' ).addClass( 'delimiter-horizontal' );
			}
		} );
	} );

	wp.customize( 'topbar_contact_mail', function( val ) {
		val.bind( function( to ) {
			$( '.topbar_contact_mail span' ).text( to );
			$( '.topbar_contact_mail' ).attr( 'href', 'mailto:' + to );
		} );
	} );

	wp.customize( 'topbar_contact_phone', function( val ) {
		val.bind( function( to ) {
			$( '.topbar_contact_phone span' ).text( to );
			$( '.topbar_contact_phone' ).attr( 'href', 'tel:' + to );
		} );
	} );

	wp.customize( 'topbar_text', function( val ) {
		val.bind( function( to ) {
			$( '.topbar-text' ).text( to );
		} );
	} );

	wp.customize( 'topbar_html_content', function( val ) {
		val.bind( function( to ) {
			$( '.topbar-html' ).html( to );
		} );
	} );

	//header_layout_desktop
	wp.customize( 'header_layout_desktop', function( val ) {
		val.bind( function( to ) {
			$( '.bottom-header-row' ).remove();

			//add class to body
			$( 'body' ).removeClass( 'has-header_layout_1 has-header_layout_2 has-header_layout_3 has-header_layout_4 has-header_layout_5 has-header_layout_6 has-header_layout_7' ).addClass( 'has-' + to );
		} );
	} );

	wp.customize( 'header_container', function( val ) {
		val.bind( function( to ) {
			if ( 'container-fluid' === to ) {
				$( '.main-header > .container' ).removeClass( 'container' ).addClass( 'container-fluid' );
				$( '.bottom-header-row > .container' ).removeClass( 'container' ).addClass( 'container-fluid' );
			} else {
				$( '.main-header > .container-fluid' ).removeClass( 'container-fluid' ).addClass( 'container' );
				$( '.bottom-header-row > .container-fluid' ).removeClass( 'container-fluid' ).addClass( 'container' );
			}
		} );
	} );	

	wp.customize( 'main_header_menu_position', function( val ) {
		val.bind( function( to ) {
			$( '.header-col.menu-col' ).removeClass( 'menu-left menu-center menu-right' ).addClass( 'menu-' + to );
		} );
	} );

	//enable_sticky_header
	wp.customize( 'enable_sticky_header', function( val ) {

		var sticky_header_type = wp.customize.instance( 'sticky_header_type' ).get();

		val.bind( function( to ) {
			if ( true === to ) {
				$( '.main-header' ).addClass( 'sticky-header sticky-' + sticky_header_type );
			} else {
				$( '.main-header' ).removeClass( 'sticky-header sticky-always sticky-scrolltop' );
			}
		} );
	} );

	//enable_sticky_header_mobile
	wp.customize( 'enable_sticky_header_mobile', function( val ) {

		var sticky_header_type = wp.customize.instance( 'sticky_header_type' ).get();

		val.bind( function( to ) {
			if ( true === to ) {
				$( '.mobile-header' ).addClass( 'sticky-header sticky-' + sticky_header_type );
			} else {
				$( '.mobile-header' ).removeClass( 'sticky-header sticky-always sticky-scrolltop' );
			}
		} );
	} );	

	var $header_components = ['header_components_l1','header_components_l3left','header_components_l3right','header_components_l4top','header_components_l4bottom','header_components_l5topleft','header_components_l5topright','header_components_l5bottom','social_profiles_header_layouts_6_7'];
	$.each( $header_components, function( index, value ) {
		wp.customize( value, function( val ) {
			val.bind( function( to ) {
				$( '.bottom-header-row' ).remove();
			} );
		} );
	} );

	wp.customize( 'header_button_text', function( val ) {
		val.bind( function( to ) {
			$( '.button.roll-button.header-item' ).text( to );
		} );
	} );

	wp.customize( 'header_button_link', function( val ) {
		val.bind( function( to ) {
			$( '.button.roll-button.header-item' ).attr( 'href', to );
		} );
	} );

	wp.customize( 'header_contact_mail', function( val ) {
		val.bind( function( to ) {
			$( '.header-contact-email span' ).text( to );
			$( '.header-contact-email' ).attr( 'href', 'mailto:' + to );
		} );
	} );

	wp.customize( 'header_contact_phone', function( val ) {
		val.bind( function( to ) {
			$( '.header-contact-phone span' ).text( to );
			$( '.header-contact-phone' ).attr( 'href', 'tel:' + to );
		} );
	} );

	wp.customize( 'header_html_content', function( val ) {
		val.bind( function( to ) {
			$( '.main-header .header-item.html-item' ).html( to );
		} );
	} );

	wp.customize( 'header_logout_text', function( val ) {
		val.bind( function( to ) {
			$( '.main-header .header-item .logout-link span' ).text( to );
		} );
	} );

	wp.customize( 'topbar_logout_text', function( val ) {
		val.bind( function( to ) {
			$( '.top-bar .header-item .logout-link span' ).text( to );
		} );
	} );

	wp.customize( 'mobile_menu_breakpoint', function( val ) {
		val.bind( function( to ) {

			$( 'head' ).find( '#sydney-customizer-styles-mobile_menu_breakpoint' ).remove();

			var output1 = '@media only screen and (max-width: ' + to + 'px) {.bottom-header-row, .main-header,.mobile-logo-link ~ .desktop-logo-link { display: none; } .sydney-offcanvas-menu, .mobile-header, .mobile-logo-link { display: block; }}';

			var output2 = '@media only screen and (min-width: ' + to + 1 + 'px) {#mainnav > div > ul,.bottom-header-row #mainnav > div > ul {align-items: initial;} #mainnav > div > ul { -ms-flex-wrap: wrap; flex-wrap: wrap; }}';

			$( 'head' ).append( '<style id="sydney-customizer-styles-mobile_menu_breakpoint">' + output1 + output2 + '</style>' );

		} );
	} );

	wp.customize( 'header_offcanvas_mode', function( val ) {
		val.bind( function( to ) {
			if ( 'layout2' === to ) {
				$( '.sydney-offcanvas-menu' ).css( 'max-width', '100%' );
			} else {
				$( '.sydney-offcanvas-menu' ).css( 'max-width', '300px' );
			}
		} );
	} );

	wp.customize( 'mobile_menu_icon', function( val ) {
		val.bind( function( to ) {
			if ( 'mobile-icon1' === to ) {
				$( '.menu-toggle i' ).html( '<svg width="16" height="7" viewBox="0 0 16 7" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="1"/><rect y="6" width="16" height="1"/></svg>' );
			} else if ( 'mobile-icon2' === to ) {
				$( '.menu-toggle i' ).html( '<svg width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="1"/><rect y="5" width="16" height="1"/><rect y="10" width="16" height="1"/></svg>' );
			} else if ( 'mobile-icon3' === to ) {
				$( '.menu-toggle i' ).html( '<svg width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="1"/><rect y="5" width="10" height="1"/><rect y="10" width="16" height="1"/></svg>' );
			} else if ( 'mobile-icon4' === to ) {
				$( '.menu-toggle i' ).html( '<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><rect y="7" width="14" height="1"/><rect x="7.5" y="0.5" width="14" height="1" transform="rotate(90 7.5 0.5)"/></svg>' );
			}
		} );
	} );

	wp.customize( 'sidebar_archives_position', function( val ) {
		val.bind( function( to ) {
			$( '.content-area.archive-wrapper' ).removeClass( 'sidebar-left sidebar-right' ).addClass( to );
		} );
	} );

	wp.customize( 'archive_title_container', function( val ) {
		val.bind( function( to ) {
			$( '.page-banner > .container' ).removeClass( 'container' ).addClass( to );
			$( '.page-banner > .container-fluid' ).removeClass( 'container-fluid' ).addClass( to );

			if ( 'container-fluid' === to ) {
				$( '.page-wrap' ).css( 'padding-top', '0' );
			} else {
				$( '.page-wrap' ).css( 'padding-top', '' );
			}
		} );
	} );

	wp.customize( 'feat_image_hover', function( val ) {
		val.bind( function( to ) {
			$( '.posts-layout .entry-thumb' ).removeClass( 'hover-effect-opacity hover-effect-zoom hover-effect-rotate hover-effect-none' ).addClass( 'hover-effect-' + to );
		} );
	} );

	wp.customize( 'archive_text_align', function( val ) {
		val.bind( function( to ) {
			$( '.posts-layout article' ).removeClass( 'post-align-left post-align-center post-align-right' ).addClass( 'post-align-' + to );
		} );
	} );

	wp.customize( 'archive_meta_elements', function( val ) {
		val.bind( function( to ) {

			var controls = { 
				'updated_date': ['archive_before_updated_date_text'],
				'custom_field': ['post_archive_custom_field_before_div','post_archive_custom_field_title','post_archive_before_custom_field','post_archive_custom_field','post_archive_after_custom_field'],
				'reading_time': ['reading_time_title','reading_time_speed','reading_time_before','reading_time_after'],
				'post_author': ['show_avatar'],
			};

			for ( var key in controls ) {
				if ( to.includes( key ) ) {
					showControls( controls[key] );
				} else {
					hideControls( controls[key] );
				}
			}
		} );
	} );

	wp.customize( 'read_more_link', function( val ) {
		val.bind( function( to ) {

			var controls = [ 'read_more_text', 'read_more_display' ];

			if ( true === to ) {
				showControls( controls );
			} else {
				hideControls( controls );
			}

		} );
	} );

	wp.customize( 'show_excerpt', function( val ) {
		val.bind( function( to ) {

			var controls = [ 'archive_content_type', 'exc_lenght', 'read_more_link', 'read_more_text', 'read_more_display' ];

			if ( true === to ) {
				showControls( controls );
			} else {
				hideControls( controls );
			}

		} );
	} );

	wp.customize( 'pagination_display', function( val ) {
		val.bind( function( to ) {

			$( '#sydney-customizer-styles-pagination_display' ).remove();

			if ( 'circle' === to ) {
				var output = '.woocommerce nav.woocommerce-pagination ul li .page-numbers, .nav-links .page-numbers { border-radius: 50%;}';							
			} else {
				var output = '.woocommerce nav.woocommerce-pagination ul li .page-numbers, .nav-links .page-numbers { border-radius: 0;}';
			}

			$( 'head' ).append( '<style id="sydney-customizer-styles-pagination_display">' + output + '</style>' );

		} );
	} );

	wp.customize( 'pagination_align', function( val ) {
		val.bind( function( to ) {
			$( '.navigation.pagination' ).css( 'text-align', to );
		} );
	} );

	wp.customize( 'post_header_container', function( val ) {
		val.bind( function( to ) {
			$( '.single-post .page-banner > .container' ).removeClass( 'container' ).addClass( to );
			$( '.single-post .page-banner > .container-fluid' ).removeClass( 'container-fluid' ).addClass( to );

			if ( 'container-fluid' === to ) {
				$( '.single-post .page-wrap' ).css( 'padding-top', '0' );
			} else {
				$( '.single-post .page-wrap' ).css( 'padding-top', '' );
			}
		} );
	} );

	wp.customize( 'single_post_show_related_posts', function( val ) {
		val.bind( function( to ) {

			var controls = [ 'related_posts_title', 'related_posts_number', 'related_posts_columns' ];

			if ( true === to ) {
				showControls( controls );
			} else {
				hideControls( controls );
			}

		} );
	} );

	wp.customize( 'enable_post_sharing', function( val ) {
		val.bind( function( to ) {

			var controls = [ 'single_post_share_title', 'single_post_sharing_networks' ];

			if ( true === to ) {
				showControls( controls );
			} else {
				hideControls( controls );
			}

		} );
	} );

	//footer_background_image
	wp.customize( 'footer_background_image', function( val ) {
		val.bind( function( to ) {

			var $footer_background_overlay = wp.customize.instance( 'footer_background_overlay' ).get();

			if ( to ) {
				$( '.footer-wrapper' ).css( 'background-image', 'url(' + to + ')' );
				$( '.site-footer, .footer-widgets, .footer-separator div' ).css( 'background', $footer_background_overlay );
			} else {
				$( '.footer-wrapper' ).css( 'background-image', '' );
				$( '.site-footer, .footer-widgets, .footer-separator div' ).css( 'background', '' );
			}
		} );
	} );

	wp.customize( 'footer_container', function( val ) {
		val.bind( function( to ) {
			if ( 'container' === to ) {
				$( '#sidebar-footer > div' ).removeClass( 'container-fluid' ).addClass( 'container' );
			} else {
				$( '#sidebar-footer > div' ).removeClass( 'container' ).addClass( 'container-fluid' );
			}
		} );
	} );

	wp.customize( 'footer_widgets_alignment', function( val ) {
		val.bind( function( to ) {
			$( '.footer-widgets-grid' ).removeClass( 'align-top align-middle align-bottom' ).addClass( 'align-' + to );
		} );
	} );

	wp.customize( 'footer_credits_container', function( val ) {
		val.bind( function( to ) {
			if ( 'container' === to ) {
				$( '.site-footer > div' ).removeClass( 'container-fluid' ).addClass( 'container' );
			} else {
				$( '.site-footer > div' ).removeClass( 'container' ).addClass( 'container-fluid' );
			}
		} );
	} );	

	//global colors
	$.each( [1,2,3,4,5,6,7,8,9], function( i ) {

		wp.customize( 'global_color_'+ i, function( value ) {
			value.bind( function( to ) {
				$( 'html' ).css( '--sydney-global-color-' + i, to );
				$( 'html' ).css( '--e-global-color-global_color_' + i, to );
			} );
		} );
	});
		
	wp.customize('offcanvas_menu_font_size',function( value ) {
		value.bind( function( to ) {
			$('.sydney-offcanvas-menu #mainnav > div > ul > li > a').css('font-size', to + 'px');
		} );
	});

	wp.customize('offcanvas_submenu_font_size',function( value ) {
		value.bind( function( to ) {
			$('.sydney-offcanvas-menu #mainnav ul ul li a').css('font-size', to + 'px');
		} );
	});	

	wp.customize( 'enable_block_templates', function( val ) {
		val.bind( function( to ) {
			$controls = ['block_templates_notice','enable_header_block_template', 'enable_footer_block_template', 'enable_single_block_template', 'enable_page_block_template', 'enable_archive_block_template', 'enable_search_block_template', 'enable_404_block_template'];
			if ( true === to ) {
				showControls( $controls );
			} else {
				hideControls( $controls );
			}
		} );
	} );

} )( jQuery );

function hideControls( options ) {
	for(var i=0;i<options.length;i++) {
		if ( ! window.parent.window.wp.customize.control( options[i] ) ) {
			continue;
		}
		window.parent.window.wp.customize.control( options[i] ).toggle(false);
		jQuery( window.parent.window.wp.customize.control( options[i] ).container[0] ).css( 'display', 'none' );
	}
}
function showControls( options ) {
	for(var i=0;i<options.length;i++) {
		if ( ! window.parent.window.wp.customize.control( options[i] ) ) {
			continue;
		}
		window.parent.window.wp.customize.control( options[i] ).toggle(true);
	}
}
function hexToRGB(hex, alpha) {
    var r = parseInt(hex.slice(1, 3), 16),
        g = parseInt(hex.slice(3, 5), 16),
        b = parseInt(hex.slice(5, 7), 16);
    if (alpha) {
        return "rgba(" + r + ", " + g + ", " + b + ", " + alpha + ")";
    } else {
        return "rgb(" + r + ", " + g + ", " + b + ")";
    }
}