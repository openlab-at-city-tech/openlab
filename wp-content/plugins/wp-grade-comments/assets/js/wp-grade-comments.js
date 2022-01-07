(function($) {
	var $add_a_grade = $( '#olgc-add-a-grade' ),
		$grade_entry = $( '.olgc-grade-entry' ),
		$grade_input = $( '#olgc-grade' ),
		$private_checkbox = $( '#olgc-private-comment' ),
		$comment_content_input = $( '#comment' ),
		$reply_to_com = $( '.comment-reply-link' );

	$( document ).ready( function() {
		toggle_grade_visibility();

		$add_a_grade.on( 'click', function() {
			toggle_grade_visibility();
		} );

		$reply_to_com.on( 'click', function() {
			set_comment_visibility( $( this ) );
		} );

		$( '.olgc-grade-toggle' ).on( 'click', function( e ) {
			e.preventDefault();
			toggle_single_grade_visibility( $( this ) );
		} );

		$grade_input.on(
			'change',
			function() {
				toggle_comment_content_required();
			}
		);
	} );

	function toggle_grade_visibility() {
		if ( $add_a_grade.is( ':checked' ) ) {
			$grade_entry.show();
		} else {
			$grade_entry.hide();
		}
	}

	function toggle_comment_content_required() {
		if ( $grade_input.val().length > 0 ) {
			$comment_content_input.prop( 'required', false );
		} else {
			$comment_content_input.prop( 'required', true );
		}
	}

	/**
	 * When responding to a private comment, comment privacy should be enforced.
	 */
	function set_comment_visibility( $crlink ) {
		$cparent = $crlink.closest( '.comment' );
		if ( $cparent.length && $cparent.find( '.olgc-private-notice' ).length ) {
			$private_checkbox.prop( 'checked', true );
			$private_checkbox.prop( 'disabled', true );
		} else {
			$private_checkbox.prop( 'checked', false );
			$private_checkbox.prop( 'disabled', false );
		}
	}

	/**
	 * Toggle grade visibility.
	 */
	function toggle_single_grade_visibility( $clicked ) {
		$clicked.closest( '.olgc-grade-display' ).toggleClass( 'olgc-grade-hidden' );
	}
}(jQuery))
