<?php

namespace Imagely\NGG\DisplayType;

/**
 * Legacy Template Locator
 *
 * Helps locate legacy template files for backward compatibility.
 */
class LegacyTemplateLocator {

	/**
	 * Singleton instance
	 *
	 * @var LegacyTemplateLocator|null
	 */
	public static $instance = null;

	/**
	 * Gets the singleton instance
	 *
	 * @return LegacyTemplateLocator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new LegacyTemplateLocator();
		}
		return self::$instance;
	}

	/**
	 * Returns an array of template storing directories
	 *
	 * @return array Template storing directories
	 */
	public function get_template_directories() {
		return apply_filters(
			'ngg_legacy_template_directories',
			[
				'Child Theme'       => get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'nggallery' . DIRECTORY_SEPARATOR,
				'Parent Theme'      => get_template_directory() . DIRECTORY_SEPARATOR . 'nggallery' . DIRECTORY_SEPARATOR,
				'NextGEN Legacy'    => NGGALLERY_ABSPATH . 'view' . DIRECTORY_SEPARATOR,
				'NextGEN Overrides' => implode(
					DIRECTORY_SEPARATOR,
					[
						WP_CONTENT_DIR,
						'ngg',
						'legacy',
						'templates',
					]
				),
			]
		);
	}

	/**
	 * Returns an array of all available template files
	 *
	 * @param bool|string|array $prefix Optional prefix filter.
	 * @return array All available template files
	 */
	public function find_all( $prefix = false ) {
		$files = [];
		foreach ( $this->get_template_directories() as $label => $dir ) {
			$tmp = $this->get_templates_from_dir( $dir, $prefix );
			if ( ! $tmp ) {
				continue;
			}
			$files[ $label ] = $tmp;
		}

		return $files;
	}

	/**
	 * Recursively scans $dir for files ending in .php
	 *
	 * @param string            $dir Directory to scan.
	 * @param bool|string|array $prefix Optional prefix filter.
	 * @return array All php files in $dir
	 */
	public function get_templates_from_dir( $dir, $prefix = false ) {
		if ( ! is_dir( $dir ) ) {
			return [];
		}

		$dir      = new \RecursiveDirectoryIterator( $dir );
		$iterator = new \RecursiveIteratorIterator( $dir );

		// convert single-item arrays to string.
		if ( is_array( $prefix ) && count( $prefix ) <= 1 ) {
			$prefix = end( $prefix );
		}

		// we can filter results by allowing a set of prefixes, one prefix, or by showing all available files.
		// Note: Legacy templates use lowercase naming (e.g., gallery.php, gallery-caption.php).
		// The regex is case-SENSITIVE to avoid matching View-based templates like Gallery.php.
		if ( is_array( $prefix ) ) {
			$str            = implode( '|', $prefix );
			$regex_iterator = new \RegexIterator( $iterator, "/({$str})-.+\\.php$/", \RecursiveRegexIterator::GET_MATCH );
		} elseif ( is_string( $prefix ) ) {
			$regex_iterator = new \RegexIterator( $iterator, "#(.*)[/\\\\]{$prefix}\\-?.*\\.php$#", \RecursiveRegexIterator::GET_MATCH );
		} else {
			$regex_iterator = new \RegexIterator( $iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH );
		}

		$files = [];
		foreach ( $regex_iterator as $filename ) {
			$files[] = reset( $filename );
		}

		return $files;
	}

	/**
	 * Whether a candidate path resolves to a location inside an allowed directory.
	 *
	 * Resolved with realpath() on both sides, so it holds regardless of how the
	 * candidate was assembled — traversal segments, symlinks and mixed separators are
	 * all collapsed before the comparison. This is the backstop for the string-level
	 * traversal check in find(): that check can only inspect the name it is given,
	 * while this one inspects the file that name actually points at.
	 *
	 * @param string $candidate Absolute path to test.
	 * @param string $dir       Allowed directory the candidate must live under.
	 * @return bool
	 */
	protected function is_within_directory( $candidate, $dir ) {
		/*
		 * Both calls are silenced, as every filesystem call in find() already is --
		 * get_templates_from_dir()'s is_dir() is not, but that one runs in the admin
		 * template dropdown rather than on a front-end render.
		 * Before this change the absolute-path branch of find() reached the filesystem
		 * not at all -- it compared strings -- so introducing an unsilenced realpath()
		 * here would emit "open_basedir restriction in effect" into front-end page
		 * output on the host population that reported #248, #62 and #130. Silenced,
		 * a restricted or non-traversable directory still refuses the template and
		 * report_containment_refusal() names open_basedir as a likely cause.
		 *
		 * The refusal stays fail-closed on purpose: an unresolvable path must not fall
		 * back to the lexical comparison this method exists to backstop.
		 */
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$dir_real = @realpath( $dir );
		if ( false === $dir_real ) {
			return false;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$candidate_real = @realpath( $candidate );
		if ( false === $candidate_real ) {
			// Only existing files reach here, so an unresolvable path is a rejection.
			return false;
		}

		$dir_real       = rtrim( str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $dir_real ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
		$candidate_real = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $candidate_real );

		return 0 === strpos( $candidate_real, $dir_real );
	}

	/**
	 * Find a particular template by name
	 *
	 * @param string $template_name
	 * @return string
	 */
	public function find( $template_name ) {
		$template_abspath = false;

		// Legacy templates may be an absolute path to a file that was moved in NextGEN 3.50. Here we remap the legacy
		// path to the current one.
		if ( false !== strpos( $template_name, 'nextgen-gallery/products/photocrati_nextgen/modules/ngglegacy' ) ) {
			$template_name = str_replace(
				'nextgen-gallery/products/photocrati_nextgen/modules/ngglegacy',
				'nextgen-gallery/src/Legacy',
				$template_name
			);
		}

		// hook into the render feature to allow other plugins to include templates.
		$custom_template = apply_filters( 'ngg_render_template', false, $template_name );

		if ( $custom_template === false ) {
			$custom_template = $template_name;
		}

		// SECURITY: refuse stream-wrapper syntax ("://") before any filesystem call.
		// A value such as "phar:///path/to/image.png/x.php" makes the @file_exists()
		// below open the named Phar archive. On PHP <= 7.4 opening a Phar unserialises
		// its metadata object, turning an existence check into deserialization of
		// attacker-chosen data (object injection); PHP 8.0+ defers that to an explicit
		// getMetadata() call, so the file op is inert there. A template name is always a
		// plain path relative to a template directory, so a wrapper is never legitimate.
		if ( false !== strpos( str_replace( '\\', '/', (string) $custom_template ), '://' ) ) {
			$this->report_refusal(
				__METHOD__,
				sprintf(
					// Translators: %s is the rejected template name.
					'The NextGEN Gallery legacy template "%s" was not loaded: its name contains stream-wrapper syntax ("://"), which a template name never uses.',
					esc_html( (string) $custom_template )
				),
				'4.5.3'
			);
			return false;
		}

		// Ensure we have a PHP extension.
		//
		// SECURITY: this must be a strict suffix test. A substring test ('.php' anywhere)
		// accepts names such as "payload.php_.jpg" as already being templates, so an
		// uploaded image whose sanitized name retains a ".php" segment resolves here and
		// is include()d by Controller::legacy_render().
		if ( ! preg_match( '/\.php$/i', $custom_template ) ) {
			$custom_template .= '.php';
		}

		// SECURITY: refuse names whose basename carries an executable segment beyond the
		// single trailing ".php" (e.g. "payload.php_.php"). A real template is one .php file.
		//
		// Tested against PHP_EXTENSION_PATTERN, not the wider EXECUTABLE_EXTENSION_PATTERN:
		// only segments PHP would execute on include() are relevant here, and the wider
		// pattern also matches `pl`, `py`, `sh` and `ini`, which are inert to include()
		// but plausible in a legitimate template name such as a locale suffix.
		$basename_without_php = preg_replace( '/\.php$/i', '', wp_basename( $custom_template ) );
		if ( \Imagely\NGG\Util\Security::has_php_executable_extension( $basename_without_php ) ) {
			// Say so. Returning a bare false here would be indistinguishable from "no such
			// template", leaving a site owner with a rejected name and nothing to go on;
			// the absolute-path refusal below reports itself the same way.
			$this->report_refusal(
				__METHOD__,
				sprintf(
					// Translators: %s is the rejected template file name.
					'The NextGEN Gallery legacy template "%s" was not loaded: its name carries a PHP extension segment beyond the single trailing ".php", which cannot be a genuine template file.',
					esc_html( wp_basename( $custom_template ) )
				),
				'4.4.0'
			);
			return false;
		}

		// SECURITY: Check for directory traversal patterns in ALL cases BEFORE processing.
		// This prevents LFI attacks via shortcode template parameters like "../../../../../../poc".
		// Normalize slashes first to catch mixed separator bypass attempts.
		$normalized_for_check = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $custom_template );
		if ( preg_match( '#\.\.' . preg_quote( DIRECTORY_SEPARATOR, '#' ) . '#', $normalized_for_check ) ) {
			// Directory traversal attempt detected - do not load this template.
			$this->report_refusal(
				__METHOD__,
				sprintf(
					// Translators: %s is the rejected template name.
					'The NextGEN Gallery legacy template "%s" was not loaded: the name contains a parent-directory segment. A legitimate template is referenced by file name relative to a template directory, never by a traversing path.',
					esc_html( $custom_template )
				),
				'4.4.0'
			);
			return false;
		}

		// Get allowed template directories once for reuse.
		$template_dirs = $this->get_template_directories();

		// Find the abspath of the template to render.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @file_exists( $custom_template ) ) {
			// Template doesn't exist as an absolute path, search through registered directories.
			foreach ( $template_dirs as $dir ) {
				if ( $template_abspath ) {
					break;
				}
				$filename = implode( DIRECTORY_SEPARATOR, [ rtrim( $dir, '/\\' ), $custom_template ] );
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$exists    = (bool) @file_exists( $filename );
				$contained = $exists && $this->is_within_directory( $filename, $dir );

				if ( $exists && ! $contained ) {
					$this->report_containment_refusal( $filename, $dir );
				}

				if ( $contained ) {
					$template_abspath = $filename;
				} elseif ( strpos( $custom_template, '-template' ) === false ) {
					$filename = implode(
						DIRECTORY_SEPARATOR,
						[
							rtrim( $dir, '/\\' ),
							// SECURITY: strip only the trailing ".php". str_replace() here removed
							// ".php" at any offset, so a segment like "...php/" collapsed to "../"
							// and reintroduced traversal AFTER the guard above had already passed:
							// "...php/...php/tmp/evil" resolved to "<dir>/../../tmp/evil-template.php"
							// and reached the include() in Controller::legacy_render().
							preg_replace( '/\.php$/i', '', $custom_template ) . '-template.php',
						]
					);
					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					$exists    = (bool) @file_exists( $filename );
					$contained = $exists && $this->is_within_directory( $filename, $dir );

					if ( $exists && ! $contained ) {
						$this->report_containment_refusal( $filename, $dir );
					}

					if ( $contained ) {
						$template_abspath = $filename;
					}
				}
			}
		} else {
			// An absolute path was given. Normalize before security checks to prevent bypass via mixed separators.
			$normalized_template = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $custom_template );

			// Check for directory traversal patterns AFTER normalization to catch all bypass attempts.
			if ( preg_match( '#\.\.' . preg_quote( DIRECTORY_SEPARATOR, '#' ) . '#', $normalized_template ) ) {
				// Directory traversal attempt detected - do not load this template.
				$this->report_refusal(
					__METHOD__,
					sprintf(
						// Translators: %s is the rejected template path.
						'The NextGEN Gallery legacy template "%s" was not loaded: the path contains a parent-directory segment once separators are normalized.',
						esc_html( $custom_template )
					),
					'4.4.0'
				);
				return false;
			}

			// Check if it's within an allowed template directory.
			//
			// is_within_directory() rather than a bare strpos() prefix test: the old test
			// compared the text against the directory name with its trailing separator
			// stripped, so a sibling such as "<theme>/nggallery-anything" prefix-matched
			// "<theme>/nggallery" and counted as inside it, and a symlink placed inside an
			// allowed directory was never resolved. This is the same containment the two
			// relative-path sites use, so the three acceptance paths cannot drift.
			foreach ( $template_dirs as $dir ) {
				if ( $this->is_within_directory( $custom_template, $dir ) ) {
					// This template is within an allowed directory.
					$template_abspath = $custom_template;
					break;
				}
			}

			/*
			 * An absolute path that sits textually inside an allowed directory but does not
			 * resolve inside it was refused by containment, not by the allow list. Reporting
			 * the deprecation notice below in that case tells owners to move the template
			 * into their theme -- which is exactly what they already did. Report the real
			 * cause instead.
			 */
			if ( ! $template_abspath ) {
				foreach ( $template_dirs as $dir ) {
					$dir_prefix = rtrim( $dir, '/\\' ) . DIRECTORY_SEPARATOR;
					if ( 0 === strpos( $normalized_template, $dir_prefix ) ) {
						$this->report_containment_refusal( $custom_template, $dir );
						return $template_abspath;
					}
				}
			}

			if ( ! $template_abspath ) {
				/*
				 * Historically, NextGEN Gallery allowed absolute paths here so that templates could be loaded from
				 * arbitrary locations on disk. This created a local file inclusion vulnerability via the `template`
				 * parameter on shortcodes.
				 *
				 * For security reasons we no longer load templates using arbitrary absolute paths. Site owners should
				 * instead move custom templates into their theme or child theme `nggallery` directory and reference them
				 * by file name (without a full path).
				 */
				$this->report_refusal(
					__METHOD__,
					sprintf(
						// Translators: %s is the absolute path that was provided.
						'Using an absolute path for a NextGEN Gallery legacy template (%s) is deprecated and no longer supported for security reasons. Please move this template file into your active theme or child theme "nggallery" directory and reference it by file name instead.',
						esc_html( $custom_template )
					),
					'3.59.13'
				);
				// Intentionally do not set $template_abspath for absolute paths outside allowed directories.
			}
		}

		return $template_abspath;
	}
	/**
	 * Reports a template file that exists but was refused by the containment check.
	 *
	 * Only reachable for a file @file_exists() already confirmed, so it cannot be flooded,
	 * and it is the one refusal in find() that is otherwise indistinguishable from "no such
	 * template": Controller::legacy_render() keeps its "[Not a valid template]" default and
	 * nothing is logged. is_within_directory() returns false whenever realpath() fails or
	 * resolves outside the directory - an open_basedir restriction, a directory without +x,
	 * or a symlinked template file - all states a site owner can fix once told.
	 *
	 * @param string $filename Absolute path of the refused template file.
	 * @param string $dir      Template directory it was expected to sit inside.
	 * @return void
	 */
	private function report_containment_refusal( $filename, $dir ) {
		$this->report_refusal(
			__METHOD__,
			sprintf(
				// Translators: 1: the template file path, 2: the template directory it must sit inside.
				'The NextGEN Gallery legacy template "%1$s" exists but was not loaded: it does not resolve inside the template directory "%2$s". A symlinked template file, a directory the web server cannot traverse, or an open_basedir restriction will all produce this.',
				esc_html( $filename ),
				esc_html( $dir )
			),
			'4.4.0'
		);
	}

	/**
	 * Reports a refused template through both of WordPress's developer channels.
	 *
	 * _doing_it_wrong() alone is not enough: wp-includes/functions.php emits its message
	 * only inside `if ( WP_DEBUG && ... )`, and even then only as a triggered notice,
	 * which a site with display_errors off and no error log never records. Every refusal
	 * in find() is otherwise indistinguishable from "no such template" -- legacy_render()
	 * keeps its "[Not a valid template]" default -- so the accompanying error_log() is
	 * what actually puts the cause in debug.log.
	 *
	 * Both channels stay gated on WP_DEBUG deliberately: these messages name absolute
	 * paths, so they belong in a developer's log and not in a production one. A site
	 * owner diagnosing a rejected template must switch WP_DEBUG on to see the cause.
	 *
	 * @param string $method  Calling method, for _doing_it_wrong().
	 * @param string $message Message describing the refusal and its likely cause.
	 * @param string $version Version the refusal was introduced in.
	 * @return void
	 */
	private function report_refusal( $method, $message, $version ) {
		/*
		 * Strip breaks once, before either channel. $remove_breaks must be true: the
		 * rejected name reaches this from a shortcode `template` attribute, whose value
		 * WordPress captures as [^"]* -- newlines included -- and esc_html() encodes
		 * markup but leaves CR and LF alone. Without this, anyone able to publish a post
		 * could write forged multi-line records into the host error log, re-emitted on
		 * every anonymous view of that page.
		 *
		 * _doing_it_wrong() has to be sanitized too, and that is easy to miss because it
		 * reads like a display-only channel. It is not: under WP_DEBUG it forwards to
		 * wp_trigger_error(), whose wp_kses( $message, array() ) strips markup but leaves
		 * the breaks, and PHP then writes the triggered message to the error log verbatim.
		 * Sanitizing only the error_log() call left the forging vector open on the same
		 * code path, under the same gate.
		 */
		$message = wp_strip_all_tags( $message, true );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every call site builds $message with esc_html().
		_doing_it_wrong( $method, $message, $version );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'NextGEN Gallery: ' . $message );
		}
	}
}
