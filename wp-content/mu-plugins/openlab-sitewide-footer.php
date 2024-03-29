<?php

/**
 * Adds 'local environment' tab
 */
function cuny_local_env_flag() {
	// Don't load on widgets or other API requests.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

    if (defined('IS_LOCAL_ENV') && IS_LOCAL_ENV) {
        $env_type   = 'local';
		$git_branch = '';
        if (defined('ENV_TYPE')) {
            $env_type = ENV_TYPE;

			$git_head_path = ABSPATH . '/.git/HEAD';
			if ( is_readable( $git_head_path ) ) {
				$git_head = file( ABSPATH . '/.git/HEAD' );
				if ( $git_head ) {
					$git_branch = str_replace( 'ref: refs/heads/', '', $git_head[0] );
				}
			}
        }
        ?>

        <style type="text/css">
			@media screen {
				#local-env-flag {
					position: fixed;
					right: 0;
					bottom: 35px;
					width: 150px;
					padding: 10px 15px;
					text-align: center;
					background: #600;
					color: #fff;
					font-size: 1em;
					line-height: 1.8em;
					border: 2px solid #666;
					z-index: 99998;
					opacity: 0.7;
				}
			}

			@media print {
				#local-env-flag {
					display: none;
					visibility: hidden;
				}
			}
        </style>

        <div id="local-env-flag">
            <?php echo esc_html(strtoupper($env_type)) ?><br /><?php echo esc_html( $git_branch ); ?>
        </div>

        <?php
    }
}

add_action('wp_footer', 'cuny_local_env_flag');
add_action('admin_footer', 'cuny_local_env_flag');
add_action('login_footer', 'cuny_local_env_flag');

add_action('wp_print_styles', 'cuny_site_wide_navi_styles');

function cuny_site_wide_navi_styles() {
	global $wpdb;

    $sw_navi_styles = set_url_scheme(WPMU_PLUGIN_URL . '/css/sw-navi.css');

	wp_register_style( 'google-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic', array(), '2014', 'all' );
	wp_enqueue_style( 'google-open-sans' );

    wp_register_style('SW_Navi_styles', $sw_navi_styles);
    wp_enqueue_style('SW_Navi_styles');

//google fonts
    wp_register_style('google-fonts', set_url_scheme('http://fonts.googleapis.com/css?family=Arvo'), $sw_navi_styles);
    wp_enqueue_style('google-fonts');

	/*
	 * Custom admin bar styles.
	 *
	 * Loaded here because they contain global footer styles, which must be loaded
	 * even if the admin bar is disabled.
	 */
	$main_site_theme    = 'openlab';
	$openlab_theme_link = get_site_url( 1, 'wp-content/themes/' ) . $main_site_theme . '/css/font-awesome.min.css';
	$openlab_theme_link = set_url_scheme( $openlab_theme_link );

	// Making sure dashicons fire up for front end.
	if ( ! is_admin() ) {
		wp_register_style( 'dashicons', '/wp-includes/css/dashicons.min.css' );
		wp_enqueue_style( 'dashicons' );
	}

	// Registering font-awesome here so it can be used on the admin bar and on the main site.
	// Plugins don't play nice.
	wp_deregister_style( 'font-awesome' );
	wp_register_style( 'font-awesome', $openlab_theme_link, array(), '20130604', 'all' );
	wp_enqueue_style( 'font-awesome' );

	$adminbar_custom_url = WP_CONTENT_URL . '/mu-plugins/css/admin-bar-custom.css';
	$adminbar_custom_url = set_url_scheme( $adminbar_custom_url );
	$openlab_toolbar_url = WP_CONTENT_URL . '/mu-plugins/css/openlab-toolbar.css';
	$openlab_toolbar_url = set_url_scheme( $openlab_toolbar_url );

	wp_enqueue_style( 'admin-bar-custom', $adminbar_custom_url, array( 'font-awesome' ), '1.6.9' );
	wp_enqueue_style( 'openlab-toolbar', $openlab_toolbar_url, array( 'font-awesome' ), filemtime( WP_CONTENT_DIR . '/mu-plugins/css/openlab-toolbar.css' ) );
}

//add_action('wp_head', 'cuny_login_popup_script');
function cuny_login_popup_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            var cpl = jQuery('#cuny-popup-login');
            jQuery("#popup-login-link").show();
            jQuery(cpl).hide();

            jQuery("#popup-login-link").click(function () {
                if ('none' == jQuery(cpl).css('display')) {
                    jQuery(cpl).show();
                    jQuery("#sidebar-user-login").focus();
                } else {
                    jQuery(cpl).hide();
                }

                return false;
            });

            jQuery(".close-popup-login").click(function () {
                jQuery(cpl).hide();
            });
        });
    </script>
    <?php
}

add_action('wp_footer', 'cuny_site_wide_footer');

function cuny_site_wide_footer() {
    global $blog_id;

	// Don't load on widgets or other API requests.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

    switch_to_blog(1);
    $site = site_url();
    restore_current_blog();

    openlab_footer_markup();
    //see explanation below
    if (get_current_blog_id() !== 1) {
        openlab_footer_markup(true);
    }
}

/**
 * A Fancy Debug option: PHP notices were interfering with markup, however
    completely hiding them risks missing important issues that need
    addressing.

    To use this functionality, in env.php, add the following:

    -----

    define('FANCY_DEBUG', true);
    //delete log file
    $log = __DIR__ . "/wp-content/debug.log";
    unlink($log);

    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);

    -----

    This functionality works like this:

    1) First it clears any existing debug.log (to keep entries current to the
    page)
    2) Then it sets up WP_DEBUG to not display, but send errors to debug.log
    3) Finally, the code in openlab-sitewide-footer.php includes debug.log in the sitewide footer with some
    nice styling, so the errors are present, but not interefering with markup

    Suggestions to improve this approach are welcome
 */
function openlab_fancy_debug() {

    //debug
    if (defined('FANCY_DEBUG') && FANCY_DEBUG && WP_DEBUG && WP_DEBUG_LOG && !WP_DEBUG_DISPLAY) {

        $log = WP_CONTENT_DIR . "/debug.log";

        $debug = '<pre id="debugLog" style="position: fixed;bottom: 0;left: 0;width: 70%;background: #fff;padding: 10px; max-height: 150px;">';
        ob_start();
        include($log);
        $debug .= ob_get_clean();
        $debug .= '</pre>';

        echo $debug;
    }
}

add_action('wp_footer', 'openlab_fancy_debug');
add_action('admin_footer', 'openlab_fancy_debug');

/**
 * Markup for openlab footer
 * The placholder variable outputs a set of footer markup (minus any js) for purposes of
 * vertical spacing; some of the themes available to group sites have a max width limit set on the ** * body,so the visible footer is absolutely positioned in order to maintain the end-to-end
 * appearance across all themes. However, on smaller screen sizes, the footer naturally grows in
 * height, and yet the main page container has no idea this is happening. As a result, the footer
 * begins to creep up and hide page elements. The placholder footer is set to visibility: hidden, so
 * it will not be visible to the user, yet will provide the page container the necessary height
 * feedback to keep all page elements above the footer
 * Note: the above now only applies to group sites, the main OpenLab theme uses a different method
 * @param type $placholder
 */
function openlab_footer_markup($placeholder = NULL) {
    $footer_out = '';
    $blog_id = get_current_blog_id();

    $site = bp_get_root_domain();
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $accessibility_link = trailingslashit( $site ) . 'blog/help/summary-of-accessibility-on-the-openlab/';

    ob_start();
    include(WPMU_PLUGIN_DIR . '/parts/persistent/footer.php');
    $footer_out = ob_get_clean();

    echo $footer_out;
}

remove_action('init', 'maybe_add_existing_user_to_blog');
add_action('init', 'maybe_add_existing_user_to_blog', 90);

function bbg_debug_queries() {
    if (!is_super_admin()) {
        return;
    }

    if (empty($_GET['debug_queries'])) {
        return;
    }

    global $wpdb;
    echo '<pre>';
    foreach ($wpdb->queries as $q) {
        if ($q[1] > 1) {
            print_r($q);
        }
    }
}

//register_shutdown_function('bbg_debug_queries');
