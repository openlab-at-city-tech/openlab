<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'trp_advanced_settings' ); ?>
        <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

        <div class="advanced_setting_tab_class">
            <div id="trp-settings__wrap">
                <?php do_action('trp_before_output_advanced_settings_options' ); ?>
                <?php do_action('trp_output_advanced_settings_options' ); ?>
                <button type="submit" class="trp-submit-btn">
                    <?php esc_html_e( 'Save Changes', 'translatepress-multilingual' ); ?>
                </button>
            </div>
        </div>
    </form>
</div>
