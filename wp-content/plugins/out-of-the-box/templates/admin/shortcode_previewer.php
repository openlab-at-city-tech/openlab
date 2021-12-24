<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Exit if no permission to add shortcodes
if (
  !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_shortcodes']))
) {
    exit();
}

$this->load_scripts();
$this->load_styles();

function OutoftheBox_remove_all_scripts()
{
    global $wp_scripts;
    $wp_scripts->queue = [];

    wp_enqueue_script('jquery-effects-fade');
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery');
    wp_enqueue_script('OutoftheBox');
    wp_enqueue_script('OutoftheBox.ShortcodeBuilder');
}

function OutoftheBox_remove_all_styles()
{
    global $wp_styles;
    $wp_styles->queue = [];
    wp_enqueue_style('OutoftheBox.ShortcodeBuilder');
    wp_enqueue_style('OutoftheBox.CustomStyle');
    wp_enqueue_style('Eva-Icons');
}

add_action('wp_print_scripts', 'OutoftheBox_remove_all_scripts', 1000);
add_action('wp_print_styles', 'OutoftheBox_remove_all_styles', 1000);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php esc_html_e('Shortcode Previewer', 'wpcloudplugins'); ?></title>
     <?php wp_print_scripts(); ?>
    <?php wp_print_styles(); ?>
</head>

<body>
  <?php

  $atts = $_REQUEST;
  echo $this->get_processor()->create_from_shortcode($atts);

  wp_footer();
  ?>
</body>
</html>