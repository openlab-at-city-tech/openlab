<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\DataMappers\DisplayedGallery as DisplayedGalleryMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\Util\URL;

class I18N {

	protected static $instance = null;

	/**
	 * @return I18N
	 */
	static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new I18N();
		}
		return self::$instance;
	}

	public function register_hooks() {
		add_action( 'init', [ $this, 'register_translation_hooks' ], 2 );
	}

	public function register_translation_hooks() {
		$dir = \str_replace(
			\wp_normalize_path( WP_PLUGIN_DIR ),
			'',
			\wp_normalize_path( NGG_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'I18N' )
		);

		// Load text domain.
		\load_plugin_textdomain( 'nggallery', false, $dir );

		// Hooks to register image, gallery, and album name & description with WPML.
		\add_action( 'ngg_image_updated', [ $this, 'register_image_strings' ] );
		\add_action( 'ngg_album_updated', [ $this, 'register_album_strings' ] );
		\add_action( 'ngg_created_new_gallery', [ $this, 'register_gallery_strings' ] );

		// do not let WPML translate posts we use as a document store.
		\add_filter( 'get_translatable_documents', [ $this, 'wpml_translatable_documents' ] );

		if ( \class_exists( 'SitePress' ) ) {
			// Copy AttachToPost entries when duplicating posts to another language.
			\add_action( 'icl_make_duplicate', [ $this, 'wpml_adjust_gallery_id' ], 10, 4 );
			\add_action( 'save_post', [ $this, 'wpml_set_gallery_language_on_save_post' ], 101, 1 );
		}

		// see function comments.
		\add_filter( 'ngg_displayed_gallery_cache_params', [ $this, 'set_qtranslate_cache_parameters' ] );
		\add_filter( 'ngg_displayed_gallery_cache_params', [ $this, 'set_wpml_cache_parameters' ] );
	}

	/**
	 * When QTranslate is active we must add its language & url-mode settings as display parameters
	 * so as to generate a unique cache for each language.
	 *
	 * @param array $arr
	 * @return array
	 */
	public function set_qtranslate_cache_parameters( $arr ) {
		if ( empty( $GLOBALS['q_config'] ) || ! \defined( 'QTRANS_INIT' ) ) {
			return $arr;
		}

		global $q_config;
		$arr['qtranslate_language'] = $q_config['language'];
		$arr['qtranslate_url_mode'] = $q_config['url_mode'];

		return $arr;
	}

	/**
	 * See notes on set_qtranslate_cache_paramters()
	 *
	 * @param array $arr
	 * @return array
	 */
	public function set_wpml_cache_parameters( $arr ) {
		if ( empty( $GLOBALS['sitepress'] ) || ! \defined( 'WPML_ST_VERSION' ) ) {
			return $arr;
		}

		global $sitepress;
		$settings             = $sitepress->get_settings();
		$arr['wpml_language'] = ICL_LANGUAGE_CODE;
		$arr['wpml_url_mode'] = $settings['language_negotiation_type'];

		return $arr;
	}

	/**
	 * Registers gallery strings with WPML
	 *
	 * @param int|object $gallery_id Gallery object or ID
	 */
	public function register_gallery_strings( $gallery_id ) {
		if ( \function_exists( 'icl_register_string' ) ) {
			$gallery = GalleryMapper::get_instance()->find( $gallery_id );
			if ( $gallery ) {
				if ( isset( $gallery->title ) && ! empty( $gallery->title ) ) {
					\icl_register_string( 'plugin_ngg', 'gallery_' . $gallery->{$gallery->id_field} . '_name', $gallery->title, true );
				}
				if ( isset( $gallery->galdesc ) && ! empty( $gallery->galdesc ) ) {
					\icl_register_string( 'plugin_ngg', 'gallery_' . $gallery->{$gallery->id_field} . '_description', $gallery->galdesc, true );
				}
			}
		}
	}

	/**
	 * Registers image strings with WPML
	 *
	 * @param object $image
	 */
	public function register_image_strings( $image ) {
		if ( \function_exists( 'icl_register_string' ) ) {
			if ( isset( $image->description ) && ! empty( $image->description ) ) {
				\icl_register_string( 'plugin_ngg', 'pic_' . $image->{$image->id_field} . '_description', $image->description, true );
			}
			if ( isset( $image->alttext ) && ! empty( $image->alttext ) ) {
				\icl_register_string( 'plugin_ngg', 'pic_' . $image->{$image->id_field} . '_alttext', $image->alttext, true );
			}
		}
	}

	/**
	 * Registers album strings with WPML
	 *
	 * @param object $album
	 */
	public function register_album_strings( $album ) {
		if ( \function_exists( 'icl_register_string' ) ) {
			if ( isset( $album->name ) && ! empty( $album->name ) ) {
				\icl_register_string( 'plugin_ngg', 'album_' . $album->{$album->id_field} . '_name', $album->name, true );
			}
			if ( isset( $album->albumdesc ) && ! empty( $album->albumdesc ) ) {
				\icl_register_string( 'plugin_ngg', 'album_' . $album->{$album->id_field} . '_description', $album->albumdesc, true );
			}
		}
	}

	/**
	 * NextGEN stores some data in custom posts that MUST NOT be automatically translated by WPML
	 *
	 * @param array $icl_post_types
	 * @return array $icl_post_types without any NextGEN custom posts
	 */
	public function wpml_translatable_documents( $icl_post_types = [] ) {
		$nextgen_post_types = [
			'ngg_album',
			'ngg_gallery',
			'ngg_pictures',
			'display_type',
			'gal_display_source',
			'lightbox_library',
			'photocrati-comments',
		];
		foreach ( $icl_post_types as $ndx => $post_type ) {
			if ( \in_array( $post_type->name, $nextgen_post_types, true ) ) {
				unset( $icl_post_types[ $ndx ] );
			}
		}

		return $icl_post_types;
	}

	public function wpml_adjust_gallery_id( $master_post_id, $lang, $post_array, $id ) {
		if ( ! isset( $post_array['post_type'] ) || $post_array['post_type'] == 'displayed_gallery' ) {
			return;
		}

		$re = '|preview/id--(\d+)|mi';
		if ( \preg_match_all( $re, $post_array['post_content'], $gallery_ids ) ) {
			foreach ( $gallery_ids[1] as $index => $gallery_id ) {
				$translated_gallery_id = \apply_filters( 'wpml_object_id', (int) $gallery_id, 'displayed_gallery', true, $lang );
			}

			$search[ $index ]           = 'preview/id--' . $gallery_id;
			$replace[ $index ]          = 'preview/id--' . $translated_gallery_id;
			$post_array['post_content'] = \str_replace( $search, $replace, $post_array['post_content'] );

			$to_save = [
				'ID'           => $id,
				'post_content' => $post_array['post_content'],
			];

			\wp_update_post( $to_save );
		}
	}

	public function wpml_set_gallery_language_on_save_post( $post_id ) {
		if ( \wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Nonce verification is not necessary: we are inspecting the URL and ending execution if a URL paramater matches.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['icl_ajx_action'] ) && 'make_duplicates' === sanitize_text_field( wp_unslash( $_POST['icl_ajx_action'] ) ) ) {
			return;
		}

		$post = \get_post( $post_id );

		if ( $post->post_type == 'displayed_gallery' ) {
			return;
		}

		if ( \preg_match_all( '#<img.*http(s)?://(.*)?' . NGG_ATTACH_TO_POST_SLUG . '(=|/)preview(/|&|&amp;)id(=|--)(\\d+).*?>#mi', $post->post_content, $matches, PREG_SET_ORDER ) ) {
			$mapper = DisplayedGalleryMapper::get_instance();
			foreach ( $matches as $match ) {
				// Find the displayed gallery.
				$displayed_gallery_id = $match[6];
				\add_filter( 'wpml_suppress_filters', '__return_true', 10, 1 );
				$displayed_gallery = $mapper->find( $displayed_gallery_id, true );
				\add_filter( 'wpml_suppress_filters', '__return_false', 11, 1 );
				if ( $displayed_gallery ) {
					$displayed_gallery_type = \apply_filters( 'wpml_element_type', 'displayed_gallery' );

					// set language of this gallery.
					$displayed_gallery_lang = \apply_filters( 'wpml_post_language_details', null, $displayed_gallery->ID );
					$post_language          = \apply_filters( 'wpml_post_language_details', null, $post_id );

					if ( ! $displayed_gallery_lang || $displayed_gallery_lang['language_code'] != $post_language['language_code'] ) {
						if ( $post_language ) {
							$args = [
								'element_id'    => $displayed_gallery->ID,
								'element_type'  => $displayed_gallery_type,
								'language_code' => $post_language['language_code'],
							];
							\do_action( 'wpml_set_element_language_details', $args );
						}
					}

					// duplicate gallery to other languages.
					$is_translated = \apply_filters( 'wpml_element_has_translations', '', $displayed_gallery->ID, $displayed_gallery_type );
					if ( ! $is_translated ) {
						\do_action( 'wpml_admin_make_post_duplicates', $displayed_gallery->ID );
					}
				}
			}
		}
	}

	public static function translate( $in, $name = null ) {
		if ( \function_exists( 'langswitch_filter_langs_with_message' ) ) {
			$in = \langswitch_filter_langs_with_message( $in );
		}

		if ( \function_exists( 'polyglot_filter' ) ) {
			$in = \polyglot_filter( $in );
		}

		if ( \function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			$in = \qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $in );
		}

		if ( \is_string( $name )
		&& ! empty( $name )
		&& \function_exists( 'icl_translate' )
		&& \apply_filters( 'wpml_default_language', null ) != \apply_filters( 'wpml_current_language', null ) ) {
			$in = \icl_translate( 'plugin_ngg', $name, $in, true );
		}

		$in = \apply_filters( 'localization', $in );

		return $in;
	}

	public static function mb_pathinfo( $path, $options = null ) {
		$ret      = [
			'dirname'   => '',
			'basename'  => '',
			'extension' => '',
			'filename'  => '',
		];
		$pathinfo = [];
		if ( \preg_match( '%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $pathinfo ) ) {
			if ( \array_key_exists( 1, $pathinfo ) ) {
				$ret['dirname'] = $pathinfo[1];
			}
			if ( \array_key_exists( 2, $pathinfo ) ) {
				$ret['basename'] = $pathinfo[2];
			}
			if ( \array_key_exists( 5, $pathinfo ) ) {
				$ret['extension'] = $pathinfo[5];
			}
			if ( \array_key_exists( 3, $pathinfo ) ) {
				$ret['filename'] = $pathinfo[3];
			}
		}
		switch ( $options ) {
			case PATHINFO_DIRNAME:
			case 'dirname':
				return $ret['dirname'];
			case PATHINFO_BASENAME:
			case 'basename':
				return $ret['basename'];
			case PATHINFO_EXTENSION:
			case 'extension':
				return $ret['extension'];
			case PATHINFO_FILENAME:
			case 'filename':
				return $ret['filename'];
			default:
				return $ret;
		}
	}

	public static function mb_basename( $path ) {
		$separator = ' qq ';
		$path      = \preg_replace( '/[^ ]/u', $separator . '$0' . $separator, $path );
		$base      = \basename( $path );
		return \str_replace( $separator, '', $base );
	}

	public static function get_kses_allowed_html() {
		global $allowedtags;

		$our_keys = [
			'a'      => [
				'href'  => [],
				'class' => [],
				'title' => [],
			],
			'br'     => [],
			'em'     => [],
			'i' 		=> [],
			'strong' => [],
			'b'      => [],
			'u'      => [],
			'p'      => [ 'class' => [] ],
			'div'    => [
				'class' => [],
				'id'    => [],
			],
			'span'   => [
				'class' => [],
				'id'    => [],
			]
		];

		return \array_merge_recursive( $allowedtags, $our_keys );
	}

	/**
	 * Sanitize text for alt/title/desc field.
	 *
	 * @param string $content content.
	 * @return string Sanitized content.
	 */
	public static function ngg_sanitize_text_alt_title_desc( $content ) {
		// Step 1: Decode HTML entities to get rid of HTML entity encoding (for example, &lt; becomes <).
		$content = self::ngg_decode_html_entities( $content );

		// Strip backslashes that may have been added by wp_kses or other functions.
		$content = stripslashes( $content );
		$content = str_replace( '`', '', $content );

		// Step 2: Remove specific dangerous patterns using regex.
		// Remove <script> tags and anything inside them.
		$content = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $content );

		// Remove inline event handlers like onclick, onload, etc.
		$content = preg_replace( '/\bon\w+\s*=\s*["\'].*?["\']/i', '', $content );

		// Remove dangerous JavaScript function calls (alert, prompt, confirm, etc.).
		$content = preg_replace( '/\b(alert|prompt|confirm)\s*\(\s*["\']?.*?["\']?\s*\)/i', '', $content ); // standard function calls.

		// Remove variations of alert(), prompt(), confirm(), including backticks and HTML entities.
		$content = preg_replace( '/\b(alert|prompt|confirm)\s*\(\s*`?[^`]*`?\s*\)/i', '', $content ); // with backticks.

		// Remove encoded JavaScript entities like &lt;script&gt;alert&lt;/script&gt; and others.
		$content = preg_replace( '/(&#?x?([0-9a-f]+);?|&(?:[a-z0-9]+|#x?[0-9a-f]+);?)*alert/i', '', $content ); // HTML-encoded alert and script.
		$content = preg_replace( '/(&#?x?([0-9a-f]+);?|&(?:[a-z0-9]+|#x?[0-9a-f]+);?)*script/i', '', $content ); // HTML-encoded script.

		// Remove JavaScript URL protocols like "javascript:", "vbscript:", etc.
		$content = preg_replace( '/(javascript|vbscript|data|file):/i', '', $content );

		// Remove dangerous JavaScript functions (eval, window.location, document.cookie, etc.).
		$content = preg_replace( '/(eval|window\.location|document\.cookie|document\.write)/i', '', $content );

		// Remove HTML entities.
		$content = preg_replace('/\&[^;]*;/', '', $content);

		// Strip remaining unwanted HTML tags and attributes (you can customize this further).
		$allowed_tags = self::get_kses_allowed_html();
		$content      = wp_kses( $content, $allowed_tags );

		// Clean up the content by re-encoding HTML entities, this ensures that no malicious code is stored in db.
		$content = htmlspecialchars( $content, ENT_QUOTES, 'UTF-8' );

		return $content;
	}

	/**
	 * Function to decode sanitized HTML content before render.
	 *
	 * @param  mixed $content
	 * @return void
	 */
	public static function ngg_decode_sanitized_html_content( $content ) {
		$content = self::ngg_sanitize_text_alt_title_desc( $content );
		$content = htmlspecialchars_decode( $content, ENT_QUOTES );
		return $content;
	}

	/**
	 * Function to return plain text for alt or title attributes.
	 *
	 * @param  mixed $text
	 * @return void
	 */
	public static function ngg_plain_text_alt_title_attributes( $text ) {
		$text = esc_attr( strip_tags( html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) );
		return $text;
	}

	/**
	 * Function to decode HTML entities recursively.
	 *
	 * @param  mixed $content
	 * @return void
	 */
	public static function ngg_decode_html_entities( $content ) {
		$previous_content = '';

		// Keep decoding until no changes occur (i.e., fully decoded).
		do {
			$previous_content = $content;
			$content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		} while ($previous_content !== $content);

		return $content;
	}
}
