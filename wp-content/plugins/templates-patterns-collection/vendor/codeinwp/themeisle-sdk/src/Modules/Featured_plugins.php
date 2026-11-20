<?php
/**
 * File responsible for showing plugins inside the featured tab.
 *
 * This is used to display information about limited events, such as Black Friday.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.0
 */
namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Loader;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Featured_Plugins module for the ThemeIsle SDK.
 */
class Featured_Plugins extends Abstract_Module {

	/**
	 * The transient key prefix.
	 *
	 * @var string $transient_key
	 */
	private $transient_key = 'themeisle_sdk_featured_plugins_';

	/**
	 * The current product instance.
	 *
	 * @var Product|null
	 */
	protected $product = null;

	/**
	 * Check if the module can be loaded.
	 *
	 * @param Product $product Product data.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}

		if ( $product->is_wordpress_available() ) {
			return false;
		}

		return ! apply_filters( 'themeisle_sdk_disable_featured_plugins', false );
	}

	/**
	 * Load the module for the selected product.
	 *
	 * @param Product $product Product data.
	 *
	 * @return void
	 */
	public function load( $product ) {
		$this->product = $product;

		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// bail if we already registered a filter for the plugin API.
		if ( apply_filters( 'themeisle_sdk_plugin_api_filter_registered', false ) ) {
			return;
		}
		add_filter( 'themeisle_sdk_plugin_api_filter_registered', '__return_true' );

		add_filter( 'plugins_api_result', [ $this, 'filter_plugin_api_results' ], 11, 3 );

		// Enqueue inline JS only on plugin-install.php.
		add_action( 'admin_enqueue_scripts', [ $this, 'maybe_add_inline_js' ] );
	}

	/**
	 * Enqueue inline JavaScript only on plugin-install.php.
	 *
	 * @return void
	 */
	public function maybe_add_inline_js() {
		$screen = get_current_screen();
		if ( isset( $screen->base ) && 'plugin-install' === $screen->base ) {
			add_action(
				'admin_footer',
				function() {
					$text = esc_html( sprintf( Loader::$labels['promotions']['recommended'], $this->product->get_friendly_name() ) );

					echo '<script>(function(){
						function onPluginCardFound(card) {
							var recommendedDiv = document.createElement("div");
							Object.assign(recommendedDiv.style, {
								display: "block",
								textAlign: "center",
								padding: "0 12px 12px",
								background: "#f6f7f7"
							});
							recommendedDiv.innerHTML = "' . esc_html( $text ) . '";
							card.appendChild(recommendedDiv);
						}

						function checkAndRun() {
							var card = document.querySelector(".plugin-card-learning-management-system");
							if (card && !card.dataset.recommendedAdded) {
								onPluginCardFound(card);
								card.dataset.recommendedAdded = "true";
							}
						}

						var observer = new MutationObserver(function(mutations) {
							checkAndRun();
						});

						observer.observe(document.body, { childList: true, subtree: true });

						// Initial check in case the card is already present.
						checkAndRun();
					})();</script>';
				}
			);
		}
	}

	/**
	 * Filter the plugin API results to include the featured plugins.
	 *
	 * @param object $res    The result object.
	 * @param string $action The type of information being requested from the Plugin Install API.
	 * @param object $args   Plugin API arguments.
	 *
	 * @return object
	 */
	public function filter_plugin_api_results( $res, $action, $args ) {

		if ( 'query_plugins' !== $action ) {
			return $res;
		}

		if ( isset( $args->page ) && 1 === (int) $args->page && isset( $args->search ) && ! empty( $args->search ) ) {
			$original_count = count( (array) $res->plugins );
			$res->plugins   = $this->maybe_prepend_easy_mcp_plugin( $res->plugins, $args );
			$res->plugins   = $this->maybe_prepend_lms_plugin( $res->plugins, $args );

			return $this->adjust_results_count( $res, count( (array) $res->plugins ) - $original_count );
		}

		if ( ! isset( $args->browse ) || $args->browse !== 'featured' ) {
			return $res;
		}

		// Inject only on the first page so the same plugins are not repeated on every page.
		if ( isset( $args->page ) && (int) $args->page > 1 ) {
			return $res;
		}

		$featured = $this->query_plugins_by_author( $args );

		$original_count = count( (array) $res->plugins );
		$plugins        = $this->remove_plugins_by_slug( (array) $res->plugins, $this->get_plugin_slugs( $featured ) );
		$res->plugins   = array_merge( $featured, $plugins );

		return $this->adjust_results_count( $res, count( $res->plugins ) - $original_count );
	}

	/**
	 * Prepend the LMS plugin if the search query matches LMS-related terms.
	 *
	 * @param array  $plugins The plugins array.
	 * @param object $args The plugin API arguments.
	 * @return array
	 */
	private function maybe_prepend_lms_plugin( $plugins, $args ) {
		$search = isset( $args->search ) ? strtolower( $args->search ) : '';
		if ( ! self::matches_lms_search_keywords( $search ) ) {
			return $plugins;
		}

		$filter_slugs = apply_filters( 'themeisle_sdk_masteriyo_filter_slugs', [ 'learning-management-system' ] );

		return $this->prepend_author_plugins( $plugins, $args, $filter_slugs, 'masteriyo' );
	}

	/**
	 * Prepend the given author's plugins to the list, removing existing copies to avoid duplicates.
	 *
	 * @param array  $plugins      The plugins array.
	 * @param object $args         The plugin API arguments.
	 * @param array  $filter_slugs The slugs to inject.
	 * @param string $author       The wp.org author that owns the slugs.
	 *
	 * @return array
	 */
	private function prepend_author_plugins( $plugins, $args, $filter_slugs, $author ) {
		$injected = $this->get_plugins_filtered_from_author( $args, $filter_slugs, $author );
		if ( empty( $injected ) ) {
			return $plugins;
		}

		$plugins = $this->remove_plugins_by_slug( (array) $plugins, $this->get_plugin_slugs( $injected ) );

		return array_merge( $injected, $plugins );
	}

	/**
	 * Prepend the Easy MCP AI plugin if the search query matches AI/MCP-related terms.
	 *
	 * @param array  $plugins The plugins array.
	 * @param object $args The plugin API arguments.
	 * @return array
	 */
	private function maybe_prepend_easy_mcp_plugin( $plugins, $args ) {
		$search = isset( $args->search ) ? strtolower( $args->search ) : '';

		// The LMS prepend keeps precedence — never stack two injected cards.
		if ( self::matches_lms_search_keywords( $search ) || ! self::matches_ai_search_keywords( $search ) ) {
			return $plugins;
		}

		$filter_slugs = apply_filters( 'themeisle_sdk_easy_mcp_filter_slugs', [ 'easy-mcp-ai' ] );

		return $this->prepend_author_plugins( $plugins, $args, $filter_slugs, 'easymcpai' );
	}

	/**
	 * Check if a plugin search query contains AI/MCP terms or supported client brands.
	 *
	 * Brand names of unrelated AI vendors are deliberately not matched — only the
	 * category terms and the clients Easy MCP AI itself integrates with.
	 *
	 * @param string $search Search query.
	 *
	 * @return bool True if the search query matches AI/MCP-related terms.
	 */
	public static function matches_ai_search_keywords( $search ) {
		// Distinctive names match as substrings (for example, "claudecode").
		foreach ( array( 'model context protocol', 'claude', 'chatgpt' ) as $keyword ) {
			if ( false !== stripos( $search, $keyword ) ) {
				return true;
			}
		}

		// Short terms need boundaries (for example, not "email" or "fulfillment").
		return preg_match( '/(^|[^a-z0-9])(ai|mcp|llms?)([^a-z0-9]|$)/i', $search ) === 1;
	}

	/**
	 * Extract the slugs from a list of plugin entries.
	 *
	 * @param array $plugins The plugins list.
	 *
	 * @return array
	 */
	private function get_plugin_slugs( $plugins ) {
		$slugs = [];
		foreach ( $plugins as $plugin ) {
			$plugin = (array) $plugin;
			if ( isset( $plugin['slug'] ) ) {
				$slugs[] = $plugin['slug'];
			}
		}

		return $slugs;
	}

	/**
	 * Remove the plugins matching the provided slugs from the list.
	 *
	 * @param array $plugins The plugins list.
	 * @param array $slugs   The slugs to remove.
	 *
	 * @return array
	 */
	private function remove_plugins_by_slug( $plugins, $slugs ) {
		return array_values(
			array_filter(
				$plugins,
				function( $plugin ) use ( $slugs ) {
					$plugin = (array) $plugin;

					return ! isset( $plugin['slug'] ) || ! in_array( $plugin['slug'], $slugs, true );
				}
			)
		);
	}

	/**
	 * Adjust the reported total results count after injecting plugins.
	 *
	 * @param object $res   The result object.
	 * @param int    $delta The net number of plugins added to the list.
	 *
	 * @return object
	 */
	private function adjust_results_count( $res, $delta ) {
		if ( 0 !== $delta && isset( $res->info['results'] ) && is_numeric( $res->info['results'] ) ) {
			$res->info['results'] = max( 0, (int) $res->info['results'] + $delta );
		}

		return $res;
	}

	/**
	 * Check if a plugin search query matches LMS-related terms.
	 *
	 * @param string $search Search query.
	 *
	 * @return bool True if the search query matches LMS-related terms.
	 */
	public static function matches_lms_search_keywords( $search ) {
		$lms_keywords = array(
			'lms',
			'learn',
			'course',
			'courses',
			'learning',
			'academy',
			'training',
			'student',
			'students',
			'quiz',
		);

		foreach ( $lms_keywords as $keyword ) {
			if ( preg_match( '/(^|[^a-z0-9])' . preg_quote( $keyword, '/' ) . '([^a-z0-9]|$)/', $search ) === 1 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Query plugins by author.
	 *
	 * @param object $args The arguments for the query.
	 *
	 * @return array
	 */
	private function query_plugins_by_author( $args ) {
		$featured = [];

		$optimole_filter_slugs  = apply_filters( 'themeisle_sdk_optimole_filter_slugs', [ 'optimole-wp' ] );
		$filtered_from_optimole = $this->get_plugins_filtered_from_author( $args, $optimole_filter_slugs, 'Optimole' );
		$featured               = array_merge( $featured, $filtered_from_optimole );

		$themeisle_filter_slugs  = apply_filters( 'themeisle_sdk_themeisle_filter_slugs', [ 'otter-blocks', 'wp-cloudflare-page-cache' ] );
		$filtered_from_themeisle = $this->get_plugins_filtered_from_author( $args, $themeisle_filter_slugs );
		$featured                = array_merge( $featured, $filtered_from_themeisle );

		// Easy MCP AI targets the Abilities API, available since WordPress 6.9.
		if ( version_compare( get_bloginfo( 'version' ), '6.9', '>=' ) ) {
			$easy_mcp_filter_slugs  = apply_filters( 'themeisle_sdk_easy_mcp_filter_slugs', [ 'easy-mcp-ai' ] );
			$filtered_from_easy_mcp = $this->get_plugins_filtered_from_author( $args, $easy_mcp_filter_slugs, 'easymcpai' );
			$featured               = array_merge( $featured, $filtered_from_easy_mcp );
		}

		return $this->dedupe_plugins_by_slug( $featured );
	}

	/**
	 * Remove entries with a duplicate slug from a list of plugins, keeping the first occurrence.
	 *
	 * @param array $plugins The plugins list.
	 *
	 * @return array
	 */
	private function dedupe_plugins_by_slug( $plugins ) {
		$seen    = [];
		$deduped = [];

		foreach ( $plugins as $plugin ) {
			$array_plugin = (array) $plugin;
			if ( isset( $array_plugin['slug'] ) ) {
				if ( isset( $seen[ $array_plugin['slug'] ] ) ) {
					continue;
				}
				$seen[ $array_plugin['slug'] ] = true;
			}
			$deduped[] = $plugin;
		}

		return $deduped;
	}

	/**
	 * Get plugins filtered from an author.
	 *
	 * @param object $args          The arguments for the query.
	 * @param array  $filter_slugs  The slugs to filter.
	 * @param string $author        The author to filter.
	 *
	 * @return array
	 */
	protected function get_plugins_filtered_from_author( $args, $filter_slugs = [], $author = 'Themeisle' ) {

		$cached = get_transient( $this->transient_key . $author );
		if ( false !== $cached ) {
			return (array) $cached;
		}

		$new_args = [
			'page'       => 1,
			'per_page'   => 36,
			'locale'     => get_user_locale(),
			'author'     => $author,
			'wp_version' => isset( $args->wp_version ) ? $args->wp_version : get_bloginfo( 'version' ),
		];

		$api = plugins_api( 'query_plugins', $new_args );
		if ( is_wp_error( $api ) ) {
			// Cache the failure for a shorter period to avoid hammering the API on every request.
			set_transient( $this->transient_key . $author, [], HOUR_IN_SECONDS );

			return [];
		}

		$filtered = array_values(
			array_filter(
				$api->plugins,
				function( $plugin ) use ( $filter_slugs ) {
					$array_plugin = (array) $plugin;
					return in_array( $array_plugin['slug'], $filter_slugs );
				}
			)
		);

		set_transient( $this->transient_key . $author, $filtered, 12 * HOUR_IN_SECONDS );

		return $filtered;
	}
}
