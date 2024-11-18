<?php

/**
 * Add-on main class.
 */
class EPKB_Convert_Ctrl {

	public function __construct() {
		add_action( 'wp_ajax_epkb_load_articles_list', array( $this, 'load_articles_list' ) );
		add_action( 'wp_ajax_nopriv_epkb_load_articles_list', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_convert_kb_content', array( $this, 'convert_kb_content' ) );
		add_action( 'wp_ajax_nopriv_epkb_convert_kb_content', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Handle loading articles data
	 */
	public function load_articles_list() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// retrieve KB ID we are saving
		$kb_id = EPKB_Utilities::post( 'kb_id' );
		$kb_id = empty( $kb_id ) ? '' : EPKB_Utilities::sanitize_get_id( $kb_id );
		if ( empty( $kb_id ) || is_wp_error( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 10 ), '', 10 );
		}

		// we cannot convert non-default KB if MKB is not active
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 20 ), '', 20 );
		}

		$post_type = EPKB_Utilities::post( 'post_type' );
		$post_type =  $post_type == 'post' ? $post_type : EPKB_KB_Handler::get_post_type( $kb_id );
		if ( empty( $post_type ) || ! post_type_exists( $post_type ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 30 ), '', 30 );
		}
		$is_post_conversion = $post_type == 'post';

		$active_categories = EPKB_Utilities::post( 'categories', [] );
		if ( empty( $active_categories ) || ! is_array( $active_categories ) ) {
			$active_categories = [];
		}

		$query_args = [
			'posts_per_page' => 10000,
			'post_type'      => $post_type,
			'category__in'   => $active_categories,
			'post_status'    => [ 'publish', 'pending', 'draft', 'future', 'private' ],
		];
		$query = new WP_Query( $query_args );
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$filtered_taxonomies = [];
		foreach ( $taxonomies as $tax_name => $taxonomy ) {
			if ( !$taxonomy->public ) {
				continue;
			}

			$filtered_taxonomies[ $tax_name ] = $taxonomy;
		}

		$response_html_1 = EPKB_Convert::get_posts_table( $query->posts, $taxonomies, $is_post_conversion );
		$response_html_2 = EPKB_Convert::get_convert_options( $filtered_taxonomies, $post_type );

		$output_messages = [
			count( $query->posts ) . ' ' . ( $is_post_conversion ? esc_html__( 'valid posts found', 'echo-knowledge-base' ) : esc_html__( 'valid articles found', 'echo-knowledge-base' ) )
		];

		wp_die( wp_json_encode( array( 'success' => $output_messages, 'response_html_1' => $response_html_1, 'response_html_2' => $response_html_2 ) ) );
	}

	/**
	 * AJAX Callback to convert posts
	 */
	public function convert_kb_content() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Only administrators can convert posts.', 'echo-knowledge-base' ) );
		}

		// retrieve KB ID we are importing into
		$kb_id = EPKB_Utilities::post( 'epkb_kb_id' );
		if ( empty( $kb_id ) ) {
			EPKB_Logging::add_log( "Received invalid kb_id when converting KB articles", $kb_id );
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 10 ), '', 10 );
		}

		// we cannot convert non-default KB if MKB is not active
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 20 ), '', 20 );
		}

		// retrieve type of post we will convert
		$post_type = EPKB_Utilities::post( 'epkb_convert_post_type' );
		if ( empty( $post_type ) || ( $post_type != 'article' && ! post_type_exists( $post_type ) ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 343, esc_html__( 'Refresh your page', 'echo-knowledge-base' ) ), '', 343 );
		}
		$is_post_conversion = $post_type == 'post';

		$step = (int)EPKB_Utilities::post( 'epkb_convert_step' );
		if ( $step != 4 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 344, esc_html__( 'Refresh your page', 'echo-knowledge-base' ) ), '', 344 );
		}

		// get user selected rows
		$selected_rows = EPKB_Utilities::get( 'selected_rows' );
		$selected_rows = json_decode( stripslashes( $selected_rows ) );
		if ( empty( $selected_rows ) || ! is_array( $selected_rows ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 344, esc_html__( 'Get Selected Rows', 'echo-knowledge-base' ), esc_html__( 'No articles were selected for import.', 'echo-knowledge-base' ) ), '', 344 );
		}

		// import options
		$convert_terms_mode = EPKB_Utilities::get( 'convert_terms_mode' );

		if ( ! in_array( $convert_terms_mode, [ 'copy_terms', 'remove_terms' ] ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 345, esc_html__( 'Refresh your page', 'echo-knowledge-base' ) ), '', 345 );
		}

		set_time_limit( 5 * MINUTE_IN_SECONDS );

		$errors = [];
		$taxonomies_names_relationship = [];

		$category_taxonomy = EPKB_Utilities::get( 'category_taxonomy' );
		if ( ! empty( $category_taxonomy ) ) {
			$taxonomies_names_relationship[ $category_taxonomy ] = $post_type == 'post' ? EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) : 'category';
		}

		$tag_taxonomy = EPKB_Utilities::get( 'tags_taxonomy' );
		if ( ! empty( $tag_taxonomy ) ) {
			$taxonomies_names_relationship[ $tag_taxonomy ] = $post_type == 'post' ? EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id ) : 'post_tag';
		}

		// old term id => new term_id
		$old_terms_relationship = [];
		// old term_id => taxonomy_name
		$taxonomy_relationship = [];

		// change post type for selected posts
		$processed_articles_count = 0;

		wp_suspend_cache_invalidation();

		foreach ( $selected_rows as $post_id ) {

			if ( ! get_post_status( $post_id ) ) {
				$errors[ $post_id ] = $is_post_conversion ? esc_html__( 'Post not found', 'echo-knowledge-base' ) : esc_html__( 'Article not found', 'echo-knowledge-base' );
				continue;
			}

			// Get article title for errors texts
			$post_title = get_the_title( $post_id );
			if ( empty( $post_title ) ) {
				$post_title = $post_id;
			}

			// taxonomy_name => terms
			$old_post_terms_data = [];
			$new_post_terms_data = [];

			// first change taxonomies
			foreach ( $taxonomies_names_relationship as $old_taxonomy_name => $kb_taxonomy_name ) {

				// skip missing or empty taxonomies
				$post_terms = wp_get_post_terms( $post_id, $old_taxonomy_name );
				if ( is_wp_error( $post_terms ) || empty( $post_terms ) ) {
					continue;
				}

				$old_post_terms_data[ $old_taxonomy_name ] = [];
				$new_post_terms_data[ $kb_taxonomy_name ] = [];

				// convert terms of the selected post
				foreach ( $post_terms as $old_term ) {

					// check if new term already exists
					$new_term_id = 0;
					if ( ! empty( $old_terms_relationship[ $old_term->term_id ] ) ) {
						$new_term_id = $old_terms_relationship[ $old_term->term_id ];
					}

					// check by slug if the new term exist already
					if ( empty( $new_term_id ) ) {
						$new_term = get_term_by( 'slug', $old_term->slug, $kb_taxonomy_name );
						if ( $new_term ) {
							$new_term_id = $new_term->term_id;
						}
					}

					// check by name if the new term exist already
					if ( empty( $new_term_id ) ) {
						$new_term = get_term_by( 'name', $old_term->name, $kb_taxonomy_name );
						if ( $new_term ) {
							$new_term_id = $new_term->term_id;
						}
					}

					// create new term if needed
					if ( empty( $new_term_id ) ) {
						$new_term = wp_insert_term( $old_term->name, $kb_taxonomy_name, [
							'description' => $old_term->description,
							'slug'        => $old_term->slug
						] );

						if ( empty( $new_term ) || is_wp_error( $new_term ) ) {

							// if user selected to remove old terms - stop converting to prevent deleting all information about the term
							if ( $convert_terms_mode == 'remove_terms' ) {
								$message = esc_html__( 'Can not create term', 'echo-knowledge-base' ) . ( is_wp_error( $new_term ) ? ': ' . $new_term->get_error_message() : '' );
								EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 355, $message . ': ' . $old_term->name ), '', 355 );
							}

							$errors[ $post_title ] = EPKB_Utilities::report_generic_error( 300, $new_term, false );
							continue 2;
						}

						$new_term_id = $new_term['term_id'];
					}

					// all posts data
					$old_terms_relationship[ $old_term->term_id ] = $new_term_id;
					$taxonomy_relationship[ $old_term->term_id ] = $old_taxonomy_name;

					// current post data
					$old_post_terms_data[ $old_taxonomy_name ][] = $old_term->term_id;
					$new_post_terms_data[ $kb_taxonomy_name ][] = $new_term_id;
				}
			}

			// remove old terms from the post
			foreach ( $old_post_terms_data as $old_taxonomy_name => $old_term_ids ) {
				$result = wp_remove_object_terms( $post_id, array_keys( $old_terms_relationship ), $old_taxonomy_name );
				if ( is_wp_error( $result ) ) {
					$errors[ $post_title ] = EPKB_Utilities::report_generic_error( 301, $result, false );
					continue 2;
				}
			}

			// convert the post to selected KB post type
			$result = wp_update_post( [
				'ID'        => $post_id,
				'post_type' => $post_type == 'post' ? EPKB_KB_Handler::get_post_type( $kb_id ) : 'post'
			] );
			if ( empty( $result ) || is_wp_error( $result ) ) {
				$errors[ $post_title ] = EPKB_Utilities::report_generic_error( 302, $result, false );
				continue;
			}

			// add new terms to the post
			foreach ( $new_post_terms_data as $new_taxonomy_name => $new_term_ids ) {
				$result = wp_set_object_terms( $post_id, $new_term_ids, $new_taxonomy_name );
				if ( is_wp_error( $result ) ) {
					$errors[ $post_title ] = EPKB_Utilities::report_generic_error( 303, $result, false );
					continue 2;
				}
			}

			$processed_articles_count ++;
		}

		// remove old empty terms
		if ( $convert_terms_mode == 'remove_terms' ) {
			global $wpdb;

			foreach ( array_keys( $old_terms_relationship ) as $old_term_id ) {

				$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->term_taxonomy} WHERE term_id = %d AND taxonomy = %s", $old_term_id, $taxonomy_relationship[ $old_term_id ] ) );
				if ( empty( $result ) || ! is_array( $result ) || (int)$result[0]->count ) {
					continue;
				}

				wp_delete_term( $old_term_id, $taxonomy_relationship[ $old_term_id ] );
			}
		}

		$output_message = esc_html__( 'Converting completed with warnings', 'echo-knowledge-base' );

		if ( empty( $errors ) ) {
			$output_message = esc_html__( 'Converting completed successfully', 'echo-knowledge-base' );
		}

		$errors_text = '';
		foreach ( $errors as $post_name => $error ) {
			$errors_text .= '<div class="epkb-admin-row">
				<span>' . esc_html( $post_name ) . '</span>
				<span>' . esc_html( $error ) . '</span>
			</div>';
		}

		wp_die( wp_json_encode( array( 'success' => $output_message, 'inserted' => $processed_articles_count, 'process_errors' => $errors_text ) ) );
	}
}