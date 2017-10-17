<?php
/*
Plugin Name: Cite
Plugin URI: http://wordpress.org/plugins/cite
Description: Help readers know how to cite your article correctly - use Cite plugin to display a box at the bottom of each page/post with reference information.
Version: 1.2.2
Author: EnigmaWeb
Author URI: https://profiles.wordpress.org/enigmaweb
*/


// Localization / Internationalization
load_plugin_textdomain('cite', false, dirname(plugin_basename(__FILE__)) . '/languages/');


// Default settings
$wpcp_default = apply_filters('wpcp_default_setting', array(
    'setting' => __('Cite this article as: {author}, "{title}," in <em>{sitename}</em>, {publication_date}, {permalink}.','cite')
        ));


// Pulling the default settings from DB + Fallback
$wpcp_setting = wp_parse_args(get_option('wpcp_setting'), $wpcp_default);


// This function registering the settings in DB
add_action('admin_init', 'wpcp_register_setting');

function wpcp_register_setting() {
    register_setting('wpcp_setting', 'wpcp_setting');
}

// Adding settings page in wp menu
add_action('admin_menu', 'wpcp_setting_menu');

function wpcp_setting_menu() {
    add_menu_page(__('Cite Settings', 'cite'), __('Cite', 'cite'), 'manage_options', 'wp-cite', 'wpcp_setting_page', plugin_dir_url(__FILE__) . 'cite-icon.png', 55);
}

// This function checks to see if we just updated the settings
// if so, it displays the "updated" message.
function wpcp_setting_update_check() {
    global $wpcp_setting;
    if (isset($wpcp_setting['update'])) {
        echo '<div style="margin-top:20px;" class="updated fade" id="message"><p>' . __('Cite Settings', 'cite') . ' <strong>' . $wpcp_setting['update'] . '</strong></p></div>';
        unset($wpcp_setting['update']);
        update_option('wpcp_setting', $wpcp_setting);
    }
}

// Display admin page
function wpcp_setting_page() {
    echo '<div class="wrap">';

    //	The cite adding form
    wpcp_admin();

    echo '</div>';
}

// Admin page
function wpcp_admin() {
    ?>
    <?php wpcp_setting_update_check(); ?>
    <form method="post" action="options.php">
        <?php settings_fields('wpcp_setting'); ?>
        <?php global $wpcp_setting; ?>
        <div class="wpcp-admin">
            <h2><?php _e('Cite Settings', 'cite') ?></h2>
            <p><?php _e('Help readers know how to cite your article correctly. Enter the reference text you wish to appear in the cite box using the editor below. Add the cite box to any page/post using shortcode', 'cite') ?> <code>[cite]</code></p>
            <p><textarea cols="80" rows="5" name="wpcp_setting[setting]" id="wpcp_setting[setting]" class="wpcp-textarea"><?php echo $wpcp_setting[setting]; ?></textarea></p>
            <p class="wpcp-templates-info"><span><?php _e('Available templates tags:', 'cite') ?></span><br>
              {author} - <?php _e('the post/page author','cite') ?><br>
              {title} - <?php _e('the title of your post/page', 'cite') ?><br>
              {sitename} - <?php _e('your site name taken from Settings > General', 'cite') ?><br>
              {publication_date} - <?php _e('date the page/post was published', 'cite') ?><br>
              {permalink} - <?php _e('the permalink of the page/post being accessed', 'cite') ?><br>
              {date} - <?php _e('the current date, if "date accessed" is desired', 'cite') ?><br>
              <?php _e('Also, you may insert words, HTML tags, and punctuation.', 'cite') ?><br><br>
              <b><?php _e('Samples', 'cite') ?></b> (<?php _e('similar to', 'cite') ?> <a href="http://www.chicagomanualofstyle.org/tools_citationguide.html" target="_blank"><?php _e('Chicago-style notes', 'cite') ?></a>):<br>
              <?php _e('Blog post:', 'cite') ?> {author}, "{title}," {sitename}, {publication_date}, {permalink}.<br>
              <?php _e('Book chapter:', 'cite') ?> {author}, "{title}," in {sitename}, ed. Jack Dougherty (Ann Arbor: Michigan Publishing, 2014), {permalink}.</p>
            <input type="hidden" name="wpcp_setting[update]" value="<?php _e('UPDATED', 'cite') ?>" />
            <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cite') ?>" />
    </form>
    </div>
    <?php
}

// Registering shortcode [cite]
add_shortcode('cite', 'cite_shortcode');

function cite_shortcode() {
    global $wpcp_setting;

    // Getting admin preferred date format for current date
	if(!function_exists('displayTodaysDate')){
    function displayTodaysDate() {
        return date_i18n(get_option('date_format'));
	}
	}

    $find_string = array('{author}','{sitename}', '{title}', '{date}', '{publication_date}', '{permalink}');
    $replace_string = array(get_the_author(), get_bloginfo('name'), get_the_title(), displayTodaysDate(), get_the_date(), '<a href="' . get_permalink() . '">' . get_permalink() . '</a>');
    $edited_setting = str_replace($find_string, $replace_string, $wpcp_setting[setting]);
    return '<div class="wpcp">' . $edited_setting . '</div>';
}

// Adding some makeup
add_action('wp_head', 'wpcp_head');

function wpcp_head() {
    ?>
    <style type="text/css">
        .wpcp {background: #f7f7f7; padding: 16px 20px; border-radius: 5px; line-height: 20px;}
    </style>
    <?php
}

add_action('admin_head', 'wpcp_admin_head');

function wpcp_admin_head() {
    ?>
    <style type="text/css">
        .wpcp-admin {width: 700px;}
        .wpcp-textarea {width: 100%; font-family: courier;}
        .wpcp-templates-info {margin: 0 0 40px;}
        .wpcp-templates-info span {display: inline-block; margin-bottom: 5px; font-weight: bold;}
    </style>
    <?php
}