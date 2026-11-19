<?php
namespace AIOSEO\TableOfContents\Main;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin (de)activation.
 *
 * @since 1.0.0
 */
class Activate {
	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		register_activation_hook( AIOSEO_TABLE_OF_CONTENTS_FILE, [ $this, 'activate' ] );
		register_deactivation_hook( AIOSEO_TABLE_OF_CONTENTS_FILE, [ $this, 'deactivate' ] );
	}

	/**
	 * Runs on activation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate() {
		aioseoTableOfContents()->access->addCapabilities();

		// Set the activation timestamps.
		$time = time();
		aioseoTableOfContents()->internalOptions->internal->activated = $time;

		if ( ! aioseoTableOfContents()->internalOptions->internal->firstActivated ) {
			aioseoTableOfContents()->internalOptions->internal->firstActivated = $time;
		}

		aioseoTableOfContents()->core->cache->clear();
	}

	/**
	 * Runs on deactivation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function deactivate() {
		aioseoTableOfContents()->access->removeCapabilities();
	}
}