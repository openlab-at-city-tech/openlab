/**
 * This file adds some LIVE to the Theme Customizer live preview.
 */
(function($) {
	
	// Preview init.
	wp.customize.bind( 'preview-ready', function() {
		if ( 'undefined' === typeof fixedtoc ) {
			return;
		}
		
		$( '#ftwp-container' ).on( 'ftocReady', function() {
			fixedtoc.option.update( 'fadeTriggerDuration', 99999999 );
		} );
	} );
	
	// Fixed position.
	reloadAfterPartialRefresh( 'location_fixed_position', function( to ) {
		fixedtoc.option.update( 'fixedPosition', to );
		var posCls = 'ftwp-top-right ftwp-middle-right ftwp-bottom-right ftwp-top-left ftwp-middle-left ftwp-bottom-left';
		$( '#ftwp-container' ).removeClass( posCls ).addClass( 'ftwp-' + to );		
	} );
	
	// Horizontal offset.
	reloadAfterPartialRefresh( 'location_horizontal_offset', function( to ) {
		fixedtoc.option.update( 'fixedOffsetX', to, 'int' );
	} );
	
	// Horizontal offset.
	reloadAfterPartialRefresh( 'location_vertical_offset', function( to ) {
		fixedtoc.option.update( 'fixedOffsetY', to, 'int' );
	} );
	
	// Trigger icon.
  wp.customize( 'fixed_toc[trigger_icon]', function( value ) {
    value.bind( function( to ) {
			$( '#ftwp-trigger .ftwp-trigger-icon, #ftwp-header-control' ).removeClass( function( i, cls ) {
				return regeCls( /ftwp\-icon\-\w+/, cls );
			} ).addClass( 'ftwp-icon-' + to );
    } );
  } );	
	
	// Trigger size.
	reloadAfterPartialRefresh( 'trigger_size', function( to ) {
		fixedtoc.option.update( 'triggerSize', to, 'int' );
	} );
	
	// Trigger shape.
  wp.customize( 'fixed_toc[trigger_shape]', function( value ) {
    value.bind( function( to ) {
			$( '#ftwp-trigger' ).removeClass( function( i, cls ) {
				return regeCls( /ftwp\-shape\-\w+/, cls );
			} ).addClass( 'ftwp-shape-' + to );
		} );
  } );
	
	// Trigger border
	reloadAfterPartialRefresh( 'trigger_border_width', function( to ) {
		$( '#ftwp-trigger' ).removeClass( function( i, cls ) {
			return regeCls( /ftwp\-border\-\w+/, cls );
		} ).addClass( 'ftwp-border-' + to );
		
		fixedtoc.option.update( 'triggerBorder', to );
		fixedtoc.option.update( 'triggerBorderWidth', getBorderWidth( to ), 'int' );
	} );
	
	// Trigger initial visibility.
  wp.customize( 'fixed_toc[trigger_initial_visibility]', function( value ) {
    value.bind( function( to ) {
			if ( 'hide' == to ) {
				$( '#ftwp-container' ).removeClass( 'ftwp-minimize' ).addClass( 'ftwp-maximize' );
			} else {
				$( '#ftwp-container' ).removeClass( 'ftwp-maximize' ).addClass( 'ftwp-minimize' );
			}
		} );
  } );
	
	// Contents font size.
	reloadAfterPartialRefresh( 'fixedtoc_contents_header' );
	reloadAfterPartialRefresh( 'fixedtoc_contents_list' );
	
	// Contents width to fixed TOC.
	reloadAfterPartialRefresh( 'contents_fixed_width' );	
	
	// Contents height to fixed TOC.
	reloadAfterPartialRefresh( 'contents_fixed_height', function( to ) {
		fixedtoc.option.update( 'contentsFixedHeight', to, 'int' );
	} );
	
	// Contents shape.
  wp.customize( 'fixed_toc[contents_shape]', function( value ) { 
    value.bind( function( to ) {
			$( '#ftwp-contents' ).removeClass( function( i, cls ) {
				return regeCls( /ftwp\-shape\-\w+/, cls );
			} ).addClass( 'ftwp-shape-' + to );
		} );
  } );	
	
	// Contents border.
	reloadAfterPartialRefresh( 'contents_border_width', function( to ) {
		$( '#ftwp-contents' ).removeClass( function( i, cls ) {
			return regeCls( /ftwp\-border\-\w+/, cls );
		} ).addClass( 'ftwp-border-' + to );

		fixedtoc.option.update( 'contentsBorder', to );
		fixedtoc.option.update( 'contentsBorderWidth', getBorderWidth( to ), 'int' );		
	} );
	
	// Contents width in post.
	reloadAfterPartialRefresh( 'contents_width_in_post', function( to ) {
		fixedtoc.option.update( 'contentsWidthInPost', to, 'int' );
	} );
	
	// Contents height in post.
	reloadAfterPartialRefresh( 'contents_height_in_post', function( to ) {
		fixedtoc.option.update( 'contentsHeightInPost', to, 'int' );
	} );
	
	// Contents header title.
  wp.customize( 'fixed_toc[contents_header_title]', function( value ) {
    value.bind( function( to ) {
			$( '#ftwp-header-title' ).text( to );
			
			fixedtoc.reload();
		} );
  } );  
	
	// Contents header font family.
	reloadAfterPartialRefresh( 'contents_header_font_family' );
	
	// Contents header custom font family.
	reloadAfterPartialRefresh( 'contents_header_customize_font_family' );
	
	// Contents header font bold.
	reloadAfterPartialRefresh( 'contents_header_font_bold' );
	
	// Contents list font family.
	reloadAfterPartialRefresh( 'contents_list_font_family' );
	
	// Contents list custom font family.
	reloadAfterPartialRefresh( 'contents_list_customize_font_family' );
	
	// Contents list style type.
	reloadAfterPartialRefresh( 'contents_list_style_type', function( to ) {
			$( '#ftwp-list' ).removeClass( function( i, cls ) {
				return regeCls( /ftwp\-liststyle\-[\w\-]+/i, cls );
			} ).addClass( 'ftwp-liststyle-' + to );		
	} );
	
	// Contents list strong 1st.
	reloadAfterPartialRefresh( 'contents_list_strong_1st', function( to ) {
		if ( to ) {
			$( '#ftwp-list' ).addClass( 'ftwp-strong-first' );
		} else {
			$( '#ftwp-list' ).removeClass( 'ftwp-strong-first' );
		}
			
	} );
	
	// Contents active link effect.
	reloadAfterPartialRefresh( 'effects_active_link', function( to ) {
			$( '#ftwp-list' ).removeClass( function( i, cls ) {
				return regeCls( /ftwp\-effect\-[\w\-]+/i, cls );
			} ).addClass( 'ftwp-effect-' + to );		
	} );
	
	
	/**
	 * Common functions.
	 *
	 */
	
	// Reload fixedtoc after partial refresh
	function reloadAfterPartialRefresh( name, func ) {
		wp.customize( 'fixed_toc[' + name + ']', function( value ) {
			value.bind( function( to ) {
				wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
					if ( undefined !== func ) func( to );
					fixedtoc.reload();
				} );			
			} );
		} );
	}
	
	// Return cls by regular express
	function regeCls( reg, cls ) {
		var new_cls = cls.match( reg );
		if ( new_cls ) {
			return new_cls[0];
		}		
	}
	
	// Get border width.
	function getBorderWidth( border ) {
		switch( border ) {
			case 'thin': return 1;
			case 'medium': return 2;
			case 'bold': return 5;
			default: return 0;
		}
	}
	
})( jQuery );