/* global jQuery, ajaxurl, gform_advancedpostcreation_form_settings_strings */

var GFTaxonomyMap = function( options ) {
	var self = this;

	self.options = options;
	self.UI = jQuery( '#gaddon-setting-row-' + self.options.fieldName );

	self.init = function() {
		self.options.formFields = JSON.parse( self.options.formFields );
		self.options.preloadedTerms = JSON.parse( self.options.preloadedTerms );

		self.bindEvents();
		self.setupData();
		self.setupRepeater();
	};

	self.bindEvents = function() {
		self.UI.on( 'change', 'select[name="_gaddon_setting_' + self.options.keyFieldName + '"]', function() {
			self.setupRow( jQuery( this ).parent().parent(), {} );
		} );

		self.UI.closest( 'form' ).on( 'submit', function( event ) {
			jQuery( '[name^="_gaddon_setting_' + self.options.fieldName + '_"]' ).each( function( i ) {
				jQuery( this ).removeAttr( 'name' );
			} );
		} );
	};

	self.setupData = function() {
		var data = jQuery( '#' + self.options.fieldId ).val();
		self.data = data ? jQuery.parseJSON( data ) : null;

		if ( ! self.data ) {
			self.data = [ {
				key: '',
				value: '',
				custom_value: '',
			} ];
		}
	};

	self.setupRow = function( $rootElm, item ) {

		// Get fields.
		var $key = $rootElm.find( 'select[name="_gaddon_setting_' + self.options.keyFieldName + '"]' ),
			$value = $rootElm.find( 'select[name="_gaddon_setting_' + self.options.valueFieldName + '"]' ),
			$customValue = $value.siblings( '.custom-value-container' );

		// Enable Select2 for key field.
		$key.select2( { minimumResultsForSearch: Infinity } );

		// Set key element as Select2 container.
		var $keyElm = $key.siblings( '.select2-container' );

		// Set value element.
		var $valueElm = $value.data( 'select2' ) ? $value.siblings( '.select2-container' ):$value;

		// If key is set to a custom value, hide value select.
		if ( 'gf_custom' === $key.val() || 'gf_custom' === item.value ) {
			$valueElm.hide();
			$customValue.show();
		} else {
			// Hide custom value.
			$customValue.hide();

			// Destroy Select2.
			if ( $value.data( 'select2' ) ) {
				$value.select2( 'destroy' );
			}

			// Remove existing options.
			$value.find( 'option' ).each( function() {

				if ( this.value.length == 0 ) {
					return;
				}

				jQuery( this ).remove();
			} );

			switch ( $key.val() ) {
				case 'field':
					// Remove disabled attribute.
					$value.removeAttr( 'disabled' );

					// Define variable to store selected item.
					var selected = '';

					// Loop through form fields.
					jQuery.each( self.options.formFields, function( i, field ) {
						// Exclude Post Category field.
						if ( 'category' !== self.options.taxonomy && 'post_category' === field.type ) {
							return;
						}

						$value.append( jQuery( '<option>', {
							value: field.value,
							text: field.label,
						} ) );

						// If this is the selected item, assign it to the selected variable.
						if ( item.value == field.value ) {
							selected = item.value;
						}
					} );

					// Set selected item.
					if ( selected.length > 0 ) {
						$value.val( selected );
					}

					// Initialize Select2.
					$value.select2();
					break;

				case 'term':
					// Remove disable attribute.
					$value.removeAttr( 'disabled' );

					// If the current item exists, preload the selected item.
					if ( self.options.preloadedTerms[item.value] ) {
						// Add option to select.
						$value.append( jQuery( '<option>', {
							value: item.value,
							text: self.options.preloadedTerms[item.value],
						} ) );

						// Set select value to item.
						$value.val( item.value );
					}

					// Initialize Select2.
					$value.select2( {
						ajax: {
							url: ajaxurl,
							dataType: 'json',
							delay: 250,
							data: function( params ) {
								return {
									action: 'gform_advancedpostcreation_taxonomy_search',
									nonce: gform_advancedpostcreation_form_settings_strings.nonce_search,
									taxonomy: self.options.taxonomy,
									query: params.term,
								};
							},
						},
					} );

					// Add selected term to preloaded term cache.
					$value.on( 'select2:selecting', function( e ) {
						// Get selected term.
						var selectedTerm = e.params.args.data;

						// If selected term is Enter Term, display custom value container.
						if ( selectedTerm.id === 'gf_custom' ) {
							$value.select2( 'destroy' ).hide();
							$customValue.show();
						}

						// If selected term is not cached, cache it.
						if ( ! self.options.preloadedTerms[ selectedTerm.id ] ) {
							self.options.preloadedTerms[ selectedTerm.id ] = selectedTerm.text;
						}
					} );

					break;

				default:
					// Disable selections.
					$value.attr( 'disabled', 'disabled' );

					// Initialize Select2.
					$value.select2();
					break;
			}
		}
	};

	// Setup jQuery repeater.
	self.setupRepeater = function() {
		var limit = self.options.limit > 0 ? self.options.limit : 0;

		self.UI.find( 'tbody.repeater' ).repeater( {
			limit: limit,
			items: self.data,
			addButtonMarkup: '<i class="gficon-add"></i>',
			removeButtonMarkup: '<i class="gficon-subtract"></i>',
			callbacks: {
				add: function( obj, $elem, item ) {
					self.setupRow( $elem, item );
				},
				save: function( obj, data ) {
					jQuery( '#' + self.options.fieldId ).val( JSON.stringify( data ) );
				},
			},
		} );
	};

	return self.init();
};
