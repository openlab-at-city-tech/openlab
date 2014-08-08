(function($) {
	var $add_a_grade = $( '#olgc-add-a-grade' ),
		$grade_entry = $( '.olgc-grade-entry' );

	$( document ).ready( function() {
		toggle_grade_visibility();

		$add_a_grade.on( 'click', function() {
			toggle_grade_visibility();
		} );
	} );

	function toggle_grade_visibility() {
		if ( $add_a_grade.is( ':checked' ) ) {
			$grade_entry.show();
		} else {
			$grade_entry.hide();
		}
	}
}(jQuery))
