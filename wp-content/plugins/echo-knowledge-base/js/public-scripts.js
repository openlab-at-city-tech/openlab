jQuery(document).ready(function($) {


	/********************************************************************
	 *           Search ( Block and Shortcode )
	 ********************************************************************/

	let moduleSearchBox = $( '#epkb-ml__module-search' );

	// If Search Box exits
	if( moduleSearchBox.length > 0 ) {

		const searchType = $( '.eckb-kb-block-search' ).length ? 'Block Search' : 'Shortcode Search';
		console.log('=== ' + searchType + ' ===');

		// handle KB search form
		$( 'body' ).on( 'submit', '#epkb-ml-search-form', function( e ) {
			e.preventDefault();  // do not submit the form

			if ( $( this ).closest( '.eckb-block-editor-preview' ).length ) {
				return;
			}

			if ( $( '.epkb-ml-search-box__input' ).val() === '' ) {
				return;
			}


			// Check if AI Search Results is enabled
				if ( $( this ).attr( "data-ai-search-results" ) === "1" && typeof window.epkbAISearchResults !== "undefined" ) {
					const query = $( ".epkb-ml-search-box__input" ).val();
					const kbId = $( "#epkb_kb_id" ).val();
					const collectionId = $( this ).attr( "data-collection-id" );
					const collectionToken = $( this ).attr( "data-collection-token" ) || null;
					window.epkbAISearchResults.openDialog( query, kbId, collectionId, collectionToken );
					return;
				}

			const kb_block_post_id = $( this ).data( 'kb-block-post-id' );

			let postData = {
				action: 'epkb-search-kb',
				epkb_kb_id: $( '#epkb_kb_id' ).val(),
				search_words: $( '.epkb-ml-search-box__input' ).val(),
				is_kb_main_page: $( '.eckb_search_on_main_page' ).length || ( !!kb_block_post_id ? 1 : 0 ),
				kb_block_post_id: !!kb_block_post_id ? kb_block_post_id : 0,
			};

			let msg = '';

			$.ajax({
				type: 'GET',
				dataType: 'json',
				data: postData,
				url: epkb_vars.ajaxurl,
				beforeSend: function (xhr)
				{
					//Loading Spinner
					$( '.epkbfa-ml-loading-icon').css( 'visibility','visible');
					$( '.epkbfa-ml-search-icon').css( 'visibility','hidden');
					$( '.epkb-ml-search-box__text').css( 'visibility','hidden');
					$( '#epkb-ajax-in-progress' ).show();
				}

			}).done(function (response)
			{
				response = ( response ? response : '' );

				//Hide Spinner
				$( '.epkbfa-ml-loading-icon').css( 'visibility','hidden');
				$( '.epkbfa-ml-search-icon').css( 'visibility','visible');
				$( '.epkb-ml-search-box__text').css( 'visibility','visible');

				if ( response.error || response.status !== 'success') {
					//noinspection JSUnresolvedVariable
					msg = epkb_vars.msg_try_again;
				} else {
					msg = response.search_result;
				}

			}).fail(function (response, textStatus, error)
			{
				//noinspection JSUnresolvedVariable
				msg = epkb_vars.msg_try_again + '. [' + ( error ? error : epkb_vars.unknown_error ) + ']';

			}).always(function ()
			{
				$('#epkb-ajax-in-progress').hide();

				if ( msg ) {

					$( '#epkb-ml-search-results' ).css( 'display','block' ).html( msg );
					
					// Call the callback if it exists for AI search integration
					if ( typeof window.epkb_ml_search_result_callback === 'function' ) {
						window.epkb_ml_search_result_callback( msg );
					}
				}
			});
		});

		$( document ).on('click', function( event ) {
			let searchResults = $( '#epkb-ml-search-results' );
			let searchBox = $( '#epkb-ml-search-box' );

			let isClickInsideResults = searchResults.has( event.target ).length > 0;
			let isClickInsideSearchBox = searchBox.has( event.target ).length > 0;

			if ( !isClickInsideResults && !isClickInsideSearchBox ) {
				// Click is outside of search results and search box
				searchResults.hide(); // Hide the search results
			}
		});

		// Hide search results if user is entering new input
		$( document ).on( 'keyup', ".epkb-ml-search-box__input", function() {
			if ( !this.value ) {
				$( '#epkb-ml-search-results' ).css( 'display','none' );
			}
		});

		// Track if mouse is down inside search module to prevent hiding on focusout during click
		let isMouseDownInsideSearch = false;

		$( '#epkb-ml__module-search' ).on( 'mousedown', function() {
			isMouseDownInsideSearch = true;
		});

		$( document ).on( 'mouseup', function() {
			// Reset flag after a short delay to allow focusout to check it
			setTimeout( function() {
				isMouseDownInsideSearch = false;
			}, 150 );
		});

		// Hide search results when user tabs out of the search module (WCAG Accessibility)
		$( '#epkb-ml__module-search' ).on( 'focusout', function( event ) {
			let searchModule = $( '#epkb-ml__module-search' )[0];
			let relatedTarget = event.relatedTarget;

			// If mouse is down inside search, don't hide (user is clicking inside)
			if ( isMouseDownInsideSearch ) {
				return;
			}

			// If relatedTarget exists and is inside the search module, don't hide
			if ( relatedTarget && searchModule.contains( relatedTarget ) ) {
				return;
			}

			// If relatedTarget is null (e.g., tabbing away), check activeElement after a delay
			if ( relatedTarget === null ) {
				setTimeout( function() {
					let activeElement = document.activeElement;
					// Only hide if focus is truly outside the search module
					if ( searchModule && !searchModule.contains( activeElement ) && !isMouseDownInsideSearch ) {
						$( '#epkb-ml-search-results' ).hide();
					}
				}, 100 );
			} else {
				// Focus is moving outside the search module
				$( '#epkb-ml-search-results' ).hide();
			}
		});
	}


	/********************************************************************
	 *           Categories and Articles ( Block and Shortcode )
	 ********************************************************************/

	let categoriesAndArticles = $( '#epkb-ml__module-categories-articles' );

	// If Categories and Articles exits
	if( categoriesAndArticles.length > 0 ) {
		const categoriesType = $( '.epkb-block-main-page-container' ).length ? 'Block Categories and Articles' : 'Shortcode Categories and Articles';
		console.log('=== ' + categoriesType + ' ===');

		// Tabs / Mobile Select ------------------------------------------------------/

		//Get the highest height of Tab and make all other tabs the same height
		$( window ).on( 'resize', function () {
			let navTabsLiLocal = $( '.epkb-nav-tabs li' );
			let tabContainerLocal = $( '#epkb-content-container' );
			if ( tabContainerLocal.length && navTabsLiLocal.length ){
				let tallestHeight = 0;
				tabContainerLocal.find( navTabsLiLocal ).each( function(){
					let this_element = $(this).outerHeight( true );
					if( this_element > tallestHeight ) {
						tallestHeight = this_element;
					}
				});
				tabContainerLocal.find( navTabsLiLocal ).css( 'min-height', tallestHeight );
			}
		} );

		// After binding the resize handler (inside the categoriesAndArticles guard)
		$(window).trigger('resize');

		function changePanels( Index ){
			$('.epkb-panel-container .' + Index + '').addClass('active');
		}

		function updateTabURL( tab_id, tab_name ) {
			let location = window.location.href;
			location = update_query_string_parameter(location, 'top-category', tab_name);
			window.history.pushState({"tab":tab_id}, "title", location);
			// http://stackoverflow.com/questions/32828160/appending-parameter-to-url-without-refresh
		}

		window.onpopstate = function(e){

			let tabContainerLocal = $( '#epkb-content-container' );

			if ( e.state && e.state.tab.indexOf('epkb_tab_') !== -1) {
				//document.title = e.state.pageTitle;

				// hide old section
				tabContainerLocal.find('.epkb_top_panel').removeClass('active');

				// re-set tab; true if mobile drop-down
				if ( $( "#main-category-selection" ).length > 0 )
				{
					$("#main-category-selection").val(tabContainerLocal.find('#' + e.state.tab).val());
				} else {
					tabContainerLocal.find('.epkb_top_categories').removeClass('active');
					tabContainerLocal.find('#' + e.state.tab).addClass('active');
				}

				tabContainerLocal.find('.' + e.state.tab).addClass('active');

			// if user tabs back to the initial state, select the first tab if not selected already
			} else if ( $('#epkb_tab_1').length > 0 && ! tabContainerLocal.find('#epkb_tab_1').hasClass('active') ) {

				// hide old section
				tabContainerLocal.find('.epkb_top_panel').removeClass('active');

				// re-set tab; true if mobile drop-down
				if ( $( "#main-category-selection" ).length > 0 )
				{
					$("#main-category-selection").val(tabContainerLocal.find('#epkb_tab_1').val());
				} else {
					tabContainerLocal.find('.epkb_top_categories').removeClass('active');
					tabContainerLocal.find('#epkb_tab_1').addClass('active');
				}

				tabContainerLocal.find('.epkb_tab_1').addClass('active');
			}
		};

		$( document ).on( 'click keydown', '#epkb-content-container .epkb-nav-tabs li', function ( e ){

			// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			// Prevent default for Space key to avoid page scroll
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let isKeyboardActivation = e.type === 'keydown';
			let tabContainerLocal = $( '#epkb-content-container' );

			// Update aria-selected for all tabs (WCAG Accessibility)
			tabContainerLocal.find( '.epkb-nav-tabs li' ).removeClass('active').attr( 'aria-selected', 'false' );

			$(this).addClass('active').attr( 'aria-selected', 'true' );

			tabContainerLocal.find( '.epkb-tab-panel' ).removeClass('active');
			changePanels ( $(this).attr('id') );
			updateTabURL( $(this).attr('id'), $(this).data('cat-name') );

			// WCAG Accessibility: Move focus to first article in the panel when activated via keyboard
			if ( isKeyboardActivation ) {
				let panelId = $(this).attr('id');
				let activePanel = tabContainerLocal.find( '.' + panelId + '.epkb-tab-panel' );
				let firstArticle = activePanel.find( '.epkb-mp-article' ).first();
				if ( firstArticle.length ) {
					firstArticle.focus();
				}
			}
		});

		// Tabs Layout: MOBILE: switch to the top category user selected
		$( document ).on( 'change', "#main-category-selection", function() {
			$('#epkb-content-container').find('.epkb-tab-panel').removeClass('active');
			// drop down
			$( "#main-category-selection option:selected" ).each(function() {

				changePanels ( $(this).attr('id') );
				if ( ! $( this ).closest( '.eckb-block-editor-preview' ).length ) {
					updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
				}
			});
		});

		// Tabs Layout: Level 1 Show more if articles are assigned to top categories
		$( document ).on( 'click', '#epkb-ml-tabs-layout .epkb-ml-articles-show-more',function( e ) {
			e.preventDefault();
			$( this ).hide();
			$( this ).parent().find( '.epkb-list-column li' ).removeClass( 'epkb-ml-article-hide' );
		});

		function update_query_string_parameter(uri, key, value) {
			let re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			let separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			}
			else {
				return uri + separator + key + "=" + value;
			}
		}


		// Categories ----------------------------------------------------------------/

		//Detect if a div is inside a list item then it's a sub category
		$( document ).on( 'click keydown', '.epkb-section-body .epkb-category-level-2-3', function( e ){
			
			// Only proceed if it's a click or Enter key
			if ( e.type !== 'click' && e.keyCode !== 13 ) {
				return;
			}

			$( this ).parent().children( 'ul' ).toggleClass( 'active' );
			let categoryId = $( this ).parent().children( 'ul' ).data( 'list-id' );
			// Accessibility: aria-expand

			// Get current data attribute value
			let ariaExpandedVal = $( this ).attr( 'aria-expanded' );

			// Switch the value of the data Attribute on click.
			switch( ariaExpandedVal ) {
				case 'true':
					// It is being closed so Set to False
					$( this ).attr( 'aria-expanded', 'false' );
					$( this ).parent().find('.epkb-show-all-articles[data-btn-id="' + categoryId + '"]').removeClass( 'epkb-show-all-btn--active' );
					break;
				case 'false':
					// It is being opened so Set to True
					$( this ).attr( 'aria-expanded', 'true' );
					$( this ).parent().find('.epkb-show-all-articles[data-btn-id="' + categoryId + '"]').addClass( 'epkb-show-all-btn--active' );
					break;
				default:
			}
		});

		/**
		 * Sub Category icon toggle
		 *
		 * Toggle between open icon and close icon
		 * Accessibility: Set aria-expand values
		 */
		$( document ).on('click keydown', '#epkb-content-container .epkb-section-body .epkb-category-level-2-3:not(.epkb-category-focused)', function ( e ){
			
			// Only proceed if it's a click or Enter key
			if ( e.type !== 'click' && e.keyCode !== 13 ) {
				return;
			}

			let $icon = $(this).find('.epkb-category-level-2-3__cat-icon');

			let plus_icons = [ 'ep_font_icon_plus' ,'ep_font_icon_minus' ];
			let plus_icons_box = [ 'ep_font_icon_plus_box' ,'ep_font_icon_minus_box' ];
			let arrow_icons1 = [ 'ep_font_icon_right_arrow' ,'ep_font_icon_down_arrow' ];
			let arrow_icons2 = [ 'ep_font_icon_arrow_carrot_right' ,'ep_font_icon_arrow_carrot_down' ];
			let arrow_icons3 = [ 'ep_font_icon_arrow_carrot_right_circle' ,'ep_font_icon_arrow_carrot_down_circle' ];
			let folder_icon = [ 'ep_font_icon_folder_add' ,'ep_font_icon_folder_open' ];

			function toggle_category_icons( $array ){

				//If Parameter Icon exists
				if( $icon.hasClass( $array[0] ) ){

					$icon.removeClass( $array[0] );
					$icon.addClass( $array[1] );

				}else if ( $icon.hasClass( $array[1] )){

					$icon.removeClass( $array[1] );
					$icon.addClass($array[0]);
				}
			}

			toggle_category_icons( plus_icons );
			toggle_category_icons( plus_icons_box );
			toggle_category_icons( arrow_icons1 );
			toggle_category_icons( arrow_icons2 );
			toggle_category_icons( arrow_icons3 );
			toggle_category_icons( folder_icon );
		});

		/**
		 * WCAG Accessibility: Categories Layout - Subcategory link navigation
		 * When user presses Enter on the focused subcategory name div, navigate to the category link
		 */
		$( document ).on( 'keydown', '#epkb-ml-categories-layout .epkb-category-level-2-3__cat-name', function( e ) {

			// Only proceed if Enter or Space key
			if ( e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			e.preventDefault();

			// Find and trigger the link inside
			let $link = $( this ).find( 'a' ).first();
			if ( $link.length ) {
				window.location.href = $link.attr( 'href' );
			}
		});

		/**
		 * Show all articles functionality
		 *
		 * When user clicks on the "Show all articles" it will toggle the "hide" class on all hidden articles
		 */
		$( document ).on( 'click keydown', '#epkb-modular-main-page-container .epkb-show-all-articles, .epkb-block-main-page-container .epkb-show-all-articles, #epkb-main-page-container .epkb-show-all-articles', function ( e ) {

			// Only proceed if it's a click or Enter/Space key
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			if ( e.type === 'keydown' ) {
				e.preventDefault();
			}

			$( this ).toggleClass( 'epkb-show-articles' );
			let categoryId = $( this ).data('btn-id');
			let article = $( '[data-list-id="' + categoryId + '"]' ).find( 'li' );
			let isExpanding = $( this ).hasClass( 'epkb-show-articles' );
			let firstRevealedArticle = null;

			//If this has class "active" then change the text to Hide extra articles
			if ( isExpanding ) {

				//If Active
				$(this).find('.epkb-show-text').addClass('epkb-hide-elem');
				$(this).find('.epkb-hide-text').removeClass('epkb-hide-elem');
				$(this).attr( 'aria-expanded','true' );

			} else {
				//If not Active
				$(this).find('.epkb-show-text').removeClass('epkb-hide-elem');
				$(this).find('.epkb-hide-text').addClass('epkb-hide-elem');
				$(this).attr( 'aria-expanded','false' );
			}

			$( article ).each(function() {

				//If has class "hide" remove it and replace it with class "Visible"
				if ( $(this).hasClass( 'epkb-hide-elem')) {
					$(this).removeClass('epkb-hide-elem');
					$(this).addClass('visible');

					// Track the first revealed article for focus management (WCAG Accessibility)
					if ( firstRevealedArticle === null ) {
						firstRevealedArticle = $(this);
					}
				}else if ( $(this).hasClass( 'visible')) {
					$(this).removeClass('visible');
					$(this).addClass('epkb-hide-elem');
				}
			});

			// WCAG Accessibility: Move focus to the first revealed article when expanding
			if ( isExpanding && firstRevealedArticle !== null ) {
				let firstLink = firstRevealedArticle.find( 'a' ).first();
				if ( firstLink.length ) {
					firstLink.focus();
				}
			}
		});


		// Classic Layout --------------------------------------------------------------/

		// Show main content of Category.
		$( document ).on( 'click keydown', '#epkb-ml-classic-layout .epkb-ml-articles-show-more', function( e ) {

			// WCAG Accessibility: Only proceed if it's a click or Enter/Space key
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let isKeyboardActivation = e.type === 'keydown';
			let categorySection = $( this ).parent().parent();

			categorySection.toggleClass( 'epkb-category-section--active');

			categorySection.find( '.epkb-category-section__body' ).slideToggle();

			$( this ).find( '.epkb-ml-articles-show-more__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );

			const isExpanded = $( this ).find( '.epkb-ml-articles-show-more__show-more__icon' ).hasClass( 'epkbfa-minus' );

			// WCAG Accessibility: Update aria-expanded attribute
			$( this ).attr( 'aria-expanded', isExpanded ? 'true' : 'false' );

			if ( isExpanded ) {
				$( this ).parent().find( '.epkb-ml-article-count' ).hide();

				// WCAG Accessibility: Move focus to first focusable element when expanded via keyboard
				if ( isKeyboardActivation ) {
					let sectionBody = categorySection.find( '.epkb-category-section__body' );
					// First check for subcategory name, then article
					let firstFocusable = sectionBody.find( '.epkb-ml-2-lvl-category__name' ).first();
					if ( ! firstFocusable.length ) {
						firstFocusable = sectionBody.find( '.epkb-ml-article-container' ).first();
					}
					if ( firstFocusable.length ) {
						// Use setTimeout to wait for slideToggle animation to start
						setTimeout( function() {
							firstFocusable.focus();
						}, 50 );
					}
				}
			} else {
				$( this ).parent().find( '.epkb-ml-article-count' ).show();
			}
		} );

		// Toggle Level 2 Category Articles and Level 3 Categories
		$( document ).on( 'click keydown', '#epkb-ml-classic-layout .epkb-ml-2-lvl-category__name', function( e ) {

			// WCAG Accessibility: Only proceed if it's a click or Enter/Space key
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let isKeyboardActivation = e.type === 'keydown';
			let container = $( this ).closest( '.epkb-ml-2-lvl-category-container' );
			// to hide Articles, use a click only on the "minus" icon (but allow keyboard to toggle)
			if ( e.type === 'click' && container.hasClass( 'epkb-ml-2-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-2-lvl-category__show-more__icon' ) ) return;
			container.find( '.epkb-ml-2-lvl-article-list' ).slideToggle();
			container.find( '.epkb-ml-3-lvl-categories' ).slideToggle();
			container.find( '.epkb-ml-2-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
			container.toggleClass( 'epkb-ml-2-lvl-category--active' );

			// WCAG Accessibility: Update aria-expanded attribute
			let isExpanded = container.hasClass( 'epkb-ml-2-lvl-category--active' );
			$( this ).attr( 'aria-expanded', isExpanded ? 'true' : 'false' );

			// WCAG Accessibility: Move focus to first article when expanded via keyboard
			if ( isKeyboardActivation && isExpanded ) {
				let firstArticle = container.find( '.epkb-ml-2-lvl-article-list .epkb-ml-article-container' ).first();
				if ( firstArticle.length ) {
					setTimeout( function() {
						firstArticle.focus();
					}, 50 );
				}
			}
		} );

		// Toggle Level 3 Category Articles and Level 4 Categories
		$( document ).on( 'click keydown', '#epkb-ml-classic-layout .epkb-ml-3-lvl-category__name', function( e ) {

			// WCAG Accessibility: Only proceed if it's a click or Enter/Space key
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let isKeyboardActivation = e.type === 'keydown';
			let container = $( this ).closest( '.epkb-ml-3-lvl-category-container' );
			// to hide Articles, use a click only on the "minus" icon (but allow keyboard to toggle)
			if ( e.type === 'click' && container.hasClass( 'epkb-ml-3-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-3-lvl-category__show-more__icon' ) ) return;
			container.find( '.epkb-ml-3-lvl-article-list' ).slideToggle();
			container.find( '.epkb-ml-4-lvl-categories' ).slideToggle();
			container.find( '.epkb-ml-3-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
			container.toggleClass( 'epkb-ml-3-lvl-category--active' );

			// WCAG Accessibility: Update aria-expanded attribute
			let isExpanded = container.hasClass( 'epkb-ml-3-lvl-category--active' );
			$( this ).attr( 'aria-expanded', isExpanded ? 'true' : 'false' );

			// WCAG Accessibility: Move focus to first article when expanded via keyboard
			if ( isKeyboardActivation && isExpanded ) {
				let firstArticle = container.find( '.epkb-ml-3-lvl-article-list .epkb-ml-article-container' ).first();
				if ( firstArticle.length ) {
					setTimeout( function() {
						firstArticle.focus();
					}, 50 );
				}
			}
		} );

		// Toggle Level 4 Category Articles and Level 5 Categories
		$( document ).on( 'click keydown', '#epkb-ml-classic-layout .epkb-ml-4-lvl-category__name', function( e ) {

			// WCAG Accessibility: Only proceed if it's a click or Enter/Space key
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let isKeyboardActivation = e.type === 'keydown';
			let container = $( this ).closest( '.epkb-ml-4-lvl-category-container' );
			// to hide Articles, use a click only on the "minus" icon (but allow keyboard to toggle)
			if ( e.type === 'click' && container.hasClass( 'epkb-ml-4-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-4-lvl-category__show-more__icon' ) ) return;
			container.find( '.epkb-ml-4-lvl-article-list' ).slideToggle();
			container.find( '.epkb-ml-5-lvl-categories' ).slideToggle();
			container.find( '.epkb-ml-4-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
			container.toggleClass( 'epkb-ml-4-lvl-category--active' );

			// WCAG Accessibility: Update aria-expanded attribute
			let isExpanded = container.hasClass( 'epkb-ml-4-lvl-category--active' );
			$( this ).attr( 'aria-expanded', isExpanded ? 'true' : 'false' );

			// WCAG Accessibility: Move focus to first article when expanded via keyboard
			if ( isKeyboardActivation && isExpanded ) {
				let firstArticle = container.find( '.epkb-ml-4-lvl-article-list .epkb-ml-article-container' ).first();
				if ( firstArticle.length ) {
					setTimeout( function() {
						firstArticle.focus();
					}, 50 );
				}
			}
		} );

		// Toggle Level 5 Category Articles
		$( document ).on( 'click keydown', '#epkb-ml-classic-layout .epkb-ml-5-lvl-category__name', function( e ) {

			// WCAG Accessibility: Only proceed if it's a click or Enter/Space key
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let isKeyboardActivation = e.type === 'keydown';
			let container = $( this ).closest( '.epkb-ml-5-lvl-category-container' );
			// to hide Articles, use a click only on the "minus" icon (but allow keyboard to toggle)
			if ( e.type === 'click' && container.hasClass( 'epkb-ml-5-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-5-lvl-category__show-more__icon' ) ) return;
			container.find( '.epkb-ml-5-lvl-article-list' ).slideToggle();
			container.find( '.epkb-ml-5-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
			container.toggleClass( 'epkb-ml-5-lvl-category--active' );

			// WCAG Accessibility: Update aria-expanded attribute
			let isExpanded = container.hasClass( 'epkb-ml-5-lvl-category--active' );
			$( this ).attr( 'aria-expanded', isExpanded ? 'true' : 'false' );

			// WCAG Accessibility: Move focus to first article when expanded via keyboard
			if ( isKeyboardActivation && isExpanded ) {
				let firstArticle = container.find( '.epkb-ml-5-lvl-article-list .epkb-ml-article-container' ).first();
				if ( firstArticle.length ) {
					setTimeout( function() {
						firstArticle.focus();
					}, 50 );
				}
			}
		} );


		// Drill Down Layout --------------------------------------------------------------/

		// Top Category Button Trigger (WCAG Accessibility: added keydown for Enter/Space)
		$( document ).on( 'click keydown', '.epkb-ml-top__cat-container', function( e ) {

			// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			// Prevent default for Space key to avoid page scroll
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			const isKeyboardActivation = e.type === 'keydown' && ( e.keyCode === 13 || e.keyCode === 32 );

			// Define frequently used selectors
			const $catContent_ShowClass     			= 'epkb-ml__cat-content--show';
			const $topCatButton_ActiveClass 			= 'epkb-ml-top__cat-container--active';
			const $catButton_ActiveClass    			= 'epkb-ml__cat-container--active';
			const $catButtonContainers_ActiveClass  	= 'epkb-ml-categories-button-container--active';
			const $catButtonContainers_ShowClass    	= 'epkb-ml-categories-button-container--show';
			const $backButton_ActiveClass 			= 'epkb-back-button--active';

			const $allCatContent            = $( '.epkb-ml-all-categories-content-container' );

			// Reset aria-expanded for all top category buttons
			$( '.epkb-ml-top__cat-container' ).attr( 'aria-expanded', 'false' );
			$( '.epkb-ml-top__cat-container' ).removeClass( $topCatButton_ActiveClass );

			// Hide content when clicked on active Top Category button
			if ( $( this ).hasClass( $topCatButton_ActiveClass ) ) {
				$( this ).removeClass( $topCatButton_ActiveClass );
				$( this ).attr( 'aria-expanded', 'false' );
				$allCatContent.hide();
				return;
			}

			// Do not show Back button for Top Category content
			$allCatContent.find( '.epkb-back-button' ).removeClass( $backButton_ActiveClass );

			let currentTopCat = $( this );

			// Highlight current Top Category button
			$( this ).removeClass( $topCatButton_ActiveClass );
			currentTopCat.addClass( $topCatButton_ActiveClass );
			currentTopCat.attr( 'aria-expanded', 'true' );

			moveCategoriesBoxUnderTopCategoryButton( currentTopCat );

			// Remove all Classes
			$( '.epkb-ml-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
			$( '.epkb-ml__cat-content' ).removeClass( $catContent_ShowClass );
			$( '.epkb-ml__cat-container' ).removeClass( $catButton_ActiveClass );
			$( '.epkb-ml__cat-container' ).attr( 'aria-expanded', 'false' );



			$allCatContent.show();

			// Get ID of current Category
			const catId = $( this ).data( 'cat-id' );

			// Show Level 1 Category Description / Articles
			$( '.epkb-ml-1-lvl__cat-content[data-cat-id="' + catId + '"]' ).addClass( $catContent_ShowClass );

			// Show Level 2 Categories
			$( '.epkb-ml-2-lvl-categories-button-container[data-cat-level="1"][data-cat-id="' + catId + '"]' ).addClass( $catButtonContainers_ShowClass );

			// WCAG Accessibility: Move focus to the first article or subcategory after expanding (keyboard only)
			if ( isKeyboardActivation ) {
				setTimeout( function() {
					// First try to focus on a subcategory
					let $firstSubcat = $( '.epkb-ml-2-lvl-categories-button-container[data-cat-level="1"][data-cat-id="' + catId + '"]' ).find( '.epkb-ml__cat-container' ).first();
					if ( $firstSubcat.length && $firstSubcat.is(':visible') ) {
						$firstSubcat.focus();
					} else {
						// Otherwise focus on the first article
						let $firstArticle = $( '.epkb-ml-1-lvl__cat-content[data-cat-id="' + catId + '"]' ).find( '.epkb-ml-article-container' ).first();
						if ( $firstArticle.length ) {
							$firstArticle.focus();
						}
					}
				}, 50 );
			}
		});

		// Move content box under the category row
		function moveCategoriesBoxUnderTopCategoryButton( currentTopCat ) {

			const $allCatContent = $( '.epkb-ml-all-categories-content-container' );

			$allCatContent.hide();

			let currentTopCatOffset = currentTopCat.offset().top;
			

			// Current Top Category is not the last one in the list
			$( '.epkb-ml-top__cat-container' ).each( function() {
				let catOffset = $( this ).offset().top;
				if ( catOffset - currentTopCatOffset > 0 ) {
					$allCatContent.insertAfter( $( this ).prev( '.epkb-ml-top__cat-container' ) );
					isBoxMoved = true;
					return false;

				// insert content after the Category if it is the last in the list but still is not below the current Category
				} else if ( ! $( this ).next( '.epkb-ml-top__cat-container' ).length ) {
					$allCatContent.insertAfter( $( this ) );
				}
			} );

			$allCatContent.show();
		}

		// Category Button Trigger (WCAG Accessibility: added keydown for Enter/Space)
		$( document ).on( 'click keydown', '.epkb-ml__cat-container', function( e ) {

			// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			// Prevent default for Space key to avoid page scroll
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			const isKeyboardActivation = e.type === 'keydown' && ( e.keyCode === 13 || e.keyCode === 32 );

			// Define frequently used selectors
			const $catContent_ShowClass     			= 'epkb-ml__cat-content--show';
			const $catButton_ActiveClass    			= 'epkb-ml__cat-container--active';
			const $catButtonContainers_ActiveClass  	= 'epkb-ml-categories-button-container--active';
			const $catButtonContainers_ShowClass    	= 'epkb-ml-categories-button-container--show';
			const $backButton_ActiveClass 			= 'epkb-back-button--active';

			// Check if the button already has the active class
			if ( $( this) .hasClass( $catButton_ActiveClass ) ) {
				return; // Don't run the rest of the code if the button is already active
			}

			// Show Back button
			$( '.epkb-ml-all-categories-content-container .epkb-back-button' ).addClass( $backButton_ActiveClass );

			// Get Level and ID of current Category
			const catLevel = parseInt( $( this ).data( 'cat-level' ) );
			const catId = $( this ).data( 'cat-id' );

			// Get Level and ID of parent Category
			const parentCatLevel = catLevel - 1;
			const parentCatId = $( this ).data( 'parent-cat-id' );

			// Get Level of child Categories
			const childCatLevel = catLevel + 1;

			// Reset aria-expanded for sibling subcategories
			$( '.epkb-ml-' + catLevel + '-lvl__cat-container' ).attr( 'aria-expanded', 'false' );

			// Show current Category Header
			$( this ).addClass( $catButton_ActiveClass );
			$( this ).attr( 'aria-expanded', 'true' );
			$( '.epkb-ml-' + catLevel + '-lvl-categories-button-container[data-cat-id="' + parentCatId + '"]' ).addClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );

			// Hide content of parent Category
			$( '.epkb-ml-' + parentCatLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
			$( '.epkb-ml-' + parentCatLevel + '-lvl__cat-container' ).removeClass( $catButton_ActiveClass );
			$( '.epkb-ml-' + parentCatLevel + '-lvl__cat-content' ).removeClass( $catContent_ShowClass );

			// Show content of current Category
			$( '.epkb-ml-' + catLevel + '-lvl__cat-content[data-cat-id="' + catId + '"]' ).addClass( $catContent_ShowClass );
			$( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container[data-cat-id="' + catId + '"]' ).addClass( $catButtonContainers_ShowClass );

			// WCAG Accessibility: Move focus to the first article or subcategory after expanding (keyboard only)
			if ( isKeyboardActivation ) {
				setTimeout( function() {
					// First try to focus on a subcategory
					let $firstSubcat = $( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container[data-cat-id="' + catId + '"]' ).find( '.epkb-ml__cat-container' ).first();
					if ( $firstSubcat.length && $firstSubcat.is(':visible') ) {
						$firstSubcat.focus();
					} else {
						// Otherwise focus on the first article
						let $firstArticle = $( '.epkb-ml-' + catLevel + '-lvl__cat-content[data-cat-id="' + catId + '"]' ).find( '.epkb-ml-article-container' ).first();
						if ( $firstArticle.length ) {
							$firstArticle.focus();
						}
					}
				}, 50 );
			}
		});

		// Back Button: Shift+Tab should go to the last visible subcategory button
		$( document ).on( 'keydown', '.epkb-back-button', function( e ) {
			// Only handle Shift+Tab
			if ( e.keyCode === 9 && e.shiftKey ) {
				e.preventDefault();

				// Find the last visible subcategory button in a container with --show class
				let $visibleSubcatContainer = $( '.epkb-ml-categories-button-container.epkb-ml-categories-button-container--show' ).last();
				if ( $visibleSubcatContainer.length ) {
					let $lastSubcat = $visibleSubcatContainer.find( '.epkb-ml__cat-container' ).last();
					if ( $lastSubcat.length ) {
						$lastSubcat.focus();
						return;
					}
				}

				// Fallback: find the last visible article in the visible content section
				let $visibleContent = $( '.epkb-ml__cat-content.epkb-ml__cat-content--show' );
				if ( $visibleContent.length ) {
					let $lastArticle = $visibleContent.find( '.epkb-ml-article-container' ).last();
					if ( $lastArticle.length ) {
						$lastArticle.focus();
						return;
					}
				}
			}
		});

		// Back Button of Category Content
		// Note: Since this is a <button> element, the browser automatically triggers click on Enter/Space
		// so we only need to handle click events - no separate keydown handling needed
		$( document ).on( 'click', '.epkb-back-button', function( e ) {

			// Define frequently used selectors
			const $catContent_ShowClass     			= 'epkb-ml__cat-content--show';
			const $topCatButton_ActiveClass 			= 'epkb-ml-top__cat-container--active';
			const $catButton_ActiveClass    			= 'epkb-ml__cat-container--active';
			const $catButtonContainers_ActiveClass  	= 'epkb-ml-categories-button-container--active';
			const $catButtonContainers_ShowClass    	= 'epkb-ml-categories-button-container--show';
			const $backButton_ActiveClass 			= 'epkb-back-button--active';

			// Get Level of current Category
			let currentCatContent = $( '.epkb-ml__cat-content' + '.' + $catContent_ShowClass );
			let catLevel = parseInt( currentCatContent.data( 'cat-level' ) );
			let currentCatId = currentCatContent.data( 'cat-id' );

			// Get Level of child Categories
			let childCatLevel = catLevel + 1;

			// Return to the Top Categories view if Level 1 Content is currently shown
			if ( catLevel === 1 ) {
				$( '.epkb-ml-top__cat-container' ).removeClass( $topCatButton_ActiveClass );
				$( '.epkb-ml-top__cat-container' ).attr( 'aria-expanded', 'false' );
				$( '.epkb-ml-all-categories-content-container' ).hide();

				// WCAG Accessibility: Move focus back to the top category button
				let $topCatButton = $( '.epkb-ml-top__cat-container[data-cat-id="' + currentCatId + '"]' );
				if ( $topCatButton.length ) {
					$topCatButton.focus();
				}
				return;
			}

			// Get Level and ID of parent Category
			let parentCatId = currentCatContent.data( 'parent-cat-id' );
			let parentCatLevel = catLevel - 1;

			// Do not show Back button for Top Category content
			if ( parentCatLevel === 1 ) {
				$( '.epkb-ml-all-categories-content-container .epkb-back-button' ).removeClass( $backButton_ActiveClass );
			}

			// Reset aria-expanded for current category
			$( '.epkb-ml-' + catLevel + '-lvl__cat-container' ).attr( 'aria-expanded', 'false' );

			// Hide elements of the current Category
			$( '.epkb-ml-' + catLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass );
			$( '.epkb-ml-' + catLevel + '-lvl__cat-container' ).removeClass( $catButton_ActiveClass );
			$( '.epkb-ml-' + catLevel + '-lvl__cat-content' ).removeClass( $catContent_ShowClass );
			$( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );

			// Show elements of previous level Category
			let parentCatButton = $( '.epkb-ml-' + parentCatLevel + '-lvl__cat-container[data-cat-id="' + parentCatId + '"]' );
			parentCatButton.closest( '.epkb-ml-categories-button-container' ).addClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
			parentCatButton.addClass( $catButton_ActiveClass );
			parentCatButton.attr( 'aria-expanded', 'true' );
			$( '.epkb-ml-' + parentCatLevel + '-lvl__cat-content[data-cat-id="' + parentCatId + '"]' ).addClass( $catContent_ShowClass );

			// WCAG Accessibility: Move focus back to the parent category button
			if ( parentCatButton.length ) {
				parentCatButton.focus();
			}
		});

	} // if( categoriesAndArticles.length > 0 )


	/********************************************************************
	 *           FAQs Module ( Block and Shortcode )
	 ********************************************************************/

	let faqsModule = $( '#epkb-ml__module-faqs' );
	if( faqsModule.length > 0 ) {
		
		const faqsType = $( '.eckb-kb-block-faqs' ).length ? 'Blocks FAQs' : 'Shortcode FAQs';
		console.log('=== ' + faqsType + ' ===');

		// FAQs Module -----------------------------------------------------------------/
		// Accordion mode
		/*$('.epkb-faqs-accordion-mode .epkb-faqs__item__question').filter(function() {
			return $(this).data('faq-type') === 'module';
		}).on('click', function(){

			let container = $(this).closest('.epkb-faqs__item-container').eq(0);

			if (container.hasClass('epkb-faqs__item-container--active')) {
				container.find('.epkb-faqs__item__answer').stop().slideUp(400);
			} else {
				container.find('.epkb-faqs__item__answer').stop().slideDown(400);
			}
			container.toggleClass('epkb-faqs__item-container--active');
		});*/
		$( document ).on( 'click keydown', '.epkb-faqs-accordion-mode .epkb-faqs__item__question[data-faq-type="module"]', function( e ){

			// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			// Prevent default for Space key to avoid page scroll
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let container = $(this).closest('.epkb-faqs__item-container').eq(0);
			let isExpanding = !container.hasClass('epkb-faqs__item-container--active');

			if (container.hasClass('epkb-faqs__item-container--active')) {
				container.find('.epkb-faqs__item__answer').stop().slideUp(400);
			} else {
				container.find('.epkb-faqs__item__answer').stop().slideDown(400);
			}
			container.toggleClass('epkb-faqs__item-container--active');

			// Update aria-expanded attribute (WCAG Accessibility)
			$(this).attr( 'aria-expanded', isExpanding ? 'true' : 'false' );
		});

		// Toggle Mode
		/*$('.epkb-faqs-toggle-mode .epkb-faqs__item__question').filter(function() {
			return $(this).data('faq-type') === 'module';
		}).on('click', function(){

			let container = $(this).closest('.epkb-faqs__item-container').eq(0);

			// Close other opened items
			$('.epkb-faqs__item-container--active').not(container).removeClass('epkb-faqs__item-container--active')
				.find('.epkb-faqs__item__answer').stop().slideUp(400);

			// Toggle the clicked item
			if (container.hasClass('epkb-faqs__item-container--active')) {
				container.find('.epkb-faqs__item__answer').stop().slideUp(400);
			} else {
				container.find('.epkb-faqs__item__answer').stop().slideDown(400);
			}
			container.toggleClass('epkb-faqs__item-container--active');
		});*/
		$( document ).on( 'click keydown', '.epkb-faqs-toggle-mode .epkb-faqs__item__question[data-faq-type="module"]', function( e ){

			// Only proceed if it's a click or Enter/Space key (WCAG Accessibility)
			if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
				return;
			}

			// Prevent default for Space key to avoid page scroll
			if ( e.type === 'keydown' && e.keyCode === 32 ) {
				e.preventDefault();
			}

			let container = $(this).closest('.epkb-faqs__item-container').eq(0);
			let isExpanding = !container.hasClass('epkb-faqs__item-container--active');

			// Close other opened items and update their aria-expanded
			$('.epkb-faqs__item-container--active').not(container).removeClass('epkb-faqs__item-container--active')
				.find('.epkb-faqs__item__answer').stop().slideUp(400);
			$('.epkb-faqs__item-container--active').not(container).find('.epkb-faqs__item__question').attr( 'aria-expanded', 'false' );

			// Toggle the clicked item
			if (container.hasClass('epkb-faqs__item-container--active')) {
				container.find('.epkb-faqs__item__answer').stop().slideUp(400);
			} else {
				container.find('.epkb-faqs__item__answer').stop().slideDown(400);
			}
			container.toggleClass('epkb-faqs__item-container--active');

			// Update aria-expanded attribute (WCAG Accessibility)
			$(this).attr( 'aria-expanded', isExpanding ? 'true' : 'false' );
		});
		
	}


	/********************************************************************
	 *           Article Page 
	 ********************************************************************/

	let articlePage = $( '#eckb-article-body' );

	// Article TOC v2 ----------------------------------/
	let TOC = {
				
		firstLevel: 1, 
		lastLevel: 6, 
		searchStr: '',
		currentId: '',
		offset: 50,
		excludeClass: false,
		
		init: function() {
			this.getOptions();
			
			let articleHeaders = this.getArticleHeaders();
			let $articleTocLocal = $( '.eckb-article-toc' );
			
			// show TOC only if headers are present
			if ( articleHeaders.length > 0 ) {
				$articleTocLocal.html( this.getToCHTML( articleHeaders ) );

				// Add h2 title for Article content section
				if( $('#eckb-article-content .eckb-article-toc').length > 0 ) {
					
					$('#eckb-article-content .eckb-article-toc').html( this.getToCHTML( articleHeaders, 'h2' ) );
				}

			} else {
				$articleTocLocal.hide();

				//FOR FE Editor ONLY
				if ($('body').hasClass('epkb-editor-preview')) {
					$articleTocLocal.show();
					let title = $articleTocLocal.find('.eckb-article-toc__title').html();
					let html = `
						<div class="eckb-article-toc__inner">
							<h2 class="eckb-article-toc__title">${title}</h2>
							<nav class="eckb-article-toc-outline" role="navigation" aria-label="${epkb_vars.toc_aria_label}">
							<ul>
								<li>${epkb_vars.toc_editor_msg}</li>
							</ul>
							</nav>
							</div>
						</div>	
						`;
					$articleTocLocal.html( html );
				}
				
			}
			
			
			// inside TOC.init(), after you render the TOC HTML:
			$('.eckb-article-toc__level a')
			.off('click.eckbTOC')                   // remove our previous handler only
			.on('click.eckbTOC', function (e) {     // add it back once
			if ( $('.epkb-editor-preview').length ) {
				e.preventDefault();
				return;
			}

			let target = $(this).data('target');
			if ( !target || $('[data-id=' + target + ']').length === 0 ) {
				return false;
			}

			let current_scroll_top = $('[data-id=' + target + ']').offset().top - TOC.offset;
			let animate_speed = parseInt($(this).closest('.eckb-article-toc').data('speed'));
			$('body, html').animate({ scrollTop: current_scroll_top }, animate_speed);
			return false;
			});
			
			this.scrollSpy();
			
			// scroll to element if it is in the hash 
			if ( ! location.hash ) {
				return;
			}
			
			let hash_link = $('[data-target=' + location.hash.slice(1) + ']' );
			if ( hash_link.length ) {
				hash_link.trigger( 'click' );
			}
		},
		
		getOptions: function() {
			let $articleTocLocal = $( '.eckb-article-toc' );
			
			if ( $articleTocLocal.data( 'min' ) ) {
				this.firstLevel = $articleTocLocal.data( 'min' );
			}
			
			if ( $articleTocLocal.data( 'max' ) ) {
				this.lastLevel = $articleTocLocal.data( 'max' );
			}
			
			if ( $articleTocLocal.data( 'offset' ) ) {
				this.offset = $articleTocLocal.data( 'offset' );
			} else {
				$articleTocLocal.data( 'offset', this.offset )
			}

			
			if ( typeof $articleTocLocal.data('exclude_class') !== 'undefined' ) {
				this.excludeClass = $articleTocLocal.data('exclude_class');
			}

			this.searchStr = '';
			while ( this.firstLevel <= this.lastLevel ) {
				this.searchStr += 'h' + this.firstLevel + ( this.firstLevel < this.lastLevel ? ',' : '' );
				this.firstLevel++;
			}
		},
		
		// return object with headers and their ids 
		getArticleHeaders: function () {
			let headers = [];
			let that = this;
			
			$( '#eckb-article-content-body' ).find( that.searchStr ).each( function(){
					
				if ( $(this).text().length === 0 ) {
					return;
				}
					
				if ( that.excludeClass && $(this).hasClass( that.excludeClass ) ) {
					return;
				}
					
				let tid;
				let header = {};
						
				if ( $(this).data( 'id' ) ) {
					tid = $(this).data( 'id' );
				} else {
					tid = 'articleTOC_' + headers.length;
					$(this).attr( 'data-id', tid );
				}

				header.id = tid;
				header.title = $(this).text();
						
				if ('H1' === $(this).prop("tagName")) {
					header.level = 1;
				} else if ('H2' === $(this).prop("tagName")) {
					header.level = 2;
				} else if ('H3' === $(this).prop("tagName")) {
					header.level = 3;
				} else if ('H4' === $(this).prop("tagName")) {
					header.level = 4;
				} else if ('H5' === $(this).prop("tagName")) {
					header.level = 5;
				} else if ('H6' === $(this).prop("tagName")) {
					header.level = 6;
				}
					
				headers.push(header);
				
			});
				
			if ( headers.length === 0 ) {
				return headers;
			}
				
			// find max and min header level 
			let maxH = 1;
			let minH = 6;
				
			headers.forEach(function(header){
				if (header.level > maxH) {
					maxH = header.level
				}
					
				if (header.level < minH) {
					minH = header.level
				}
			});
				
			// move down all levels to have 1 lowest 
			if ( minH > 1 ) {
				headers.forEach(function(header, i){
					headers[i].level = header.level - minH + 1;
				});
			}
				
			// now we have levels started from 1 but maybe some levels do not exist
			// check level exist and decrease if not exist 
			let i = 1;
				
			while ( i < maxH ) {
				let levelExist = false;
				headers.forEach( function( header ) {
					if ( header.level == i ) {
						levelExist = true;
					}
				});
					
				if ( levelExist ) {
					// all right, level exist, go to the next 
					i++;
				} else {
					// no such levelm move all levels that more than current down and check once more
					headers.forEach( function( header, j ) {
						if ( header.level > i ) {
							headers[j].level = header.level - 1;
						}
					});
				}
				
				i++;
			}
				
			return headers;
		},
		
		// return html from headers object 
		getToCHTML: function ( headers, titleTag='h2' ) {
			let html;
			let $articleTocLocal = $( '.eckb-article-toc' );
				
			if ( $articleTocLocal.find('.eckb-article-toc__title').length ) {
					
				let title = $articleTocLocal.find('.eckb-article-toc__title').html();
				html = `
					<div class="eckb-article-toc__inner">
						<${titleTag} class="eckb-article-toc__title">${title}</${titleTag}>
						<nav class="eckb-article-toc-outline" role="navigation" aria-label="${epkb_vars.toc_aria_label}">
						<ul>
					`;
					
			} else {
					
				html = `
					<div class="eckb-article-toc__inner">
						<ul>
					`;
			}

			headers.forEach( function( header ) {
				let url = new URL( location.href );
				url.hash = header.id;
				url = url.toString();
				html += `<li class="eckb-article-toc__level eckb-article-toc__level-${header.level}"><a href="${url}" data-target="${header.id}">${header.title}</a></li>`;
			});
				
			html += `
						</ul>
						</nav>
					</div>
				`;
				
			return html;
		},
		
		// highlight needed element
		scrollSpy: function () {

			// No selection if page does not have scroll
			if ( $( document ).height() <= $( window ).height() ) {
				$( '.eckb-article-toc__level a' ).removeClass( 'active' );
				return;
			}

			let $articleTocLocal = $( '.eckb-article-toc' );
			let currentTop = $(window).scrollTop();
			let currentBottom = $(window).scrollTop() + $(window).height();
			let highlighted = false;
			let $highlightedEl = false;
			let offset = $articleTocLocal.data( 'offset' );

			// scrolled to the end, activate last el
			if ( currentBottom == $(document).height() ) {
				highlighted = true;
				$highlightedEl = $('.eckb-article-toc__level a').last();
				$('.eckb-article-toc__level a').removeClass('active');
				$highlightedEl.addClass('active');
			// at least less than 1 px from the end
			} else {

				$('.eckb-article-toc__level a').each( function ( index ) {

					$(this).removeClass('active');

					if ( highlighted ) {
						return true;
					}

					let target = $(this).data('target');

					if ( !target || $('[data-id=' + target + ']').length === 0 ) {
						return true;
					}

					let $targetEl = $('[data-id=' + target + ']');
					let elTop = $targetEl.offset().top;
					let elBottom = $targetEl.offset().top + $targetEl.height();

					// check if we have last element
					if ( ( index + 1 ) === $('.eckb-article-toc__level a').length ) {
						elBottom = $targetEl.parent().offset().top + $targetEl.parent().height();
					} else {
						let nextTarget = $('.eckb-article-toc__level a').eq( index + 1 ).data('target');

						if ( nextTarget && $('[data-id=' + nextTarget + ']').length ) {
							elBottom = $('[data-id=' + nextTarget + ']').offset().top;
						}
					}

					elTop -= offset;
					elBottom -= offset + 1;

					let elOnScreen = false;

					if ( elTop < currentBottom && elTop > currentTop ) {
						// top corner inside the screen
						elOnScreen = true;
					} else if ( elBottom < currentBottom && elBottom > currentTop ) {
						// bottom corner inside the screen
						elOnScreen = true;
					} else if ( elTop < currentTop && elBottom > currentBottom ) {
						// screen inside the block
						elOnScreen = true;
					}

					if ( elOnScreen ) {
						$(this).addClass('active');
						highlighted = true;
						$highlightedEl = $(this);
					}

				});
			}

			// check if the highlighted element is visible 
			if ( ! $highlightedEl || $highlightedEl.length === 0 || ! highlighted ){
				return;
			}
			
			let highlightPosition = $highlightedEl.position().top;
			
			if ( highlightPosition < 0 || highlightPosition > $highlightedEl.closest('.eckb-article-toc__inner').height() ) {
				$highlightedEl.closest('.eckb-article-toc__inner').scrollTop( highlightPosition - $highlightedEl.closest('.eckb-article-toc__inner').find( '.eckb-article-toc__title' ).position().top );
			}
		},
	};
	function mobile_TOC() {
		let window_width = $(window).width();
		let mobile_breakpoint = typeof $('#eckb-article-page-container-v2').data('mobile_breakpoint') == "undefined" ? 111 : $('#eckb-article-page-container-v2').data('mobile_breakpoint');

		if ( window_width > mobile_breakpoint ) {
			return;
		}

		if ( $('#eckb-article-content-header-v2 .eckb-article-toc').length ) {
			return;
		}

		if ( $('#eckb-article-left-sidebar .eckb-article-toc').length ) {
			$('#eckb-article-content-header-v2').append($('#eckb-article-left-sidebar .eckb-article-toc'));
			return;
		}

		if ( $('#eckb-article-right-sidebar .eckb-article-toc').length ) {
			$('#eckb-article-content-header-v2').append($('#eckb-article-right-sidebar .eckb-article-toc'));
		}
	}
	function initialize_toc() {
		let $articleTocLocal = $( '.eckb-article-toc' );

		if ( $articleTocLocal.length ) {
			TOC.init();
		}

		// Get the Article Content Body Position
		let articleContentBodyPosition = $('#eckb-article-content-body' ).position();
		let window_width = $(window).width();
		let default_mobile_breakpoint = 768 // This is the default set on first installation.
		let mobile_breakpoint = typeof $('#eckb-article-page-container-v2').data('mobile_breakpoint') == "undefined" ? default_mobile_breakpoint : $('#eckb-article-page-container-v2').data('mobile_breakpoint');

		// If the setting is on, Offset the Sidebar to match the article Content
		if( $('.eckb-article-page--L-sidebar-to-content').length > 0 && window_width > mobile_breakpoint ){
			$('#eckb-article-page-container-v2').find( '#eckb-article-left-sidebar ').css( "margin-top" , articleContentBodyPosition.top+'px' );
		}

		if( $('.eckb-article-page--R-sidebar-to-content').length > 0 && window_width > mobile_breakpoint ){
			$('#eckb-article-page-container-v2').find( '#eckb-article-right-sidebar ').css( "margin-top" , articleContentBodyPosition.top+'px' );
		}

		if ( $articleTocLocal.length ) {
			mobile_TOC();
		}
	}
	// If Article Page exits
	if( articlePage.length > 0 ) {
		console.log('=== Article Page ===');

		// Print Button ----------------------------------/
		$('body').on("click keydown", ".eckb-print-button-container, .eckb-print-button-meta-container", function(event) {

			// Only proceed if it's a click or Enter/Space key
			if ( event.type === 'keydown' && event.keyCode !== 13 && event.keyCode !== 32 ) {
				return;
			}

			if ( event.type === 'keydown' ) {
				event.preventDefault();
			}
		
			if ( $('body').hasClass('epkb-editor-preview') ) {
				return;
			}
			
			$('#eckb-article-content').parents().each(function(){
				$(this).siblings().addClass('eckb-print-hidden');
			});
			
			window.print();
		});

	
		setTimeout(function () {
			initialize_toc();
		  
			// prevent duplicates and make unbinding easy
			$(window).off('scroll.eckbTOCspy resize.eckbTOCspy');
			$(window).on('scroll.eckbTOCspy', TOC.scrollSpy);
			$(window).on('resize.eckbTOCspy', TOC.scrollSpy);
		  }, 500);

		// Article Views Counter ----------------------------------/
		function epkb_send_article_view() {

			// check if we on article page
			if ( $('#eckb-article-content').length === 0 ) {
				return;
			}

			let article_id = $('#eckb-article-content').data('article-id');

			if ( typeof article_id == undefined || article_id == '' || typeof epkb_vars
				.article_views_counter_method == undefined || epkb_vars
				.article_views_counter_method == '' ) {
				return;
			}

			// check method for article views counter
			if ( epkb_vars
				.article_views_counter_method === 'delay' ) {
				setTimeout( function() {
					epkb_send_article_view_ajax( article_id );
				}, 5000 );
			}

			if ( epkb_vars
				.article_views_counter_method === 'scroll' ) {
				$(window).one( 'scroll', function() {
					epkb_send_article_view_ajax( article_id );
				});
			}
		}
		epkb_send_article_view();

		function epkb_send_article_view_ajax( article_id ) {
			// prevent double sent ajax request
			if ( typeof epkb_vars.article_view_sent !== 'undefined' ) {
				return;
			}

			let postData = {
				action: 'epkb_count_article_view',
				article_id: article_id,
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			};

			// don't need response
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: postData,
				url: epkb_vars.ajaxurl,
				beforeSend: function( xhr ) {
					epkb_vars.article_view_sent = true;
				}
			});
		}


		
		$( window ).on( 'resize', function() {
			epkb_send_article_view();
			initialize_toc();
		} );


	} // if( articlePage.length > 0 )


	/********************************************************************
	 *                      Category Archive Page
	 ********************************************************************/

	let categoryArchivePage = $( '#eckb-archive-content' );
	if( categoryArchivePage.length > 0 ) {
		console.log('=== Category Archive Page ===');
		
		$( document ).on( 'click', '.eckb-article-list-show-more-container', function() {

			$( this ).parent().find( '.eckb-article-container' ).removeClass( 'epkb-hide-elem' );
			$( '.eckb-article-list-show-more-container' ).hide();
		});
	

	}


	/********************************************************************
	 *                     Create Demo Data 
	 ********************************************************************/

    let createDemoData = $( '.eckb-kb-no-content' );
	if( createDemoData.length > 0 ) {
		console.log('=== Create Demo Data ===');

		$( document ).on( 'click', '#eckb-kb-create-demo-data', function( e ) {
			e.preventDefault();
	
			// Do nothing on Editor preview mode
			if ( $( this ).closest( '.epkb-editor-preview, .eckb-block-editor-preview' ).length ) {
				return;
			}
	
			let postData = {
				action: 'epkb_create_kb_demo_data',
				epkb_kb_id: $( this ).data( 'id' ),
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			};
	
			let parent_container = $( this ).closest( '.eckb-kb-no-content' ),
				confirmation_box = $( '.eckb-kb-no-content' ).find( '#epkb-created-kb-content' );
	
			let loading_dialog_message = epkb_vars.creating_demo_data ? epkb_vars.creating_demo_data : '';
	
			$.ajax( {
				type: 'POST',
				dataType: 'json',
				data: postData,
				url: epkb_vars.ajaxurl,
				beforeSend: function( xhr ) {
					epkb_loading_Dialog( 'show', loading_dialog_message, parent_container );
				}
	
			} ).done( function( response ) {
				response = ( response ? response : '' );
				if ( typeof response.message !== 'undefined' ) {
					confirmation_box.addClass( 'epkb-dialog-box-form--active' );
				}
	
			} ).fail( function( response, textStatus, error ) {
							confirmation_box.addClass( 'epkb-dialog-box-form--active' ).find( '.epkb-dbf__body' ).html( error );
	
			} ).always( function() {
				epkb_loading_Dialog( 'remove', '', parent_container );
			} );
		});
	
		function epkb_loading_Dialog( displayType, message, parent_container ){
	
			if ( displayType === 'show' ) {
	
				let output =
					'<div class="epkb-admin-dialog-box-loading">' +
	
					//<-- Header -->
					'<div class="epkb-admin-dbl__header">' +
					'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
					(message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
					'</div>'+
	
					'</div>' +
					'<div class="epkb-admin-dialog-box-overlay"></div>';
	
				//Add message output at the end of Body Tag
				parent_container.append( output );
	
			} else if( displayType === 'remove' ) {
	
				// Remove loading dialogs.
				parent_container.find( '.epkb-admin-dialog-box-loading' ).remove();
				parent_container.find( '.epkb-admin-dialog-box-overlay' ).remove();
			}
		}
	
		$( document ).on( 'click', '.eckb-kb-no-content #epkb-created-kb-content .epkb-dbf__footer__accept__btn', function() {
			location.reload();
		} );
	}

	
	/********************************************************************
	 *                      Sidebar v2
	 ********************************************************************/

	if( $( '#elay-sidebar-container-v2' ).length === 0 && $( '#epkb-sidebar-container-v2' ).length > 0 ){

		console.log('=== Sidebar v2 ===');

		function init_elay_sidebar_v2() {
			

				function epkb_toggle_category_icons( icon, icon_name ) {

					let icons_closed = [ 'ep_font_icon_plus', 'ep_font_icon_plus_box', 'ep_font_icon_right_arrow', 'ep_font_icon_arrow_carrot_right', 'ep_font_icon_arrow_carrot_right_circle', 'ep_font_icon_folder_add' ];
					let icons_opened = [ 'ep_font_icon_minus', 'ep_font_icon_minus_box', 'ep_font_icon_down_arrow', 'ep_font_icon_arrow_carrot_down', 'ep_font_icon_arrow_carrot_down_circle', 'ep_font_icon_folder_open' ];

					let index_closed = icons_closed.indexOf( icon_name );
					let index_opened = icons_opened.indexOf( icon_name );

					if ( index_closed >= 0 ) {
						icon.removeClass( icons_closed[index_closed] );
						icon.addClass( icons_opened[index_closed] );
					} else if ( index_opened >= 0 ) {
						icon.removeClass( icons_opened[index_opened] );
						icon.addClass( icons_closed[index_opened] );
					}
				}

				function epkb_open_and_highlight_selected_article_v2() {

					let $el = $( '#eckb-article-content' );

					if ( typeof $el.data( 'article-id' ) === 'undefined' ) {
						return;
					}

					// active article id
					let id = $el.data( 'article-id' );

					// true if we have article with multiple categories (locations) in the SBL; ignore old links
					if ( typeof $el.data('kb_article_seq_no') !== 'undefined' && $el.data('kb_article_seq_no') > 0 ) {
						let new_id = id + '_' + $el.data('kb_article_seq_no');
						id = $('#sidebar_link_' + new_id).length > 0 ? new_id : id;
					}

					// after refresh highlight the Article link that is now active
					$('.epkb-sidebar__cat__top-cat li').removeClass( 'active' );
					$('.epkb-category-level-1').removeClass( 'active' );
					$('.epkb-category-level-2-3').removeClass( 'active' );
					$('.epkb-sidebar__cat__top-cat__heading-container').removeClass( 'active' );
					let $sidebar_link = $('#sidebar_link_' + id);
					$sidebar_link.addClass('active');

					// open all subcategories
					$sidebar_link.parents('.epkb-sub-sub-category, .epkb-articles').each(function(){

						let $button = $(this).parent().children('.epkb-category-level-2-3');
						if ( ! $button.length ) {
							return true;
						}

						if ( ! $button.hasClass('epkb-category-level-2-3') ) {
							return true;
						}

						$button.next().show();
						$button.next().next().show();

						let icon = $button.find('.epkb_sidebar_expand_category_icon');
						if ( icon.length > 0 ) {
							epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
						}
					});

					// open main accordion
					$sidebar_link.closest('.epkb-sidebar__cat__top-cat').parent().toggleClass( 'epkb-active-top-category' );
					$sidebar_link.closest('.epkb-sidebar__cat__top-cat').find( $( '.epkb-sidebar__cat__top-cat__body-container') ).show();

					let icon = $sidebar_link.closest('.epkb-sidebar__cat__top-cat').find('.epkb-sidebar__cat__top-cat__heading-container .epkb-sidebar__heading__inner span');
					if ( icon.length > 0 ) {
						epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
					}
				}

				function epkb_open_current_archive_category() {
					let $current_cat = $( '.epkb-sidebar__cat__current-cat' );
					if ( ! $current_cat.length ) {
						return;
					}

					// expand parent if chosen category is hidden
					let list = $current_cat.closest( 'li' );
					for ( let i = 0; i < 5; i ++ ) {
						if ( ! list.length ) {
							continue;
						}
						// open the top category here
						if ( list.hasClass( 'epkb-sidebar__cat__top-cat' ) ) {
							list.find( '.epkb-sidebar__cat__top-cat__body-container' ).css( 'display', 'block' );
							list.closest( '.epkb-sidebar__cat__top-cat__body-container' ).css( 'display', 'block' );
						}
						list.children( 'ul' ).show();
						list = list.closest( 'li' ).closest( 'ul' ).parent();
					}

					// highlight categories
					let level = $current_cat.closest( 'li' );
					let level_icon;
					for ( let i = 0; i < 5; i ++ ) {
						level_icon = level.find( 'span' ).first();
						level = level_icon.closest( 'ul' ).closest( 'ul' ).closest( 'li' );
						if ( level_icon.length ) {
							let match_icon = level_icon.attr('class').match(/\ep_font_icon_\S+/g);
							if ( match_icon ) {
								epkb_toggle_category_icons( level_icon, match_icon[0] );
							}
						}
						level.find( 'div[class^=elay-category]' ).first().addClass( 'active' );

						// open the top category here
						if ( i === 0 ) {
							level.find( '.epkb-sidebar__cat__top-cat__body-container' ).css( 'display', 'block' );
							level.closest( '.epkb-sidebar__cat__top-cat__body-container' ).css( 'display', 'block' );
						}
					}
				}

				let sidebarV2 = $('#epkb-sidebar-container-v2');

				// TOP-CATEGORIES -----------------------------------/
				// Show or hide article in sliding motion
				// WCAG Accessibility: Added keydown for Enter/Space support
				sidebarV2
				.off('click.epkbTopCat keydown.epkbTopCat')   // remove any previous bindings
				.on('click.epkbTopCat keydown.epkbTopCat',
					'.epkb-top-class-collapse-on, .epkb-sidebar__cat__top-cat__heading-container',
					function (e) {

					// Only proceed if click or Enter/Space key
					if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
						return;
					}

					/* 1. Ignore clicks coming from the block-editor tabs */
					if ($(e.target).closest('.epkb-editor-zone__tab--active, .epkb-editor-zone__tab--parent').length) {
						return;
					}

					/* 2. Toggle body + active class */
					const $heading = $(this);
					const $topCat  = $heading.parent();                       // <li> (or wrapper)
					const $body    = $topCat.children('.epkb-sidebar__cat__top-cat__body-container');

					$topCat.toggleClass('epkb-active-top-category');
					$body.stop(true, true).slideToggle();                     // kill queued anims

					/* 3. Flip caret / plus-minus icon */
					const $icon = $heading.find('span[class*="ep_font_icon_"]');
					if ($icon.length) {
						const cls = ($icon.attr('class').match(/ep_font_icon_\S+/) || [''])[0];
						epkb_toggle_category_icons($icon, cls);
					}

					/* 4. WCAG Accessibility: Toggle aria-expanded */
					let isExpanded = $heading.attr( 'aria-expanded' ) === 'true';
					$heading.attr( 'aria-expanded', isExpanded ? 'false' : 'true' );

					e.preventDefault();                                       // no unwanted nav
				});


				// SUB-CATEGORIES -----------------------------------/
				// Show or hide article in sliding motion
				// WCAG Accessibility: Added keydown for Enter/Space support
				sidebarV2
				.off('click.epkbToggleCat keydown.epkbToggleCat')                 // ← remove any earlier copy
				.on('click.epkbToggleCat keydown.epkbToggleCat', '.epkb-category-level-2-3', function (e) {

				// Only proceed if click or Enter/Space key
				if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
					return;
				}
			
				/* ─── 1. toggle the sibling <ul>s ───────────────────────────── */
				$(this)
					.nextUntil(':not(ul)', 'ul')            // every UL that follows the header
					.stop(true, true)                       // kill queued animations
					.slideToggle();
			
				/* ─── 2. flip the arrow / plus-minus icon ──────────────────── */
				const $icon = $(this).children('.epkb_sidebar_expand_category_icon');
				if ($icon.length) {
					const iconClass = ($icon.attr('class').match(/ep_font_icon_\S+/) || [''])[0];
					epkb_toggle_category_icons($icon, iconClass);
				}

				/* ─── 3. WCAG Accessibility: Toggle aria-expanded ──────────── */
				let isExpanded = $( this ).attr( 'aria-expanded' ) === 'true';
				$( this ).attr( 'aria-expanded', isExpanded ? 'false' : 'true' );
			
				e.preventDefault();                       // no unwanted navigation
				});

				// SHOW ALL articles functionality
				sidebarV2
				.off('click.epkbShowAll keydown.epkbShowAll')   // remove any previous bindings
				.on('click.epkbShowAll keydown.epkbShowAll', '.epkb-show-all-articles', function ( e ) {

					// Only proceed if it's a click or Enter/Space key
					if ( e.type === 'keydown' && e.keyCode !== 13 && e.keyCode !== 32 ) {
						return;
					}

					if ( e.type === 'keydown' ) {
						e.preventDefault();
					}

					$( this ).toggleClass( 'active' );
					let parent = $( this ).parent( 'ul' );
					let article = parent.find( 'li');
					let isExpanding = $(this).hasClass( 'active' );
					let firstRevealedArticle = null;

					//If this has class "active" then change the text to Hide extra articles
					if ( isExpanding ) {

						//If Active
						$(this).find('.epkb-show-text').addClass('epkb-hide-elem');
						$(this).find('.epkb-hide-text').removeClass('epkb-hide-elem');
						$(this).attr( 'aria-expanded','true' );

					} else {
						//If not Active
						$(this).find('.epkb-show-text').removeClass('epkb-hide-elem');
						$(this).find('.epkb-hide-text').addClass('epkb-hide-elem');
						$(this).attr( 'aria-expanded','false' );
					}

					$( article ).each(function() {
						//If has class "hide" remove it and replace it with class "Visible"
						if ( $(this).hasClass( 'epkb-hide-elem') ) {
							$(this).removeClass('epkb-hide-elem');
							$(this).addClass('visible');

							// Track the first revealed article for focus management (WCAG Accessibility)
							if ( firstRevealedArticle === null ) {
								firstRevealedArticle = $(this);
							}
						} else if ( $(this).hasClass( 'visible')) {
							$(this).removeClass('visible');
							$(this).addClass('epkb-hide-elem');
						}
					});

					// WCAG Accessibility: Move focus to the first revealed article when expanding
					if ( isExpanding && firstRevealedArticle !== null ) {
						let firstLink = firstRevealedArticle.find( 'a' ).first();
						if ( firstLink.length ) {
							firstLink.focus();
						}
					}
				});

				epkb_open_and_highlight_selected_article_v2();
				epkb_open_current_archive_category();
			
		}
		init_elay_sidebar_v2();

		$( window ).on( 'resize', function() {

			init_elay_sidebar_v2();
		
		} );

	} // if( $( '#elay-sidebar-container-v2' ).length === 0 && $( '#epkb-sidebar-container-v2' ).length > 0 )

	
	/********************************************************************
	 *                      Focus Debug
	 ********************************************************************/

	function enableFocusDebug() {

		// Track last key was shift+tab
		window._lastKeyWasShiftTab = false;
	
		window.handleKeydown = function (e) {
			if ( e.key === 'Tab' ) {
				window._lastKeyWasShiftTab = e.shiftKey;
			}
		};
	
		window.handleFocusin = function (e) {
			console.log( 'Focused element:', e.target );
	
			// Clear previous outlines
			document.querySelectorAll('[style*="outline"]').forEach(function (el) {
				el.style.outline = '';
			});
	
			// Apply red for Tab, blue for Shift+Tab
			e.target.style.outline = window._lastKeyWasShiftTab ? '3px solid blue' : '3px solid red';
		};
	
		// Now it's safe to remove
		document.removeEventListener( 'keydown', window.handleKeydown );
		document.removeEventListener( 'focusin', window.handleFocusin );
	
		document.querySelectorAll('[style*="outline"]').forEach(function (el) {
			el.style.outline = '';
		});
	
		document.addEventListener( 'keydown', window.handleKeydown );
		document.addEventListener( 'focusin', window.handleFocusin );
	}

	//enableFocusDebug();


	/********************************************************************
	 *                   None Module Mode legacy layout
	 ********************************************************************/

	let search_text = $( '#epkb-search-kb' ).text();
	$( '#epkb-search-kb' ).text( search_text );
	
	// Legacy Search
	$( 'body' ).on( 'submit', '#epkb_search_form', function( e ) {
		e.preventDefault();  // do not submit the form

		if ( $('#epkb_search_terms').val() === '' ) {
			return;
		}

		let postData = {
			action: 'epkb-search-kb',
			epkb_kb_id: $('#epkb_kb_id').val(),
			search_words: $('#epkb_search_terms').val(),
			is_kb_main_page: $('.eckb_search_on_main_page').length
		};

		let msg = '';

		$.ajax({
			type: 'GET',
			dataType: 'json',
			data: postData,
			url: epkb_vars.ajaxurl,
			beforeSend: function (xhr)
			{
				//Loading Spinner
				$( '.loading-spinner').css( 'display','block');
				$('#epkb-ajax-in-progress').show();
			}

		}).done(function (response)
		{
			response = ( response ? response : '' );

			//Hide Spinner
			$( '.loading-spinner').css( 'display','none');

			if ( response.error || response.status !== 'success') {
				//noinspection JSUnresolvedVariable
				msg = epkb_vars.msg_try_again;
			} else {
				msg = response.search_result;
			}

		}).fail(function (response, textStatus, error)
		{
			//noinspection JSUnresolvedVariable
			msg = epkb_vars.msg_try_again + '. [' + ( error ? error : epkb_vars.unknown_error ) + ']';

		}).always(function ()
		{
			$('#epkb-ajax-in-progress').hide();

			if ( msg ) {
				$( '#epkb_search_results' ).css( 'display','block' );
				$( '#epkb_search_results' ).html( msg );

			}

		});
	});

	// Legacy Search
	$( document ).on( 'keyup', "#epkb_search_terms", function() {
		if (!this.value) {
			$('#epkb_search_results').css( 'display','none' );
		}
	});

});
