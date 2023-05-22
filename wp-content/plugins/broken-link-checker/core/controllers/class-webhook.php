<?php
/**
 * Webhooks Parent Class.
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
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Traits\Sanitize;

/**
 * Class Mailer
 *
 * @package WPMUDEV_BLC\Core\Controllers
 */
abstract class Webhook extends Base {
	/**
	 * Use Sanitize Trait.
	 *
	 * @since 2.0.0
	 */
	use Sanitize;

	/**
	 * The webhook title.
	 *
	 * @var string $webhook_title The webhook title.
	 */
	public $webhook_title = '';

	/**
	 * The webhook.
	 *
	 * @var string $webhook The webhook
	 */
	public $webhook = 'broken-link-checker';

	/**
	 * The webhook tag.
	 *
	 * @var string $webhook The webhook tag
	 */
	public $webhook_tag = '';

	/**
	 * Comments status.
	 *
	 * @var bool $disable_comments When set to true, comments will be disabled for current virtual page instance. By
	 * default it is true.
	 */
	public $disable_comments = true;

	/**
	 * Sets the DONOTCACHEPAGE constant to true so caching plugins do not cache the virtual pages.
	 *
	 * @var bool $do_not_cache When true, the const DONOTCACHEPAGE is set to  true.
	 */
	public $do_not_cache = true;

	/**
	 * Init Webhook
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

		add_action( 'init', array( $this, 'set_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'pass_tag_to_query_vars' ) );
		add_filter( 'parse_request', array( $this, 'parse_request' ) );
		add_action( 'wpmudev_blc_plugin_activated', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'flush_rewrite_rules' ) );
	}

	/**
	 * Prepares the class properties. Required in each child class to set $webhook and $webhook_tag properties.
	 *
	 * @return void
	 */
	abstract public function prepare_vars();

	/**
	 * Sets the webhook
	 *
	 * @return void
	 */
	public function set_endpoints() {
		// http://site.com/[WEBHOOK]/[TAG].
		$rewrite_args = apply_filters(
			'wpmudev_blc_rewrite_rule_args',
			array(
				'regex' => '^' . $this->webhook . '/([^/]*)/?',
				'query' => 'index.php?' . $this->webhook_tag . '=$matches[1]',
				'after' => 'top', // Accepts 'top' or 'bottom'.
			),
			$this
		);

		add_rewrite_rule( $rewrite_args['regex'], $rewrite_args['query'], $rewrite_args['after'] );
	}

	/**
	 * Passes the tag to query vars.
	 *
	 * @param array $query_vars The quey vars.
	 *
	 * @return mixed
	 */
	public function pass_tag_to_query_vars( array $query_vars = array() ) {
		$query_vars[] = $this->webhook_tag;

		return $query_vars;
	}

	/**
	 * Handles the request to webhook.
	 *
	 * @param object $wp The WP object.
	 *
	 * @return void
	 */
	public function parse_request( &$wp ) {

		if ( Utilities::plain_permalinks_mode() ) {
			$wp->query_vars = wp_parse_args( $this->sanitize_array( $_GET ), $wp->query_vars );
		}

		if ( array_key_exists( $this->webhook_tag, $wp->query_vars ) ) {
			$this->webhook_action( $wp );
			$this->disable_comments();
			$this->do_not_cache();
		}
	}

	/**
	 * Disables post comments.
	 *
	 * @return void
	 */
	public function disable_comments() {
		if ( $this->disable_comments ) {
			// Empties the comments array.
			add_filter( 'comments_array', '__return_empty_array' );

			// Closes comments.
			add_filter( 'comments_open', '__return_false' );

			// Closes pings.
			add_filter( 'pings_open', '__return_false' );
		}
	}

	/**
	 *
	 *
	 * @return void
	 */
	public function do_not_cache() {
		if ( $this->do_not_cache && ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', 1 );
		}
	}

	/**
	 * Executes the webhook action(s).
	 *
	 * @param object $wp The WP object.
	 *
	 * @return void
	 */
	abstract public function webhook_action( &$wp );

	/**
	 * Flushes rewrite rules. It is an expensive action, so it's good to run on plugin activation/deactivation.
	 *
	 * @return void
	 */
	public function flush_rewrite_rules() {
		flush_rewrite_rules( false );
	}

	/**
	 * Returns the webhook full url.
	 *
	 * @return string
	 */
	public function webhook_url() {
		return apply_filters(
			"blc_webhook_url_{$this->webhook_tag}",
			Utilities::plain_permalinks_mode() ? 
			add_query_arg( array(
				$this->webhook => $this->webhook,
				$this->webhook_tag => $this->webhook_tag,
			), site_url() ) :
			site_url( "{$this->webhook}/{$this->webhook_tag}" ),
			$this
		);
	}

	/**
	 * Checks if rewrite rule has been registered.
	 *
	 * @return bool
	 */
	public function rule_set() {
		$wp_rules = new \WPMUDEV_BLC\Core\Models\Option( array( 'name' => 'rewrite_rules' ) );

		return isset( $rules[ "({$this->webhook})/(\d*)$" ] );
	}

	/**
	 * Resets rewrite rule.
	 *
	 * @param bool $force Force reset
	 *
	 * @return void
	 */
	public function reset_rule( bool $force = false ) {
		// If rule is already set let's return, unless we need to force reset.
		if ( ! $force && $this->rule_set() ) {
			return;
		}

		$this->set_endpoints();
		$this->flush_rewrite_rules();
	}
}