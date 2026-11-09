<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Admin class.
 */
class DLM_Admin_OptionsUpsells {

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'dlm_options_end', array( $this, 'add_upsells_products' ), 99 );
	}

	/**
	 * @return DLM_Pro\Extensions\Extensions|DLM_Extensions_Base
	 */
	private function get_extensions_provider() {
		return class_exists( 'DLM_Pro\Extensions\Extensions' )
			? DLM_Pro\Extensions\Extensions::get_instance()
			: DLM_Extensions_Base::get_instance();
	}

	public function add_upsells_products() {

		$provider = $this->get_extensions_provider();

		if ( $provider->is_upgradable_addon( 'dlm-email-lock' ) ) {
			$this->render_email_lock_upsell();
		}

		if ( $provider->is_upgradable_addon( 'dlm-captcha' ) ) {
			$this->render_captcha_upsell();
		}

		if ( $provider->is_upgradable_addon( 'dlm-mailchimp-lock' ) ) {
			$this->render_mailchimp_upsell();
		}

		if ( $provider->is_upgradable_addon( 'dlm-gravity-forms' ) ) {
			$this->render_gravity_forms_upsell();
		}

		if ( $provider->is_upgradable_addon( 'dlm-ninja-forms' ) ) {
			$this->render_ninja_forms_upsell();
		}

		if ( $provider->is_upgradable_addon( 'dlm-cf7-lock' ) ) {
			$this->render_cf7_forms_upsell();
		}

		if ( $provider->is_upgradable_addon( 'dlm-wpforms-lock' ) ) {
			$this->render_wp_forms_upsell();
		}
	}

	public function render_email_lock_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=Email%20Lock' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'Email Lock', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'Email locked downloads will only be available after user entered their email address.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}

	public function render_captcha_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=Captcha' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'Require Captcha', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'User is required to complete a reCAPTCHA before access is granted to the download.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}

	/**
	 * Render Mailchimp upsell
	 *
	 * @since 5.0.13
	 */
	public function render_mailchimp_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=mailchimp' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'Mailchimp lock', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'Require users to subscribe to a Mailchimp list before access is granted to the download.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}

	/**
	 * Render Mailchimp upsell
	 *
	 * @since 5.0.13
	 */
	public function render_gravity_forms_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=gravity_forms' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'Gravity Forms', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'Require users to complete a Gravity Forms form before access is granted to the download.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}

	/**
	 * Render Mailchimp upsell
	 *
	 * @since 5.0.13
	 */
	public function render_ninja_forms_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=ninja_forms' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'Ninja Forms', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'Require users to complete a Ninja Forms form before access is granted to the download.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}

	/**
	 * Render Mailchimp upsell
	 *
	 * @since 5.0.13
	 */
	public function render_cf7_forms_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=cf7' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'Contact Form 7', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'Require users to complete a Contact Form 7 form before access is granted to the download.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}

	/**
	 * Render Mailchimp upsell
	 *
	 * @since 5.0.13
	 */
	public function render_wp_forms_upsell() {
		?>
		<a href="<?php echo esc_url( 'https://www.download-monitor.com/pricing/?utm_source=plugin&utm_medium=extension-block&utm_campaign=wpforms' ); ?>" class="options_upsell_link" target="_blank">
			<p class="form-field form-field-checkbox not-active">
				<span class="dashicons dashicons-lock"></span>
				<span><?php esc_html_e( 'WPForms', 'download-monitor' ); ?><span class="dlm-upsell-badge">PAID</span></span>
				<span class="dlm-description"> <?php esc_html_e( 'Require users to complete a WPForms form before access is granted to the download.', 'download-monitor' ); ?></span>
			</p>
		</a>
		<?php
	}
}
