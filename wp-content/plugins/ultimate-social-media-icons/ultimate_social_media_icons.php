<?php
/*
Plugin Name: Social Media and Share Icons (Ultimate Social Media)
Plugin URI: http://ultimatelysocial.com
Description: Easy to use and 100% FREE social media plugin which adds social media icons to your website with tons of customization features!. 

Author: UltimatelySocial
Author URI: http://ultimatelysocial.com
Version: 2.5.7
License: GPLv2 or later
*/
require_once 'analyst/main.php';

analyst_init(array(
    'client-id' => 'ao6grd4ed38kyeqz',
    'client-secret' => 'ae93c43c738bdf50f10ef9d4c6d811006b468c74',
    'base-dir' => __FILE__
));




sfsi_error_reporting();
global $wpdb;
/* define the Root for URL and Document */

define('SFSI_DOCROOT',    dirname(__FILE__));

define('SFSI_PLUGURL',    plugin_dir_url(__FILE__));

define('SFSI_WEBROOT',    str_replace(getcwd(), home_url(), dirname(__FILE__)));

define('SFSI_SUPPORT_FORM', 'https://goo.gl/wgrtUV');

define('SFSI_DOMAIN', 'ultimate-social-media-icons');
$wp_upload_dir = wp_upload_dir();

define('SFSI_UPLOAD_DIR_BASEURL', trailingslashit($wp_upload_dir['baseurl']));
define('SFSI_ALLICONS', serialize(array("rss", "email", "facebook", "twitter", "share", "youtube", "pinterest", "instagram")));
function sfsi_get_current_page_url()

{
    global $post, $wp;
    if (!empty($wp->request)) {
        return home_url(add_query_arg($_GET ? $_GET : array(), $wp->request));
    } elseif (is_home()) {
        return site_url();
    } elseif (!empty($post->ID)) {
        if ($_GET) {
            return add_query_arg($_GET, get_permalink($post->ID));
        } else {
            return get_permalink($post->ID);
        }
    } else {
        return site_url();
    }
}
/* load all files  */

include(SFSI_DOCROOT . '/libs/sfsi_install_uninstall.php');
include(SFSI_DOCROOT . '/helpers/common_helper.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsi_socialhelper.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsi_class_theme_check.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsi_buttons_controller.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsi_iconsUpload_contoller.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsi_floater_icons.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsi_frontpopUp.php');

include(SFSI_DOCROOT . '/libs/controllers/sfsiocns_OnPosts.php');
include(SFSI_DOCROOT . '/libs/sfsi_Init_JqueryCss.php');

include(SFSI_DOCROOT . '/libs/sfsi_widget.php');

include(SFSI_DOCROOT . '/libs/sfsi_subscribe_widget.php');

include(SFSI_DOCROOT . '/libs/sfsi_custom_social_sharing_data.php');

include(SFSI_DOCROOT . '/libs/sfsi_ajax_social_sharing_settings_updater.php');
/* plugin install and uninstall hooks */

register_activation_hook(__FILE__, 'sfsi_activate_plugin');

register_deactivation_hook(__FILE__, 'sfsi_deactivate_plugin');

register_uninstall_hook(__FILE__, 'sfsi_Unistall_plugin');

if (!get_option('sfsi_pluginVersion') || get_option('sfsi_pluginVersion') < 2.57) {
    add_action("init", "sfsi_update_plugin");
}
/* redirect setting page hook */
add_action('admin_init', 'sfsi_plugin_redirect');

function sfsi_plugin_redirect()

{

    if (get_option('sfsi_plugin_do_activation_redirect', false)) {

        delete_option('sfsi_plugin_do_activation_redirect');

        wp_redirect(admin_url('admin.php?page=sfsi-options'));
    }
}
//************************************** Setting error reporting STARTS ****************************************//
function sfsi_error_reporting()
{
    $option5 = unserialize(get_option('sfsi_section5_options', false));
    if (
        isset($option5['sfsi_icons_suppress_errors'])
        && !empty($option5['sfsi_icons_suppress_errors'])
        && "yes" == $option5['sfsi_icons_suppress_errors']
    ) {
        error_reporting(0);
    }
}
//************************************** Setting error reporting CLOSES ****************************************//
//shortcode for the ultimate social icons {Monad}

add_shortcode("DISPLAY_ULTIMATE_SOCIAL_ICONS", "DISPLAY_ULTIMATE_SOCIAL_ICONS");

function DISPLAY_ULTIMATE_SOCIAL_ICONS($args = null, $content = null)

{

    $instance = array("showf" => 1, "title" => '');

    $return = '';

    if (!isset($before_widget)) : $before_widget = '';
    endif;

    if (!isset($after_widget)) : $after_widget = '';
    endif;
    /*Our variables from the widget settings. */

    $title         = apply_filters('widget_title', $instance['title']);

    $show_info  = isset($instance['show_info']) ? $instance['show_info'] : false;
    global $is_floter;
    $return .= $before_widget;

    /* Display the widget title */

    if ($title) $return .= $before_title . $title . $after_title;

    $return .= '<div class="sfsi_widget sfsi_shortcode_container">';

    $return .= '<div id="sfsi_wDiv"></div>';

    /* Link the main icons function */

    $return .= sfsi_check_visiblity(0, true);

    $return .= '<div style="clear: both;"></div>';

    $return .= '</div>';

    $return .= $after_widget;

    return $return;
}
//adding some meta tags for facebook news feed {Monad}

function sfsi_checkmetas()

{

    if (!function_exists('get_plugins')) {

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $adding_tags = "yes";
    $all_plugins = get_plugins();
    foreach ($all_plugins as $key => $plugin) :
        if (is_plugin_active($key)) {

            if (preg_match("/(seo|search engine optimization|meta tag|open graph|opengraph|og tag|ogtag)/im", $plugin['Name']) || preg_match("/(seo|search engine optimization|meta tag|open graph|opengraph|og tag|ogtag)/im", $plugin['Description'])) :
                $adding_tags = "no";
                break;
            endif;
        }
    endforeach;
    update_option("adding_tags", $adding_tags);
}

if (is_admin()) {

    add_action('after_setup_theme', 'sfsi_checkmetas');
}
add_action('wp_head', 'ultimatefbmetatags');

function ultimatefbmetatags()

{

    $metarequest = get_option("adding_tags");

    $post_id = get_the_ID();
    $feed_id = sanitize_text_field(get_option('sfsi_feed_id'));

    $verification_code = get_option('sfsi_verificatiom_code');

    if (!empty($feed_id) && !empty($verification_code) && $verification_code != "no") {
        echo '<meta name="follow.it-verification-code-' . $feed_id . '" content="' . $verification_code . '"/>';
    }
    if ($metarequest == 'yes' && !empty($post_id)) {
        $post = get_post($post_id);

        $attachment_id = get_post_thumbnail_id($post_id);

        $title = str_replace('"', "", strip_tags(get_the_title($post_id)));

        $url = get_permalink($post_id);

        $description = $post->post_content;

        $description = str_replace('"', "", strip_tags($description));
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        if ($attachment_id) {

            $feat_image = wp_get_attachment_url($attachment_id);

            if (preg_match('/https/', $feat_image)) {

                echo '<meta property="og:image:secure_url" content="' . $feat_image . '" data-id="sfsi">';
            } else {

                echo '<meta property="og:image" content="' . $feat_image . '" data-id="sfsi">';
            }

            $metadata = wp_get_attachment_metadata($attachment_id);

            if (isset($metadata) && !empty($metadata)) {

                if (isset($metadata['sizes']['post-thumbnail'])) {

                    $image_type = $metadata['sizes']['post-thumbnail']['mime-type'];
                } else {

                    $image_type = '';
                }

                if (isset($metadata['width'])) {

                    $width = $metadata['width'];
                } else {

                    $width = '';
                }

                if (isset($metadata['height'])) {

                    $height = $metadata['height'];
                } else {

                    $height = '';
                }
            } else {

                $image_type = '';

                $width = '';

                $height = '';
            }

            echo '<meta property="og:image:type" content="' . $image_type . '" data-id="sfsi" />';

            echo '<meta property="og:image:width" content="' . $width . '" data-id="sfsi" />';

            echo '<meta property="og:image:height" content="' . $height . '" data-id="sfsi" />';

            echo '<meta property="og:url" content="' . $url . '" data-id="sfsi" />';

            echo '<meta property="og:description" content="' . $description . '" data-id="sfsi" />';

            echo '<meta property="og:title" content="' . $title . '" data-id="sfsi" />';
        }
    }
}
//Get verification code

if (is_admin()) {

    $code = sanitize_text_field(get_option('sfsi_verificatiom_code'));

    $feed_id = sanitize_text_field(get_option('sfsi_feed_id'));

    if (empty($code) && !empty($feed_id)) {

        add_action("init", "sfsi_getverification_code");
    }
}

function sfsi_getverification_code()

{
    $feed_id = sanitize_text_field(get_option('sfsi_feed_id'));
    $url = $http_url = 'https://api.follow.it/wordpress/getVerifiedCode_plugin';

    $args = array(
        'timeout' => 30,
        'body'    => array(
            'feed_id'  =>  $feed_id
        )
    );

    $request = wp_remote_post($url, $args);

    if (is_wp_error($request)) {
        // var_dump($request);
        // update_option("sfsi_plus_curlErrorNotices", "yes");
        // update_option("sfsi_plus_curlErrorMessage", $request->get_error_message());
    } else {
        $resp = json_decode($request['body']);
        update_option('sfsi_verificatiom_code', $resp->code);
    }
}
//checking for the youtube username and channel id option

add_action('admin_init', 'check_sfsfiupdatedoptions');

function check_sfsfiupdatedoptions()

{

    $option4 =  unserialize(get_option('sfsi_section4_options', false));

    if (isset($option4['sfsi_youtubeusernameorid']) && !empty($option4['sfsi_youtubeusernameorid'])) { } else {

        $option4['sfsi_youtubeusernameorid'] = 'name';

        update_option('sfsi_section4_options', serialize($option4));
    }
}
add_action('plugins_loaded', 'sfsi_load_domain');

function sfsi_load_domain()

{

    $plugin_dir = basename(dirname(__FILE__)) . '/languages';

    load_plugin_textdomain(SFSI_DOMAIN, false, $plugin_dir);
}
//sanitizing values

function string_sanitize($s)
{

    $result = preg_replace("/[^a-zA-Z0-9]+/", " ", html_entity_decode($s, ENT_QUOTES));

    return $result;
}
//Add Subscriber form css

add_action("wp_footer", "addStyleFunction");

function addStyleFunction()

{

    $option8 = unserialize(get_option('sfsi_section8_options', false));

    $sfsi_feediid = sanitize_text_field(get_option('sfsi_feed_id'));

    $url = "https://api.follow.it/subscription-form/";

    echo $return = '';

    ?>
    <script>
        window.addEventListener('sfsi_functions_loaded', function() {
            if (typeof sfsi_plugin_version == 'function') {
                sfsi_plugin_version(<?php echo get_option("sfsi_pluginVersion"); ?>);
            }
        });

        function sfsi_processfurther(ref) {
            var feed_id = '<?php echo $sfsi_feediid ?>';
            var feedtype = 8;
            var email = jQuery(ref).find('input[name="email"]').val();
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if ((email != "Enter your email") && (filter.test(email))) {
                if (feedtype == "8") {
                    var url = "<?php echo $url; ?>" + feed_id + "/" + feedtype;
                    window.open(url, "popupwindow", "scrollbars=yes,width=1080,height=760");
                    return true;
                }
            } else {
                alert("Please enter email address");
                jQuery(ref).find('input[name="email"]').focus();
                return false;
            }
        }
    </script>
    <style type="text/css" aria-selected="true">
        .sfsi_subscribe_Popinner {
            <?php if (sanitize_text_field($option8['sfsi_form_adjustment']) == 'yes') : ?>width: 100% !important;

            height: auto !important;

            <?php else : ?>width: <?php echo intval($option8['sfsi_form_width']) ?>px !important;

            height: <?php echo intval($option8['sfsi_form_height']) ?>px !important;

            <?php endif;
                ?><?php if (sanitize_text_field($option8['sfsi_form_border']) == 'yes') : ?>border: <?php echo intval($option8['sfsi_form_border_thickness']) . "px solid " . sfsi_sanitize_hex_color($option8['sfsi_form_border_color']);
                                                                                                            ?> !important;

            <?php endif;
                ?>padding: 18px 0px !important;

            background-color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_background']) ?> !important;

        }

        .sfsi_subscribe_Popinner form {

            margin: 0 20px !important;

        }

        .sfsi_subscribe_Popinner h5 {

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_heading_font']) ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_heading_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_heading_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_heading_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_heading_fontcolor']) ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_heading_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_heading_fontalign']) ?> !important;

            margin: 0 0 10px !important;

            padding: 0 !important;

        }

        .sfsi_subscription_form_field {

            margin: 5px 0 !important;

            width: 100% !important;

            display: inline-flex;

            display: -webkit-inline-flex;

        }

        .sfsi_subscription_form_field input {

            width: 100% !important;

            padding: 10px 0px !important;

        }

        .sfsi_subscribe_Popinner input[type=email] {

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']);
                                ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']);
                                ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_field_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']);
                            ?> !important;

        }

        .sfsi_subscribe_Popinner input[type=email]::-webkit-input-placeholder {

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']);
                                ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']);
                                ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_field_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']);
                            ?> !important;

        }

        .sfsi_subscribe_Popinner input[type=email]:-moz-placeholder {
            /* Firefox 18- */

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']);
                                ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']);
                                ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_field_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']);
                            ?> !important;

        }

        .sfsi_subscribe_Popinner input[type=email]::-moz-placeholder {
            /* Firefox 19+ */

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']);
                                ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']);
                                ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_field_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']);
                            ?> !important;

        }

        .sfsi_subscribe_Popinner input[type=email]:-ms-input-placeholder {

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']);
                                ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']);
                                ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_field_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']);
                            ?> !important;

        }

        .sfsi_subscribe_Popinner input[type=submit] {

            font-family: <?php echo sanitize_text_field($option8['sfsi_form_button_font']);
                                ?> !important;

            <?php if (sanitize_text_field($option8['sfsi_form_button_fontstyle']) != 'bold') {
                    ?>font-style: <?php echo sanitize_text_field($option8['sfsi_form_button_fontstyle']) ?> !important;

            <?php
                } else {
                    ?>font-weight: <?php echo sanitize_text_field($option8['sfsi_form_button_fontstyle']) ?> !important;

            <?php
                }

                ?>color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_button_fontcolor']);
                                ?> !important;

            font-size: <?php echo intval($option8['sfsi_form_button_fontsize']) . "px" ?> !important;

            text-align: <?php echo sanitize_text_field($option8['sfsi_form_button_fontalign']);
                            ?> !important;

            background-color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_button_background']);
                                    ?> !important;

        }

        <?php
            $option5            =  unserialize(get_option('sfsi_section5_options', false));

            if ($option5['sfsi_icons_Alignment_via_shortcode'] == 'left') {
                ?>.sfsi_shortcode_container {
            float: left;
        }

        .sfsi_shortcode_container .norm_row .sfsi_wDiv {
            position: relative !important;
        }

        .sfsi_shortcode_container .sfsi_holders {
            display: none;
        }

        <?php
            } elseif ($option5['sfsi_icons_Alignment_via_shortcode'] == 'right') {
                ?>.sfsi_shortcode_container {
            float: right;
        }

        .sfsi_shortcode_container .norm_row .sfsi_wDiv {
            position: relative !important;
        }

        .sfsi_shortcode_container .sfsi_holders {
            display: none;
        }

        <?php
            } elseif ($option5['sfsi_icons_Alignment_via_shortcode'] == 'center') {
                ?>.sfsi_shortcode_container {
            /* float: right; */
        }

        .sfsi_shortcode_container .norm_row.sfsi_wDiv {
            position: relative !important;
            float: none;
            margin: 0 auto;
        }

        .sfsi_shortcode_container .sfsi_holders {
            display: none;
        }

        <?php
            }
            ?>
    </style>

<?php

}

add_action('admin_notices', 'sfsi_admin_notice', 10);

function sfsi_admin_notice()

{

    $language = get_option("WPLANG");
    // if(isset($_GET['page']) && $_GET['page'] == "sfsi-options")

    // {

    // 	$style = "overflow: hidden; margin:12px 3px 0px;";

    // }

    // else

    // {

    // 	$style = "overflow: hidden;"; 

    // }
    // $style = "overflow: hidden;"; 
    // /**

    //  * if wordpress uses other language

    //  */

    // if(!empty($language) && isset($_GET['page']) && $_GET['page'] == "sfsi-options" && 

    // 	get_option("sfsi_languageNotice") == "yes")

    // {

    // 	
    ?>

    <!-- 	// 	<style type="text/css">

	// 		form.sfsi_languageNoticeDismiss{

	// 		    display: inline-block;

	// 		    margin: 5px 0 0;

	// 		    vertical-align: middle;

	// 		}

	// 		.sfsi_languageNoticeDismiss input[type='submit']{

	// 			background-color: transparent;

	// 		    border: medium none;

	// 		    margin: 0;

	// 		    padding: 0;

	// 		    cursor: pointer;

	// 		}

	// 	</style>

	// 	<div class="updated" style="<?php //echo $style; 
                                            ?>">

	// 		<div class="alignleft" style="margin: 9px 0;">

	// 			We detected that you're using a language other than English in Wordpress. We created also the <a target="_blank" href="https://wordpress.org/plugins/ultimate-social-media-plus/">Ultimate Social Media PLUS</a> plugin (still FREE) which allows you to select buttons in non-English languages (under question 6).

	// 		</div>

	// 		<div class="alignright">

	// 			<form method="post" class="sfsi_languageNoticeDismiss">

	// 				<input type="hidden" name="sfsi-dismiss-languageNotice" value="true">

	// 				<input type="submit" name="dismiss" value="Dismiss" />

	// 			</form>

	// 		</div>

	// 	</div> -->

    <?php

        // }
        /**

         * Premium Notification

         */

        $sfsi_themecheck = new sfsi_ThemeCheck();

        $domain     = $sfsi_themecheck->sfsi_plus_getdomain(site_url());

        $siteMatch     = false;
        if (!empty($domain)) {

            $regexp = "/^([a-d A-D])/im";

            if (preg_match($regexp, $domain)) {

                $siteMatch = true;
            } else {

                $siteMatch = false;
            }
        }
        // $screen = get_current_screen();
        // if (get_option("show_premium_notification") == "yes" && ($screen->id != "toplevel_page_sfsi-options")) {

        //     
        ?>



    </style>
    <?php

        include("views/sfsi_plugin_lists.php");
        include("views/sfsi_other_banners.php");
        include("views/sfsi_global_banners.php");

        if (is_ssl() && false) {
            if (get_option("show_premium_cumulative_count_notification") == "yes") {

                ?>
            <style type="text/css">
                div.sfsi_show_premium_cumulative_count_notification {

                    color: #fff;

                    margin-left: 37px;

                    margin-top: 15px;

                    padding: 8px;

                    background-color: #38B54A;

                    color: #fff;

                    font-size: 18px;

                }

                .sfsi_show_premium_cumulative_count_notification a {

                    color: #fff;
                }

                form.sfsi_premiumCumulativeCountNoticeDismiss {

                    display: inline-block;

                    margin: 5px 0 0;

                    vertical-align: middle;

                }

                .sfsi_premiumCumulativeCountNoticeDismiss input[type='submit'] {

                    background-color: transparent;

                    border: medium none;

                    color: #fff;

                    margin: 0;

                    padding: 0;

                    cursor: pointer;

                }
            </style>

            <div class="updated sfsi_show_premium_cumulative_count_notification">

                <div style="margin: 9px 0;">

                    <b>Recently switched to https?</b> If you don’t want to lose the Facebook share & like counts <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=https_share_counts&utm_medium=banner" target="_blank">have a look at our Premium Plugin</a>, we found a fix for that: <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=https_share_counts&utm_medium=banner" target="_blank">Check it out</a>

                </div>

                <div style="text-align: right;margin-top:-30px">

                    <form method="post" class="sfsi_premiumCumulativeCountNoticeDismiss" style="padding:10px">

                        <input type="hidden" name="sfsi-dismiss-premiumCumulativeCountNoticeDismiss" value="true">

                        <input type="submit" name="dismiss" value="Dismiss" />

                    </form>

                </div>

                <div style=”clear:both”></div>

            </div>

        <?php

                }
            }

            /* show mobile notification */

            if (get_option("show_mobile_notification") == "yes") {

                $sfsi_install_date = strtotime(get_option('sfsi_installDate'));

                $sfsi_future_date = strtotime('14 days', $sfsi_install_date);

                $sfsi_past_date = strtotime("now");

                if ($sfsi_past_date >= $sfsi_future_date) {

                    ?>

            <style type="text/css">
                .sfsi_show_mobile_notification a {

                    color: #fff;

                }

                form.sfsi_mobileNoticeDismiss {

                    display: inline-block;

                    margin: 5px 0 0;

                    vertical-align: middle;

                }

                .sfsi_mobileNoticeDismiss input[type='submit'] {

                    background-color: transparent;

                    border: medium none;

                    color: #fff;

                    margin: 0;

                    padding: 0;

                    cursor: pointer;

                }
            </style>

            <!-- <div class="updated sfsi_show_mobile_notification" style="<?php //echo $style; 
                                                                                        ?>background-color: #38B54A; color: #fff; font-size: 18px;">

				<div class="alignleft" style="margin: 9px 0;line-height: 24px;width: 95%;">

					<b>Over 50% of visitors are mobile visitors.</b> Make sure your social media icons look good on mobile too, so that people like & share your site. With the premium plugin you can define the location of the icons separately on mobile:<a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=check_mobile&utm_medium=banner" target="_blank">Check it out</a>

				</div>

				<div class="alignright">

					<form method="post" class="sfsi_mobileNoticeDismiss">

						<input type="hidden" name="sfsi-dismiss-mobileNotice" value="true">

						<input type="submit" name="dismiss" value="Dismiss" />

					</form>

				</div>

			</div> -->

        <?php

                }
            }

            /* end show mobile notification */

            /* start phpversion error notification*/

            $phpVersion = phpVersion();

            if ($phpVersion <= '5.4') {

                if (get_option("sfsi_serverphpVersionnotification") == "yes") {
                    ?>



            <style type="text/css">
                .sfsi_show_phperror_notification {

                    color: #fff;

                    text-decoration: underline;

                }

                form.sfsi_phperrorNoticeDismiss {

                    display: inline-block;

                    margin: 5px 0 0;

                    vertical-align: middle;

                }

                .sfsi_phperrorNoticeDismiss input[type='submit'] {

                    background-color: transparent;

                    border: medium none;

                    color: #fff;

                    margin: 0;

                    padding: 0;

                    cursor: pointer;

                }

                .sfsi_show_phperror_notification p {
                    line-height: 22px;
                }

                p.sfsi_show_notifictaionpragraph {
                    padding: 0 !important;
                    font-size: 18px;
                }
            </style>
            <div class="updated sfsi_show_phperror_notification" style="<?php echo (isset($style) ? $style : ''); ?>background-color: #D22B2F; color: #fff; font-size: 18px; border-left-color: #D22B2F;">

                <div style="margin: 9px 0;">
                    <p class="sfsi_show_notifictaionpragraph">

                        We noticed you are running your site on a PHP version older than 5.4. Please upgrade to a more recent
                        version. This is not only important for running the Ultimate Social Media Plugin, but also for security
                        reasons in general.

                        <br>

                        If you do not know how to do the upgrade, please ask your server team or hosting company to do it for you.'

                    </p>
                </div>

                <div style="text-align:right;margin-top:-30px">

                    <form method="post" class="sfsi_phperrorNoticeDismiss" style="padding-bottom:10px">

                        <input type="hidden" name="sfsi-dismiss-phperrorNotice" value="true">

                        <input type="submit" name="dismiss" value="Dismiss" />

                    </form>

                </div>

            </div>



        <?php

                }
            }
            sfsi_get_language_detection_notice();
            sfsi_language_notice();



            sfsi_addThis_removal_notice();
            sfsi_error_reporting_notice();
        }
        function sfsi_get_language_detection_notice()
        {
            $currLang = get_locale();

            $text     = '';
            switch ($currLang) {
                    // Arabic

                    // case 'ar':



                    //     $text = "";

                    //     break;
                    // Chinese - simplified

                case 'zh-Hans':



                    $text = "似乎你的WordPress仪表盘使用的是法语。你知道 终极社交媒体插件 也支持法语吗？ <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'><b>请点击此处</b></a>";

                    break;
                    // Chinese - traditional

                    // case 'zh-Hant':



                    //     $text = "";

                    //     break;
                    // Dutch, Dutch (Belgium)

                    // case 'nl_NL': case 'nl_BE':                

                    //     $text = "";

                    //     break;
                    // French (Belgium), French (France)

                case 'fr_BE':
                case 'fr_FR':



                    $text = "Il semblerait que votre tableau de bord Wordpress soit en Français. Saviez-vous que l'extension Ultimate  Social Media est aussi disponible en Français? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Cliquez ici</a>";

                    break;
                    // German, German (Switzerland)

                case 'de':
                case 'de_CH':
                    $text = "Dein Wordpress-Dashboard scheint auf deutsch zu sein. Wusstest Du dass das Ultimate Social Media Plugin auch auf deutsch verfügbar ist? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Klicke hier</a>";

                    break;
                    // Greek

                    // case 'el':



                    //     $text = "";

                    //     break;
                    // Hebrew

                case 'he_IL':
                    $text = "נדמה שלוח הבקרה שלך הוא בעברית. האם ידעת שהתוסף זמין גם בשפה העברית? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>לחץ כאן</a>";

                    break;
                    // Hindi

                    // case 'hi_IN':



                    //     $text = ""; 

                    //     break;
                    // Indonesian

                    // case 'id':



                    //     $text = "";
                    //     break;
                    // Italian

                case 'it_IT':



                    $text = "Semberebbe che la tua bacheca di WordPress sia in Italiano.Lo sapevi che il plugin Ultimate Social Media è anche dispoinibile in Italiano? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Fai click qui</a>";



                    break;
                    // Japanese

                    // case 'ja':



                    //     $text = "";
                    //     break;                       
                    // Korean

                    // case 'ko_KR ':
                    //     $text = ""; 
                    //     break;                       
                    // Persian, Persian (Afghanistan)

                    // case 'fa_IR':case 'fa_AF':



                    //     $text = "";



                    //     break;                       
                    // Polish
                    // case 'pl_PL':

                    //     $text = "";

                    //     break;
                    //Portuguese (Brazil), Portuguese (Portugal)
                case 'pt_BR':
                case 'pt_PT':
                    $text = "Parece que seu painel Wordpress está em português. Você sabia que o plugin Ultimate Social Media também está disponível em português? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Clique aqui</a>";
                    break;
                    // Russian, Russian (Ukraine)

                case 'ru_RU':
                case 'ru_UA':
                    $text = "Ты говоришь по-русски? Если у вас есть вопросы о плагине Ultimate Social Media, задайте свой вопрос в форуме поддержки, мы постараемся ответить на русский: <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Нажмите здесь</a>";



                    break;



                    /* Spanish (Argentina), Spanish (Chile), Spanish (Colombia), Spanish (Mexico),

            Spanish (Peru), Spanish (Puerto Rico), Spanish (Spain), Spanish (Venezuela) */
                case 'es_AR':
                case 'es_CL':
                case 'es_CO':
                case 'es_MX':
                case 'es_PE':
                case 'es_PR':

                case 'es_ES':
                case 'es_VE':
                    $text = "Al parecer, tu dashboard en Wordpress está en Francés/ ¿Sabías que el complemento Ultimate Social Media está también disponible en Francés? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Haz clic aquí</a>";

                    break;
                    //  Swedish
                    // case 'sv_SE':


                    //     $text = "<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klicka här</a>";

                    //     break;                       
                    //  Turkish
                case 'tr_TR':

                    $text = "Wordpress gösterge panelinizin dili Türkçe olarak görünüyor. Ultimate Social Media eklentisinin Türkçe için de mevcut olduğunu biliyor musunuz? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Buraya tıklayın</a>";

                    break;
                    //  Ukrainian
                    // case 'uk':

                    //     $text = "<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>натисніть тут</a>";

                    //     break;                       
                    //  Vietnamese
                case 'vi':

                    $text = 'Có vẻ như bảng điều khiển Wordpress của bạn đang hiển thị "tiếng Việt". Bạn có biết rằng Ultimate Social Media plugin cũng hỗ trợ tiếng Việt? <a target="_blank" href="https://wordpress.org/plugins/ultimate-social-media-plus/">Hãy nhấn vào đây</a>';

                    break;
            }
            $style = "overflow: hidden;padding:8px;margin:15px 15px 15px 0px !important";
            if (
                !empty($text) && isset($_GET['page'])

                && ("sfsi-options" == $_GET['page']) && ("yes" == get_option("sfsi_languageNotice"))
            ) {

                ?>
            <style type="text/css">
                form.sfsi_languageNoticeDismiss {
                    display: inline-block;
                    margin: 5px 0 0;
                    vertical-align: middle;
                }

                .sfsi_languageNoticeDismiss input[type='submit'] {
                    background-color: transparent;
                    border: medium none;
                    margin: 0 5px 0 0px;
                    padding: 0;
                    cursor: pointer;
                    font-size: 22px;
                }
            </style>

            <div class="notice notice-info" style="<?php echo isset($style) ? $style : ''; ?>">

                <div style="margin: 9px 0;">

                    <?php echo $text; ?>

                </div>

                <div style="text-align: right;margin-top:-30px">

                    <form method="post" class="sfsi_languageNoticeDismiss" style="padding-bottom:10px">

                        <input type="hidden" name="sfsi-dismiss-languageNotice" value="true">

                        <input type="submit" name="dismiss" value="&times;" />

                    </form>

                </div>

            </div>
        <?php }
        }
        add_action('admin_init', 'sfsi_dismiss_admin_notice');

        function sfsi_dismiss_admin_notice()

        {

            $current_date_sfsi = date("Y-m-d h:i:s");

            if (isset($_REQUEST['sfsi-dismiss-notice']) && $_REQUEST['sfsi-dismiss-notice'] == 'true') {

                update_option('show_notification_plugin', "no");

                //header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options");die;

            }
            if (isset($_REQUEST['sfsi-dismiss-languageNotice']) && $_REQUEST['sfsi-dismiss-languageNotice'] == 'true') {

                update_option('sfsi_languageNotice', "no");

                //header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options"); die;

            }
            if (isset($_REQUEST['sfsi-dismiss-premiumNotice']) && $_REQUEST['sfsi-dismiss-premiumNotice'] == 'true') {

                update_option('show_premium_notification', "no");

                //header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options");die;

            }
            if (isset($_REQUEST['sfsi-dismiss-mobileNotice']) && $_REQUEST['sfsi-dismiss-mobileNotice'] == 'true') {

                update_option('show_mobile_notification', "no");

                //header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options");die;

            }

            if (isset($_REQUEST['sfsi-dismiss-phperrorNotice']) && $_REQUEST['sfsi-dismiss-phperrorNotice'] == 'true') {

                update_option('sfsi_serverphpVersionnotification', "no");
            }

            if (isset($_REQUEST['sfsi-dismiss-premiumCumulativeCountNoticeDismiss']) && $_REQUEST['sfsi-dismiss-premiumCumulativeCountNoticeDismiss'] == 'true') {

                update_option('show_premium_cumulative_count_notification', "no");
            }
            if (isset($_REQUEST['sfsi-dismiss-sharecount']) && $_REQUEST['sfsi-dismiss-sharecount'] == 'true') {
                $sfsi_dismiss_sharecount = array(
                    'show_banner'     => "no",
                    'timestamp' => strtotime(date("Y-m-d h:i:s"))
                );
                update_option('sfsi_dismiss_sharecount', serialize($sfsi_dismiss_sharecount));
            }
            if (isset($_REQUEST['sfsi-dismiss-google-analytic']) && $_REQUEST['sfsi-dismiss-google-analytic'] == 'true') {
                $sfsi_dismiss_google_analytic = array(
                    'show_banner'     => "no",
                    'timestamp' => strtotime(date("Y-m-d h:i:s"))
                );
                update_option('sfsi_dismiss_google_analytic', serialize($sfsi_dismiss_google_analytic));
            }
            if (isset($_REQUEST['sfsi-dismiss-gdpr']) && $_REQUEST['sfsi-dismiss-gdpr'] == 'true') {
                $sfsi_dismiss_gdpr = array(
                    'show_banner'     => "no",
                    'timestamp' => strtotime(date("Y-m-d h:i:s"))
                );
                update_option('sfsi_dismiss_gdpr', serialize($sfsi_dismiss_gdpr));
            }
            if (isset($_REQUEST['sfsi-dismiss-optimization']) && $_REQUEST['sfsi-dismiss-optimization'] == 'true') {
                $sfsi_dismiss_optimization = array(
                    'show_banner'     => "no",
                    'timestamp' => strtotime(date("Y-m-d h:i:s"))
                );
                update_option('sfsi_dismiss_optimization', serialize($sfsi_dismiss_optimization));
            }
            if (isset($_REQUEST['sfsi-dismiss-gallery']) && $_REQUEST['sfsi-dismiss-gallery'] == 'true') {
                $sfsi_dismiss_gallery = array(
                    'show_banner'     => "no",
                    'timestamp' => strtotime(date("Y-m-d h:i:s"))
                );
                update_option('sfsi_dismiss_gallery', serialize($sfsi_dismiss_gallery));
            }


            if (isset($_REQUEST['sfsi-banner-global-upgrade']) && $_REQUEST['sfsi-banner-global-upgrade'] == 'true') {
                $sfsi_banner_global_upgrade = unserialize(get_option('sfsi_banner_global_upgrade', false));
                $sfsi_banner_global_upgrade = array(
                    'met_criteria'     =>  $sfsi_banner_global_upgrade['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_upgrade', serialize($sfsi_banner_global_upgrade));
                sfsi_check_banner();
            }
            if (isset($_REQUEST['sfsi-banner-global-http']) && $_REQUEST['sfsi-banner-global-http'] == 'true') {
                $sfsi_banner_global_http = unserialize(get_option('sfsi_banner_global_http', false));
                $sfsi_banner_global_http = array(
                    'met_criteria'     =>  $sfsi_banner_global_http['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_http', serialize($sfsi_banner_global_http));
                sfsi_check_banner();
            }
            if (isset($_REQUEST['sfsi-banner-global-gdpr']) && $_REQUEST['sfsi-banner-global-gdpr'] == 'true') {
                $sfsi_banner_global_gdpr = unserialize(get_option('sfsi_banner_global_gdpr', false));
                $sfsi_banner_global_gdpr = array(
                    'met_criteria'     => $sfsi_banner_global_gdpr['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_gdpr', serialize($sfsi_banner_global_gdpr));
                sfsi_check_banner();
            }

            if (isset($_REQUEST['sfsi-banner-global-shares']) && $_REQUEST['sfsi-banner-global-shares'] == 'true') {
                $sfsi_banner_global_shares = unserialize(get_option('sfsi_banner_global_shares', false));
                $sfsi_banner_global_shares = array(
                    'met_criteria'     => $sfsi_banner_global_shares['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_shares', serialize($sfsi_banner_global_shares));
                sfsi_check_banner();
            }
            if (isset($_REQUEST['sfsi-banner-global-load_faster']) && $_REQUEST['sfsi-banner-global-load_faster'] == 'true') {
                $sfsi_banner_global_load_faster = unserialize(get_option('sfsi_banner_global_load_faster', false));
                $sfsi_banner_global_load_faster = array(
                    'met_criteria'     => $sfsi_banner_global_load_faster['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_load_faster', serialize($sfsi_banner_global_load_faster));
                sfsi_check_banner();
            }
            if (isset($_REQUEST['sfsi-banner-global-social']) && $_REQUEST['sfsi-banner-global-social'] == 'true') {
                $sfsi_banner_global_social = unserialize(get_option('sfsi_banner_global_social', false));
                $sfsi_banner_global_social = array(
                    'met_criteria'     =>  $sfsi_banner_global_social['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_social', serialize($sfsi_banner_global_social));
                sfsi_check_banner();
            }
            if (isset($_REQUEST['sfsi-banner-global-pinterest']) && $_REQUEST['sfsi-banner-global-pinterest'] == 'true') {
                $sfsi_banner_global_pinterest = unserialize(get_option('sfsi_banner_global_pinterest', false));
                $sfsi_banner_global_pinterest = array(
                    'met_criteria'     => $sfsi_banner_global_pinterest['met_criteria'],
                    'banner_appeared' => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_pinterest', serialize($sfsi_banner_global_pinterest));
                sfsi_check_banner();
            }
            $sfsi_install_time = strtotime(get_option('sfsi_installDate'));
            $sfsi_max_show_time = $sfsi_install_time + (60 * 60);
            $sfsi_banner_global_firsttime_offer = unserialize(get_option('sfsi_banner_global_firsttime_offer', false));
            if (
                (isset($_REQUEST['sfsi-banner-global-firsttime-offer']) && $_REQUEST['sfsi-banner-global-firsttime-offer'] == 'true') || (isset($sfsi_banner_global_firsttime_offer['is_active']) && $sfsi_banner_global_firsttime_offer['is_active'] == "yes" &&  ceil(($sfsi_max_show_time - strtotime(date('Y-m-d h:i:s'))) / 60) <= 0)
            ) {

                $sfsi_banner_global_firsttime_offer = array(
                    'met_criteria'     => "yes",
                    'is_active' => "no",
                    'timestamp' => $current_date_sfsi
                );
                update_option('sfsi_banner_global_firsttime_offer', serialize($sfsi_banner_global_firsttime_offer));
                sfsi_check_banner();
            }
        }
        // add_action("admin_init","sfsi_check_banner");

        function sfsi_get_bloginfo($url)

        {

            $web_url = get_bloginfo($url);
            //Block to use feedburner url

            if (preg_match("/(feedburner)/im", $web_url, $match)) {

                $web_url = site_url() . "/feed";
            }

            return $web_url;
        }

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), "sfsi_actionLinks", -10);

        function sfsi_actionLinks($links)

        {

            unset($links['edit']);

            $links['a'] = '<a target="_blank" href="https://goo.gl/auxJ9C#no-topic-0" id="sfsi_deactivateButton" style="color:#FF0000;"><b>Need help?</b></a>';

            //$links[] = '<a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_manage_plugin_page&utm_campaign=check_out_pro_version&utm_medium=banner" id="sfsi_deactivateButton" style="color:#38B54A;"><b>Check out pro version</b></a>';
            /*if(isset($links["edit"]) && !empty($links["edit"])){

		$links[] = @$links["edit"];		

	}*/
            //$slug = plugin_basename(dirname(__FILE__));

            //$links[$slug] = @$links["deactivate"].'<i class="sfsi-deactivate-slug"></i>';
            $links['e'] = '<a href="' . admin_url("/admin.php?page=sfsi-options") . '">Settings</a>';
            ksort($links);
            //unset($links["deactivate"]);

            return $links;
        }
        global $pagenow;
        if ('plugins.php' === $pagenow) {
            add_action('admin_footer', '_sfsi_add_deactivation_feedback_dialog_box');
            function _sfsi_add_deactivation_feedback_dialog_box()
            {
                include_once(SFSI_DOCROOT . '/views/deactivation/sfsi_deactivation_popup.php'); ?>
            <script type="text/javascript">
                window.addEventListener('sfsi_functions_loaded', function($) {
                    var _deactivationLink = $('.sfsi-deactivate-slug').prev();
                    $('.sfsi-deactivation-reason-link').find('a').attr('href', _deactivationLink.attr('href'));
                    _deactivationLink.on('click', function(e) {

                        e.preventDefault();

                        $('[data-popup="popup-1"]').fadeIn(350);

                    });
                    //----- CLOSE

                    $('[data-popup-close]').on('click', function(e) {

                        e.preventDefault();

                        var targeted_popup_class = jQuery(this).attr('data-popup-close');

                        $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

                    });
                    //----- OPEN

                    $('[data-popup-open]').on('click', function(e) {

                        e.preventDefault();

                        var targeted_popup_class = jQuery(this).attr('data-popup-open');

                        $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

                    });
                    $('.sfsi-deactivate-radio').on('click', function(e) {
                        $('.sfsi-deactivate-radio').attr('checked', false);

                        $(this).attr('checked', true);
                        var val = $(this).val();
                        $('.sfsi-reason-section').removeClass('show').addClass('hide');

                        $(this).parent().find('.sfsi-reason-section').addClass('show').removeClass('hide');

                    });
                    $('.sfsi-deactivate-radio-text').on('click', function(e) {

                        $(this).prev().trigger('click');

                    });
                });
            </script>

            <?php

                }
            }
            /* redirect setting page hook */
            /*add_action('admin_init', 'sfsi_plugin_redirect');

function sfsi_plugin_redirect()

{

    if (get_option('sfsi_plugin_do_activation_redirect', false))

    {

        delete_option('sfsi_plugin_do_activation_redirect');

        wp_redirect(admin_url('admin.php?page=sfsi-options'));

    }

}

*/

            function _is_curl_installed()
            {
                if (in_array('curl', get_loaded_extensions())) {

                    return true;
                } else {

                    return false;
                }
            }
            // ********************************* Link to support forum for different languages STARTS *******************************//
            function sfsi_get_language_notice_text()
            {
                $currLang = get_locale();

                $text     = '';
                switch ($currLang) {
                        // Arabic

                    case 'ar':



                        $text = "hal tatakalam alearabia? 'iidha kanat ladayk 'asyilat hawl almukawan al'iidafii l Ultimate Social Media , aitruh sualik fi muntadaa aldaem , sanuhawil alrada biallughat alearabiat: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'><b>'unqur huna</b></a>";

                        break;
                        // Chinese - simplified

                    case 'zh-Hans':



                        $text = "你会说中文吗？如果您有关于Ultimate Social Media插件的问题，请在支持论坛中提出您的问题，我们将尝试用中文回复：<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'><b>点击此处</b></a>";

                        break;
                        // Chinese - traditional

                    case 'zh-Hant':



                        $text = "你會說中文嗎？如果您有關於Ultimate Social Media插件的問題，請在支持論壇中提出您的問題，我們將嘗試用中文回复：<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'><b>點擊此處</b></a>";

                        break;
                        // Dutch, Dutch (Belgium)

                    case 'nl_NL':
                    case 'nl_BE':

                        $text = "Jij spreekt Nederlands? Als je vragen hebt over de Ultimate Social Media-plug-in, stel je vraag in het ondersteuningsforum, we zullen proberen in het Nederlands te antwoorden: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>klik hier</a>";

                        break;
                        // French (Belgium), French (France)

                    case 'fr_BE':
                    case 'fr_FR':



                        $text = "Vous parlez français? Si vous avez des questions sur le plugin Ultimate Social Media, posez votre question sur le forum de support, nous essaierons de répondre en français: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Cliquez ici</a>";

                        break;
                        // German, German (Switzerland)

                    case 'de':
                    case 'de_CH':
                        $text = "Du sprichst Deutsch? Wenn Du Fragen zum Ultimate Social Media-Plugins hast, einfach im Support Forum fragen. Wir antworten auch auf Deutsch! <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klicke hier</a>";

                        break;
                        // Greek

                    case 'el':



                        $text = "Μιλάτε Ελληνικά? Αν έχετε ερωτήσεις σχετικά με το plugin Ultimate Social Media, ρωτήστε την ερώτησή σας στο φόρουμ υποστήριξης, θα προσπαθήσουμε να απαντήσουμε στα ελληνικά: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Κάντε κλικ εδώ</a>";

                        break;
                        // Hebrew

                    case 'he_IL':



                        $text = "אתה מדבר עברית? אם יש לך שאלות על תוסף המדיה החברתית האולטימטיבית, שאל את השאלה שלך בפורום התמיכה, ננסה לענות בעברית: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>לחץ כאן</a>";

                        break;
                        // Hindi

                    case 'hi_IN':



                        $text = "आप हिंदी बोलते हो? यदि आपके पास अल्टीमेट सोशल मीडिया प्लगइन के बारे में कोई प्रश्न है, तो समर्थन फोरम में अपना प्रश्न पूछें, हम हिंदी में जवाब देने का प्रयास करेंगे: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>यहां क्लिक करें</a>";

                        break;
                        // Indonesian

                    case 'id':



                        $text = "Anda berbicara bahasa Indonesia? Jika Anda memiliki pertanyaan tentang plugin Ultimate Social Media, ajukan pertanyaan Anda di Forum Dukungan, kami akan mencoba menjawab dalam Bahasa Indonesia: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klik di sini</a>";
                        break;
                        // Italian

                    case 'it_IT':



                        $text = "Tu parli italiano? Se hai domande sul plugin Ultimate Social Media, fai la tua domanda nel Forum di supporto, cercheremo di rispondere in italiano: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>clicca qui</a>";



                        break;
                        // Japanese

                    case 'ja':



                        $text = "あなたは日本語を話しますか？アルティメットソーシャルメディアのプラグインに関する質問がある場合は、サポートフォーラムで質問してください。日本語で対応しようと思っています：<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>ここをクリック</a>";
                        break;
                        // Korean

                    case 'ko_KR ':
                        $text = "한국어를 할 줄 아세요? 궁극적 인 소셜 미디어 플러그인에 대해 궁금한 점이 있으면 지원 포럼에서 질문하십시오. 한국어로 답변하려고합니다 : <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>여기를 클릭하십시오.</a>";
                        break;
                        // Persian, Persian (Afghanistan)

                    case 'fa_IR':
                    case 'fa_AF':



                        $text = "شما فارسی صحبت می کنید؟ اگر سوالی در مورد پلاگین رسانه Ultimate Social دارید، سوال خود را در انجمن پشتیبانی بپرسید، سعی خواهیم کرد به فارسی پاسخ دهید: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>اینجا را کلیک کنید</a>";



                        break;
                        // Polish
                    case 'pl_PL':

                        $text = "Mówisz po polsku? Jeśli masz pytania dotyczące wtyczki Ultimate Social Media, zadaj pytanie na Forum pomocy technicznej, postaramy się odpowiedzieć po polsku: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Kliknij tutaj</a>";

                        break;
                        //Portuguese (Brazil), Portuguese (Portugal)
                    case 'pt_BR':
                    case 'pt_PT':
                        $text = "Você fala português? Se você tiver dúvidas sobre o plug-in Ultimate Social Media, faça sua pergunta no Fórum de suporte, tentaremos responder em português: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Clique aqui</a>";
                        break;
                        // Russian, Russian (Ukraine)

                    case 'ru_RU':
                    case 'ru_UA':
                        $text = "Ты говоришь по-русски? Если у вас есть вопросы о плагине Ultimate Social Media, задайте свой вопрос в форуме поддержки, мы постараемся ответить на русский: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Нажмите здесь</a>";



                        break;



                        /* Spanish (Argentina), Spanish (Chile), Spanish (Colombia), Spanish (Mexico),

            Spanish (Peru), Spanish (Puerto Rico), Spanish (Spain), Spanish (Venezuela) */
                    case 'es_AR':
                    case 'es_CL':
                    case 'es_CO':
                    case 'es_MX':
                    case 'es_PE':
                    case 'es_PR':

                    case 'es_ES':
                    case 'es_VE':
                        $text = "¿Tu hablas español? Si tiene alguna pregunta sobre el complemento Ultimate Social Media, formule su pregunta en el foro de soporte, intentaremos responder en español: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>haga clic aquí</a>";

                        break;
                        //  Swedish
                    case 'sv_SE':



                        $text = "Pratar du svenska? Om du har frågor om programmet Ultimate Social Media, fråga din fråga i supportforumet, vi försöker svara på svenska: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klicka här</a>";

                        break;
                        //  Turkish
                    case 'tr_TR':

                        $text = "Sen Türkçe konuş? Nihai Sosyal Medya eklentisi hakkında sorularınız varsa, sorunuza Destek Forumu'nda sorun, Türkçe olarak cevap vermeye çalışacağız: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Tıklayın</a>";

                        break;
                        //  Ukrainian
                    case 'uk':

                        $text = "Ви говорите по-українськи? Якщо у вас є запитання про плагін Ultimate Social Media, задайте своє питання на Форумі підтримки, ми спробуємо відповісти українською: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>натисніть тут</a>";

                        break;
                        //  Vietnamese
                    case 'vi':

                        $text = "Bạn nói tiếng việt không Nếu bạn có câu hỏi về plugin Ultimate Social Media, hãy đặt câu hỏi của bạn trong Diễn đàn hỗ trợ, chúng tôi sẽ cố gắng trả lời bằng tiếng Việt: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Nhấp vào đây</a>";

                        break;
                }
                return $text;
            }
            function sfsi_language_notice()
            {
                if (isset($_GET['page']) && "sfsi-options" == $_GET['page']) :
                    $langText    = sfsi_get_language_notice_text();

                    $isDismissed = get_option('sfsi_lang_notice_dismissed');
                    if (!empty($langText) && false == $isDismissed) { ?>



                    <div id="sfsi_plus_langnotice" class="notice notice-info">
                        <p><?php echo $langText; ?></p>
                        <button type="button" class="sfsi-notice-dismiss notice-dismiss"></button>
                    </div>
                <?php } ?>
                <?php endif;
                }
                function sfsi_dismiss_lang_notice()
                {

                    if (!wp_verify_nonce($_POST['nonce'], "sfsi_dismiss_lang_notice'")) {

                        echo  json_encode(array('res' => "error"));
                        exit;
                    }

                    if (!current_user_can('manage_options')) {
                        echo json_encode(array('res' => 'not allowed'));
                        die();
                    }
                    echo update_option('sfsi_lang_notice_dismissed', true) ? "true" : "false";

                    die;
                }
                add_action('wp_ajax_sfsi_dismiss_lang_notice', 'sfsi_dismiss_lang_notice');
                // ********************************* Link to support forum for different languages CLOSES *******************************//

                // ********************************* Notice for removal of AddThis option STARTS *******************************//

                function sfsi_addThis_removal_notice()
                {
                    if (isset($_GET['page']) && "sfsi-options" == $_GET['page']) :



                        $sfsi_addThis_removalText    = "We removed Addthis from the plugin due to issues with GDPR, the new EU data protection regulation.";
                        $isDismissed   =  get_option('sfsi_addThis_icon_removal_notice_dismissed', false);
                        if (false == $isDismissed) { ?>



                    <div id="sfsi_plus_addThis_removal_notice" class="notice notice-info">
                        <p><?php echo $sfsi_addThis_removalText; ?></p>
                        <button type="button" class="sfsi-AddThis-notice-dismiss notice-dismiss"></button>
                    </div>
                <?php } ?>
            <?php endif;
            }
            function sfsi_dismiss_addthhis_removal_notice()
            {

                if (!wp_verify_nonce($_POST['nonce'], "sfsi_dismiss_addThis_icon_notice")) {

                    echo  json_encode(array('res' => "error"));
                    exit;
                }

                if (!current_user_can('manage_options')) {
                    echo json_encode(array('res' => 'not allowed'));
                    die();
                }

                echo (string) update_option('sfsi_addThis_icon_removal_notice_dismissed', true);

                die;
            }
            add_action('wp_ajax_sfsi_dismiss_addThis_icon_notice', 'sfsi_dismiss_addthhis_removal_notice');
            // ********************************* Notice for removal of AddThis option CLOSES *******************************//
            // ********************************* Link to support forum left of every Save button STARTS *******************************//
            function sfsi_ask_for_help($viewNumber)
            { ?>
            <div class="sfsi_askforhelp askhelpInview<?php echo $viewNumber; ?>">
                <img src="<?php echo SFSI_PLUGURL . "images/questionmark.png"; ?>" alt="error" />
                <span>Questions? <a target="_blank" href="#" onclick="event.preventDefault();sfsi_open_chat(event)"><b>Ask
                            us</b></a></span>
            </div>
            <?php }
            // ********************************* Link to support forum left of every Save button CLOSES *******************************//
            // ********************************* Notice for error reporting STARTS *******************************//
            function sfsi_error_reporting_notice()
            {
                if (is_admin()) :



                    $sfsi_error_reporting_notice_txt    = 'We noticed that you have set error reporting to "yes" in wp-config. Our plugin (Ultimate Social Media Icons) switches this to "off" so that no errors are displayed (which may also impact error messages from your theme or other plugins). If you don\'t want that, please select the respective option under question 6 (at the bottom).';
                    $isDismissed   =  get_option('sfsi_error_reporting_notice_dismissed', false);
                    $option5 = unserialize(get_option('sfsi_section5_options', false));
                    $sfsi_icons_suppress_errors = isset($option5['sfsi_icons_suppress_errors']) && !empty($option5['sfsi_icons_suppress_errors']) ? $option5['sfsi_icons_suppress_errors'] : false;
                    if (isset($isDismissed) && false == $isDismissed && defined('WP_DEBUG') && false != WP_DEBUG && "yes" == $sfsi_icons_suppress_errors) { ?>



                    <div style="padding: 10px;margin-left: 0px;position: relative;" id="sfsi_error_reporting_notice" class="error notice">
                        <p><?php echo $sfsi_error_reporting_notice_txt; ?></p>
                        <button type="button" class="sfsi_error_reporting_notice-dismiss notice-dismiss"></button>
                    </div>
                    <script type="text/javascript">
                        window.addEventListener('sfsi_functions_loaded', function() {
                            if (typeof jQuery != 'undefined') {
                                (function sfsi_dismiss_notice(btnClass, ajaxAction, nonce) {



                                    var btnClass = "." + btnClass;
                                    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                                    jQuery(document).on("click", btnClass, function() {



                                        jQuery.ajax({

                                            url: ajaxurl,

                                            type: "post",

                                            data: {
                                                action: ajaxAction
                                            },

                                            success: function(e) {

                                                if (false != e) {

                                                    jQuery(btnClass).parent().remove();

                                                }

                                            }

                                        });
                                    });
                                }("sfsi_error_reporting_notice-dismiss", "sfsi_dismiss_error_reporting_notice",
                                    "<?php echo wp_create_nonce('sfsi_dismiss_error_reporting_notice'); ?>"));

                            }
                        });
                    </script>
                <?php } ?>
        <?php endif;
        }
        function sfsi_dismiss_error_reporting_notice()
        {

            if (!wp_verify_nonce($_POST['nonce'], "sfsi_dismiss_error_reporting_notice")) {

                echo  json_encode(array('res' => "error"));
                exit;
            }

            if (!current_user_can('manage_options')) {
                echo json_encode(array('res' => 'not allowed'));
                die();
            }

            echo (string) update_option('sfsi_error_reporting_notice_dismissed', true);

            die;
        }

        add_action('wp_ajax_sfsi_dismiss_error_reporting_notice', 'sfsi_dismiss_error_reporting_notice');
        // ********************************* Notice for error reporting CLOSE *******************************//

        function sfsi_check_banner()
        {
            $gallery_plugins  = array(
                array('option_name' => 'photoblocks', 'dir_slug' => 'photoblocks-grid-gallery/photoblocks.php'),
                array('option_name' => 'everlightbox_options', 'dir_slug' => 'everlightbox/everlightbox.php'),
                array('option_name' => 'Total_Soft_Gallery_Video', 'dir_slug' => 'gallery-videos/index.php'),
                array('option_name' => 'Wpape-gallery-settings', 'dir_slug' => 'gallery-images-ape/index.php'),
                array('option_name' => 'overview', 'dir_slug' => 'robo-gallery/robogallery.php'),
                array('option_name' => 'flag-overview', 'dir_slug' => 'flash-album-gallery/flag.php'),
                array('option_name' => 'GrandMedia', 'dir_slug' => 'grand-media/grand-media.php'),
                array('option_name' => 'emg-whats-new', 'dir_slug' => 'easy-media-gallery/easy-media-gallery.php'),
                array('option_name' => 'grid-kit', 'dir_slug' => 'portfolio-wp/portfolio-wp.php'),
                array('option_name' => 'Wc-gallery', 'dir_slug' => 'wc-gallery/wc-gallery.php'),
                array('option_name' => 'elementor-getting-started', 'dir_slug' => 'elementor/elementor.php'),
                array('option_name' => 'photospace.php', 'dir_slug' => 'photospace/photospace.php'),
                array('option_name' => 'unitegallery', 'dir_slug' => 'unite-gallery-lite/unitegallery.php'),
                array('option_name' => 'resmushit_options', 'dir_slug' => 'resmushit-image-optimizer/resmushit.php'),
                array('option_name' => 'picture-gallery', 'dir_slug' => 'picture-gallery/picture-gallery.php'),
                array('option_name' => 'imagify', 'dir_slug' => 'imagify/imagify.php'),
                array('option_name' => 'gallery_bank', 'dir_slug' => 'gallery-bank/gallery-bank.php'),
                array('option_name' => 'wp-shortpixel-settings', 'dir_slug' => 'shortpixel-image-optimiser/wp-shortpixel.php'),
                array('option_name' => 'post-gallery-settings', 'dir_slug' => 'simple-post-gallery/plugin.php'),
                array('option_name' => 'image-gallery-settings', 'dir_slug' => 'responsive-photo-gallery/get-responsive-gallery.php'),
                array('option_name' => 'gallery-plugin.php', 'dir_slug' => 'gallery-plugin/gallery-plugin.php'),
                array('option_name' => 'youtube-my-preferences', 'dir_slug' => 'youtube-embed-plus/youtube.php'),
                array('option_name' => 'pfg-update-plugin', 'dir_slug' => 'portfolio-filter-gallery/portfolio-filter-gallery.php'),
                array('option_name' => 'jetpack', 'dir_slug' => 'jetpack/jetpack.php'),
                array('option_name' => 'gallery-options', 'dir_slug' => 'fancy-gallery/plugin.php'),
                array('option_name' => 'gallery-box-options.php', 'dir_slug' => 'gallery-box/gallery-box.php'),
                array('option_name' => 'catch-gallery', 'dir_slug' => 'catch-gallery/catch-gallery.php'),
                array('option_name' => 'galleries_grs', 'dir_slug' => 'limb-gallery/gallery-rs.php'),
                array('option_name' => 'wooswipe-options', 'dir_slug' => 'wooswipe/wooswipe.php'),
                array('option_name' => 'photoswipe-masonry.php', 'dir_slug' => 'photoswipe-masonry/photoswipe-masonry.php'),
                array('option_name' => 'maxgalleria-settings', 'dir_slug' => 'maxgalleria/maxgalleria-admin.php'),
                array('option_name' => 'Emg-whats-new', 'dir_slug' => 'easy-media-gallery/easy-media-gallery.php'),
                array('option_name' => 'wpffag_products', 'dir_slug' => 'flickr-album-gallery/flickr-album-gallery.php'),
                array('option_name' => 'foogallery-settings', 'dir_slug' => 'foogallery/foogallery.php'),
                array('option_name' => 'foogallery-settings', 'dir_slug' => 'foogallery/foogallery.php'),
                array('option_name' => 'modula', 'dir_slug' => 'modula-best-grid-gallery/Modula.php'),
                array('option_name' => 'robo-gallery-settings', 'dir_slug' => 'robo-gallery/robogallery.php'),
                array('option_name' => 'envira', 'dir_slug' => 'envira-gallery-lite/envira-gallery-lite.php'),
                array('option_name' => 'supsystic-gallery', 'dir_slug' => 'gallery-by-supsystic/index.php'),
                array('option_name' => 'ftg-lite-gallery-admin', 'dir_slug' => 'final-tiles-grid-gallery-lite/FinalTilesGalleryLite.php'),
                array('option_name' => 'everest-gallery-lite', 'dir_slug' => 'everest-gallery-lite/everest-gallery-lite.php'),
                array('option_name' => 'photonic-options-manager', 'dir_slug' => 'photonic/photonic.php'),
                array('option_name' => 'meowapps-main-menu', 'dir_slug' => 'meow-gallery/meow-gallery.php'),
                array('option_name' => 'video_galleries_origincode_video_gallery', 'dir_slug' => 'smart-grid-gallery/smart-video-gallery.php'),
                array('option_name' => 'wpape_gallery_type', 'dir_slug' => 'gallery-images-ape/index.php'),
                array('option_name' => 'wc-gallery', 'dir_slug' => 'wc-gallery/wc-gallery.php'),
                array('option_name' => 'elementor', 'dir_slug' => 'elementor/elementor.php'),
                array('option_name' => 'robo_gallery_table', 'dir_slug' => 'robo-gallery/robogallery.php'),
                array('option_name' => 'awl_filter_gallery', 'dir_slug' => 'portfolio-filter-gallery/portfolio-filter-gallery.php'),
                array('option_name' => 'gallery_box', 'dir_slug' => 'gallery-box/gallery-box.php'),
                array('option_name' => 'maxgalleria-settings', 'dir_slug' => 'maxgalleria/maxgalleria.php'),
                array('option_name' => 'fa_gallery', 'dir_slug' => 'flickr-album-gallery/flickr-album-gallery.php'),
                array('option_name' => 'grid_gallery', 'dir_slug' => 'new-grid-gallery/grid-gallery.php'),
            );
            $sharecount_plugins  = array(
                array("dir_slug" => "optinmonster/optin-monster-wp-api.php", 'option_name' => 'optin-monster-api-welcome'),
                array("dir_slug" => "floating-social-bar/floating-social-bar.php", 'option_name' => 'floating-social-bar'),
                array("dir_slug" => "tweet-old-post/tweet-old-post.php", 'option_name' => 'TweetOldPost'),
                array("dir_slug" => "wp-to-buffer/wp-to-buffer.php", 'option_name' => 'wp-to-buffer-settings'),
                array("dir_slug" => "wordpress-seo/wp-seo.php", 'option_name' => 'wpseo_dashboard'),
                array("dir_slug" => "intelly-related-posts/index.php", 'option_name' => 'intelly-related-posts'),
                array("dir_slug" => "wordpress-popular-posts/wordpress-popular-posts.php", 'option_name' => 'wordpress-popular-posts'),
                array("dir_slug" => "subscribe-to-comments-reloaded/subscribe-to-comments-reloaded.php", 'option_name' => 'stcr_options'),
                array("dir_slug" => "click-to-tweet-by-todaymade/tm-click-to-tweet.php", 'option_name' => 'tmclicktotweet'),
                array("dir_slug" => "fb-instant-articles/facebook-instant-articles.php", 'option_name' => 'instant-articles-wizard'),
                array("dir_slug" => "sharebar/sharebar.php", 'option_name' => 'Sharebar'),
                array("dir_slug" => "wp-to-twitter/wp-to-twitter.php", 'option_name' => 'wp-tweets-pro'),
                array("dir_slug" => "sem-bookmark-me/sem-bookmark-me.php", 'option_name' => ''),
                array("dir_slug" => "onlywire-bookmark-share-button/owbutton_wordpress.php", 'option_name' => 'onlywireoptions'),
                array("dir_slug" => "google-analyticator/google-analyticator.php", 'option_name' => 'google-analyticator'),
                array("dir_slug" => "getsocial/getsocial.php", 'option_name' => 'getsocial/getsocial.php'),
                array("dir_slug" => "visitors-traffic-real-time-statistics/Visitors-Traffic-Real-Time-Statistics.php", 'option_name' => 'ahc_hits_counter_menu_free'),
                array("dir_slug" => "microblog-poster/microblogposter.php", 'option_name' => 'microblogposter.php'),
                array("dir_slug" => "triberr-wordpress-plugin/triberr.php", 'option_name' => 'triberr-options'),
                array("dir_slug" => "social-networks-auto-poster-facebook-twitter-g/NextScripts_SNAP.php", 'option_name' => 'nxssnap-ntadmin'),
                array("dir_slug" => "all-in-one-seo-pack/all_in_one_seo_pack.php", 'option_name' => 'all-in-one-seo-pack/aioseop_class.php'),
                array("dir_slug" => "multi-rating/multi-rating.php", 'option_name' => 'mr_settings'),
                array("dir_slug" => "social-pug/index.php", 'option_name' => 'dpsp-social-pug'),
                array("dir_slug" => "comment-reply-email-notification/cren_plugin.php", 'option_name' => 'comment_reply_email_notification'),
                array("dir_slug" => "share-subscribe-contact-aio-widget/free_profitquery_aio_widgets.php", 'option_name' => 'free_profitquery_aio_widgets'),
                array("dir_slug" => "better-robots-txt/better-robots-txt.php", 'option_name' => 'better-robots-txt'),
                array("dir_slug" => "google-analytics-for-wordpress/googleanalytics.php", 'option_name' => 'monsterinsights_settings'),
                array("dir_slug" => "onesignal-free-web-push-notifications/onesignal-push", 'option_name' => 'onesignal-push'),
                array("dir_slug" => "access-watch/index.php", 'option_name' => 'access-watch-dashboard'),
                array("dir_slug" => "tweet-old-post/tweet-old-post.php", 'option_name' => 'TweetOldPost'),
                array("dir_slug" => "mailoptin/mailoptin.php", 'option_name' => 'mailoptin-settings'),
                array("dir_slug" => "NextScripts_SNAP/NextScripts_SNAP.php", 'option_name' => 'nxssnap-reposter'),
                array("dir_slug" => "social-pug-author-box/index.php", 'option_name' => 'social_pug_author_box'),
                array("dir_slug" => "google-analytics-for-wordpress/googleanalytics.php", 'option_name' => 'monsterinsights-getting-started'),
                array("dir_slug" => "onesignal-free-web-push-notifications/onesignal.php", 'option_name' => 'onesignal-push'),
            );
            $optimization_plugins  = array(
                array('dir_slug' => 'litespeed-cache/litespeed-cache.php', 'option_name' => 'lscache-settings'),
                array('dir_slug' => 'w3-total-cache/w3-total-cache.php', 'option_name' => 'w3tc_dashboard'),
                array('dir_slug' => 'wp-fastest-cache/wpFastestCache.php', 'option_name' => 'wpfastestcacheoptions'),
                array('dir_slug' => 'wp-optimize/wp-optimize.php', 'option_name' => 'WP-Optimize'),
                array('dir_slug' => 'autoptimize/autoptimize.php', 'option_name' => 'autoptimize'),
                array('dir_slug' => 'cache-enabler/cache-enabler.php', 'option_name' => 'cache-enabler'),
                array('dir_slug' => 'wp-super-cache/wp-cache.php', 'option_name' => 'wpsupercache'),
                array('dir_slug' => 'hummingbird-performance/wp-hummingbird.php', 'option_name' => 'wphb'),
                array('dir_slug' => 'breeze/breeze.php', 'option_name' => 'breeze'),
                array('dir_slug' => 'sg-cachepress/sg-cachepress.php', 'option_name' => 'sg-cachepress'),
                array('dir_slug' => 'wp-rest-cache/wp-rest-cache.php', 'option_name' => 'wp-rest-cache'),
                array('dir_slug' => 'fast-velocity-minify/fvm.php', 'option_name' => 'fastvelocity-min'),
                array('dir_slug' => 'hyper-cache/plugin.php', 'option_name' => 'hyper-cache/options.php'),
                array('dir_slug' => 'redis-cache/redis-cache.php', 'option_name' => 'redis-cache'),
                array('dir_slug' => 'varnish-page', 'option_name' => 'varnish-page'),
                array('dir_slug' => 'sns-count-cache/sns-count-cache.php', 'option_name' => 'scc-dashboard'),
                array('dir_slug' => 'harrys-gravatar-cache/harrys-gravatar-cache.php', 'option_name' => 'harrys-gravatar-cache-options'),
                array('dir_slug' => 'fv-gravatar-cache/fv-gravatar-cache.php', 'option_name' => 'fv-gravatar-cache'),
                array('dir_slug' => 'wpe-advanced-cache-options/wpe-advanced-cache.php', 'option_name' => 'cache-settings'),
                array('dir_slug' => 'simple-cache/simple-cache.php', 'option_name' => 'simple-cache'),
                array('dir_slug' => 'ezcache/ezcache.php', 'option_name' => 'ezcache'),
                array('dir_slug' => 'wp-cloudflare-page-cache/wp-cloudflare-super-page-cache.php', 'option_name' => 'wp-cloudflare-super-page-cache-index'),
                array('dir_slug' => 'optimum-gravatar-cache/optimum-gravatar-cache.php', 'option_name' => 'optimum-gravatar-cache'),
                array('dir_slug' => 'yasakani-cache/yasakani-cache.php', 'option_name' => 'yasakani-cache'),
                array('dir_slug' => 'cachify/cachify.php', 'option_name' => 'cachify'),
                array('dir_slug' => 'gator-cache/gator-cache.php', 'option_name' => 'gtr_cache'),
                array('dir_slug' => 'wp-speed-of-light/wp-speed-of-light.php', 'option_name' => 'wpsol_dashboard'),
                array('dir_slug' => 'wp-super-minify/wp-super-minify.php', 'option_name' => 'wp-super-minify'),
                array('dir_slug' => 'wsa-cachepurge/wsa-cachepurge.php', 'option_name' => 'wsa-cachepurge/lib/wsa-cachepurge_display.php'),
                array('dir_slug' => 'a2-optimized-wp/a2-optimized.php', 'option_name' => 'A2_Optimized_Plugin_admin'),
                array('dir_slug' => 'nitropack/main.php', 'option_name' => 'nitropack'),
                array('dir_slug' => 'swift-performance-lite/performance.php', 'option_name' => 'swift-performance'),
                array('dir_slug' => 'wp-performance/wp-performance.php', 'option_name' => 'wp-performance'),
                array('dir_slug' => 'arvancloud-cache-cleaner/Arvancloud.php', 'option_name' => 'ar_cache'),
                array('dir_slug' => 'clear-cache-for-widgets/clear-cache-for-widgets.php', 'option_name' => 'ccfm-options'),
                array('dir_slug' => 'wp-asset-clean-up/wpacu.php', 'option_name' => 'wpassetcleanup_settings'),
                array('dir_slug' => 'flying-pages/flying-pages.php', 'option_name' => 'flying-pages'),
                array('dir_slug' => 'speed-booster-pack/speed-booster-pack.php', 'option_name' => 'sbp-options'),
                array('dir_slug' => 'baqend/baqend.php', 'option_name' => 'baqend'),
                array('dir_slug' => 'wp-smushit/wp-smush.php', 'option_name' => 'smush'),
                array('dir_slug' => 'varnish-http-purge/varnish-http-purge.php', 'option_name' => 'varnish-page'),
                array('dir_slug' => 'varnish-http-purge/varnish-http-purge.php', 'option_name' => 'varnish-check-caching'),
            
            );
            $gdpr_plugins  = array(
                array('dir_slug' => 'cookie-law-info/cookie-law-info.php', 'option_name' => 'cookie-law-info'),
                array('dir_slug' => 'complianz-gdpr/complianz-gpdr.php', 'option_name' => 'complianz'),
                array('dir_slug' => 'shapepress-dsgvo/sp-dsgvo.php', 'option_name' => 'sp-dsgvo'),
                array('dir_slug' => 'cookiebot/cookiebot.php', 'option_name' => 'cookiebot'),
                array('dir_slug' => 'gdpr-banner/gdpr-banner.php', 'option_name' => 'gdpr_banner'),
                array('dir_slug' => 'dsgvo-tools-cookie-hinweis-datenschutz/main.php', 'option_name' => 'fhw_dsgvo_cookies_options'),
                array('dir_slug' => 'ga-germanized/ga-germanized.php', 'option_name' => 'ga-germanized'),
                array('dir_slug' => 'cwis-antivirus-malware-detected/cwis-antivirus-malware-detected.php', 'option_name' => 'cwis-updater'),
                array('dir_slug' => 'luckywp-cookie-notice-gdpr/luckywp-cookie-notice-gdpr.php', 'option_name' => 'lwpcng_settings'),
                array('dir_slug' => 'ninja-gdpr-compliance/njt-gdpr.php', 'option_name' => 'njt-gdpr'),
                array('dir_slug' => 'gdpr-cookie-consent/gdpr-cookie-consent.php', 'option_name' => 'gdpr-cookie-consent'),
                array('dir_slug' => 'uniconsent-cmp/uniconsent-cmp.php', 'option_name' => 'unic-options'),
                array('dir_slug' => 'wplegalpages/wplegalpages.php', 'option_name' => 'legal-pages'),
                array('dir_slug' => 'smart-cookie-kit/plugin.php', 'option_name' => 'nmod_sck_graphics'),
                array('dir_slug' => 'cookie-information-consent-solution/cookie-information.php', 'option_name' => 'cookie-information'),
                array('dir_slug' => 'dsgvo-fur-die-schweiz/dsgvo-fur-die-schweiz.php', 'option_name' => 'dsgvo-admin'),
                array('dir_slug' => 'gdpr-cookies-pro/gdpr-cookies-pro.php', 'option_name' => 'gdpr-cookies-pro'),
                array('dir_slug' => 'seahorse-gdpr-data-manager/seahorse-gdpr-data-manager.php', 'option_name' => 'seahorse_gdpr_data_manager_plugin'),
                array('dir_slug' => 'dsgvo-tools-kommentar-ip-entfernen/main.php', 'option_name' => 'fhw_dsgvo_kommentar_options'),
                array('dir_slug' => 'gdpr-tools/gdpr-tools.php', 'option_name' => 'gdpr-tools-settings'),
                array('dir_slug' => 'gdpr-cookie-compliance/moove-gdpr.php', 'option_name' => 'moove-gdpr'),
                array('dir_slug' => 'cookie-notice/cookie-notice.php', 'option_name' => 'cookie-notice'),
                array('dir_slug' => 'tarteaucitronjs/tarteaucitron.php', 'option_name' => 'tarteaucitronjs'),
                array('dir_slug' => 'wp-gdpr-compliance/wp-gdpr-compliance.php', 'option_name' => 'wp_gdpr_compliance'),
                array('dir_slug' => 'iubenda_cookie_solution/iubenda_cookie_solution.php', 'option_name' => 'iubenda'),
                array('dir_slug' => 'easy-wp-cookie-popup/easy-wp-cookie-popup.php', 'option_name' => 'cookii_settings'),
                array('dir_slug' => 'gdpr-compliance-cookie-consent/gdpr-compliance-cookie-consent.php', 'option_name' => 'gdpr-compliance-cookie-consent'),
                array('dir_slug' => 'yetience-plugin/yetience-plugin.php', 'option_name' => 'yetience-yeloni'),
                array('dir_slug' => 'cwis-antivirus-malware-detected/cwis-antivirus-malware-detected.php', 'option_name' => 'cwis-scanner'),
                array('dir_slug' => 'gdpr-compliance-by-supsystic/grs.php', 'option_name' => 'gdpr-compliance-by-supsystic'),
                array('dir_slug' => 'auto-terms-of-service-privacy-policy/auto-terms-of-service-privacy-policy.php', 'option_name' => 'wpautoterms_page'),
                array('dir_slug' => 'google-analytics-opt-out/google-analytics-opt-out.php', 'option_name' => 'gaoo-options'),
                array('dir_slug' => 'surbma-gdpr-proof-google-analytics/surbma-gdpr-proof-google-analytics.php', 'option_name' => 'surbma-gpga-menu'),
                array('dir_slug' => 'bp-gdpr/buddypress-gdpr.php', 'option_name' => 'buddyboss-bp-gdpr'),
                array('dir_slug' => 'beautiful-and-responsive-cookie-consent/nsc_bar-cookie-consent.php', 'option_name' => 'nsc_bar-cookie-consent'),
                array('dir_slug' => 'simple-gdpr/simple-gdpr.php', 'option_name' => 'SGDPR_settings'),
                array('dir_slug' => 'wonderpush-web-push-notifications/wonderpush.php', 'option_name' => 'wonderpush'),
                array('dir_slug' => 'ns-gdpr/ns-gdpr.php', 'option_name' => 'ns-gdpr'), 
            );
            $google_analytics  = array(
                array('dir_slug' => 'really-simple-ssl/rlrsssl-really-simple-ssl.php', 'option_name' => 'rlrsssl_really_simple_ssl'),
                array('dir_slug' => 'ssl-insecure-content-fixer/ssl-insecure-content-fixer.php', 'option_name' => 'ssl-insecure-content-fixer'),
                array('dir_slug' => 'https-redirection/https-redirection.php', 'option_name' => 'https-redirection'),
                array('dir_slug' => 'wordpress-https/wordpress-https.php', 'option_name' => 'wordpress-https'),
                array('dir_slug' => 'wp-force-ssl/wp-force-ssl.php', 'option_name' => 'wpfs-settings'),
                array('dir_slug' => 'sakura-rs-wp-ssl/sakura-rs-ssl.php', 'option_name' => 'sakura-admin-menu'),
                array('dir_slug' => 'wp-letsencrypt-ssl/wp-letsencrypt.php', 'option_name' => 'wp_encryption'),
                array('dir_slug' => 'ssl-zen/ssl_zen.php', 'option_name' => 'ssl_zen'),
                array('dir_slug' => 'one-click-ssl/ssl.php', 'option_name' => 'one-click-ssl'),
                array('dir_slug' => 'http-https-remover/http-https-remover.php', 'option_name' => 'httphttpsRemoval')
            );

            $socialObj = new sfsi_SocialHelper();
            $current_url = site_url();
            $fb_data = $socialObj->sfsi_get_fb($current_url);
            $check_fb_count_more_than_one = $fb_data > 0 || $socialObj->sfsi_get_pinterest($current_url) > 0;


            // $sfsi_banner_global_firsttime_offer = unserialize(get_option('sfsi_banner_global_firsttime_offer', false));
            $sfsi_banner_global_pinterest = unserialize(get_option('sfsi_banner_global_pinterest', false));
            $sfsi_banner_global_social = unserialize(get_option('sfsi_banner_global_social', false));
            $sfsi_banner_global_load_faster = unserialize(get_option('sfsi_banner_global_load_faster', false));
            $sfsi_banner_global_shares = unserialize(get_option('sfsi_banner_global_shares', false));
            $sfsi_banner_global_gdpr = unserialize(get_option('sfsi_banner_global_gdpr', false));
            $sfsi_banner_global_http = unserialize(get_option('sfsi_banner_global_http', false));
            $sfsi_banner_global_upgrade = unserialize(get_option('sfsi_banner_global_upgrade', false));

            // $sfsi_banner_global_firsttime_offer_criteria = true;
            $sfsi_banner_global_pinterest_criteria = ((sfsi_count_media_item() > 2) || (sfsi_pinterest_icon_shown()) || sfsi_has_gallery_plugin_activated($gallery_plugins));
            $sfsi_banner_global_social_criteria =  sfsi_mobile_icons_shown();
            $sfsi_banner_global_load_faster_criteria = sfsi_has_cache_plugin_activated($optimization_plugins);
            $sfsi_banner_global_shares_criteria = sfsi_has_sharecount_plugin_activated($sharecount_plugins);
            $sfsi_banner_global_gdpr_criteria  = sfsi_has_gdpr_plugin_activated($gdpr_plugins);
            $sfsi_banner_global_http_criteria = is_ssl() && $check_fb_count_more_than_one;
            // $sfsi_banner_global_http_criteria = true;


            $global_banners = array(
                array($sfsi_banner_global_social, 'sfsi_banner_global_social', $sfsi_banner_global_social_criteria),
                array($sfsi_banner_global_gdpr, 'sfsi_banner_global_gdpr', $sfsi_banner_global_gdpr_criteria),
                array($sfsi_banner_global_pinterest, 'sfsi_banner_global_pinterest', $sfsi_banner_global_pinterest_criteria),
                array($sfsi_banner_global_load_faster, 'sfsi_banner_global_load_faster', $sfsi_banner_global_load_faster_criteria),
                array($sfsi_banner_global_shares, 'sfsi_banner_global_shares', $sfsi_banner_global_shares_criteria),
                array($sfsi_banner_global_http, 'sfsi_banner_global_http', $sfsi_banner_global_http_criteria),
            );
            $global_banners_not_met_criteria = array(
                array($sfsi_banner_global_pinterest, 'sfsi_banner_global_pinterest', !(sfsi_count_media_item() > 2)),
                array($sfsi_banner_global_shares, 'sfsi_banner_global_shares', $sfsi_banner_global_shares_criteria),
                array($sfsi_banner_global_load_faster, 'sfsi_banner_global_load_faster', $sfsi_banner_global_load_faster_criteria),
                array($sfsi_banner_global_gdpr, 'sfsi_banner_global_gdpr', $sfsi_banner_global_gdpr_criteria),
            );
            $global_banner_criteria = array(
                $sfsi_banner_global_pinterest_criteria,
                $sfsi_banner_global_social_criteria,
                $sfsi_banner_global_load_faster_criteria,
                $sfsi_banner_global_shares_criteria,
                $sfsi_banner_global_gdpr_criteria,
                $sfsi_banner_global_http_criteria
            );
            // var_dump($global_banner_criteria);

            $global_banner_criteria_true_count = count(array_keys($global_banner_criteria, true));
            $global_banner_appeared_true_count = 0;

            $count = 0;
            $sfsi_present_time = strtotime(date('Y-m-d h:i:s'));
            $sfsi_install_time = (get_option('sfsi_installDate'));
            $sfsi_loyalty = get_option("sfsi_loyaltyDate");
            $sfsi_min_loyalty_time = date('Y-m-d H:i:s', strtotime($sfsi_install_time . $sfsi_loyalty));
            $sfsi_round_one_added = false;
            foreach ($global_banners as $key => $global_banner) {

                if ($sfsi_present_time >= strtotime($global_banner[0]['timestamp']) || ($global_banner[0]['timestamp'] == "")) {
                    // var_dump("round1",$global_banner[1]);

                    if ($global_banner[0]['met_criteria'] == "yes") {
                        $count = $count + 1;
                    }
                    if ($global_banner[0]['banner_appeared'] == "yes") {
                        $global_banner_appeared_true_count = $global_banner_appeared_true_count + 1;
                    }
                    if ($global_banner[0]['met_criteria'] == "no" && $global_banner[0]['banner_appeared'] == "no" && $global_banner[0]['is_active'] == "no" && $global_banner[2] == true) {
                        // var_dump('met criteria');
                        $todaysdate = date("Y-m-d h:i:s");
                        $showNextBanner = get_option('sfsi_showNextBannerDate');
                        if ($todaysdate >= $sfsi_min_loyalty_time && $sfsi_banner_global_upgrade['met_criteria'] == "no") {
                            $date = date('Y-m-d H:i:s', strtotime($todaysdate . $showNextBanner));
                            $update_banner_status = array(
                                'met_criteria'     => "yes",
                                'is_active' => "yes",
                                'timestamp' =>  $date
                            );
                            update_option('sfsi_banner_global_upgrade', serialize($update_banner_status));
                            break;
                        }
                        $date = date('Y-m-d H:i:s', strtotime($todaysdate . $showNextBanner));
                        $update_banner_status = array(
                            'met_criteria'     => "yes",
                            'banner_appeared' => "yes",
                            'is_active' => "yes",
                            'timestamp' =>  $date
                        );
                        update_option($global_banner[1], serialize($update_banner_status));
                        $sfsi_round_one_added = true;

                        break;
                    }
                }
            }

            $global_banners_filters = array_filter($global_banners_not_met_criteria, function ($global_banner) {
                return ($global_banner[2] == false && $global_banner[0]['met_criteria'] == "no" && $global_banner[0]['banner_appeared'] == "no" && $global_banner[0]['is_active'] == "no");
            });
            $global_banners_criteria_filters = array_filter($global_banners, function ($global_banner) {
                return ($global_banner[2] == true && $global_banner[0]['met_criteria'] == "no" && $global_banner[0]['banner_appeared'] == "no" && $global_banner[0]['is_active'] == "no");
            });
            // var_dump("round one added",$sfsi_round_one_added);
            if (false === $sfsi_round_one_added) {
                foreach ($global_banners_filters as $key => $global_banners_filter) {
                    // if ($count >= $global_banner_criteria_true_count) {
                    // var_dump('round2', $global_banners_filter);
                    if ($global_banners_filter[0]['met_criteria'] == "no" && $global_banners_filter[0]['banner_appeared'] == "no" && $global_banners_filter[0]['is_active'] == "no" && $global_banners_filter[2] == false) {
                        $todaysdate = date("Y-m-d h:i:s");
                        $showNextBanner = get_option('sfsi_showNextBannerDate');
                        if ($todaysdate >= $sfsi_min_loyalty_time && $sfsi_banner_global_upgrade['met_criteria'] == "no") {
                            $date = date('Y-m-d H:i:s', strtotime($todaysdate . $showNextBanner));
                            $update_banner_status = array(
                                'met_criteria'     => "yes",
                                'is_active' => "yes",
                                'timestamp' =>  $date
                            );
                            update_option('sfsi_banner_global_upgrade', serialize($update_banner_status));
                            break;
                        }
                        $date = date('Y-m-d H:i:s', strtotime($todaysdate . $showNextBanner));
                        $update_banner_status = array(
                            'met_criteria'     => "no",
                            'banner_appeared' => "yes",
                            'is_active' => "yes",
                            'timestamp' =>  $date
                        );
                        update_option($global_banners_filter[1], serialize($update_banner_status));
                        break;
                    }
                    // }
                }
            }
            if (empty($global_banners_filters) && empty($global_banners_criteria_filters)) {
                foreach ($global_banners as $key => $global_banner) {

                    $todaysdate = date("Y-m-d h:i:s");
                    $cycleDate = get_option('sfsi_cycleDate');

                    $date_plus_180 =  date('Y-m-d H:i:s', strtotime($todaysdate . $cycleDate));
                    $update_banner_status = array(
                        'met_criteria'     => "no",
                        'banner_appeared' => "no",
                        'is_active' => "no",
                        'timestamp' =>  $date_plus_180,
                    );
                    update_option($global_banner[1], serialize($update_banner_status));
                }
                foreach ($global_banners as $key => $global_banner) {
                    if ($global_banner[2] == true) {
                        $update_banner_status = array(
                            'met_criteria'     => "yes",
                            'banner_appeared' => "yes",
                            'is_active' => "yes",
                            'timestamp' =>  $date_plus_180,
                        );
                        update_option($global_banner[1], serialize($update_banner_status));
                        break;
                    }
                }
                if ($global_banner_criteria_true_count == 0) {
                    foreach ($global_banners_not_met_criteria as $key => $global_banner) {
                        $update_banner_status = array(
                            'met_criteria'     => "no",
                            'banner_appeared' => "yes",
                            'is_active' => "yes",
                            'timestamp' =>  $date_plus_180,
                        );
                        // var_dump($global_banners_not_met_criteria,'kfdjgkdsfgndfkngn isdfhgi hsdfg    idhfguidfi');
                        if ($global_banner[2] == false) {
                            update_option($global_banner[1], serialize($update_banner_status));
                            break;
                        }
                    }
                }
            }
            // return false;
        }

        function sfsi_count_media_item()
        {
            $query_img_args = array(
                'post_type' => 'attachment',
                'post_mime_type' => array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'png' => 'image/png',
                ),
                'post_status' => 'inherit',
                'posts_per_page' => -1,
            );
            $query_img = new WP_Query($query_img_args);
            return $query_img->post_count;
        }
        function sfsi_pinterest_icon_shown()
        {
            $sfsi_section1       =  unserialize(get_option('sfsi_section1_options', false));
            $option9 =  unserialize(get_option('sfsi_section9_options', false));
            $option6 =  unserialize(get_option('sfsi_section6_options', false));
            // var_dump($option9["sfsi_icons_float"]);
            // var_dump($option9["sfsi_show_via_widget"]);
            // var_dump($option9["sfsi_show_via_shortcode"]);
            // var_dump($sfsi_section1["sfsi_pinterest_display"]);
            // var_dump($option6["sfsi_show_Onposts"]);
            // var_dump($option6["sfsi_rectpinit"]);
            // var_dump($option9["sfsi_show_via_afterposts"]);
            //check if icons are displayed
            if (
                (
                    (
                        (isset($option9["sfsi_icons_float"]) && $option9["sfsi_icons_float"] == "yes") || (isset($option9["sfsi_show_via_widget"]) && $option9["sfsi_show_via_widget"] == "yes") || (isset($option9["sfsi_show_via_shortcode"]) && $option9["sfsi_show_via_shortcode"] == "yes")) &&
                    $sfsi_section1["sfsi_pinterest_display"] == "yes") || (isset($option9["sfsi_show_via_afterposts"]) &&
                    $option9["sfsi_show_via_afterposts"] == "yes" &&
                    $option6["sfsi_show_Onposts"] == "yes" &&
                    $option6["sfsi_rectpinit"] == "yes")
            ) {
                return true;
            }
            return false;
        }
        function sfsi_mobile_icons_shown()
        {
            /// check if mobile icons are shown and mobile icons are present on the homepage.
            $sfsi_section9            =  unserialize(get_option('sfsi_section9_options', false));
            if ($sfsi_section9['sfsi_disable_floaticons'] == "yes") {
                return true;
            }
            return false;
        }
        function sfsi_has_cache_plugin_activated($optimization_plugins)
        {
            $sfsi_optimization_plugin_active = array();
            foreach ($optimization_plugins as $key => $optimization_plugin) {
                $sfsi_optimization_plugin_active[$key] = is_plugin_active($optimization_plugin['dir_slug']);
            }
            $check_optimization_plugin_active_is_true = in_array(true, $sfsi_optimization_plugin_active);
            return $check_optimization_plugin_active_is_true;
        }

        function sfsi_has_sharecount_plugin_activated($sharecount_plugins)
        {
            $sfsi_sharecount_plugin_active = array();
            foreach ($sharecount_plugins as $key => $sharecount_plugin) {
                $sfsi_sharecount_plugin_active[$key] = is_plugin_active($sharecount_plugin['dir_slug']);
            }
            $check_sharecount_plugin_active_is_true = in_array(true, $sfsi_sharecount_plugin_active);
            return $check_sharecount_plugin_active_is_true;
        }

        function sfsi_has_gdpr_plugin_activated($gdpr_plugins)
        {
            $sfsi_gdpr_plugin_active = array();
            foreach ($gdpr_plugins as $key => $gdpr_plugin) {
                $sfsi_gdpr_plugin_active[$key] = is_plugin_active($gdpr_plugin['dir_slug']);
            }
            $check_gdpr_plugin_active_is_true = in_array(true, $sfsi_gdpr_plugin_active);
            return $check_gdpr_plugin_active_is_true;
        }

        function sfsi_has_gallery_plugin_activated($gallery_plugins)
        {
            $sfsi_gallery_plugin_active = array();
            foreach ($gallery_plugins as $key => $gallery_plugin) {
                $sfsi_gallery_plugin_active[$key] = is_plugin_active($gallery_plugin['dir_slug']);
            }
            $check_gallery_plugin_active_is_true = in_array(true, $sfsi_gallery_plugin_active);
            return $check_gallery_plugin_active_is_true;
        }

        ?>