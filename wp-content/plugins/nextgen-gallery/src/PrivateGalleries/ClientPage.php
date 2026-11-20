<?php
/**
 * The single service that makes a gallery private or public.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\Util\Transient;
use WP_Error;

/**
 * Creates, reuses, locks, unlocks and drafts client pages. The only code that sets
 * `is_private` and `pageid` together.
 */
class ClientPage {

	/**
	 * Privacy states.
	 */
	const STATE_PUBLIC       = 'public';
	const STATE_PRIVATE      = 'private';
	const STATE_NOT_SET_UP   = 'not_set_up';
	const STATE_PAGE_MISSING = 'page_missing';

	/**
	 * Longest page password accepted. Core stores it in a VARCHAR(255).
	 *
	 * @var int
	 */
	const MAX_PASSWORD_LENGTH = 255;

	/**
	 * Set while this service saves a gallery, so the mapper lets is_private and pageid change.
	 *
	 * @var bool
	 */
	private static $writing = false;

	/**
	 * Whether the mapper is being called by this service.
	 *
	 * @return bool
	 */
	public static function is_writing() {
		return self::$writing;
	}

	/**
	 * A random page password.
	 *
	 * @param int $length Length.
	 * @return string
	 */
	public static function generate_password( $length = 12 ) {
		return wp_generate_password( $length, false, false );
	}

	/**
	 * The page a gallery links to, if it still exists and is not in the trash.
	 *
	 * @param object $gallery Gallery entity.
	 * @return \WP_Post|null
	 */
	public static function get_live_page( $gallery ) {
		$page_id = (int) $gallery->pageid;

		if ( $page_id <= 0 ) {
			return null;
		}

		$page = get_post( $page_id );

		if ( ! $page instanceof \WP_Post || 'trash' === $page->post_status ) {
			return null;
		}

		return $page;
	}

	/**
	 * Whether the gallery's page carries this gallery's client page mark.
	 *
	 * @param int $page_id    Page id.
	 * @param int $gallery_id Gallery id.
	 * @return bool
	 */
	public static function page_has_mark( $page_id, $gallery_id ) {
		$marks = get_post_meta( (int) $page_id, Registry::PAGE_MARK, false );
		return in_array( (string) (int) $gallery_id, array_map( 'strval', (array) $marks ), true );
	}

	/**
	 * The privacy state of a gallery.
	 *
	 * @param object $gallery Gallery entity.
	 * @return string One of the STATE_* constants, or a state added by the filter.
	 */
	public static function get_status( $gallery ) {
		$gid = (int) $gallery->{$gallery->id_field};

		if ( empty( $gallery->is_private ) ) {
			$status = self::STATE_PUBLIC;
		} elseif ( Registry::is_private( $gid ) ) {
			$status = self::get_live_page( $gallery ) ? self::STATE_PRIVATE : self::STATE_PAGE_MISSING;
		} else {
			$status = self::STATE_NOT_SET_UP;
		}

		/**
		 * Filters the privacy state of a gallery (Pro adds "expired").
		 *
		 * @param string $status  State.
		 * @param object $gallery Gallery entity.
		 */
		return (string) apply_filters( 'ngg_private_gallery_status', $status, $gallery );
	}

	/**
	 * The client link of a private gallery.
	 *
	 * @param object $gallery Gallery entity.
	 * @return string Empty when the gallery has no live client page.
	 */
	public static function get_client_url( $gallery ) {
		$gid = (int) $gallery->{$gallery->id_field};

		if ( ! Registry::is_private( $gid ) ) {
			return '';
		}

		$page = self::get_live_page( $gallery );

		if ( ! $page ) {
			return '';
		}

		/**
		 * Filters the link a photographer sends to the client (Pro adds the magic link).
		 *
		 * @param string   $url     Page permalink.
		 * @param object   $gallery Gallery entity.
		 * @param \WP_Post $page    Client page.
		 */
		return (string) apply_filters( 'ngg_private_gallery_client_url', get_permalink( $page ), $gallery, $page );
	}

	/**
	 * A copy of a gallery without its magic link token, for lists sent to users who may not edit it.
	 *
	 * @param object $gallery Gallery entity.
	 * @return object
	 */
	public static function without_secrets( $gallery ) {
		$copy = clone $gallery;
		unset( $copy->link_token );

		return $copy;
	}

	/**
	 * Makes a gallery private: creates the client page, or locks the linked one.
	 *
	 * @param object      $gallery  Gallery entity, already saved.
	 * @param string|null $password Page password. Null keeps the page's own or generates one.
	 * @return true|WP_Error
	 */
	public static function make_private( $gallery, $password = null ) {
		$gid = (int) $gallery->{$gallery->id_field};

		if ( $gid <= 0 ) {
			return new WP_Error( 'ngg_private_no_gallery', __( 'Save the gallery before making it private.', 'nggallery' ), [ 'status' => 400 ] );
		}

		if ( null !== $password ) {
			$checked = self::check_password( $password );
			if ( is_wp_error( $checked ) ) {
				return $checked;
			}
		}

		$page = self::get_live_page( $gallery );

		if ( $page ) {
			$result = self::lock_page( $page, $gid, $password );
		} else {
			$result = self::create_page( $gallery, null === $password ? self::generate_password() : $password );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$stale_page = (int) $gallery->pageid;
		if ( $stale_page > 0 && $stale_page !== (int) $result ) {
			// A trashed page keeps its meta; it is no longer this gallery's client page.
			delete_post_meta( $stale_page, Registry::PAGE_MARK, (string) $gid );
		}

		$gallery->pageid     = (int) $result;
		$gallery->is_private = 1;

		$saved = self::save_gallery( $gallery );
		Registry::rebuild();

		if ( is_wp_error( $saved ) ) {
			// A page created here must not outlive a gallery that could not be saved.
			if ( ! $page ) {
				wp_delete_post( (int) $result, true );
			}
			return $saved;
		}

		/**
		 * Fires after a gallery was made private.
		 *
		 * @param object $gallery Gallery entity.
		 * @param int    $page_id Client page id.
		 */
		do_action( 'ngg_private_gallery_made_private', $gallery, (int) $result );

		return true;
	}

	/**
	 * Makes a gallery public: removes the page password and drafts the page.
	 *
	 * @param object $gallery Gallery entity.
	 * @return true|WP_Error
	 */
	public static function make_public( $gallery ) {
		$gid  = (int) $gallery->{$gallery->id_field};
		$page = self::get_live_page( $gallery );

		// Only a page this feature locked is unlocked and drafted; a never set up link is left alone.
		if ( $page && self::page_has_mark( $page->ID, $gid ) ) {
			if ( ! current_user_can( 'edit_post', $page->ID ) ) {
				return self::page_permission_error();
			}

			$updated = wp_update_post(
				[
					'ID'            => $page->ID,
					'post_password' => '',
					'post_status'   => 'draft',
				],
				true
			);

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}

			delete_post_meta( $page->ID, Registry::PAGE_MARK, (string) $gid );
		} elseif ( $gallery->pageid ) {
			delete_post_meta( (int) $gallery->pageid, Registry::PAGE_MARK, (string) $gid );
		}

		$gallery->is_private = 0;

		$saved = self::save_gallery( $gallery );
		Registry::rebuild();

		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		/**
		 * Fires after a gallery was made public.
		 *
		 * @param object $gallery Gallery entity.
		 */
		do_action( 'ngg_private_gallery_made_public', $gallery );

		return true;
	}

	/**
	 * Changes the client page password. Every unlocked browser is logged out.
	 *
	 * @param object $gallery  Gallery entity.
	 * @param string $password New password.
	 * @return true|WP_Error
	 */
	public static function set_password( $gallery, $password ) {
		$checked = self::check_password( $password );
		if ( is_wp_error( $checked ) ) {
			return $checked;
		}

		$gid  = (int) $gallery->{$gallery->id_field};
		$page = self::get_live_page( $gallery );

		if ( ! $page || ! Registry::is_private( $gid ) ) {
			return new WP_Error( 'ngg_private_no_page', __( 'This gallery has no client page.', 'nggallery' ), [ 'status' => 409 ] );
		}

		if ( ! current_user_can( 'edit_post', $page->ID ) ) {
			return self::page_permission_error();
		}

		if ( hash_equals( (string) $page->post_password, (string) $password ) ) {
			return true;
		}

		$updated = wp_update_post(
			[
				'ID'            => $page->ID,
				'post_password' => $password,
			],
			true
		);

		return is_wp_error( $updated ) ? $updated : true;
	}

	/**
	 * Creates a new client page for a private gallery whose page is gone.
	 *
	 * @param object      $gallery  Gallery entity.
	 * @param string|null $password Page password; generated when null.
	 * @return true|WP_Error
	 */
	public static function recreate_page( $gallery, $password = null ) {
		if ( empty( $gallery->is_private ) ) {
			return new WP_Error( 'ngg_private_not_private', __( 'This gallery is not private.', 'nggallery' ), [ 'status' => 409 ] );
		}

		if ( self::get_live_page( $gallery ) ) {
			return new WP_Error( 'ngg_private_page_exists', __( 'This gallery already has a client page.', 'nggallery' ), [ 'status' => 409 ] );
		}

		return self::make_private( $gallery, $password );
	}

	/**
	 * Applies a "Link to page" change. Refused while the gallery is a set up private gallery.
	 *
	 * @param object $gallery Gallery entity (not saved here).
	 * @param int    $page_id New page id, 0 to unlink.
	 * @return true|WP_Error
	 */
	public static function check_linked_page_change( $gallery, $page_id ) {
		$gid = (int) $gallery->{$gallery->id_field};

		if ( $gid > 0 && Registry::is_private( $gid ) && (int) $page_id !== Registry::page_for( $gid ) ) {
			return new WP_Error(
				'ngg_private_page_locked',
				__( 'The linked page cannot be changed while the gallery is private. Turn off "Private gallery" first.', 'nggallery' ),
				[ 'status' => 409 ]
			);
		}

		return true;
	}

	/**
	 * Removes is_private and pageid from a mass assigned property bag, for apply_assigned().
	 *
	 * @param array $assignable Properties that passed MassAssignment. Changed in place.
	 * @return array The removed privacy properties.
	 */
	public static function extract_assigned( array &$assignable ) {
		$privacy = array_intersect_key( $assignable, array_flip( [ 'is_private', 'pageid' ] ) );

		unset( $assignable['is_private'], $assignable['pageid'] );

		return $privacy;
	}

	/**
	 * Applies is_private and pageid from a remote editing client (REST, XML-RPC, Lightroom).
	 *
	 * @param object $gallery Gallery entity, already saved.
	 * @param array  $privacy From extract_assigned().
	 * @return true|WP_Error
	 */
	public static function apply_assigned( $gallery, array $privacy ) {
		if ( array_key_exists( 'pageid', $privacy ) && (int) $privacy['pageid'] !== (int) $gallery->pageid ) {
			$allowed = self::check_linked_page_change( $gallery, (int) $privacy['pageid'] );
			if ( is_wp_error( $allowed ) ) {
				return $allowed;
			}

			$gallery->pageid = (int) $privacy['pageid'];
			$saved           = self::save_gallery( $gallery );
			Registry::rebuild();

			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}

		if ( ! array_key_exists( 'is_private', $privacy ) ) {
			return true;
		}

		$gid        = (int) $gallery->{$gallery->id_field};
		$want       = (bool) $privacy['is_private'];
		$is_set_up  = Registry::is_private( $gid );
		$is_flagged = ! empty( $gallery->is_private );

		if ( $want && ! $is_set_up ) {
			return self::make_private( $gallery );
		}

		if ( ! $want && ( $is_set_up || $is_flagged ) ) {
			return self::make_public( $gallery );
		}

		return true;
	}

	/**
	 * Validates a page password.
	 *
	 * @param mixed $password Password.
	 * @return true|WP_Error
	 */
	public static function check_password( $password ) {
		if ( ! is_string( $password ) || '' === trim( $password ) ) {
			return new WP_Error( 'ngg_private_password_empty', __( 'Enter a password for the client page.', 'nggallery' ), [ 'status' => 400 ] );
		}

		if ( strlen( $password ) > self::MAX_PASSWORD_LENGTH ) {
			return new WP_Error( 'ngg_private_password_long', __( 'The password is too long.', 'nggallery' ), [ 'status' => 400 ] );
		}

		return true;
	}

	/**
	 * Locks an existing page: sets its password, publishes it and marks it.
	 *
	 * @param \WP_Post    $page     Page.
	 * @param int         $gid      Gallery id.
	 * @param string|null $password Null keeps the page's own password, or generates one.
	 * @return int|WP_Error Page id.
	 */
	private static function lock_page( $page, $gid, $password ) {
		if ( ! current_user_can( 'edit_post', $page->ID ) ) {
			return self::page_permission_error();
		}

		if ( null === $password ) {
			$password = '' !== (string) $page->post_password ? (string) $page->post_password : self::generate_password();
		}

		$update = [ 'ID' => $page->ID ];

		if ( ! hash_equals( (string) $page->post_password, $password ) ) {
			$update['post_password'] = $password;
		}

		if ( 'publish' !== $page->post_status ) {
			if ( ! current_user_can( 'publish_pages' ) ) {
				return self::page_permission_error();
			}
			$update['post_status'] = 'publish';
		}

		if ( count( $update ) > 1 ) {
			$updated = self::without_menu_auto_add(
				static function () use ( $update ) {
					return wp_update_post( $update, true );
				}
			);

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		if ( ! self::page_has_mark( $page->ID, $gid ) ) {
			add_post_meta( $page->ID, Registry::PAGE_MARK, (string) $gid );
		}

		return (int) $page->ID;
	}

	/**
	 * Creates the client page.
	 *
	 * @param object $gallery  Gallery entity.
	 * @param string $password Page password.
	 * @return int|WP_Error Page id.
	 */
	private static function create_page( $gallery, $password ) {
		if ( ! current_user_can( 'publish_pages' ) ) {
			return self::page_permission_error();
		}

		$gid = (int) $gallery->{$gallery->id_field};

		// Same name as "Link to page" > "Create new page".
		$title = sprintf(
			/* translators: %s: gallery title */
			__( '%s Page', 'nggallery' ),
			'' !== trim( (string) $gallery->title ) ? (string) $gallery->title : __( 'Gallery', 'nggallery' )
		);

		$page_id = self::without_menu_auto_add(
			static function () use ( $gid, $title, $password ) {
				return wp_insert_post(
					[
						'post_type'      => 'page',
						'post_status'    => 'publish',
						'post_title'     => $title,
						'post_content'   => '[imagely id="' . $gid . '"]',
						'post_password'  => $password,
						'post_author'    => get_current_user_id(),
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
					],
					true
				);
			}
		);

		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}

		add_post_meta( (int) $page_id, Registry::PAGE_MARK, (string) $gid );

		return (int) $page_id;
	}

	/**
	 * Runs a page write without core adding the page to menus set to auto add new pages.
	 *
	 * @param callable $write The write.
	 * @return mixed What the write returned.
	 */
	private static function without_menu_auto_add( $write ) {
		$priority = has_action( 'transition_post_status', '_wp_auto_add_pages_to_menu' );

		if ( false !== $priority ) {
			remove_action( 'transition_post_status', '_wp_auto_add_pages_to_menu', $priority );
		}

		try {
			return $write();
		} finally {
			if ( false !== $priority ) {
				add_action( 'transition_post_status', '_wp_auto_add_pages_to_menu', $priority, 3 );
			}
		}
	}

	/**
	 * Saves a gallery through the mapper with the privacy columns unlocked.
	 *
	 * @param object $gallery Gallery entity.
	 * @return true|WP_Error
	 */
	private static function save_gallery( $gallery ) {
		self::$writing = true;

		try {
			$saved = GalleryMapper::get_instance()->save( $gallery );
		} finally {
			self::$writing = false;
		}

		Transient::flush( 'rest_galleries' );

		if ( ! $saved ) {
			return new WP_Error( 'ngg_private_save_failed', __( 'The gallery could not be saved.', 'nggallery' ), [ 'status' => 500 ] );
		}

		return true;
	}

	/**
	 * The error for a user who may edit the gallery but not its page.
	 *
	 * @return WP_Error
	 */
	private static function page_permission_error() {
		return new WP_Error(
			'ngg_private_page_forbidden',
			__( 'You are not allowed to change the client page of this gallery.', 'nggallery' ),
			[ 'status' => 403 ]
		);
	}
}
