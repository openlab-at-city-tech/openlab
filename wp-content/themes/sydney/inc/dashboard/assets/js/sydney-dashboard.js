/**
 * Sticky-kit v1.1.3 | MIT | Leaf Corcoran 2015 | http://leafo.net
 * 
 * @cc_on 
 * 
 */
(function(){var c,f;c=window.jQuery;f=c(window);c.fn.stick_in_parent=function(b){var A,w,J,n,B,K,p,q,L,k,E,t;null==b&&(b={});t=b.sticky_class;B=b.inner_scrolling;E=b.recalc_every;k=b.parent;q=b.offset_top;p=b.spacer;w=b.bottoming;null==q&&(q=0);null==k&&(k=void 0);null==B&&(B=!0);null==t&&(t="is_stuck");A=c(document);null==w&&(w=!0);L=function(a){var b;return window.getComputedStyle?(a=window.getComputedStyle(a[0]),b=parseFloat(a.getPropertyValue("width"))+parseFloat(a.getPropertyValue("margin-left"))+
parseFloat(a.getPropertyValue("margin-right")),"border-box"!==a.getPropertyValue("box-sizing")&&(b+=parseFloat(a.getPropertyValue("border-left-width"))+parseFloat(a.getPropertyValue("border-right-width"))+parseFloat(a.getPropertyValue("padding-left"))+parseFloat(a.getPropertyValue("padding-right"))),b):a.outerWidth(!0)};J=function(a,b,n,C,F,u,r,G){var v,H,m,D,I,d,g,x,y,z,h,l;if(!a.data("sticky_kit")){a.data("sticky_kit",!0);I=A.height();g=a.parent();null!=k&&(g=g.closest(k));if(!g.length)throw"failed to find stick parent";
v=m=!1;(h=null!=p?p&&a.closest(p):c("<div />"))&&h.css("position",a.css("position"));x=function(){var d,f,e;if(!G&&(I=A.height(),d=parseInt(g.css("border-top-width"),10),f=parseInt(g.css("padding-top"),10),b=parseInt(g.css("padding-bottom"),10),n=g.offset().top+d+f,C=g.height(),m&&(v=m=!1,null==p&&(a.insertAfter(h),h.detach()),a.css({position:"",top:"",width:"",bottom:""}).removeClass(t),e=!0),F=a.offset().top-(parseInt(a.css("margin-top"),10)||0)-q,u=a.outerHeight(!0),r=a.css("float"),h&&h.css({width:L(a),
height:u,display:a.css("display"),"vertical-align":a.css("vertical-align"),"float":r}),e))return l()};x();if(u!==C)return D=void 0,d=q,z=E,l=function(){var c,l,e,k;if(!G&&(e=!1,null!=z&&(--z,0>=z&&(z=E,x(),e=!0)),e||A.height()===I||x(),e=f.scrollTop(),null!=D&&(l=e-D),D=e,m?(w&&(k=e+u+d>C+n,v&&!k&&(v=!1,a.css({position:"fixed",bottom:"",top:d}).trigger("sticky_kit:unbottom"))),e<F&&(m=!1,d=q,null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.detach()),c={position:"",width:"",top:""},a.css(c).removeClass(t).trigger("sticky_kit:unstick")),
B&&(c=f.height(),u+q>c&&!v&&(d-=l,d=Math.max(c-u,d),d=Math.min(q,d),m&&a.css({top:d+"px"})))):e>F&&(m=!0,c={position:"fixed",top:d},c.width="border-box"===a.css("box-sizing")?a.outerWidth()+"px":a.width()+"px",a.css(c).addClass(t),null==p&&(a.after(h),"left"!==r&&"right"!==r||h.append(a)),a.trigger("sticky_kit:stick")),m&&w&&(null==k&&(k=e+u+d>C+n),!v&&k)))return v=!0,"static"===g.css("position")&&g.css({position:"relative"}),a.css({position:"absolute",bottom:b,top:"auto"}).trigger("sticky_kit:bottom")},
y=function(){x();return l()},H=function(){G=!0;f.off("touchmove",l);f.off("scroll",l);f.off("resize",y);c(document.body).off("sticky_kit:recalc",y);a.off("sticky_kit:detach",H);a.removeData("sticky_kit");a.css({position:"",bottom:"",top:"",width:""});g.position("position","");if(m)return null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.remove()),a.removeClass(t)},f.on("touchmove",l),f.on("scroll",l),f.on("resize",y),c(document.body).on("sticky_kit:recalc",y),a.on("sticky_kit:detach",H),setTimeout(l,
0)}};n=0;for(K=this.length;n<K;n++)b=this[n],J(c(b));return this}}).call(this);

(function ($) {

	'use strict';

	$(document).ready(function () {

		// Globals
		var $body = $('body');

		// Dashboard hero re-position
		var $header = $('.wp-header-end');
		var $notice = $('.sydney-dashboard-notice');

		if ($header.length && $notice.length) {
			$header.after($notice);
			$notice.addClass('show');
		}

		// Dashboard hero dismissable
		var $dismissable = $('.sydney-dashboard-dismissable');

		if ($dismissable.length) {

			$dismissable.on('click', function () {

				$dismissable.parent().hide();

				$.post(window.sydney_dashboard.ajax_url, {
					action: 'sydney_dismissed_handler',
					nonce: window.sydney_dashboard.nonce,
					notice: $dismissable.data('notice'),
				});

			});

		}

		//Templates builder
		const $template_save = $('#save-templates');

		if ($template_save.length) {

			$template_save.on('click', function (e) {

				e.preventDefault();

				$( this ).html( '<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.saving );

				var data = [];

				$('#template-builder .template-item').each(function() {

					var id 				= $(this).data('id');
					var template_name 	= $(this).find('input[name="template_name"]').val();
					var conditions 		= $(this).find('input[name="conditions"]').val();

					var header 			= $(this).find('input[name="header"]').val();
					var header_builder 	= $(this).find('.template-part.header').data('page-builder');
					
					var page_title 			= $(this).find('input[name="page_title"]').val();
					var page_title_builder 	= $(this).find('.template-part.page_title').data('page-builder');

					var content 		= $(this).find('input[name="content"]').val();
					var content_builder = $(this).find('.template-part.content').data('page-builder');

					var footer 			= $(this).find('input[name="footer"]').val();
					var footer_builder 	= $(this).find('.template-part.footer').data('page-builder');

					var template_item = {
						id: id,
						template_name: template_name,
						header: header,
						header_builder: header_builder,
						page_title: page_title,
						page_title_builder: page_title_builder,
						content: content,
						content_builder: content_builder,
						footer: footer,
						footer_builder: footer_builder,
						conditions: conditions
					};

					data.push(template_item);
				});		
			

				$.post( window.sydney_dashboard.ajax_url, {
					action: 'sydney_template_builder_data',
					data: data,
					nonce: window.sydney_dashboard.nonce,
				}, function ( response ) {
					if( response.success ) {
						$template_save.html(window.sydney_dashboard.i18n.saved);

						$('#save-templates').addClass('saved');

						setTimeout(function () {
							$template_save.html(window.sydney_dashboard.i18n.save);
							$template_save.blur();
						},2000);
					}
				});
			});
		}		
		
			// Add new template
			const $add_template = $('#add-new-template');

			if ($add_template.length) {

				$('#add-new-template').on('click', function() {
					var $template_item = $('#template-builder .template-item').first().clone();

					$template_item.attr('data-id', 'sydney-template-' + Math.random().toString(36).slice(2));
					$template_item.find('h4').replaceWith('<input type="text" placeholder="Template name" name="template_name" value="">');
					$template_item.find('.template-options').show();

					$template_item.find('input').val(''); // Clear input values
					$template_item.find('.template-part').attr('data-part-active', 'inactive');

					$template_item.appendTo('#template-builder');

					save_notice();
				});

			}


			$(document).on('click', '.part-options-toggle', function() {
				$( this ).parent().find('.part-options').fadeToggle(100);
				$( this) .find('span').toggleClass('dashicons-ellipsis dashicons-no-alt');
			});

			$(document).on('click', '.template-part-inner .not-selected', function() {
				$( this ).parent().parent().find('.part-options').fadeToggle(100);
				$( this).parent().parent().next('.part-options-toggle').find('span').toggleClass('dashicons-ellipsis dashicons-no-alt');
			});			

			//delete
			$(document).on('click', '.template-options .delete-template', function(e) {
				e.preventDefault();
				
				$(this).closest('.template-item').fadeOut(300, function() {
					$(this).remove();
				} );

				save_notice();
			});

			$( '#template-builder' ).sortable({
				opacity: 0.6,
				revert: true,
				animation: 150,
				cursor: 'move',
				cancel: '.not-sortable',
				handle: '.sort-handle',
			});

			//duplicate
			$(document).on('click', '.template-options .duplicate-template', function(e) {
				e.preventDefault();
				var $template_item = $(this).closest('.template-item').clone();
				$template_item.attr('data-id', 'sydney-template-' + Math.random().toString(36).slice(2)); //replace id

				$template_item.insertAfter($(this).closest('.template-item')).hide().fadeIn(300);

				save_notice();
			});


			//select existing
			$(document).on('click', '.part-options .select-existing', function() {
				$(this).parent().parent().parent().find('.existing-parts-wrapper').fadeToggle(50);		

				$(document).mouseup(function(e) {
					var container = $( '.existing-parts-wrapper' );
					if ( !container.is(e.target) && container.has(e.target).length === 0 && !$(e.target).hasClass('select-existing') ) {
						container.fadeOut(50);
					}
				} );
			});

			//select existing
			$(document).on('change', '.existing-parts-select', function() {
				var $template_item 	= $(this).closest('.template-item');
				var id 				= $(this).val();
				var part_type 		= $(this ).parents('.template-part').data('part-type');
				var page_builder 	= $(this).find(':selected').data('page-builder');

				$template_item.find('input[name="' + part_type + '"]').val(id);

				if ( id ) {
					$(this ).parents('.template-part').attr('data-part-active', 'active');
					$(this).parents('.template-part').attr('data-page-builder', page_builder);
					$(this ).parents('.template-part').find( '.part-title' ).find( '.selected' ).show().siblings().hide();
				}

				save_notice();
			});

			//reset
			$(document).on('click', '.part-options .reset', function() {
				var $template_item 	= $(this).closest('.template-item');
				var part_type 		= $(this ).parents('.template-part').data('part-type');

				$template_item.find('input[name="' + part_type + '"]').val('');

				$(this ).parents('.template-part').attr('data-part-active', 'inactive');
				$(this ).parents('.template-part').find( '.part-title' ).find( '.not-selected' ).show().siblings().hide();

				$(this ).parent().fadeToggle(100);
				$(this ).parents('.template-part').find('.part-options-toggle span').toggleClass('dashicons-ellipsis dashicons-no-alt');

				save_notice();
			});

			//select page builder
			$(document).on('click', '.part-options .select-page-builder', function() {
				var $template_item 	= $(this).closest('.template-item');
				$(this).parent().parent().parent().find('.page-builder-wrapper').fadeToggle(50);

				$(document).mouseup(function(e) {
					var container = $( '.page-builder-wrapper' );
					if ( !container.is(e.target) && container.has(e.target).length === 0 && !$(e.target).hasClass('select-page-builder') ) {
						container.fadeOut(50);
					}
				} );
			});

			//create new
			$(document).on('click', '.page-builder-wrapper .create-new', function() {

				var $template_item = $(this).closest('.template-item');
				var id 	= $template_item.data('id');
				var part 		= $(this ).parents('.template-part');
				var part_type 	= part.data('part-type');

				var $modalContainer = $( '.sydney-elementor-iframe-wrapper' );
                var editorIframe 	= $modalContainer.find( '.sydney-elementor-iframe' );

				var page_builder = $(this).data('page-builder');

				//spinner
				var current_text = $(this).html();
				$(this).html( current_text + '<i class="dashicons dashicons-update-alt page-builder-spinner" style="position:relative;top:10px;left:5px;"></i>' );

				$.post( window.sydney_dashboard.ajax_url, {
					action: 'insert_template_part_callback',
					key: id,
					part_type: part_type,
					page_builder: page_builder,
					nonce: window.sydney_dashboard.nonce,
				}, function ( response ) {
					if( response.success ) {
						$template_item.find('input[name="' + part_type + '"]').val(response.data.id);

						if ( page_builder == 'elementor' ) {

							editorIframe.attr("src", response.data.url);
		
							editorIframe.on("load", function () {
								$modalContainer.show();
								$modalContainer.css("z-index", "9999");
							});
							
							$(document).on('click', '.sydney-editor-close', function() {
								$modalContainer.hide();
								$modalContainer.css("z-index", "0");
							} );

						} else {
							window.open(response.data.url, '_blank');
						}


						//Handle save notice
						save_notice();

						//set the page builder
						part.attr('data-page-builder', page_builder );

						//this part is active
						setTimeout(function() {
							part.attr('data-part-active', 'active');
							part.find( '.part-title' ).find( '.selected' ).show().siblings().hide();
							$('.page-builder-spinner').remove();
						}, 3000);
					}
				});

				return false;

			});

			//handle edit
			$(document).on('click', '.part-options .edit-part', function() {
				var part = $(this ).parents('.template-part');
				var part_type = part.data('part-type');
				var edit_text = $(this).text();

				var id = part.find('input[name="' + part_type + '"]').val();

				var $modalContainer = $( '.sydney-elementor-iframe-wrapper' );
				var editorIframe 	= $modalContainer.find( '.sydney-elementor-iframe' );

				var page_builder = part.data('page-builder');

				$(this).html('<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.loading);
				
				$.post( window.sydney_dashboard.ajax_url, {
					action: 'edit_template_part_callback',
					key: id,
					part_type: part_type,
					page_builder: page_builder,
					nonce: window.sydney_dashboard.nonce,
				}, function ( response ) {
					if( response.success ) {
						if ( page_builder == 'elementor' ) {

							editorIframe.attr("src", response.data.url);
		
							editorIframe.on("load", function () {
								$modalContainer.show();
								$modalContainer.css("z-index", "9999");
							});
							
							$(document).on('click', '.sydney-editor-close', function() {
								$modalContainer.hide();
								$modalContainer.css("z-index", "0");
							} );

						} else {
							window.open(response.data.url, '_blank');
						}
					}

					setTimeout(function() {
						part.find('.edit-part').html(edit_text);
					}, 3000);
				});

			} );

			//save notice
			function save_notice() {
				//return if free
				var $container = $('.sydney-dashboard-container');
				if ( $container.data('theme') == 'sydney' ) {
					return;
				}

				$('#save-templates').removeClass('saved');
				$(window).on('beforeunload', function() {
					if ( !$('#save-templates').hasClass('saved') ) {
						return window.sydney_dashboard.i18n.unsaved_changes;
					}
				});
			}


		//Display conditions
		$(document).on('sydney-display-conditions-select2-initalize', function (event, item) {
			var $item = $(item);
			var $control = $item.closest('.sydney-display-conditions-control');
			var $typeSelectWrap = $item.find('.sydney-display-conditions-select2-type');
			var $typeSelect = $typeSelectWrap.find('select');
			var $conditionSelectWrap = $item.find('.sydney-display-conditions-select2-condition');
			var $conditionSelect = $conditionSelectWrap.find('select');
			var $idSelectWrap = $item.find('.sydney-display-conditions-select2-id');
			var $idSelect = $idSelectWrap.find('select');
			$typeSelect.select2({
			  width: '100%',
			  minimumResultsForSearch: -1
			});
			$typeSelect.on('select2:select', function (event) {
			  $typeSelectWrap.attr('data-type', event.params.data.id);
			});
			$conditionSelect.select2({
			  width: '100%'
			});
			$conditionSelect.on('select2:select', function (event) {
			  var $element = $(event.params.data.element);
		
			  if ($element.data('ajax')) {
				$idSelectWrap.removeClass('hidden');
			  } else {
				$idSelectWrap.addClass('hidden');
			  }

			  $idSelect.val(null).trigger('change');
			});
			var isAjaxSelected = $conditionSelect.find(':selected').data('ajax');

			if (isAjaxSelected) {
			  $idSelectWrap.removeClass('hidden');
			}
		
			$idSelect.select2({
			  width: '100%',
			  placeholder: '',
			  allowClear: true,
			  minimumInputLength: 1,
			  ajax: {
				url: window.sydney_dashboard.ajax_url,
				dataType: 'json',
				delay: 250,
				cache: true,
				data: function data(params) {
				  return {
					action: 'sydney_templates_display_conditions_select_ajax',
					term: params.term,
					nonce: window.sydney_dashboard.nonce,
					source: $conditionSelect.val()
				  };
				},
				processResults: function processResults(response, params) {

				  if (response.success) {
					return {
					  results: response.data
					};
				  }
		
				  return {};
				}
			  }
			});
		  });
		  $(document).on('click', '.sydney-display-conditions-modal-toggle', function (event) {
			event.preventDefault();
			var $button = $(this);
			var template = wp.template('sydney-display-conditions-template');
			var $control = $button.closest('.sydney-display-conditions-control');
			var $modal = $control.find('.sydney-display-conditions-modal');
		
			if (!$modal.data('initialized')) {
			  $control.append(template($control.data('condition-settings')));
			  var $items = $control.find('.sydney-display-conditions-modal-content-list-item').not('.hidden');
		
			  if ($items.length) {
				$items.each(function () {
				  $(document).trigger('sydney-display-conditions-select2-initalize', this);
				});
			  }
		
			  $modal = $control.find('.sydney-display-conditions-modal');
			  $modal.data('initialized', true);
			  $modal.addClass('open');
			} else {
			  $modal.toggleClass('open');
			}
		  });
		  $(document).on('click', '.sydney-display-conditions-modal', function (event) {
			event.preventDefault();
			var $modal = $(this);
		
			if ($(event.target).is($modal)) {
			  $( '.sydney-display-conditions-modal' ).removeClass('open');
			}
		  });
		  $(document).on('click', '.sydney-display-conditions-modal-add', function (event) {
			event.preventDefault();
			var $button = $(this);
			var $control = $button.closest('.sydney-display-conditions-control');
			var $modal = $control.find('.sydney-display-conditions-modal');
			var $list = $modal.find('.sydney-display-conditions-modal-content-list');
			var $item = $modal.find('.sydney-display-conditions-modal-content-list-item').first().clone();
			var conditionGroup = $button.data('condition-group');
			$item.removeClass('hidden');
			$item.find('.sydney-display-conditions-select2-condition').not('[data-condition-group="' + conditionGroup + '"]').remove();
			$list.append($item);
			$(document).trigger('sydney-display-conditions-select2-initalize', $item);
		  });
		  $(document).on('click', '.sydney-display-conditions-modal-remove', function (event) {
			event.preventDefault();
			var $item = $(this).closest('.sydney-display-conditions-modal-content-list-item');
			$item.remove();
		  });
		  $(document).on('click', '.sydney-display-conditions-modal-save', function (event) {
			event.preventDefault();
			var data = [];
			var $button = $(this);
			var $control = $button.closest('.sydney-display-conditions-control');
			var $modal = $control.find('.sydney-display-conditions-modal');
			var $textarea = $control.find('.sydney-display-conditions-textarea');
			var $items = $modal.find('.sydney-display-conditions-modal-content-list-item').not('.hidden');
			$items.each(function () {
			  var $item = $(this);
			  data.push({
				type: $item.find('select[name="type"]').val(),
				condition: $item.find('select[name="condition"]').val(),
				id: $item.find('select[name="id"]').val()
			  });
			});
			$textarea.val(JSON.stringify(data)).trigger('change');
		});		

		// Tabs Navigation
		const tabs = $( '.sydney-dashboard-tabs-nav' );
		if( tabs.length ) {

			tabs.each(function(){
				const tabWrapperId = $( this ).data( 'tab-wrapper-id' );

				$( this ).find( '.sydney-dashboard-tabs-nav-link' ).on( 'click', function(e){
					e.preventDefault();

					const 
						tabsNavLink  = $( this ).closest( '.sydney-dashboard-tabs-nav' ).find( '.sydney-dashboard-tabs-nav-link' ),
						to           = $( this ).data( 'tab-to' );

					// Tab Nav Item
					tabsNavLink.each( function(){
						$( this ).closest( '.sydney-dashboard-tabs-nav-item' ).removeClass( 'active' );
					});
					
					$( this ).closest( '.sydney-dashboard-tabs-nav-item' ).addClass( 'active' );

					// Tab Content
					const tabContentWrapper = $( '.sydney-dashboard-tab-content-wrapper[data-tab-wrapper-id="'+ tabWrapperId +'"]' );
					tabContentWrapper.find( '> .sydney-dashboard-tab-content' ).removeClass( 'active' );
					tabContentWrapper.find( '> .sydney-dashboard-tab-content[data-tab-content-id="'+ to +'"]' ).addClass( 'active' );

					// Recalculate sticky
					if( to === 'home' ) {
						$( document.body ).trigger( 'sticky_kit:recalc' );
					}
				} );
			});

		}

		// License button
		var $license = $('.sydney-license-button');

		if ($license.length) {

			$license.on('click', function (e) {

				var $button = $(this);

				if ($button.data('type') === 'activate') {
					$button.html('<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.activating);
				} else {
					$button.html('<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.deactivating);
				}

			});

		}

		// Install plugin
		var $plugin = $('.sydney-dashboard-plugin-ajax-button');

		if ($plugin.length) {

			$plugin.on('click', function (e) {

				e.preventDefault();

				var $button = $(this);
				var href = $button.attr('href');
				var slug = $button.data('slug');
				var type = $button.data('type');
				var path = $button.data('path');
				var caption = $button.html();

				$button.addClass('sydney-ajax-progress');
				$button.parent().siblings('.sydney-dashboard-hero-warning').remove();

				if (type === 'install') {
					$button.html('<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.installing);
				} else if (type === 'activate') {
					$button.html('<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.activating);
				} else if (type === 'deactivate') {
					$button.html('<i class="dashicons dashicons-update-alt"></i>' + window.sydney_dashboard.i18n.deactivating);
				}

				$.post(window.sydney_dashboard.ajax_url, {
					action: 'sydney_plugin',
					nonce: window.sydney_dashboard.nonce,
					slug: slug,
					type: type,
					path: path,
				}, function (response) {

					if (response.success) {
						if( $button.hasClass( 'sydney-ajax-success-redirect' ) ) {
							setTimeout(function () {
								$button.html(window.sydney_dashboard.i18n.redirecting);

								setTimeout(function () {
									window.location = href;
								}, 1000);
							}, 500);

							return false;
						}
						if( type === 'install' ) {
							$button
								.html( window.sydney_dashboard.i18n.deactivate )
								.removeClass( 'sydney-dashboard-link-info' )
								.addClass( 'sydney-dashboard-link-danger' )
								.removeClass( 'loading' )
								.data( 'type', 'deactivate' );
						} else if( type === 'deactivate' ) {
							$button
								.html( window.sydney_dashboard.i18n.activate )
								.removeClass( 'sydney-dashboard-link-danger' )
								.addClass( 'sydney-dashboard-link-success' )
								.removeClass( 'loading' )
								.data( 'type', 'activate' );
						} else {
							$button
								.html( window.sydney_dashboard.i18n.deactivate )
								.removeClass( 'sydney-dashboard-link-success' )
								.addClass( 'sydney-dashboard-link-danger' )
								.removeClass( 'loading' )
								.data( 'type', 'deactivate' );
						}

						$button.removeClass( 'sydney-ajax-progress' );

					} else if (response.data) {

						$button.html(caption);
						$button.parent().after('<div class="sydney-dashboard-hero-warning">' + response.data + '</div>');

					} else {

						$button.html(caption);
						$button.parent().after('<div class="sydney-dashboard-hero-warning">' + window.sydney_dashboard.i18n.failed_message + '</div>');

					}

				}).fail(function (xhr, textStatus, e) {

					$button.html(caption);
					$button.parent().after('<div class="sydney-dashboard-hero-warning">' + window.sydney_dashboard.i18n.failed_message + '</div>');

				});

			});

		}

		// Activate Module
		const $activationModuleButton = $('.sydney-dashboard-module-activation');

		if ( $activationModuleButton.length ) {
			$activationModuleButton.on('click', function (e) {
				e.preventDefault();
				const 
					$this          = $( this ),
					moduleId 	   = $this.data( 'module-id' ),
					activate   	   = $this.data( 'module-activate' ) ? true : false,
					loadingMessage = activate ? window.sydney_dashboard.i18n.activating : window.sydney_dashboard.i18n.deactivating;

				$this
					.html( '<i class="dashicons dashicons-update-alt"></i>' + loadingMessage )
					.removeClass( 'sydney-dashboard-link-success' )
					.addClass( 'loading' );

				$.post( window.sydney_dashboard.ajax_url, {
					action: 'sydney_module_activation_handler',
					nonce: window.sydney_dashboard.nonce,
					module: moduleId,
					activate: activate
				}, function ( response ) {
					if( response.success ) {

						if( activate ) {
							$this
								.html( window.sydney_dashboard.i18n.deactivate )
								.removeClass( 'sydney-dashboard-link-success' )
								.addClass( 'sydney-dashboard-link-danger' )
								.removeClass( 'loading' )
								.data( 'module-activate', false );

							$this
								.parent()
								.find( '.sydney-dashboard-customize-link' )
								.removeClass( 'bt-d-none' );

							if( moduleId === 'templates' ) {
								window.location.reload();
							}
						} else {
							$this
								.html( window.sydney_dashboard.i18n.activate )
								.removeClass( 'sydney-dashboard-link-danger' )
								.addClass( 'sydney-dashboard-link-success' )
								.removeClass( 'loading' )
								.data( 'module-activate', true );

							$this
								.parent()
								.find( '.sydney-dashboard-customize-link' )
								.addClass( 'bt-d-none' );

							if( moduleId === 'templates' ) {
								window.location.reload();
							}								
						}
					}
				});
			});
		}

		// Activate All Modules
		const $activationAllModulesButton = $('.sydney-dashboard-module-activation-all');

		if ( $activationAllModulesButton.length ) {
			$activationAllModulesButton.on( 'click', function(e){
				e.preventDefault();

				const 
					$this          = $( this ),
					activate   	   = $this.data( 'module-activate' ) ? true : false,
					loadingMessage = activate ? window.sydney_dashboard.i18n.activating : window.sydney_dashboard.i18n.deactivating;

				$this
					.html( loadingMessage )
					.addClass( 'loading' );

					$.post( window.sydney_dashboard.ajax_url, {
						action: 'sydney_module_activation_all_handler',
						nonce: window.sydney_dashboard.nonce,
						activate: activate
					}, function ( response ) {
						if( response.success ) {
							window.location.reload();
						}
					});
			} );
		}

		// Sticky Sidebar
		$( '.sydney-dashboard-sticky-wrapper' ).stick_in_parent({
			offset_top: 54
		});

		// Notifications Sidebar
		const $notificationsSidebar = $( '.sydney-dashboard-notifications-sidebar' );
		if( $notificationsSidebar.length ) {
		
			// Open/Toggle Sidebar
			$( '.sydney-dashboard-theme-notifications' ).on( 'click', function(e){
				e.preventDefault();

				const latestNotificationDate = $notificationsSidebar.find( '.sydney-dashboard-notification:first-child .sydney-dashboard-notification-date' ).data( 'raw-date' );

				$notificationsSidebar.toggleClass( 'opened' );

				if( ! $( this ).hasClass( 'read' ) ) {
					$.post( window.sydney_dashboard.ajax_url, {
						action: 'sydney_notifications_read',
						latest_notification_date: latestNotificationDate,
						nonce: window.sydney_dashboard.nonce,
					}, function ( response ) {
						if( response.success ) {
							setTimeout(function(){
								$( '.sydney-dashboard-theme-notifications' ).addClass( 'read' );
							}, 2000);
						}
					});
				}
			} );

			$( window ).on( 'scroll', function(){
				if( window.pageYOffset > 60 ) {
					$notificationsSidebar.addClass( 'scrolled' );
				} else {
					$notificationsSidebar.removeClass( 'scrolled' );
				}
			} );

			// Close Sidebar
			$( '.sydney-dashboard-notifications-sidebar-close' ).on( 'click', function(e){
				e.preventDefault();

				$notificationsSidebar.addClass( 'closing' );
				setTimeout(function(){
					$notificationsSidebar.removeClass( 'opened' );
					$notificationsSidebar.removeClass( 'closing' );
				}, 300);
			} );

		}

	});

})(jQuery);
