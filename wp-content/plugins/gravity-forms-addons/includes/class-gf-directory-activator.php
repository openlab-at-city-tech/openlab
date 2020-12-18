<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes
 */

class GFDirectory_Activator {

	public static function activate() {
		self::add_permissions();
		self::flush_rules();
		self::add_activation_notice();
	}

	public static function add_permissions() {
		global $wp_roles;
		$wp_roles->add_cap( 'administrator', 'gravityforms_directory' );
		$wp_roles->add_cap( 'administrator', 'gravityforms_directory_uninstall' );
	}

	public static function flush_rules() {
		global $wp_rewrite;
		GFDirectory::add_rewrite();
		$wp_rewrite->flush_rules();
		return;
	}

	public static function add_activation_notice() {
		$message = sprintf(
			// Translators: placeholder: Link to Directory settings page.
			esc_html__(
				'Congratulations - the Gravity Forms Directory plugin has been installed. %1$sGo to the settings page%2$s to read usage instructions and configure the plugin default settings. %3$sGo to settings page%4$s',
				'gravity-forms-addons'
			),
			'<a href="' . esc_url_raw( admin_url( 'admin.php?page=gf_settings&addon=Directory&viewinstructions=true' ) ) . '">',
			'</a>',
			'<p class="submit"><a href="' . esc_url_raw( admin_url( 'admin.php?page=gf_settings&addon=Directory&viewinstructions=true' ) ) . '" class="button button-secondary">',
			'</a></p>'
		);
		set_transient( 'kws_gf_activation_notice', $message, 60 * 60 );
	}
}
