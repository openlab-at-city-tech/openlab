<?php

namespace Imagely\NGG\Util;

/**
 * Security utility class.
 */
class Security {

	/**
	 * Matches a single filename segment that a web server may route to a script
	 * handler. Compared against dot-delimited segments, so it is anchored.
	 *
	 * @var string
	 */
	const EXECUTABLE_EXTENSION_PATTERN = '/^(?:php[0-9]*|phtml|phtm|phps|pht|phar|phpt|hphp|shtml|shtm|cgi|fcgi|pl|py|rb|sh|bash|asp|aspx|ashx|asmx|jsp|jspx|htaccess|htpasswd|ini)$/i';

	/**
	 * Matches a single filename segment that PHP itself would execute if the file
	 * were include()d or require()d.
	 *
	 * Deliberately narrower than self::EXECUTABLE_EXTENSION_PATTERN. That pattern
	 * answers "could a web server route this file to some script handler", which is
	 * the right question when deciding how to store an uploaded file. It is the wrong
	 * question when deciding whether a filename is a valid PHP template: segments
	 * like `pl`, `py`, `sh` and `ini` are inert to include() but are plausible in a
	 * legitimate template name (a locale suffix, for instance), so testing against
	 * the wider pattern would reject real templates for no security benefit.
	 *
	 * @var string
	 */
	const PHP_EXTENSION_PATTERN = '/^(?:php[0-9]*|phtml|phtm|phps|pht|phar|phpt|hphp)$/i';

	/*
	 * MERGE NOTE (#932 + #933): the property allow-lists, the never-editable set, the
	 * value casts and filter_assignable_properties() used to live here. They were the
	 * #932 engine; #933 landed a second one in Util\MassAssignment over the same
	 * property bag. Two engines over one bag drift until one of them is the way in, so
	 * MassAssignment is now the single engine and this copy is gone.
	 *
	 * MassAssignment was kept because it is the strict superset: it reports refused keys
	 * back to the caller instead of dropping them silently, it denies dangerous *nested*
	 * keys inside `display_type_settings` - `template` among them, which is the key in
	 * #932's own proof of concept - and it keeps the presentation and commerce columns
	 * assignable so existing XML-RPC and Lightroom clients do not break. Everything this
	 * copy denied is denied there: `path`, `author`, `gid`, `slug`, `meta_data`,
	 * `filename`, `galleryid` and `extras_post_id`. The value casts moved with it.
	 *
	 * The `ngg_lightroom_editable_properties` filter is superseded by
	 * `ngg_assignable_properties`, which is applied for every entity type and every
	 * endpoint rather than the Lightroom route alone.
	 */

	/**
	 * Returns the dot-delimited segments of a filename that could be interpreted as
	 * an executable extension.
	 *
	 * WordPress's sanitize_file_name() defuses double extensions by appending
	 * underscores rather than removing them ("image.php.jpg" becomes
	 * "image.php_.jpg"), so segments are compared with trailing underscores trimmed.
	 *
	 * @param string $filename Filename (not a path) to inspect.
	 * @param string $pattern  Optional. Segment pattern to test against. Defaults to
	 *                         self::EXECUTABLE_EXTENSION_PATTERN; pass
	 *                         self::PHP_EXTENSION_PATTERN for the include()-only question.
	 * @return array List of offending segments, empty when the filename is inert.
	 */
	public static function get_executable_extension_segments( $filename, $pattern = self::EXECUTABLE_EXTENSION_PATTERN ) {
		if ( ! is_string( $filename ) || '' === $filename ) {
			return [];
		}

		$segments = explode( '.', \wp_basename( $filename ) );
		$found    = [];

		// Index 0 is the base name, never an extension, so start at 1.
		$count = count( $segments );
		for ( $index = 1; $index < $count; $index++ ) {
			/*
			 * Trim every trailing character that cannot be part of an extension, not
			 * only the underscore sanitize_file_name() appends. The pattern is anchored,
			 * so any surviving trailing byte breaks the match: "pwn.php;.jpg" and
			 * "pwn.php .jpg" both read as inert if only "_" is trimmed.
			 */
			$segment = preg_replace( '/[^a-z0-9]+$/i', '', $segments[ $index ] );

			if ( '' !== $segment && preg_match( $pattern, $segment ) ) {
				$found[] = $segment;
			}
		}

		return $found;
	}

	/**
	 * Whether a filename contains any segment that could be executed as a script.
	 *
	 * @param string $filename Filename (not a path) to inspect.
	 * @return bool
	 */
	public static function has_executable_extension( $filename ) {
		return ! empty( self::get_executable_extension_segments( $filename ) );
	}

	/**
	 * Whether a filename contains any segment PHP would execute on include().
	 *
	 * Use this, not self::has_executable_extension(), when the question is "is this a
	 * legitimate PHP template name" — see self::PHP_EXTENSION_PATTERN for why the
	 * wider pattern produces false rejections there.
	 *
	 * @param string $filename Filename (not a path) to inspect.
	 * @return bool
	 */
	public static function has_php_executable_extension( $filename ) {
		return ! empty( self::get_executable_extension_segments( $filename, self::PHP_EXTENSION_PATTERN ) );
	}

	/**
	 * Demotes every executable-looking extension segment of a filename to part of
	 * the base name, so no web server can route the file to a script handler.
	 *
	 * "payload.php_.jpg" becomes "payload-php.jpg"; "image.jpg" is returned as-is.
	 *
	 * Takes a filename, not a path. Directory components are rejected rather than
	 * silently mangled: splitting a path on "." would treat "./" or a dotted
	 * directory name as an extension segment. Callers that hold a path should pass
	 * wp_basename( $path ) and rejoin themselves.
	 *
	 * @param string $filename Filename (not a path) to neutralize.
	 * @return string
	 */
	public static function neutralize_executable_extensions( $filename ) {
		if ( ! is_string( $filename ) || '' === $filename ) {
			return $filename;
		}

		// Guard the documented precondition instead of trusting it: a path handed in
		// here would have its directory components rewritten.
		if ( \wp_basename( $filename ) !== $filename ) {
			return $filename;
		}

		$segments = explode( '.', $filename );
		$count    = count( $segments );

		if ( $count < 2 ) {
			return $filename;
		}

		$result = $segments[0];

		for ( $index = 1; $index < $count; $index++ ) {
			$segment = $segments[ $index ];

			/*
			 * Same normalization as get_executable_extension_segments(): trim every trailing
			 * byte that cannot be part of an extension, not only the underscore
			 * sanitize_file_name() appends. Trimming just "_" left "pwn.php-.jpg" and
			 * "pwn.php;.jpg" untouched here while the anchored detector still flagged them,
			 * so the two functions disagreed and the sizing guard refused the file forever.
			 */
			$trimmed = (string) preg_replace( '/[^a-z0-9]+$/i', '', $segment );
			$glue    = '.';

			if ( '' !== $trimmed && preg_match( self::EXECUTABLE_EXTENSION_PATTERN, $trimmed ) ) {
				// Join with a dash instead of a dot: the segment is no longer an extension.
				$glue    = '-';
				$segment = $trimmed;
			}

			$result .= $glue . $segment;
		}

		return $result;
	}

	/**
	 * Resolves a filesystem path lexically, collapsing "." and ".." segments.
	 *
	 * Deliberately does not touch the filesystem: realpath() returns false for a path
	 * that does not exist yet, which is the normal case for a thumbnail that is about
	 * to be generated, so it cannot be the only way a traversal is caught.
	 *
	 * @param string $path Path to canonicalize.
	 * @return string Canonical path, or an empty string when $path is not usable.
	 */
	public static function canonicalize_path( $path ) {
		if ( ! is_string( $path ) || '' === $path ) {
			return '';
		}

		$path = \wp_normalize_path( $path );

		/*
		 * Hold the root aside so it survives the rebuild. The UNC form must be matched
		 * before the single-slash form: wp_normalize_path() deliberately preserves a
		 * leading "//" for network shares (its slash-collapsing lookbehind spares index
		 * 0), and a regex that captures only one of them turns "//server/share/x.jpg"
		 * into "/server/share/x.jpg" — a path that no longer exists on such a host.
		 * DataStorage\Manager::collapse_path_traversal() keeps the same prefix for the
		 * same reason; keep the two in step.
		 */
		$prefix = '';
		if ( preg_match( '#^(?://[^/]+/|[a-zA-Z]:/|/)#', $path, $match ) ) {
			$prefix = $match[0];
			$path   = substr( $path, strlen( $match[0] ) );
		}

		$resolved = [];
		foreach ( explode( '/', $path ) as $segment ) {
			if ( '' === $segment || '.' === $segment ) {
				continue;
			}

			if ( '..' === $segment ) {
				// Popping an empty list is a no-op, so ".." can never climb above the
				// root of an absolute path. A relative path that starts with ".." keeps
				// no prefix and therefore fails containment against an absolute base.
				array_pop( $resolved );
				continue;
			}

			$resolved[] = $segment;
		}

		return $prefix . implode( '/', $resolved );
	}

	/**
	 * Whether one canonical path is the given base or sits underneath it.
	 *
	 * @param string $path Canonical path to test.
	 * @param string $base Canonical base directory.
	 * @return bool
	 */
	public static function path_is_within( $path, $base ) {
		if ( ! is_string( $path ) || ! is_string( $base ) || '' === $path || '' === $base ) {
			return false;
		}

		/*
		 * A base that reduces to the filesystem root is too wide to be a boundary:
		 * trailingslashit( "/" ) is "/", which prefixes every absolute path, so a gallery
		 * folder setting of "wp-content/gallery/../../../.." would make containment accept
		 * anything on the server. The prefix pattern is the same one canonicalize_path()
		 * uses, so "C:/" and "//server/share/" are covered too. Mirrors the guard in
		 * DataStorage\Manager::path_contains() (96fe6832) - the sibling suite this one is
		 * asked to stay in step with.
		 */
		$base_body = preg_replace( '#^(?://[^/]+/|[a-zA-Z]:/|/)#', '', $base );
		if ( '' === rtrim( (string) $base_body, '/' ) ) {
			return false;
		}

		if ( $path === $base ) {
			return true;
		}

		return 0 === strpos( $path, \trailingslashit( $base ) );
	}

	/**
	 * Returns $path only when it is contained by $base, and null when it escapes.
	 *
	 * The containment check is what stops a stored filename from being read as a
	 * traversal: several paths in the storage manager are built by path_join()'ing a
	 * filename that came from the database, and path_join() is happy to join
	 * "../../../../wp-config.php".
	 *
	 * Note for whoever changes this next: DataStorage\Manager carries a second,
	 * older containment suite for the image-deletion path — collapse_path_traversal(),
	 * path_contains(), is_within_gallery_locations(), is_deletion_path_allowed() —
	 * added for the arbitrary-file-deletion report (#886). It is deliberately
	 * separate because it requires the path to already exist, which this one cannot:
	 * a thumbnail is contained before it is generated. Tighten or loosen containment
	 * in one of them and check the other, or unify them behind a single
	 * implementation with the #886 reproduction re-run.
	 *
	 * @param string $path Path to contain, existing or not.
	 * @param string $base Directory the path must stay inside.
	 * @return string|null Canonical contained path, or null when it escapes $base.
	 */
	public static function contain_path( $path, $base ) {
		$canonical_path = self::canonicalize_path( $path );
		$canonical_base = self::canonicalize_path( $base );

		if ( ! self::path_is_within( $canonical_path, $canonical_base ) ) {
			return null;
		}

		/*
		 * Second pass through realpath() so a symlink planted inside the base cannot
		 * point back out of it. Only the part of the path that already exists can be
		 * resolved; the rest is re-attached lexically, so a not-yet-generated
		 * thumbnail is still checked against the real location of its directory.
		 */
		$real_base = @realpath( $canonical_base ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( false === $real_base ) {
			// The gallery directory does not exist yet, so there is nothing further to
			// verify: the lexical check above is the whole answer and it passed.
			return $canonical_path;
		}

		$resolved = self::resolve_existing_prefix( $canonical_path );
		if ( null !== $resolved && ! self::path_is_within( $resolved, \wp_normalize_path( $real_base ) ) ) {
			return null;
		}

		return $canonical_path;
	}

	/**
	 * Resolves the deepest existing ancestor of a path through realpath() and
	 * re-attaches the segments that do not exist yet.
	 *
	 * @param string $path Canonical path.
	 * @return string|null Resolved path, or null when no ancestor exists.
	 */
	private static function resolve_existing_prefix( $path ) {
		$remainder = [];
		$current   = $path;

		while ( '' !== $current ) {
			$real = @realpath( $current ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			if ( false !== $real ) {
				$real = \wp_normalize_path( $real );

				return empty( $remainder )
					? $real
					: \trailingslashit( $real ) . implode( '/', array_reverse( $remainder ) );
			}

			$parent = \dirname( $current );
			if ( $parent === $current ) {
				break;
			}

			$remainder[] = \wp_basename( $current );
			$current     = $parent;
		}

		return null;
	}

	public static function get_mapped_cap( $capability_name ) {
		switch ( $capability_name ) {
			case 'nextgen_edit_display_settings':
			case 'nextgen_edit_settings':
				$capability_name = 'NextGEN Change options';
				break;
			case 'nextgen_edit_style':
				$capability_name = 'NextGEN Change style';
				break;
			case 'nextgen_edit_displayed_gallery':
				$capability_name = 'NextGEN Attach Interface';
				break;
			case 'nextgen_edit_gallery':
				$capability_name = 'NextGEN Manage gallery';
				break;
			case 'nextgen_edit_gallery_unowned':
				$capability_name = 'NextGEN Manage others gallery';
				break;
			case 'nextgen_upload_image':
			case 'nextgen_upload_images':
				$capability_name = 'NextGEN Upload images';
				break;
			case 'nextgen_edit_album_settings':
				$capability_name = 'NextGEN Edit album settings';
				break;
			case 'nextgen_edit_album':
				$capability_name = 'NextGEN Edit album';
				break;
		}

		return $capability_name;
	}

	public static function create_nonce( $cap = -1 ) {
		return \wp_create_nonce( self::get_mapped_cap( $cap ) );
	}

	public static function verify_nonce( $nonce, $cap = -1 ) {
		return \wp_verify_nonce( $nonce, self::get_mapped_cap( $cap ) );
	}

	public static function is_allowed( $capability_name, $user = false ) {
		$capability_name = self::get_mapped_cap( $capability_name );

		if ( ! $user && function_exists( 'wp_get_current_user' ) ) {
			$user = \wp_get_current_user();
		} elseif ( is_numeric( $user ) ) {
			$user = new \WP_User( $user );
		}

		return $user && $user->has_cap( $capability_name );
	}

	/**
	 * Whether the current user may manage a row owned by the given author.
	 *
	 * Ownership is never authorization on its own: the stored author id outlives the capability
	 * that let the user create the row, so the required capability is checked first and ownership
	 * only decides whether the cross-author capability is also needed. Every gallery and album
	 * gate calls this so the rule cannot drift between them.
	 *
	 * @param int|string|null $author_id           Owning user id, as stored on the row.
	 * @param string          $required_capability Capability this surface requires of every caller.
	 * @param string          $unowned_capability  Capability that permits acting on someone else's row.
	 * @return bool
	 */
	public static function can_manage_owned( $author_id, $required_capability = 'nextgen_edit_gallery', $unowned_capability = 'nextgen_edit_gallery_unowned' ) {
		if ( ! self::is_allowed( $required_capability ) ) {
			return false;
		}

		if ( self::is_allowed( $unowned_capability ) ) {
			return true;
		}

		// Cast because the storage layer returns the author as a numeric string on some paths.
		$author_id       = (int) $author_id;
		$current_user_id = (int) \get_current_user_id();

		// An unattributed row and a logged-out caller must not match on id 0.
		if ( $author_id <= 0 || $current_user_id <= 0 ) {
			return false;
		}

		return $current_user_id === $author_id;
	}

	/**
	 * Whether the current user may read a given media-library attachment.
	 *
	 * The import and conversion paths take attachment ids from the request - directly, or out of
	 * a post whose id the request names - and hand each one to get_attached_file() before reading
	 * the bytes off disk. The capability that opens those routes authorizes the *destination*
	 * gallery; nothing on them asks whose file is being read, so the object-level question has to
	 * be asked here.
	 *
	 * Two tests, because each answers a different question:
	 *
	 * - `edit_post`, not `read_post`. `read_post` is useless as a gate here: core's
	 *   get_post_status() reports `publish` for an attachment with no `post_parent` ("unattached
	 *   attachments are considered publicly accessible"), and map_meta_cap() resolves read_post on
	 *   a public-status post down to plain `read`, which every logged-in role holds - so a
	 *   Subscriber passes for any unattached upload, and most of a real media library is
	 *   unattached. `edit_post` resolves through the attachment's own author and
	 *   `edit_others_posts`, so an author keeps their own media and nobody reaches anyone else's.
	 *   This is also what the same bug settled on in the sibling plugin
	 *   (envira-gallery-plugin#5639), and importing is a write-shaped act anyway: the bytes are
	 *   copied out and republished at a new public URL.
	 * - the post-type test narrows the id space to real attachment rows, which is what stops an
	 *   arbitrary post id being used as a file reference: get_attached_file() reads
	 *   `_wp_attached_file` from **any** post id. It bounds the *object*, not the path - an
	 *   absolute stored value is returned verbatim and the `get_attached_file` filter may rewrite
	 *   it, so offloaded-media and migrated rows can still resolve outside the uploads tree.
	 *   Callers needing path containment have to assert it separately.
	 *
	 * @param int|string $attachment_id Attachment id, as supplied by the request.
	 * @return bool
	 */
	public static function can_read_attachment( $attachment_id ) {
		$attachment_id = (int) $attachment_id;

		if ( $attachment_id <= 0 ) {
			return false;
		}

		if ( 'attachment' !== \get_post_type( $attachment_id ) ) {
			return false;
		}

		return \current_user_can( 'edit_post', $attachment_id );
	}
}
