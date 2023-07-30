<?php
/**
 * CSV download on Reckoning's "Users > User Summary" page.
 *
 * @see https://redmine.gc.cuny.edu/issues/11941
 */

/**
 * Inject 'Download CSV' button on "Users > User Summary" page.
 */
add_action( 'in_admin_footer', function() {
	printf( '<div id="reckoning-csv-download"><p>You can also download a CSV of summarized user activity data below:</p><a href="%1$s" class="button-primary">%2$s</a></div>', wp_nonce_url( admin_url( '/users.php?page=Reckoning&view=download' ), 'reckoning-csv', 'csv-nonce' ), 'Download CSV' );

	// Inline JS.
	$js = <<<JS

	<script>
	jQuery(function($){
		$( 'h3:first' ).before( $( '#reckoning-csv-download' ) );
	});
	</script>

JS;

	echo $js;
} );

/**
 * Serve Reckoning data as a CSV download.
 */
add_action(
	'admin_init',
	function() {
		if ( empty( $_GET['csv-nonce'] ) || empty( $_GET['view'] ) || 'download' !== $_GET['view'] ) {
			return;
		}

		// Bail if user doesn't have the correct permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_redirect( admin_url( '/users.php?page=Reckoning' ) );
			die();
		}

		check_admin_referer( 'reckoning-csv', 'csv-nonce' );

		@set_time_limit( 0 );

		// Set timezone string.
		$timezone = wp_timezone_string();
		if ( 0 === strpos( $timezone, '+' ) || 0 === strpos( $timezone, '-' ) ) {
			$timezone = 'GMT' . $timezone;
		}

		$show_grade_column   = reckoning_show_grade_column();
		$show_private_column = reckoning_show_private_column();

		// Set CSV data; start with column headers.
		$csv_data = [];

		$headers = [
			esc_html__( 'User display name', 'reckoning' ),
			esc_html__( 'Username', 'reckoning' ),
			esc_html__( 'User email', 'reckoning' ),
			esc_html__( 'Type', 'reckoning' ),
			esc_html__( 'Content URL', 'reckoning' ),
			esc_html__( 'Post title', 'reckoning' ),
			esc_html__( 'Post category', 'reckoning' ),
			sprintf( 'Date posted (%s)', $timezone ),

		];

		if ( $show_grade_column ) {
			array_splice( $headers, -1, 0, [ esc_html__( 'Grade', 'reckoning' ) ] );
		}

		if ( $show_private_column ) {
			array_splice( $headers, -1, 0, [ esc_html__( 'Private Comment?', 'reckoning' ) ] );
		}

		$csv_data[] = $headers;

		// Loop through all posts.
		$posts = get_posts( [
			'post_status' => [ 'publish', 'private' ],
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows' => true,
			'nopaging' => true
		] );

		if ( $show_grade_column ) {
			$post_comment_grades = reckoning_get_comment_grades( wp_list_pluck( $posts, 'ID' ) );
		}

		// Set CSV row for each post.
		foreach ( $posts as $post ) {
			$user = get_user_by( 'ID', $post->post_author );

			$row = [
				$user->display_name,
				$user->user_login,
				$user->user_email,
				'post',
				get_permalink( $post->ID ),
				get_post_field( 'post_title', $post ),
				strip_tags( get_the_category_list( ',', 'multiple', $post->ID ) ),
				get_the_date( 'Y-m-d H:i:s', $post ),
			];

			if ( $show_grade_column ) {
				$post_grade = isset( $post_comment_grades[ $post->ID ] ) ? $post_comment_grades[ $post->ID ] : '';
				array_splice( $row, -1, 0, [ esc_html( $post_grade ) ] );
			}

			if ( $show_private_column ) {
				array_splice( $row, -1, 0, [ '' ] );
			}

			$csv_data[] = $row;
		}

		unset( $posts );

		// Loop through all comments.
		$comments = get_comments( [
			'type' => 'comment',
			'no_found_rows' => true,
			'update_comment_meta_cache' => false,
			'update_comment_post_cache' => false
		] );

		// Set CSV row for each comment.
		foreach ( $comments as $comment ) {
			$user = get_user_by( 'ID', $comment->user_id );

			$row = [
				! $user ? $comment->comment_author : $user->display_name,
				! $user ? 'Anonymous' : $user->user_login,
				get_comment_author_email( $comment ),
				'comment',
				get_comment_link( $comment ),
				get_post_field( 'post_title', $comment->comment_post_ID ),
				'',
				get_comment_date( 'Y-m-d H:i:s', $comment )
			];

			// Insert an empty column.
			if ( $show_grade_column ) {
				array_splice( $row, -1, 0, [ '' ] );
			}

			if ( $show_private_column ) {
				$private = get_comment_meta( $comment->comment_ID, 'olgc_is_private', true ) ? __( 'Yes', 'reckoning' ) : '';
				array_splice( $row, -1, 0, [ esc_html( $private ) ] );
			}

			$csv_data[] = $row;
		}

		unset( $comments );

		// Serve the CSV!
		cac_serve_csv_download( [
			'filename' => sprintf( '%1$s-User-Summary-%2$s', get_bloginfo( 'name' ), date( 'Y-m-d' ) ),
			'csv_data' => $csv_data
		] );
	}
);

/**
 * Utility function to serve a CSV file from generated data.
 *
 * You should run this after permission checks are done.
 *
 * @param array $r {
 *     Array of parameters.
 *
 *     @type string $filename Filename for CSV without extension.
 *     @type array  $csv_data Multi-dimensional array of CSV data. Each array is a CSV row.
 * }
 */
function cac_serve_csv_download( $r = array() ) {
	if ( empty( $r['filename'] ) ) {
		$r['filename'] = 'download.csv';
	} else {
		$r['filename'] = sanitize_file_name( $r['filename'] . '.csv' );
	}

	if ( empty( $r['csv_data'] ) ) {
		$r['csv_data'] = [];
	}

	// Output headers so the file is downloaded.
	header( 'Content-type: text/csv' );
	header( "Content-disposition: attachment; filename = \"{$r['filename']}\"" );

	// Don't cache!
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Pragma: no-cache' );

	// Create the csv file.
	$csv = fopen( 'php://output', 'w' );

	// UTF-8 fix?
	fprintf( $csv, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

	// Inject CSV data.
	foreach ( $r['csv_data'] as $row ) {
		fputcsv( $csv, $row );
	}

	// All done!
	fclose( $csv );
	die();
}
