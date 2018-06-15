<?php

/**
 * ECS_Walker_Edit
 * 
 * Create HTML list of sidebar input items. This is 
 * used to generate the markup used to output the 
 * sortable list items used in the admin page
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 * @uses Walker_Nav_Menu
 * 
 */
class ECS_Walker_Edit extends Walker_Nav_Menu {

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		// Start output buffer
		ob_start();
		$item_id   = esc_attr( $item->ID );
		$page_name = 'easy-custom-sidebars';
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		// Set the title
		$original_title = '';

		/**
		 * Initialise Variables Based on Type
		 *
		 * Checks the item type and initializes the 
		 * variable required to produce the relevant
		 * output.
		 * 
		 */
		switch ( $item->type ) {
			
			// Taxonomy term
			case 'taxonomy':
				// Get the taxonomy term
				$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );

				// Check if it is valid
				if ( is_wp_error( $original_title ) ) {
					$original_title = false;
				}
				break;

			// All items in taxonomy
			case 'taxonomy_all':
				$original_title  = $item->title;
				break;

			// Single post in posttype
			case 'post_type':
				$original_title  = $item->post_title;
				break;

			// All posts in a posttype
			case 'post_type_all':
				$original_title   = is_wp_error( $item->title ) ? false : $item->title;
				break;

			// Posttype archive page
			case 'post_type_archive':
				$original_title   = is_wp_error( $item->title ) ? false : $item->title;
				break;

			// All posts in a category
			case 'category_posts':
				$original_title   = $item->title;
				$item->type_label = __( 'All Posts In Category', 'easy-custom-sidebars' );
				break;

			// Author archive page
			case 'author_archive':
				$original_title   = $item->title;
				$item->url = get_author_posts_url( $item->ID );
				break;

			// WordPress templates
			case 'template_hierarchy':
				$original_title   = $item->title;
				break;
			
			default:
				# code...
				break;
		}

		// Add any classes
		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		// Add classes to indicate whether this is a pending item
		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)', 'easy-custom-sidebars' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {

			if ( isset( $args->pending ) ) {
				
				if ( ! $args->pending ) {
					$classes[] = 'not-pending';
				} else {
					$classes[] = 'pending';
				}

			} else {
				$classes[] = 'pending';
			}
			
			/* translators: %s: title of menu item in draft status */
			$title = $item->title;
		}

		// Test for no title edge case
		$title          = empty( $item->label ) ? $title : $item->label;
		$title          = empty( $title ) ? __( '(No Title)', 'easy-custom-sidebars' ) : $title;
		$original_title = empty( $original_title ) ? __( '(No Title)', 'easy-custom-sidebars' ) : $original_title; 

		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><?php echo esc_html( $title ); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e( 'Edit Sidebar Item', 'easy-custom-sidebars' ); ?>" href="#">
							<?php _e( 'Edit Sidebar Item', 'easy-custom-sidebars' ); ?>
						</a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				
				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __( 'Original: %s', 'easy-custom-sidebars' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action'       => 'delete-sidebar-item',
								'sidebar-item' => $item_id,
								'page'         => $page_name
							),
							remove_query_arg($removed_args, admin_url( 'themes.php' ) )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php _e( 'Remove', 'easy-custom-sidebars' ); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'themes.php' ) ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e( 'Cancel', 'easy-custom-sidebars' ); ?></a>
					<div style="clear:both;"></div>
				</div>
				
				<input class="menu-item-data-db-id"     type="hidden" name="menu-item-db-id[<?php esc_html_e( $item_id ); ?>]"        value="<?php esc_html_e( $item_id ); ?>" />
				<input class="menu-item-title"          type="hidden" name="menu-item-title[<?php esc_html_e( $original_title ); ?>]" value="<?php esc_html_e( $original_title ); ?>">
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]"             value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object"    type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"                value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]"             value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position"  type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"              value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type"      type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"                  value="<?php echo esc_attr( $item->type ); ?>" />
				<div style="clear:both;"></div>
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}

