<?php
/**
 * Displays the suspend main metabox.
 *
 * @since 2.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="bptk-suspend-settings" class="bptk-box">
    <h3 class="bptk-box-header"><?php _e( 'Suspend Settings', 'bp-toolkit' ) ?></h3>
    <div id="" class="panel bptk-metabox bptk-no-tabs">
        <div class="bptk-metabox-header">
            <p><?php _e( 'With the suspend service, you can use the suspend button on your member\'s profile pages to prevent them from logging in. If they are currently using your site, the plugin will detect that and end their session. You can also choose the error message they receive when they try to log in. As well as the suspend buttons, users can also be suspended via the reporting system, either automatically when a set number of reports are received, or when an administrator suspends them via an individual report page. If you want to notify the user that they have been suspended (and unsuspended), you can use the Emails tab under report settings.',
					'bp-toolkit' ) ?></p>
        </div>
        <form method="post" action="options.php">
			<?php
			do_settings_sections( 'suspend_section' );
			settings_fields( 'suspend_section' );
			submit_button();
			?>
        </form>
        <p class="bptk-docs-link"><a href="<?php echo BP_TOOLKIT_SUPPORT ?>"
                                     target="_blank"><?php _e( 'Need Help? See docs on "Suspend Settings"',
					'bp-toolkit' ) ?><span class="dashicons dashicons-editor-help"></span></a></p>
    </div>
</div>
