<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Will be truthy if the plugin is installed
 *
 * @param  string $name name of the plugin 'ml-slider'
 * @return bool|string - will return path, ex. 'ml-slider/ml-slider.php'
 */
function metaslider_plugin_is_installed($name = 'ml-slider')
{
    // @since 3.101 - Get path from db if available
    $path = metaslider_plugin_data( $name, 'path' );
    if ( $path && file_exists( WP_PLUGIN_DIR . '/' . $path ) ) {
        return $path;
    }

    // Callback for the old way of getting path
    if (!function_exists('get_plugins')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $plugins = get_plugins();
    
    $delete_cache = apply_filters( 'metaslider_plugins_delete_cache', true );
    if ( $delete_cache ) {
        // Don't cache plugins this early
        wp_cache_delete('plugins', 'plugins');
    }

    foreach ($plugins as $plugin => $data) {
        if ($data['TextDomain'] == $name) {
            return $plugin;
        }
    }
    return false;
}

/**
 * Will return the stored plugin data
 *
 * @since 3.101
 * 
 * @param  string $slug name of the plugin 'ml-slider' or 'ml-slider-pro'
 * @param  string $data name of the data to return. e.g. 'path' or 'version'
 * 
 * @return bool|string - will return path or version, e.g. 'ml-slider/ml-slider.php' or '3.101'
 */
function metaslider_plugin_data( $slug = 'ml-slider', $data = 'path' )
{
    $skip       = apply_filters( 'metaslider_skip_get_plugin_data', false );
    $allowed    = array( 'version', 'path' );

    // Short circuit if we want to skip or $data is invalid
    if ( $skip === true || ! in_array( $data, $allowed ) ) {
        return false;
    }

    if ( $slug == 'ml-slider' ) {
        return get_option( 'metaslider_plugin_' . $data );
    } elseif ( $slug == 'ml-slider-pro' ) {
        return get_option( 'metaslider_pro_plugin_' . $data );
    }

    return false;
}

/**
 * checks if metaslider pro is installed
 *
 * @return bool
 */
function metaslider_pro_is_installed()
{
    return (bool) metaslider_plugin_is_installed('ml-slider-pro');
}

/**
 * Will be true if the plugin is active
 *
 * @return bool
 */
function metaslider_pro_is_active()
{
    return function_exists('is_plugin_active') && is_plugin_active(metaslider_plugin_is_installed('ml-slider-pro'));
}

/**
 * Returns true if the user does not have the pro version installed
 *
 * @return bool
 */
function metaslider_user_sees_upgrade_page()
{
    return (bool) apply_filters('metaslider_show_upgrade_page', !metaslider_pro_is_installed());
}

/**
 * Returns true if the user does not have the pro version installed
 *
 * @return bool
 */
function metaslider_user_has_at_least_one_slideshow()
{
    $posts = get_posts(array('posts_per_page' => 1, 'post_type' => 'ml-slider'));
    return (bool) is_array($posts) && count($posts);
}

/**
 * Returns true if the user does not have the pro version installed
 *
 * @return bool
 */
function metaslider_user_sees_call_to_action()
{
    return (bool) apply_filters('metaslider_show_upgrade_page', !metaslider_pro_is_installed());
}

/**
 * Returns true if the user is ready to see notices. Exceptions include
 * when they have no slideshows (first start) and while on the initial tour.
 *
 * @return boolean
 */
function metaslider_user_is_ready_for_notices()
{
    $args = array(
        'post_type' => 'ml-slider',
        'post_status' => 'publish',
        'suppress_filters' => 1, // wpml, ignore language filter
        'order' => 'ASC',
        'posts_per_page' => -1
    );

    // If no slideshows, don't show a notice
    if (!count(get_posts($args))) {
        return false;
    }

    // If they have slideshows but have yet to finish the tour or cancel it,
    // hold off on showing notices
    return (bool) get_option('metaslider_tour_cancelled_on');
}

/**
 * Returns true if the user is on the specified admin page
 *
 * @param  string $page_name Admin page name
 * @return boolean
 */
function metaslider_user_is_on_admin_page($page_name = 'admin.php')
{
    global $pagenow;
    return ($pagenow == $page_name);
}

/**
 * Returns the upgrade link
 *
 * @return string
 */
function metaslider_get_upgrade_link()
{
    return esc_url(apply_filters('metaslider_hoplink', add_query_arg(array(
        'utm_source' => 'lite',
        'utm_medium' => 'banner',
        'utm_campaign' => 'pro',
    ), 'https://www.metaslider.com/upgrade')));
}

/**
 * Returns the privacy policy link
 *
 * @return string
 */
function metaslider_get_privacy_link()
{
    return esc_url('https://www.metaslider.com/privacy-policy/');
}

/**
 * Returns an array of the trashed slides
 *
 * @param int $slider_id Slider ID
 * @return array
 */
function metaslider_has_trashed_slides($slider_id)
{
    return get_posts(array(
        'force_no_custom_order' => true,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_type' => array('attachment', 'ml-slide'),
        'post_status' => array('trash'),
        'lang' => '',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'ml-slider',
                'field' => 'slug',
                'terms' => $slider_id
            )
        )
    ));
}

/**
 * Returns whether we are looking at trashed slides
 *
 * @param int $slider_id - the id
 * @return bool
 */
function metaslider_viewing_trashed_slides($slider_id)
{

    // If there are no trashed slides, no need to see this page
    if (!count(metaslider_has_trashed_slides($slider_id))) {
        return false;
    }

    // Checks to see if the parameter is set and if it's boolean
    return isset($_REQUEST['show_trashed']) && filter_input(INPUT_GET, 'show_trashed', FILTER_VALIDATE_BOOLEAN);
}

/**
 * Returns the current pro version registered by WP
 *
 * @return string
 */
function metaslider_pro_version()
{
    // @since 3.101 - Get version from db if available
    if ( $version = metaslider_plugin_data( 'ml-slider-pro', 'version' ) ) {
        return $version;
    }

    // Callback for the old way of getting version
    $file = trailingslashit(WP_PLUGIN_DIR) . metaslider_plugin_is_installed('ml-slider-pro');
    $data = get_file_data($file, array('Version' => 'Version'));
    return $data['Version'];
}

/**
 * Returns the current version registered by WP
 *
 * @return string
 */
function metaslider_version()
{
    // @since 3.101 - Get version from db if available
    if ( $version = metaslider_plugin_data( 'ml-slider', 'version' ) ) {
        return $version;
    }

    // Callback for the old way of getting version
    $file = trailingslashit(WP_PLUGIN_DIR) . metaslider_plugin_is_installed('ml-slider');
    $data = get_file_data($file, array('Version' => 'Version'));
    return $data['Version'];
}

/**
 * Returns whether we are looking at a trashed slide
 *
 * @param object $slide a slide object
 * @return bool
 */
function metaslider_this_is_trash($slide)
{
    return (is_object($slide) && "trash" === $slide->post_status);
}

/**
 * This will customize a URL with a correct Affiliate link
 *
 * This function can be updated to suit any URL as long as the URL is passed
 *
 * @param string $url   URL to be checked to see if it is an metaslider match.
 * @param string $text  Anchor Text
 * @param string $html  Any specific HTML to be added.
 * @param string $class Specify a class for the anchor tag.
 *
 * @return string Optimized affiliate link
 */
function metaslider_optimize_url($url, $text, $html = null, $class = '')
{

    // Check if the URL is metaslider.
    if (false !== strpos($url, 'metaslider.com')) {
        // Set URL with Affiliate ID.
        $url = metaslider_get_upgrade_link();
    }

    // Return URL - check if there is HTML such as Images.
    if (!empty($html)) {
        return sprintf('<a class="%1$s" href="%2$s">%3$s</a>', esc_attr($class), esc_url($url), $html);
    } else {
        return sprintf('<a class="ml-upgrade-button %1$s" href="%2$s">%3$s</a>', esc_attr($class), esc_url($url), htmlspecialchars($text));
    }
}

/**
 * Check if meta value is enabled
 * 
 * @since 3.100
 * 
 * @param mixed $value
 * 
 * @return bool
 */
function metaslider_option_is_enabled( $value ) 
{
    return $value === 'yes' || $value === 'on' || $value === true || $value == 1;
}

/**
 * Used to filter out empty strings/arrays with array_filter()
 *
 * @since 3.100
 * 
 * @param mixed $item The item being tested
 * 
 * @return bool - Will return whether empty on arrays/strings
 */
function metaslider_remove_empty_vars($item)
{
    // If it's an array and not empty, keep it (return true)
    if (is_array($item)) {
        return ! empty($item);
    }

    // If it's a string and not '', keep it (return true)
    if (is_string($item)) {
        return ('' !== trim($item));
    }

    // Not likely to get this far but just in case, keep everything else
    return true;
}

/**
 * Check if we're using native width/height from slideshow main options
 * or custom image width/height
 * 
 * @since 3.100
 * 
 * @param $side string          'width' or 'height' only
 * @param $settings array|null  Slideshow settings
 * 
 * @return int|bool
 */
function metaslider_image_cropped_size( $side, $settings ) 
{
    if ( ! in_array( $side, array( 'width', 'height' ) ) ) {
        return false;
    }

    $Side = ucfirst( $side ); // e.g 'width' -> 'Width'

    if ( class_exists( 'MetaSliderPro' ) 
        && isset( $settings['smartCropSource'] ) 
        && $settings['smartCropSource'] == 'image' 
    ) {
        // e.g. we look for 'imageWidth' settings
        if ( isset( $settings['image' . $Side] ) && absint( $settings['image' . $Side] ) > 0 ) {
            return absint( $settings['image' . $Side] );
        }
    }

    return isset( $settings[$side] ) ? $settings[$side] : 0; // Slideshow width or height setting
}

/**
 * Get global settings
 *
 * @since 3.101
 * 
 * @return array
 */
function metaslider_global_settings()
{
    if ($settings = get_option('metaslider_global_settings')) {
        return $settings;
    }

    return array();
}

/**
 * Upgrade to pro small yellow button with lock icon
 * 
 * @since 3.101
 * 
 * @param string $text Optional tooltip text
 * 
 * @return html
 */
function metaslider_upgrade_pro_small_btn($text = '')
{
    if (empty($text)) {
        $text = __( 'Some of these features are available in MetaSlider Pro', 'ml-slider' );
    }
    
    $link = 'https://www.metaslider.com/upgrade?utm_source=lite&utm_medium=banner&utm_campaign=pro';
    return '<a class="dashicons dashicons-lock is-pro-setting tipsy-tooltip-top" original-title="' . 
        esc_attr( $text ) . '" href="' . 
        esc_url( $link ) . '" target="_blank"></a>';
}

/**
 * Get the closest image based on a width size
 * 
 * @since 3.102
 * 
 * @param int $width            Image width we want to target
 * @param int $attachment_id    Image ID
 * 
 * @return string A valid media image URL or a placeholder URL
 */
function metaslider_intermediate_image_src( $width, $attachment_id )
{
    $image_sizes = wp_get_attachment_image_src( $attachment_id, 'full' );

    if ( is_array( $image_sizes ) && count( $image_sizes ) ) {
        $original_width = $image_sizes[1]; // Image width value from array
        
        // Find the closest image size to $width in width
        $sizes = get_intermediate_image_sizes(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_intermediate_image_sizes_get_intermediate_image_sizes

        // Default if no smaller size is found
        $closest_size = 'full'; 

        foreach ( $sizes as $size ) {
            $size_info  = image_get_intermediate_size( $attachment_id, $size );

            if ( isset( $size_info['width'] ) 
                && $size_info['width'] >= $width 
                && $size_info['width'] < $original_width 
            ) {
                $closest_size = $size;
                break;
            }
        }

        // Get the URL of the closest image size.
        $closest_image = wp_get_attachment_image_src( $attachment_id, $closest_size );
        
        // $closest_image[0] URL
        // $closest_image[1] width
        // $closest_image[2] height
        // $closest_image[3] boolean for: is the image cropped?

        if ( is_array( $closest_image ) ) {
            $image_ = is_ssl() ? set_url_scheme( $closest_image[0], 'https' ) : set_url_scheme( $closest_image[0], 'http' );
            return $image_;
        }
    }

    return METASLIDER_ASSETS_URL . 'metaslider/placeholder-thumb.jpg';
}

/**
 * Filter unsafe HTML from slide content (e.g. caption)
 * 
 * @since 3.103
 * 
 * @param string $content    The HTML content to be purified
 * @param array  $slide      The slide data such as id, caption, caption_raw, etc.
 * @param int    $slider_id  The slideshow ID
 * @param array  $settings   The slideshow settings
 * 
 * @return string The purified HTML content
 */
function metaslider_filter_unsafe_html( $content, $slide, $slider_id, $settings )
{
    try {
        if ( ! class_exists( 'HTMLPurifier' ) ) {
            require_once( METASLIDER_PATH . 'lib/htmlpurifier/library/HTMLPurifier.auto.php' );
        }
        $config = HTMLPurifier_Config::createDefault();
        // How to filter:
        // add_filter('metaslider_html_purifier_config', function($config) {
        //     $config->set('HTML.Allowed', 'a[href|target]');
        //     $config->set('Attr.AllowedFrameTargets', array('_blank'));
        //     return $config;
        // });
        $config   = apply_filters('metaslider_html_purifier_config', $config, $slide, $slider_id, $settings);
        $purifier = new HTMLPurifier( $config );
        $content  = $purifier->purify( $content );
    } catch ( Exception $e ) {
        // If something goes wrong then escape
        $content = htmlspecialchars( do_shortcode( $content ), ENT_NOQUOTES, 'UTF-8' );
    }

    return $content;
}