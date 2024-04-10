<?php
/**
 * Performance tweaks for Sydney
 */

if ( !class_exists( 'Sydney_Performance' ) ) {
	class Sydney_Performance {

		/**
		 * Instance
		 */		
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Disable emojis.
			add_action( 'init', array( $this, 'disable_emojis' ) );

			// Remove jquery migrate.
			add_action( 'wp_default_scripts', array( $this, 'remove_jquery_migrate' ) );

			// Defer Gutenberg styles
			add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_block_styles' ) );
			add_action( 'get_footer', array( $this, 'enqueue_block_styles' ) );
		}

		/**
		 * Disable the emojis.
		 */
		public function disable_emojis() {

			$disable = get_theme_mod('perf_disable_emojis', 1);

			if ( !$disable ) {
				return;
			}

			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );	
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
			add_filter( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
		}

		/**
		 * Filter function used to remove the tinymce emoji plugin.
		 */
		public function disable_emojis_tinymce( $plugins ) {
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			}

			return array();
		}

		/**
		 * Remove emoji CDN hostname from DNS prefetching hints.
		 */
		public function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {

			if ( 'dns-prefetch' == $relation_type ) {

				$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
				foreach ( $urls as $key => $url ) {
					if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
						unset( $urls[$key] );
					}
				}
			}

			return $urls;
		}

		/**
		 * Remove jquery migrate
		 */
		public function remove_jquery_migrate( $scripts ) {

			$disable = get_theme_mod('perf_jquery_migrate', 0);

			if ( !$disable ) {
				return;
			}

			if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
				$script = $scripts->registered['jquery'];
				
				if ( ! empty( $script->deps ) ) {
					$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
				}
			}
		}

		/**
		 * Defer Gutenberg block styles if not used on page
		 */
		public function dequeue_block_styles() {
			
			$disable = get_theme_mod('perf_defer_block_styles', 0);

			if ( !$disable ) {
				return;
			}

			if ( !is_singular() ) {
				return;
			}

			$post = get_post();

			if ( has_blocks( $post->post_content ) ) {
				return;
			}

			wp_dequeue_style( 'wp-block-library' );
		}

		/**
		 * Enqueue Gutenberg styles back
		 */
		public function enqueue_block_styles() {
			
			$disable = get_theme_mod('perf_defer_block_styles', 0);

			if ( !$disable ) {
				return;
			}

			if ( !is_singular() ) {
				return;
			}

			$post = get_post();

			if ( has_blocks( $post->post_content ) ) {
				return;
			}

			add_filter( 'style_loader_tag', array( $this, 'block_styles_loader_tag' ), 10, 2 );

			wp_enqueue_style( 'wp-block-library' );
		}

		/**
		 * Add media attribute to Gutenberg styles
		 */
		public function block_styles_loader_tag( $html, $handle ) {
			if ( 'wp-block-library' !== $handle ) {
				return $html;
			}

			return str_replace( "media='all'", "media='none' onload=\"media='all'\"", $html );
		}
			

	}

	Sydney_Performance::get_instance();
}