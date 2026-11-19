<?php
namespace AIOSEO\TableOfContents\Options;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Traits;

/**
 * Class that holds all internal options for Table of Contents Plus.
 *
 * @since 1.0.0
 */
class InternalOptions {
	use Traits\Options;

	/**
	 * Holds a list of all the possible deprecated options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $allDeprecatedOptions = [];

	/**
	 * All the default options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $defaults = [
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		'internal' => [
			'firstActivated'             => [ 'type' => 'number', 'default' => 0 ],
			'lastActiveVersion'          => [ 'type' => 'string', 'default' => '0.0' ],
			'lastSchemaVersion'          => [ 'type' => 'string', 'default' => '0.0' ],
			'migratedAppearanceDefaults' => [ 'type' => 'boolean', 'default' => false ],
		]
		// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	];

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $optionsName The options name.
	 */
	public function __construct( $optionsName = 'aioseo_table_of_contents_options_internal' ) {
		$this->optionsName = is_network_admin() ? $optionsName . '_network' : $optionsName;

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
		// Options from the DB.
		$dbOptions = $this->getDbOptions( $this->optionsName );

		// Refactor options.
		$this->defaultsMerged = array_replace_recursive( $this->defaults, $this->defaultsMerged );

		$options = array_replace_recursive(
			$this->defaultsMerged,
			$this->addValueToValuesArray( $this->defaultsMerged, $dbOptions )
		);

		aioseoTableOfContents()->core->optionsCache->setOptions( $this->optionsName, apply_filters( 'aioseo_table_of_contents_get_options_internal', $options ) );
	}

	/**
	 * Get all the deprecated options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function getAllDeprecatedOptions() {
		return $this->allDeprecatedOptions;
	}

	/**
	 * Sanitizes, then saves the options to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $options An array of options to sanitize, then save.
	 * @return void
	 */
	public function sanitizeAndSave( $options ) {
		if ( ! is_array( $options ) ) {
			return;
		}

		// Refactor options.
		$cachedOptions = aioseoTableOfContents()->core->optionsCache->getOptions( $this->optionsName );
		$dbOptions     = array_replace_recursive(
			$cachedOptions,
			$this->addValueToValuesArray( $cachedOptions, $options, [], true )
		);

		aioseoTableOfContents()->core->optionsCache->setOptions( $this->optionsName, $dbOptions );

		// Update values.
		$this->save( true );
	}
}