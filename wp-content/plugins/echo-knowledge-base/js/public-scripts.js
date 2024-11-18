jQuery(document).ready(function($) {

	/* Variables -----------------------------------------------------------------*/

	let knowledgebase;
	// If Module Layout is active
	if ( $( '#epkb-modular-main-page-container' ).length ) {
		knowledgebase = $( '#epkb-modular-main-page-container' );
	} else if ( $( '.epkb-block-main-page-container' ).length ) {
		knowledgebase = $( '.epkb-block-main-page-container' );
	} else {
		// Use Legacy Layouts as a fallback if the modular Main Page doesn't exist
		knowledgebase = $( '#epkb-main-page-container' );
	}
	let tabContainer = $('#epkb-content-container');
	let navTabsLi    = $('.epkb-nav-tabs li');
	let tabPanel     = $('.epkb-tab-panel');
	let articleContent = $('#eckb-article-content-body');
	let articleToc     = $('.eckb-article-toc');

	/********************************************************************
	 *                      Search
	 ********************************************************************/

	// handle KB search form
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

	$("#epkb_search_terms").on( 'keyup', function() {
		if (!this.value) {
			$('#epkb_search_results').css( 'display','none' );
		}
	});

	/********************************************************************
	 *                      Module Search
	 ********************************************************************/

	// handle KB search form
	$( 'body' ).on( 'submit', '#epkb-ml-search-form', function( e ) {
		e.preventDefault();  // do not submit the form

		if ( $( this ).closest( '.eckb-block-editor-preview' ).length ) {
			return;
		}

		if ( $( '.epkb-ml-search-box__input' ).val() === '' ) {
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
	$( ".epkb-ml-search-box__input" ).on( 'keyup', function() {
		if ( !this.value ) {
			$( '#epkb-ml-search-results' ).css( 'display','none' );
		}
	});

	/********************************************************************
	 *                      Tabs / Mobile Select
	 ********************************************************************/

	//Get the highest height of Tab and make all other tabs the same height
	if ( tabContainer.length && navTabsLi.length ){
		let tallestHeight = 0;
		tabContainer.find( navTabsLi ).each( function(){
			let this_element = $(this).outerHeight( true );
			if( this_element > tallestHeight ) {
				tallestHeight = this_element;
			}
		});
		tabContainer.find( navTabsLi ).css( 'min-height', tallestHeight );
	}

	function changePanels( Index ){
		$('.epkb-panel-container .epkb-tab-panel:nth-child(' + (Index + 1) + ')').addClass('active');
	}

	function updateTabURL( tab_id, tab_name ) {
		let location = window.location.href;
		location = update_query_string_parameter(location, 'top-category', tab_name);
		window.history.pushState({"tab":tab_id}, "title", location);
		// http://stackoverflow.com/questions/32828160/appending-parameter-to-url-without-refresh
	}

	window.onpopstate = function(e){

		if ( e.state && e.state.tab.indexOf('epkb_tab_') !== -1) {
			//document.title = e.state.pageTitle;

			// hide old section
			tabContainer.find('.epkb_top_panel').removeClass('active');

			// re-set tab; true if mobile drop-down
			if ( $( "#main-category-selection" ).length > 0 )
			{
				$("#main-category-selection").val(tabContainer.find('#' + e.state.tab).val());
			} else {
				tabContainer.find('.epkb_top_categories').removeClass('active');
				tabContainer.find('#' + e.state.tab).addClass('active');
			}

			tabContainer.find('.' + e.state.tab).addClass('active');

		// if user tabs back to the initial state, select the first tab if not selected already
		} else if ( $('#epkb_tab_1').length > 0 && ! tabContainer.find('#epkb_tab_1').hasClass('active') ) {

			// hide old section
			tabContainer.find('.epkb_top_panel').removeClass('active');

			// re-set tab; true if mobile drop-down
			if ( $( "#main-category-selection" ).length > 0 )
			{
				$("#main-category-selection").val(tabContainer.find('#epkb_tab_1').val());
			} else {
				tabContainer.find('.epkb_top_categories').removeClass('active');
				tabContainer.find('#epkb_tab_1').addClass('active');
			}

			tabContainer.find('.epkb_tab_1').addClass('active');
		}
	};

	// Tabs Layout: switch to the top category user clicked on
	tabContainer.find( navTabsLi ).each(function(){

		$(this).on('click', function (){
			tabContainer.find( navTabsLi ).removeClass('active');

			$(this).addClass('active');

			tabContainer.find(tabPanel).removeClass('active');
			changePanels ( $(this).index() );
			updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
		});
	});

	// Tabs Layout: MOBILE: switch to the top category user selected
	$( document ).on( 'change', "#main-category-selection", function() {
		$('#epkb-content-container').find('.epkb-tab-panel').removeClass('active');
		// drop down
		$( "#main-category-selection option:selected" ).each(function() {
			let selected_index = $( this ).index();
			changePanels ( selected_index );
			if ( ! $( this ).closest( '.eckb-block-editor-preview' ).length ) {
				updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
			}
		});
	});

	// Tabs Layout: Level 1 Show more if articles are assigned to top categories
	$( '#epkb-ml-tabs-layout .epkb-ml-articles-show-more' ).on( 'click',function( e ) {
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


	/********************************************************************
	 *                      Categories
	 ********************************************************************/

	//Detect if a div is inside a list item then it's a sub category
	$('.epkb-section-body .epkb-category-level-2-3').each(function(){

		$( this ).on('click', function(){

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
	});

	/**
	 * Sub Category icon toggle
	 *
	 * Toggle between open icon and close icon
	 * Accessibility: Set aria-expand values
	 */
	tabContainer.find('.epkb-section-body .epkb-category-level-2-3').each(function(){

		if( $(this).hasClass( 'epkb-category-focused' ) ) {
			return;
		}

		let $icon = $(this).find('.epkb-category-level-2-3__cat-icon');

		$(this).on('click', function (){

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
	});

	/**
	 * Show all articles functionality
	 *
	 * When user clicks on the "Show all articles" it will toggle the "hide" class on all hidden articles
	 */
	knowledgebase.find('.epkb-show-all-articles').on( 'click', function () {

		$( this ).toggleClass( 'epkb-show-articles' );
		let categoryId = $( this ).data('btn-id');
		let article = $( '[data-list-id="' + categoryId + '"]' ).find( 'li' );

		//If this has class "active" then change the text to Hide extra articles
		if ( $( this ).hasClass( 'epkb-show-articles') ) {

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
			}else if ( $(this).hasClass( 'visible')) {
				$(this).removeClass('visible');
				$(this).addClass('epkb-hide-elem');
			}
		});
	});
	
	let search_text = $( '#epkb-search-kb' ).text();
	$( '#epkb-search-kb' ).text( search_text );


	/********************************************************************
	 *                      Article Print 
	 ********************************************************************/
	$('body').on("click", ".eckb-print-button-container, .eckb-print-button-meta-container", function(event) {
		
		if ( $('body').hasClass('epkb-editor-preview') ) {
			return;
		}
		
		$('#eckb-article-content').parents().each(function(){
			$(this).siblings().addClass('eckb-print-hidden');
		});
		
		window.print();
	});


	/********************************************************************
	 *                      Article TOC v2
	 ********************************************************************/
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
			
			// show TOC only if headers are present
			if ( articleHeaders.length > 0 ) {
				
				articleToc.html( this.getToCHTML( articleHeaders ) );

				// Add h2 title for Article content section
				if( $('#eckb-article-content .eckb-article-toc').length > 0 ) {
					
					$('#eckb-article-content .eckb-article-toc').html( this.getToCHTML( articleHeaders, 'h2' ) );
				}

			} else {
				articleToc.hide();

				//FOR FE Editor ONLY
				if ($('body').hasClass('epkb-editor-preview')) {
					articleToc.show();
					let title = articleToc.find('.eckb-article-toc__title').html();
					let html = `
						<div class="eckb-article-toc__inner">
							<h4 class="eckb-article-toc__title">${title}</h4>
							<nav class="eckb-article-toc-outline" role="navigation" aria-label="${epkb_vars.toc_aria_label}">
							<ul>
								<li>${epkb_vars.toc_editor_msg}</li>
							</ul>
							</nav>
							</div>
						</div>	
						`;
					articleToc.html( html );
				}
				
			}
			
			let that = this;
			
			$('.eckb-article-toc__level a').on('click', function( e ){
				
				if ( $('.epkb-editor-preview').length ) {
					e.preventDefault();
					return;
				}
				
				let target = $(this).data('target');
				
				if ( ! target || $( '[data-id=' + target + ']' ).length === 0 ) {
					return false;
				}

				// calculate the speed of animation
				let current_scroll_top = $( '[data-id=' + target + ']').offset().top - that.offset;
				let animate_speed =  parseInt($(this).closest('.eckb-article-toc').data('speed'));

				$('body, html').animate({ scrollTop: current_scroll_top }, animate_speed);
				
				return false;
			});
			
			$(window).on( 'scroll', this.scrollSpy );
			
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
			
			if ( articleToc.data( 'min' ) ) {
				this.firstLevel = articleToc.data( 'min' );
			}
			
			if ( articleToc.data( 'max' ) ) {
				this.lastLevel = articleToc.data( 'max' );
			}
			
			if ( articleToc.data( 'offset' ) ) {
				this.offset = articleToc.data( 'offset' );
			} else {
				articleToc.data( 'offset', this.offset )
			}

			
			if ( typeof articleToc.data('exclude_class') !== 'undefined' ) {
				this.excludeClass = articleToc.data('exclude_class');
			}
			
			while ( this.firstLevel <= this.lastLevel ) {
				this.searchStr += 'h' + this.firstLevel + ( this.firstLevel < this.lastLevel ? ',' : '' );
				this.firstLevel++;
			}
		},
		
		// return object with headers and their ids 
		getArticleHeaders: function () {
			let headers = [];
			let that = this;
			
			articleContent.find( that.searchStr ).each( function(){
					
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
		getToCHTML: function ( headers, titleTag='h4' ) {
			let html;
				
			if ( articleToc.find('.eckb-article-toc__title').length ) {
					
				let title = articleToc.find('.eckb-article-toc__title').html();
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

			let currentTop = $(window).scrollTop();
			let currentBottom = $(window).scrollTop() + $(window).height();
			let highlighted = false;
			let $highlightedEl = false;
			let offset = articleToc.data( 'offset' );

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

	setTimeout ( function() {

		if ( articleToc.length ) {
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

		if ( articleToc.length ) {
			mobile_TOC();
		}
	}, 500 );

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


	/********************************************************************
	 *                      Create Demo Data
	 ********************************************************************/
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

	/********************************************************************
	 *                      Sidebar v2
	 ********************************************************************/
	if( $( '#elay-sidebar-container-v2' ).length === 0 && $( '#epkb-sidebar-container-v2' ).length > 0 ){

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
		sidebarV2.on('click', '.epkb-top-class-collapse-on', function (e) {

			// prevent open categories when click on editor tabs 
			if ( typeof e.originalEvent !== 'undefined' && ( $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--active') || $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--parent') ) ) {
				return;
			}

			$( this ).parent().toggleClass( 'epkb-active-top-category' );
			$( this).parent().find( $( '.epkb-sidebar__cat__top-cat__body-container') ).slideToggle();
		});

		// Icon toggle - toggle between open icon and close icon
		sidebarV2.on('click', '.epkb-sidebar__cat__top-cat__heading-container', function (e) {

			// prevent open categories when click on editor tabs 
			if ( typeof e.originalEvent !== 'undefined' && ( $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--active') || $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--parent') ) ) {
				return;
			}

			let icon = $(this).find('.epkb-sidebar__heading__inner span');
			if ( icon.length > 0 ) {
				epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
			}
		});

		// SUB-CATEGORIES -----------------------------------/
		// Show or hide article in sliding motion
		sidebarV2.on('click', '.epkb-category-level-2-3', function () {

			// show lower level of categories and show articles in this category
			$( this ).next().slideToggle();
			$( this ).next().next().slideToggle();

		});
		// Icon toggle - toggle between open icon and close icon
		sidebarV2.on('click', '.epkb-category-level-2-3', function () {
			let icon = $(this).find('span');
			if ( icon.length > 0 ) {
				epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
			}
		});

		// SHOW ALL articles functionality
		sidebarV2.on('click', '.epkb-show-all-articles', function () {

			$( this ).toggleClass( 'active' );
			let parent = $( this ).parent( 'ul' );
			let article = parent.find( 'li');

			//If this has class "active" then change the text to Hide extra articles
			if ( $(this).hasClass( 'active') ) {

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
				} else if ( $(this).hasClass( 'visible')) {
					$(this).removeClass('visible');
					$(this).addClass('epkb-hide-elem');
				}
			});
		});

		epkb_open_and_highlight_selected_article_v2();
		epkb_open_current_archive_category();
	}


	/********************************************************************
	 *                      Module Layout
	 ********************************************************************/

	// Classic Layout --------------------------------------------------------------/

	// Show main content of Category.
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-articles-show-more', function() {

		$( this ).parent().parent().toggleClass( 'epkb-category-section--active');

		$( this ).parent().parent().find( '.epkb-category-section__body' ).slideToggle();

		$( this ).find( '.epkb-ml-articles-show-more__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );

		const isExpanded = $( this ).find( '.epkb-ml-articles-show-more__show-more__icon' ).hasClass( 'epkbfa-minus' );

		if ( isExpanded ) {
			$( this ).parent().find( '.epkb-ml-article-count' ).hide();
		} else {
			$( this ).parent().find( '.epkb-ml-article-count' ).show();
		}
	} );

	// Toggle Level 2 Category Articles and Level 3 Categories
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-2-lvl-category-container', function( e ) {
		// to hide Articles, use a click only on the "minus" icon
		if ( $( this ).hasClass( 'epkb-ml-2-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-2-lvl-category__show-more__icon' ) ) return;
		$( this ).find( '.epkb-ml-2-lvl-article-list' ).slideToggle();
		$( this ).find( '.epkb-ml-3-lvl-categories' ).slideToggle();
		$( this ).find( '.epkb-ml-2-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		$( this ).toggleClass( 'epkb-ml-2-lvl-category--active' );
	} );

	// Toggle Level 3 Category Articles and Level 4 Categories
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-3-lvl-category-container', function( e ) {
		// to hide Articles, use a click only on the "minus" icon
		if ( $( this ).hasClass( 'epkb-ml-3-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-3-lvl-category__show-more__icon' ) ) return;
		$( this ).find( '.epkb-ml-3-lvl-article-list' ).slideToggle();
		$( this ).find( '.epkb-ml-4-lvl-categories' ).slideToggle();
		$( this ).find( '.epkb-ml-3-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		$( this ).toggleClass( 'epkb-ml-3-lvl-category--active' );
	} );

	// Toggle Level 4 Category Articles and Level 5 Categories
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-4-lvl-category-container', function( e ) {
		// to hide Articles, use a click only on the "minus" icon
		if ( $( this ).hasClass( 'epkb-ml-4-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-4-lvl-category__show-more__icon' ) ) return;
		$( this ).find( '.epkb-ml-4-lvl-article-list' ).slideToggle();
		$( this ).find( '.epkb-ml-5-lvl-categories' ).slideToggle();
		$( this ).find( '.epkb-ml-4-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		$( this ).toggleClass( 'epkb-ml-4-lvl-category--active' );
	} );

	// Toggle Level 5 Category Articles
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-5-lvl-category-container', function( e ) {
		// to hide Articles, use a click only on the "minus" icon
		if ( $( this ).hasClass( 'epkb-ml-5-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-5-lvl-category__show-more__icon' ) ) return;
		$( this ).find( '.epkb-ml-5-lvl-article-list' ).slideToggle();
		$( this ).find( '.epkb-ml-5-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		$( this ).toggleClass( 'epkb-ml-5-lvl-category--active' );
	} );

	// Drill Down Layout --------------------------------------------------------------/

	// Define frequently used selectors
	const $catContent_ShowClass     			= 'epkb-ml__cat-content--show';
	const $topCatButton_ActiveClass 			= 'epkb-ml-top__cat-container--active';
	const $catButton_ActiveClass    			= 'epkb-ml__cat-container--active';
	const $catButtonContainers_ActiveClass  	= 'epkb-ml-categories-button-container--active';
	const $catButtonContainers_ShowClass    	= 'epkb-ml-categories-button-container--show';

	const $backButton_ActiveClass = 'epkb-back-button--active';

	// Top Category Button Trigger
	$( document ).on('click', '.epkb-ml-top__cat-container', function() {

		const $allCatContent            = $( '.epkb-ml-all-categories-content-container' );

		$( '.epkb-ml-top__cat-container' ).removeClass( $topCatButton_ActiveClass );

		// Hide content when clicked on active Top Category button
		if ( $( this ).hasClass( $topCatButton_ActiveClass ) ) {
			$( this ).removeClass( $topCatButton_ActiveClass );
			$allCatContent.hide();
			return;
		}

		// Do not show Back button for Top Category content
		$allCatContent.find( '.epkb-back-button' ).removeClass( $backButton_ActiveClass );

		let currentTopCat = $( this );

		// Highlight current Top Category button
		$( this ).removeClass( $topCatButton_ActiveClass );
		currentTopCat.addClass( $topCatButton_ActiveClass );

		moveCategoriesBoxUnderTopCategoryButton( currentTopCat );

		// Remove all Classes
		$( '.epkb-ml-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
		$( '.epkb-ml__cat-content' ).removeClass( $catContent_ShowClass );
		$( '.epkb-ml__cat-container' ).removeClass( $catButton_ActiveClass );



		$allCatContent.show();

		// Get ID of current Category
		const catId = $( this ).data( 'cat-id' );

		// Show Level 1 Category Description / Articles
		$( '.epkb-ml-1-lvl__cat-content[data-cat-id="' + catId + '"]' ).addClass( $catContent_ShowClass );

		// Show Level 2 Categories
		$( '.epkb-ml-2-lvl-categories-button-container[data-cat-level="1"][data-cat-id="' + catId + '"]' ).addClass( $catButtonContainers_ShowClass );
	});

	// Insure categories content box is right under the current Top Category button when window resized
	$( window ).resize( function() {
		let resizeTimeout = setTimeout( function() {

			if ( resizeTimeout ) {
				clearTimeout( resizeTimeout );
			}

			// Continue only if any Top Category is currently active
			let currentTopCat = $( '.' + $topCatButton_ActiveClass );
			if ( ! currentTopCat.length ) {
				return;
			}

			moveCategoriesBoxUnderTopCategoryButton( currentTopCat );
		}, 1000 );
	} );

	// Move content box under the category row
	function moveCategoriesBoxUnderTopCategoryButton( currentTopCat ) {

		const $allCatContent = $( '.epkb-ml-all-categories-content-container' );

		$allCatContent.hide();

		let currentTopCatOffset = currentTopCat.offset().top;
		let isBoxMoved = false;

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

	// Category Button Trigger
	$( document ).on('click', '.epkb-ml__cat-container', function() {

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

		// Show current Category Header
		$( this ).addClass( $catButton_ActiveClass );
		$( '.epkb-ml-' + catLevel + '-lvl-categories-button-container[data-cat-id="' + parentCatId + '"]' ).addClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );

		// Hide content of parent Category
		$( '.epkb-ml-' + parentCatLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
		$( '.epkb-ml-' + parentCatLevel + '-lvl__cat-container' ).removeClass( $catButton_ActiveClass );
		$( '.epkb-ml-' + parentCatLevel + '-lvl__cat-content' ).removeClass( $catContent_ShowClass );

		// Show content of current Category
		$( '.epkb-ml-' + catLevel + '-lvl__cat-content[data-cat-id="' + catId + '"]' ).addClass( $catContent_ShowClass );
		$( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container[data-cat-id="' + catId + '"]' ).addClass( $catButtonContainers_ShowClass );
	});

	// Back Button of Category Content
	$( document ).on('click', '.epkb-back-button', function() {

		// Get Level of current Category
		let currentCatContent = $( '.epkb-ml__cat-content' + '.' + $catContent_ShowClass );
		let catLevel = parseInt( currentCatContent.data( 'cat-level' ) );

		// Get Level of child Categories
		let childCatLevel = catLevel + 1;

		// Return to the Top Categories view if Level 1 Content is currently shown
		if ( catLevel === 1 ) {
			$( '.epkb-ml-top__cat-container' ).removeClass( $topCatButton_ActiveClass );
			$( '.epkb-ml-all-categories-content-container' ).hide();
			return;
		}

		// Get Level and ID of parent Category
		let parentCatId = currentCatContent.data( 'parent-cat-id' );
		let parentCatLevel = catLevel - 1;

		// Do not show Back button for Top Category content
		if ( parentCatLevel === 1 ) {
			$( '.epkb-ml-all-categories-content-container .epkb-back-button' ).removeClass( $backButton_ActiveClass );
		}

		// Hide elements of the current Category
		$( '.epkb-ml-' + catLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass );
		$( '.epkb-ml-' + catLevel + '-lvl__cat-container' ).removeClass( $catButton_ActiveClass );
		$( '.epkb-ml-' + catLevel + '-lvl__cat-content' ).removeClass( $catContent_ShowClass );
		$( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );

		// Show elements of previous level Category
		let parentCatButton = $( '.epkb-ml-' + parentCatLevel + '-lvl__cat-container[data-cat-id="' + parentCatId + '"]' );
		parentCatButton.closest( '.epkb-ml-categories-button-container' ).addClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
		parentCatButton.addClass( $catButton_ActiveClass );
		$( '.epkb-ml-' + parentCatLevel + '-lvl__cat-content[data-cat-id="' + parentCatId + '"]' ).addClass( $catContent_ShowClass );
	});

	// FAQs Module -----------------------------------------------------------------/
	// Accordion mode
	$('.epkb-faqs-accordion-mode .epkb-faqs__item__question').filter(function() {
		return $(this).data('faq-type') === 'module';
	}).on('click', function(){

		let container = $(this).closest('.epkb-faqs__item-container').eq(0);

		if (container.hasClass('epkb-faqs__item-container--active')) {
			container.find('.epkb-faqs__item__answer').stop().slideUp(400);
		} else {
			container.find('.epkb-faqs__item__answer').stop().slideDown(400);
		}
		container.toggleClass('epkb-faqs__item-container--active');
	});
	// Toggle Mode
	$('.epkb-faqs-toggle-mode .epkb-faqs__item__question').filter(function() {
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
	});



	/********************************************************************
	 *                      Articles Views Counter
	 ********************************************************************/

	// check if we on article page
	if ( $('#eckb-article-content').length > 0 ) {
		epkb_send_article_view();
	}

	function epkb_send_article_view() {
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


	/********************************************************************
	 *                      Category Archive Page
	 ********************************************************************/
	if ( $( '#eckb-archive-page-container' ).length ) {

		$( document ).on( 'click', '.eckb-article-list-show-more-container', function() {
			$( this ).parent().find( '.eckb-article-container' ).removeClass( 'epkb-hide-elem' );
			$( '.eckb-article-list-show-more-container' ).hide();
		});
	}


});
