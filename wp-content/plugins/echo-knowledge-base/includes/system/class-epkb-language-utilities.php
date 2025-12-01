<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Language-related utilities for handling WPML, Polylang, and WordPress locales
 *
 * @copyright   Copyright (C) 2024, Echo Plugins
 */
class EPKB_Language_Utilities {

	/**
	 * Detect the current language from WPML, Polylang, or WordPress locale
	 *
	 * @return array Array with 'locale' (e.g., 'en_US') and 'code' (e.g., 'en')
	 */
	public static function detect_current_language() {
		// Check WPML first
		$wpml_lang = apply_filters( 'wpml_current_language', null );
		if ( ! empty( $wpml_lang ) ) {
			// Get the full locale from WPML
			$wpml_locale = apply_filters( 'wpml_locale', get_locale(), $wpml_lang );
			return array(
				'locale' => $wpml_locale,
				'code'   => $wpml_lang
			);
		}

		// Check Polylang
		if ( function_exists( 'pll_current_language' ) ) {
			$pll_lang = pll_current_language( 'slug' );
			if ( ! empty( $pll_lang ) ) {
				$pll_locale = pll_current_language( 'locale' );
				return array(
					'locale' => $pll_locale,
					'code'   => $pll_lang
				);
			}
		}

		// Fallback to WordPress locale
		$locale = get_locale();
		$code = strtolower( substr( $locale, 0, 2 ) );
		
		return array(
			'locale' => $locale,
			'code'   => $code
		);
	}

	/**
	 * Get the default language for the site
	 *
	 * @return array Array with 'locale' and 'code' - falls back to WordPress locale if no multilingual plugin
	 */
	public static function get_site_default_language() {
		// Check for WPML
		if ( EPKB_Utilities::is_wpml_plugin_active() ) {
			global $sitepress;
			if ( isset( $sitepress ) && method_exists( $sitepress, 'get_default_language' ) ) {
				$default_code = $sitepress->get_default_language();
				if ( ! empty( $default_code ) ) {
					// Get the locale for this language code
					$default_locale = apply_filters( 'wpml_locale', get_locale(), $default_code );
					return array(
						'locale' => $default_locale,
						'code'   => $default_code
					);
				}
			}
		}
		
		// Check for Polylang
		if ( function_exists( 'pll_default_language' ) ) {
			$default_code = pll_default_language();
			if ( ! empty( $default_code ) ) {
				// Get the locale for this language code
				$default_locale = function_exists( 'pll_default_language' ) ? pll_default_language( 'locale' ) : get_locale();
				return array(
					'locale' => $default_locale,
					'code'   => $default_code
				);
			}
		}
		
		// Fallback to WordPress locale when no multilingual plugin is active
		$locale = get_locale();
		$code = strtolower( substr( $locale, 0, 2 ) );
		
		return array(
			'locale' => $locale,
			'code'   => $code
		);
	}

	/**
	 * Check if a multilingual plugin is active
	 *
	 * @return bool
	 */
	public static function is_multilingual_active() {
		return EPKB_Utilities::is_wpml_plugin_active() || function_exists( 'pll_current_language' );
	}

	/**
	 * Get the language of a specific post
	 *
	 * @param int $post_id Post ID
	 * @return string Language identifier with name (e.g., "en (English)" or "de (German)")
	 */
	public static function get_post_language( $post_id ) {
		
		$language = '';
		
		// Check for WPML
		if ( EPKB_Utilities::is_wpml_plugin_active() ) {
			$lang_details = apply_filters( 'wpml_post_language_details', null, $post_id );
			if ( ! empty( $lang_details['language_code'] ) ) {
				$language = $lang_details['language_code'];
				if ( ! empty( $lang_details['native_name'] ) ) {
					$language .= ' (' . $lang_details['native_name'] . ')';
				}
			}
		}
		
		// Check for Polylang
		if ( empty( $language ) && function_exists( 'pll_get_post_language' ) ) {
			$lang_code = pll_get_post_language( $post_id, 'slug' );
			if ( ! empty( $lang_code ) ) {
				$language = $lang_code;
				// Try to get the language name
				$lang_name = pll_get_post_language( $post_id, 'name' );
				if ( ! empty( $lang_name ) ) {
					$language .= ' (' . $lang_name . ')';
				}
			}
		}
		
		// Fallback to WordPress locale if no multilingual plugin
		if ( empty( $language ) ) {
			$locale = get_locale();
			$lang_code = self::get_language_code( $locale );
			$lang_name = self::get_language_name( $lang_code );
			$language = $lang_code . ' (' . $lang_name . ')';
		}
		
		return $language;
	}

	/**
	 * Filter posts by language
	 *
	 * @param array $post_ids Array of post IDs to filter
	 * @param string $language_code Language code to filter by
	 * @return array Filtered post IDs
	 */
	public static function filter_posts_by_language( $post_ids, $language_code ) {
		if ( empty( $post_ids ) || empty( $language_code ) ) {
			return $post_ids;
		}

		// For WPML
		if ( EPKB_Utilities::is_wpml_plugin_active() ) {
			$filtered_ids = array();
			foreach ( $post_ids as $post_id ) {
				$post_language = apply_filters( 'wpml_post_language_details', null, $post_id );
				if ( $post_language && $post_language['language_code'] === $language_code ) {
					$filtered_ids[] = $post_id;
				} elseif ( ! $post_language ) {
					// If no language info, include it (might be in default language)
					$filtered_ids[] = $post_id;
				}
			}
			return $filtered_ids;
		}

		// For Polylang, the filtering should have been done in the query
		// This is a fallback check
		if ( function_exists( 'pll_get_post_language' ) ) {
			$filtered_ids = array();
			foreach ( $post_ids as $post_id ) {
				$post_lang = pll_get_post_language( $post_id );
				if ( $post_lang === $language_code ) {
					$filtered_ids[] = $post_id;
				}
			}
			return $filtered_ids;
		}

		// No multilingual plugin, return all posts
		return $post_ids;
	}

	/**
	 * Get query args for filtering posts by language
	 *
	 * @param array $args Existing query args
	 * @param string $language_code Language code to filter by
	 * @return array Modified query args
	 */
	public static function add_language_filter_to_query( $args, $language_code = null ) {
		// Ensure WPML/Polylang filters are applied
		$args['suppress_filters'] = false;

		// Use current language if not specified
		if ( $language_code === null ) {
			$current_lang = self::detect_current_language();
			$language_code = $current_lang['code'];
		}

		// Add language filter for Polylang
		if ( function_exists( 'pll_current_language' ) && ! empty( $language_code ) ) {
			$args['lang'] = $language_code;
		}

		return $args;
	}

	/**
	 * Get the language code from a locale
	 *
	 * @param string $locale Locale code (e.g., 'en_US')
	 * @return string Language code (e.g., 'en')
	 */
	public static function get_language_code( $locale ) {
		return strtolower( substr( $locale, 0, 2 ) );
	}

	/**
	 * Get human-readable language name from code
	 *
	 * @param string $code Language code (e.g., 'en', 'de')
	 * @return string Language name (e.g., 'English', 'German')
	 */
	public static function get_language_name( $code ) {
		$languages = array(
			'ar' => 'Arabic',
			'bn' => 'Bengali',
			'bg' => 'Bulgarian',
			'ca' => 'Catalan; Valencian',
			'zh' => 'Chinese',
			'hr' => 'Croatian',
			'cs' => 'Czech',
			'da' => 'Danish',
			'nl' => 'Dutch; Flemish',
			'en' => 'English',
			'et' => 'Estonian',
			'fi' => 'Finnish',
			'fr' => 'French',
			'de' => 'German',
			'el' => 'Greek, Modern',
			'gu' => 'Gujarati',
			'he' => 'Hebrew',
			'hi' => 'Hindi',
			'hu' => 'Hungarian',
			'id' => 'Indonesian',
			'it' => 'Italian',
			'ja' => 'Japanese',
			'kn' => 'Kannada',
			'ko' => 'Korean',
			'lv' => 'Latvian',
			'lt' => 'Lithuanian',
			'ms' => 'Malay',
			'ml' => 'Malayalam',
			'mr' => 'Marathi',
			'no' => 'Norwegian',
			'pa' => 'Panjabi; Punjabi',
			'fa' => 'Persian',
			'pl' => 'Polish',
			'pt' => 'Portuguese',
			'ro' => 'Romanian',
			'ru' => 'Russian',
			'sr' => 'Serbian',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'es' => 'Spanish; Castilian',
			'sw' => 'Swahili',
			'sv' => 'Swedish',
			'ta' => 'Tamil',
			'te' => 'Telugu',
			'th' => 'Thai',
			'tr' => 'Turkish',
			'uk' => 'Ukrainian',
			'ur' => 'Urdu',
			'vi' => 'Vietnamese',
			'zu' => 'Zulu',
		);

		return isset( $languages[$code] ) ? $languages[$code] : $code;
	}
}