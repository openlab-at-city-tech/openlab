<?php
/**
 * MU Plugins enqueues
 * Keeping this all in once place
 */

/**
 * Add a custom version to the querystring for cache busting on OL version updates.
 */
function openlab_asset_ver($tag, $handle, $src = '') {
    // 'style_loader_tag' doesn't pass a src, so we sniff it from the tag.
    if (!$src) {
        preg_match('/href\=\'([^\']+)\'/', $tag, $src_matches);
        if ($src_matches) {
            $src = $src_matches[1];
        }
    }

    // Unencode ampersands etc.
    $src = str_replace('&#038;', '&', $src);
    $_src = parse_url($src);

    // Ignore non-openlab assets.
    if (false === strpos($_src['host'], 'openlab')) {
        return $tag;
    }

    if (!empty($_src['query'])) {
        wp_parse_str($_src['query'], $vars);
        foreach ($vars as $k => &$v) {
            if ('ver' !== $k) {
                continue;
            }

            $v .= '-' . OL_VERSION;
        }

        $new_path_and_query = add_query_arg($vars, $_src['path']);
        $tag = str_replace($_src['path'] . '?' . $_src['query'], $new_path_and_query, $tag);
    }

    return $tag;
}

add_filter('script_loader_tag', 'openlab_asset_ver', 10, 3);
add_filter('style_loader_tag', 'openlab_asset_ver', 10, 2);

function openlab_mu_enqueue() {

    // google plus one
    wp_register_script('google-plus-one', 'https://apis.google.com/js/plusone.js');
    wp_enqueue_script('google-plus-one');

    $js_ver = '1.6.9.1';

    // adding smooth scroll
    if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
        wp_register_script('smoothscroll-js', plugins_url('js', __FILE__) . '/jquery-smooth-scroll/jquery.smooth-scroll.min.js', array('jquery'), '', true);
        wp_enqueue_script('smoothscroll-js');
        wp_register_script('select-js', plugins_url('js', __FILE__) . '/select2/select2.min.js', array('jquery'), '', true);
        wp_enqueue_script('select-js');
        wp_register_script('hyphenator-js', plugins_url('js', __FILE__) . '/hyphenator/hyphenator.js', array('jquery'));
        wp_enqueue_script('hyphenator-js');
        wp_register_script('succinct-mod-js', plugins_url('js', __FILE__) . '/succint/jQuery.succinct.mod.js', array('jquery'));
        wp_enqueue_script('succinct-mod-js');
        wp_register_script('openlab-search-js', plugins_url('js', __FILE__) . '/openlab/openlab.search.js', array('jquery'), $js_ver, true);
        wp_enqueue_script('openlab-search-js');
        wp_register_script('openlab-truncation-js', plugins_url('js', __FILE__) . '/openlab/openlab.truncation.js', array('jquery'), $js_ver);
        wp_enqueue_script('openlab-truncation-js');
        wp_register_script('openlab-nav-js', plugins_url('js', __FILE__) . '/openlab/openlab.nav.js', array('jquery'), $js_ver, true);
        wp_enqueue_script('openlab-nav-js');
        wp_localize_script(
                'openlab-nav-js', 'utilityVars', array(
            'loginForm' => openlab_get_loginform(),
                )
        );

        wp_register_script('openlab-theme-fixes-js', plugins_url('js', __FILE__) . '/openlab/openlab.theme.fixes.js', array('jquery'), $js_ver, true);
        wp_enqueue_script('openlab-theme-fixes-js');
    } else {
        wp_enqueue_script('openlab-smoothscroll', content_url('js/smoothscroll.js'), array('jquery'), $js_ver);
        wp_localize_script(
                'openlab-smoothscroll', 'utilityVars', array(
            'loginForm' => openlab_get_loginform(),
                )
        );
    }
}

add_action('wp_enqueue_scripts', 'openlab_mu_enqueue', 9);
add_action('admin_enqueue_scripts', 'openlab_mu_enqueue');

function openlab_script_additional_attributes($good_protocol_url, $original_url, $_context) {

    if (false !== strpos($original_url, 'plusone.js')) {
        remove_filter('clean_url', 'openlab_script_additional_attributes', 10, 3);
        $url_parts = parse_url($good_protocol_url);
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' async defer='defer";
    }
    return $good_protocol_url;
}

add_filter('clean_url', 'openlab_script_additional_attributes', 10, 3);

/**
 * Concatenate buddypress.js dependencies.
 *
 * @param array $deps
 * @return array
 */
function openlab_bp_js_dependencies($deps) {
    if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
        return $deps;
    }

    wp_register_script('openlab-buddypress', content_url('js/buddypress.js'), array('jquery'));

    $concat = array(
        'bp-confirm',
        'bp-widget-members',
        'bp-jquery-query',
        'bp-jquery-cookie',
        'bp-jquery-scroll-to',
    );

    $deps = array_diff($deps, $concat);
    $deps[] = 'openlab-buddypress';

    wp_deregister_script('bp-confirm');
    wp_localize_script(
            'openlab-buddypress', 'BP_Confirm', array(
        'are_you_sure' => __('Are you sure?', 'buddypress'),
            )
    );

    return $deps;
}

add_filter('bp_core_get_js_dependencies', 'openlab_bp_js_dependencies');

/**
 * Dequeue scripts for BP plugins.
 *
 * The scripts are concatenated with openlab-buddypress.
 */
function openlab_bp_js_concat() {
    if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
        return;
    }

    if (!bp_is_root_blog()) {
        return;
    }

    wp_dequeue_script('bp-group-documents');
    wp_dequeue_script('bp-activity-subscription-js');
}

add_action('wp_print_scripts', 'openlab_bp_js_concat', 0);

/**
 * Dequeue late-loaded scripts that would normally print to the footer.
 *
 * Concatenated in openlab-buddypress.
 */
function openlab_js_late_load_dequeue() {
    if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
        return;
    }

    if (!bp_is_root_blog()) {
        return;
    }

    wp_dequeue_script('bp-mentions');
}

add_action('wp_print_footer_scripts', 'openlab_js_late_load_dequeue', 0);

/**
 * Concatenate styles on main site.
 */
function openlab_css_concat() {
    if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
        return;
    }

    if (!bp_is_root_blog()) {
        return;
    }

    // Dequeues.
    // Achievements.
    wp_dequeue_style('dpa-default-achievements');

    // bbPress.
    wp_dequeue_style('bbp-default');

    // Contact Form 7.
    wp_dequeue_style('contact-form-7');

    // Post Gallery Widget.
    wp_dequeue_style('pgw-cycle');

    // BuddyPress.
    wp_dequeue_style('bp-legacy-css');
    wp_dequeue_style('bp-mentions-css');

    // BuddyPress Group Email Subscription.
    wp_dequeue_style('activity-subscription-style');

    // Enqueue concatentated styles.
    wp_enqueue_style('openlab-root-blog-css', content_url('css/root-blog-styles.css'));
}

add_action('wp_print_styles', 'openlab_css_concat', 0);

/**
 * Dequeue late-loaded styles.
 *
 * Styles loaded here are concatenated in root-blog-styles.css.
 */
function openlab_css_late_load_dequeue() {
    // CAC Featured Content.
    wp_dequeue_style('cfcw-default-styles');
}

add_action('wp_print_footer_scripts', 'openlab_css_late_load_dequeue', 0);

function openlab_gtm_instatiation() {
    ?>

    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({'gtm.start':
                        new Date().getTime(), event: 'gtm.js'});
            var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-T5XJ92C');</script>
    <!-- End Google Tag Manager -->

    <?php
}

add_action('wp_head', 'openlab_gtm_instatiation', 1);

/**
 * If Javascript is present, obfuscates adminbar until truncation operations are complete
 * Otherwise there is some nasty FOUT going on
 */
function openlab_truncation_prep() {
    ?>
    <!-- truncate on the fly prep -->
    <script>

        document.write('<style>.truncate-obfuscate { opacity: 0; } .menu-loading { overflow: hidden; height: 50px; }</style>');

    </script>

    <?php
}

add_action('wp_head', 'openlab_truncation_prep', 2);
add_action('admin_head', 'openlab_truncation_prep', 2);

function admin_bar_padding_in_admin() {
    ?>

    <style type="text/css" media="screen">
        html.wp-toolbar {
            padding-top: 0;
        }
        html.wp-toolbar #wpcontent,
        html.wp-toolbar #adminmenuwrap{
            padding-top: 50px;
        }
        @media (max-width: 767px){
            html.wp-toolbar #wpcontent,
            html.wp-toolbar #adminmenuwrap{
                padding-top: 115px;
            }
            html.wp-toolbar #wpbody{
                padding-top: 0;
            }
        }
    </style>

    <?php
}

add_action('admin_footer', 'admin_bar_padding_in_admin');

function admin_bar_device_width_detection() {
	// Don't load on widgets or other API requests.
	if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
		return;
	}

    /**
     * Adds divs that can be used for client-side detection of bootstrap breakpoints
     */
    ?>
    <div class = "oplb-bs">
        <div class = "device-xxs visible-xxs"></div>
        <div class = "device-xs visible-xs"></div>
        <div class = "device-sm visible-sm"></div>
        <div class = "device-md visible-md"></div>
        <div class = "device-lg visible-lg"></div>
    </div>
    <?php
}

add_action('wp_footer', 'admin_bar_device_width_detection');
add_action('admin_footer', 'admin_bar_device_width_detection');
