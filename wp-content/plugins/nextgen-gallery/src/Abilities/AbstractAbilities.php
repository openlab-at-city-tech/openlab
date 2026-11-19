<?php
/**
 * NextGEN Gallery Abilities — shared base.
 *
 * Registers NextGEN gallery operations as WordPress Abilities so AI agents and MCP clients can
 * discover and run them through the Abilities API instead of the admin REST routes.
 *
 * Holds every definition, schema, execute callback and permission callback. The free plugin's
 * Abilities subclass is the one instance that registers; NextGEN Pro, Plus and Starter extend
 * this class and contribute through the `ngg_register_abilities` filter, so the abilities
 * register once, under one category, whichever editions are active.
 *
 * This class is a registration surface only. Gallery reads and writes go through
 * Imagely\NGG\Services\GalleryService, which wraps the Gallery data mapper the admin REST
 * controller writes through.
 *
 * Requires WordPress 6.9+. Nothing is hooked when wp_register_ability() is absent.
 *
 * @package NextGEN Gallery
 */

namespace Imagely\NGG\Abilities;

use Imagely\NGG\Services\GalleryService;
use Imagely\NGG\Util\Security;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared NextGEN Abilities API registration surface.
 */
abstract class AbstractAbilities {

	/**
	 * Ability namespace, also used as the category slug.
	 *
	 * Not the text domain ('nggallery'): ability names are a public contract that MCP clients
	 * are configured against, so they are named after the product.
	 *
	 * @var string
	 */
	const ABILITY_NAMESPACE = 'nextgen-gallery';

	/**
	 * Option holding the write opt-in. Absent means off.
	 *
	 * Kept out of ngg_options on purpose: the settings REST route saves that whole bag, so a
	 * stale copy held by an open settings screen would silently revert this switch.
	 *
	 * @var string
	 */
	const WRITE_OPTION = 'ngg_abilities_write_enabled';

	/**
	 * Read capability, the one the admin gallery list requires.
	 *
	 * @var string
	 */
	const CAP_READ = 'NextGEN Gallery overview';

	/**
	 * Capability the admin REST create route requires.
	 *
	 * @var string
	 */
	const CAP_CREATE = 'NextGEN Upload images';

	/**
	 * Capability the admin REST edit, delete and import routes require.
	 *
	 * @var string
	 */
	const CAP_MANAGE = 'NextGEN Manage gallery';

	/**
	 * Singleton instances, keyed by concrete class name.
	 *
	 * @var array<string, static>
	 */
	private static $instances = [];

	/**
	 * Gallery service.
	 *
	 * @var GalleryService|null
	 */
	private $galleries;

	/**
	 * Ability names wp_register_ability() rejected on this request.
	 *
	 * @var string[]
	 */
	protected $failed_registrations = [];

	/**
	 * Whether wp_register_ability_category() accepted the category. Null until it has run.
	 *
	 * @var bool|null
	 */
	protected $category_registered = null;

	/**
	 * Return the singleton for the concrete class.
	 *
	 * @return static
	 */
	public static function instance() {
		$class = static::class;

		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new static();
		}

		return self::$instances[ $class ];
	}

	/**
	 * Register WordPress hooks. No-op on WordPress < 6.9.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! $this->is_abilities_api_available() ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_category' ] );
		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	/**
	 * Whether the WordPress Abilities API is present.
	 *
	 * @return bool
	 */
	public function is_abilities_api_available(): bool {
		return function_exists( 'wp_register_ability' ) && function_exists( 'wp_register_ability_category' );
	}

	/**
	 * Register the nextgen-gallery ability category.
	 *
	 * @return void
	 */
	public function register_category() {
		$this->category_registered = null !== wp_register_ability_category(
			self::ABILITY_NAMESPACE,
			[
				'label'       => __( 'NextGEN Gallery', 'nggallery' ),
				'description' => __( 'Manage NextGEN galleries — list, retrieve, create, update and delete galleries, and add media library images to them.', 'nggallery' ),
			]
		);
	}

	/**
	 * Register all abilities.
	 *
	 * @return void
	 */
	public function register_abilities() {
		$this->failed_registrations = [];

		foreach ( $this->get_ability_definitions() as $name => $args ) {
			// wp_register_ability() returns null on a rejected registration (duplicate name,
			// malformed schema) after a _doing_it_wrong() that is invisible with WP_DEBUG off.
			// Recording it is what lets the settings screen name the failure.
			if ( null === wp_register_ability( $name, $args ) ) {
				$this->failed_registrations[] = $name;
			}
		}
	}

	/**
	 * Ability names that are not in the registry.
	 *
	 * The registry fires wp_abilities_api_init lazily, the first time anything asks it for an
	 * ability, so a screen that never asks would see no failures at all. Asking here primes it.
	 *
	 * @return string[]
	 */
	public function get_failed_registrations(): array {
		if ( ! $this->is_abilities_api_available() || ! did_action( 'init' ) || ! function_exists( 'wp_has_ability' ) ) {
			return $this->failed_registrations;
		}

		$failed = [];

		foreach ( array_keys( $this->get_ability_definitions() ) as $name ) {
			if ( ! wp_has_ability( $name ) ) {
				$failed[] = (string) $name;
			}
		}

		return $failed;
	}

	/**
	 * Whether the nextgen-gallery ability category is registered.
	 *
	 * Core rejects an ability whose category is missing, so a rejected category takes every
	 * ability down; reporting it separately names the root cause.
	 *
	 * @return bool
	 */
	public function is_category_registered(): bool {
		if ( ! $this->is_abilities_api_available() || ! did_action( 'init' ) || ! function_exists( 'wp_has_ability_category' ) ) {
			return false !== $this->category_registered;
		}

		// wp_has_ability() primes both registries, the category one first.
		wp_has_ability( self::ABILITY_NAMESPACE . '/list-galleries' );

		return wp_has_ability_category( self::ABILITY_NAMESPACE );
	}

	/**
	 * Return the ability definitions to register.
	 *
	 * @return array<string, array>
	 */
	public function get_ability_definitions(): array {
		return $this->get_common_ability_definitions();
	}

	/**
	 * The Phase 1 gallery abilities.
	 *
	 * @return array<string, array>
	 */
	public function get_common_ability_definitions(): array {
		$ns = self::ABILITY_NAMESPACE;

		return [
			$ns . '/list-galleries'          => $this->definition_list_galleries(),
			$ns . '/get-gallery'             => $this->definition_get_gallery(),
			$ns . '/describe-gallery-schema' => $this->definition_describe_gallery_schema(),
			$ns . '/create-gallery'          => $this->definition_create_gallery(),
			$ns . '/update-gallery'          => $this->definition_update_gallery(),
			$ns . '/delete-gallery'          => $this->definition_delete_gallery(),
			$ns . '/add-images'              => $this->definition_add_images(),
		];
	}

	// -------------------------------------------------------------------------
	// Write gate
	// -------------------------------------------------------------------------

	/**
	 * Whether the write abilities are enabled.
	 *
	 * Default is false: an agent cannot create, change or delete a gallery until a site
	 * administrator opts in under Settings → AI & MCP.
	 *
	 * @return bool
	 */
	public function write_enabled(): bool {
		$default = (bool) get_option( self::WRITE_OPTION, false );

		/**
		 * Filters whether the NextGEN write abilities are enabled.
		 *
		 * @param bool              $enabled Whether writes are enabled. Defaults to the
		 *                                   `ngg_abilities_write_enabled` option (false when absent).
		 * @param AbstractAbilities $handler The abilities handler.
		 */
		return (bool) apply_filters( 'ngg_abilities_allow_write', $default, $this );
	}

	/**
	 * Check the write gate.
	 *
	 * @return true|\WP_Error True when writes are allowed.
	 */
	protected function check_write_gate() {
		if ( ! $this->write_enabled() ) {
			return new \WP_Error(
				'ngg_abilities_writes_disabled',
				__( 'NextGEN gallery write abilities are disabled. Enable them under NextGEN Gallery → Settings → AI & MCP.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Check a NextGEN capability and translate a failure into a WP_Error.
	 *
	 * A bare `false` tells a calling agent nothing, so it retries; a WP_Error carries a code
	 * and a status it can act on. Unauthenticated callers get 401, as core's REST routes do.
	 *
	 * @param string $cap NextGEN capability name or alias, resolved by Security::get_mapped_cap().
	 * @return true|\WP_Error
	 */
	protected function check_cap( string $cap ) {
		if ( Security::is_allowed( $cap ) ) {
			return true;
		}

		return $this->forbidden();
	}

	/**
	 * Check that the current user may act on a gallery, under the admin REST ownership rule.
	 *
	 * A gallery that does not exist passes here and is reported as 404 by the execute callback,
	 * which is the order the admin REST routes answer in.
	 *
	 * @param int    $id       Gallery ID.
	 * @param string $required Capability every caller needs.
	 * @param string $unowned  Capability that permits acting on another author's gallery.
	 * @return true|\WP_Error
	 */
	protected function check_gallery_cap( int $id, string $required, string $unowned = 'nextgen_edit_gallery_unowned' ) {
		$allowed = $this->check_cap( $required );

		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$author = $this->galleries()->get_author( $id );

		if ( null === $author ) {
			return true;
		}

		return Security::can_manage_owned( $author, $required, $unowned ) ? true : $this->forbidden();
	}

	/**
	 * The error a failed capability check returns.
	 *
	 * @return \WP_Error
	 */
	protected function forbidden(): \WP_Error {
		$logged_in = is_user_logged_in();

		return new \WP_Error(
			$logged_in ? 'ngg_abilities_forbidden' : 'ngg_abilities_unauthenticated',
			$logged_in
				? __( 'You do not have permission to perform this action on NextGEN galleries.', 'nggallery' )
				: __( 'You must be logged in to use the NextGEN gallery abilities.', 'nggallery' ),
			[ 'status' => $logged_in ? 403 : 401 ]
		);
	}

	/**
	 * Require a gallery ID on abilities whose capability is per-gallery.
	 *
	 * @param mixed  $input Ability input.
	 * @param string $key   Input key holding the gallery ID.
	 * @return int|\WP_Error Gallery ID, or WP_Error when absent.
	 */
	protected function require_gallery_id( $input, string $key = 'id' ) {
		$input = $this->normalize_input( $input );
		$id    = isset( $input[ $key ] ) && is_numeric( $input[ $key ] ) ? (int) $input[ $key ] : 0;

		if ( $id <= 0 ) {
			return new \WP_Error(
				'ngg_abilities_missing_id',
				/* translators: %s: input property name. */
				sprintf( __( 'A valid gallery ID is required in "%s".', 'nggallery' ), $key ),
				[ 'status' => 400 ]
			);
		}

		return $id;
	}

	/**
	 * Run the write gate, then the per-gallery capability check.
	 *
	 * @param mixed  $input Ability input.
	 * @param string $key   Input key holding the gallery ID.
	 * @return true|\WP_Error
	 */
	protected function check_gallery_write_permission( $input, string $key ) {
		$gate = $this->check_write_gate();

		if ( is_wp_error( $gate ) ) {
			return $gate;
		}

		$id = $this->require_gallery_id( $input, $key );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		return $this->check_gallery_cap( $id, self::CAP_MANAGE, 'nextgen_edit_gallery_unowned' );
	}

	// -------------------------------------------------------------------------
	// Ability definitions
	// -------------------------------------------------------------------------

	/**
	 * Definition: list-galleries.
	 *
	 * @return array
	 */
	protected function definition_list_galleries(): array {
		return [
			'label'               => __( 'List Galleries', 'nggallery' ),
			'description'         => __( 'Returns a paginated list of NextGEN galleries. Accounts that may not manage other authors\' galleries see only their own.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_list_galleries' ],
			'permission_callback' => [ $this, 'can_list_galleries' ],
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'default'              => [],
				'properties'           => [
					'per_page' => [
						'type'        => 'integer',
						'description' => __( 'Galleries per page (1–100).', 'nggallery' ),
						'default'     => 20,
						'minimum'     => 1,
						'maximum'     => 100,
					],
					'page'     => [
						'type'        => 'integer',
						'description' => __( 'Page number.', 'nggallery' ),
						'default'     => 1,
						'minimum'     => 1,
					],
					'search'   => [
						'type'        => 'string',
						'description' => __( 'Search galleries by title. A number also matches the gallery ID.', 'nggallery' ),
					],
				],
			],
			'output_schema'       => [
				'type'       => 'object',
				'properties' => [
					'galleries'   => [
						'type'  => 'array',
						'items' => $this->gallery_schema(),
					],
					'total'       => [
						'type'        => 'integer',
						'description' => __( 'Total galleries matching the query.', 'nggallery' ),
					],
					'page'        => [ 'type' => 'integer' ],
					'per_page'    => [ 'type' => 'integer' ],
					'total_pages' => [ 'type' => 'integer' ],
				],
			],
			'meta'                => $this->read_meta(),
		];
	}

	/**
	 * Definition: get-gallery.
	 *
	 * @return array
	 */
	protected function definition_get_gallery(): array {
		return [
			'label'               => __( 'Get Gallery', 'nggallery' ),
			'description'         => __( 'Retrieves a single NextGEN gallery by ID.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_get_gallery' ],
			'permission_callback' => [ $this, 'can_get_gallery' ],
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'required'             => [ 'id' ],
				'properties'           => [
					'id' => [
						'type'        => 'integer',
						'description' => __( 'Gallery ID.', 'nggallery' ),
						'minimum'     => 1,
					],
				],
			],
			'output_schema'       => $this->gallery_schema(),
			'meta'                => $this->read_meta(),
		];
	}

	/**
	 * Definition: describe-gallery-schema.
	 *
	 * Lets an agent ask what is writable, and which display types exist, instead of guessing.
	 * Hidden from MCP while writes are off, so agents do not see an editing surface they cannot
	 * act on. REST exposure is unaffected.
	 *
	 * @return array
	 */
	protected function definition_describe_gallery_schema(): array {
		return [
			'label'               => __( 'Describe Gallery Schema', 'nggallery' ),
			'description'         => __( 'Describes which gallery fields can be created or edited, the display types available on this site, and whether writes are currently enabled.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_describe_gallery_schema' ],
			'permission_callback' => [ $this, 'can_describe_gallery_schema' ],
			// No `properties` key: an empty PHP array serialises to JSON `[]`, not `{}`, which
			// stricter validators reject as a malformed schema.
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'default'              => [],
			],
			'output_schema'       => [
				'type'       => 'object',
				'properties' => [
					'writes_enabled'  => [
						'type'        => 'boolean',
						'description' => __( 'Whether the write abilities will currently execute.', 'nggallery' ),
					],
					'edition'         => $this->edition_schema(),
					'display_types'   => [
						'type'  => 'array',
						'items' => [
							'type'       => 'object',
							'properties' => [
								'name'  => [ 'type' => 'string' ],
								'title' => [ 'type' => 'string' ],
							],
						],
					],
					'writable_fields' => [
						'type'  => 'array',
						'items' => [
							'type'       => 'object',
							'properties' => [
								'name'      => [ 'type' => 'string' ],
								'type'      => [ 'type' => 'string' ],
								'abilities' => [
									'type'  => 'array',
									'items' => [ 'type' => 'string' ],
								],
							],
						],
					],
				],
			],
			'meta'                => $this->read_meta( [ 'mcp_public' => $this->write_enabled() ] ),
		];
	}

	/**
	 * Definition: create-gallery.
	 *
	 * @return array
	 */
	protected function definition_create_gallery(): array {
		return [
			'label'               => __( 'Create Gallery', 'nggallery' ),
			'description'         => __( 'Creates a new, empty NextGEN gallery. Add images with add-images.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_create_gallery' ],
			'permission_callback' => [ $this, 'can_create_gallery' ],
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'required'             => [ 'title' ],
				'properties'           => [
					'title'       => [
						'type'        => 'string',
						'description' => __( 'Gallery title.', 'nggallery' ),
						'minLength'   => 1,
						'maxLength'   => 255,
					],
					'description' => [
						'type'        => 'string',
						'description' => __( 'Gallery description.', 'nggallery' ),
					],
				],
			],
			'output_schema'       => $this->gallery_schema(),
			'meta'                => $this->write_meta( [ 'idempotent' => false ] ),
		];
	}

	/**
	 * Definition: update-gallery.
	 *
	 * @return array
	 */
	protected function definition_update_gallery(): array {
		return [
			'label'               => __( 'Update Gallery', 'nggallery' ),
			'description'         => __( 'Updates the title, description or display type of an existing NextGEN gallery. Call describe-gallery-schema for the display type names.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_update_gallery' ],
			'permission_callback' => [ $this, 'can_update_gallery' ],
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'required'             => [ 'id' ],
				'properties'           => [
					'id'           => [
						'type'        => 'integer',
						'description' => __( 'Gallery ID.', 'nggallery' ),
						'minimum'     => 1,
					],
					'title'        => [
						'type'        => 'string',
						'description' => __( 'New gallery title. Changing it also changes the gallery slug.', 'nggallery' ),
						'minLength'   => 1,
						'maxLength'   => 255,
					],
					'description'  => [
						'type'        => 'string',
						'description' => __( 'New gallery description.', 'nggallery' ),
					],
					'display_type' => [
						'type'        => 'string',
						'description' => __( 'Display type name, as listed by describe-gallery-schema.', 'nggallery' ),
						'minLength'   => 1,
					],
				],
			],
			'output_schema'       => $this->gallery_schema(),
			'meta'                => $this->write_meta(),
		];
	}

	/**
	 * Definition: delete-gallery.
	 *
	 * NextGEN galleries have no trash, so deleting is always permanent and the caller must say
	 * so with `force`. That keeps an agent from deleting by default, the way trashing does on
	 * post-backed galleries.
	 *
	 * @return array
	 */
	protected function definition_delete_gallery(): array {
		return [
			'label'               => __( 'Delete Gallery', 'nggallery' ),
			'description'         => __( 'Permanently deletes a NextGEN gallery and its images, exactly as deleting it in the admin does. NextGEN galleries have no trash, so `force` must be true.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_delete_gallery' ],
			'permission_callback' => [ $this, 'can_delete_gallery' ],
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'required'             => [ 'id' ],
				'properties'           => [
					'id'    => [
						'type'        => 'integer',
						'description' => __( 'Gallery ID to delete.', 'nggallery' ),
						'minimum'     => 1,
					],
					'force' => [
						'type'        => 'boolean',
						'description' => __( 'Must be true: confirms the permanent deletion.', 'nggallery' ),
						'default'     => false,
					],
				],
			],
			'output_schema'       => [
				'type'       => 'object',
				'properties' => [
					'deleted' => [ 'type' => 'boolean' ],
					'id'      => [ 'type' => 'integer' ],
				],
			],
			'meta'                => $this->write_meta( [ 'destructive' => true ] ),
		];
	}

	/**
	 * Definition: add-images.
	 *
	 * @return array
	 */
	protected function definition_add_images(): array {
		return [
			'label'               => __( 'Add Images to Gallery', 'nggallery' ),
			'description'         => __( 'Imports media library images into an existing NextGEN gallery, the same way the admin "Import from Media Library" does. Each call imports a fresh copy, so an image added twice appears twice. A call stops when its time budget is spent and lists the attachments it did not reach in "remaining": send those in the next call, not the whole list again.', 'nggallery' ),
			'category'            => self::ABILITY_NAMESPACE,
			'execute_callback'    => [ $this, 'execute_add_images' ],
			'permission_callback' => [ $this, 'can_add_images' ],
			'input_schema'        => [
				'type'                 => 'object',
				'additionalProperties' => false,
				'required'             => [ 'gallery_id', 'attachment_ids' ],
				'properties'           => [
					'gallery_id'     => [
						'type'        => 'integer',
						'description' => __( 'Gallery ID to add images to.', 'nggallery' ),
						'minimum'     => 1,
					],
					'attachment_ids' => [
						'type'        => 'array',
						'description' => __( 'Media library attachment IDs to import.', 'nggallery' ),
						'items'       => [
							'type'    => 'integer',
							'minimum' => 1,
						],
						'minItems'    => 1,
						'maxItems'    => 20,
					],
				],
			],
			'output_schema'       => [
				'type'       => 'object',
				'properties' => [
					'gallery'   => $this->gallery_schema(),
					'added'     => [
						'type'        => 'array',
						'description' => __( 'Attachment IDs that were imported.', 'nggallery' ),
						'items'       => [ 'type' => 'integer' ],
					],
					'image_ids' => [
						'type'        => 'array',
						'description' => __( 'NextGEN image IDs created by the import.', 'nggallery' ),
						'items'       => [ 'type' => 'integer' ],
					],
					'skipped'   => [
						'type'        => 'array',
						'description' => __( 'Attachments that were not imported, each with the reason.', 'nggallery' ),
						'items'       => [
							'type'       => 'object',
							'properties' => [
								'id'     => [ 'type' => 'integer' ],
								'reason' => [ 'type' => 'string' ],
							],
						],
					],
					'remaining' => [
						'type'        => 'array',
						'description' => __( 'Attachment IDs this call did not reach before its time budget ran out. Send them in another call.', 'nggallery' ),
						'items'       => [ 'type' => 'integer' ],
					],
					'warnings'  => [
						'type'        => 'array',
						'description' => __( 'Attachments that were imported but whose import stopped early, each with the reason. Their images exist; do not import them again.', 'nggallery' ),
						'items'       => [
							'type'       => 'object',
							'properties' => [
								'id'     => [ 'type' => 'integer' ],
								'reason' => [ 'type' => 'string' ],
							],
						],
					],
				],
			],
			'meta'                => $this->write_meta( [ 'idempotent' => false ] ),
		];
	}

	// -------------------------------------------------------------------------
	// Execute callbacks — thin delegates to GalleryService.
	// -------------------------------------------------------------------------

	/**
	 * Execute: list-galleries.
	 *
	 * @param mixed $input Validated ability input.
	 * @return array
	 */
	public function execute_list_galleries( $input = null ) {
		return $this->galleries()->list_galleries( $this->normalize_input( $input ) );
	}

	/**
	 * Execute: get-gallery.
	 *
	 * @param mixed $input Validated ability input.
	 * @return array|\WP_Error
	 */
	public function execute_get_gallery( $input = null ) {
		$id = $this->require_gallery_id( $input, 'id' );

		return is_wp_error( $id ) ? $id : $this->galleries()->get( $id );
	}

	/**
	 * Execute: describe-gallery-schema.
	 *
	 * @param mixed $input Ability input (unused).
	 * @return array
	 */
	public function execute_describe_gallery_schema( $input = null ) {
		$ns = self::ABILITY_NAMESPACE;

		// Derived from what is actually registered, so a field is never advertised through an
		// ability another edition or a filter removed.
		$registered = array_keys( $this->get_ability_definitions() );
		$candidates = [
			[
				'name'      => 'title',
				'type'      => 'string',
				'abilities' => [ $ns . '/create-gallery', $ns . '/update-gallery' ],
			],
			[
				'name'      => 'description',
				'type'      => 'string',
				'abilities' => [ $ns . '/create-gallery', $ns . '/update-gallery' ],
			],
			[
				'name'      => 'display_type',
				'type'      => 'string',
				'abilities' => [ $ns . '/update-gallery' ],
			],
			[
				'name'      => 'attachment_ids',
				'type'      => 'array',
				'abilities' => [ $ns . '/add-images' ],
			],
		];

		$writable_fields = [];

		foreach ( $candidates as $field ) {
			$available = array_values( array_intersect( $field['abilities'], $registered ) );

			if ( empty( $available ) ) {
				continue;
			}

			$field['abilities'] = $available;
			$writable_fields[]  = $field;
		}

		return [
			'writes_enabled'  => $this->write_enabled(),
			'edition'         => $this->get_edition(),
			'display_types'   => $this->galleries()->get_display_types(),
			'writable_fields' => $writable_fields,
		];
	}

	/**
	 * Execute: create-gallery.
	 *
	 * @param mixed $input Validated ability input.
	 * @return array|\WP_Error
	 */
	public function execute_create_gallery( $input = null ) {
		$input = $this->normalize_input( $input );

		return $this->galleries()->create( $this->gallery_fields( $input ) );
	}

	/**
	 * Execute: update-gallery.
	 *
	 * @param mixed $input Validated ability input.
	 * @return array|\WP_Error
	 */
	public function execute_update_gallery( $input = null ) {
		$id = $this->require_gallery_id( $input, 'id' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		return $this->galleries()->update( $id, $this->gallery_fields( $this->normalize_input( $input ) ) );
	}

	/**
	 * Execute: delete-gallery.
	 *
	 * @param mixed $input Validated ability input.
	 * @return array|\WP_Error
	 */
	public function execute_delete_gallery( $input = null ) {
		$id = $this->require_gallery_id( $input, 'id' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$input = $this->normalize_input( $input );

		// Before WP 7.1 the REST run controller passes DELETE query input through unsanitized,
		// so `force` can arrive as the string 'false', which a plain cast reads as true.
		if ( ! rest_sanitize_boolean( $input['force'] ?? false ) ) {
			return new \WP_Error(
				'ngg_abilities_force_required',
				__( 'NextGEN galleries have no trash, so deleting one is permanent. Repeat the call with "force": true to delete it.', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		return $this->galleries()->delete( $id );
	}

	/**
	 * Execute: add-images.
	 *
	 * @param mixed $input Validated ability input.
	 * @return array|\WP_Error
	 */
	public function execute_add_images( $input = null ) {
		$id = $this->require_gallery_id( $input, 'gallery_id' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$input = $this->normalize_input( $input );

		return $this->galleries()->add_images( $id, (array) ( $input['attachment_ids'] ?? [] ) );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission: list-galleries.
	 *
	 * @param mixed $input Ability input (unused).
	 * @return true|\WP_Error
	 */
	public function can_list_galleries( $input = null ) {
		return $this->check_cap( self::CAP_READ );
	}

	/**
	 * Permission: get-gallery.
	 *
	 * The ID is derived exactly as execute_get_gallery() derives it, so the ownership check
	 * always runs against the gallery that is returned.
	 *
	 * @param mixed $input Ability input.
	 * @return true|\WP_Error
	 */
	public function can_get_gallery( $input = null ) {
		$id = $this->require_gallery_id( $input, 'id' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		// The rule GalleryREST::get_gallery() applies: the overview capability, plus the
		// cross-author one for someone else's gallery.
		return $this->check_gallery_cap( $id, self::CAP_READ, 'nextgen_edit_gallery_unowned' );
	}

	/**
	 * Permission: describe-gallery-schema.
	 *
	 * @param mixed $input Ability input (unused).
	 * @return true|\WP_Error
	 */
	public function can_describe_gallery_schema( $input = null ) {
		return $this->check_cap( self::CAP_READ );
	}

	/**
	 * Permission: create-gallery.
	 *
	 * @param mixed $input Ability input (unused).
	 * @return true|\WP_Error
	 */
	public function can_create_gallery( $input = null ) {
		$gate = $this->check_write_gate();

		return is_wp_error( $gate ) ? $gate : $this->check_cap( self::CAP_CREATE );
	}

	/**
	 * Permission: update-gallery.
	 *
	 * @param mixed $input Ability input.
	 * @return true|\WP_Error
	 */
	public function can_update_gallery( $input = null ) {
		return $this->check_gallery_write_permission( $input, 'id' );
	}

	/**
	 * Permission: delete-gallery.
	 *
	 * @param mixed $input Ability input.
	 * @return true|\WP_Error
	 */
	public function can_delete_gallery( $input = null ) {
		return $this->check_gallery_write_permission( $input, 'id' );
	}

	/**
	 * Permission: add-images.
	 *
	 * @param mixed $input Ability input.
	 * @return true|\WP_Error
	 */
	public function can_add_images( $input = null ) {
		return $this->check_gallery_write_permission( $input, 'gallery_id' );
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Map ability input names onto gallery columns, keeping only the ones supplied.
	 *
	 * @param array $input Normalised ability input.
	 * @return array<string, mixed>
	 */
	protected function gallery_fields( array $input ): array {
		$map    = [
			'title'        => 'title',
			'description'  => 'galdesc',
			'display_type' => 'display_type',
		];
		$fields = [];

		foreach ( $map as $key => $column ) {
			if ( array_key_exists( $key, $input ) ) {
				$fields[ $column ] = $input[ $key ];
			}
		}

		return $fields;
	}

	/**
	 * The edition block describe-gallery-schema reports.
	 *
	 * The free plugin reports itself. A Pro edition's subclass overrides this to report its
	 * edition and license state; that is diagnostics only, and no ability is gated on it.
	 *
	 * @return array{slug: string, label: string}
	 */
	public function get_edition(): array {
		return [
			'slug'  => 'free',
			'label' => 'NextGEN Gallery',
		];
	}

	/**
	 * JSON Schema for the edition block.
	 *
	 * @return array
	 */
	protected function edition_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'slug'    => [
					'type'        => 'string',
					'description' => __( 'Active edition: free, starter, plus or pro.', 'nggallery' ),
				],
				'label'   => [ 'type' => 'string' ],
				'license' => [
					'type'        => 'object',
					'description' => __( 'License state, reported by the paid editions for diagnostics. Nothing is gated on it.', 'nggallery' ),
					'properties'  => [
						'level'    => [ 'type' => 'string' ],
						'licensed' => [ 'type' => 'boolean' ],
					],
				],
			],
		];
	}

	/**
	 * Normalise ability input to an array.
	 *
	 * @param mixed $input Input data (array, object or null).
	 * @return array
	 */
	protected function normalize_input( $input ): array {
		if ( is_array( $input ) ) {
			return $input;
		}

		if ( is_object( $input ) ) {
			return (array) $input;
		}

		return [];
	}

	/**
	 * Return the gallery service.
	 *
	 * @return GalleryService
	 */
	protected function galleries(): GalleryService {
		if ( ! $this->galleries instanceof GalleryService ) {
			$this->galleries = new GalleryService();
		}

		return $this->galleries;
	}

	/**
	 * Meta block for a read-only ability.
	 *
	 * @param array $overrides Optional. `mcp_public` to override MCP exposure.
	 * @return array
	 */
	protected function read_meta( array $overrides = [] ): array {
		return [
			'annotations'  => [
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			],
			'show_in_rest' => true,
			'mcp'          => [
				'public' => array_key_exists( 'mcp_public', $overrides ) ? (bool) $overrides['mcp_public'] : true,
			],
		];
	}

	/**
	 * Meta block for a write ability.
	 *
	 * Writes are hidden from MCP while the write gate is off, so an agent never sees an
	 * operation it cannot perform. REST exposure is unaffected and stays gated by the
	 * permission callback.
	 *
	 * @param array $overrides Optional. `destructive` and/or `idempotent`.
	 * @return array
	 */
	protected function write_meta( array $overrides = [] ): array {
		return [
			'annotations'  => [
				'readonly'    => false,
				'destructive' => ! empty( $overrides['destructive'] ),
				'idempotent'  => array_key_exists( 'idempotent', $overrides ) ? (bool) $overrides['idempotent'] : true,
			],
			'show_in_rest' => true,
			'mcp'          => [
				'public' => $this->write_enabled(),
			],
		];
	}

	/**
	 * JSON Schema fragment describing a single gallery.
	 *
	 * @return array
	 */
	protected function gallery_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'id'               => [
					'type'        => 'integer',
					'description' => __( 'Gallery ID.', 'nggallery' ),
				],
				'title'            => [ 'type' => 'string' ],
				'slug'             => [ 'type' => 'string' ],
				'description'      => [ 'type' => 'string' ],
				'display_type'     => [
					'type'        => 'string',
					'description' => __( 'Display type name.', 'nggallery' ),
				],
				'author'           => [
					'type'        => 'integer',
					'description' => __( 'User ID of the gallery author.', 'nggallery' ),
				],
				'image_count'      => [ 'type' => 'integer' ],
				'preview_image_id' => [
					'type'        => 'integer',
					'description' => __( 'NextGEN image ID used as the gallery preview, 0 when none.', 'nggallery' ),
				],
				'page_id'          => [
					'type'        => 'integer',
					'description' => __( 'Page linked to the gallery, 0 when none.', 'nggallery' ),
				],
				'created'          => [
					'type'        => [ 'string', 'null' ],
					'format'      => 'date-time',
					'description' => __( 'Creation date (UTC, RFC 3339). Null for galleries created before NextGEN recorded it.', 'nggallery' ),
				],
				'modified'         => [
					'type'        => [ 'string', 'null' ],
					'format'      => 'date-time',
					'description' => __( 'Last modified date (UTC, RFC 3339).', 'nggallery' ),
				],
				'edit_url'         => [
					'type'        => 'string',
					'description' => __( 'Admin URL for editing the gallery.', 'nggallery' ),
				],
			],
		];
	}
}
