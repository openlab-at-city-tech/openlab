/**======================================================
 * WORDPRESS ADMINISTRATION CUSTOM SIDEBAR INTERFACE JS
 * ======================================================
 * 
 * This file contains all custom jQuery plugins and code 
 * used in the admin part of the website. All jQuery 
 * is loaded in no-conflict mode in order to prevent js 
 * library conflicts.
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 *
 * Licensed under the Apache License, Version 2.0 
 * (the "License") you may not use this file except in 
 * compliance with the License. You may obtain a copy 
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in 
 * writing, software distributed under the License is 
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES 
 * OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing 
 * permissions and limitations under the License.
 *
 * PLEASE NOTE: The following dependancies are required
 * in order for this file to run correctly:
 *
 * 1. jQuery	( http://jquery.com/ )
 * 2. jQueryUI	( http://jqueryui.com/ )
 * 3. sidebarL10n js object to be enqueued on the page
 *
 * ======================================================= */

;( function($, window, document, undefined) {"use strict";
	$.fn.themeSidebarMenu = function() {

		var api     = this;
		var options = {
			menuItemDepthPerLevel : 30, // Do not use directly. Use depthToPx and pxToDepth instead.
			globalMaxDepth : 11
		};
		var sidebarList = $('#sidebar-to-edit');
		var targetList;
		var isRTL       = !! ( 'undefined' != typeof isRtl && isRtl );
		var negateIfRTL = ( 'undefined' != typeof isRtl && isRtl ) ? -1 : 1;

		// Flag to listen for sidebar changes
		var sidebarChanged = false;

		/**
		 * Init Function:
		 *  
		 * Called upon plugin initialisation in order to set
		 * up the behaviour for the admin options screen.
		 *
		 * @version 1.0.9
		 * 
		 */
		api.init = function() {
			// Register jQuery function extenstions
			api.jQueryExtensions();

			// Edit Sidebars Screen Functionality
			api.initAccordion();
			api.initToggles();
			api.attachTabsPanelListeners();
			api.attachQuickSearchListeners();
			api.setupInputWithDefaultTitle();
			api.initSortables();
			api.registerEditEvents();

			// Manage Sidebar Replacements Screen Functionality
			api.registerManagementEvents();		

			// Add unload event	
			$(window).on( 'beforeunload', function() {
				if ( sidebarChanged ) {
					return sidebarsL10n.confirmation;
				}
			});
		};

		/**
		 * Initialise Tab Panel Events:
		 * 
		 * Creates event listeners and attaches them to the
		 * tab panel in order to add interactivity.
		 *
		 * @version 1.0.9
		 * 
		 */
		api.attachTabsPanelListeners = function( panelId ) {

			panelId = panelId || '#sidebar-all-pages-column';

			var tabPanelNav       = panelId + ' a.nav-tab-link';
			var tabPanelSelectAll = panelId + ' a.select-all';
			var tabPanelAddNew    = panelId + ' .submit-add-to-menu';

			// Switch tab panels
			$( tabPanelNav ).on( 'click', function(e){
				// Prevent default actions
				e.preventDefault();

				// Switch tabs and clear checked inputs
				$(this).parent().addClass('tabs').siblings().removeClass('tabs');
				$( $(this).attr( 'href' ) )
					.removeClass( 'tabs-panel-inactive' )
					.addClass( 'tabs-panel-active' )
					.siblings().removeClass( 'tabs-panel-active' )
					.addClass ( 'tabs-panel-inactive' )
					.find( 'input' ).removeAttr( 'checked' );

				// Set focus into the quicksearch input field
				$( $(this).attr( 'href' ) + ' .quick-search' ).focus();
			});

			// Select all checkboxes functionality
			$( tabPanelSelectAll ).on( 'click', function(e){
				// Prevent default actions
				e.preventDefault();
				// Find and select all inputs
				$(this).parent().parent().siblings('.tabs-panel-active').find('input').prop('checked', true);
			});

			// Add listeners for Add to Sidebar Buttons
			$( tabPanelAddNew ).on('click', function(e){
				e.preventDefault();
				$('#' + $(this).attr('id').replace(/submit-/, '')).addSelectedToSidebar( api.addMenuItemToBottom );
			});

			return false;
		};

		/**
		 * Initialise Metabox Accordion
		 *
		 * Initialises the metabox accordion used to show
		 * pages/posts/taxonomies etc that are available
		 * for sidebar replacements.
		 * 
		 * @version 1.0.9
		 * 
		 */
		api.initAccordion = function() {
			var accordionOptions = $( '.accordion-container li.accordion-section' );
			$('.accordion-section-content input[type="checkbox"]').prop('checked', false);
			accordionOptions.removeClass('open');
			accordionOptions.filter(':visible').first().addClass( 'open first-child' );
			api.initPagination();
		};

		/**
		 * Initialise Pagination
		 *
		 * Enables the ability for the user to paginate
		 * through a large list in a taxonomy without 
		 * refreshing the page. This is a recursive function.
		 * 
		 * @version 1.0.9
		 * 
		 */
		api.initPagination = function( panelLink ) {

			var panelId;
			// Attach event to all panel links if one hasn't been passed in the parameter
			panelLink = panelLink || '.add-menu-item-pagelinks a';


			// Pagination Handling
			$( panelLink ).on( 'click', function(e) {
					e.preventDefault();
					$.post( ajaxurl, e.target.href.replace(/.*\?/, '').replace(/action=([^&]*)/, '') + '&action=ecs_sidebar_get_metabox',
						function( resp ) {
							if ( -1 == resp.indexOf('replace-id') )
								return;
							var metaBoxData = $.parseJSON(resp),
							toReplace       = document.getElementById(metaBoxData['replace-id']),
							placeholder     = document.createElement('div'),
							wrap            = document.createElement('div');

							// Update Panel Link and Pnale ID
							panelLink = '#' + metaBoxData['replace-id'] + ' .add-menu-item-pagelinks a';
							panelId   = '#' + metaBoxData['replace-id'];

							if ( ! metaBoxData['markup'] || ! toReplace )
								return;

							wrap.innerHTML = metaBoxData['markup'] ? metaBoxData['markup'] : '';

							toReplace.parentNode.insertBefore( placeholder, toReplace );
							placeholder.parentNode.removeChild( toReplace );
							placeholder.parentNode.insertBefore( wrap, placeholder );
							placeholder.parentNode.removeChild( placeholder );

						}
					).done(function(){
						api.initPagination( panelLink );
						api.attachTabsPanelListeners( panelId );
						api.setupInputWithDefaultTitle();
						api.attachQuickSearchListeners( panelId + ' .quick-search' );
					});

				return false;
			});
		};

		/**
		 * Initialise Metabox Toggles
		 *
		 * @description - AJAX function that enables the user to 
		 *     show/hide the metaboxes on the options page screen. 
		 *     This calls an ajax function server side in order 
		 *     for persistent storage of the users preference.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		api.initToggles = function() {
			$('.hide-postbox-tog').on( 'click', function(){
				var checkbox = $(this);
				var value = checkbox.attr( 'value' );

				if ( checkbox.prop( 'checked' ) ) {
					$( '#' + value ).removeClass('hidden');
				} else {
					$( '#' + value ).addClass('hidden');
				}

				var accordionTitles = $( '.accordion-container li.accordion-section' );
				accordionTitles.removeClass( 'first-child' );
				accordionTitles.filter(':visible').first().addClass( 'first-child' );

				if ( ! accordionTitles.filter(':visible').hasClass( 'open') ) {
					api.initAccordion();
				}

				var hidden = $( '.accordion-container li.accordion-section' ).filter(':hidden').map(function() { return this.id; }).get().join(',');
				$.post(ajaxurl, {
					action: 'closed-postboxes',
					hidden: hidden,
					closedpostboxesnonce: $('#closedpostboxesnonce').val(),
					page: 'appearance_page_easy-custom-sidebars'
				});
			});
		};

		/**
		 * Extend Global jQuery functions
		 *
		 * Defines additional jQuery functions that can be used
		 * in a global context upon plugin initialisation.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		api.jQueryExtensions = function() {
			$.fn.extend({
				menuItemDepth : function() {
					var margin = api.isRTL ? this.eq(0).css('margin-right') : this.eq(0).css('margin-left');
					return api.pxToDepth( margin && -1 != margin.indexOf('px') ? margin.slice(0, -2) : 0 );
				},
				updateParentMenuItemDBId : function() {
					return this.each(function(){
						var item        = $(this),
							input       = item.find( '.menu-item-data-parent-id' ),
							depth       = parseInt( item.menuItemDepth(), 16 ),
							parentDepth = depth - 1,
							parent      = item.prevAll( '.menu-item-depth-' + parentDepth ).first();

						if ( 0 === depth ) { // Item is on the top level, has no parent
							input.val(0);
						} else { // Find the parent item, and retrieve its object id.
							input.val( parent.find( '.menu-item-data-db-id' ).val() );
						}
					});
				},
				hideAdvancedMenuItemFields : function() {
					return this.each(function(){
						var that = $(this);
						$('.hide-column-tog').not(':checked').each(function(){
							that.find('.field-' + $(this).val() ).addClass('hidden-field');
						});
					});
				},
				slideFadeToggle : function(speed, easing, callback) {
					return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
				},
				/**
				 * Adds selected menu items to the menu.
				 *
				 * @param jQuery metabox The metabox jQuery object.
				 */
				addSelectedToSidebar : function(processMethod) {

					if ( 0 === $('#sidebar-to-edit').length ) {
						return false;
					}

					return this.each(function() {
						var t          = $(this);
						var menuItems  = {};
						var checkboxes = ( sidebarsL10n.oneThemeLocationNoSidebars && 0 === t.find('.tabs-panel-active .categorychecklist li input:checked').length ) ? t.find('#page-all li input[type="checkbox"]') : t.find('.tabs-panel-active .categorychecklist li input:checked');
						var re         = new RegExp('menu-item\\[(\[^\\]\]*)');

						processMethod  = processMethod || api.addMenuItemToBottom;

						// If no items are checked, bail.
						if ( !checkboxes.length )
							return false;

						// Show the ajax spinner
						// t.find('.spinner').show();
						t.find('.spinner').toggleClass( 'ecs-visible' );

						// Retrieve menu item data
						$(checkboxes).each(function(){

							var t = $(this),
								listItemDBIDMatch = re.exec( t.attr('name') ),
								listItemDBID = 'undefined' == typeof listItemDBIDMatch[1] ? 0 : parseInt(listItemDBIDMatch[1], 10);

							if ( this.className && -1 != this.className.indexOf('add-to-top') )
								processMethod = api.addMenuItemToTop;
								menuItems[listItemDBID] = t.closest('li').getItemData( 'add-menu-item', listItemDBID );
						});

						// Add the individual item to the sortable list
						api.addItemToSidebar(menuItems, processMethod, function(){
							// Deselect the items and hide the ajax spinner
							checkboxes.removeAttr('checked');
							t.find('.spinner').toggleClass( 'ecs-visible' );
							// t.find('.spinner').hide();
						});
					});
				},
				getItemData : function( itemType, id ) {
					itemType = itemType || 'menu-item';

					var itemData = {}, i,
					fields = [
						'menu-item-db-id',
						'menu-item-object-id',
						'menu-item-object',
						'menu-item-parent-id',
						'menu-item-position',
						'menu-item-type',
						'menu-item-title',
						'menu-item-url',
						'menu-item-description',
						'menu-item-attr-title',
						'menu-item-target',
						'menu-item-classes',
						'menu-item-xfn'
					];

					if( !id && itemType == 'menu-item' ) {
						id = this.find('.menu-item-data-db-id').val();
					}

					if( !id ) return itemData;

					this.find('input').each(function() {
						var field;
						i = fields.length;
						while ( i-- ) {
							if( itemType == 'menu-item' )
								field = fields[i] + '[' + id + ']';
							else if( itemType == 'add-menu-item' )
								field = 'menu-item[' + id + '][' + fields[i] + ']';

							if (
								this.name &&
								field == this.name
							) {
								itemData[fields[i]] = this.value;
							}
						}
					});

					return itemData;
				},
				setItemData : function( itemData, itemType, id ) { // Can take a type, such as 'menu-item', or an id.
					itemType = itemType || 'menu-item';

					if( !id && itemType == 'menu-item' ) {
						id = $('.menu-item-data-db-id', this).val();
					}

					if( !id ) return this;

					this.find('input').each(function() {
						var t = $(this), field;
						$.each( itemData, function( attr, val ) {
							if( itemType == 'menu-item' )
								field = attr + '[' + id + ']';
							else if( itemType == 'add-menu-item' )
								field = 'menu-item[' + id + '][' + attr + ']';

							if ( field == t.attr('name') ) {
								t.val( val );
							}
						});
					});
					return this;
				}
			});
		};

		/**
		 * Set Input Placeholder Text
		 *
		 * Provides a cross browser compatible way to
		 * set placeholder text for input fields.
		 *
		 * @version 1.0.9
		 * 
		 */
		api.setupInputWithDefaultTitle = function() {
			var name = 'input-with-default-title';

			$( '.' + name ).each( function(){
				var $t    = $(this);
				var title = $t.attr('title');
				var val   = $t.val();
				$t.data( name, title );

				if( '' === val ) {
					$t.val( title );
				} else if( title === val ) {
					return;
				} else {
					$t.removeClass( name );
				}

				// Add class on input focus event
				$t.on('focus', function(){
					if( $t.val() === $t.data(name) ) {
						$t.val('').removeClass( name );
					}
				});

				// Remove class on input blur event
				$t.on('blur', function(){
					if( '' === $t.val() ) {
						$t.addClass( name ).val( $t.data(name) );
					}
				});

			});

			$( '.blank-slate .input-with-default-title' ).focus();
		};

		/**
		 * Add Quick Search Listener
		 *
		 * Registers listener events in order to trigger
		 * the quick search functionality in the tab panel
		 * located in the accordion.
		 *
		 * @version 1.0.9
		 * 
		 */
		api.attachQuickSearchListeners = function( selector ) {
			var searchTimer;

			if ( ! selector ) {
				selector = '.quick-search';
			}

			$( selector ).on('keypress', function(e){
				var t = $(this);

				if( 13 == e.which ) {
					api.updateQuickSearchResults( t );
					return false;
				}

				if( searchTimer ) {
					clearTimeout( searchTimer );
				}

				searchTimer = setTimeout(function(){
					api.updateQuickSearchResults( t );
				}, 400);
			}).attr( 'autocomplete', 'off' );
		};

		/**
		 * Update Instant Seach Results
		 *
		 * Generates an AJAX query and updates the search panel
		 * with the returned results based on the user input.
		 * 
		 * @param  {string} input - User search query
		 * @return {[type]}       [description]
		 *
		 * @version 1.0.9
		 * 
		 */
		api.updateQuickSearchResults = function( input ) {
			var panel;
			var params;
			var minSearchLength = 2;
			var q = input.val();

			if( q.length < minSearchLength ) return;

			panel = input.parents( '.tabs-panel' );
			params = {
				'action': 'ecs_sidebar_quick_search',
				'response-format': 'markup',
				'menu': $('#menu').val(),
				'ecs_sidebar_quick_search_nonce': $('#ecs_sidebar_quick_search_nonce').val(),
				'q': q,
				'type': input.attr('name')
			};

			$('.spinner', panel).show();
			$('.spinner', panel).toggleClass( 'ecs-visible' );

			$.post( ajaxurl, params, function(sidebarMarkup) {
				api.processQuickSearchQueryResponse(sidebarMarkup, params, panel);
			});

		};

		/**
		 * Process the quick search response into a search result
		 *
		 * Processes the user input into a response which is
		 * then used to update the tab panel with the corresponding
		 * results.
		 * 
		 * @param string resp The server response to the query.
		 * @param object req The request arguments.
		 * @param jQuery panel The tabs panel we're searching in.
		 * @todo Add in Localisation L10n and update menu references
		 *
		 * @version 1.0.9
		 * 
		 */
		api.processQuickSearchQueryResponse = function(resp, req, panel) {
			var matched, newID,
			takenIDs = {},
			form     = $('#sidebar-meta'),
			pattern  = new RegExp('sidebar-item\\[(\[^\\]\]*)', 'g'),
			$items   = $('<div>').html(resp).find('li'),
			$item;

			if( ! $items.length ) {
				$('.categorychecklist', panel).html( '<li><p>' + sidebarsL10n.noResultsFound + '</p></li>' );
				$('.spinner', panel).toggleClass( 'ecs-visible' );
				// $('.spinner', panel).hide();
				return;
			}

			$items.each(function(){
				$item = $(this);

				// make a unique DB ID number
				matched = pattern.exec($item.html());

				if ( matched && matched[1] ) {
					newID = matched[1];
					while( form.elements['sidebar-item[' + newID + '][sidebar-item-type]'] || takenIDs[ newID ] ) {
						newID--;
					}

					takenIDs[newID] = true;
					if ( newID != matched[1] ) {
						$item.html( $item.html().replace(new RegExp(
							'sidebar-item\\[' + matched[1] + '\\]', 'g'),
							'sidebar-item[' + newID + ']'
						) );
					}
				}
			});


			$('.categorychecklist', panel).html( $items );
			$('.spinner', panel).toggleClass('ecs-visible');
			// $('.spinner', panel).hide();
		};

		/**
		 * Initialise jQuery Sortables on Sidebar Items List
		 *
		 * Initialised the jQuery UI Sortables plugin functionality
		 * in order to allow the user to order their sidebar replacements.
		 * Mimics the native nav menu functionality in WordPress.
		 * 
		 * @version 1.0.9
		 * 
		 */
		api.initSortables = function() {

			var currentDepth = 0;
			var originalDepth, minDepth, maxDepth, prev, next, prevBottom, nextThreshold, helperHeight, transport;

			if( 0 !== $( '#sidebar-to-edit li' ).length ) {
				$( '.drag-instructions' ).show();
			} else {
				$( '#sidebar-instructions' ).show();
			}

			// Initialise jQuery sortable on the sidebar items list
			$( "#sidebar-to-edit" ).sortable({
				// axis: "y", // Uncomment to restrict sidebar item movement
				handle: ".menu-item-handle",
				placeholder: "sortable-placeholder",
				sort : function( event, ui ) {
					$( "#sidebar-to-edit .sortable-placeholder" ).height( ( $('.ui-sortable-helper').height() - 2 ) );
				},
				change : function( event, ui ) {
					api.registerChange();
				}
			});

			// Attach events to each list item
			$("#sidebar-to-edit li").each(function(){
				
				var li            = $(this);
				var trigger       = $(this).find('a.item-edit');
				var cancelTrigger = $(this).find('a.item-cancel');
				var removeTrigger = $(this).find('a.item-delete');
				var panel         = $(this).find('div.menu-item-settings');

				// Panel show/hide trigger handling
				trigger.on('click', function(e){
					e.preventDefault();
					panel.slideToggle('fast');
					return false;
				});

				// Cancel trigger handling
				cancelTrigger.on('click', function(){
					panel.toggle();
					return false;
				});

				// Remove trigger handling
				removeTrigger.on('click', function(){
					li.addClass('deleting');
					li.slideFadeToggle( 350, function(){
						li.remove();
						api.registerChange();
						// Update contextual help message
						if( 0 !== $( '#sidebar-to-edit li' ).length ) {
							$( '#sidebar-instructions' ).hide();
							$( '.drag-instructions' ).show();
						} else {
							$( '.drag-instructions' ).hide();
							$( '#sidebar-instructions' ).show();
						}
					});

					return false;
				});
			});

			$( "#sidebar-to-edit" ).disableSelection();
		};

		/**
		 * Add Item to Sidebar Using AJAX
		 * @return {[type]} [description]
		 */
		api.addItemToSidebar = function( menuItem, processMethod, callback ) {
			api.registerChange();

			var menu      = $( '#sidebar' ).val();
			var nonce     = $( '#ecs_sidebar_settings_column_nonce' ).val();
			processMethod = processMethod || function(){};
			callback      = callback || function(){};

			var dataObj = {
				'action': 'ecs_add_sidebar_item',
				'menu': menu,
				'menu-item': menuItem,
				'ecs_sidebar_settings_column_nonce' : nonce
			};

			// NOTE: Append this markup to the list (Possible Sanity Checks to see if it has already been added)

			$.post( ajaxurl, dataObj, function(sidebarMarkup) {

				var ins   = $('#sidebar-instructions');
				processMethod(sidebarMarkup, dataObj);

				// Make it stand out a bit more visually, by adding a fadeIn
				$( 'li.pending' ).each( function() {
					var li            = $(this).hide().fadeIn( 'slow' ).removeClass( 'pending' );
					var panel         = li.find( 'div.menu-item-settings' );
					var trigger       = li.find('a.item-edit');
					var cancelTrigger = li.find('a.item-cancel');
					var removeTrigger = li.find('a.item-delete');

					// Trigger Event
					trigger.on( 'click', function() {
						panel.slideToggle('fast');
						return false;
					});

					cancelTrigger.on( 'click', function() {
						panel.toggle();
						return false;
					});

					removeTrigger.on( 'click', function() {
						api.registerChange();
						li.addClass('deleting');
						li.slideFadeToggle( 350, function(){
							li.remove();

							// Update contextual help message
							if( 0 !== $( '#sidebar-to-edit li' ).length ) {
								$( '#sidebar-instructions' ).hide();
								$( '.drag-instructions' ).show();
							} else {
								$( '.drag-instructions' ).hide();
								$( '#sidebar-instructions' ).show();
							}
						});
						return false;
					});

				});

					// Update contextual help message
					$( '#sidebar-instructions' ).hide();
					$( '.drag-instructions' ).show();

				if( ! ins.hasClass( 'menu-instructions-inactive' ) && ins.siblings().length )
					ins.addClass( 'menu-instructions-inactive' );
				callback();
			});
		};

		/**
		 * Process the add menu item request response into menu list item.
		 *
		 * @param string sidebarMarkup The text server response of menu item markup.
		 * @param object req The request arguments.
		 */
		api.addMenuItemToBottom = function( sidebarMarkup, req ) {
			$(sidebarMarkup).appendTo( $('#sidebar-to-edit') );
		};

		api.addMenuItemToTop = function( sidebarMarkup, req ) {
			$(sidebarMarkup).prependTo( $('#sidebar-to-edit') );
		};

		/**
		 * Register Sidebar Changes
		 *
		 * Set the sidebarChanged variable to true
		 * to flag any unsaved changes in the admin interface.
		 * 
		 */
		api.registerChange = function() {
			sidebarChanged = true;
		};

		/**
		 * Unregister Sidebar Changes
		 *
		 * Set the sidebarChanged variable to true
		 * to flag any unsaved changes in the admin interface.
		 * 
		 */
		api.unregisterChange = function() {
			sidebarChanged = false;
		};

		/**
		 * Delete Sidebar Using AJAX
		 *
		 * Sends an AJAX request in order to delete a specific sidebar
		 * with the id that matches the value passed into this function. 
		 * 
		 * @param  {string} sidebarId The ID (post meta id not post id) of the sidebar we want to delete
		 * @return {[type]}           [description]
		 */
		api.deleteSidebar = function( sidebarId, processMethod, callback ) {

			api.unregisterChange();

			processMethod = processMethod || function(){};
			callback      = callback || function(){};
			var nonce     = $( '#ecs_delete_sidebar_instance_nonce' ).val();

			var dataObj = {
				'action': 'ecs_delete_sidebar_instance',
				'sidebarId': sidebarId,
				'ecs_delete_sidebar_instance_nonce' : nonce
			};

			$.post( ajaxurl, dataObj, function() {
				processMethod();
			}).done( function() {
				callback();
			});
		};

		/**
		 * Delete All Sidebars Using AJAX
		 *
		 * Constructs an AJAX request to delete all sidebar instances.
		 * Sends the WordPress generated nonce to ensure that this is
		 * a legitamate request.
		 * 
		 * @param  {Function}   processMethod - Function to execute during request
		 * @param  {Function}   callback      - Function to execute after successful AJAX reequest.
		 *
		 * @version 1.0.9
		 * 
		 */
		api.deleteAllSidebars = function( processMethod, callback ) {

			api.unregisterChange();

			processMethod = processMethod || function(){};
			callback      = callback || function(){};
			var nonce     = $( '#ecs_delete_sidebar_instance_nonce' ).val();

			var dataObj = {
					'action': 'ecs_delete_all_sidebar_instances',
					'ecs_delete_sidebar_instance_nonce' : nonce
			};

			$.post( ajaxurl, dataObj, function() {
				processMethod();
			}).done( function() {
				callback();
			});
		};

		/**
		 * Create New Sidebar Instance Using AJAX
		 *
		 * Constructs an AJAX request to create a new sidebar instance.
		 * Sends the WordPress generated nonce to ensure that this is
		 * a legitamate request.
		 * 
		 * @param  {Function}   processMethod [description]
		 * @param  {Function} callback      [description]
		 *
		 * @version 1.0.9
		 * 
		 */
		api.createNewSidebar = function( sidebarName, processMethod, callback ) {

			api.unregisterChange();

			processMethod = processMethod || function(){};
			callback      = callback || function(){};
			var nonce     = $( '#ecs_edit_sidebar_instance_nonce' ).val();

			var dataObj = {
				'action': 'ecs_create_sidebar_instance',
				'ecs_edit_sidebar_instance_nonce' : nonce,
				'sidebar_name' : sidebarName
			};

			$.post( ajaxurl, dataObj, function() {
				processMethod();
			}).done( function(response) {
				var newSidebarId;
				var redirectUrl = $( '#create_sidebar_header' ).attr( 'data-redirect-url' );

				callback();

				// Get new sidebar ID 
				$(response).find('supplemental').each(function(){
					newSidebarId = $(this).find('new_sidebar_id').text();
					redirectUrl  += '&sidebar=' + newSidebarId;
				});

				// Redirect the user to the newly created sidebar
				window.location = redirectUrl.replace( ' ', '+' );
			});
		};

		/**
		 * Delete Sidebar Using AJAX
		 *
		 * Sends an AJAX request in order to delete a specific sidebar
		 * with the id that matches the value passed into this function. 
		 * 
		 * @param  {string} sidebarId The ID (post meta id not post id) of the sidebar we want to delete
		 * @return {[type]}           [description]
		 */
		api.saveSidebar = function( sidebarName, sidebarId, replacementId, description, processMethod, callback ) {
			
			api.unregisterChange();

			sidebarName       = sidebarName || '';
			sidebarId         = sidebarId || '0';
			replacementId     = replacementId || '0';
			description       = description || '';
			processMethod     = processMethod || function(){};
			callback          = callback || function(){};
			var nonce         = $( '#ecs_edit_sidebar_instance_nonce' ).val();
			var sidebarItems  = {};
			var position      = 0;

			$('#sidebar-to-edit li').each( function(e){
				// Increment position before to prevent conflicts
				position++;
				
				//Set position and increment position count
				sidebarItems[position] = $(this).getItemData();

			});

			var dataObj = {
				'action': 'ecs_update_sidebar_instance',
				'sidebarName' : sidebarName,
				'sidebarId': sidebarId,
				'replacementId' : replacementId,
				'description' : description,
				'ecs_edit_sidebar_instance_nonce' : nonce,
				'sidebar-items' : sidebarItems
			};

			$.post( ajaxurl, dataObj, function() {
				processMethod();
			}).done( function( response ) {
				var newSidebarName;
				// Get new sidebar Name
				$(response).find('supplemental').each(function(){
					newSidebarName = $(this).find('sidebar_name').text();
				});
				callback( newSidebarName );
			});
		};

		/**
		 * Register all events for the Edit Sidebar Screen
		 * ===============================================
		 *
		 * Registers all of the js events for the 'Edit Sidebars' 
		 * Screen.
		 * 
		 * @version 1.0.9
		 * 
		 */
		api.registerEditEvents = function() {

			// Sidebar change event listeners
			$( '#custom-sidebar-name, #sidebar_replacement_id, #sidebar_description' ).on( 'change', function(){
				api.registerChange();
			});

			// Disable metaboxes on create screen
			$( '.metabox-holder-disabled input[type="submit"]' ).attr( 'disabled', 'disabled' );

			// Create Event
			// Attach event listener in order to create a new sidebar instance
			$( '#create_sidebar_header, #create_sidebar_footer' ).on( 'click', function() {

				var sidebarNameLabel = $( '.custom-sidebar-name-label' );
				var sidebarNameInput = $( '#custom-sidebar-name' );
				var spinner          = $( '.sidebar-edit .spinner' );

				if ( sidebarNameInput.attr('title') === sidebarNameInput.val() ) {
					sidebarNameLabel.addClass('form-invalid');
					return false;

				} else {
					sidebarNameLabel.removeClass('form-invalid');
					spinner.toggleClass('ecs-visible');
					// spinner.fadeIn(200);
					api.createNewSidebar( sidebarNameInput.val() );
				}

				return false;
			});

			// Save/Update Event
			// Attaches an event listener to the 'Save Sidebar' buttons
			$( '#save_sidebar_header, #save_sidebar_footer' ).on( 'click', function() {
				var sidebarName      = $( '#custom-sidebar-name' ).val();
				var replacementId    = $( '#sidebar_replacement_id' ).val();
				var description      = $( '#sidebar_description' ).val();
				var sidebarId        = $(this).attr( 'data-sidebar-id' );
				var spinner          = $( '.sidebar-edit .spinner' );
				var sidebarNameLabel = $( '.custom-sidebar-name-label' );
				var redirectUrl      = $(this).attr( 'data-redirect-url' );

				var processMethod = function() {};
				var callback      = function( newSidebarName ) {

					// Fade out spinner and redirect user
					// spinner.fadeOut(200);
					spinner.toggleClass('ecs-visible');
					redirectUrl  += '&name=' + newSidebarName;
					window.location = redirectUrl.replace( ' ', '+' );
				};

				// Make sure a sidebar name has been entered
				if ( $( '#custom-sidebar-name' ).attr( 'title' ) === sidebarName ) {
					sidebarNameLabel.addClass('form-invalid');
					return false;
				}

				sidebarNameLabel.removeClass('form-invalid');

				// Clear placeholder text from description if no text has been entered
				if ( $( '#sidebar_description' ).attr( 'title' ) === description ) {
					description = '';
				}

				// spinner.fadeIn(100);
				spinner.toggleClass('ecs-visible');
				api.saveSidebar( sidebarName, sidebarId, replacementId, description, processMethod, callback );
				return false;
			});
			
			// Delete Sidebar Event
			// Attaches an event listener to each sidebar 'Delete Sidebar' link.
			$( '#delete-sidebar' ).on( 'click', function() {

				var confirmation = confirm( sidebarsL10n.deleteWarning );
				var sidebarId    = $(this).attr( 'data-sidebar-id' );
				var spinner      = $( '.sidebar-edit .spinner' );
				var redirectUrl  = $(this).attr( 'data-redirect-url' );

				var processMethod = function() {};
				var callback      = function() {
					window.location = redirectUrl.replace( ' ', '+' );
				};

				// Delete sidebar now that we have gained user consent
				if( confirmation ) {
					if( sidebarId !== '0' ) {
						spinner.toggleClass('ecs-visible');
						// spinner.fadeIn(200);
						api.deleteSidebar( sidebarId, processMethod, callback );
					} else {
						callback();
					}
				}
				return false;
			});
		};

		/**
		 * Register all events for the Management Screen
		 * =============================================
		 *
		 * Registers all of the js events for the 'Manage Sidebar
		 * Replacements' Screen.
		 * 
		 * @version 1.0.9
		 * 
		 */
		api.registerManagementEvents = function() {
			// Create New Sidebar Event
			// Attaches an event listener to the create new sidebar button.
			$( '#create_new_sidebar' ).on( 'click', function() {
				window.location =  $(this).attr( 'data-create-sidebar-url' );
				return false;
			});

			// Sidebar Delete Event
			// Attaches an event listener to each sidebar 'delete' link.
			$( '#sidebar-replacements-table a.sidebar-delete-link' ).on( 'click', function(){
				var confirmation = confirm( sidebarsL10n.deleteWarning );
				var spinner      = $(this).parent().next('.spinner' );
				var row          = $(this).closest('tr');
				var sidebarId    = $(this).attr( 'data-sidebar-reference' );

				var processMethod = function() {};
				var callback      = function() {
					row.fadeOut(200, function() {
						row.remove();
						// Update dialog screen if there are no sidebars
						if ( $('#sidebar-replacements-table tbody tr').length === 0 ) {

							// Fade out the table if there are no sidebars
							$( '#sidebar-replacements-table' ).fadeOut(500);

							// Update sidebar dialog if there are no sidebars
							$( '.sidebar-dialog .manage-label' ).fadeOut(200, function(){
								$( '.sidebar-dialog .new-label' ).fadeIn(300);
							});
						}
					});
				};

				// Delete sidebar now that we have gained user consent.
				if( confirmation ) {
					// spinner.fadeIn();
					spinner.toggleClass('ecs-visible');
					row.addClass('deleting', 200);
					api.deleteSidebar( sidebarId, processMethod, callback );

				}

				return false;
			});

			// Sidebar Delete All Event
			// Attaches an event listener to the 'delete all' link.
			$( '#delete_all_sidebars' ).on( 'click', function() {

				var confirmation = confirm( sidebarsL10n.deleteAllWarning );
				var spinners     = $( '#sidebar-replacements-table .spinner' );
				var rows         = $( '#sidebar-replacements-table tr' );

				var processMethod = function() {};
				var callback      = function() {
					rows.fadeOut(200);

					// Fade out the table if there are no sidebars
					$( '#sidebar-replacements-table, #delete_all_sidebars' ).fadeOut(500);

					// Update sidebar dialog if there are no sidebars
					$( '.sidebar-dialog .manage-label' ).fadeOut(200, function(){
						$( '.sidebar-dialog .new-label' ).fadeIn(300);
					});
				};

				// Delete all sidebars now that we have gained user consent
				if( confirmation ) {
					// spinners.fadeIn();
					spinner.toggleClass('ecs-visible');
					rows.addClass( 'deleting', 200 );
					api.deleteAllSidebars( processMethod, callback );
				}

				return false;
			});

			// Sidebar Replacement Change
			// Attaches an event listener to the replacement sidebar 
			// select menu for each sidebar. 
			$( '#sidebar-replacements-table select' ).on( 'change', function(){

				var select        = $(this);
				var row           = $(this).closest('tr');
				var spinner       = row.find( '.spinner' );
				var sidebarId     = $(this).attr( 'data-sidebar-reference' );
				var nonce         = $( '#ecs_edit_sidebar_instance_nonce' ).val();
				var replacementId = $(this).val();

				var dataObj = {
					'action': 'ecs_edit_sidebar_replacement',
					'sidebarId': sidebarId,
					'replacementId' : replacementId,
					'ecs_edit_sidebar_instance_nonce': nonce
				};

				// spinner.fadeIn();
				spinner.toggleClass('ecs-visible');
				row.addClass('success', 200);
				$.post( ajaxurl, dataObj, function() {

				}).done( function(){
					row.removeClass('success', 300);

					// Change Select text if sidebar has been deactivated for a better user experience
					if ( '0' === replacementId ) {
						select.children( 'option:selected' ).html( sidebarsL10n.activateSidebar );
					} else {
						select.children( 'option[value="0"]' ).html( sidebarsL10n.deactivateSidebar );
					}
					spinner.fadeOut(200);
				});
			});
		};

		//Run Plugin Init
		api.init();

	}; // END $.fn.themeSidebarMenu

}(jQuery, window, document));

/**============================================================
 * INITIALISE PLUGINS & JS ON DOCUMENT READY EVENT
 * ============================================================ */

	jQuery(document).ready(function($) {"use strict";
		$(this).themeSidebarMenu();
	});
