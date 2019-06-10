<?php

class BPDIG_Group_Extension extends BP_Docs_Group_Extension {
	// Override
	public function url_backpat() {}

	public function create_screen( $group_id = null ) {
		parent::create_screen( $group_id );
	}

	public function create_screen_save( $group_id = null ) {
		parent::create_screen_save( $group_id );
	}

	public function edit_screen( $group_id = null ) {
		parent::edit_screen( $group_id );
	}

	public function edit_screen_save( $group_id = null ) {
		parent::edit_screen_save( $group_id );
	}

	public function admin_screen( $group_id = null ) {
		parent::admin_screen( $group_id );
	}

	public function admin_screen_save( $group_id = null ) {
		parent::admin_screen_save( $group_id );
	}

	/**
	 * Loads the display template
	 *
	 * @package BuddyPress Docs
	 * @since 1.0-beta
	 */
	public function display( $group_id = null ) {
		global $bp;

		// Docs are stored on the root blog
		if ( !bp_is_root_blog() )
			switch_to_blog( BP_ROOT_BLOG );

		switch ( $bp->bp_docs->current_view ) {
			case 'create' :
				/**
				 * Load the template tags for the edit screen
				 */
				if ( !function_exists( 'wp_tiny_mce' ) ) {
					bp_docs_define_tiny_mce();
				}

				require_once( BP_DOCS_INCLUDES_PATH . 'templatetags-edit.php' );

				$template = 'single/edit.php';

				$template_path = bp_docs_locate_template( $template );
				include( apply_filters( 'bp_docs_template', $template_path, $this ) );

				break;
			case 'list' :
				$template = 'docs-loop.php';
				$template_path = bp_docs_locate_template( $template );
				include( apply_filters( 'bp_docs_template', $template_path, $this ) );
				break;
			case 'category' :
				// Check to make sure the category exists
				// If not, redirect back to list view with error
				// Otherwise, get args based on category ID
				// Then load the loop template
				break;
			case 'single' :
			case 'edit' :
			case 'delete' :
			case 'history' :
				$doc_slug = bp_action_variable( 0 );

				// Look up Doc by slug for this group
				$matching_docs_query = new WP_Query( array(
					'post_type' => bp_docs_get_post_type_name(),
					'name' => $doc_slug,
					'tax_query' => array(
						BP_Docs_Groups_Integration::tax_query_arg_for_groups( bp_get_current_group_id() ),
					),
					'posts_per_page' => 1,
				) );

				if ( $matching_docs_query->have_posts() ) {
					while ( $matching_docs_query->have_posts() ) {
						$matching_docs_query->the_post();

						add_filter( 'bp_docs_is_doc_history', 'bpdig_filter_is_doc_history' );


						// If this is the edit screen, we won't really be able to use a
						// regular have_posts() loop in the template, so we'll stash the
						// post in the $bp global for the edit-specific template tags
						if ( $bp->bp_docs->current_view == 'edit' ) {
							require_once( BP_DOCS_INCLUDES_PATH . 'templatetags-edit.php' );
						}

						switch ( $bp->bp_docs->current_view ) {
							case 'single' :
								$template = 'single/index.php';
								break;
							case 'edit' :
								$template = 'single/edit.php';
								break;
							case 'history' :
								$template = 'single/history.php';
								break;

						}
						// Todo: Maybe some sort of error if there is no edit permission?

						$template_path = bp_docs_locate_template( $template );
						if ( !empty( $template ) ) {
							include( apply_filters( 'bp_docs_template', $template_path, $this ) );
						}
					}
				}

				break;
		}

		// Only register on the root blog
		if ( !bp_is_root_blog() )
			restore_current_blog();
	}
}
