<?php

namespace Imagely\NGG\DataStorage;

/**
 * Data sanitization utility class.
 *
 * Provides methods for sanitizing and cleaning data, particularly HTML content.
 */
class Sanitizer {

	/**
	 * Strips HTML from data with various options.
	 *
	 * @param string   $data                  The data to sanitize.
	 * @param bool     $just_scripts          Whether to only remove script/style tags. Default false.
	 * @param string[] $allowed_frame_targets Link target values to preserve, e.g. [ '_blank' ]. Empty
	 *                                        by default, which is HTMLPurifier's own default and
	 *                                        strips every target. Only consulted on the purifier
	 *                                        path: both tag-stripping fallbacks remove the whole
	 *                                        anchor regardless. Permitting a target also makes
	 *                                        HTMLPurifier add rel="noreferrer noopener" to it.
	 * @return string The sanitized data.
	 */
	public static function strip_html( $data, $just_scripts = false, $allowed_frame_targets = [] ) {
		// NGG 3.3.11 fix. Some of the data persisted with 3.3.11 didn't strip out all HTML.
		if ( strpos( $data, 'ngg_data_strip_html_placeholder' ) !== false ) {
			if ( class_exists( 'DomDocument' ) ) {
				$dom = new \DOMDocument( '1.0', 'UTF-8' );
				$dom->loadHTML( $data );
				$el    = $dom->getElementById( 'ngg_data_strip_html_placeholder' );
				$parts = array_map(
					function ( $el ) use ( $dom ) {
						$part = $dom->saveHTML( $el );
						return $part instanceof \DOMText ? $part->data : (string) $part;
					},
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$el->childNodes ? iterator_to_array( $el->childNodes ) : []
				);
				return self::strip_html( implode( ' ', $parts ), $just_scripts, $allowed_frame_targets );
			} else {
				return \wp_strip_all_tags( $data );
			}
		}

		// Remove all HTML elements.
		if ( ! $just_scripts ) {
			return \wp_strip_all_tags( $data );
		} elseif ( class_exists( 'DOMDocument' ) ) {
			// Remove unsafe HTML. This can generate a *lot* of warnings when given improper texts.
			libxml_use_internal_errors( true );
			libxml_clear_errors();

			// HTMLPurifier resolves its schema, entity table and language files from a global
			// constant, and the first writer wins for the whole request. Its eager autoload entry
			// is dropped at build time (see bin/neutralize_htmlpurifier_bootstrap.php) so we no
			// longer claim that constant on every page load. See #884.
			//
			// Everything below is ordered deliberately: prove our own tree is usable first, only
			// then claim the constant, and only pin the entity table when the constant points
			// somewhere we do not control.
			$prefix        = dirname( __DIR__, 2 ) . '/vendor/ezyang/htmlpurifier/library';
			$schema_file   = $prefix . '/HTMLPurifier/ConfigSchema/schema.ser';
			$entities_file = $prefix . '/HTMLPurifier/EntityLookup/entities.ser';

			// Both data files must be usable before we purify anything. Each bail logs, because
			// the fallback strips every tag and collapses line breaks, and validation() persists
			// that over the stored value -- a silent bail looks exactly like "my descriptions lost
			// their formatting" with nothing to correlate it to.
			//
			// The class must also be loaded before unserialize(), or the schema comes back as an
			// incomplete object. class_exists() resolves through the PSR-0 map and does not itself
			// claim the constant.
			if ( ! class_exists( '\HTMLPurifier_ConfigSchema' ) || ! is_readable( $schema_file ) ) {
				self::log_purifier_fallback( 'HTMLPurifier is unavailable or ' . $schema_file . ' is unreadable' );
				return \wp_strip_all_tags( $data, true );
			}

			// unlike Util/Serializable.php, which forbids objects outright, this one must produce
			// one. The trust boundary is what makes that acceptable: the input is our own vendored,
			// read-only schema.ser, not database or request content. allowed_classes is further
			// restricted to the three classes that file legitimately contains, none of which
			// declares __destruct or __wakeup, so there is no gadget to reach.
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Reading HTMLPurifier's own schema.ser, exactly as HTMLPurifier_ConfigSchema::makeFromSerial() does; allowed_classes is restricted to the classes that file legitimately contains.
			$schema = \unserialize(
				(string) \file_get_contents( $schema_file ),
				[ 'allowed_classes' => [ 'HTMLPurifier_ConfigSchema', 'HTMLPurifier_PropertyList', 'stdClass' ] ]
			);

			if ( ! $schema instanceof \HTMLPurifier_ConfigSchema ) {
				self::log_purifier_fallback( $schema_file . ' did not unserialize to a config schema' );
				return \wp_strip_all_tags( $data, true );
			}

			if ( ! is_readable( $entities_file ) ) {
				self::log_purifier_fallback( $entities_file . ' is unreadable' );
				return \wp_strip_all_tags( $data, true );
			}

			// The entity table is a plain array of strings, so nothing may be instantiated here.
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Reading HTMLPurifier's own entities.ser with allowed_classes => false, so no object can be instantiated.
			$entities = \unserialize( (string) \file_get_contents( $entities_file ), [ 'allowed_classes' => false ] );

			if ( ! \is_array( $entities ) ) {
				self::log_purifier_fallback( $entities_file . ' did not unserialize to an entity table' );
				return \wp_strip_all_tags( $data, true );
			}

			// Only now claim the constant, having proved our tree works. Claiming it earlier would
			// hand a co-installed HTMLPurifier a directory we just rejected, and its own
			// ConfigSchema::makeFromSerial() would then throw inside that plugin.
			if ( ! defined( 'HTMLPURIFIER_PREFIX' ) ) {
				define( 'HTMLPURIFIER_PREFIX', $prefix );
			}

			// Purification rules always come from our own schema rather than createDefault(), which
			// would resolve through the constant -- where a forced HTML.Trusted lets <script>
			// through. The directive set therefore stays ours whoever won the race, which also
			// keeps allowed markup in titles and descriptions intact instead of stripping it.
			//
			// The entity table needs pinning only when the constant is not ours. HTMLPurifier
			// reaches it lazily via HTMLPurifier_EntityLookup::instance(), whose setup()
			// unserializes entities.ser with no allowed_classes -- an object-injection sink if that
			// path is foreign (reachable: text inside <style>/<script> is parsed as CDATA, which is
			// what consults the entity table). When the constant is ours, that default read already
			// hits our own file, so we leave the shared singleton untouched; we only claim it in
			// the conflict case, where using our table is the whole point. It is re-seeded per call
			// rather than once per request so another plugin cannot leave a table of its choosing
			// in place for our purify.
			//
			// LanguageFactory still resolves through the constant but reads no serialized data and
			// is only reached via ErrorCollector when Core.CollectErrors is on, which it is not.
			// Cache.DefinitionImpl => null below keeps the definition cache from being read or
			// written at all.
			if ( HTMLPURIFIER_PREFIX !== $prefix ) {
				$lookup        = new \HTMLPurifier_EntityLookup();
				$lookup->table = $entities;
				\HTMLPurifier_EntityLookup::instance( $lookup );
			}

			$config = new \HTMLPurifier_Config( $schema );
			$config->set( 'Cache.DefinitionImpl', null );

			// Opt-in per caller. HTMLPurifier's default allows no link target at all, which is
			// right for most columns, but the album name and description are allowed to carry
			// a[target] (see Album::validation()). Setting this also makes HTMLPurifier attach
			// rel="noreferrer noopener" to any target it keeps, so permitting one does not
			// reintroduce reverse tabnabbing.
			if ( ! empty( $allowed_frame_targets ) ) {
				$config->set( 'Attr.AllowedFrameTargets', array_values( (array) $allowed_frame_targets ) );
			}

			$purifier       = new \HTMLPurifier( $config );
			$default_return = $purifier->purify( $data );
			return \apply_filters( 'ngg_html_sanitization', $default_return, $data );
		} else {
			// wp_strip_all_tags() is misleading in a way - it only removes <script> and <style> tags.
			return \wp_strip_all_tags( $data, true );
		}
	}

	/**
	 * Records that purification degraded to tag-stripping.
	 *
	 * Logged once per request: strip_html() runs per image and per gallery on save, so a bulk
	 * import would otherwise write thousands of identical lines.
	 *
	 * @param string $reason Why the purifier could not be used.
	 * @return void
	 */
	private static function log_purifier_fallback( $reason ) {
		static $logged = false;

		if ( $logged ) {
			return;
		}

		$logged = true;

		\error_log( 'NextGEN Gallery: HTML purification unavailable, falling back to stripping all tags. ' . $reason );
	}
}
