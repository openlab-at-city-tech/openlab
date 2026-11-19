<?php
namespace AIOSEO\TableOfContents\Options;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Traits;

/**
 * Handles the main options.
 *
 * @since 1.0.0
 */
class Options {
	use Traits\Options;

	/**
	 * All the default options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $defaults = [
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		'general'  => [
			// Define options here.
		]
		// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	];

	/**
	 * The Construct method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $optionsName An array of options.
	 */
	public function __construct( $optionsName = 'aioseo_table_of_contents_options' ) {
		$this->optionsName = $optionsName;

		$this->init();

		add_action( 'shutdown', [ $this, 'save' ] );
	}

	/**
	 * Initializes the options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init() {
		$options = $this->getTableOfContentsDbOptions();

		aioseoTableOfContents()->core->optionsCache->setOptions( $this->optionsName, apply_filters( 'aioseo_table_of_contents_get_options', $options ) );
	}

	/**
	 * Get the DB options.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of options.
	 */
	public function getTableOfContentsDbOptions() {
		// Options from the DB.
		$dbOptions = $this->getDbOptions( $this->optionsName );

		// Refactor options.
		$this->defaultsMerged = array_replace_recursive( $this->defaults, $this->defaultsMerged );

		return array_replace_recursive(
			$this->defaultsMerged,
			$this->addValueToValuesArray( $this->defaultsMerged, $dbOptions )
		);
	}

	/**
	 * Sanitizes, then saves the options to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $newOptions An array of options to sanitize, then save.
	 * @return void
	 */
	public function sanitizeAndSave( $newOptions ) {
		$this->init();

		if ( ! is_array( $newOptions ) ) {
			return;
		}

		// First, recursively replace the new options into the cached state.
		// It's important we use the helper method since we want to replace populated arrays with empty ones if needed (when a setting was cleared out).
		$cachedOptions = aioseoTableOfContents()->core->optionsCache->getOptions( $this->optionsName );
		$dbOptions     = aioseoTableOfContents()->helpers->arrayReplaceRecursive(
			$cachedOptions,
			$this->addValueToValuesArray( $cachedOptions, $newOptions, [], true )
		);

		// Now, we must also intersect both arrays to delete any individual keys that were unset.
		// We must do this because, while arrayReplaceRecursive will update the values for keys or empty them out,
		// it will keys that aren't present in the replacement array unaffected in the target array.
		$dbOptions = aioseoTableOfContents()->helpers->arrayIntersectRecursive(
			$dbOptions,
			$this->addValueToValuesArray( $cachedOptions, $newOptions, [], true ),
			'value'
		);

		if ( isset( $newOptions['advanced']['excludeDomains'] ) ) {
			$dbOptions['advanced']['excludeDomains'] = preg_replace( '/\h/', "\n", (string) $newOptions['advanced']['excludeDomains'] );
		}

		// Update the cache state.
		aioseoTableOfContents()->core->optionsCache->setOptions( $this->optionsName, $dbOptions );

		// Finally, save the new values to the DB.
		$this->save( true );
	}
}