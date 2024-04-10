<?php
/**
 * The Radio Icon customize control extends the WP_Customize_Control class.
 *
 * @package customizer-controls
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}


/**
 * Class Kadence_Control_Import_Export
 *
 * @access public
 */
class Kadence_Control_Import_Export extends WP_Customize_Control {
	/**
	 * Control type
	 *
	 * @var string
	 */
	public $type = 'kadence_import_export_control';
	/**
	 * Empty Render Function to prevent errors.
	 */
	public function render_content() {
		?>
			<span class="customize-control-title">
				<?php esc_html_e( 'Export', 'kadence' ); ?>
			</span>
			<span class="description customize-control-description">
				<?php esc_html_e( 'Click the button below to export the customization settings for this theme.', 'kadence' ); ?>
			</span>
			<input type="button" class="button kadence-theme-export kadence-theme-button" name="kadence-theme-export-button" value="<?php esc_attr_e( 'Export', 'kadence' ); ?>" />

			<hr class="kt-theme-hr" />

			<span class="customize-control-title">
				<?php esc_html_e( 'Import', 'kadence' ); ?>
			</span>
			<span class="description customize-control-description">
				<?php esc_html_e( 'Upload a file to import customization settings for this theme.', 'kadence' ); ?>
			</span>
			<div class="kadence-theme-import-controls">
				<input type="file" name="kadence-theme-import-file" class="kadence-theme-import-file" />
				<?php wp_nonce_field( 'kadence-theme-importing', 'kadence-theme-import' ); ?>
			</div>
			<div class="kadence-theme-uploading"><?php esc_html_e( 'Uploading...', 'kadence' ); ?></div>
			<input type="button" class="button kadence-theme-import kadence-theme-button" name="kadence-theme-import-button" value="<?php esc_attr_e( 'Import', 'kadence' ); ?>" />

			<hr class="kt-theme-hr" />
			<span class="customize-control-title">
				<?php esc_html_e( 'Reset', 'kadence' ); ?>
			</span>
			<span class="description customize-control-description">
				<?php esc_html_e( 'Click the button to reset all theme settings.', 'kadence' ); ?>
			</span>
			<input type="button" class="components-button is-destructive kadence-theme-reset kadence-theme-button" name="kadence-theme-reset-button" value="<?php esc_attr_e( 'Reset', 'kadence' ); ?>" />
			<?php
	}
}