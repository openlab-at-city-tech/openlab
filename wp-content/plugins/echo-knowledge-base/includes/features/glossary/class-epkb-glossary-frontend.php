<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Inject glossary tooltips into article content
 */
class EPKB_Glossary_Frontend {

	public function __construct() {
		add_filter( 'epkb_article_content', array( $this, 'inject_glossary_tooltips' ), 20, 2 );
	}

	/**
	 * Return inline CSS for glossary colors from KB config. Single source of truth for color overrides.
	 * Called from scripts-registration-public when enqueueing glossary assets (same place as ap-frontend-layout).
	 *
	 * @param array $kb_config
	 * @return string
	 */
	public static function get_glossary_color_css( $kb_config ) {

		$highlight_color = isset( $kb_config['glossary_highlight_color'] ) ? $kb_config['glossary_highlight_color'] : '#1e73be';
		$tooltip_bg      = isset( $kb_config['glossary_tooltip_background_color'] ) ? $kb_config['glossary_tooltip_background_color'] : '#FFFFFF';
		$tooltip_text    = isset( $kb_config['glossary_tooltip_text_color'] ) ? $kb_config['glossary_tooltip_text_color'] : '#000000';

		return '.epkb-glossary-term { border-bottom-color: ' . esc_attr( $highlight_color ) . '; }' .
			'.epkb-glossary-term--highlight-style-3 { background-color: ' . esc_attr( $highlight_color ) . '; }' .
			'.epkb-glossary-tooltip { background-color: ' . esc_attr( $tooltip_bg ) . '; color: ' . esc_attr( $tooltip_text ) . '; }' .
			'.epkb-glossary-tooltip::after { border-top-color: ' . esc_attr( $tooltip_bg ) . '; }' .
			'.epkb-glossary-tooltip.epkb-glossary-tooltip--above::after { border-top-color: ' . esc_attr( $tooltip_bg ) . '; }' .
			'.epkb-glossary-tooltip.epkb-glossary-tooltip--below::after { border-bottom-color: ' . esc_attr( $tooltip_bg ) . '; }';
	}

	/**
	 * Scan article content and wrap first occurrence of each glossary term with a tooltip span
	 *
	 * @param string $content
	 * @param array $args
	 * @return string
	 */
	public function inject_glossary_tooltips( $content, $args ) {

		if ( empty( $content ) ) {
			return $content;
		}

		// Check if glossary is enabled
		$kb_config = isset( $args['config'] ) ? $args['config'] : array();
		if ( empty( $kb_config ) ) {
			$kb_id = isset( $args['id'] ) ? $args['id'] : EPKB_KB_Config_DB::DEFAULT_KB_ID;
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		}
		if ( empty( $kb_config['glossary_enable'] ) || $kb_config['glossary_enable'] !== 'on' ) {
			return $content;
		}

		// Get published glossary terms
		$terms = get_terms( array(
			'taxonomy'   => EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY,
			'hide_empty' => false,
			'meta_key'   => 'epkb_glossary_status',
			'meta_value' => 'publish',
		) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return $content;
		}

		// Filter out terms without definitions
		$valid_terms = array();
		foreach ( $terms as $term ) {
			if ( ! empty( $term->description ) ) {
				$valid_terms[] = $term;
			}
		}
		if ( empty( $valid_terms ) ) {
			return $content;
		}

		// Sort by name length descending to avoid partial matches
		usort( $valid_terms, function( $a, $b ) {
			return mb_strlen( $b->name ) - mb_strlen( $a->name );
		} );

		// Split content by HTML tags so we only replace in text nodes (handles comments, CDATA, and > inside attributes)
		$parts = wp_html_split( $content );

		$matched_any = false;
		$matched_terms = array(); // track which terms were already matched
		$skip_depth = 0; // track nested tags to skip

		// Tags whose inner content we should skip
		$skip_tags = array( 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'code', 'pre', 'script', 'style' );

		for ( $i = 0; $i < count( $parts ); $i++ ) {

			// HTML tag
			if ( isset( $parts[$i][0] ) && $parts[$i][0] === '<' ) {

				// Check for opening skip tags
				foreach ( $skip_tags as $tag ) {
					if ( preg_match( '/^<' . $tag . '[\s>]/i', $parts[$i] ) ) {
						$skip_depth++;
						break;
					}
					if ( preg_match( '/^<\/' . $tag . '>/i', $parts[$i] ) ) {
						$skip_depth = max( 0, $skip_depth - 1 );
						break;
					}
				}
				continue;
			}

			// Skip text inside certain tags
			if ( $skip_depth > 0 ) {
				continue;
			}

			// Text node - try to replace terms
			foreach ( $valid_terms as $term ) {

				// Only match first occurrence of each term
				if ( isset( $matched_terms[$term->term_id] ) ) {
					continue;
				}

				$escaped_name = preg_quote( $term->name, '/' );
				$pattern = '/(?<!\w)(' . $escaped_name . ')(?!\w)/iu';

				if ( preg_match( $pattern, $parts[$i] ) ) {
					$tooltip_text = esc_attr( $term->description );
					$tooltip_name = esc_attr( $term->name );
					$style_slug = isset( $kb_config['glossary_highlight_style'] ) && in_array( $kb_config['glossary_highlight_style'], array( 'style_1', 'style_2', 'style_3' ), true )
						? $kb_config['glossary_highlight_style']
						: 'style_1';
					$style_class = 'epkb-glossary-term epkb-glossary-term--highlight-style-' . str_replace( 'style_', '', $style_slug );
					$aria_label = $term->name . '. ' . $term->description;
					$replacement = '<span class="' . esc_attr( $style_class ) . '" role="button" tabindex="0" aria-label="' . esc_attr( $aria_label ) . '" data-glossary-term="' . $tooltip_name . '" data-glossary-definition="' . $tooltip_text . '">$1</span>';

					// Replace first occurrence only
					$parts[$i] = preg_replace( $pattern, $replacement, $parts[$i], 1 );
					$matched_terms[$term->term_id] = true;
					$matched_any = true;

					// Re-split to isolate injected HTML from remaining text so subsequent terms don't match inside attributes
					$new_parts = wp_html_split( $parts[$i] );
					if ( count( $new_parts ) > 1 ) {
						array_splice( $parts, $i, 1, $new_parts );
					}
				}
			}
		}

		if ( ! $matched_any ) {
			return $content;
		}

		// Assets and color CSS are enqueued in epkb_enqueue_public_resources (scripts-registration-public.php).

		return implode( '', $parts );
	}
}
