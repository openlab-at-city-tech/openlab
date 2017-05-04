/*!
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/
 */
(function($) {
	$(document).ready( function() {

		/**
		 * Handle the styling of the "Settings" tab on the plugin settings page
		 * @since 4.2.3
		 */
		var tabs = $( '#cptch_settings_tabs_wrapper' );
		if ( tabs.length ) {
			var carousel        = $( '#cptch_settings_slick' ),
				tabs_panel      = $( '#cptch_settings_tabs' ),
				tab_index_field = $( 'input[name="cptch_active_tab"]' ),
				tabs_height     = tabs_panel.outerHeight() + 1,
				option_window   = $( window ),
				prevent_tabs_change = prevent_slides_change = false,
				height, min_height;
			/**
			 * Change tabs height
			 * @param  string    current_tab_id  The CSS ID on the selected options block
			 * @return void
			 */
			function set_tabs_height( current_tab_id ) {

				if ( ! current_tab_id.match( /\#/ ) )
					current_tab_id = '#' + current_tab_id;

				height = $( current_tab_id ).outerHeight() + 5;

				/* The side panel with tabs is not visible if screen width is less than 782px */
				if ( tabs_panel.is( ':visible' ) )
					min_height = tabs_height > height ? tabs_height : height;
				else
					min_height = height;

				tabs.css({
					'height': height,
					'min-height': min_height
				});
			}

			/**
			 *
			 * @param  object   window    A window global object
			 * @return void
			 */
			function set_carousel_height( window ) {
				if( window.width() <= 480 ) {
					if ( ! carousel.hasClass( 'cptch_slick_mobile' ) )
						carousel.addClass( 'cptch_slick_mobile' )
				} else {
					carousel.removeClass( 'cptch_slick_mobile' );
				}
			}

			/* jQuery tabs initialization */
			tabs.tabs({
				active: cptch_vars.start_tab,
				create: function( event, ui ) {
					set_tabs_height( $( '#cptch_settings_tabs .ui-tabs-active a' ).attr( 'href' ) );
				}
			/* change the current slide in the carousel after the switching between tabs */
			}).on( "tabsactivate", function( event, ui ) {
				if( ! prevent_tabs_change ) {
					prevent_slides_change = true;
					var slide_index = ui.newTab.index();
					carousel.slick( "slickGoTo", parseInt( slide_index ) );
					tab_index_field.val( slide_index );
					set_tabs_height( ui.newPanel[0].id );
				}
				prevent_tabs_change = false;
			});

			/* get the current slide for the carousel */
			var anchor       = window.location.hash,
				start_slide  = cptch_vars.start_tab,
				current_slide;
			if ( anchor != "" && anchor.match( /\#cptch\_(.)*\_tab/ ) ) {
				current_slide = $( '#cptch_settings_slick div:has(a[href="' + anchor + '"])' );
				if ( current_slide.length )
					start_slide = current_slide.index();
			}

			/* jQuery Slick carousel initialization */
			carousel.slick({
				initialSlide:   parseInt( start_slide ),
				slidesToShow:   3,
				slidesToScroll: 1,
				autoplay:       false,
				variableWidth:  true,
				prevArrow:      '<div class="slick-prev dashicons dashicons-arrow-left-alt2"></div>',
				nextArrow:      '<div class="slick-next dashicons dashicons-arrow-right-alt2"></div>',
				infinite:       true,
				focusOnSelect:  true,
				centerMode:     true
			/* change the current tab in the tabs panel after the switching between slides */
			}).on( 'afterChange', function( event, slick, currentSlide ) {
				if ( ! prevent_slides_change ) {
					prevent_tabs_change = true;
					var id        = $( "[data-slick-index='" + currentSlide + "'] a" ).attr( 'href' ),
						tab_index = $( '.ui-state-default:has(a[href="' + id + '"])' ).index();

					tabs.tabs({ active: parseInt( tab_index ) });
					set_tabs_height( id );
					tab_index_field.val( tab_index );
				}
				prevent_slides_change = false;
			}).find( 'a' ).click( function( event ) {
				event = event || window.event;
				event.preventDefault();
			});

			set_carousel_height( option_window );
			option_window.resize( function() {
				set_tabs_height( $( '#cptch_settings_tabs .ui-tabs-active a' ).attr( 'href' ) );
				set_carousel_height( $( this ) );
			});

			var enable_option  = $( "input[name*='[enable]']" ),
				image_format   = $( '#cptch_operand_format_images' ),
				image_options  = $( '.cptch_images_options' ),
				package_list   = $( ".cptch_tabs_package_list:not(.cptch_pro_pack_tab)" ),
				limit_option   = $( "input[name=cptch_enable_time_limit]" ),
				limit_value    = $( '.cptch_time_limit' ),
				notice;
			/*
			 * Show/hide all form settings by mark/unmark "Enable" checkbox.
			 * With this all form settings will be hidden except "Enable" checkbox.
			 */
			enable_option.click( function() {
				var current = $( this ),
					next    = current.closest( 'tr' ).next( 'tr' );
				if ( next.find( "input[name*='[use_general]']" ).is( ':checked' ) ) {
					current.is( ':checked' ) ? next.show() : next.hide();
				} else {
					var rows = current.closest( '.cptch_form_tab' ).find( 'tr, .bws_pro_version_bloc' ).not( '.cptch_form_option_enable' );
					current.is( ':checked' ) ? rows.show() : rows.hide();
				}
				set_tabs_height( $( '#cptch_settings_tabs .ui-tabs-active a' ).attr( 'href' ) );
			});

			/* Handle the displaying of notice message above lists of image packages */
			function cptch_image_options() {
				var is_checked = image_format.is( ':checked' );
				if ( is_checked )
					image_options.show();
				else
					image_options.hide();
				
				package_list.each( function() {
					notice = image_format.prev( '.cptch_enable_images_notice' );
					if ( ! notice.length )
						return;
					if ( image_format.find( 'input:checked' ).length && ! is_checked )
						notice.show();
					else
						notice.hide();
				});
				set_tabs_height( $( '#cptch_settings_tabs .ui-tabs-active a' ).attr( 'href' ) );
			}
			cptch_image_options()
			image_format.click( function() { cptch_image_options(); } );

			/* Handle lists of packages on form options tabs */
			package_list.resizable({
				alsoResize: "#cptch_settings_tabs_wrapper",
				handles: ( $( 'body' ).hasClass( 'rtl' ) ? 'sw' : 'se' )
			}).find( '.ui-resizable-handle' ).addClass( 'dashicons dashicons-editor-code' );

			package_list.find( 'input' ).change( function() {
				var pack_wrapper = $( this ).closest( '.cptch_tabs_package_list' ),
					notice       = pack_wrapper.prev( '.cptch_enable_images_notice' );
				if ( ! notice.length )
					return;
				if (
					! image_format.is( ':checked' ) &&
					pack_wrapper.find( 'input:checked' ).length
				)
					notice.show();
				else
					notice.hide();
				set_tabs_height( $( '#cptch_settings_tabs .ui-tabs-active a' ).attr( 'href' ) );
			});

			limit_option.click( function() {
				if ( $( this ).is( ':checked' ) )
					limit_value.show();
				else
					limit_value.hide();
			});

			function cptch_type() {
				if ( 'recognition' == $( 'input[name="cptch_type"]:checked' ).val() ) {
					$( '.cptch_for_recognition' ).show();
					$( '.cptch_for_math_actions' ).hide();
					image_format.attr( 'checked', 'checked' );
					cptch_image_options();	
				} else {
					$( '.cptch_for_recognition' ).hide();
					$( '.cptch_for_math_actions' ).show();
				}
			}
			cptch_type();
			$( 'input[name="cptch_type"]' ).click( function() { cptch_type(); } );		
		}

		/**
		 * Handle the "Whitelist" tab on the plugins option page
		 */
		$( 'button[name="cptch_show_whitelist_form"]' ).click( function() {
			$( this ).parent( 'form' ).hide();
			$( '.cptch_whitelist_form' ).show();
			return false;
		});

		var limit_options = $( '.cptch_limt_options' );
		$( 'input[name="cptch_use_time_limit"]' ).each( function() {
			if ( ! $( this ).is( ':checked' ) )
				limit_options.hide();
		}).click( function() {
			if ( $( this ).is( ':checked' ) )
				limit_options.show();
			else
				limit_options.hide();
		});

		$( 'input[name="cptch_use_la_whitelist"]' ).click( function() {
			$( this ).closest( 'form' ).submit();
		});
		/*  add to whitelist my ip */
		$( 'input[name="cptch_add_to_whitelist_my_ip"]' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				var my_ip = $( 'input[name="cptch_add_to_whitelist_my_ip_value"]' ).val();
				$( 'input[name="cptch_add_to_whitelist"]' ).val( my_ip ).attr( 'readonly', 'readonly' );
			} else {
				$( 'input[name="cptch_add_to_whitelist"]' ).val( '' ).removeAttr( 'readonly' );
			}
		});

	});
})(jQuery);

/*!
 * jQuery UI Touch Punch 0.2.3
 * jQuery UI Touch Punch is a small hack that enables the use of touch events on sites using the jQuery UI user interface library.
 * https://github.com/furf/jquery-ui-touch-punch
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);