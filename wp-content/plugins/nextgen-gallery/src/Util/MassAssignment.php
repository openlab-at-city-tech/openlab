<?php

namespace Imagely\NGG\Util;

use Imagely\NGG\DataStorage\Manager;

/**
 * Allow-lists for the caller-supplied property bags that the remote-editing endpoints
 * (XML-RPC, the Lightroom task queue) assign onto image, gallery and album models.
 *
 * Assigning whatever keys arrive over the wire turns an "edit the caption" call into a write to any
 * column of the model. Some of those columns end up in filesystem paths - an image's 'filename' and
 * the per-size 'filename' entries under 'meta_data' are both joined into an absolute path by
 * DataStorage\Manager, and a gallery's 'path' is the directory those paths are built from - while
 * others decide authorization ('author', 'galleryid'). Those columns are maintained by the storage
 * manager and the upload/import routines, which is where their values are validated, so they are
 * never accepted from a property bag.
 */
class MassAssignment {

	/**
	 * Stands in for the property names in $refused when the supplied bag was not a name/value
	 * structure at all, so there are no keys to name. See filter().
	 */
	const REFUSED_WHOLE_BAG = '(the whole property list - it was not a name/value structure)';

	/**
	 * Caps on what a single refusal log line may carry. This kind is not throttled, and the keys
	 * are request-supplied, so the line itself has to be bounded. See filter().
	 */
	const MAX_LOGGED_REFUSED_KEYS = 20;
	const MAX_LOGGED_KEY_LENGTH   = 64;

	/**
	 * Fields that may be mass assigned, per model type.
	 *
	 * @var array<string, string[]>
	 */
	private static $allowed = [
		'image'   => [ 'alttext', 'description', 'exclude', 'imagedate', 'sortorder', 'image_slug', 'pricelist_id' ],
		'gallery' => [
			'name',
			'title',
			'galdesc',
			'previewpic',
			'pageid',
			'is_private',
			// Presentation and commerce columns. These were assignable before the allow-list
			// existed and carry no path or authorization meaning, so they stay assignable -
			// dropping them would silently break XML-RPC and Lightroom clients that set them.
			'display_type',
			'display_type_settings',
			'external_source',
			'is_ecommerce_enabled',
			'pricelist_id',
		],
		'album'   => [ 'name', 'albumdesc', 'previewpic', 'pageid', 'sortorder', 'display_type', 'display_type_settings' ],
	];

	/**
	 * Fields that are never assignable from a property bag, even if a filter adds them back.
	 *
	 * @var array<string, string[]>
	 */
	private static $denied = [
		'image'   => [ 'pid', 'filename', 'meta_data', 'galleryid', 'post_id', 'extras_post_id', 'updated_at' ],
		'gallery' => [ 'gid', 'path', 'author', 'slug', 'extras_post_id' ],
		'album'   => [ 'id', 'slug', 'extras_post_id' ],
	];

	/**
	 * Value coercions applied to assignable fields, keyed by field name.
	 *
	 * Merged here from #932's allow-list (Util\Security) when the two engines were unified:
	 * the mappers store these columns without normalising them, so a remote client sending
	 * `"pageid": "12abc"` or `"is_private": "no"` persisted a value the column's own
	 * consumers then read as a different thing. Coercion belongs with the allow-list because
	 * both are statements about what a property bag is permitted to put in a column.
	 *
	 * @var array<string, string>
	 */
	private static $casts = [
		'pageid'     => 'absint',
		'previewpic' => 'absint',
		'exclude'    => 'bool',
		'is_private' => 'bool',
	];

	/**
	 * Keys that are never assignable *inside* an otherwise assignable array column.
	 *
	 * `display_type_settings` stays assignable - clients set presentation options through it - but
	 * it is not inert. Its per-display-type sub-array is merged into `$display_settings` on every
	 * front-end render (DisplayManager::get_display_params(), Renderer, DisplayedGallery), and four
	 * of its keys reach code from there:
	 *
	 * - `display_view` / `display_type_view` are joined onto a template directory and `include`d by
	 *   DisplayType\Controller::get_display_type_view_abspath().
	 * - `template` is passed as the template name to DisplayType\Controller::legacy_render(), which
	 *   resolves it through LegacyTemplateLocator and then `include`s the result. Reached from
	 *   DisplayTypes\SinglePicture, DisplayTypes\Thumbnails and DisplayTypes\Albums\SharedController,
	 *   each as `$display_settings['template']`. This is the key in #932's proof of concept
	 *   (`"template":"ngg430-direct3-pwn.php_.jpg"`) and the parameter behind #629 and #682 - it
	 *   reaches an `include` by the same route as the two view keys and belongs here with them.
	 * - `gallery_display_template` is the second route to that same `include`: an album's copy of it
	 *   is assigned onto the child gallery's `template` param by
	 *   DisplayTypes\Albums\SharedController::get_alternate_displayed_gallery() (line 678), which
	 *   then renders through Thumbnails and `legacy_render()`. Denying `template` without it would
	 *   leave the sink reachable by editing an album instead of a gallery. The codebase already
	 *   treats the three view keys as reserved for children (`RESERVED_CHILD_SETTINGS`, line 48);
	 *   this key is the sanctioned way to set the child's template, so it needs the same gate.
	 * - `trigger_handler` is instantiated with `new $klass()` by DisplayedGallery\TriggerManager.
	 *
	 * These five are the complete set: a sweep of every `display_settings['...']` read in `src/`
	 * and `products/` whose key names a template, view, handler, class, path or file returns
	 * exactly `display_view`, `display_type_view`, `template`, `gallery_display_template` and
	 * `trigger_handler`.
	 *
	 * A key-level allow-list alone would let a property bag reach all of them, which is the same
	 * outcome the `meta_data` / `filename` / `path` denials exist to stop. Denying them here blocks
	 * only the mass-assignment delivery route - `filter()` is reached from XMLRPC\Controller and
	 * Lightroom\Controller alone, never from the display-type admin UI, which legitimately stores
	 * an absolute template path and keeps working. The render-time containment checks in
	 * DisplayType\Controller and LegacyTemplateLocator remain the defence for values that arrive
	 * any other way, and every refusal is reported back to the client.
	 *
	 * @var array<string, string[]>
	 */
	private static $denied_nested = [
		'display_type_settings' => [
			'display_view',
			'display_type_view',
			'template',
			'gallery_display_template',
			'trigger_handler',
		],
	];

	/**
	 * Reduces a caller-supplied property bag to the fields that may safely be assigned.
	 *
	 * @param mixed      $properties Caller-supplied properties (anything not array-like yields []).
	 * @param string     $type       One of 'image', 'gallery' or 'album'.
	 * @param array|null $refused    Out. Keys that were dropped, in the order supplied. Callers
	 *                               must surface these: array_intersect_key() drops a refused key
	 *                               silently, so an edit that never stored the value would report
	 *                               success and leave the client no way to learn which field was
	 *                               ignored. The WP_DEBUG log below is not that channel - it is
	 *                               off in production.
	 * @return array The subset of $properties that may be assigned.
	 */
	public static function filter( $properties, $type, &$refused = null ) {
		$refused = [];

		if ( ! is_array( $properties ) && ! is_object( $properties ) ) {
			// Reported, not dropped quietly. This is the one branch that discards everything the
			// caller supplied, and leaving $refused empty made every caller's `if ( $refused )`
			// false: the edit was ignored and the endpoint still answered success. A scalar has no
			// key names to list, so the whole bag is named instead.
			if ( null !== $properties && '' !== $properties ) {
				$refused = [ self::REFUSED_WHOLE_BAG ];
			}

			return [];
		}

		if ( ! isset( self::$allowed[ $type ] ) ) {
			// Unknown entity type: nothing is assignable, so everything supplied was dropped.
			$refused = self::key_list( (array) $properties );
			return [];
		}

		/**
		 * Filters the fields a remote-editing endpoint may assign onto a model.
		 *
		 * Keys that feed filesystem paths or authorization are stripped after this filter runs, so
		 * they cannot be re-enabled here.
		 *
		 * @param array  $allowed Field names that may be assigned.
		 * @param string $type    One of 'image', 'gallery' or 'album'.
		 */
		$allowed = (array) apply_filters( 'ngg_assignable_properties', self::$allowed[ $type ], $type );
		$allowed = array_diff( $allowed, self::$denied[ $type ] );

		$properties = (array) $properties;
		$kept       = array_intersect_key( $properties, array_flip( $allowed ) );
		$refused    = self::key_list( array_diff_key( $properties, $kept ) );

		// Everything appended past this point came from the nested deny-list, not from the
		// caller's own key names. Recording the boundary is what makes that distinction
		// unspoofable - see the log-selection comment below.
		$top_level_refused = count( $refused );

		$kept = self::strip_denied_nested( $kept, $refused );
		$kept = self::apply_casts( $kept );

		// Kept as well as returned. The callers do turn $refused into a client-visible error, but
		// not every client shows it - the Lightroom desktop client ignores the response's error
		// field once the task status is 'done' - so this stays the local trace for a site owner or
		// support reading the PHP log.
		//
		// REFUSAL_MASS_ASSIGNMENT is deliberately NOT in THROTTLED_REFUSAL_KINDS: this is an
		// authenticated, low-frequency XML-RPC or Lightroom edit, not a front-end hot path, and
		// one line per hour would hide the 2nd..Nth refusal. So this line is written on every
		// occurrence - which is why the keys are bounded and stripped of newlines below rather
		// than interpolated raw. They come straight from request JSON, so a key containing a
		// newline would otherwise forge log entries, and a request carrying thousands of junk
		// keys would write all of them.
		if ( $refused ) {
			// Nested refusals are kept in preference to top-level ones. strip_denied_nested()
			// APPENDS its findings (display_type_settings.<type>.display_view and friends) to the
			// end of $refused, and those are the entries the deny-list exists for - the template
			// redirection in #932's proof of concept is one. Slicing the first N would let a
			// request pad itself with 20 unrecognised top-level names and push the interesting
			// refusal out of the only local trace, inverting the point of writing this line on
			// every occurrence.
			//
			// Split on the recorded boundary, NOT on whether the key text contains a dot. Top-level
			// refused keys are arbitrary names lifted from the request - the same untrusted origin
			// this block sanitizes for - so a caller could send 20 junk keys named "a.b" and have
			// them classified as nested, taking the reserved budget and re-opening the same hole
			// at the cost of one character per key. Position is set by us and cannot be forged.
			$top_level = array_slice( $refused, 0, $top_level_refused );
			$nested    = array_slice( $refused, $top_level_refused );

			$logged = array_slice( $nested, 0, self::MAX_LOGGED_REFUSED_KEYS );
			$logged = array_merge( $logged, array_slice( $top_level, 0, self::MAX_LOGGED_REFUSED_KEYS - count( $logged ) ) );

			$remainder = count( $refused ) - count( $logged );
			$rendered  = implode( ', ', array_map( [ self::class, 'sanitize_key_for_log' ], $logged ) );

			if ( $remainder > 0 ) {
				$rendered .= sprintf( ' and %d more', $remainder );
			}

			Manager::get_instance()->log_path_refusal(
				sprintf(
					'NextGEN Gallery: refused to assign %s propert%s [%s] - not in the assignable allow-list',
					$type,
					count( $refused ) === 1 ? 'y' : 'ies',
					$rendered
				),
				Manager::REFUSAL_MASS_ASSIGNMENT
			);
		}

		return $kept;
	}

	/**
	 * Coerces assignable values to the shape their column expects.
	 *
	 * @param array $kept Properties that passed the allow-list.
	 * @return array
	 */
	private static function apply_casts( array $kept ) {
		foreach ( $kept as $key => $value ) {
			if ( ! isset( self::$casts[ $key ] ) ) {
				continue;
			}

			$kept[ $key ] = ( 'bool' === self::$casts[ $key ] )
				? (int) (bool) $value
				: \absint( $value );
		}

		return $kept;
	}

	/**
	 * Removes the never-assignable sub-keys of an assignable array column.
	 *
	 * Only the sub-key is dropped, not the whole column: a client setting legitimate presentation
	 * options alongside a refused one keeps the legitimate ones. Refusals are appended to $refused
	 * as "column.subkey" so the caller reports them the same way as a top-level refusal.
	 *
	 * Both nesting shapes are handled: `display_type_settings['display_view']` (flat, as
	 * DisplayManager merges it for the default display type) and
	 * `display_type_settings['<display-type>']['display_view']` (per-display-type, the shape the
	 * column actually stores).
	 *
	 * @param array    $kept    Properties that passed the key-level allow-list.
	 * @param string[] $refused Out. Appended to, not replaced.
	 * @return array
	 */
	private static function strip_denied_nested( array $kept, array &$refused ) {
		foreach ( self::$denied_nested as $column => $denied_keys ) {
			if ( ! isset( $kept[ $column ] ) || ! is_array( $kept[ $column ] ) ) {
				continue;
			}

			foreach ( $kept[ $column ] as $key => $value ) {
				if ( in_array( (string) $key, $denied_keys, true ) ) {
					unset( $kept[ $column ][ $key ] );
					$refused[] = $column . '.' . $key;
					continue;
				}

				if ( ! is_array( $value ) ) {
					continue;
				}

				foreach ( $denied_keys as $denied_key ) {
					if ( array_key_exists( $denied_key, $value ) ) {
						unset( $kept[ $column ][ $key ][ $denied_key ] );
						$refused[] = $column . '.' . $key . '.' . $denied_key;
					}
				}
			}
		}

		return $kept;
	}

	/**
	 * The keys of a property bag as a list of strings, for reporting.
	 *
	 * @param array $properties Property bag.
	 * @return string[]
	 */
	private static function key_list( array $properties ) {
		return array_values( array_map( 'strval', array_keys( $properties ) ) );
	}

	/**
	 * Flattens a request-supplied property key for safe inclusion in a single log line.
	 *
	 * Newlines and carriage returns are what let a crafted key forge additional log entries, and
	 * an over-long key would push the real message off the end of the line. The key is kept
	 * otherwise verbatim so the trace stays useful for diagnosing a client sending the wrong field.
	 *
	 * @param string $key Refused property key.
	 * @return string
	 */
	private static function sanitize_key_for_log( $key ) {
		// All C0/C1 control characters, not just CR/LF/TAB: \x1b would otherwise pass ANSI escape
		// sequences through to whatever terminal reads the log, and \x0b / \x0c are line breaks to
		// some log viewers.
		$flattened = preg_replace( '/[\x00-\x1f\x7f-\x9f]/u', ' ', (string) $key );

		if ( null === $flattened ) {
			// preg_replace returns null on malformed UTF-8; fall back to a byte-wise strip so a
			// crafted key cannot skip sanitization entirely by being invalid UTF-8. The range
			// includes \x80-\x9f: a raw 0x9b is the 8-bit CSI, i.e. an ANSI escape introducer for
			// a terminal in 8-bit mode, and being invalid UTF-8 is exactly what routes a key here.
			$flattened = preg_replace( '/[\x00-\x1f\x7f-\x9f]/', ' ', (string) $key );
		}

		// mb_* so a multibyte key is not truncated mid-character, which would write an invalid
		// UTF-8 sequence into the log.
		if ( function_exists( 'mb_strlen' ) ) {
			if ( mb_strlen( $flattened, 'UTF-8' ) > self::MAX_LOGGED_KEY_LENGTH ) {
				$flattened = mb_substr( $flattened, 0, self::MAX_LOGGED_KEY_LENGTH, 'UTF-8' ) . '...';
			}
		} elseif ( strlen( $flattened ) > self::MAX_LOGGED_KEY_LENGTH ) {
			$flattened = substr( $flattened, 0, self::MAX_LOGGED_KEY_LENGTH ) . '...';
		}

		return $flattened;
	}


	/**
	 * Renders refused keys for a human-readable message.
	 *
	 * Percent signs are doubled because the Lightroom caller stores its message as a sprintf()
	 * format string and interpolates the object id into it later. These keys come straight from
	 * the request, so a key named "%s" would otherwise consume an argument or raise a ValueError
	 * instead of being printed.
	 *
	 * @param string[] $refused Refused property keys.
	 * @return string
	 */
	public static function describe_refused( array $refused ) {
		return str_replace( '%', '%%', implode( ', ', $refused ) );
	}
}
