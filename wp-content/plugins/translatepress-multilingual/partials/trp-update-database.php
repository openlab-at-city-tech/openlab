<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <div id="trp-settings__wrap">
        <div class="trp-settings-container">
            <h1 class="trp-settings-primary-heading"> <?php esc_html_e( 'TranslatePress Database Updater', 'translatepress-multilingual' );?></h1>
            <div class="trp-settings-separator"></div>

            <div class="grid feat-header">
                <div class="grid-cell">
                    <h2><?php esc_html_e('Updating TranslatePress tables. Please leave this window open.', 'translatepress-multilingual' );?> </h2>
                    <div id="trp-update-database-progress">
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>