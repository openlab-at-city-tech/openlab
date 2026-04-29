<?php

/**
 * Shortcode - Displays all published glossary terms grouped alphabetically with letter navigation.
 *
 * @copyright   Copyright (c) 2018, Echo Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Glossary_Index_Shortcode {

	public function __construct() {
		add_shortcode( 'epkb-glossary-index', array( $this, 'output_shortcode' ) );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @return string HTML output.
	 */
	public function output_shortcode( $attributes ) {

		$attributes = shortcode_atts( array(
			'color'   => '#1e73be',
			'toptext' => esc_html__( 'Back to top', 'echo-knowledge-base' ),
		), $attributes, 'epkb-glossary-index' );

		return self::render_glossary_index( $attributes );
	}

	/**
	 * Render the glossary index. Shared between shortcode and block.
	 *
	 * @param array $attributes {
	 *     Shortcode params: color, toptext
	 *     Block params: glossary_index_accent_color, glossary_index_back_to_top_text, is_block
	 * }
	 * @return string HTML output.
	 */
	public static function render_glossary_index( $attributes ) {

		// Only enqueue shortcodes CSS when used as shortcode, not block
		if ( empty( $attributes['is_block'] ) ) {
			wp_enqueue_style( 'epkb-shortcodes' );
		}

		// Accept both shortcode (color/toptext) and block (glossary_index_*) param names
		$raw_color      = isset( $attributes['glossary_index_accent_color'] ) ? $attributes['glossary_index_accent_color'] : ( isset( $attributes['color'] ) ? $attributes['color'] : '' );
		$accent_color   = empty( $raw_color ) ? '#1e73be' : EPKB_Utilities::sanitize_hex_color( $raw_color );
		$inactive_color = '#cccccc';
		$back_to_top_text = isset( $attributes['glossary_index_back_to_top_text'] ) ? sanitize_text_field( $attributes['glossary_index_back_to_top_text'] ) : ( isset( $attributes['toptext'] ) ? sanitize_text_field( $attributes['toptext'] ) : esc_html__( 'Back to top', 'echo-knowledge-base' ) );

		// Get published glossary terms (same query as EPKB_Glossary_Frontend::inject_glossary_tooltips)
		$terms = get_terms( array(
			'taxonomy'   => EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY,
			'hide_empty' => false,
			'meta_key'   => 'epkb_glossary_status',
			'meta_value' => 'publish',
		) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			ob_start(); ?>
			<div class="epkb-glossary-index">
				<p class="epkb-glossary-index__empty"><?php echo esc_html__( 'No glossary terms found.', 'echo-knowledge-base' ); ?></p>
			</div><?php
			return ob_get_clean();
		}

		// Group terms by first character (using sort_key meta if available for CJK)
		$grouped = array();
		$has_non_latin_group = false;
		foreach ( $terms as $term ) {
			$name = trim( $term->name );
			if ( empty( $name ) ) {
				continue;
			}

			$sort_key = get_term_meta( $term->term_id, 'epkb_glossary_sort_key', true );
			$grouping_text = ! empty( $sort_key ) ? $sort_key : $name;
			$group_key = self::get_group_key( $grouping_text );

			if ( ! preg_match( '/^[A-Z#]$/', $group_key ) ) {
				$has_non_latin_group = true;
			}

			if ( ! isset( $grouped[ $group_key ] ) ) {
				$grouped[ $group_key ] = array();
			}

			// Attach sort_key to term for sorting within groups
			$term->_sort_key = $sort_key;
			$grouped[ $group_key ][] = $term;
		}

		if ( empty( $grouped ) ) {
			ob_start(); ?>
			<div class="epkb-glossary-index">
				<p class="epkb-glossary-index__empty"><?php echo esc_html__( 'No glossary terms found.', 'echo-knowledge-base' ); ?></p>
			</div><?php
			return ob_get_clean();
		}

		// Sort groups: '#' first, then alphabetical/kana order
		uksort( $grouped, function( $a, $b ) {
			if ( $a === '#' ) return -1;
			if ( $b === '#' ) return 1;
			return strcmp( $a, $b );
		} );

		// Sort terms within each group using sort_key when available
		foreach ( $grouped as $group_key => &$group_terms ) {
			usort( $group_terms, function( $a, $b ) {
				$sort_a = ! empty( $a->_sort_key ) ? $a->_sort_key : $a->name;
				$sort_b = ! empty( $b->_sort_key ) ? $b->_sort_key : $b->name;
				return strcasecmp( $sort_a, $sort_b );
			} );
		}
		unset( $group_terms );

		ob_start(); ?>
		<div class="epkb-glossary-index">

			<nav id="epkb-glossary-index-nav" class="epkb-glossary-index__nav" aria-label="<?php echo esc_attr__( 'Glossary letter navigation', 'echo-knowledge-base' ); ?>"><?php
				if ( $has_non_latin_group ) {
					// Dynamic nav: only show group keys that have terms
					foreach ( $grouped as $group_key => $group_terms ) {
						$letter_slug = self::get_group_slug( $group_key ); ?>
						<a href="#epkb-glossary-<?php echo esc_attr( $letter_slug ); ?>" class="epkb-glossary-index__nav-item epkb-glossary-index__nav-item--active" style="color:<?php echo esc_attr( $accent_color ); ?>"><?php echo esc_html( $group_key ); ?></a><?php
					}
				} else {
					// Latin nav: full A-Z with inactive states
					$all_letters = array_merge( array( '#' ), range( 'A', 'Z' ) );
					foreach ( $all_letters as $letter ) {
						$letter_slug = self::get_group_slug( $letter );
						if ( isset( $grouped[ $letter ] ) ) { ?>
							<a href="#epkb-glossary-<?php echo esc_attr( $letter_slug ); ?>" class="epkb-glossary-index__nav-item epkb-glossary-index__nav-item--active" style="color:<?php echo esc_attr( $accent_color ); ?>"><?php echo esc_html( $letter ); ?></a><?php
						} else { ?>
							<span class="epkb-glossary-index__nav-item epkb-glossary-index__nav-item--inactive" aria-hidden="true" style="color:<?php echo esc_attr( $inactive_color ); ?>"><?php echo esc_html( $letter ); ?></span><?php
						}
					}
				} ?>
			</nav><?php

			foreach ( $grouped as $group_key => $group_terms ) {
				$letter_slug = self::get_group_slug( $group_key );
				// Display label: Latin letters show "Aa", non-Latin shows just the character, # shows #
				$display_label = $group_key === '#' ? '#' : ( preg_match( '/^[A-Z]$/', $group_key ) ? $group_key . mb_strtolower( $group_key ) : $group_key ); ?>
				<section id="epkb-glossary-<?php echo esc_attr( $letter_slug ); ?>" class="epkb-glossary-index__section" aria-labelledby="epkb-glossary-heading-<?php echo esc_attr( $letter_slug ); ?>">
					<div id="epkb-glossary-heading-<?php echo esc_attr( $letter_slug ); ?>" class="epkb-glossary-index__section-letter" role="heading" aria-level="2" style="color:<?php echo esc_attr( $accent_color ); ?>"><?php echo esc_html( $display_label ); ?></div>
					<div class="epkb-glossary-index__section-content">
						<dl class="epkb-glossary-index__terms"><?php
							foreach ( $group_terms as $term ) { ?>
								<div class="epkb-glossary-index__term">
									<dt class="epkb-glossary-index__term-name" style="color:<?php echo esc_attr( $accent_color ); ?>"><?php echo esc_html( $term->name ); ?></dt>
									<dd class="epkb-glossary-index__term-definition"><?php echo wp_kses_post( $term->description ); ?></dd>
								</div><?php
							} ?>
						</dl>
						<a href="#epkb-glossary-index-nav" class="epkb-glossary-index__back-to-top" aria-label="<?php echo esc_attr( $back_to_top_text . ' - ' . $group_key ); ?>"><span aria-hidden="true">&uarr; </span><?php echo esc_html( $back_to_top_text ); ?></a>
					</div>
				</section><?php
			} ?>

		</div><?php

		return ob_get_clean();
	}

	/**
	 * Determine the group key for a term based on its first character.
	 * For Japanese kana, maps to the kana row (あ, か, さ, etc.).
	 * For Latin letters, returns the uppercase letter.
	 *
	 * @param string $text The sort key or term name to derive the group from.
	 * @return string Single-character group key.
	 */
	private static function get_group_key( $text ) {

		$first_char = mb_substr( trim( $text ), 0, 1 );

		// Convert katakana to hiragana for uniform kana row lookup
		$hiragana_char = function_exists( 'mb_convert_kana' ) ? mb_convert_kana( $first_char, 'c' ) : $first_char;

		// Check kana row mapping (Japanese)
		$kana_rows = self::get_kana_row_map();
		if ( isset( $kana_rows[ $hiragana_char ] ) ) {
			return $kana_rows[ $hiragana_char ];
		}

		// Latin letter — strip diacritics so Ä→A, É→E, Ñ→N, etc.
		$base = strtoupper( substr( remove_accents( $first_char ), 0, 1 ) );
		if ( preg_match( '/^[A-Z]$/', $base ) ) {
			return $base;
		}

		// Other letter characters (e.g. CJK ideographs without sort_key) — group by the character itself
		if ( preg_match( '/[\p{L}]/u', $first_char ) ) {
			return $first_char;
		}

		return '#';
	}

	/**
	 * Get an HTML-safe slug for use in element IDs.
	 *
	 * @param string $group_key The group key character.
	 * @return string Safe slug for HTML id attributes.
	 */
	private static function get_group_slug( $group_key ) {

		if ( $group_key === '#' ) {
			return 'other';
		}

		// Latin letters use as-is for backward compatibility
		if ( preg_match( '/^[A-Z]$/', $group_key ) ) {
			return $group_key;
		}

		// Non-ASCII characters: use hex-encoded UTF-8 bytes for safe IDs
		return 'g-' . bin2hex( $group_key );
	}

	/**
	 * Map hiragana characters to their kana row representative.
	 * Standard Japanese dictionary grouping (五十音順).
	 *
	 * @return array Hiragana character => row representative.
	 */
	private static function get_kana_row_map() {

		return array(
			// あ行
			'あ' => 'あ', 'い' => 'あ', 'う' => 'あ', 'え' => 'あ', 'お' => 'あ',
			'ぁ' => 'あ', 'ぃ' => 'あ', 'ぅ' => 'あ', 'ぇ' => 'あ', 'ぉ' => 'あ',
			// か行 + が行
			'か' => 'か', 'き' => 'か', 'く' => 'か', 'け' => 'か', 'こ' => 'か',
			'が' => 'か', 'ぎ' => 'か', 'ぐ' => 'か', 'げ' => 'か', 'ご' => 'か',
			// さ行 + ざ行
			'さ' => 'さ', 'し' => 'さ', 'す' => 'さ', 'せ' => 'さ', 'そ' => 'さ',
			'ざ' => 'さ', 'じ' => 'さ', 'ず' => 'さ', 'ぜ' => 'さ', 'ぞ' => 'さ',
			// た行 + だ行
			'た' => 'た', 'ち' => 'た', 'つ' => 'た', 'て' => 'た', 'と' => 'た',
			'だ' => 'た', 'ぢ' => 'た', 'づ' => 'た', 'で' => 'た', 'ど' => 'た',
			'っ' => 'た',
			// な行
			'な' => 'な', 'に' => 'な', 'ぬ' => 'な', 'ね' => 'な', 'の' => 'な',
			// は行 + ば行 + ぱ行
			'は' => 'は', 'ひ' => 'は', 'ふ' => 'は', 'へ' => 'は', 'ほ' => 'は',
			'ば' => 'は', 'び' => 'は', 'ぶ' => 'は', 'べ' => 'は', 'ぼ' => 'は',
			'ぱ' => 'は', 'ぴ' => 'は', 'ぷ' => 'は', 'ぺ' => 'は', 'ぽ' => 'は',
			// ま行
			'ま' => 'ま', 'み' => 'ま', 'む' => 'ま', 'め' => 'ま', 'も' => 'ま',
			// や行
			'や' => 'や', 'ゆ' => 'や', 'よ' => 'や',
			'ゃ' => 'や', 'ゅ' => 'や', 'ょ' => 'や',
			// ら行
			'ら' => 'ら', 'り' => 'ら', 'る' => 'ら', 'れ' => 'ら', 'ろ' => 'ら',
			// わ行
			'わ' => 'わ', 'を' => 'わ', 'ん' => 'わ',
			'ゎ' => 'わ',
		);
	}
}
