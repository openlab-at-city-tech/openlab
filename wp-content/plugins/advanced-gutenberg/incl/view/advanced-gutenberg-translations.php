<?php
defined('ABSPATH') || die;
?>

<div class="advgb-header" style="padding-top: 40px">
    <h1 class="header-title"><?php esc_html_e('Translation', 'advanced-gutenberg') ?></h1>
</div>

<div class="notice notice-info">
    <p><?php _e('The previous translation system has been removed. However, you can now translate PublishPress Blocks into your language using the normal WordPress translation system. You can use the .mo and .po files located in the /wp-content/advanced-gutenberg/plugins/languages/ folder.', 'advanced-gutenberg'); ?></p>
    <p><?php echo sprintf(
            __('If you have any questions, please send us a message through %1$sour contact page%2$s.', 'advanced-gutenberg'),
            '<a href="' . esc_url('https://publishpress.com/publishpress-support/') . '" target="_blank">',
            '</a>'
        ); ?></p>
</div>