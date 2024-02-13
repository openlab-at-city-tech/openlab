<?php


if ( ! class_exists( 'Redux' ) ) {
    return;
}

/**
 * Redux params
 */

$opt_name = 'typology_settings';

$args = array(
    'opt_name'             => $opt_name,
    'display_name'         => wp_kses( sprintf( __( 'Typology Options%sTheme Documentation%s', 'typology' ), '<a href="http://mekshq.com/documentation/typology" target="_blank">', '</a>' ), wp_kses_allowed_html( 'post' )),
    'display_version'      => typology_get_update_notification(),
    'menu_type'            => 'menu',
    'allow_sub_menu'       => true,
    'menu_title'           => esc_html__( 'Theme Options', 'typology' ),
    'page_title'           => esc_html__( 'Typology Options', 'typology' ),
    'google_api_key'       => '',
    'google_update_weekly' => false,
    'async_typography'     => true,
    'admin_bar'            => true,
    'admin_bar_icon'       => 'dashicons-admin-generic',
    'admin_bar_priority'   => '100',
    'global_variable'      => '',
    'dev_mode'             => false,
    'update_notice'        => false,
    'customizer'           => false,
    'allow_tracking' => false,
    'ajax_save' => true,
    'page_priority'        => '27.11',
    'page_parent'          => 'themes.php',
    'page_permissions'     => 'manage_options',
    'menu_icon'            => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDE2MDAiPiAgPHBhdGggZmlsbD0iIzlGQTRBOSIgZD0iTTI0NSAyNDV2MTExMGgxMTEwVjI0NUgyNDV6bTU1NS45IDU1OGgtMTI1djI2NC44SDc5OHYxMTdINTQ4LjhWODAzaC04MS4xVjY4Ni4xaDgxLjFWNDc2LjZoMTI3djIwOS41aDEyNVY4MDN6Ii8+PC9zdmc+',
    'last_tab'             => '',
    'page_icon'            => 'icon-themes',
    'page_slug'            => 'typology_options',
    'save_defaults'        => true,
    'default_show'         => false,
    'default_mark'         => '',
    'show_import_export'   => true,
    'transient_time'       => 60 * MINUTE_IN_SECONDS,
    'output'               => false,
    'output_tag'           => true,
    'database'             => '',
    'system_info'          => false
);

$GLOBALS['redux_notice_check'] = 1;

/* Footer social icons */

$args['share_icons'][] = array(
    'url'   => 'https://www.facebook.com/mekshq',
    'title' => 'Like us on Facebook',
    'icon'  => 'el-icon-facebook'
);

$args['share_icons'][] = array(
    'url'   => 'http://twitter.com/mekshq',
    'title' => 'Follow us on Twitter',
    'icon'  => 'el-icon-twitter'
);

$args['intro_text'] = '';
$args['footer_text'] = '';


/**
 * Initialize Redux
 */

Redux::setArgs( $opt_name , $args );


/**
 * Include redux option fields (settings)
 */

include_once get_parent_theme_file_path( '/core/admin/options-fields.php' );


/**
 * Check if there is available theme update
 *
 * @return string HTML output with update notification and the link to change log
 * @since  1.0
 */


function typology_get_update_notification() {
    $current = get_site_transient( 'update_themes' );
    $message_html = '';
    if ( isset( $current->response['typology'] ) ) {
        $message_html = '<span class="update-message">New update available!</span>
            <span class="update-actions">Version '.$current->response['typology']['new_version'].': <a href="http://mekshq.com/docs/typology-change-log" target="blank">See what\'s new</a><a href="'.admin_url( 'update-core.php' ).'">Update</a></span>';
    } else {
        $message_html = '<a class="theme-version-label" href="https://mekshq.com/docs/typology-change-log" target="blank">Version '.TYPOLOGY_THEME_VERSION.'</a>';
    }

    return $message_html;
}


/**
 * Append custom css to redux framework admin panel
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_redux_custom_css' ) ):
    function typology_redux_custom_css() {
        wp_register_style( 'typology-redux-custom', get_parent_theme_file_uri('/assets/css/admin/options.css'), array( 'redux-admin-css' ), TYPOLOGY_THEME_VERSION );
        wp_enqueue_style( 'typology-redux-custom' );
    }
endif;

add_action( 'redux/page/typology_settings/enqueue', 'typology_redux_custom_css' );




/**
 * Remove redux framework admin page
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_remove_redux_page' ) ):
    function typology_remove_redux_page() {
        remove_submenu_page( 'tools.php', 'redux-about' );
        remove_submenu_page( 'tools.php', 'redux-framework' );
    }
endif;

add_action( 'admin_menu', 'typology_remove_redux_page', 99 );

/* Prevent redux auto redirect */
update_option( 'redux_version_upgraded_from', 100 );
update_user_meta( get_current_user_id(), '_redux_welcome_guide', '1' );


/* More redux cleanup, blah... */

add_action( 'init', 'typology_redux_cleanup' );

if ( !function_exists( 'typology_redux_cleanup' ) ):
	function typology_redux_cleanup() {
		
		if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
			remove_action( 'admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );
		}
	}
endif;

/* Remove new redux banner bypassing class_exists */
class Redux_Connection_Banner {
    public static function init() {
        return false;
    }
    public static function tos_blurb() {
        return false;
    }
}


/**
 * Add section custom field to redux
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_section_field_path' ) ):
	function typology_section_field_path( $field ) {
		return get_parent_theme_file_path( '/core/admin/options-custom-fields/section/section.php' );
	}
endif;

add_filter( "redux/typology_settings/field/class/typology_section", "typology_section_field_path" );

?>
