<?php

if (!defined('CSS_DEBUG')) {
    define('CSS_DEBUG', false);
}

function openlab_core_setup() {
	global $content_width;

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'main'        => __( 'Main Menu', 'openlab' ),
		'aboutmenu'   => __( 'About Menu', 'openlab' ),
		'helpmenu'    => __( 'Help Menu', 'openlab' ),
		'helpmenusec' => __( 'Help Menu Secondary', 'openlab' ),
	) );
}
add_action( 'after_setup_theme', 'openlab_core_setup' );

/* * creating a library to organize functions* */
/* * core* */
$stylesheet_path = defined( 'STYLESHEETPATH' ) ? STYLESHEETPATH : get_template_directory();
$stylesheet_path = untrailingslashit( $stylesheet_path );
require_once( $stylesheet_path . '/lib/core/page-control.php' );
require_once( $stylesheet_path . '/lib/core/frontend-admin.php' );
require_once( $stylesheet_path . '/lib/core/backend-admin.php' );

require_once( $stylesheet_path . '/lib/wp-background-processing/wp-background-processing.php' );
require_once( $stylesheet_path . '/lib/clone-wp-async-process.php' );
require_once( $stylesheet_path . '/lib/course-clone.php' );
require_once( $stylesheet_path . '/lib/header-funcs.php' );
require_once( $stylesheet_path . '/lib/post-types.php' );
require_once( $stylesheet_path . '/lib/menus.php' );
require_once( $stylesheet_path . '/lib/content-processing.php' );
require_once( $stylesheet_path . '/lib/nav.php' );
require_once( $stylesheet_path . '/lib/breadcrumbs.php' );
require_once( $stylesheet_path . '/lib/shortcodes.php' );
require_once( $stylesheet_path . '/lib/media-funcs.php' );
require_once( $stylesheet_path . '/lib/group-funcs.php' );
require_once( $stylesheet_path . '/lib/ajax-funcs.php' );
require_once( $stylesheet_path . '/lib/help-funcs.php' );
require_once( $stylesheet_path . '/lib/member-funcs.php' );
require_once( $stylesheet_path . '/lib/page-funcs.php' );
require_once( $stylesheet_path . '/lib/sidebar-funcs.php' );
require_once( $stylesheet_path . '/lib/plugin-hooks.php' );
require_once( $stylesheet_path . '/lib/theme-hooks.php' );
require_once( $stylesheet_path . '/lib/group-announcements.php' );
require_once( $stylesheet_path . '/lib/group-connections.php' );
require_once( $stylesheet_path . '/lib/bp-auto-export-data.php' );
require_once( $stylesheet_path . '/lib/two-factor.php' );

require_once( $stylesheet_path . '/lib/site-template.php' );

// Initialize async cloning.
openlab_clone_async_process();

function openlab_load_scripts() {
    $stylesheet_dir_uri = get_stylesheet_directory_uri();

    /**
     * scripts, additional functionality
     */
    if (!is_admin()) {

        //google fonts
        wp_register_style('google-open-sans', set_url_scheme('http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic'), array(), '2014', 'all');
        wp_enqueue_style('google-open-sans');

        wp_register_style('camera-js-styles', $stylesheet_dir_uri . '/css/camera.css', array(), '20130604', 'all');
        wp_enqueue_style('camera-js-styles');

        //less compliation via js so we can check styles in firebug via fireless - local dev only
        if (CSS_DEBUG) {
            wp_register_script('less-config-js', $stylesheet_dir_uri . '/js/less.config.js', array('jquery'));
            wp_enqueue_script('less-config-js');
            wp_register_script('less-js', $stylesheet_dir_uri . '/js/less-1.7.4.js', array('jquery'));
            wp_enqueue_script('less-js');
        }

        wp_register_script('vendor-js', $stylesheet_dir_uri . '/js/dist/vendor.js', array('jquery'), '1.6.8', true);
        wp_enqueue_script('vendor-js');

        wp_register_script( 'openlab-academic-units', $stylesheet_dir_uri . '/js/academic-units.js', array( 'jquery' ) );

        $utility_deps = array( 'jquery' );
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            $utility_deps[] = 'hyphenator-js';
        } else {
            $utility_deps[] = 'openlab-smoothscroll';
        }
        wp_register_script('utility', $stylesheet_dir_uri . '/js/utility.js', $utility_deps, '1.7.0.2', true);
        wp_enqueue_script('utility');
        wp_localize_script('utility', 'localVars', array(
            'nonce' => wp_create_nonce('request-nonce'),
			'whatsHappeningAtCityTechEndpoint' => home_url( 'wp-json/openlab/v1/whats-happening-at-city-tech' ),
        ));

        wp_register_script('parsley', $stylesheet_dir_uri . '/js/parsley.min.js', array('jquery'));
        wp_register_script( 'openlab-validators', $stylesheet_dir_uri . '/js/validators.js', array('parsley') );
    }

    if ( is_page( 'people' ) || is_page( 'courses' ) || is_page( 'projects' ) || is_page( 'clubs' ) || is_page( 'portfolios' ) || openlab_is_search_results_page() ) {
        wp_enqueue_script( 'openlab-directory', $stylesheet_dir_uri . '/js/directory.js', array( 'jquery' ) );
        wp_localize_script(
            'openlab-directory',
            'OLDirectory',
			[
				'groupTypeDisabledFilters' => openlab_group_type_disabled_filters(),
            ]
        );
    }

	if ( bp_is_group() && bp_is_current_action( 'invite-anyone' ) ) {
		wp_enqueue_script( 'openlab-invite-anyone', $stylesheet_dir_uri . '/js/invite-anyone.js', [ 'jquery' ] );
	}

	if ( bp_is_current_action( 'invite-new-members' ) ) {
		wp_enqueue_script( 'openlab-invite-anyone', $stylesheet_dir_uri . '/js/invite-anyone-by-email.js', [ 'jquery', 'jquery-ui-autocomplete' ] );
	}

    if (bp_is_register_page()) {
        wp_enqueue_script('password-strength-meter');
    }

    if( bp_is_user() || bp_is_group() ) {
        wp_enqueue_script( 'openlab-activity', $stylesheet_dir_uri . '/js/activity.js', [ 'jquery' ] );
        wp_localize_script( 'openlab-activity', 'activityVars', array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ) );
    }

    wp_enqueue_script( 'openlab-group-documents', $stylesheet_dir_uri . '/js/group-documents.js', [ 'jquery' ] );

    if( bp_is_group() ) {
        wp_enqueue_script( 'openlab-group-membership', $stylesheet_dir_uri . '/js/membership-privacy.js', [ 'jquery' ] );
        wp_localize_script( 'openlab-group-membership', 'membershipVars', array(
            'ajax_url'  => admin_url( 'admin-ajax.php' )
        ) );
    }

	wp_register_script(
		'bp-docs-edit',
		$stylesheet_dir_uri . '/js/bp-docs-edit.js',
		[],
		OL_VERSION,
		true
	);
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts');

function openlab_admin_scripts() {
    wp_register_script('utility-admin', get_stylesheet_directory_uri() . '/js/utility.admin.js', array('jquery', 'jquery-ui-autocomplete'), '1.6.9.7', true);
    wp_enqueue_script('utility-admin');
    wp_localize_script('utility-admin', 'localVars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('request-nonce'),
    ));
}

add_action('admin_enqueue_scripts', 'openlab_admin_scripts');

/**
 * Giving the main stylesheet the highest priority among stylesheets to make sure it loads last
 */
function openlab_load_scripts_high_priority() {
    $stylesheet_dir_uri = get_stylesheet_directory_uri();

    global $post;
    //less compliation via js so we can check styles in firebug via fireless - local dev only
    //@to-do: way to enqueue as last item?
    if (CSS_DEBUG) {
        wp_register_style('main-styles', $stylesheet_dir_uri . '/style.less', array(), '1.7.0.5', 'all');
        wp_enqueue_style('main-styles');
    } else {

        wp_register_style('main-styles', $stylesheet_dir_uri . '/style.css', array(), '1.7.0.5', 'all');
        wp_enqueue_style('main-styles');
    }

    if (isset($post->post_type) && $post->post_type == 'help') {
        wp_register_style('print-styles', $stylesheet_dir_uri . '/css/print.css', array(), '2015', 'print');
        wp_enqueue_style('print-styles');
    }

	wp_enqueue_style(
		'openlab-print',
		get_stylesheet_directory_uri() . '/print.css',
		array(),
		OL_VERSION,
		'print'
	);
}

add_action('wp_enqueue_scripts', 'openlab_load_scripts_high_priority', 999);

/**
 * Custom image sizes
 */
//front page slider
add_image_size('front-page-slider', 735, 295, true);

//custom widgets for OpenLab
function cuny_widgets_init() {
    //add widget for Rotating Post Gallery Widget - will be placed on the homepage
    register_sidebar(array(
        'name' => __('Rotating Post Gallery Widdget', 'cuny'),
        'description' => __('This is the widget for holding the Rotating Post Gallery Widget', 'cuny'),
        'id' => 'pgw-gallery',
        'before_widget' => '<div id="pgw-gallery">',
        'after_widget' => '</div>',
    ));
    //add widget for the Featured Widget - will be placed on the homepage under "In the Spotlight"
    register_sidebar(array(
        'name' => __('Featured Widget', 'cuny'),
        'description' => __('This is the widget for holding the Featured Widget', 'cuny'),
        'id' => 'cac-featured',
        'before_widget' => '<div id="cac-featured" class="links-lighter-hover">',
        'after_widget' => '</div>',
    ));
}

add_action('widgets_init', 'cuny_widgets_init');

function cuny_o_e_class($num) {
    return $num % 2 == 0 ? " even" : " odd";
}

function cuny_third_end_class($num) {
    return $num % 3 == 0 ? " last" : "";
}

/**
 * Modify the body class
 *
 * Invite New Members and Your Email Options fall under "Settings", so need
 * an appropriate body class.
 */
function openlab_group_admin_body_classes($classes) {
    if (!bp_is_group()) {
        return $classes;
    }

    if (in_array(bp_current_action(), array('invite-anyone', 'notifications'))) {
        $classes[] = 'group-admin';
    }

    return $classes;
}

add_filter('bp_get_the_body_class', 'openlab_group_admin_body_classes');

//for less js - local dev only
function enqueue_less_styles($tag, $handle) {
    global $wp_styles;
    $match_pattern = '/\.less$/U';
    if (preg_match($match_pattern, $wp_styles->registered[$handle]->src)) {
        $handle = $wp_styles->registered[$handle]->handle;
        $media = $wp_styles->registered[$handle]->args;
        $href = $wp_styles->registered[$handle]->src;
        $rel = isset($wp_styles->registered[$handle]->extra['alt']) && $wp_styles->registered[$handle]->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
        $title = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr($wp_styles->registered[$handle]->extra['title']) . "'" : '';

        $tag = "<link rel='stylesheet/less' $title href='$href' type='text/css' media='$media' />";
    }
    return $tag;
}

add_filter('style_loader_tag', 'enqueue_less_styles', 5, 2);

/**
 * Get content with formatting in place
 * @param type $more_link_text
 * @param type $stripteaser
 * @param type $more_file
 * @return type
 */
function get_the_content_with_formatting($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
}

/**
 * Get a value from a failed POST request, especially during registration.
 */
function openlab_post_value($key) {
    $value = '';
    if (!empty($_POST[$key])) {
        $value = wp_unslash($_POST[$key]);
    }
    return $value;
}

/**
 * Disable the new avatar upload interface introduced in BP 2.3.
 */
add_filter('bp_avatar_is_front_edit', '__return_false');

/**
 * Generate data attributes for xprofile 'input' fields.
 *
 * Used for Parsely validation.
 */
function openlab_profile_field_input_attributes() {
    $attributes = array();
    $field_name = bp_get_the_profile_field_input_name();

    switch (bp_get_the_profile_field_name()) {
        case 'Name' :
            $attributes[] = 'data-parsley-required';
            $attributes[] = 'data-parsley-required-message="Display Name is required."';
            $attributes[] = "data-parsley-errors-container='#{$field_name}_confirm_error'";
            break;

        case 'First Name' :
            $attributes[] = 'data-parsley-required';
            $attributes[] = 'data-parsley-required-message="First Name is required."';
            $attributes[] = "data-parsley-errors-container='#{$field_name}_confirm_error'";
            break;

        case 'Last Name' :
            $attributes[] = 'data-parsley-required';
            $attributes[] = 'data-parsley-required-message="Last Name is required."';
            $attributes[] = "data-parsley-errors-container='#{$field_name}_confirm_error'";
            break;

        case 'Major Program of Study':
        case 'Department':

            $field = bp_get_the_profile_field_name();

            $attributes[] = 'data-parsley-required';
            $attributes[] = "data-parsley-required-message='$field is required.'";
            $attributes[] = "data-parsley-errors-container='#{$field_name}_confirm_error'";
            break;
    }

    if ($attributes) {
        return ' ' . implode(' ', $attributes) . ' ';
    }
}

/**
 * Don't allow the /profile/ page to be accessed.
 */
function openlab_redirect_from_member_profile() {
    if (bp_is_user_profile() && bp_is_current_action('public')) {
        wp_redirect(bp_displayed_user_domain());
        die();
    }
}

add_action('template_redirect', 'openlab_redirect_from_member_profile');

/**
 * Reusable markup for the "Notify subscribed..." UI.
 *
 * @param bool $checked Whether the checkbox should be checked.
 */
function openlab_notify_group_members_ui( $checked = false ) {
    ?>
<label><input type="checkbox" name="ol-notify-group-members" value="1" class="ol-notify-group-members" <?php checked( $checked ); ?> /> Notify subscribed members by email</label>
    <?php
}

/**
 * Reusable wrapper for checking whether the "Notify subscribed..." checkbox was checked.
 *
 * @return bool
 */
function openlab_notify_group_members_of_this_action() {
    return ! empty( $_POST['ol-notify-group-members'] );
}

/**
 * Prevent wpautop from running on group description excerpts.
 *
 * This breaks the markup necessary for auto-truncation.
 */
add_filter(
	'bp_get_group_description_excerpt',
	function( $excerpt ) {
		remove_filter( 'bp_get_group_description_excerpt', 'wpautop' );
		return $excerpt;
	},
	0
);

/**
 * Is this the search results page?
 */
function openlab_is_search_results_page() {
	return is_page( 'search' );
}

/**
 * Gets our custom visibility levels for xprofile fields.
 *
 * @return array
 */
function openlab_get_xprofile_visibility_levels() {
	$visibility_levels = bp_xprofile_get_visibility_levels();

	// Reorder: public, loggedin, friends, adminsonly
	$visibility_levels = array_merge(
		array(
			'public'     => $visibility_levels['public'],
			'loggedin'   => $visibility_levels['loggedin'],
			'friends'    => $visibility_levels['friends'],
			'adminsonly' => $visibility_levels['adminsonly'],
		)
	);

	$visibility_levels['loggedin']['label'] = 'OpenLab Members';
	$visibility_levels['friends']['label']  = 'Friends';

	return $visibility_levels;
}

/**
 * Generates the markup for xprofile field visibility selector.
 *
 * @param int $field_id The ID of the field.
 */
function openlab_xprofile_field_visibility_selector( $field_id = null ) {
	if ( ! $field_id ) {
		$field_id = bp_get_the_profile_field_id();
	}

	$visibility_levels = openlab_get_xprofile_visibility_levels();

	$selected_value = bp_is_register_page() ? '' : xprofile_get_field_visibility_level( $field_id, bp_displayed_user_id() );

	if ( bp_current_user_can( 'bp_xprofile_change_field_visibility', $field_id ) ) : ?>
		<div class="field-visibility-settings" id="field-visibility-settings-<?php echo esc_attr( $field_id ); ?>">
			<label for="field-visibility-settings-select-<?php echo esc_attr( $field_id ); ?>">Who can see this?</label>

			<select name="<?php echo esc_attr( 'field_' . $field_id . '_visibility' ); ?>" id="field-visibility-settings-select-<?php echo esc_attr( $field_id ); ?>">
				<?php if ( bp_is_register_page() ) : ?>
					<option value="">----</option>
				<?php endif; ?>

				<?php foreach( $visibility_levels as $level ) : ?>
					<option value="<?php echo esc_attr( $level['id'] ) ?>"<?php selected( $selected_value, $level['id'], true ) ?>><?php echo esc_html( $level['label'] ) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif;
}

/**
 * Renders a collapsible definition.
 *
 * @param string $type    The type of definition (e.g. 'member-role')
 * @param string $label   The label for the definition.
 * @param string $content The content of the definition.
 */
function openlab_render_collapsible_definition( $type, $label, $content ) {
	?>
	<div class="collapsible-definition <?php echo esc_attr( $type ); ?>-definition col-sm-24">
		<div class="collapsible-definition-label <?php echo esc_attr( $type ); ?>-definition-label">
			<i class="fa fa-chevron-circle-right" aria-hidden="true"></i>
			<span><?php echo esc_html( $label ); ?></span>
		</div>
		<div class="collapsible-definition-text <?php echo esc_attr( $type ); ?>-definition-text">
			<?php echo $content; // Output is escaped where it's created ?>
		</div>
	</div>
	<?php
}
