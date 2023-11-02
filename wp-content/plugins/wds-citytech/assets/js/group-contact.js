window.wp = window.wp || {};

(function($){
	var OL_Group_Contacts = function() {
		var $ac, $existing, nonce;

		$( document ).ready( function() {
			// Toggle hide-if-js and hide-if-no-js.
			$( '.hide-if-js' ).hide();
			$( '.hide-if-no-js' ).show();

			$ac = $( '#group-contact-autocomplete' );
			nonce = $( '#_ol_group_contact_nonce' ).val();
			group_id = $( '#group-contact-group-id' ).val();

			$ac.autocomplete( {
				source: ajaxurl + '?action=openlab_group_contact_autocomplete&nonce=' + nonce + '&group_id=' + group_id,
				minLength: 2,
				select: function( event, ui ) {
					// No dupes.
					if ( 0 == $( '#group-contact-' + ui.item.value ).length ) {
						create_list_item( ui.item.value, ui.item.label );
					}

					$ac.val( '' );
					return false;
				}
			} );

			// Init existing items.
			$existing = OL_Group_Contact_Existing;
			$.each( $existing, function( k, v ) {
				create_list_item( v.value, v.label );
			} );

			// Bind remove actions by delegation.
			$( '#group-contact-list' ).on( 'click', '.group-contact-remove', function( event ) {
				event.preventDefault();
				$( event.target ).closest( '.group-contact-member' ).remove();
			} );
		} );

		/**
		 * Generate a user-related list item.
		 */
		function create_list_item( nicename, label ) {
			var li = '';

			li = '<li id="group-contact-' + nicename + '" class="group-contact-member" data-nicename="' + nicename + '"><span class="group-contact-remove"><a href="#"><span class="fa fa-minus-circle"></span><span class="screen-reader-text">Remove Contact</span></a></span> ' + label + '<input type="hidden" name="group-contact-js[]" value="' + nicename + '"></li>';
			li = '<li id="group-contact-' + nicename + '" class="group-contact-member" data-nicename="' + nicename + '">' + label + '<input type="hidden" name="group-contact-js[]" value="' + nicename + '"><span class="group-contact-remove"> <a href="#"><span class="fa fa-minus-circle"></span><span class="screen-reader-text">Remove Contact</span></a></span></li>';
			$( '#group-contact-list' ).append( li );
		}
	}

	wp.ol_group_contact = new OL_Group_Contacts();
}(jQuery));
