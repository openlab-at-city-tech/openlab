( function( $ ) {

	$( document ).ready( function() {
		var utils = window.GFAPCUtils;

		var $postDateSelector = 'select[name="' + utils.getSettingFieldName( 'postDate' ) + '"]';

		$( $postDateSelector ).on( 'change', function() {
			// Hide all options.
			$optionFields = utils.getPostDateOptionFields( $(this) ).each( function ( i, field ){
				$( field ).hide();
			});
			// Show selected option field.
			var optionFieldName = 'date_' + $( this ).val();
			utils.getPostDateOptionFieldByName( $( this ), optionFieldName ).show();
		} );

		// Use Select2.
		$( '#postSettings select:not(#postAuthor), #postThumbnail' ).select2();
		$( 'select[id="postMedia[]"]' ).select2( { placeholder: $( 'select[id="postMedia[]"]' ).attr( 'placeholder' ) } );

		// Add Select2 support to post meta mapping.
		gform.addAction( 'gform_fieldmap_add_row', function( obj, $elem, item ) {

			$elem.find( 'select' ).each( function() {

				var $select = $( this );

				// If there are more than 100 options, do not use Select2.
				if ( $select.find( 'option' ).length > 100 ) {
					return;
				}

				if ( ! $select.data( 'select2' ) ) {
					$select.select2();
				}

				if ( 'gf_custom' === $select.val() ) {
					$select.siblings( '.select2-container' ).hide();
				}

			} );

		} );

		// Add Select2 to post author field.
		$( '#postAuthor' ).select2( {
		    allowClear:  false,
		    placeholder: gform_advancedpostcreation_form_settings_strings.select_user,
			ajax:        {
				url:      ajaxurl,
				dataType: 'json',
				delay:    250,
				data:     function( params ) {
					return {
						action: 'gform_advancedpostcreation_author_search',
						nonce:  gform_advancedpostcreation_form_settings_strings.nonce_author,
						query:  params.term,
					}
				}
			}
		} );

	} );

} )( jQuery );
