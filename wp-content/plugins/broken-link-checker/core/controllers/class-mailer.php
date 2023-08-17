<?php
/**
 * Email controller.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Controllers
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Models\Option;
use WPMUDEV_BLC\Core\Traits\Cron;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Mailer
 *
 * @package WPMUDEV_BLC\Core\Controllers
 */
abstract class Mailer extends Base {
	use Cron;

	/**
	 * Email headers.
	 *
	 * @var null|string|array
	 */
	public $email_headers = null;

	/**
	 * Email attachments.
	 *
	 * @var array
	 */
	public $email_attachments = array();

	/**
	 * Module name. This can be set in child class. It might be useful in hooks.
	 *
	 * @var string
	 */
	public $email_module_name = '';

	/**
	 * Define if scheduled events ( wp pseudo cron ) can be used for emails.
	 *
	 * @var bool
	 */
	protected $use_cron = false;

	/**
	 * WP Cron hook to execute when event is run.
	 *
	 * @var string
	 */
	protected $email_storage_option_name = 'blc_email_storage';

	/**
	 * The Option that stores email list.
	 *
	 * @var null|object
	 */
	protected $email_pool = null;

	/**
	 * The number of emails to send per cron event.
	 *
	 * @var int
	 */
	protected $emails_limit = 50;

	/**
	 * The subject to be used in emails.
	 *
	 * @var string
	 */
	protected $email_subject = '';

	/**
	 * Body variables map.
	 *
	 * @var array
	 */
	protected $body_variables = array();

	/**
	 * Init Mailer
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		/*
		 * Prepare email and cron vars.
		 */
		$this->prepare_vars();

		if ( self::$cron_disabled ) {
			$this->use_cron = false;
		}

		if ( $this->cron_status() ) {
			/*
			* setup_cron() is handled by Cron trait.
			*/
			$this->setup_cron();
		}
	}

	/**
	 * Returns the cron status for current email module.
	 *
	 * @return bool
	 */
	public function cron_status() {
		return apply_filters(
			'wpmudev_blc_use_email_cron',
			( ! self::$cron_disabled && $this->use_cron ),
			$this
		);
	}

	/**
	 * Sets email and cron vars. At least $this->cron_hook needs to be set in this method.
	 */
	abstract protected function prepare_vars();

	/**
	 * Scheduled event's action callback
	 *
	 * @return void
	 */
	public function process_scheduled_event() {
		$this->process_pool();
	}

	/**
	 * Goes through emails list and sends until limit is reached. Called from cron callback ( process_scheduled_event() ).
	 *
	 * @return void
	 */
	public function process_pool() {
		// Fetch all emails from pool in db (options table).
		$email_pool = $this->get_email_pool()->get();

		/*
		 * Send emails until limit is reached.
		 */
		if ( ! empty( $email_pool ) ) {
			for ( $count = 0; $count < $this->emails_limit; $count ++ ) {
				if ( empty( $email_pool ) ) {
					break;
				}

				$email_data           = array_shift( $email_pool );

				$this->process_send( $email_data );
			}

			$this->get_email_pool()->save( $email_pool, true );
		}
	}

	/**
	 * Returns the Option Model object.
	 *
	 * @return object|Option|null
	 */
	private function get_email_pool() {
		if ( is_null( $this->email_pool ) ) {
			$this->email_pool = new Option( array( 'name' => $this->email_storage_option_name ) );
		}

		return $this->email_pool;
	}

	/**
	 * Sets the body_variables. Ideal for overriding.
	 *
	 * @param array $email_args Email args.
	 *
	 * @return void
	 */
	public function set_mail_variables( array $email_args = array() ) {
		$this->body_variables = $email_args;
	}

	public function get_mail_variables() {
		return $this->body_variables;
	}

	/**
	 * Handles the email send.
	 *
	 * @param array $email_data The email information.
	 *
	 * @return void
	 */
	private function process_send( array $email_data = array() ) {
		if ( ! isset( $email_data['email'] ) ) {
			return;
		}

		$this->set_mail_variables( $email_data );

		$mail_args = apply_filters(
			'wpmudev_blc_mailer_email_args',
			array(
				'to'          => $email_data['email'],
				'subject'     => $this->email_subject,
				'message'     => $this->email_body_template( $this->get_mail_variables() ),
				'headers'     => $this->email_headers,
				'attachments' => $this->email_attachments,
			),
			$email_data
		);

		wp_mail(
			$mail_args['to'],
			$mail_args['subject'],
			$mail_args['message'],
			$mail_args['headers'],
			$mail_args['attachments']
		);
	}

	/**
	 * Sets the email body. Accepts an array of args to help for various cases.
	 *
	 * @param array $args Args.
	 *
	 * @return string
	 */
	protected function email_body_template( array $args = array() ) {
		return apply_filters(
			'wpmudev_blc_mailer_body_content',
			$this->load_body_template( $args ),
			$this->email_module_name,
			$args,
			$this
		);
	}

	/**
	 * Loads the template file that contains the email body.
	 */
	protected function load_body_template( array $args = array() ) {
		if ( empty( $args ) ) {
			$args = $this->body_variables ?? array();
		}

		$body = $this->get_body_from_template();

		return Utilities::replace_mapped_values( $body, $args, 'mailer' );
	}

	protected function get_body_from_template() {
		static $body = array();

		if ( ! isset( $body[ $this->email_module_name ] ) ) {
			/**
			 * Filter the email template fil path. When using this hook, the full file path should be provided along with the file name, not only the folder path.
			 *
			 * @param null|string.
			 * @param string The email module name.
			 * @param object The current object.
			 *
			 * @since 2.0.0
			 *
			 * @return null|string.
			 */
			$template_path = apply_filters( 'wpmudev_blc_mailer_body_template_file', null, $this->email_module_name, $this );

			if ( empty( $template_path ) ) {
				$upload_dir = wp_upload_dir();

				if ( isset( $upload_dir['basedir'] ) ) {
					$template_path = $upload_dir['basedir'] . "/broken-link-checker/email-templates/{$this->email_module_name}";
				}

				if ( ! $template_path || ! \file_exists( $template_path ) ) {
					$reflection    = new \ReflectionClass( $this );
					$template_path = dirname( $reflection->getFileName() ) . '/templates';
				}

				$template_path .= '/index.php';
			}

			if ( \file_exists( $template_path ) ) {
				\ob_start();
				include $template_path;

				$body[ $this->email_module_name ] = \ob_get_clean();
			} else {
				$body[ $this->email_module_name ] = '';
			}
		}

		return $body[ $this->email_module_name ];
	}

	/**
	 * Clears the email pool by emptying the db option.
	 *
	 * @return void
	 */
	public function clear_email_pool() {
		$this->get_email_pool()->reset_option();
	}

	/**
	 * Sends a single email or if cron is enabled it stores that in email pool db option.
	 *
	 * @param array $email_args Email args.
	 *
	 * @return void
	 */
	public function send( array $email_args = array() ) {
		$this->cron_status() ? $this->store_email( $email_args ) : $this->process_send( $email_args );
	}

	/**
	 * Stores an email instance in options table.
	 *
	 * @param array $email_args Emails args.
	 *
	 * @return void
	 */
	private function store_email( array $email_args = array() ) {
		$email_pool = $this->get_email_pool()->get();

		if ( ! is_array( $email_pool ) ) {
			$email_pool = array();
		}

		$email_pool[] = $email_args;

		$this->get_email_pool()->save( $email_pool );
	}

	/**
	 * Sends multiple emails or if cron is enabled it stores them in email pool db option.
	 *
	 * @param array $email_list Email list.
	 * @param bool $override Override existing values in db option.
	 *
	 * @return void
	 */
	public function send_multiple( array $email_list = array(), bool $override = false ) {
		if ( $this->cron_status() ) {
			$this->store_emails( $email_list, $override );
		} else {
			if ( ! empty( $email_list ) ) {
				$this->process_send_multiple( $email_list );
			}
		}
	}

	/**
	 * Stores given emails in db option.
	 *
	 * @param array $emails Emails to store.
	 * @param bool $override Override existing values in db option.
	 *
	 * @return void
	 */
	private function store_emails( array $emails = array(), bool $override = false ) {
		if ( ! empty( $emails ) ) {
			$this->get_email_pool()->save( $emails, $override );
		}
	}

	/**
	 * Sends the actual email for multiple email instances.
	 *
	 * @param array $email_args Email args.
	 *
	 * @return void
	 */
	private function process_send_multiple( array $email_args = array() ) {
		if ( ! empty( $email_args ) ) {
			foreach ( $email_args as $email_data ) {
				$this->process_send( $email_data );
			}
		}
	}
}
