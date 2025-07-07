<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Notice;

use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;

/**
 * An improved admin notice system for general messages.
 *
 * @see \TEC\Common\StellarWP\Uplink\Admin\Notice
 */
final class Notice_Handler {

	public const STORAGE_KEY = 'stellarwp_uplink_notices';

	/**
	 * Handles rendering notices.
	 *
	 * @var Notice_Controller
	 */
	private $controller;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Notice[]
	 */
	private $notices;

	public function __construct(
		Notice_Controller $controller,
		Storage $storage
	) {
		$this->controller = $controller;
		$this->storage    = $storage;
		$this->notices    = $this->all();
	}

	/**
	 * Add a notice to display.
	 *
	 * @param  Notice  $notice
	 *
	 * @return void
	 */
	public function add( Notice $notice ): void {
		$this->notices = array_merge( $this->all(), [ $notice ] );
		$this->save();
	}

	/**
	 * Display all notices and then clear them.
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function display(): void {
		if ( count( $this->notices ) <= 0 ) {
			return;
		}

		foreach ( $this->notices as $notice ) {
			$this->controller->render( $notice->toArray() );
		}

		$this->clear();
	}

	/**
	 * Get all notices.
	 *
	 * @return Notice[]
	 */
	private function all(): array {
		return array_filter( (array) $this->storage->get( self::STORAGE_KEY ) );
	}

	/**
	 * Save the existing state of notices.
	 *
	 * @return bool
	 */
	private function save(): bool {
		return $this->storage->set( self::STORAGE_KEY, $this->notices, 300 );
	}

	/**
	 * Clear all notices.
	 *
	 * @return bool
	 */
	private function clear(): bool {
		return $this->storage->delete( self::STORAGE_KEY );
	}

}
