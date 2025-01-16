<?php
/**
 * Add-on main class.
 */

class EPKB_Convert {

	/**
	 * Generate html for article table
	 *
	 * @param $posts
	 * @param $taxonomies
	 * @param $is_post_conversion
	 * @return string
	 */
	public static function get_posts_table( $posts, $taxonomies, $is_post_conversion ) {

		if ( empty( $posts ) ) {
			return '<div class="epkb-no-results">' . ( $is_post_conversion ? esc_html__( 'No posts found', 'echo-knowledge-base' ) : esc_html__( 'No articles found', 'echo-knowledge-base' ) ) . '</div>';
		}

		$description = $is_post_conversion ? esc_html__( 'Below are posts that can be converted to KB articles', 'echo-knowledge-base' ) : esc_html__( 'Below are KB articles that can be converted to posts', 'echo-knowledge-base' );
		$check_all_input = "<input type='checkbox' id='check_all_convert'>";

		$table_header = [
			$check_all_input . esc_html__( 'Selection', 'echo-knowledge-base' ),
			__( 'Status', 'echo-knowledge-base' ),
			( $is_post_conversion ? __( 'Post Title', 'echo-knowledge-base' ) : __( 'Article Title', 'echo-knowledge-base' ) ),
			__( 'Taxonomies', 'echo-knowledge-base' ),
		];

		$table_rows = array();

		foreach ( $posts as $post ) {

			$input_box = $post->ID == 1 ? '' : "<input type='checkbox' name='row_id' value='" . esc_attr( $post->ID ) . "' checked>";
			$post_title = substr( $post->post_title, 0, 100 ) . ( strlen( $post->post_title ) > 100 ? '...' : '' );
			$post_title = esc_html( $post_title );
			if ( $post->ID == 1 ) {
				$post_title .= '<span class="epkb-admin-row--warning">(' . esc_html__( 'The default WordPress post can not be converted.', 'echo-knowledge-base' ) . ')</span>';
			}

			$terms_list = '';
			foreach ( $taxonomies as $tax_slug => $tax ) {
				$terms = wp_get_object_terms( $post->ID, $tax_slug, [ 'fields' => 'id=>name' ] );
				if ( is_wp_error( $terms ) ) {
					return '<div class="epkb-no-results">' . esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' (9433)' . $terms->get_error_message() . '</div>';
				}

				$terms_list .= '<ul data-kb-import-tax=' . $tax_slug . '>';

				foreach ( $terms as $cat_id => $cat ) {
					$terms_list .= '<li data-kb-import-cat-id=' . $cat_id . '>' . $cat . '</li>';
				}

				$terms_list .= '</ul>';
			}

			$table_row = [
				'checkbox' => $input_box,
				'status'   => esc_html( $post->post_status ),
				'title'    => $post_title,
				'terms'    => $terms_list
			];

			$table_rows[] = $table_row;
		}

		$title = $is_post_conversion ? esc_html__( 'Convert Posts', 'echo-knowledge-base' ) : esc_html__( 'Convert Articles', 'echo-knowledge-base' );
		return self::display_import_table( $title, $description, $table_header, $table_rows, 'new', $taxonomies );
	}

	/**
	 * Generate html for convert options
	 *
	 * @param $taxonomies
	 * @param string $post_type
	 * @return string
	 */
	public static function get_convert_options( $taxonomies, $post_type = '' ) {

		$selected_category = '';
		$selected_tag = '';

		// detect potential category
		foreach ( $taxonomies as $tax ) {
			if ( $tax->hierarchical ) {
				$selected_category = $tax->name;
				break;
			}
		}

		// detect potential tags
		foreach ( $taxonomies as $tax ) {
			if ( ! $tax->hierarchical ) {
				$selected_tag = $tax->name;
				break;
			}
		}

		// both hierarchical
		if ( $selected_category && empty( $selected_tag ) && count( $taxonomies ) > 1 ) {
			foreach ( $taxonomies as $tax ) {
				if ( $selected_category != $tax->name ) {
					$selected_tag = $tax->name;
					break;
				}
			}
		}

		// both not hierarchical
		if ( $selected_tag && empty( $selected_category ) && count( $taxonomies ) > 1 ) {
			foreach ( $taxonomies as $tax ) {
				if ( $selected_tag != $tax->name ) {
					$selected_category = $tax->name;
					break;
				}
			}
		}

		ob_start(); ?>

		<div class="epkb-import-attachment-options">
		<h3><?php esc_html_e( 'How to convert terms?', 'echo-knowledge-base' ); ?></h3>
		<div class="epkb-import-attachment-option">
			<label for="copy_terms">
				<input type="radio" id="copy_terms" name="convert_terms_mode" value="copy_terms" checked>
				<span class="epkb-import-attachment-option__text"><?php esc_html_e( "Copy terms to KB and leave current terms as is", "echo-knowledge-base" ); ?></span>
				<span class="epkb-import-attachment-option__sub-text"><?php esc_html_e( "Categories/Tags/Other terms will not be removed", "echo-knowledge-base" ); ?></span>
			</label>
		</div>
		<div class="epkb-import-attachment-option">
			<label for="remove_terms">
				<input type="radio" id="remove_terms" name="convert_terms_mode" value="remove_terms">
				<span class="epkb-import-attachment-option__text"><?php esc_html_e( 'Move Categories and Tags', 'echo-knowledge-base' ); ?></span>
				<span class="epkb-import-attachment-option__sub-text"><?php esc_html_e( "Original categories and tags are removed if they are empty.", "echo-knowledge-base" ); ?></span>
			</label>
		</div>
		</div><?php

		// no taxonomies
		if ( count( $taxonomies ) == 0 ) {
			return ob_get_clean();
		}

		if ( $post_type == 'post' ) { ?>
			<input type="hidden" name="categories_taxonomy" value="category">
			<input type="hidden" name="tags_taxonomy" value="post_tag"><?php
			return ob_get_clean();
		}

		// get post name
		$post_type_object = get_post_type_object( $post_type );
		if ( empty ( $post_type_object ) ) {
			return ob_get_clean();
		}

		$post_type_label = EPKB_Utilities::get_post_type_label( $post_type_object );
		$mapping_title = sprintf( '%s %s', esc_html__( 'Map Categories and Tags from', 'echo-knowledge-base' ), $post_type_label ) . ': '; ?>

		<div class="epkb-author-mapping-container">
		<div class="epkb-author-mapping__header">
			<h3><?php echo esc_html( $mapping_title ); ?></h3>
		</div>
		<div class="epkb-author-mapping__author-list">
			<div class="epkb-author-mapping__author-list__author-container">
				<div class="epkb-author__orig_auth"> <?php esc_html_e( 'Choose CPT category to map to Article category: ', 'echo-knowledge-base' ); ?></div>

				<div class="epkb-author__curr_auth">
					<select name="categories_taxonomy">
						<option value="" <?php selected( '', $selected_category ); ?>><?php esc_html_e( 'Not selected', 'echo-knowledge-base' ); ?></option><?php
						foreach ( $taxonomies as $tax ) { ?>
							<option value="<?php echo esc_attr( $tax->name ); ?>" <?php selected( $tax->name, $selected_category ); ?>><?php echo esc_html( $tax->label ); ?></option><?php
						} ?>
					</select>
				</div>
			</div>
			<div class="epkb-author-mapping__author-list__author-container">
				<div class="epkb-author__orig_auth"> <?php esc_html_e( 'Choose CPT tag to map to Article tag: ', 'echo-knowledge-base' ); ?></div>

				<div class="epkb-author__curr_auth">
					<select name="tags_taxonomy">
						<option value="" <?php selected( '', $selected_tag ); ?>><?php esc_html_e( 'Not selected', 'echo-knowledge-base' ); ?></option><?php
						foreach ( $taxonomies as $tax ) { ?>
							<option value="<?php echo esc_attr( $tax->name ); ?>" <?php selected( $tax->name, $selected_tag ); ?>><?php echo esc_html( $tax->label ); ?></option><?php
						} ?>
					</select>
				</div>
			</div>
		</div>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Show drop-down select for taxonomy with the terms
	 * @param $taxonomy
	 */
	public static function get_taxonomy_filter( $taxonomy ) {

		$all_label = esc_html__( 'All', 'echo-knowledge-base' ) . ' ' . $taxonomy->label;

		$terms = get_terms( [
			'taxonomy' => $taxonomy->name,
			'fields'   => 'id=>name'
		] );


		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		} ?>

		<div class="epkb-convert-categories-select">
		<select name="epkb-convert-categories" data-taxonomy-name="<?php echo esc_attr( $taxonomy->name ); ?>">
			<option value="" selected><?php echo esc_html( $all_label ); ?></option><?php
			foreach ( $terms as $id => $term_name ) { ?>
				<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $term_name ); ?></option><?php
			} ?>
		</select>
		</div><?php
	}

	/**
	 * Return html of convert posts filters
	 * @param array $taxonomies
	 * @return false|string
	 */
	public static function get_taxonomy_filters( $taxonomies = [] ) {

		if ( ! is_array( $taxonomies ) ) {
			return '';
		}

		ob_start(); ?>
		<div class="epkb-convert-categories-filters"><?php
		foreach ( $taxonomies as $tax ) {
			self::get_taxonomy_filter( $tax );
		} ?>

		<div class="epkb-convert-categories-filters--name-filter">
			<input type="text" placeholder="<?php esc_html_e( 'Search', 'echo-knowledge-base' ); ?>">
		</div>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Output HTML table with details.
	 *
	 * @param $title
	 * @param $description
	 * @param $table_header
	 * @param $table_rows
	 * @param $type
	 * @param $taxonomies
	 * @param string $learn_more
	 *
	 * @return string
	 */
	public static function display_import_table( $title, $description, $table_header, $table_rows, $type, $taxonomies, $learn_more = '' ) {

		$table_class = $type == 'error' ? 'epkb-dsl__article-list--error-articles' : ( $type == 'override' ? 'epkb-dsl__article-list--overwrite-articles' : 'epkb-dsl__article-list--new-articles' );

		$response_html = '<div class="epkb-dsl__article-list-container ' . $table_class . '">';

		$response_html .= '<h3>' . esc_html( $title ) . '</h3>';
		$response_html .= '<p>' . esc_html( $description );

		$response_html .= EPKB_Convert::get_taxonomy_filters( $taxonomies );

		if ( ! empty( $learn_more ) ) {
			$response_html .= ' <a href="' . esc_url( $learn_more ) . '" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>' . '</p>';
		}

		$response_html .= '</p>';

		// output header
		$response_html .= '<section class="epkb-dsl__article-list__header">';
		$response_html .= '<div class="epkb-admin-row">';
		foreach ( $table_header as $table_header_item ) {
			$response_html .= '<span>' . wp_kses( $table_header_item, EPKB_Utilities::get_admin_ui_extended_html_tags() ) . '</span>';
		}
		$response_html .= '</div>';
		$response_html .= '</section>';

		// display the body
		$response_html .= '<section class="epkb-dsl__article-list__body">';

		foreach ( $table_rows as $rows ) {
			$response_html .= '<div class="epkb-admin-row">';
			foreach ( $rows as $row_id => $row ) {
				$response_html .= '<span class="' . esc_attr( $row_id ) . '">' . wp_kses( $row, EPKB_Utilities::get_admin_ui_extended_html_tags() ) . '</span>';
			}
			$response_html .= '</div>';

		}
		$response_html .= '</section>';
		$response_html .= '</div>';

		return $response_html;
	}
}