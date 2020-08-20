<?php

namespace OpenLab\NavMenus;

/**
 * Get the primary menu ID for current site.
 *
 * @return int Primary menu ID.
 */
function get_primary_menu_id() {
	$locations = get_theme_mod( 'nav_menu_locations', [] );

	if ( ! empty( $locations['primary'] ) ) {
		return (int) $locations['primary'];
	}

	if ( ! empty( $locations['primary-menu'] ) ) {
		return (int) $locations['primary-menu'];
	}

	if ( ! empty( $locations['main'] ) ) {
		return (int) $locations['main'];
	}

	if ( ! empty( $locations['top'] ) ) {
		return (int) $locations['top'];
	}

	if ( ! empty( $locations['expanded'] ) ) {
		return (int) $locations['expanded'];
	}

	return 0;
}

/**
 * Insert "Home" menu item in the primary menu.
 *
 * @param int $menu_id  Optional. Primary menu ID.
 * @return void
 */
function add_home_menu_item( $menu_id = null ) {
	if ( ! $menu_id ) {
		$menu_id = get_primary_menu_id();
	}

	if ( empty( $menu_id ) ) {
		return false;
	}

	wp_update_nav_menu_item(
		$menu_id,
		0,
		[
			'menu-item-title'    => 'Home',
			'menu-item-url'      => trailingslashit( home_url() ),
			'menu-item-status'   => 'publish',
			'menu-item-type'     => 'custom',
			'menu-item-position' => -1,
			'menu-item-classes'  => 'menu-item menu-item-home',
		]
	);
	
	return true;
}

/**
 * Insert "Group Profile" menu item in the primary menu.
 *
 * @param int $group_id Group ID.
 * @param int $menu_id  Optional. Primary menu ID.
 * @return void
 */
function add_group_menu_item( $group_id = 0, $menu_id = null ) {
	if ( ! $menu_id ) {
		$menu_id = get_primary_menu_id();
	}

	if ( empty( $menu_id ) ) {
		return false;
	}

	$group      = groups_get_group( $group_id );
	$group_type = ucfirst( groups_get_groupmeta( $group_id, 'wds_group_type' ) );

	wp_update_nav_menu_item(
		$menu_id,
		0,
		[
			'menu-item-title'    => sprintf( '%s Profile', $group_type ),
			'menu-item-url'      => bp_get_group_permalink( $group ),
			'menu-item-status'   => 'publish',
			'menu-item-type'     => 'custom',
			'menu-item-position' => -2,
			'menu-item-classes'  => 'menu-item menu-item-group-profile-link',
		]
	);
	
	return true;
}

/**
 * Generate array of nav menu items.
 *
 * @return array $items
 */
function get_group_menu_items() {
	$items = [];

	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

	if ( ! $group_id ) {
		return $items;
	}

	$group = groups_get_group( $group_id );
	if ( ! $group->is_visible ) {
		return $items;
	}

	$group_type = ucfirst( groups_get_groupmeta( $group_id, 'wds_group_type' ) );

	$items[] = (object) [
		'ID'               => 'group-profile-link',
		'db_id'            => 0,
		'object_id'        => -1,
		'object'           => 'custom',
		'title'            => sprintf( '%s Profile', $group_type ),
		'url'              => bp_get_group_permalink( $group ),
		'slug'             => 'group-profile-link',
		'type'             => 'custom',
		'classes'          => [ 'menu-item', 'menu-item-group-profile-link' ],
		'menu_item_parent' => 0,
		'attr_title'       => '',
		'target'           => '',
		'xfn'              => ''
	];

	return $items;
}

/**
 * Register meta box for OpenLab nav menus.
 *
 * @return void
 */
function add_nav_menu_meta_box() {
	$is_group_site = (bool) openlab_get_group_id_by_blog_id( get_current_blog_id() );

	// Only add meta box panel to group sites.
	if ( ! $is_group_site ) {
		return;
	}

	add_meta_box(
		'openlab-nav-menu-box',
		__( 'OpenLab', 'openlab' ),
		__NAMESPACE__ . '\\render_nav_menu_meta_box',
		'nav-menus',
		'side',
		'default'
	);
}
add_action( 'load-nav-menus.php', __NAMESPACE__ . '\\add_nav_menu_meta_box' );

/**
 * Render OpenLab meta box panel on Appearance > Menus.
 *
 * @return void
 */
function render_nav_menu_meta_box() {
	global $nav_menu_selected_id;

	$walker = new \Walker_Nav_Menu_Checklist();
	$args   = [ 'walker' => $walker ];
	$items  = get_group_menu_items();

	?>
	<div id="openlab-menu" class="posttypediv">
		<div id="tabs-panel-openlab-all" class="tabs-panel tabs-panel-active">
			<ul id="openlab-menu-checklist" class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $items ), 0, (object) $args ); ?>
			</ul>
		</div>

		<p class="button-controls wp-clearfix">
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'openlab' ); ?>" name="add-custom-menu-item" id="submit-openlab-menu" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>
	<?php
}

/**
 * Set OpenLab item nav for the customizer.
 *
 * @param array $item_types Navigation menu item types.
 * @return array
 */
function register_customize_nav_menu_item_types( $item_types = [] ) {
	$item_types[] = [
		'title'      => __( 'OpenLab', 'openlab' ),
		'type_label' => __( 'OpenLab', 'openlab' ),
		'type'       => 'openab_nav',
		'object'     => 'openlab_box',
	];

	return $item_types;
}
add_filter( 'customize_nav_menu_available_item_types', __NAMESPACE__ . '\\register_customize_nav_menu_item_types' );

/**
 * Populate CBOX OpenLab nav menu items for the customizer.
 *
 * @param  array   $items  List of nav menu items.
 * @param  string  $type   Nav menu type.
 * @param  string  $object Nav menu object.
 * @param  integer $page   Page number.
 * @return array
 */
function register_customize_nav_menu_items( $items = [], $type = '', $object = '', $page = 0 ) {
	if ( $object !== 'openlab_box' ) {
		return $items;
	}

	// Don't allow pagination since all items are loaded at once.
	if ( 0 < $page ) {
		return $items;
	}

	$menu_items = get_group_menu_items();

	foreach ( $menu_items as $menu_item ) {
		$items[] = [
			'id'         => 'group-profile-link',
			'title'      => html_entity_decode( $menu_item->title, ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'type'       => $menu_item->type,
			'url'        => esc_url_raw( $menu_item->url ),
			'classes'    => $menu_item->classes,
			'type_label' => _x( 'Custom Link', 'customizer menu type label', 'openlab' ),
		];
	}

	return $items;
}
add_filter( 'customize_nav_menu_available_items', __NAMESPACE__ . '\\register_customize_nav_menu_items', 10, 4 );

/**
 * Setup custom OpenLab menu items.
 *
 * @param object $menu_item The menu item.
 * @return object $menu_item The modified menu item object.
 */
function setup_nav_menu_item( $menu_item ) {
	if ( is_admin() ) {
		return $menu_item;
	}

	// Skip if not "Group Profile" menu item.
	if ( ! in_array( 'menu-item-group-profile-link', $menu_item->classes, true ) ) {
		return $menu_item;
	}

	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	$group    = groups_get_group( $group_id );
	
	// Hide "Group Profile" when a group is inaccessible.
	if ( ! $group->is_visible ) {
		$menu_item->_invalid = true;
	}

	return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', __NAMESPACE__ . '\\setup_nav_menu_item' );
