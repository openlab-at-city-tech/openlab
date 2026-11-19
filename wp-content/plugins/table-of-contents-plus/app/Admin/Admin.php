<?php
namespace AIOSEO\TableOfContents\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all general admin code.
 *
 * @since 1.0.0
 */
class Admin {
	/**
	 * The main page slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $pageSlug = 'table-of-contents';

	/**
	 * The current page.
	 * This gets set as soon as we've identified that we're on a Table of Contents Plus page.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $currentPage = '';

	/**
	 * An list of asset slugs to use.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $assetSlugs = [
		'pages' => 'src/vue/pages/{page}/main.js'
	];

	/**
	 * The plugin basename.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $plugin = '';

	/**
	 * The list of pages.
	 *
	 * @since 1.2.0
	 *
	 * @var array
	 */
	private $pages = [];

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'registerMenu' ] );
		add_action( 'admin_menu', [ $this, 'hideScheduledActionsMenu' ], 999 );
		add_filter( 'language_attributes', [ $this, 'addDirAttribute' ], 3000 );

		add_filter( 'plugin_row_meta', [ $this, 'registerRowMeta' ], 10, 2 );
		add_filter( 'plugin_action_links_' . AIOSEO_TABLE_OF_CONTENTS_PLUGIN_BASENAME, [ $this, 'registerActionLinks' ], 10, 2 );

		add_action( 'admin_footer', [ $this, 'addAioseoModalPortal' ] );
	}

	/**
	 * Checks whether the current page is a Table of Contents Plus page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the current page is a Table of Contents Plus page.
	 */
	public function isTableOfContentsPage() {
		return ! empty( $this->currentPage );
	}

	/**
	 * Add the dir attribute to the HTML tag.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $output The HTML language attribute.
	 * @return string         The modified HTML language attribute.
	 */
	public function addDirAttribute( $output ) {
		if ( is_rtl() || preg_match( '/dir=[\'"](ltr|rtl|auto)[\'"]/i', (string) $output ) ) {
			return $output;
		}

		return 'dir="ltr" ' . $output;
	}

	/**
	 * Registers the menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function registerMenu() {
		$hook = add_menu_page(
			__( 'Table of Contents Plus', 'table-of-contents-plus' ),
			__( 'ToC+', 'table-of-contents-plus' ),
			'manage_options',
			$this->pageSlug,
			[ $this, 'renderMenuPage' ],
			'data:image/svg+xml;base64,' . base64_encode( $this->getMenuIcon() )
		);

		add_action( "load-{$hook}", [ $this, 'checkCurrentPage' ] );

		$this->registerMenuPages();
	}

	/**
	 * Renders the element that we mount our Vue UI on.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function renderMenuPage() {
		echo '<div id="table-of-contents-plus-app"></div>';
	}


	/**
	 * Returns the branded menu icon (anchor badge) as SVG markup.
	 *
	 * @since 202607.1
	 *
	 * @return string The SVG markup.
	 */
	private function getMenuIcon() {
		return '<svg width="20" height="20" viewBox="0 0 128 128" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M64 128C99.3462 128 128 99.3462 128 64C128 28.6538 99.3462 0 64 0C28.6538 0 0 28.6538 0 64C0 99.3462 28.6538 128 64 128Z M64.1538 25.5384C67.5385 25.5384 70.6154 26.7692 72.9231 29.3846C75.2308 31.6923 76.3077 34.6153 76.4615 37.8461V38.4615C76.4615 41.2307 75.6923 43.6923 74.1538 45.8461C72.9231 47.5384 71.3846 48.923 69.5385 49.8461V52.923H76.3077C78.3077 52.923 80 54.6153 80 56.6153V60.6153C80 62.6153 78.3077 64.3077 76.3077 64.3077H69.5385V89.2307C72.3077 88.6153 74.9231 87.3846 77.5385 85.8461C80.3077 84.1538 82.4615 82.1538 83.8461 80L80.9231 76.923C79.3846 75.3846 79.5385 72.7692 81.3846 71.3846L91.2308 63.6923C93.6923 61.8461 97.0769 63.5384 97.0769 66.6153V74.1538C97.0769 78 96 81.6923 93.8461 85.0769C91.8461 88.3076 89.0769 91.2307 86 93.5384C82.7692 96 79.2308 97.8461 75.3846 99.2307C71.5385 100.615 67.6923 101.385 63.8461 101.385C60 101.385 56.3077 100.615 52.3077 99.2307C48.4615 97.8461 44.9231 96 41.6923 93.5384C38.4615 91.0769 35.8461 88.3076 33.8461 85.0769C31.6923 81.6923 30.6154 78 30.6154 74.1538V66.6153C30.6154 63.5384 34 61.8461 36.4615 63.6923L46.3077 71.3846C48 72.7692 48.1538 75.3846 46.7692 76.923L43.8461 80C45.3846 82.1538 47.3846 84 50.1538 85.8461C52.7692 87.3846 55.5385 88.6153 58.1538 89.2307V64.4615H51.3846C49.3846 64.4615 47.6923 62.7692 47.6923 60.7692V56.7692C47.6923 54.7692 49.3846 53.0769 51.3846 53.0769H58.4615V50C56.6154 49.0769 55.0769 47.6923 53.8461 46C52.3077 44 51.5385 41.5384 51.5385 39.0769V38.6153C51.5385 35.0769 52.7692 32 55.0769 29.5384C57.6923 26.923 60.6154 25.5384 64.1538 25.5384ZM64.1538 34.1538C62.9231 34.1538 62 34.6153 61.2308 35.3846C60.4615 36.1538 60 37.2307 60 38.4615C60 39.6923 60.4615 40.7692 61.2308 41.5384C62 42.3077 62.9231 42.7692 64.1538 42.7692C65.3846 42.7692 66.3077 42.3077 67.0769 41.5384C67.8461 40.7692 68.3077 39.6923 68.3077 38.4615C68.3077 37.2307 67.8461 36.1538 67.0769 35.3846C66.3077 34.6153 65.3846 34.1538 64.1538 34.1538Z" fill="#a7aaad"/></svg>'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded
	}

	/**
	 * Registers our menu pages.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function registerMenuPages() {
		// Relabel the auto-created first submenu (which otherwise inherits the "ToC+"
		// menu title) to "Settings" for clarity.
		add_submenu_page(
			$this->pageSlug,
			__( 'Settings', 'table-of-contents-plus' ),
			__( 'Settings', 'table-of-contents-plus' ),
			'manage_options',
			$this->pageSlug,
			[ $this, 'renderMenuPage' ]
		);

		$hook = add_submenu_page(
			$this->pageSlug,
			__( 'About Us', 'table-of-contents-plus' ),
			__( 'About Us', 'table-of-contents-plus' ),
			'manage_options',
			$this->pageSlug . '-about',
			[ $this, 'renderMenuPage' ]
		);

		$this->pages[] = $this->pageSlug . '-about';

		add_action( "load-{$hook}", [ $this, 'checkCurrentPage' ] );
	}

	/**
	 * Checks if the current page is a Table of Contents Plus page and if so, starts enqueing the relevant assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function checkCurrentPage() {
		global $admin_page_hooks; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		$currentScreen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		if ( empty( $currentScreen->id ) || empty( $admin_page_hooks ) ) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
			return;
		}

		$pages = [
			'about'
		];

		foreach ( $pages as $page ) {
			$addScripts = false;

			if ( 'toplevel_page_table-of-contents' === $currentScreen->id ) {
				$page       = 'settings';
				$addScripts = true;
			}

			if ( strpos( $currentScreen->id, 'table-of-contents-' . $page ) !== false ) {
				$addScripts = true;
			}

			if ( ! $addScripts ) {
				continue;
			}

			// We don't want other plugins adding notices to our screens. Let's clear them out here.
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'network_admin_notices' );
			remove_all_actions( 'all_admin_notices' );

			$this->currentPage = $page;
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueMenuAssets' ], 11 );
			add_filter( 'admin_footer_text', [ $this, 'addFooterText' ] );

			break;
		}
	}

	/**
	 * Enqueues our menu assets, based on the current page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueueMenuAssets() {
		if ( ! $this->currentPage ) {
			return;
		}

		$scriptHandle = str_replace( '{page}', $this->currentPage, $this->assetSlugs['pages'] );
		aioseoTableOfContents()->core->assets->load( $scriptHandle, [], aioseoTableOfContents()->helpers->getVueData( $this->currentPage ) );
	}

	/**
	 * Hides the Scheduled Actions menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hideScheduledActionsMenu() {
		// Don't hide it for developers when the main plugin isn't active.
		if ( defined( 'AIOSEO_TABLE_OF_CONTENTS_DEV' ) && ! function_exists( 'aioseo' ) ) {
			return;
		}

		global $submenu;
		if ( ! isset( $submenu['tools.php'] ) ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, HM.Security.NonceVerification.Recommended
		// Don't hide it if we're on the Scheduled Actions menu page.
		$page = isset( $_GET['page'] )
			? sanitize_text_field( wp_unslash( $_GET['page'] ) )
			: '';
		// phpcs:enable

		if ( 'action-scheduler' === $page || aioseoTableOfContents()->helpers->isDev() ) {
			return;
		}

		foreach ( $submenu['tools.php'] as $index => $props ) {
			if ( ! empty( $props[2] ) && 'action-scheduler' === $props[2] ) {
				unset( $submenu['tools.php'][ $index ] );

				return;
			}
		}
	}

	/**
	 * Registers our row meta for the plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $actions    List of existing actions.
	 * @param  string $pluginFile The plugin file.
	 * @return array              List of action links.
	 */
	public function registerRowMeta( $actions, $pluginFile ) {
		$reviewLabel = str_repeat( '<span class="dashicons dashicons-star-filled" style="font-size: 18px; width:16px; height: 16px; color: #ffb900;"></span>', 5 );

		$actionLinks = [
			'suggest-feature' => [
				// Translators: This is an action link users can click to open a feature request.
				'label' => __( 'Suggest a Feature', 'table-of-contents-plus' ),
				'url'   => aioseoTableOfContents()->helpers->utmUrl( AIOSEO_TABLE_OF_CONTENTS_MARKETING_URL . 'table-of-contents-suggest-a-feature/', 'plugin-row-meta', 'feature' ),
			],
			'review'          => [
				'label' => $reviewLabel,
				'url'   => aioseoTableOfContents()->helpers->utmUrl( 'https://aioseo.com/table-of-contents-plus-rating', 'plugin-row-meta', 'review' ),
			]
		];

		return $this->parseActionLinks( $actions, $pluginFile, $actionLinks );
	}

	/**
	 * Registers our action links for the plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $actions    List of existing actions.
	 * @param  string $pluginFile The plugin file.
	 * @return array              List of action links.
	 */
	public function registerActionLinks( $actions, $pluginFile ) {
		$actionLinks = [
			'support' => [
				// Translators: This is an action link users can click to open our support.
				'label' => __( 'Support', 'table-of-contents-plus' ),
				'url'   => aioseoTableOfContents()->helpers->utmUrl( AIOSEO_TABLE_OF_CONTENTS_MARKETING_URL . 'contact/', 'plugin-action-links', 'Support' ),
			]
		];

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions = $this->parseActionLinks( $actions, $pluginFile, $actionLinks, 'before' );

		// Prepend an internal Settings link. Built separately from the links above
		// because those open in a new tab, whereas Settings should stay in the admin.
		$settingsLink = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'admin.php?page=' . $this->pageSlug ) ),
			esc_html__( 'Settings', 'table-of-contents-plus' )
		);

		return array_merge( [ 'settings' => $settingsLink ], $actions );
	}

	/**
	 * Parses the action links.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $actions     The actions.
	 * @param  string $pluginFile  The plugin file.
	 * @param  array  $actionLinks The action links.
	 * @param  string $position    The position.
	 * @return array               The parsed actions.
	 */
	private function parseActionLinks( $actions, $pluginFile, $actionLinks = [], $position = 'after' ) {
		if ( empty( $this->plugin ) ) {
			$this->plugin = AIOSEO_TABLE_OF_CONTENTS_PLUGIN_BASENAME;
		}

		if ( $this->plugin === $pluginFile && ! empty( $actionLinks ) ) {
			foreach ( $actionLinks as $key => $value ) {
				$link = [
					$key => sprintf(
						'<a href="%1$s" %2$s target="_blank">%3$s</a>',
						esc_url( $value['url'] ),
						isset( $value['title'] ) ? 'title="' . esc_attr( $value['title'] ) . '"' : '',
						$value['label']
					)
				];

				$actions = 'after' === $position ? array_merge( $actions, $link ) : array_merge( $link, $actions );
			}
		}

		return $actions;
	}

	/**
	 * Add the div for the modal portal.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function addAioseoModalPortal() {
		if ( ! function_exists( 'aioseo' ) ) {
			echo '<div id="aioseo-modal-portal"></div>';
		}
	}

	/**
	 * Checks whether the current page is a Table of Contents Plus menu page.
	 *
	 * @since 1.2.0
	 *
	 * @return bool Whether the current page is a Table of Contents Plus menu page.
	 */
	public function isTableOfContentsScreen() {
		$currentScreen = aioseoTableOfContents()->helpers->getCurrentScreen();
		if ( empty( $currentScreen->id ) ) {
			return false;
		}

		$adminPages = array_keys( $this->pages );
		$adminPages = array_map( function( $slug ) {
			if ( 'aioseo' === $slug ) {
				return 'toplevel_page_table-of-contents';
			}

			return 'table-of-contents_page_' . $slug;
		}, $adminPages );

		return in_array( $currentScreen->id, $adminPages, true );
	}

	/**
	 * Add footer text to the WordPress admin screens.
	 *
	 * @since 1.2.0
	 *
	 * @return string The footer text.
	 */
	public function addFooterText() {
		$linkText = esc_html__( 'Give us a 5-star rating!', 'table-of-contents-plus' );
		$href     = 'https://aioseo.com/table-of-contents-plus-rating';

		$link1 = sprintf(
			'<a href="%1$s" target="_blank" title="%2$s">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
			$href,
			$linkText
		);

		$link2 = sprintf(
			'<a href="%1$s" target="_blank" title="%2$s">WordPress.org</a>',
			$href,
			$linkText
		);

		printf(
			// Translators: 1 - The plugin name, 2 - Star icons, 3 - "WordPress.org", 4 - The plugin name.
			esc_html__( 'Please rate %1$s %2$s on %3$s to help us spread the word. Thank you!', 'table-of-contents-plus' ),
			sprintf( '<strong>%1$s</strong>', esc_html( AIOSEO_TABLE_OF_CONTENTS_PLUGIN_NAME ) ),
			wp_kses_post( $link1 ),
			wp_kses_post( $link2 )
		);

		// Stop WP Core from outputting its version number and instead add both theirs & ours.
		global $wp_version; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		printf(
			wp_kses_post( '<p class="alignright">%1$s</p>' ),
			sprintf(
				// Translators: 1 - WP Core version number, 2 - version number.
				esc_html__( 'WordPress %1$s | Table of Contents Plus %2$s', 'table-of-contents-plus' ),
				esc_html( $wp_version ), // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
				esc_html( AIOSEO_TABLE_OF_CONTENTS_VERSION )
			)
		);

		remove_filter( 'update_footer', 'core_update_footer' );

		return '';
	}
}