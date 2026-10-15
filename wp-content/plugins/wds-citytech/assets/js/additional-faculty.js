window.wp = window.wp || {};

(function($){
	var OL_Addl_Faculty = function() {
		var $ac, $existing, nonce;

		$( document ).ready( function() {
			// Toggle hide-if-js and hide-if-no-js.
			$( '.hide-if-js' ).hide();
			$( '.hide-if-no-js' ).show();

			$ac = $( '#additional-faculty-autocomplete' );
			nonce = $( '#_ol_faculty_autocomplete_nonce' ).val();

			$ac.autocomplete( {
				source: ajaxurl + '?action=openlab_additional_faculty_autocomplete&nonce=' + nonce,
				minLength: 2,
				select: function( event, ui ) {
					// No dupes.
					if ( 0 == $( '#addl-faculty-' + ui.item.value ).length ) {
						create_list_item( ui.item.value, ui.item.label );
					}

					$ac.val( '' );
					return false;
				}
			} );

			// Init existing items.
			$existing = OL_Addl_Faculty_Existing;
			$.each( $existing, function( k, v ) {
				create_list_item( v.value, v.label );
			} );

			// Bind remove actions by delegation.
			$( '#additional-faculty-list' ).on( 'click', '.addl-faculty-remove', function( event ) {
				event.preventDefault();
				$( event.target ).closest( '.addl-faculty-member' ).remove();
			} );
		} );

		/**
		 * Generate a user-related list item.
		 */
		function create_list_item( nicename, label ) {
			var $li = $( '<li class="addl-faculty-member">' )
				.attr( { id: 'addl-faculty-' + nicename, 'data-nicename': nicename } )
				.text( label )
				.append(
					$( '<input type="hidden" name="additional-faculty-js[]">' ).val( nicename ),
					'<span class="addl-faculty-remove"> <a href="#"><span class="sr-only">Remove</span><span class="fa fa-minus-circle"></span></a></span>'
				);

			$( '#additional-faculty-list' ).append( $li );
		}
	}

	wp.ol_addl_faculty = new OL_Addl_Faculty();

	var OL_Primary_Faculty = function() {
		var $ac, $existing, nonce;

		$( document ).ready( function() {
			// Toggle hide-if-js and hide-if-no-js.
			$( '.hide-if-js' ).hide();
			$( '.hide-if-no-js' ).show();

			$ac = $( '#primary-faculty-autocomplete' );
			nonce = $( '#_ol_faculty_autocomplete_nonce' ).val();

			$ac.autocomplete( {
				source: ajaxurl + '?action=openlab_additional_faculty_autocomplete&nonce=' + nonce,
				minLength: 2,
				select: function( event, ui ) {
					// Only one item can exist at a time.
					$( '#primary-faculty-list' ).empty();
					create_list_item( ui.item.value, ui.item.label );

					$ac.val( '' );
					return false;
				}
			} );

			// Init existing items.
			$existing = OL_Primary_Faculty_Existing;
			$.each( $existing, function( k, v ) {
				create_list_item( v.value, v.label );
			} );

			// Bind remove actions by delegation.
			$( '#primary-faculty-list' ).on( 'click', '.addl-faculty-remove', function( event ) {
				event.preventDefault();
				$( event.target ).closest( '.addl-faculty-member' ).remove();
			} );
		} );

		/**
		 * Generate a user-related list item.
		 */
		function create_list_item( nicename, label ) {
			var $li = $( '<li class="addl-faculty-member">' )
				.attr( { id: 'primary-faculty-' + nicename, 'data-nicename': nicename } )
				.text( label )
				.append(
					$( '<input type="hidden" name="primary-faculty-js[]">' ).val( nicename ),
					'<span class="addl-faculty-remove"> <a href="#"><span class="sr-only">Remove</span><span class="fa fa-minus-circle"></span></a></span>'
				);

			$( '#primary-faculty-list' ).append( $li );
		}
	}

	wp.ol_primary_faculty = new OL_Primary_Faculty();
}(jQuery));
