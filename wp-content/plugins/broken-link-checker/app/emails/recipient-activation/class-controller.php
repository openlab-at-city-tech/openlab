<?php
/**
 * Controller for Recipient activation emails.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Emails\Recipient_Activation
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Emails\Recipient_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Mailer;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Emails\Recipient_Activation
 */
class Controller extends Mailer {
	/**
	 * Module name. It might be useful in hooks.
	 *
	 * @var string
	 */
	public $email_module_name = 'recipient_activation';

	/**
	 * WP Cron interval.
	 *
	 * @var boolean
	 */
	protected $cron_generate_interval = true;

	/**
	 * Sets required vars. In parent class it is an abstract method.
	 *
	 * @return void
	 */
	protected function prepare_vars() {
		$site_name           = get_bloginfo( 'name' );
		$site_email          = get_bloginfo( 'admin_email' );
		$this->email_headers = array(
			'Content-Type: text/html; charset=UTF-8',
			"From: {$site_name} <{$site_email}> \r\n",
		);

		$this->use_cron = false;
		$this->email_subject = esc_html__( 'Broken links reports activation', 'broken-link-checker' );
	}

	/**
	 * Sets up body variables to be mapped in email body.
	 *
	 * @param array $email_args
	 *
	 * @return void
	 */
	public function set_mail_variables( array $email_args = array() ) {
		$activation_link   = $email_args['activation_link'] ?? '';
		$cancellation_link = $email_args['cancellation_link'] ?? '';
		$name              = $email_args['name'] ?? '';
		$email              = $email_args['email'] ?? '';
		$site_name         = get_bloginfo( 'name' );

		$this->body_variables =
			apply_filters(
				'wpmudev_blc_scan_report_email_vars',
				array(
					//HEADER
					'{{HEADER_LOGO_SOURCE}}'       => esc_html( Model::header_logo() ),
					'{{TITLE}}'                    => esc_html( Model::header_title() ),
					'{{SITENAME}}'                 => $site_name,
					//BODY
					'{{GREETING}}'                 => esc_html__( 'Hi {{USERNAME}}', 'broken-link-checker' ),
					'{{USERNAME}}'                 => $name,
					'{{EMAIL_ADDRESS}}'            => $email,
					'{{SITE_URL}}'                  => site_url(),
					'{{CONFIRM_BTN_LABEL}}'        => esc_html__( 'Confirm Subscription', 'broken-link-checker' ),
					'{{ACTIVATION_LINK}}'          => $activation_link,
					// FOOTER PART
					'{{FOOTER_TITLE}}'             => esc_html__( 'Broken Link Checker', 'broken-link-checker' ),
					'{{FOOTER_COMPANY}}'           => 'WPMU DEV', //$site_name,
					'{{FOOTER_CONTENT}}'           => '',//View::instance()->get_footer_content(),
					'{{FOOTER_LOGO_SOURCE}}'       => Model::footer_logo(),
					'{{LINK_TO_WPMUDEV_HOME}}'     => esc_html__( 'Link to WPMU DEV Home page', 'broken-link-checker' ),
					'{{FOOTER_SLOGAN}}'            => esc_html__( 'Build A Better WordPress Business', 'broken-link-checker' ),
					'{{COMPANY_ADDRESS}}'          => esc_html__( 'INCSUB PO BOX 163, ALBERT PARK, VICTORIA.3206 AUSTRALIA', 'broken-link-checker' ),
					'{{COMPANY_TITLE}}'            => $site_name,
					'{{UNSUBSCRIBE}}'              => esc_html__( 'Unsubscribe', 'broken-link-checker' ),
					'{{UNSUBSCRIBE_LINK}}'         => $cancellation_link,
					'{{SOCIAL_LINKS}}'             => Model::get_social_links(),
				),
				$this
			);
	}

}
