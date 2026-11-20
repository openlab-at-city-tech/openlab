<?php
/**
 * Gallery service used by the NextGEN abilities.
 *
 * Every gallery write goes through the Gallery data mapper, the writer the admin REST
 * controller (GalleryREST) uses, with the same mass-assignment allow-list and the same
 * sanitisation. Media library imports go through ImageREST::import_media_library(), the
 * admin "Import from Media Library" handler. Nothing here writes a gallery row or an image any
 * other way.
 *
 * Capability checks are the caller's job: the abilities' permission callbacks run first.
 *
 * @package NextGEN Gallery
 */

namespace Imagely\NGG\Services;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\REST\DataMappers\ImageREST;
use Imagely\NGG\Util\MassAssignment;
use Imagely\NGG\Util\Security;
use Imagely\NGG\Util\Transient;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes NextGEN galleries for the abilities.
 */
class GalleryService {

	/**
	 * Gallery columns the abilities may write.
	 *
	 * @var string[]
	 */
	const WRITABLE_COLUMNS = [ 'title', 'galdesc', 'display_type' ];

	/**
	 * List galleries the current user may see.
	 *
	 * Callers without the cross-author capability see only their own galleries, the scope the
	 * admin gallery list applies (GalleryREST::get_galleries()).
	 *
	 * @param array $args {
	 *     Optional.
	 *
	 *     @type int    $per_page Galleries per page, 1–100. Default 20.
	 *     @type int    $page     Page number. Default 1.
	 *     @type string $search   Title search; a number also matches the ID.
	 * }
	 * @return array
	 */
	public function list_galleries( array $args = [] ): array {
		global $wpdb;

		$per_page = max( 1, min( 100, (int) ( $args['per_page'] ?? 20 ) ) );
		$page     = max( 1, (int) ( $args['page'] ?? 1 ) );
		$search   = isset( $args['search'] ) && is_string( $args['search'] ) ? trim( $args['search'] ) : '';

		$where  = [];
		$params = [];

		if ( ! Security::is_allowed( 'nextgen_edit_gallery_unowned' ) ) {
			$where[]  = 'author = %d';
			$params[] = (int) get_current_user_id();
		}

		if ( '' !== $search ) {
			$like = '%' . $wpdb->esc_like( $search ) . '%';

			if ( ctype_digit( $search ) ) {
				$where[]  = '( title LIKE %s OR gid = %d )';
				$params[] = $like;
				$params[] = (int) $search;
			} else {
				$where[]  = 'title LIKE %s';
				$params[] = $like;
			}
		}

		$table     = $wpdb->nggallery;
		$where_sql = $where ? ' WHERE ' . implode( ' AND ', $where ) : '';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- $table is the plugin's own table name and $where_sql holds only the placeholders built above, whose values are $params.
		$count_sql = "SELECT COUNT(*) FROM {$table}{$where_sql}";
		$total     = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT gid FROM {$table}{$where_sql} ORDER BY gid DESC LIMIT %d OFFSET %d",
				array_merge( $params, [ $per_page, ( $page - 1 ) * $per_page ] )
			)
		);
		// phpcs:enable

		$mapper    = GalleryMapper::get_instance();
		$counts    = $this->get_image_counts( array_map( 'intval', (array) $ids ) );
		$galleries = [];

		foreach ( (array) $ids as $id ) {
			$gallery = $mapper->find( (int) $id );

			if ( $gallery ) {
				$galleries[] = $this->format( $gallery, $counts[ (int) $id ] ?? 0 );
			}
		}

		return [
			'galleries'   => $galleries,
			'total'       => $total,
			'page'        => $page,
			'per_page'    => $per_page,
			'total_pages' => (int) ceil( $total / $per_page ),
		];
	}

	/**
	 * Get one gallery.
	 *
	 * @param int $id Gallery ID.
	 * @return array|\WP_Error
	 */
	public function get( int $id ) {
		$gallery = $this->find( $id );

		return is_wp_error( $gallery ) ? $gallery : $this->format( $gallery );
	}

	/**
	 * Author of a gallery, or null when it does not exist.
	 *
	 * @param int $id Gallery ID.
	 * @return int|null
	 */
	public function get_author( int $id ) {
		$gallery = $id > 0 ? GalleryMapper::get_instance()->find( $id ) : null;

		return $gallery ? (int) $gallery->author : null;
	}

	/**
	 * Create a gallery owned by the current user.
	 *
	 * @param array $fields Column => value, from WRITABLE_COLUMNS. `title` is required.
	 * @return array|\WP_Error
	 */
	public function create( array $fields ) {
		$fields = $this->prepare_fields( $fields );

		if ( is_wp_error( $fields ) ) {
			return $fields;
		}

		if ( ! isset( $fields['title'] ) ) {
			return $this->invalid_title();
		}

		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->create( $fields );

		// Server-derived, as GalleryREST::create_gallery() does: the mapper builds the folder
		// from `name`, and the owner is whoever made the call.
		$gallery->name   = sanitize_title( $fields['title'] );
		$gallery->author = get_current_user_id();

		return $this->save( $gallery, 'ngg_abilities_create_failed' );
	}

	/**
	 * Update a gallery.
	 *
	 * @param int   $id     Gallery ID.
	 * @param array $fields Column => value, from WRITABLE_COLUMNS.
	 * @return array|\WP_Error
	 */
	public function update( int $id, array $fields ) {
		$gallery = $this->find( $id );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		$fields = $this->prepare_fields( $fields );

		if ( is_wp_error( $fields ) ) {
			return $fields;
		}

		if ( empty( $fields ) ) {
			return new \WP_Error(
				'ngg_abilities_nothing_to_update',
				__( 'Nothing to update: supply at least one of title, description or display_type.', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		foreach ( $fields as $column => $value ) {
			$gallery->$column = $value;
		}

		return $this->save( $gallery, 'ngg_abilities_update_failed' );
	}

	/**
	 * Permanently delete a gallery, its images and, when the deleteImg setting is on, its files.
	 *
	 * Same call GalleryREST::delete_gallery() makes.
	 *
	 * @param int $id Gallery ID.
	 * @return array|\WP_Error
	 */
	public function delete( int $id ) {
		$gallery = $this->find( $id );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		if ( ! GalleryMapper::get_instance()->destroy( $gallery, true ) ) {
			return new \WP_Error(
				'ngg_abilities_delete_failed',
				__( 'The gallery could not be deleted.', 'nggallery' ),
				[ 'status' => 500 ]
			);
		}

		Transient::flush( 'rest_galleries' );

		return [
			'deleted' => true,
			'id'      => $id,
		];
	}

	/**
	 * Import media library attachments into a gallery.
	 *
	 * Each attachment goes through ImageREST::import_media_library() on its own, so the result
	 * can say which attachment produced which image and why one was skipped. An attachment is
	 * refused before the import when it is not an image or the caller may not read it: the
	 * import handler itself only authorises the destination gallery.
	 *
	 * @param int   $id             Gallery ID.
	 * @param int[] $attachment_ids Attachment IDs.
	 * @return array|\WP_Error
	 */
	public function add_images( int $id, array $attachment_ids ) {
		$gallery = $this->find( $id );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		$attachment_ids = array_values( array_unique( array_filter( array_map( 'absint', $attachment_ids ) ) ) );

		if ( empty( $attachment_ids ) ) {
			return new \WP_Error(
				'ngg_abilities_no_images',
				__( 'No valid attachment IDs provided.', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		$added     = [];
		$image_ids = [];
		$skipped   = [];
		$remaining = [];
		$warnings  = [];
		$started   = microtime( true );
		$budget    = $this->get_import_time_budget();

		foreach ( $attachment_ids as $index => $attachment_id ) {
			// Stop between imports once the budget is spent and hand the rest back: dying at the
			// PHP time limit part-way through leaves a half-imported image and drops the rest
			// unreported (#1153), and the caller's retry would then duplicate what did land.
			if ( $index > 0 && ( microtime( true ) - $started ) >= $budget ) {
				$remaining = array_slice( $attachment_ids, $index );
				break;
			}

			if ( 'attachment' !== get_post_type( $attachment_id ) || ! wp_attachment_is_image( $attachment_id ) ) {
				$skipped[] = $this->skip( $attachment_id, __( 'Not a media library image.', 'nggallery' ) );
				continue;
			}

			if ( ! current_user_can( 'read_post', $attachment_id ) ) {
				$skipped[] = $this->skip( $attachment_id, __( 'This account may not read that attachment.', 'nggallery' ) );
				continue;
			}

			$request = new \WP_REST_Request( 'POST', '/imagely/v1/images/import-media-library' );
			$request->set_param( 'gallery_id', $id );
			$request->set_param( 'attachment_ids', [ $attachment_id ] );

			$last_image_id = $this->get_last_image_id( $id );
			$error         = '';

			try {
				$response = ImageREST::import_media_library( $request );
				$data     = $response instanceof \WP_REST_Response ? (array) $response->get_data() : [];

				if ( ! $response instanceof \WP_REST_Response || $response->get_status() >= 300 ) {
					$error = isset( $data['error'] ) && is_string( $data['error'] ) ? $data['error'] : __( 'The image could not be imported.', 'nggallery' );
				}
			} catch ( \Throwable $e ) {
				// The handler catches Exception per file but not Error.
				$error = $e->getMessage();
				error_log( 'NextGEN Gallery: add-images ability, attachment #' . $attachment_id . ': ' . $error ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}

			// The gallery's new rows are the truth, not the response: a failure in a late step (the
			// backup copy, an ngg_medialibrary_imported_image filter), caught by the handler or
			// not, arrives after the image row was saved and the handler leaves the row in place.
			// Reporting that attachment as skipped would make a retry import a duplicate.
			$created = $this->get_image_ids_after( $id, $last_image_id );

			if ( ! $created ) {
				$skipped[] = $this->skip( $attachment_id, wp_strip_all_tags( '' !== $error ? $error : __( 'The image could not be imported.', 'nggallery' ) ) );
				continue;
			}

			$added[]   = $attachment_id;
			$image_ids = array_merge( $image_ids, $created );

			if ( '' !== $error ) {
				// The steps after the row was saved (caption, alt text, filters) may not have run.
				$warnings[] = $this->skip(
					$attachment_id,
					sprintf(
						/* translators: %s: error message. */
						__( 'Imported, but the import stopped early, so its caption, alt text or other details may be missing: %s', 'nggallery' ),
						wp_strip_all_tags( $error )
					)
				);
			}
		}

		$gallery = $this->get( $id );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		return [
			'gallery'   => $gallery,
			'added'     => $added,
			'image_ids' => $image_ids,
			'skipped'   => $skipped,
			'remaining' => $remaining,
			'warnings'  => $warnings,
		];
	}

	/**
	 * Highest image ID in a gallery, 0 when it has none.
	 *
	 * @param int $gallery_id Gallery ID.
	 * @return int
	 */
	private function get_last_image_id( int $gallery_id ): int {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(pid) FROM {$wpdb->nggpictures} WHERE galleryid = %d", $gallery_id ) );
	}

	/**
	 * IDs of the images added to a gallery after a given image ID.
	 *
	 * @param int $gallery_id Gallery ID.
	 * @param int $after      Image ID the new rows come after.
	 * @return int[]
	 */
	private function get_image_ids_after( int $gallery_id, int $after ): array {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT pid FROM {$wpdb->nggpictures} WHERE galleryid = %d AND pid > %d ORDER BY pid", $gallery_id, $after ) );

		return array_map( 'intval', (array) $ids );
	}

	/**
	 * Seconds add_images() may spend importing before it hands the rest back.
	 *
	 * A third of max_execution_time, capped at 20 seconds, leaves room for the import in
	 * progress and the response. No time limit (0) gets the cap.
	 *
	 * @return float
	 */
	private function get_import_time_budget(): float {
		$limit  = (int) ini_get( 'max_execution_time' );
		$budget = $limit > 0 ? min( 20, $limit / 3 ) : 20;

		/**
		 * Filters how many seconds one add-images call may spend importing.
		 *
		 * The first attachment is always attempted; the budget is checked between imports.
		 *
		 * @param float $budget Seconds.
		 */
		return max( 0.0, (float) apply_filters( 'ngg_abilities_import_time_budget', $budget ) );
	}

	/**
	 * Display types a gallery can use: visible ones that render images.
	 *
	 * Read from the display type mapper, so the list is whatever the active editions
	 * registered — Pro, Plus and Starter add their own.
	 *
	 * @return array<int, array{name: string, title: string}>
	 */
	public function get_display_types(): array {
		$types = [];

		foreach ( (array) DisplayTypeMapper::get_instance()->find_all() as $display_type ) {
			if ( ! $this->is_gallery_display_type( $display_type ) ) {
				continue;
			}

			$types[] = [
				'name'  => (string) $display_type->name,
				'title' => (string) $display_type->title,
			];
		}

		return $types;
	}

	/**
	 * Normalise a gallery entity into the shape the abilities return.
	 *
	 * @param object   $gallery     Gallery entity.
	 * @param int|null $image_count Precomputed image count, or null to count now.
	 * @return array
	 */
	public function format( $gallery, $image_count = null ): array {
		$id = (int) $gallery->{$gallery->id_field};

		if ( null === $image_count ) {
			$counts      = $this->get_image_counts( [ $id ] );
			$image_count = $counts[ $id ] ?? 0;
		}

		return [
			'id'               => $id,
			'title'            => (string) $gallery->title,
			'slug'             => (string) $gallery->slug,
			'description'      => (string) $gallery->galdesc,
			'display_type'     => (string) $gallery->display_type,
			'author'           => (int) $gallery->author,
			'image_count'      => (int) $image_count,
			'preview_image_id' => (int) $gallery->previewpic,
			'page_id'          => (int) $gallery->pageid,
			'created'          => $this->to_rfc3339( $gallery->date_created ?? '' ),
			'modified'         => $this->to_rfc3339( $gallery->date_modified ?? '' ),
			'edit_url'         => admin_url( 'admin.php?page=imagely-add-new&tab=images&id=' . $id ),
		];
	}

	/**
	 * Find a gallery or report a 404.
	 *
	 * @param int $id Gallery ID.
	 * @return object|\WP_Error
	 */
	private function find( int $id ) {
		$gallery = $id > 0 ? GalleryMapper::get_instance()->find( $id ) : null;

		if ( ! $gallery ) {
			return new \WP_Error(
				'ngg_abilities_gallery_not_found',
				/* translators: %d: gallery ID. */
				sprintf( __( 'Gallery with ID %d not found.', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		return $gallery;
	}

	/**
	 * Save through the mapper and return the stored gallery.
	 *
	 * Re-read after the save: listeners on ngg_created_new_gallery (Pro's default pricelist)
	 * save the row again.
	 *
	 * @param object $gallery    Gallery entity.
	 * @param string $error_code Code for the failure WP_Error.
	 * @return array|\WP_Error
	 */
	private function save( $gallery, string $error_code ) {
		$mapper = GalleryMapper::get_instance();

		try {
			$saved = $mapper->save( $gallery );
		} catch ( \Exception $e ) {
			return new \WP_Error( $error_code, $e->getMessage(), [ 'status' => 500 ] );
		}

		if ( ! $saved ) {
			$errors   = $gallery->validation();
			$messages = [];

			foreach ( is_array( $errors ) ? $errors : [] as $field_errors ) {
				$messages = array_merge( $messages, array_map( 'strval', (array) $field_errors ) );
			}

			return new \WP_Error(
				$error_code,
				$messages ? implode( ' ', $messages ) : __( 'The gallery could not be saved.', 'nggallery' ),
				[ 'status' => $messages ? 400 : 500 ]
			);
		}

		Transient::flush( 'rest_galleries' );

		return $this->get( (int) $gallery->{$gallery->id_field} );
	}

	/**
	 * Validate and sanitise fields the way the admin REST routes do.
	 *
	 * @param array $fields Column => value.
	 * @return array|\WP_Error
	 */
	private function prepare_fields( array $fields ) {
		$fields = array_intersect_key( $fields, array_flip( self::WRITABLE_COLUMNS ) );

		// The same allow-list GalleryREST runs, so a filter that narrows it narrows this too.
		$fields = MassAssignment::filter( $fields, 'gallery', $refused );

		if ( $refused ) {
			return new \WP_Error(
				'ngg_abilities_property_not_editable',
				sprintf(
					/* translators: %s: comma-separated list of property names. */
					__( 'These gallery properties may not be set and nothing was saved: %s', 'nggallery' ),
					MassAssignment::describe_refused( $refused )
				),
				[
					'status'  => 403,
					'refused' => $refused,
				]
			);
		}

		if ( array_key_exists( 'title', $fields ) ) {
			// GalleryREST registers sanitize_text_field for title. An empty slug leaves the
			// mapper no folder name to build, so such a title is refused rather than saved.
			$fields['title'] = sanitize_text_field( (string) $fields['title'] );

			if ( '' === $fields['title'] || '' === sanitize_title( $fields['title'] ) ) {
				return $this->invalid_title();
			}
		}

		if ( array_key_exists( 'galdesc', $fields ) ) {
			$fields['galdesc'] = wp_kses_post( (string) $fields['galdesc'] );
		}

		if ( array_key_exists( 'display_type', $fields ) ) {
			$display_type = DisplayTypeMapper::get_instance()->find_by_name( (string) $fields['display_type'] );

			if ( ! $display_type || ! $this->is_gallery_display_type( $display_type ) ) {
				return new \WP_Error(
					'ngg_abilities_invalid_display_type',
					/* translators: %s: display type name. */
					sprintf( __( '"%s" is not a gallery display type on this site. Call describe-gallery-schema for the list.', 'nggallery' ), (string) $fields['display_type'] ),
					[ 'status' => 400 ]
				);
			}

			// find_by_name() also matches aliases; store the canonical name.
			$fields['display_type'] = (string) $display_type->name;
		}

		return $fields;
	}

	/**
	 * Whether a display type is offered for galleries: rendered by an active edition, visible
	 * in the UI and image-based.
	 *
	 * The controller check matters: the display type store keeps the rows a Pro edition
	 * installed after that edition is deactivated, and nothing can render them then.
	 *
	 * @param object $display_type Display type entity.
	 * @return bool
	 */
	private function is_gallery_display_type( $display_type ): bool {
		if ( ! empty( $display_type->hidden_from_ui ) || ! ControllerFactory::has_controller( (string) $display_type->name ) ) {
			return false;
		}

		$entity_types = isset( $display_type->entity_types ) ? (array) $display_type->entity_types : [];

		return in_array( 'image', $entity_types, true );
	}

	/**
	 * The error for a missing or unusable title.
	 *
	 * @return \WP_Error
	 */
	private function invalid_title(): \WP_Error {
		return new \WP_Error(
			'ngg_abilities_invalid_title',
			__( 'The gallery title must contain at least one letter or number.', 'nggallery' ),
			[ 'status' => 400 ]
		);
	}

	/**
	 * A skipped-attachment row.
	 *
	 * @param int    $id     Attachment ID.
	 * @param string $reason Why it was skipped.
	 * @return array{id: int, reason: string}
	 */
	private function skip( int $id, string $reason ): array {
		return [
			'id'     => $id,
			'reason' => $reason,
		];
	}

	/**
	 * Image counts for several galleries in one grouped query.
	 *
	 * @param int[] $ids Gallery IDs.
	 * @return array<int, int>
	 */
	private function get_image_counts( array $ids ): array {
		global $wpdb;

		$ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );

		if ( empty( $ids ) ) {
			return [];
		}

		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- {$placeholders} is a list of %d built from count( $ids ), and $ids is passed to prepare().
				"SELECT galleryid, COUNT(*) AS total FROM {$wpdb->nggpictures} WHERE galleryid IN ( {$placeholders} ) GROUP BY galleryid",
				$ids
			)
		);

		$counts = [];

		foreach ( (array) $rows as $row ) {
			$counts[ (int) $row->galleryid ] = (int) $row->total;
		}

		return $counts;
	}

	/**
	 * Format a stored GMT datetime as RFC 3339, or null when it was never recorded.
	 *
	 * The mapper writes date_created and date_modified with current_time( 'mysql', true ), so
	 * the columns are already UTC. Galleries created before the columns existed hold NULL or a
	 * zeroed date.
	 *
	 * @param mixed $gmt Stored value.
	 * @return string|null
	 */
	private function to_rfc3339( $gmt ) {
		if ( ! is_string( $gmt ) || '' === $gmt || 0 === strpos( $gmt, '0000-00-00' ) ) {
			return null;
		}

		$timestamp = strtotime( $gmt . ' UTC' );

		return false === $timestamp ? null : gmdate( 'Y-m-d\TH:i:s\Z', $timestamp );
	}
}
