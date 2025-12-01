<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display AI Chat widget on the frontend
 */
class EPKB_AI_Chat_Frontend {

	private static $active_collection = array();

	public function __construct() {
		// Hook into wp_footer with high priority to ensure it loads after theme
		add_action( 'wp_footer', array( $this, 'maybe_display_chat_widget' ), 999 );
	}

	/**
	 * Check if chat should be displayed and output the widget
	 */
	public function maybe_display_chat_widget() {

		if ( ! self::can_display_chat_widget() ) {
			return;
		}

		// Allow filtering of where to display the chat
		$display_chat = apply_filters( 'epkb_display_ai_chat', true );
		if ( ! $display_chat ) {
			return;
		}
		
		// Output the chat widget root element
		$this->output_chat_widget_html();
	}

	/**
	 * Check if chat widget should be displayed on current page
	 *
	 * @return bool
	 */
	public static function can_display_chat_widget() {

		$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled', 'off' );
		if ( $ai_chat_enabled == 'off' || ( $ai_chat_enabled == 'preview' && ! current_user_can( 'manage_options' ) ) ) {
			return false;
		}

		// Get the active collection for this page
		self::$active_collection = self::get_active_chat_collection_for_current_page();

		// If collection_id is 0, don't show the chat
		return ! empty( self::$active_collection['collection_id'] );
	}

	/**
	 * Output the HTML for the chat widget
	 */
	private function output_chat_widget_html() {

		// Use output buffering to ensure clean output
		ob_start();		?>

		<!-- EPKB AI Chat Widget -->
		<div id="epkb-ai-chat-widget-root" class="epkb-ai-chat-widget-root" data-is-admin="<?php echo esc_attr( current_user_can( 'manage_options' ) ? 'true' : 'false' ); ?>"
			data-collection-id="<?php echo esc_attr( self::$active_collection['collection_id'] ); ?>"></div>
		<script>
			// Initialize the chat widget root element ID and collection info for the script
			window.epkbChatWidgetRoot = 'epkb-ai-chat-widget-root';
			window.epkbChatCollectionInfo = <?php echo wp_json_encode( self::$active_collection ); ?>;
		</script>   <?php

		$output = ob_get_clean();

		// Use wp_footer action to ensure proper placement
		echo $output;
	}

	/**
	 * Get the active AI Chat collection for the current page
	 * Returns an array with collection ID and rules that match the current page
	 *
	 * @return array {
	 *     @type int $collection_id The collection ID to use (1-5, or 0 if no collection)
	 *     @type string $display_mode The display mode ('all_pages', 'selected_only', 'all_except')
	 *     @type array $page_rules Array of page rule keys that match
	 *     @type array $post_types Array of post type keys that match
	 *     @type string $url_patterns URL patterns string
	 * }
	 */
	private static function get_active_chat_collection_for_current_page() {

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$display_mode = isset( $ai_config['ai_chat_display_mode'] ) ? $ai_config['ai_chat_display_mode'] : 'all_pages';

		// If display mode is 'all_pages', use collection 1 by default
		if ( $display_mode === 'all_pages' ) {
			return array(
				'collection_id'   => isset( $ai_config['ai_chat_display_collection'] ) ? absint( $ai_config['ai_chat_display_collection'] ) : 1,
				'display_mode'    => $display_mode,
				'page_rules'      => array(),
				'post_types'      => array(),
				'url_patterns'    => ''
			);
		}

		// Check collections in priority order: 1 (highest) to 5 (lowest)
		$collection_slots = array( 1, 2, 3, 4, 5 );

		foreach ( $collection_slots as $slot_num ) {

			// Get collection ID and rules for this slot
			if ( $slot_num === 1 ) {
				$collection_id = isset( $ai_config['ai_chat_display_collection'] ) ? absint( $ai_config['ai_chat_display_collection'] ) : 1;
				$page_rules = isset( $ai_config['ai_chat_display_page_rules'] ) ? $ai_config['ai_chat_display_page_rules'] : array();
				$post_types = isset( $ai_config['ai_chat_display_other_post_types'] ) ? $ai_config['ai_chat_display_other_post_types'] : array();
				$url_patterns = isset( $ai_config['ai_chat_display_url_patterns'] ) ? $ai_config['ai_chat_display_url_patterns'] : '';
			} else {
				$collection_id = isset( $ai_config["ai_chat_display_collection_{$slot_num}"] ) ? absint( $ai_config["ai_chat_display_collection_{$slot_num}"] ) : 0;

				// Skip this slot if no collection is assigned
				if ( empty( $collection_id ) ) {
					continue;
				}

				$page_rules = isset( $ai_config["ai_chat_display_page_rules_{$slot_num}"] ) ? $ai_config["ai_chat_display_page_rules_{$slot_num}"] : array();
				$post_types = isset( $ai_config["ai_chat_display_other_post_types_{$slot_num}"] ) ? $ai_config["ai_chat_display_other_post_types_{$slot_num}"] : array();
				$url_patterns = isset( $ai_config["ai_chat_display_url_patterns_{$slot_num}"] ) ? $ai_config["ai_chat_display_url_patterns_{$slot_num}"] : '';
			}

			// Check if any rules are configured for this slot
			$has_rules = ! empty( $page_rules ) || ! empty( $post_types ) || ! empty( $url_patterns );

			// For 'selected_only' and 'all_except' modes: only check Location 1, skip all other locations
			// Multiple locations are only used when display_mode is 'all_pages'
			if ( ( $display_mode === 'selected_only' || $display_mode === 'all_except' ) && $slot_num !== 1 ) {
				continue;
			}

			if ( ! $has_rules && $slot_num !== 1 ) {
				continue; // Skip slots 2-5 if they have no rules configured
			}

			// Check if current page matches this slot's rules
			$is_selected_only = ( $display_mode === 'selected_only' );
			$matches = self::check_current_page_matches_rules( $page_rules, $post_types, $url_patterns );

			// For 'selected_only' mode: return first slot that matches
			// For 'all_except' mode: return first slot that doesn't match (invert the match result)
			if ( ( $is_selected_only && $matches ) || ( ! $is_selected_only && ! $matches ) ) {
				return array(
					'collection_id'   => $collection_id,
					'display_mode'    => $display_mode,
					'page_rules'      => $page_rules,
					'post_types'      => $post_types,
					'url_patterns'    => $url_patterns
				);
			}
		}

		// No match found - for 'selected_only' return null (don't show chat), for 'all_except' use collection 1
		if ( $display_mode === 'selected_only' ) {
			return array(
				'collection_id'   => 0,
				'display_mode'    => $display_mode,
				'page_rules'      => array(),
				'post_types'      => array(),
				'url_patterns'    => ''
			);
		}

		// Default to collection 1
		return array(
			'collection_id'   => isset( $ai_config['ai_chat_display_collection'] ) ? absint( $ai_config['ai_chat_display_collection'] ) : 1,
			'display_mode'    => $display_mode,
			'page_rules'      => isset( $ai_config['ai_chat_display_page_rules'] ) ? $ai_config['ai_chat_display_page_rules'] : array(),
			'post_types'      => isset( $ai_config['ai_chat_display_other_post_types'] ) ? $ai_config['ai_chat_display_other_post_types'] : array(),
			'url_patterns'    => isset( $ai_config['ai_chat_display_url_patterns'] ) ? $ai_config['ai_chat_display_url_patterns'] : ''
		);
	}

	/**
	 * Check if current page matches the given display rules
	 *
	 * @param array $page_rules Page rules to check
	 * @param array $post_types Post types to check
	 * @param string $url_patterns URL patterns to check
	 * @return bool True if current page matches the rules
	 */
	private static function check_current_page_matches_rules( $page_rules, $post_types, $url_patterns ) {

		// Check basic WordPress page types first
		if ( ! empty( $page_rules ) ) {
			// Check Posts
			if ( in_array( 'posts', $page_rules ) && is_single() && get_post_type() === 'post' ) {
				return true;
			}
			// Check Pages
			if ( in_array( 'pages', $page_rules ) && is_page() ) {
				return true;
			}
		}

		// Check Knowledge Base pages
		if ( ! empty( $post_types ) ) {
			// Check KB Main Page
			if ( EPKB_Utilities::is_kb_main_page() ) {
				foreach ( $post_types as $kb_post_type ) {
					if ( preg_match( '/epkb_post_type_(\d+)/', $kb_post_type, $matches ) ) {
						$kb_id = intval( $matches[1] );
						$current_kb_id = EPKB_Utilities::get_eckb_kb_id();
						if ( $current_kb_id == $kb_id ) {
							return true;
						}
					}
				}
			}

			// Check KB Article Page
			if ( is_singular() ) {
				$current_post_type = get_post_type();
				if ( in_array( $current_post_type, $post_types ) ) {
					return true;
				}
			}

			// Check KB Category/Tag Archive
			if ( is_tax() ) {
				$current_post_type = get_queried_object()->taxonomy;
				// Convert taxonomy to post type (e.g., 'epkb_post_type_1_category' -> 'epkb_post_type_1')
				if ( preg_match( '/(epkb_post_type_\d+)_/', $current_post_type, $matches ) ) {
					$kb_post_type = $matches[1];
					if ( in_array( $kb_post_type, $post_types ) ) {
						return true;
					}
				}
			}
		}

		// Check URL patterns last
		if ( ! empty( $url_patterns ) ) {

			$current_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			// Split patterns by comma or newline
			$patterns = preg_split( '/[\n,]+/', $url_patterns, -1, PREG_SPLIT_NO_EMPTY );

			foreach ( $patterns as $pattern ) {
				$pattern = trim( $pattern );
				if ( empty( $pattern ) ) {
					continue;
				}

				// Check if pattern has wildcard (*)
				if ( strpos( $pattern, '*' ) !== false ) {
					// Wildcard pattern: /knowledge-base/* matches page and all subpages
					$regex_pattern = str_replace( array( '/', '*' ), array( '\/', '.*' ), $pattern );
					$regex_pattern = '/' . $regex_pattern . '/';
				} else {
					// Exact match: /knowledge-base/ matches only this exact page (query params allowed)
					$regex_pattern = str_replace( '/', '\/', $pattern );
					$regex_pattern = '/' . $regex_pattern . '(\?.*)?$/';
				}

				if ( preg_match( $regex_pattern, $current_url ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
