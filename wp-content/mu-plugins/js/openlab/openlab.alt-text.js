(function($){
 	$(document).ready(function(){
 		var selection,
 		  $altInput,
 		  $mediaInsert,
 		  $toolbarEl;

		var altInputClassicSelector = 'label[data-setting="alt"] input';
		var altInputBlockSelector = '#attachment-details-alt-text';

 		var removeWarning = function() {
 			$toolbarEl.find('.alt-tag-warning').remove();
 		}

 		var disableSubmit = function() {
 			$mediaInsert.attr('disabled', 'disabled');
 		}

 		var enableSubmit = function() {
 			$mediaInsert.removeAttr('disabled');
 		}

		var bindInputEvents = function() {
			setTimeout(
				function(){
					var $altInput = $(altInputClassicSelector + ',' + altInputBlockSelector);
					console.log($altInput);
					if ( $altInput.length > 0 ) {
						$altInput.on('keyup', checkForAltText);
						checkForAltText();
					}
				},
				500
			);
		}

 		var bindSelectionEvents = function() {
 			selection = wp.media.frame.state().get('selection');

			// Alt Text input will exist only if pre-selected, ie when coming from Upload Files.
			bindInputEvents();

 			$toolbarEl = wp.media.frame.toolbar.view.$el;

			// Selectors: Classic, Block.
 			$mediaInsert = $toolbarEl.find('button.media-button-insert,button.media-button-select');

			// Account for changed selections.
 			selection.on( 'selection:single selection:multiple', bindInputEvents );

 			var uploaderView = wp.media.frame.views.get('.media-frame-uploader')[0]
 			uploaderView.on('ready', function() {
 				uploaderView.uploader.success = function( attData ) {
 					visibleSidebarCallback();
 					wp.media.frame.views.get('.media-frame-content')[0].sidebar.$el.addClass( 'visible' );
 				}
 			})
 		}

 		var checkForAltText = function() {
			console.log('checking for alt text');
 			var hasAltText = true;
 			var imageIsSelected = false;

 			selection.each(function(item){
 				if ( ! hasAltText ) {
 					return;
 				}

 				if ( 'image' !== item.attributes.type ) {
 					return;
 				} else {
 					imageIsSelected = true;
 				}

 				if ( ! item.attributes.hasOwnProperty( 'alt' ) ) {
 					hasAltText = false;
 					return;
 				}

 				var $altInput = $(altInputBlockSelector);
				if ( $altInput.length === 0 ) {
					$altInput = $(altInputClassicSelector);
				}

 				hasAltText = $altInput.length > 0 && 0 !== $altInput.val().length;
 			});

 			// Existing warning should be removed in all cases.
 			removeWarning();

 			// Nothing more to do.
 			if ( ! imageIsSelected ) {
 				return;
 			}

 			if ( hasAltText ) {
 				enableSubmit();

 			} else {
 				var warning = '<div id="alt-tag-warning" class="alt-tag-warning">You must supply alt text before inserting this image.</div>';

 				$toolbarEl.find('.media-frame-toolbar .media-toolbar-primary button').before(warning);
 				var sidebar = wp.media.frame.views.get('.media-frame-content')[0].sidebar;

				// Selectors: Classic, Block.
 				sidebar.$el.find('label.setting[data-setting="alt"], span.setting[data-setting="alt"]').addClass('has-error');

 				disableSubmit();
 			}
 		}

 		// Bind our events to successful uploads.
 		$(document).ready(function(){
 			if ( typeof wp.Uploader === 'function' ) {
 				$.extend( wp.Uploader.prototype, {
 					success: function( attachment ) {
 						// Selection events must be rebound after redraw.
						setTimeout(function(){
							bindSelectionEvents();

							checkForAltText();
						},500);
 					}
 				} );
 			}
 		});

 		// Bind to the 'selection' events.
 		wp.media.view.Modal.prototype.on( 'ready', function() {

			// Have to look for DOM clicks when changing tabs.
 			var router = wp.media.frame.views.get('.media-frame-router')[0];
			router.$el.find('button').on('click', function(){
				removeWarning();
				bindInputEvents();
			});

 			wp.media.view.Modal.prototype.on( 'open', bindSelectionEvents );
 		});
 	});
}(jQuery))
