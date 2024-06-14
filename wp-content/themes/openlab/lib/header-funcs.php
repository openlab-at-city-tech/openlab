<?php //header related functionality

if (!defined( 'BP_DISABLE_ADMIN_BAR' ) ){
	define('BP_DISABLE_ADMIN_BAR', true);
}

add_action( 'widgets_init', 'cuny_remove_default_widget_areas', 11 );
function cuny_remove_default_widget_areas() {
	unregister_sidebar('sidebar');
	unregister_sidebar('sidebar-alt');
}
/** Add support for custom background **/
add_theme_support( 'custom-background', array() );

add_action( 'wp_print_styles', 'cuny_no_bp_default_styles', 100 );

// Enqueue Styles For Testimonials Page & sub-pages
add_action('wp_print_styles', 'wds_cuny_ie_styles');
function wds_cuny_ie_styles() {
  if ( is_admin() )
	return;
	?>

	<!--[if lte IE 7]>
	  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie7.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 8]>
	  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie8.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 9]>
	  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie9.css" type="text/css" media="screen" />
	<![endif]-->


	<?php }

function cuny_no_bp_default_styles() {
	wp_dequeue_style( 'gconnect-bp' );
	wp_dequeue_script('superfish');
	wp_dequeue_script('superfish-args');

	wp_dequeue_style( 'gconnect-adminbar' );
}

function openlab_google_font() {
	wp_register_style('google-fonts', set_url_scheme( 'http://fonts.googleapis.com/css?family=Arvo' ),get_bloginfo( 'stylesheet_url' ));
	wp_enqueue_style('google-fonts');
}

add_action( 'wp_enqueue_scripts', 'openlab_google_font');

/**
 * Enqueue our front-end scripts
 */
function openlab_enqueue_frontend_scripts() {
	if ( ( bp_is_group_create() && bp_is_action_variable( 'group-details', 1 ) ) ||
		 ( bp_is_group_create() && bp_is_action_variable( 'invite-anyone', 1 ) ) ||
			 ( bp_is_group_admin_page() && bp_is_action_variable( 'edit-details', 0 ) ) ) {
		wp_enqueue_script( 'openlab-group-create', get_stylesheet_directory_uri() . '/js/group-create.js', array( 'jquery', 'parsley', 'openlab-validators' ) );

		$group_type_label = 'Group';
		if ( bp_is_group() ) {
			$group_type_label = openlab_get_group_type_label( [ 'case' => 'upper'] );
		} elseif ( bp_is_group_create() ) {
			$group_type = isset( $_GET['type'] ) ? wp_unslash( $_GET['type'] ) : 'Group';
			if ( 'portfolio' === $group_type ) {
				$user_type = openlab_get_user_member_type( get_current_user_id() );
				if ( 'student' === strtolower( $user_type ) ) {
					$group_type_label = 'ePortfolio';
				} else {
					$group_type_label = 'Portfolio';
				}
			} else {
				$group_type_label = ucwords( $group_type );
			}
		}

		wp_localize_script( 'openlab-group-create', 'OLGroupCreate', array(
			'groupTypeCanBeCloned' => true, // No need to rewrite JS logic. Just enable for all groups.
			'groupTypeLabel' => $group_type_label,
			'schools' => openlab_get_school_list(),
		) );
	}

	if ( bp_is_register_page() ) {
		wp_enqueue_script( 'openlab-registration', get_stylesheet_directory_uri() . '/js/register.js', array( 'jquery', 'parsley', 'openlab-validators' ) );

		wp_localize_script('openlab-registration', 'OLReg', array(
			'post_data' => $_POST,
		));
    }

    if ( bp_is_user_profile_edit() ) {
        wp_enqueue_script( 'openlab-profile-edit', get_stylesheet_directory_uri() . '/js/profile-edit.js', [ 'jquery', 'parsley', 'openlab-validators' ] );

		wp_add_inline_script(
			'openlab-profile-edit',
			'var olProfileEdit = ' . wp_json_encode( [
				'userLastName' => get_user_meta( bp_displayed_user_id(), 'last_name', true ),
				'userEmail'    => wp_get_current_user()->user_email,
			] ) . ';',
			'before'
		);
    }
}
add_action( 'wp_enqueue_scripts', 'openlab_enqueue_frontend_scripts', 20 );
