<?php

namespace TEC\Common\StellarWP\Installer;

use TEC\Common\StellarWP\Installer\Contracts\Handler;

class Button {
	/**
	 * Handler.
	 *
	 * @var Handler
	 */
	protected $handler;

	/**
	 * Selector.
	 *
	 * @var string
	 */
	protected $selector;

	/**
	 * Selector prefix.
	 *
	 * @var string
	 */
	protected $selector_prefix;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Handler $handler ) {
		$this->handler         = $handler;
		$hook_prefix           = Config::get_hook_prefix();
		$sanitized_slug        = sanitize_key( $this->handler->get_slug() );
		$this->selector_prefix = "stellarwp-installer-{$hook_prefix}__install-button";
		$this->selector        = "{$this->selector_prefix}--{$sanitized_slug}";
	}

	/**
	 * Gets the activated label.
	 *
	 * @return mixed|null
	 */
	protected function get_activated_label() {
		$hook_prefix = Config::get_hook_prefix();

		$text = __( 'Activated!', 'tribe-common' );

		/**
		 * Filters the activated label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The label.
		 * @param string $slug The slug of the resource.
		 * @param Handler $handler The handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/activated_label", $text, $this->handler->get_slug(), $this->handler );
	}

	/**
	 * Gets the activating label.
	 *
	 * @return mixed|null
	 */
	protected function get_activating_label() {
		$hook_prefix = Config::get_hook_prefix();

		$text = __( 'Activating...', 'tribe-common' );

		/**
		 * Filters the activating label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The label.
		 * @param string $slug The slug of the resource.
		 * @param Handler $handler The handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/activating_label", $text, $this->handler->get_slug(), $this->handler );
	}

	protected function get_classes() {
		$hook_prefix = Config::get_hook_prefix();

		$classes = [
			$this->selector_prefix,
			$this->selector,
		];

		/**
		 * Filters the button classes.
		 *
		 * @since 1.0.0
		 *
		 * @param array $classes The button classes.
		 * @param string $slug The slug of the resource.
		 * @param Handler $handler The handler.
		 */
		$classes = apply_filters( "stellarwp/installer/{$hook_prefix}/button_classes", $classes, $this->handler->get_slug(), $this->handler );

		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}

		return $classes;
	}

	/**
	 * Gets the button id.
	 *
	 * @return mixed|null
	 */
	protected function get_id() {
		$hook_prefix    = Config::get_hook_prefix();

		/**
		 * Filters the button id.
		 *
		 * @since 1.0.0
		 *
		 * @param string|null $id The button id.
		 * @param string $slug The slug of the resource.
		 * @param Handler $handler The handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/button_id", null, $this->handler->get_slug(), $this->handler );
	}

	/**
	 * Gets the installed label.
	 *
	 * @return mixed|null
	 */
	protected function get_installed_label() {
		$hook_prefix = Config::get_hook_prefix();

		$text = __( 'Installed!', 'tribe-common' );

		/**
		 * Filters the installing label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The label.
		 * @param string $slug The slug of the resource.
		 * @param Handler $handler The handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/installed_label", $text, $this->handler->get_slug(), $this->handler );
	}

	/**
	 * Gets the installing label.
	 *
	 * @return mixed|null
	 */
	protected function get_installing_label() {
		$hook_prefix = Config::get_hook_prefix();

		$text = __( 'Installing...', 'tribe-common' );

		/**
		 * Filters the installing label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The label.
		 * @param string $slug The slug of the resource.
		 * @param Handler $handler The handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/installing_label", $text, $this->handler->get_slug(), $this->handler );
	}

	/**
	 * Gets the selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_selector(): string {
		return $this->selector;
	}

	/**
	 * Renders an install/activate button.
	 *
	 * @param string      $request_action The action to perform.
	 * @param string|null $button_label   The button label.
	 * @param string|null $redirect_url   The redirect URL.
	 *
	 * @return void
	 */
	public function render( string $request_action, string $button_label = null, string $redirect_url = null ): void {
		$assets = Installer::get()->assets();
		if ( did_action( 'admin_enqueue_scripts' ) ) {
			$assets->enqueue_scripts();
		} elseif ( ! $assets->has_enqueued() ) {
			add_action( 'admin_enqueue_scripts', [ $assets, 'enqueue_scripts' ] );
		}

		if ( empty( $button_label ) ) {
			if ( $request_action === 'activate' ) {
				$button_label = sprintf( __( 'Activate %s', 'tribe-common' ), $this->handler->get_name() );
			} else {
				$button_label = sprintf( __( 'Install %s', 'tribe-common' ), $this->handler->get_name() );
			}
		}

		$button_id        = $this->get_id();
		$slug             = $this->handler->get_slug();
		$button_classes   = $this->get_classes();
		$ajax_nonce       = Installer::get()->get_nonce();
		$hook_prefix      = Config::get_hook_prefix();
		$action           = $this->handler->get_js_action();
		$activated_label  = $this->get_activated_label();
		$activating_label = $this->get_activating_label();
		$installed_label  = $this->get_installed_label();
		$installing_label = $this->get_installing_label();

		include dirname( __DIR__ ) . '/admin-views/button.php';
	}
}
