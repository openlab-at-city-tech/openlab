<?php

/**
 * Register menus
 *
 * Callback function theme menus registration and init
 *
 * @since  1.0
 */

add_action( 'init', 'johannes_register_menus' );

if ( !function_exists( 'johannes_register_menus' ) ) :
	function johannes_register_menus() {
		register_nav_menu( 'johannes_menu_primary', esc_html__( 'Primary Menu' , 'johannes' ) );
		register_nav_menu( 'johannes_menu_social', esc_html__( 'Social Menu' , 'johannes' ) );
		register_nav_menu( 'johannes_menu_secondary_1', esc_html__( 'Secondary Menu 1' , 'johannes' ) );
		register_nav_menu( 'johannes_menu_secondary_2', esc_html__( 'Secondary Menu 2' , 'johannes' ) );
	}
endif;



/**
 * wp_setup_nav_menu_item callback
 *
 * Get our meta data from nav menu
 *
 * @since  1.0
 */

add_filter( 'wp_setup_nav_menu_item', 'johannes_get_menu_meta' );

if ( !function_exists( 'johannes_get_menu_meta' ) ):
	function johannes_get_menu_meta( $menu_item ) {

		$defaults = array(
			'mega_cat' => 0,
			'mega' => 0
		);

		$meta = get_post_meta( $menu_item->ID, '_johannes_meta', true );
		$meta = wp_parse_args( $meta, $defaults );
		$menu_item->johannes_meta = $meta;

		return $menu_item;

	}
endif;


/**
 * wp_update_nav_menu_item callback
 *
 * Store values from custom fields in nav menu
 *
 * @since  1.0
 */

add_action( 'wp_update_nav_menu_item', 'johannes_update_menu_meta', 10, 3 );


if ( !function_exists( 'johannes_update_menu_meta' ) ):
	function johannes_update_menu_meta( $menu_id, $menu_item_db_id, $args ) {

		$meta = array();

		if ( isset( $_REQUEST['menu-item-johannes-mega-cat'][$menu_item_db_id] ) ) {
			$meta['mega_cat'] = 1;
		}

		if ( isset( $_REQUEST['menu-item-johannes-mega'][$menu_item_db_id] ) ) {
			$meta['mega'] = 1;
		}

		if ( !empty( $meta ) ) {
			update_post_meta( $menu_item_db_id, '_johannes_meta', $meta );
		} else {
			delete_post_meta( $menu_item_db_id, '_johannes_meta' );
		}


	}
endif;




/**
 * wp_edit_nav_menu_walker callback
 *
 * Add custom fields to nav menu form
 *
 * @since  1.0
 */

add_filter( 'wp_edit_nav_menu_walker', 'johannes_edit_menu_walker', 10, 2 );

if ( !function_exists( 'johannes_edit_menu_walker' ) ):
	function johannes_edit_menu_walker( $walker, $menu_id ) {

		if ( !johannes_get_option( 'mega_menu' ) ) {
			return $walker;
		}

		class johannes_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

			public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

				parent::start_el( $default_output, $item, $depth, $args, $id );

				$inject_html = '';

				if ( $item->object == 'category' ) {
					$inject_html .= '<p class="description">
		                <label for="menu-item-johannes-mega-cat['.$item->db_id.']">
		        		<input type="checkbox" id="menu-item-johannes-mega-cat['.$item->db_id.']" class="widefat" name="menu-item-johannes-mega-cat['.$item->db_id.']" value="1" '.checked( $item->johannes_meta['mega_cat'], 1, false ). ' />
		                '.esc_html__( 'Automatically display category posts as "mega menu"', 'johannes' ).'</label>
		            </p>';
				}

				if ( !$item->menu_item_parent && $item->object != 'category' ) {

					$inject_html .= '<p class="description">
			                <label for="menu-item-johannes-mega['.$item->db_id.']">
			        		<input type="checkbox" id="menu-item-johannes-mega['.$item->db_id.']" class="widefat" name="menu-item-johannes-mega['.$item->db_id.']" value="1" '.checked( $item->johannes_meta['mega'], 1, false ). ' />
			                '.esc_html__( 'Display submenu items as "mega menu"', 'johannes' ).'</label>
			            </p>';
				}

				ob_start();
				do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args );
				$inject_html .= ob_get_clean();

				$new_output = preg_replace( '/(?=<div.*submitbox)/', $inject_html, $default_output );

				$output .= $new_output;


			}

		}

		return 'johannes_Walker_Nav_Menu_Edit';
	}
endif;



/**
 * nav_menu_css_class callback
 *
 * Used to add/modify CSS classes in nav menu
 *
 * @since  1.0
 */

add_filter( 'nav_menu_css_class', 'johannes_modify_nav_menu_classes', 10, 2 );

if ( !function_exists( 'johannes_modify_nav_menu_classes' ) ):
	function johannes_modify_nav_menu_classes( $classes, $item ) {

		if ( !johannes_get_option( 'mega_menu' ) ) {
			return $classes;
		}

		if ( $item->object == 'category' && isset( $item->johannes_meta['mega_cat'] ) && $item->johannes_meta['mega_cat'] ) {
			$classes[] = 'menu-item-has-children johannes-mega-menu johannes-category-menu';
		}

		if ( isset( $item->johannes_meta['mega'] ) && $item->johannes_meta['mega'] ) {
			$classes[] = 'johannes-mega-menu';
		}

		return $classes;

	}
endif;


/**
 * Display category posts in mega menu
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_nav_menu_category_posts' ) ) :

	function johannes_get_nav_menu_category_posts( $cat_id ) {

		$args = array(
			'post_type'    => 'post',
			'cat'      => $cat_id,
			'posts_per_page' => absint( johannes_get_option( 'mega_menu_ppp' ) )
		);

		$slider_class = $args['posts_per_page'] > 4 ? 'johannes-slider has-arrows' : '';

		$output = '<li class="row '.esc_attr( $slider_class ).'">';

		ob_start();

		$args['ignore_sticky_posts'] = 1;

		$menu_posts = new WP_Query( $args );

		if ( $menu_posts->have_posts() ) :

			while ( $menu_posts->have_posts() ) : $menu_posts->the_post(); ?>

				<article <?php post_class( 'col-12 col-lg-3' ); ?>>

		            <?php if ( $fimg = johannes_get_featured_image( 'johannes-d' ) ) : ?>

			                <div class="entry-media">
				                <a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
				                   	<?php echo wp_kses_post( $fimg ); ?>
				                </a>
			                </div>

		            <?php endif; ?>

		            <div class="entry-header">
		                <?php the_title( sprintf( '<a href="%s" class="entry-title h6">', esc_url( get_permalink() ) ), '</a>' ); ?>
		            </div>

				</article>

			<?php endwhile;

		endif;

		wp_reset_postdata();

		$output .= ob_get_clean();

		$output .= '</li>';

		return $output;

	}

endif;




/**
 * walker_nav_menu_start_el callback
 *
 * Used to display specific data in nav menu on website front-end
 *
 * @since  1.0
 */

add_filter( 'walker_nav_menu_start_el', 'johannes_walker_nav_menu_start_el', 10, 4 );

function johannes_walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {

	if ( !johannes_get_option( 'mega_menu' ) ) {
		return $item_output;
	}

	if ( isset( $item->johannes_meta['mega_cat'] ) && $item->johannes_meta['mega_cat'] ) {

		$item_output .= '<ul class="sub-menu johannes-menu-posts">';
		$item_output .= johannes_get_nav_menu_category_posts( $item->object_id );
		$item_output .= '</ul>';

	}

	return $item_output;
}
?>
