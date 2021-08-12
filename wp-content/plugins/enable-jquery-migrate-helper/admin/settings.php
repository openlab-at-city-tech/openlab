<?php
/**
 * Admin settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

$downgraded = get_option( '_jquery_migrate_downgrade_version', 'no' );
$show_deprecations = jQuery_Migrate_Helper::show_deprecated_scripts_notice();
$public_deprecations = get_option( '_jquery_migrate_public_deprecation_logging', 'no' );
$log_modern_deprecations = get_option( '_jquery_migrate_modern_deprecations', 'no' );
$has_auto_downgraded = get_option( '_jquery_migrate_has_auto_downgraded', 'no' );
?>

<h2>Settings</h2>

<form method="post" action="">
    <input type="hidden" name="jqmh-settings" value="true">
	<?php wp_nonce_field( 'jqmh-settings' ); ?>

	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">
                <label for="jquery-version">
				    <?php _e( 'jQuery Version', 'enable-jquery-migrate-helper' ); ?>
                </label>
			</th>
			<td>
				<select name="jquery-version" id="jquery-version" <?php echo ( ! is_wp_version_compatible( '5.6-alpha' ) ? 'disabled="disabled"' : '' ); ?>>
                    <option value="no" <?php echo ( 'no' === $downgraded ? 'selected="selected"' : '' ); ?>><?php _ex( 'Default from WordPress', 'jQuery version', 'enable-jquery-migrate-helper' ); ?></option>
                    <option value="yes" <?php echo ( 'yes' === $downgraded ? 'selected="selected"' : '' ); ?>><?php _ex( 'Legacy 1.12.4-wp', 'jQuery version', 'enable-jquery-migrate-helper' ); ?></option>
                </select>
				<?php if ( ! is_wp_version_compatible( '5.6-alpha' ) ) : ?>
                <p class="description">
                    <?php _e( 'You can only change jQuery versions in WordPress 5.6 or later', 'enable-jquery-migrate-helper' ); ?>
                </p>
                <?php endif; ?>
			</td>
		</tr>

        <tr>
            <th scope="row">
                <?php _e( 'Live deprecations', 'enable-jquery-migrate-helper' ); ?>
            </th>
            <td>
                <label>
                    <input name="live-deprecations" type="checkbox" <?php checked( $show_deprecations ); ?>>
                    <?php _e( 'Show deprecation notices, on each admin page, as they happen', 'enable-jquery-migrate-helper' ); ?>
                </label>
            </td>
        </tr>

        <?php if ( 'no' === $downgraded && is_wp_version_compatible( '5.6-alpha' ) ) : ?>
        <tr>
            <th scope="row">
                <?php _e( 'Automatic downgrades', 'enable-jquery-migrate-helper' ); ?>
            </th>
            <td>
                <label>
                    <input name="automatic-downgrade" type="checkbox" <?php checked( ( 'no' === $has_auto_downgraded ) ); ?>>
                    <?php _e( 'Allow website visitors to trigger an automatic downgrading to legacy jQuery, when a failure is detected', 'enable-jquery-migrate-helper' ); ?>
                </label>
            </td>
        </tr>
        <?php endif; ?>

        <?php if ( 'no' === $downgraded && is_wp_version_compatible( '5.6-alpha' ) ) : ?>
        <tr>
            <th scope="row">
				<?php _e( 'Capture modern deprecations', 'enable-jquery-migrate-helper' ); ?>
            </th>
            <td>
                <label>
                    <input name="modern-deprecations" type="checkbox" <?php checked( 'yes' === $log_modern_deprecations ); ?>>
					<?php _e( 'Detect and log deprecations in the default WordPress version of jQuery', 'enable-jquery-migrate-helper' ); ?>
                </label>
                <p class="description">
                    <?php _e( 'This may report many entries from WordPress it self. This is expected, as WordPress continues to update its own code in the upcoming releases.', 'enable-jquery-migrate-helper' ); ?>
                </p>
            </td>
        </tr>
        <?php endif; ?>

        <tr>
            <th scope="row">
                <?php _e( 'Public deprecation logging' ); ?>
            </th>
            <td>
                <label>
                    <input name="public-deprecation-logging" type="checkbox" <?php checked( 'yes' === $public_deprecations ); ?>>
                    <?php _e( 'Log deprecations caused by anonymous users browsing your website', 'enable-jquery-migrate-helper' ); ?>
                </label>
                <p class="description">
                    <?php _e( 'Caution: This option may lead to more deprecations being discovered, but will also increase the amount of database entries. Use sparingly and under supervision.', 'enable-jquery-migrate-helper' ); ?>
                </p>
            </td>
        </tr>
	</table>

    <?php submit_button( __( 'Save settings', 'enable-jquery-migrate-helper' ) ); ?>
</form>
