/* global hestiaGtb */

(function ( $ ) {
	$.hestiaGutenberg = {
		init: function () {
			this.manipulateDom();
			this.setupFeaturedImage();
			this.setupSidebar( hestiaGtb.sidebarLayout );
			this.handleMetabox();
			this.resizeTitle();
		},

		handleMetabox: function () {
			var self = this;
			var value;
			$( '#hestia-page-settings #control-hestia_layout_select label' ).on( 'click', function () {
				value = $( this ).prev().val();
				self.toggleEditorClasses( 'sidebar', value );
				self.setupSidebar( value );
			} );

			$( '#hestia-page-settings #control-hestia_layout_select .reset-data' ).on( 'click', function () {
				value = $(this).data('default');
				self.toggleEditorClasses( 'sidebar', value );
				self.setupSidebar( value );
			} );

			$( '#hestia-page-settings #control-hestia_header_layout label' ).on( 'click', function () {
				value = $( this ).prev().val();
				self.toggleEditorClasses( 'header', value );
			} );

			$( '#hestia-page-settings #control-hestia_header_layout .reset-data' ).on( 'click', function () {
				value = $(this).data('default');
				self.toggleEditorClasses( 'header', value );
			} );
		},

		toggleEditorClasses: function ( context, value ) {
			var editor = $( '.hestia-gtb' );
			var classesList;

			if ( $( editor ).hasClass( context + '-' + value ) ) {
				return false;
			}

			if ( context === 'header' ) {
				classesList = 'header-default header-no-content header-classic-blog';
			} else {
				classesList = 'sidebar-sidebar-right sidebar-sidebar-left sidebar-full-width';
			}

			$( editor ).removeClass( classesList );
			$( editor ).addClass( context + '-' + value );

			this.resizeTitle();
		},

		resizeTitle: function() {
			jQuery( '.editor-post-title__input' ).focus().blur();
		},

		/**
		 * Add classes to elements.
		 */
		manipulateDom: function () {
			var editor = $( '.editor-styles-wrapper' );
			var blockList = $( '.editor-styles-wrapper .editor-block-list__layout' );
			var classes = 'hestia-gtb ' + ' header-' + hestiaGtb.headerLayout + ' sidebar-' + hestiaGtb.sidebarLayout;

			// Add classes.
			$( editor ).addClass( classes );
			$( blockList ).prev().addClass( 'hestia-featured-background title-container' );

			$( '.editor-styles-wrapper > .editor-writing-flow > div:first-child > div' ).wrap( '<div class="hestia-writing-flow-inside"></div>' );

			// Insert header for no content header layout.
			$( '.hestia-gtb' ).prepend( '<div class="hestia-featured-background no-content"></div>' );

			// Add image for featured image in content.
			$( '.editor-writing-flow .editor-post-title' ).append( '<div class="wp-block hestia-featured-background classic-blog-featured"></div>' );

			$( blockList ).wrap( '<div class="hestia-block-list-wrap"></div>' );
		},

		setupSidebar: function ( position ) {
			$( '.hestia-sidebar-dummy' ).remove();
			var contentContainer = $( '.hestia-block-list-wrap' );
			var fullContainer = $( '.hestia-writing-flow-inside' );
			switch ( position ) {
				default:
					break;
				case 'full-width':
					break;
				case 'sidebar-left':
					$( contentContainer ).prepend( this.getSidebarMarkup( 'left' ) );
					$( fullContainer ).prepend( this.getSidebarMarkup( 'left', true ) );
					break;
				case 'sidebar-right':
					$( contentContainer ).append( this.getSidebarMarkup( 'right' ) );
					$( fullContainer ).append( this.getSidebarMarkup( 'right', true ) );

					break;

			}
		},

		getSidebarMarkup: function ( position, fullWidth ) {
			fullWidth = typeof fullWidth !== 'undefined' ? fullWidth : false;

			if ( fullWidth === true ) {
				position += ' full-width';
			}
			return '<div class="hestia-sidebar-dummy ' + position + '">' + hestiaGtb.strings.sidebar + '</div>';
		},

		setupFeaturedImage: function () {
			if ( hestiaGtb.headerSitewide === 'yes' ) {
				if ( hestiaGtb.headerImage.length > 0 && hestiaGtb.headerImage !== 'remove-header' ) {
					this.setFeaturedImage( hestiaGtb.headerImage );
				}
				return false;
			}

			if( hestiaGtb.featuredImage.length > 0 ) {
				this.setFeaturedImage( hestiaGtb.featuredImage );
			}
			var self = this;
			var observer = new MutationObserver( function ( mutations ) {
				mutations.forEach( function ( mutation ) {
					if ( mutation.target.className === 'editor-post-featured-image' ) {
						var url = $( mutation.target ).find( 'img' ).attr( 'src' );
						if ( typeof url !== 'undefined' ) {
							self.setFeaturedImage( url );
							return false;
						}
						self.setFeaturedImage( null );
						return false;
					}
				} );
			} );

			var featuredControls = $( '.edit-post-layout' );

			observer.observe( featuredControls[ 0 ], {
				childList: true,
				subtree: true
			} );
		},

		setFeaturedImage: function ( url ) {
			var featuredImages = $( '.hestia-featured-background' );
			if ( url === null ) {
				$( featuredImages ).removeAttr( 'style' );
				$( '.hestia-gtb' ).removeClass( 'has-featured-image' );
				return false;
			}
			$( featuredImages ).css( { 'background-image': 'url(' + url + ')' } );
			$( '.hestia-gtb' ).addClass( 'has-featured-image' );
		},
	};
})( jQuery );

jQuery( window ).load( function () {
	jQuery.hestiaGutenberg.init();
} );
