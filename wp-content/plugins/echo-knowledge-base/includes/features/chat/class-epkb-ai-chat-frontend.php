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

		// Check access control before showing widget
		if ( ! self::is_user_allowed_to_access_chat( self::$active_collection ) ) {
			return false;
		}

		// If collection_id is 0, don't show the chat
		if ( empty( self::$active_collection['collection_id'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Output the HTML for the chat widget
	 */
	private function output_chat_widget_html() {

		// Build collection info with any configuration errors
		$collection_info = self::$active_collection;
		$collection_info['configuration_error'] = self::get_configuration_error_for_frontend();

		// Use output buffering to ensure clean output
		ob_start();		?>

		<!-- EPKB AI Chat Widget -->
		<div id="epkb-ai-chat-widget-root" class="epkb-ai-chat-widget-root" data-is-admin="<?php echo esc_attr( current_user_can( 'manage_options' ) ? 'true' : 'false' ); ?>"
			data-collection-id="<?php echo esc_attr( self::$active_collection['collection_id'] ); ?>"></div>
		<script>
			// Initialize the chat widget root element ID and collection info for the script
			window.epkbChatWidgetRoot = 'epkb-ai-chat-widget-root';
			window.epkbChatCollectionInfo = <?php echo wp_json_encode( $collection_info ); ?>;
		</script>   <?php

		$output = ob_get_clean();

		// Use wp_footer action to ensure proper placement
		echo $output;
	}

	/**
	 * Get configuration error formatted for frontend display
	 *
	 * @return array|null Error info or null if no error
	 */
	private static function get_configuration_error_for_frontend() {

		$collection_id = self::$active_collection['collection_id'];
		$is_admin = EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' );

		// Check for provider mismatch first
		$provider_mismatch = EPKB_AI_Training_Data_Config_Specs::get_active_and_selected_provider_if_mismatched( $collection_id );
		if ( $provider_mismatch !== null ) {
			return array(
				'type'    => 'provider_mismatch',
				// translators: %1$s is the collection provider, %2$s is the active provider
				'message' => $is_admin
					? sprintf(
						__( 'AI Chat unavailable: Collection uses %1$s but active provider is %2$s.', 'echo-knowledge-base' ),
						$provider_mismatch['collection_provider'],
						$provider_mismatch['active_provider']
					)
					: __( 'AI Chat is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
			);
		}

		// Check for collection/vector store issues
		$vector_store_result = EPKB_AI_Training_Data_Config_Specs::get_vector_store_id_by_collection( $collection_id );
		if ( is_wp_error( $vector_store_result ) ) {
			$error_code = $vector_store_result->get_error_code();

			// Provide helpful messages for specific error types
			if ( $error_code === 'collection_not_found' ) {
				return array(
					'type'    => $error_code,
					'message' => $is_admin
						? __( 'The selected AI data collection no longer exists. Please select a different collection in KB Configuration.', 'echo-knowledge-base' )
						: __( 'AI Chat is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
				);
			}

			if ( $error_code === 'no_vector_store' ) {
				return array(
					'type'    => $error_code,
					'message' => $is_admin
						? __( 'The selected collection has not been synced yet. Please sync it in the Training Data settings.', 'echo-knowledge-base' )
						: __( 'AI Chat is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
				);
			}

			// Fallback for other errors
			return array(
				'type'    => $error_code,
				'message' => $is_admin ? $vector_store_result->get_error_message() : __( 'AI Chat is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
			);
		}

		return null;
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

		// If display mode is 'all_pages', use collection from config
		if ( $display_mode === 'all_pages' ) {
			return array(
				'collection_id'   => isset( $ai_config['ai_chat_display_collection'] ) ? absint( $ai_config['ai_chat_display_collection'] ) : 0,
				'display_mode'    => $display_mode,
				'page_rules'      => array(),
				'post_types'      => array(),
				'url_patterns'    => '',
				'slot'            => 1
			);
		}

		// Check collections in priority order: 1 (highest) to 5 (lowest)
		$collection_slots = array( 1, 2, 3, 4, 5 );

		foreach ( $collection_slots as $slot_num ) {

			// Get collection ID and rules for this slot
			if ( $slot_num === 1 ) {
				$collection_id = isset( $ai_config['ai_chat_display_collection'] ) ? absint( $ai_config['ai_chat_display_collection'] ) : 0;
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
					'url_patterns'    => $url_patterns,
					'slot'            => $slot_num
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
				'url_patterns'    => '',
				'slot'            => 1
			);
		}

		// Default to configured collection (0 if not set)
		return array(
			'collection_id'   => isset( $ai_config['ai_chat_display_collection'] ) ? absint( $ai_config['ai_chat_display_collection'] ) : 0,
			'display_mode'    => $display_mode,
			'page_rules'      => isset( $ai_config['ai_chat_display_page_rules'] ) ? $ai_config['ai_chat_display_page_rules'] : array(),
			'post_types'      => isset( $ai_config['ai_chat_display_other_post_types'] ) ? $ai_config['ai_chat_display_other_post_types'] : array(),
			'url_patterns'    => isset( $ai_config['ai_chat_display_url_patterns'] ) ? $ai_config['ai_chat_display_url_patterns'] : '',
			'slot'            => 1
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
			// Check Pages (includes home page)
			if ( in_array( 'pages', $page_rules ) && ( is_page() || is_front_page() ) ) {
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

	/**
	 * Get a URL where AI Chat will be displayed based on current settings
	 *
	 * @return string URL where AI Chat is visible, or home_url as fallback
	 */
	public static function get_ai_chat_preview_url() {

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$display_mode = isset( $ai_config['ai_chat_display_mode'] ) ? $ai_config['ai_chat_display_mode'] : 'all_pages';

		// For 'all_pages' or 'all_except' mode, home page usually works
		if ( $display_mode === 'all_pages' || $display_mode === 'all_except' ) {
			return home_url( '/' );
		}

		// For 'selected_only' mode, find a matching page based on rules
		$page_rules = isset( $ai_config['ai_chat_display_page_rules'] ) ? $ai_config['ai_chat_display_page_rules'] : array();
		$post_types = isset( $ai_config['ai_chat_display_other_post_types'] ) ? $ai_config['ai_chat_display_other_post_types'] : array();

		// If 'pages' is selected, home/front page will work
		if ( in_array( 'pages', $page_rules ) ) {
			return home_url( '/' );
		}

		// If KB post types are selected, get the KB main page URL
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $kb_post_type ) {
				if ( preg_match( '/epkb_post_type_(\d+)/', $kb_post_type, $matches ) ) {
					$kb_id = intval( $matches[1] );
					$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
					$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
					if ( ! empty( $kb_main_page_url ) ) {
						return $kb_main_page_url;
					}
				}
			}
		}

		// If 'posts' is selected, get a recent post URL
		if ( in_array( 'posts', $page_rules ) ) {
			$recent_post = get_posts( array( 'numberposts' => 1, 'post_status' => 'publish' ) );
			if ( ! empty( $recent_post ) ) {
				return get_permalink( $recent_post[0]->ID );
			}
		}

		// Fallback to home page
		return home_url( '/' );
	}

	/**
	 * Check if the current user is allowed to access chat based on access control settings
	 *
	 * @param array $active_collection Active collection data with 'slot' key
	 * @return bool
	 */
	public static function is_user_allowed_to_access_chat( $active_collection = array() ) {

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$display_mode = isset( $ai_config['ai_chat_display_mode'] ) ? $ai_config['ai_chat_display_mode'] : 'all_pages';

		// For selected_only, use the matched location's settings; otherwise use global (slot 1)
		$slot = 1;
		if ( $display_mode === 'selected_only' && ! empty( $active_collection['slot'] ) ) {
			$slot = $active_collection['slot'];
		}
		$suffix = $slot === 1 ? '' : "_{$slot}";

		$access_mode = isset( $ai_config["ai_chat_access_mode{$suffix}"] ) ? $ai_config["ai_chat_access_mode{$suffix}"] : 'all';

		return self::check_access_for_current_user( $access_mode, $ai_config, $suffix );
	}

	/**
	 * Check if current user passes the given access mode
	 *
	 * @param string $access_mode 'all', 'logged_in', or 'wp_role'
	 * @param array $ai_config Full AI configuration
	 * @param string $suffix Setting suffix for the location slot
	 * @return bool
	 */
	private static function check_access_for_current_user( $access_mode, $ai_config, $suffix ) {

		if ( $access_mode === 'all' ) {
			return true;
		}

		if ( $access_mode === 'logged_in' ) {
			return is_user_logged_in();
		}

		if ( $access_mode === 'wp_role' ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}
			$allowed_roles = isset( $ai_config["ai_chat_access_roles{$suffix}"] ) ? $ai_config["ai_chat_access_roles{$suffix}"] : array();
			if ( empty( $allowed_roles ) ) {
				return true; // No roles configured = allow all logged-in users
			}
			$user = wp_get_current_user();
			return ! empty( array_intersect( $allowed_roles, $user->roles ) );
		}

		return true;
	}

	/**
	 * Check if the current user is allowed to access a specific collection based on access control settings.
	 * Used by REST API to gate access by collection_id rather than URL.
	 *
	 * @param int $collection_id The collection ID being accessed
	 * @return bool
	 */
	public static function is_user_allowed_for_collection( $collection_id ) {

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$display_mode = isset( $ai_config['ai_chat_display_mode'] ) ? $ai_config['ai_chat_display_mode'] : 'all_pages';

		// For all_pages/all_except, only global (slot 1) access settings apply
		if ( $display_mode !== 'selected_only' ) {
			$access_mode = isset( $ai_config['ai_chat_access_mode'] ) ? $ai_config['ai_chat_access_mode'] : 'all';
			return self::check_access_for_current_user( $access_mode, $ai_config, '' );
		}

		// For selected_only, find slots that use this collection and allow if any slot permits the user.
		// If collection_id is 0 (missing/spoofed), check ALL slots — user must pass at least one.
		$collection_id = absint( $collection_id );
		for ( $slot = 1; $slot <= 5; $slot++ ) {
			$suffix = $slot === 1 ? '' : "_{$slot}";

			if ( $collection_id > 0 ) {
				$slot_collection = isset( $ai_config["ai_chat_display_collection{$suffix}"] ) ? absint( $ai_config["ai_chat_display_collection{$suffix}"] ) : 0;
				if ( $slot_collection !== $collection_id ) {
					continue;
				}
			}

			$access_mode = isset( $ai_config["ai_chat_access_mode{$suffix}"] ) ? $ai_config["ai_chat_access_mode{$suffix}"] : 'all';
			if ( self::check_access_for_current_user( $access_mode, $ai_config, $suffix ) ) {
				return true;
			}
		}

		return false;
	}
}
